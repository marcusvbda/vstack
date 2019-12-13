<?php

namespace marcusvbda\vstack\Mutators;

use marcusvbda\vstack\Contract\MutatorContract;
use Closure;

class BaseMutator implements MutatorContract{
    protected $user;
    protected $needsAuth = false;

    public function validate(){
        if($this->needsAuth){
            return $this->user = auth()->user();
        }
        return true;
    }

    public function handle($content,Closure $next){
        if($this->validate())
            $content = $this->process($content);

        return $next($content);
    }

    public function process($content){
        return $content;
    }
}