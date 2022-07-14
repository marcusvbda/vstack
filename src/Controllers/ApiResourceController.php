<?php

namespace marcusvbda\vstack\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use marcusvbda\vstack\Vstack;
use \Hashids;
use ResourcesHelpers;

class ApiResourceController extends Controller
{
    public function getResource($resource_id)
    {
        $resource = ResourcesHelpers::find($resource_id);
        $result = $resource->serialize();
        return response()->json($result);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials, (@$request['remember'] ? true : false))) {
            $user = Auth::user();
            $result = Vstack::encodeJWT(["id" => $user->id, "tenant_id" => $user->tenant_id, "name" => $user->name, "email" => $user->email]);
            return response()->json($result);
        }
        return response()->json("Invalid credentials", 401);
    }

    public function get($resource_id, Request $request)
    {
        request()->merge(["is_api" => true]);
        $result = (new ResourceController)->index($resource_id, $request);
        return response()->json($result);
    }

    public function find($resource_id, $code, Request $request)
    {
        request()->merge(["is_api" => true]);
        $id = data_get(Hashids::decode($code), 0, $code);
        $result = (new ResourceController)->view($request, $resource_id, $id);
        return response()->json($result);
    }

    private function apiStore($resource_id, $id, $type, Request $request)
    {
        request()->merge(["is_api" => true, "resource_id" => $resource_id, "id" => @$id, "page_type" => $type]);
        $result = (new ResourceController)->store($request);
        return $result;
    }

    public function edit($resource_id, $code, Request $request)
    {
        $id = data_get(Hashids::decode($code), 0, $code);
        $result = $this->apiStore($resource_id, $id, "edit", $request);
        return response()->json(data_get($result, "model"));
    }

    public function create($resource_id, Request $request)
    {
        $result = $this->apiStore($resource_id, null, "create", $request);
        return response()->json(data_get($result, "model"));
    }

    public function destroy($resource_id, $code)
    {
        $id = data_get(Hashids::decode($code), 0, $code);
        $result = (new ResourceController)->destroy($resource_id, $id);
        return response()->json(data_get($result, "success"));
    }
}
