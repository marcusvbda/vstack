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
        $result = $resource->model->findOrFail($request["row_id"]);
        return $result->{$request["table_key"]};
    }
}
