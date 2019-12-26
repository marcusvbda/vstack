<?php 

namespace marcusvbda\vstack\Mutators;

use Illuminate\Support\Arr;

class SetVstackMutator extends BaseMutator{
    protected $needsAuth = false;
    public function process($content){
        $content["vstack"] = [
            "resource_field_route"   => route('resource.fielddata',['resource'=>"%%resource%%"]),
            "default_upload_route"   => Config("vstack.default_upload_route"),
            "default_import_csv_separator"   => Config("vstack.default_import_csv_separator"),
        ];
        return $content;
    }
}