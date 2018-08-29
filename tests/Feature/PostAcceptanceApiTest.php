<?php

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class PostAcceptanceApiTest extends TestCase
{
    use DatabaseMigrations;
    use WithoutMiddleware;

    public function setUp()
    {
        parent::setUp();

        $this->Post = factory(App\Models\Post::class)->make([
            'id' => '1',
		'name' => 'sequi',
		'author' => 'porro',

        ]);
        $this->PostEdited = factory(App\Models\Post::class)->make([
            'id' => '1',
		'name' => 'sequi',
		'author' => 'porro',

        ]);
        $user = factory(App\Models\User::class)->make();
        $this->actor = $this->actingAs($user);
    }

    public function testIndex()
    {
        $response = $this->actor->call('GET', 'api/v1/posts');
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testStore()
    {
        $response = $this->actor->call('POST', 'api/v1/posts', $this->Post->toArray());
        $this->assertEquals(200, $response->getStatusCode());
        $this->seeJson(['id' => 1]);
    }

    public function testUpdate()
    {
        $this->actor->call('POST', 'api/v1/posts', $this->Post->toArray());
        $response = $this->actor->call('PATCH', 'api/v1/posts/1', $this->PostEdited->toArray());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertDatabaseHas('posts', $this->PostEdited->toArray());
    }

    public function testDelete()
    {
        $this->actor->call('POST', 'api/v1/posts', $this->Post->toArray());
        $response = $this->call('DELETE', 'api/v1/posts/'.$this->Post->id);
        $this->assertEquals(200, $response->getStatusCode());
        $this->seeJson(['success' => 'post was deleted']);
    }

}
