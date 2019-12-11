<?php

namespace marcusvbda\vstack\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\hasCode;
use Illuminate\Database\Eloquent\SoftDeletes;
use marcusvbda\vstack\Traits\CascadeOrRestrictSoftdeletes;

class DefaultModel extends Model
{
    use hasCode, SoftDeletes, CascadeOrRestrictSoftdeletes;
    public $guarded = ["created_at"];
    public $cascadeDeletes = [];
    public $restrictDeletes = [];
}
