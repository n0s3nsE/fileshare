<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Content;

class ListAPIController extends Controller
{
    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

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
}
