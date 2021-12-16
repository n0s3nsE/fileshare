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
        $contents = Content::select("id", "name", "size", "isfolder", "islocked")->where("path", "/" . $id)->get();
        if ($contents->isEmpty()) {
            return response("", 204);
        } else {

            return response(json_encode(array("itemlist" => $contents)), 200);
        }
    }

    public function update(Request $request)
    {
        $rq = $request;
        if ($rq->id == null || !isset($rq->new_name)) {
            return response(json_encode(['msg' => 'error']), 500);
        }

        $ct = Content::find($rq->id);
        $ct->name = $rq->new_name;
        $ct->save();
        return response(json_encode(['msg' => 'success']), 200);
    }

    public function destroy(Request $request)
    {
        $rq = $request;
        if ($rq->delete_items == []) {
            return response(json_encode(['msg' => 'error']), 500);
        }

        foreach ($rq->delete_items as $i) {
            Content::where("id", $i)->delete();
        }
        return response(json_encode(['msg' => 'success']), 200);
    }

    public function store(Request $request)
    {
        $name = $request->name;
        $path = $request->path;
        $data = $request->data;

        $content = new Content();
        if ($content->where('path', $path)->exists()) {
            Storage::put('uploads/', $data);
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
}
