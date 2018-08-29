<?php

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class PostAcceptanceTest extends TestCase
{
    use DatabaseMigrations;
    use WithoutMiddleware;

    public function setUp()
    {
        parent::setUp();

        $this->Post = factory(App\Models\Post::class)->make([
            'id' => '1',
		'name' => 'qui',
		'author' => 'voluptates',

        ]);
        $this->PostEdited = factory(App\Models\Post::class)->make([
            'id' => '1',
		'name' => 'qui',
		'author' => 'voluptates',

        ]);
        $user = factory(App\Models\User::class)->make();
        $this->actor = $this->actingAs($user);
    }

    public function testIndex()
    {
        $response = $this->actor->call('GET', 'posts');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertViewHas('posts');
    }

    public function testCreate()
    {
        $response = $this->actor->call('GET', 'posts/create');
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testStore()
    {
        $response = $this->actor->call('POST', 'posts', $this->Post->toArray());

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertRedirectedTo('posts/'.$this->Post->id.'/edit');
    }

    public function testEdit()
    {
        $this->actor->call('POST', 'posts', $this->Post->toArray());

        $response = $this->actor->call('GET', '/posts/'.$this->Post->id.'/edit');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertViewHas('post');
    }

    public function testUpdate()
    {
        $this->actor->call('POST', 'posts', $this->Post->toArray());
        $response = $this->actor->call('PATCH', 'posts/1', $this->PostEdited->toArray());

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertDatabaseHas('posts', $this->PostEdited->toArray());
        $this->assertRedirectedTo('/');
    }

    public function testDelete()
    {
        $this->actor->call('POST', 'posts', $this->Post->toArray());

        $response = $this->call('DELETE', 'posts/'.$this->Post->id);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertRedirectedTo('posts');
    }

}
