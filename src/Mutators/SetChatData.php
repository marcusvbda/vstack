<?php

namespace marcusvbda\vstack\Mutators;

class SetChatData extends BaseMutator
{
    protected $needsAuth = false;
    public function process($content)
    {
        $config = config("vstack.socket_service");
        $content["chat"] = [
            "enabled" => data_get($config, "enabled", false),
            "uri" => data_get($config, "uri", ""),
            "port" => data_get($config, "port", ""),
        ];
        return $content;
    }
}
