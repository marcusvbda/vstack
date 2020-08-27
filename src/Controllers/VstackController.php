<?php

namespace marcusvbda\vstack\Controllers;

use App\Http\Controllers\Controller;
use ResourcesHelpers;
use Illuminate\Http\Request;

class VstackController extends Controller
{
    public function getPartialContent($resource, Request $request)
    {
        $resource = ResourcesHelpers::find($resource);
        return  $this->{$request["type"]}($resource, $request);
    }

    protected function resourceTableContent($resource, $request)
    {
        $row = $resource->model->findOrFail($request["row_id"]);
        $content = [];
        foreach ($resource->table() as $key => $value) {
            $content[$key] = @$row->{$key} ? $row->{$key} : " - ";
        }
        return $content;
    }
}
