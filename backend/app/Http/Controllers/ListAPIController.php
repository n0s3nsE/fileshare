<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Content;
use Exception;
use Illuminate\Support\Facades\Storage;

use function PHPUnit\Framework\isEmpty;

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
                Content::where("id", $i)->delete();
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
