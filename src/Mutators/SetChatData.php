<?php

namespace marcusvbda\vstack\Mutators;

class SetChatData extends BaseMutator
{
    protected $needsAuth = false;
    public function process($content)
    {
        $config = config("vstack.socket_service");
        $content["chat"] = [
            "uri" => data_get($config, "uri", ""),
            "uid" => data_get($config, "uid", ""),
            "port" => data_get($config, "port", ""),
            "token" => base64_encode(json_encode([
                "username" => data_get($config, "username", ""),
                "password" => data_get($config, "password", ""),
            ]))
        ];
        return $content;
    }
}
