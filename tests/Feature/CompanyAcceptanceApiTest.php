<?php

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CompanyAcceptanceApiTest extends TestCase
{
    use DatabaseMigrations;
    use WithoutMiddleware;

    public function setUp()
    {
        parent::setUp();

        $this->Company = factory(App\Models\Company::class)->make([
            'id' => '1',
		'name' => 'voluptatem',
		'description' => '1',
		'industry' => '1',
		'subscription-tier' => '1',
		'number_of_staff' => '1',
		'active' => '1',

        ]);
        $this->CompanyEdited = factory(App\Models\Company::class)->make([
            'id' => '1',
		'name' => 'voluptatem',
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
        $response = $this->actor->call('GET', 'api/v1/companies');
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testStore()
    {
        $response = $this->actor->call('POST', 'api/v1/companies', $this->Company->toArray());
        $this->assertEquals(200, $response->getStatusCode());
        $this->seeJson(['id' => 1]);
    }

    public function testUpdate()
    {
        $this->actor->call('POST', 'api/v1/companies', $this->Company->toArray());
        $response = $this->actor->call('PATCH', 'api/v1/companies/1', $this->CompanyEdited->toArray());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertDatabaseHas('companies', $this->CompanyEdited->toArray());
    }

    public function testDelete()
    {
        $this->actor->call('POST', 'api/v1/companies', $this->Company->toArray());
        $response = $this->call('DELETE', 'api/v1/companies/'.$this->Company->id);
        $this->assertEquals(200, $response->getStatusCode());
        $this->seeJson(['success' => 'company was deleted']);
    }

}
