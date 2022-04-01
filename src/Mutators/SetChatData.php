<?php

namespace marcusvbda\vstack\Mutators;

class SetChatData extends BaseMutator
{
    protected $needsAuth = false;
    public function process($content)
    {
        // dd(json_decode(base64_decode("*************************")));
        $content["chat"] = base64_encode(json_encode(config("vstack.socket_service")));
        return $content;
    }
}
