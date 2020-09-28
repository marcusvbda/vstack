<?php

namespace marcusvbda\vstack\Models;

use Illuminate\Database\Eloquent\Model;

class TagRelation extends Model
{
    public $table = "resource_tags_relation";
    public $guarded = ["created_at"];

    public static function hasTenant()
    {
        return true;
    }

    public function tag()
    {
        return $this->belongsTo(Tag::class, 'resource_tag_id');
    }
}
