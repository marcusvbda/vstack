<?php

namespace marcusvbda\vstack\Models\Traits;

use marcusvbda\vstack\Models\Tag;

trait hasTags
{
    public function tags()
    {
        return $this->belongsToMany(Tag::class, "resource_tags_relation", "relation_id", "resource_tag_id")
            ->where("resource_tags_relation.model", static::class);
    }
}
