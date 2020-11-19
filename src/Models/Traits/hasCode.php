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

    public static function findByCode($code)
    {
        return static::find(@\Hashids::decode($code)[0]);
    }

    public static function findByCodeOrFail($code)
    {
        return static::findOrFail(@\Hashids::decode($code)[0]);
    }
}
