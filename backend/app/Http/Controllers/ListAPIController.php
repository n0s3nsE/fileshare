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

        if ($id == null || $new_name == "") {
            return response(json_encode(['msg' => 'error']), 500);
        }

        $ct = Content::find($id);
        if ($ct === null) {
            return response(json_encode(['msg' => 'error']), 500);
        }

        Storage::move('uploads' . $ct->path . '/' . $ct->name, 'uploads' . $ct->path . '/' . $new_name);
        if ($ct->isfolder) {
            $this->update_child_path($id, $new_name);
        }
        $ct->fill(['name' => $new_name])->save();

        return response(json_encode(['msg' => 'success']), 200);
    }

    public function update_child_path($id, $new_name)
    {
        $ct = Content::find($id);
        if ($ct->path == "/") {
            $ct_path = $ct->path . $ct->name;
            $new_path = $ct->path . $new_name;
        } else {
            $ct_path = $ct->path . "/" . $ct->name;
            $new_path = $ct->path . "/" . $new_name;
        }

        $child_contents = Content::where('path', $ct_path)->get();
        $child_contents_sub = Content::where('path', 'like', $ct_path . "/%")->get();

        foreach ($child_contents as $cc) {
            if ($ct->path == "/") {
                $new_path2 = "/" . $new_name;
            } else {
                $new_path2 = pathinfo($cc->path)['dirname'] . "/" . $new_name;
            }
            $cc->fill(['path' => $new_path2])->save();
        }

        foreach ($child_contents_sub as $ccs) {
            $ccs_old_path = $ccs->path;
            $ccs_new_path = preg_replace('{^' . $ct_path . '}', $new_path, $ccs_old_path);
            $ccs->fill(['path' => $ccs_new_path])->save();
        }
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
        if ($request->name == "" || $request->path == "") {
            return response(['msg' => 'error'], 500);
        }

        if ($this->check_folder_exists(substr($request->path, 1))) {
            if ($request->tmp_name) {
                if ($this->chunk_upload($request)) {
                    return response(json_encode(['msg' => 'success']), 200);
                } else {
                    return response(json_encode(['msg' => 'error', 500]));
                }
            } else {
                if ($this->upload($request)) {
                    return response(json_encode(['msg' => 'success']), 200);
                } else {
                    return response(json_encode(['msg' => 'error', 500]));
                }
            }
        } else {
            return response(json_encode(['msg' => 'not exists folder: ' . $request->path]), 500);
        }
    }
    public function upload($request)
    {
        $name = $request->name;
        $path = $request->path;
        $data = $request->data;
        $name = $this->rename_same_filename($name, $path);

        $content = new Content();

        try {
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
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    public function chunk_upload(Request $request)
    {
        $name = $request->name;
        $path = $request->path;
        $tmp_name = $request->tmp_name;

        if ($request->endflag) {
            return $this->join_chunk($name, $path, $tmp_name);
        } else {
            $index = $request->index;
            $data = $request->data;
            $name = $this->rename_same_filename($name, $path);

            Storage::putFileAs('uploads' . $path, $data, "{$tmp_name}.tmp.{$index}");
        }
    }

    public function join_chunk($name, $path, $tmp_name)
    {
        $name = $this->rename_same_filename($name, $path);
        try {
            $content = new Content();
            $content->fill([
                'name' => $name,
                'size' => 0,
                'isfolder' => false,
                'path' => $path,
                'islocked' => false,
                'created_at' => null,
                'updated_at' => null
            ])->save();

            if ($path == "/") {
                $path = "";
            }

            $tmp_name = $tmp_name . '.tmp';
            $index = 0;
            while (Storage::exists("uploads{$path}/{$tmp_name}." . ($index + 1))) {
                $f = Storage::get("uploads{$path}/{$tmp_name}." . ($index + 1));
                Storage::append("uploads{$path}/{$tmp_name}.0", $f, null);
                Storage::delete("uploads{$path}/{$tmp_name}." . ($index + 1));
                $index += 1;
            }
            Storage::move("uploads{$path}/{$tmp_name}.0", "uploads{$path}/{$name}");

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function create(Request $request)
    {
        $new_folder_name = $request->new_folder_name;
        $path = $request->path;

        if ($new_folder_name == "" || $path == "") {
            return response(['msg' => 'error'], 500);
        }

        $new_folder_name = $this->rename_same_foldername($new_folder_name, $path);

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

    public function rename_same_filename($name, $path)
    {
        //split filename
        $filename = pathinfo($name)['filename'];
        $ext = pathinfo($name)['extension'];
        $index = 1;

        while (Content::where('path', $path)->where('name', $name)->where('isfolder', false)->exists()) {
            $name = $filename . "({$index})." . $ext;
            $index += 1;
        }
        return $name;
    }

    public function rename_same_foldername($name, $path)
    {
        $foldername = $name;
        $index = 1;

        while (Content::where('path', $path)->where('name', $name)->where('isfolder', true)->exists()) {
            $name = $foldername . "({$index})";
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
