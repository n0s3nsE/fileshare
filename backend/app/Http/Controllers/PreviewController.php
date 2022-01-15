<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Content;

class PreviewController extends Controller
{
    //
    public function show($param)
    {
        $ct = Content::find($param);
        if (!is_null($ct) && !$ct->isfolder) {
            return response()->file("storage/uploads{$ct->path}/{$ct->name}");
        } else {
            return response(json_encode(['msg' => 'File not found.']), 404);
        }
    }
}
