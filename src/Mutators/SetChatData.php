<?php 

namespace marcusvbda\vstack\Mutators;

use Illuminate\Support\Arr;

class SetChatData extends BaseMutator{
    protected $needsAuth = true;
    public function process($content){
        $content["chat"] = [
            "pusher_key"     => config("broadcasting.connections.pusher.key"),
            "pusher_cluster" => config("broadcasting.connections.pusher.options.cluster")
        ];
        return $content;
    }
}