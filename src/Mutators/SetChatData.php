<?php

namespace marcusvbda\vstack\Mutators;

class SetChatData extends BaseMutator
{
    protected $needsAuth = false;
    public function process($content)
    {
        $default = config("broadcasting.default");
        $config_broadcasting = config("broadcasting.connections");
        $content["broadcast"] = $config_broadcasting[$default];
        return $content;
    }
}
