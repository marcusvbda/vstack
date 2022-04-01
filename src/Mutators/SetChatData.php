<?php

namespace marcusvbda\vstack\Mutators;

use Illuminate\Support\Arr;

class SetChatData extends BaseMutator
{
    protected $needsAuth = false;
    public function process($content)
    {
        $content["chat"] = config("vstack.socket_service");
        return $content;
    }
}
