<?php

namespace marcusvbda\vstack\Models\Traits;

use Hashids;

trait hasCode
{
    private function hasCode()
    {
        $this->append('code');
    }

    public function getCodeAttribute()
    {
        return Hashids::encode($this->id);
    }
}
