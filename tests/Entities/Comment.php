<?php

namespace Tests\Entities;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['body','is_active'];

    public function post()
    {
        return $this->belongsTo('Tests\Entities\Post');
    }
}
