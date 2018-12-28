<?php

namespace Tests\Entities;

use jrmadsen67\Database\Support\CascadeUpdates;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use SoftDeletes, CascadeUpdates;

    protected $cascadeUpdates = [
        'comments' => ['is_active'],
    ];

    protected $fillable = ['title', 'body', 'is_active'];

    public function comments()
    {
        return $this->hasMany('Tests\Entities\Comment');
    }

}
