<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\RegistrationController
 */
class RegistrationControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function add_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $user = factory(\App\User::class)->create();

        $response = $this->actingAs($user)->get('registration/add/{id?}');

        $response->assertOk();
        $response->assertViewIs('registrations.create');
        $response->assertViewHas('retreats');
        $response->assertViewHas('retreatants');
        $response->assertViewHas('rooms');
        $response->assertViewHas('defaults');

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function add_group_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $user = factory(\App\User::class)->create();

        $response = $this->actingAs($user)->get('group/{group_id?}/registration');

        $response->assertOk();
        $response->assertViewIs('registrations.add_group');
        $response->assertViewHas('retreats');
        $response->assertViewHas('groups');
        $response->assertViewHas('rooms');
        $response->assertViewHas('defaults');

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function arrive_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $registration = factory(\App\Registration::class)->create();
        $user = factory(\App\User::class)->create();

        $response = $this->actingAs($user)->get(route('registration.arrive', ['id' => $registration->id]));

        $response->assertRedirect(back());

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function attend_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $registration = factory(\App\Registration::class)->create();
        $user = factory(\App\User::class)->create();

        $response = $this->actingAs($user)->get(route('registration.attend', ['id' => $registration->id]));

        $response->assertRedirect(back());

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function cancel_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $registration = factory(\App\Registration::class)->create();
        $user = factory(\App\User::class)->create();

        $response = $this->actingAs($user)->get(route('registration.cancel', ['id' => $registration->id]));

        $response->assertRedirect(back());

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function confirm_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $registration = factory(\App\Registration::class)->create();
        $user = factory(\App\User::class)->create();

        $response = $this->actingAs($user)->get(route('registration.confirm', ['id' => $registration->id]));

        $response->assertRedirect(back());

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function confirm_attendance_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $registration = factory(\App\Registration::class)->create();

        $response = $this->get('registration/confirm/{token}');

        $response->assertRedirect(away('https://montserratretreat.org/retreat-attendance'));

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function create_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $user = factory(\App\User::class)->create();

        $response = $this->actingAs($user)->get(route('registration.create'));

        $response->assertOk();
        $response->assertViewIs('registrations.create');
        $response->assertViewHas('retreats');
        $response->assertViewHas('retreatants');
        $response->assertViewHas('rooms');
        $response->assertViewHas('defaults');

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function depart_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $registration = factory(\App\Registration::class)->create();
        $user = factory(\App\User::class)->create();

        $response = $this->actingAs($user)->get(route('registration.depart', ['id' => $registration->id]));

        $response->assertRedirect(back());

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function destroy_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $registration = factory(\App\Registration::class)->create();
        $user = factory(\App\User::class)->create();

        $response = $this->actingAs($user)->delete(route('registration.destroy', [$registration]));

        $response->assertRedirect(action('RegistrationController@index'));
        $this->assertDeleted($registration);

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function edit_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $registration = factory(\App\Registration::class)->create();
        $user = factory(\App\User::class)->create();

        $response = $this->actingAs($user)->get(route('registration.edit', [$registration]));

        $response->assertOk();
        $response->assertViewIs('registrations.edit');
        $response->assertViewHas('registration');
        $response->assertViewHas('retreats');
        $response->assertViewHas('rooms');
        $response->assertViewHas('defaults');

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function index_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $user = factory(\App\User::class)->create();

        $response = $this->actingAs($user)->get(route('registration.index'));

        $response->assertOk();
        $response->assertViewIs('registrations.index');
        $response->assertViewHas('registrations');

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function offwaitlist_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $registration = factory(\App\Registration::class)->create();
        $user = factory(\App\User::class)->create();

        $response = $this->actingAs($user)->get(route('registration.register', ['id' => $registration->id]));

        $response->assertRedirect(back());

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function register_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $registration = factory(\App\Registration::class)->create();
        $user = factory(\App\User::class)->create();

        $response = $this->actingAs($user)->get(route('registration.register', ['retreat_id' => $registration->retreat_id]));

        $response->assertOk();
        $response->assertViewIs('registrations.create');
        $response->assertViewHas('retreats');
        $response->assertViewHas('retreatants');
        $response->assertViewHas('rooms');
        $response->assertViewHas('defaults');

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function registration_email_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $registration = factory(\App\Registration::class)->create();
        $user = factory(\App\User::class)->create();

        $response = $this->actingAs($user)->get('registration/{participant}/email');

        $response->assertRedirect('person/'.$participant->contact->id);

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function show_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $registration = factory(\App\Registration::class)->create();
        $user = factory(\App\User::class)->create();

        $response = $this->actingAs($user)->get(route('registration.show', [$registration]));

        $response->assertOk();
        $response->assertViewIs('registrations.show');
        $response->assertViewHas('registration');

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function store_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $user = factory(\App\User::class)->create();

        $response = $this->actingAs($user)->post(route('registration.store'), [
            // TODO: send request data
        ]);

        $response->assertRedirect(action('RegistrationController@index'));

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function store_validates_with_a_form_request()
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\RegistrationController::class,
            'store',
            \App\Http\Requests\StoreRegistrationRequest::class
        );
    }

    /**
     * @test
     */
    public function store_group_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $user = factory(\App\User::class)->create();

        $response = $this->actingAs($user)->post('registration/add_group', [
            // TODO: send request data
        ]);

        $response->assertRedirect(action('RetreatController@show', $retreat->id));

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function store_group_validates_with_a_form_request()
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\RegistrationController::class,
            'store_group',
            \App\Http\Requests\StoreGroupRegistrationRequest::class
        );
    }

    /**
     * @test
     */
    public function update_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $registration = factory(\App\Registration::class)->create();
        $user = factory(\App\User::class)->create();

        $response = $this->actingAs($user)->put(route('registration.update', [$registration]), [
            // TODO: send request data
        ]);

        $response->assertRedirect(action('PersonController@show', $registration->contact_id));

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function update_validates_with_a_form_request()
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\RegistrationController::class,
            'update',
            \App\Http\Requests\UpdateRegistrationRequest::class
        );
    }

    /**
     * @test
     */
    public function update_group_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $registration = factory(\App\Registration::class)->create();
        $user = factory(\App\User::class)->create();

        $response = $this->actingAs($user)->post(route('registration.update_group', ['id' => $registration->id]), [
            // TODO: send request data
        ]);

        $response->assertRedirect(action('RegistrationController@index'));

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function update_group_validates_with_a_form_request()
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\RegistrationController::class,
            'update_group',
            \App\Http\Requests\UpdateGroupRegistrationRequest::class
        );
    }

    /**
     * @test
     */
    public function waitlist_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $registration = factory(\App\Registration::class)->create();
        $user = factory(\App\User::class)->create();

        $response = $this->actingAs($user)->get(route('registration.waitlist', ['id' => $registration->id]));

        $response->assertRedirect(back());

        // TODO: perform additional assertions
    }

    // test cases...
}
