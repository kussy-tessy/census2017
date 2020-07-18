<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Answer extends Model
{
    // ハードコーディング万歳
    public function scopeOnlyWorker(Builder $query)
    {
        return $query->whereNotIn('job', [6, 7, 8]);
    }

    public function scopeOnlyActive(Builder $query)
    {
        return $query->where('activity', '<>', 1);
    }

    public function scopeOnlyActor(Builder $query)
    {
        return $query->whereIn('activity', [3, 5]);
    }
}
