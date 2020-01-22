<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\AddressController
 */
class AddressControllerTest extends TestCase
{
    // use RefreshDatabase;

    /**
     * @test
     */
    public function create_returns_an_ok_response() //create method empty - nothing to test
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $user = factory(\App\User::class)->create();

        $response = $this->actingAs($user)->get(route('address.create'));

        $response->assertOk();

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function destroy_returns_an_ok_response()
    {
        $address = factory(\App\Address::class)->create();
        $contact_id = $address->contact_id;
        $user = $this->createUserWithPermission('delete-address');

        $response = $this->actingAs($user)->delete(route('address.destroy', [$address]));

        $response->assertRedirect(action('PersonController@show', $contact_id));
        $this->assertSoftDeleted($address);

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function edit_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $address = factory(\App\Address::class)->create();
        $user = factory(\App\User::class)->create();

        $response = $this->actingAs($user)->get(route('address.edit', [$address]));

        $response->assertOk();

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function index_returns_an_ok_response()
    {

        $user = $this->createUserWithPermission('show-contact');

        $response = $this->actingAs($user)->get(route('address.index'));

        $response->assertOk();
        $response->assertViewIs('addresses.index');
        $response->assertViewHas('addresses');
        $response->assertSeeText('Addresses');


        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function show_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $address = factory(\App\Address::class)->create();
        $user = factory(\App\User::class)->create();

        $response = $this->actingAs($user)->get(route('address.show', [$address]));

        $response->assertOk();

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function store_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $user = factory(\App\User::class)->create();

        $response = $this->actingAs($user)->post(route('address.store'), [
            // TODO: send request data
        ]);

        $response->assertOk();

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function update_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $address = factory(\App\Address::class)->create();
        $user = factory(\App\User::class)->create();

        $response = $this->actingAs($user)->put(route('address.update', [$address]), [
            // TODO: send request data
        ]);

        $response->assertOk();

        // TODO: perform additional assertions
    }

    // test cases...
}
