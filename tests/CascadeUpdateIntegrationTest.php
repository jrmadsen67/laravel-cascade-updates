<?php
use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Events\Dispatcher;

class CascadeUpdateIntegrationTest extends \PHPUnit\Framework\TestCase {

    public static function setupBeforeClass(){
        $manager = new Manager();
        $manager->addConnection([
            'driver'   => 'sqlite',
            'database' => ':memory:',
        ]);
        $manager->setEventDispatcher(new Dispatcher(new Container()));
        $manager->setAsGlobal();
        $manager->bootEloquent();
        $manager->schema()->create('authors', function($table){
            $table->increments('id');
            $table->string('name');
            $table->boolean('is_active')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
        $manager->schema()->create('posts', function($table){
            $table->increments('id');
            $table->integer('author_id')->unsigned()->nullable();
            $table->string('title');
            $table->string('body');
            $table->boolean('is_active')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
        $manager->schema()->create('comments', function($table){
            $table->increments('id');
            $table->integer('post_id')->unsigned();
            $table->string('body');
            $table->boolean('is_active')->default(1);
            $table->timestamps();
        });
    }

    /** @test */
    public function it_cascades_updates_when_updating_a_parent_model()
    {
        $author = Tests\Entities\Author::create([
            'name' => 'Eli Dyrynda',
            'is_active' => 1
        ]);

        $author = $this->attachPostsAndCommentsToAuthor($author);
        $this->assertCount(2, $author->posts);
        $this->assertCount(2, Tests\Entities\Post::all());

        $author->update(['is_active' => 0]);
        $author->load('posts');

        $author->posts->each(function($post){
            $post->load('comments');
            $post->each(function($comment){
                $this->assertEquals(0, $comment->is_active);
            });
        });
    }

    /**
     * Attach some dummy posts (w/ comments) to the given author.
     *
     * @return void
     */
    private function attachPostsAndCommentsToAuthor($author)
    {
        $author->posts()->saveMany([
            $this->attachCommentsToPost(
                Tests\Entities\Post::create([
                    'title' => 'First post',
                    'body'  => 'This is the first test post',
                    'is_active' => 1
                ])
            ),
            $this->attachCommentsToPost(
                Tests\Entities\Post::create([
                    'title' => 'Second post',
                    'body'  => 'This is the second test post',
                    'is_active' => 1
                ])
            ),
        ]);

        return $author;
    }

    /**
     * Attach some dummy comments to the given post.
     *
     * @return void
     */
    private function attachCommentsToPost($post)
    {
        $post->comments()->saveMany([
            new Tests\Entities\Comment(['body' => 'This is the first test comment', 'is_active' => 1]),
            new Tests\Entities\Comment(['body' => 'This is the second test comment', 'is_active' => 1]),
            new Tests\Entities\Comment(['body' => 'This is the third test comment', 'is_active' => 1]),
        ]);

        return $post;
    }

    /** @test */
    public function check(){
        $this->assertTrue(true);
    }
}