<?php

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CompanyAcceptanceTest extends TestCase
{
    use DatabaseMigrations;
    use WithoutMiddleware;

    public function setUp()
    {
        parent::setUp();

        $this->Company = factory(App\Models\Company::class)->make([
            'id' => '1',
		'name' => 'rerum',
		'description' => '1',
		'industry' => '1',
		'subscription-tier' => '1',
		'number_of_staff' => '1',
		'active' => '1',

        ]);
        $this->CompanyEdited = factory(App\Models\Company::class)->make([
            'id' => '1',
		'name' => 'rerum',
		'description' => '1',
		'industry' => '1',
		'subscription-tier' => '1',
		'number_of_staff' => '1',
		'active' => '1',

        ]);
        $user = factory(App\Models\User::class)->make();
        $this->actor = $this->actingAs($user);
    }

    public function testIndex()
    {
        $response = $this->actor->call('GET', 'companies');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertViewHas('companies');
    }

    public function testCreate()
    {
        $response = $this->actor->call('GET', 'companies/create');
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testStore()
    {
        $response = $this->actor->call('POST', 'companies', $this->Company->toArray());

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertRedirectedTo('companies/'.$this->Company->id.'/edit');
    }

    public function testEdit()
    {
        $this->actor->call('POST', 'companies', $this->Company->toArray());

        $response = $this->actor->call('GET', '/companies/'.$this->Company->id.'/edit');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertViewHas('company');
    }

    public function testUpdate()
    {
        $this->actor->call('POST', 'companies', $this->Company->toArray());
        $response = $this->actor->call('PATCH', 'companies/1', $this->CompanyEdited->toArray());

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertDatabaseHas('companies', $this->CompanyEdited->toArray());
        $this->assertRedirectedTo('/');
    }

    public function testDelete()
    {
        $this->actor->call('POST', 'companies', $this->Company->toArray());

        $response = $this->call('DELETE', 'companies/'.$this->Company->id);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertRedirectedTo('companies');
    }

}
