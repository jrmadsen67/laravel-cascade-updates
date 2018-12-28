<?php

namespace Tests\Entities;

use jrmadsen67\Database\Support\CascadeUpdates;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Author extends Model
{
    use SoftDeletes, CascadeUpdates;

    protected $cascadeUpdates = [
        'posts' => ['is_active'],
    ];

    protected $fillable = ['name','is_active'];

    public function posts()
    {
        return $this->hasMany('Tests\Entities\Post');
    }
}
