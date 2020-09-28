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
            if (strpos($key, "->") === false) {
                $content[$key] = @$row->{$key} ? $row->{$key} : " - ";
            } else {
                $value = $row;
                $_runner = explode("->", $key);
                foreach ($_runner as $idx) $value = @$value->{$idx};
                $content[$key] = ($value ? $value : ' - ');
            }
        }
        return $content;
    }
}
