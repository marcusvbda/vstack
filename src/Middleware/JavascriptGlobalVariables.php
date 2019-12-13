<?php

namespace marcusvbda\vstack\Middleware;
use Illuminate\Routing\Pipeline;
use Closure;
use marcusvbda\vstack\Mutators\{
    SetChatData,
    SetUserData,
    SetGeneralMutator
};

class SendJSVarsToView{
    private $variableNameInBlade = "javascript_globals";

    private $pipes = [
        SetUserData::class,
        SetChatData::class,
        SetGeneralMutator::class,
    ];

    private $globals = [];

    public function handle($request, Closure $next){
        return (app(Pipeline::class))
            ->send($this->globals)
            ->through($this->pipes)
            ->then(function($v) use ($request,$next){
                view()->share($this->variableNameInBlade, json_encode($v));
                return $next($request);
            });


    }
}