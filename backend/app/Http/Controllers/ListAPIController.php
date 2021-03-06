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
        $contents = Content::select("id", "name", "size", "isfolder", "islocked", "updated_at")->where("path", "/" . $id)->get();

        if (!$this->check_folder_exists($id)) {
            return response(json_encode(['msg' => 'Folder does not exist.']), 404);
        }

        if ($contents->isEmpty()) {
            return response("", 204);
        } else {
            return response(json_encode(array("itemlist" => $contents)), 200);
        }
    }

    public function detail($id)
    {
        $contents = Content::select("id", "name", "size", "isfolder", "islocked", "updated_at")->where("id", $id)->get();
        if ($contents->isEmpty()) {
            return response(json_encode(['msg' => 'Content does not exist.']), 404);
        } else {
            return response(json_encode(['detail' => $contents[0]]), 200);
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'new_name' => 'required',
        ]);

        $id = $request->id;
        $new_name = $request->new_name;

        $ct = Content::find($id);
        if ($ct === null) {
            return response(json_encode(['msg' => 'failed', 'detail' => 'Content does not exist.']), 500);
        }

        if ($this->check_islocked($id)) {
            return response(json_encode(['msg' => 'failed', 'detail' => 'Content is locked.']), 500);
        }

        Storage::move('public/uploads' . $ct->path . '/' . $ct->name, 'public/uploads' . $ct->path . '/' . $new_name);
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
        $request->validate([
            'delete_items' => 'required',
        ]);

        $delete_item_ids = $request->delete_items;
        $failed_items = [];

        foreach ($delete_item_ids as $delete_item_id) {
            $delete_item = Content::find($delete_item_id);

            if (!is_null($delete_item)) {
                if (!$this->check_islocked($delete_item_id)) {
                    if ($delete_item->isfolder) {
                        $ds = $this->delete_subcontents($delete_item_id);
                        if (!$ds) {
                            if ($delete_item->path === "/") {
                                $delete_item->path = "";
                            }
                            Storage::deleteDirectory("public/uploads{$delete_item->path}/{$delete_item->name}");
                            $delete_item->delete();
                        } else {
                            array_push($failed_items, $ds);
                        }
                    } else {
                        Storage::delete("public/uploads{$delete_item->path}/{$delete_item->name}");
                        $delete_item->delete();
                    }
                } else {
                    array_push($failed_items, 'Contents (id: ' . $delete_item_id . ') is locked.');
                }
            } else {
                array_push($failed_items, 'Contents (id: ' . $delete_item_id . ') does not exist.');
            }
        }

        if (!$failed_items) {
            return response(json_encode(['msg' => 'success']), 200);
        } else {
            return response(json_encode(['msg' => 'failed', 'detail' => $failed_items]), 500);
        }
    }

    public function delete_subcontents($id)
    {
        $parent_folder = Content::find($id);
        if ($parent_folder->path === "/") {
            $parent_folder->path = "";
        }
        $sub_file = Content::where('path', "{$parent_folder->path}/{$parent_folder->name}")->where('isfolder', false);
        $sub_folder = Content::where('path', "{$parent_folder->path}/{$parent_folder->name}")->where('isfolder', true);
        $failed_items = [];

        if ($sub_file->exists()) {
            foreach ($sub_file->get() as $sfile) {
                if (!$sfile->islocked) {
                    Storage::delete("public/uploads{$sfile->path}/{$sfile->name}");
                } else {
                    array_push($failed_items, ['id' => $sfile->id, 'reason' => 'Content is locked.']);
                }
                Content::where('path', "{$parent_folder->path}/{$parent_folder->name}")->where('isfolder', false)->where('islocked', false)->delete();
            }
        }
        if ($sub_folder->exists()) {
            foreach ($sub_folder->get() as $sfolder) {
                if (!$sfolder->islocked) {
                    $res = $this->delete_subcontents($sfolder->id);
                    if (!$res) {
                        Storage::deleteDirectory("public/uploads{$sfolder->path}/{$sfolder->name}");
                        Content::find($sfolder->id)->delete();
                    } else {
                        array_push($failed_items, 'Content (id: ' . $sfolder->id . ') is locked.');
                    }
                } else {
                    array_push($failed_items, 'Content (id: ' . $sfolder->id . ') is locked.');
                }
            }
        }

        if (!$failed_items) {
            return false;
        } else {
            return $failed_items;
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'path' => 'required'
        ]);

        if ($this->check_folder_exists(substr($request->path, 1))) {
            if ($request->tmp_name) {
                if ($this->chunk_upload($request)) {
                    return response(json_encode(['msg' => 'success']), 200);
                } else {
                    return response(json_encode(['msg' => 'failed', 'detail' => 'File merge failed.']), 500);
                }
            } else {
                if ($this->upload($request)) {
                    return response(json_encode(['msg' => 'success']), 200);
                } else {
                    return response(json_encode(['msg' => 'failed', 'detail' => 'File upload failed.']), 500);
                }
            }
        } else {
            return response(json_encode(['msg' => 'failed', 'detail' => 'Folder does not exist.']), 500);
        }
    }

    public function upload(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'path' => 'required',
            'data' => 'required',
        ]);

        $name = $request->name;
        $path = $request->path;
        $data = $request->data;
        $name = $this->rename_same_filename($name, $path);

        $content = new Content();

        try {
            Storage::putFileAs('public/uploads' . $path, $data, $name);
            $f_size = Storage::size('public/uploads' . $path . '/' . $name) / 1024; //KB

            $content->fill([
                'name' => $name,
                'size' => $f_size,
                'isfolder' => false,
                'path' => $path,
                'islocked' => false,
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

            Storage::putFileAs('public/uploads' . $path, $data, "{$tmp_name}.tmp.{$index}");
        }
    }

    public function join_chunk($name, $path, $tmp_name)
    {
        $name = $this->rename_same_filename($name, $path);
        try {
            $f_path = $path;

            if ($path == "/") {
                $path = "";
            }

            $tmp_name = $tmp_name . '.tmp';
            $index = 0;
            while (Storage::exists("public/uploads{$path}/{$tmp_name}." . ($index + 1))) {
                $f = Storage::get("public/uploads{$path}/{$tmp_name}." . ($index + 1));
                $f2 = fopen(storage_path("app/public/uploads{$path}/{$tmp_name}.0"), "a");
                fwrite($f2, $f);
                fclose($f2);

                Storage::delete("public/uploads{$path}/{$tmp_name}." . ($index + 1));
                $index += 1;
            }
            Storage::move("public/uploads{$path}/{$tmp_name}.0", "public/uploads{$path}/{$name}");
            $f_size = Storage::size("public/uploads{$path}/{$name}") / 1024; //KB

            $content = new Content();
            $content->fill([
                'name' => $name,
                'size' => $f_size,
                'isfolder' => false,
                'path' => $f_path,
                'islocked' => false,
            ])->save();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function create(Request $request)
    {
        $request->validate([
            'new_folder_name' => 'required',
            'path' => 'required',
        ]);

        $new_folder_name = $request->new_folder_name;
        $path = $request->path;

        $new_folder_name = $this->rename_same_foldername($new_folder_name, $path);

        if ($this->check_folder_exists(substr($path, 1))) {
            Storage::makeDirectory("public/uploads/" . $path . "/" . $new_folder_name);
            $content = new Content();
            $content->fill([
                'name' => $new_folder_name,
                'size' => 0,
                'isfolder' => true,
                'path' => $path,
                'islocked' => false,
            ])->save();
            return response(json_encode(['msg' => 'success']), 200);
        } else {
            return response(json_encode(['msg' => 'error', 'detail' => 'Folder does not exists.']), 500);
        }
    }

    public function content_lock($id)
    {
        $ct = Content::find($id);
        if ($ct) {
            $ct->fill(['islocked' => !$ct->islocked])->save();
            return response(json_encode(['msg' => 'success']), 200);
        } else {
            return response(json_encode(['msg' => 'failed', 'detail' => 'Content does not exists.']), 500);
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

    public function check_islocked($id)
    {
        $ct = Content::find($id);
        if ($ct->islocked) {
            return true;
        } else {
            return false;
        }
    }
}
