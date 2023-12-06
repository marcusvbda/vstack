<?php

namespace marcusvbda\vstack;

use Illuminate\View\Component;

class Components extends Component
{
    public function __construct(private $callback, private $payload = null)
    {
        //
    }

    public function render()
    {
        try {
            $splitted = explode("@", $this->callback);
            $class = $splitted[0];
            $method = $splitted[1];
            $instance = app()->make($class);
            return $instance->$method($this->payload);
        } catch (\Throwable $th) {
            return "Erro ao renderizar o componente {$this->callback}";
        }
    }
}
