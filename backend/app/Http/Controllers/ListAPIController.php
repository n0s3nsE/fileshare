<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Content;
use Illuminate\Support\Facades\Storage;

use function PHPUnit\Framework\isEmpty;

class ListAPIController extends Controller
{
    public function show($id = "")
    {
        $contents = Content::select("id", "name", "size", "isfolder", "islocked")->where("path", "/" . $id);

        if (!$contents->exists()) {
            return response(json_encode(['msg' => 'not exists folder']), 404);
        } else if ($contents->get()->isEmpty()) {
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
        $ct->fill(['name' => $new_name])->save();
        return response(json_encode(['msg' => 'success']), 200);
    }

    public function destroy(Request $request)
    {
        $delete_items = $request->delete_items;
        if ($delete_items == []) {
            return response(json_encode(['msg' => 'error']), 500);
        }

        foreach ($delete_items as $i) {
            Content::where("id", $i)->delete();
        }
        return response(json_encode(['msg' => 'success']), 200);
    }

    public function store(Request $request)
    {
        $name = $request->name;
        $path = $request->path;
        $data = $request->data;

        $name = $this->rename_same_name($name, $path);

        $content = new Content();

        if ($path == "/") {
            $parent_folder_path = "/";

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
            $parent_folder_name = mb_substr($path, mb_strrpos($path, '/') + 1, mb_strlen($path));
            $parent_folder_path = mb_substr($path, 0, mb_strrpos($path, '/') + 1);

            if ($content->where('name', $parent_folder_name)->where('isfolder', true)->where('path', $parent_folder_path)->exists()) {
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
}
