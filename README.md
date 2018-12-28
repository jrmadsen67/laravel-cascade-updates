# Laravel Cascade Updates

##v1.0.0

This is basically a fork of https://github.com/michaeldyrynda/laravel-cascade-soft-deletes by Michael Dyrynda. I had the idea to make this for sometime, and after planning out how I wanted it to work I searched around to see what was similar. Michael's library was already well established and worked very similarly to what I had in mind, so in the interest of getting it done quickly over the holidays and sticking to a known format, I just based mine on his work.

This Trait allows you to update all lower entities in a hierarchy when a top level record is changed. For example, if I have Authors with multiple Posts, and each Post has multiple Comments, I can set a `is_active` flag to false on an Author and have all of their Posts and the related Comments deactivate at the same time.

## Code Samples

```php
<?php

namespace App;

use App\Comment;
use jrmadsen67\Database\Support\CascadeUpdates;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use CascadeUpdates;

    protected $cascadeUpdates = ['comments' => ['is_active']];
    
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
```

Now you can update an `App\Post` record, and any associated `App\Comment` records will be update. If the `App\Comment` record implements the `CascadeUpdates` trait as well, it's children will also be updated and so on.

###IMPORTANT: This will only update the same field on all entities. In other words, it is not possible to tell it to update `myField` whenever the `is_active` is changed.

```php
$post = App\Post::find($postId)
$post->update(['is_active' => 0]); // Updates the post, which will also trigger the update() method on any comments and their children.
```

## Installation

This trait is installed via [Composer](http://getcomposer.org/). To install, simply add to your `composer.json` file:

```
$ composer require jrmadsen67/laravel-cascade-updates
```

## Support

If you are having general issues with this package, feel free to contact me on [Twitter](https://twitter.com/codebyjeff).

If you believe you have found an issue, please report it using the [GitHub issue tracker](https://github.com/jrmadsen67/laravel-cascade-updates/issues), or better yet, fork the repository and submit a pull request.

If you're using this package, I'd love to hear your thoughts. Thanks!
