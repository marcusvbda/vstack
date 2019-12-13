<?php

namespace marcusvbda\vstack\Contract;

use Closure;

interface MutatorContract{
    public function handle($content,Closure $next);

    public function process($content);
}