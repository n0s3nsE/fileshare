<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Content;
use Exception;
use Illuminate\Support\Facades\Storage;


class ListAPIController extends Controller
{
    public function show($id = "")
    {
        $contents = Content::select("id", "name", "size", "isfolder", "islocked")->where("path", "/" . $id);

        if (!$this->check_folder_exists($id)) {
            return response(json_encode(['msg' => 'not exists folder']), 404);
        }

        if ($contents->get()->isEmpty()) {
            return response("", 204);
        } else {
            return response(json_encode(array("itemlist" => $contents->get())), 200);
        }
    }

    public function update(Request $request)
    {
        $id = $request->id;
        $new_name = $request->new_name;

        if ($id == null || !isset($new_name)) {
            return response(json_encode(['msg' => 'error']), 500);
        }

        $ct = Content::find($id);
        if ($ct === null) {
            return response(json_encode(['msg' => 'error']), 500);
        }

        Storage::move('uploads' . $ct->path . '/' . $ct->name, 'uploads' . $ct->path . '/' . $new_name);
        $ct->fill(['name' => $new_name])->save();

        return response(json_encode(['msg' => 'success']), 200);
    }

    public function destroy(Request $request)
    {
        $delete_items = $request->delete_items;
        $error_items_id = [];

        if ($delete_items == []) {
            return response(json_encode(['msg' => 'error']), 500);
        }

        foreach ($delete_items as $i) {
            try {
                $delcontent = Content::find($i);

                if ($delcontent->isfolder) {
                    if ($delcontent->path == "/") {
                        $delcontent_path = "/" . $delcontent->name;
                    } else {
                        $delcontent_path = $delcontent->path . "/" . $delcontent->name;
                    }

                    Storage::deleteDirectory("uploads" . $delcontent_path);
                    Content::where('id', $delcontent->id)->delete();

                    $this->delete_child_record($delcontent_path);
                } else {
                    Storage::delete('uploads' . $delcontent->path . '/' . $delcontent->name);
                }
                Content::where('id', $i)->delete();
            } catch (Exception $e) {
                array_push($error_items_id, $i);
            }
        }

        if ($error_items_id != []) {
            return response(json_encode(['msg' => 'delete failed', 'id' => $error_items_id]), 500);
        } else {
            return response(json_encode(['msg' => 'success']), 200);
        }
    }

    public function delete_child_record($del_rec)
    {
        $child_folder = Content::where('path', $del_rec)->where('isfolder', true)->get();
        foreach ($child_folder as $i) {
            $path = $i->path . '/' . $i->name;
            Content::select('id')->where('path', $path)->where('isfolder', false)->delete();
            if (Content::where('path', $path)->where('isfolder', true)->exists()) {
                $this->delete_child_record($path);
            }
        }
        Content::select('id')->where('path', $del_rec)->where('isfolder', false)->delete();
        Content::where('path', $del_rec)->where('isfolder', true)->delete();
    }

    public function store(Request $request)
    {
        $name = $request->name;
        $path = $request->path;
        $data = $request->data;

        if ($this->check_folder_exists(substr($path, 1))) {

            $name = $this->rename_same_name($name, $path);

            $content = new Content();

            Storage::putFileAs('uploads' . $path, $data, $name);
            $content->fill([
                'name' => $name,
                'size' => 0,
                'isfolder' => false,
                'path' => $path,
                'islocked' => false,
                'created_at' => null,
                'updated_at' => null
            ])->save();
            return response(json_encode(['msg' => 'success']), 200);
        } else {
            return response(json_encode(['msg' => 'not exists folder: ' . $path]), 500);
        }
    }
    public function chunk_upload(Request $request)
    {
    }

    public function create(Request $request)
    {
        $new_folder_name = $request->new_folder_name;
        $path = $request->path;

        if ($this->check_folder_exists(substr($path, 1))) {
            Storage::makeDirectory("uploads/" . $path . "/" . $new_folder_name);
            $content = new Content();
            $content->fill([
                'name' => $new_folder_name,
                'size' => 0,
                'isfolder' => true,
                'path' => $path,
                'islocked' => false,
                'created_at' => null,
                'updated_at' => null
            ])->save();
            return response(json_encode(['msg' => 'success']), 200);
        } else {
            return response(json_encode(['msg' => 'not exists folder: ' . $path]), 500);
        }
    }

    public function rename_same_name($name, $path)
    {
        //split filename
        $filename = pathinfo($name)['filename'];
        $ext = pathinfo($name)['extension'];
        $index = 1;

        while (Content::where('path', $path)->where('name', $name)->exists()) {
            $name = $filename . "({$index})." . $ext;
            $index += 1;
        }

        return $name;
    }

    public function check_folder_exists($path)
    {
        if ($path != "") {
            if (pathinfo($path)["dirname"] == ".") {
                $dirname = "/";
            } else {
                $dirname = "/" . pathinfo($path)["dirname"];
            }
            if (!Content::where("name", pathinfo($path)["basename"])->where("path", $dirname)->where("isfolder", true)->exists()) {
                return false;
            }
        }
        return true;
    }

    public function check_file_exists($filename, $path)
    {
        if (!Content::where("name", $filename)->where("path", $path)->where("isfolder", false)->exists()) {
            return false;
        }
        return true;
    }
}
