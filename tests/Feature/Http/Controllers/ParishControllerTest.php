<?php

namespace Tests\Feature\Http\Controllers;

use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ParishController
 */
final class ParishControllerTest extends TestCase
{
    // use DatabaseTransactions;
    use withFaker;

    #[Test]
    public function create_returns_an_ok_response(): void
    {
        $user = $this->createUserWithPermission('create-contact');

        $response = $this->actingAs($user)->get(route('parish.create'));

        $response->assertOk();
        $response->assertViewIs('parishes.create');
        $response->assertViewHas('dioceses');
        $response->assertViewHas('pastors');
        $response->assertViewHas('states');
        $response->assertViewHas('countries');
        $response->assertViewHas('defaults');
        $response->assertSeeText('Add a Parish');
    }

    #[Test]
    public function destroy_returns_an_ok_response(): void
    {
        $user = $this->createUserWithPermission('delete-contact');
        $parish = \App\Models\Parish::factory()->create();

        $response = $this->actingAs($user)->delete(route('parish.destroy', [$parish->id]));
        $response->assertSessionHas('flash_notification');

        $response->assertRedirect(action([\App\Http\Controllers\ParishController::class, 'index']));
        $this->assertSoftDeleted($parish);
    }

    #[Test]
    public function edit_returns_an_ok_response(): void
    {
        $user = $this->createUserWithPermission('update-contact');
        $parish = \App\Models\Parish::factory()->create();
        $parish = \App\Models\Contact::findOrFail($parish->id);
        $main_address = \App\Models\Address::factory()->create([
            'contact_id' => $parish->id,
            'location_type_id' => config('polanco.location_type.main'),
            'is_primary' => 1,
        ]);

        $main_phone = \App\Models\Phone::factory()->create([
            'contact_id' => $parish->id,
            'location_type_id' => config('polanco.location_type.main'),
            'is_primary' => 1,
            'phone_type' => 'Phone',
        ]);

        $main_fax = \App\Models\Phone::factory()->create([
            'contact_id' => $parish->id,
            'location_type_id' => config('polanco.location_type.main'),
            'phone_type' => 'Fax',
        ]);

        $main_email = \App\Models\Email::factory()->create([
            'contact_id' => $parish->id,
            'is_primary' => 1,
            'location_type_id' => config('polanco.location_type.main'),
        ]);

        $url_main = \App\Models\Website::factory()->create([
            'contact_id' => $parish->id,
            'website_type' => 'Main',
            'url' => $this->faker->url(),
        ]);
        $url_work = \App\Models\Website::factory()->create([
            'contact_id' => $parish->id,
            'website_type' => 'Work',
            'url' => $this->faker->url(),
        ]);
        $url_facebook = \App\Models\Website::factory()->create([
            'contact_id' => $parish->id,
            'website_type' => 'Facebook',
            'url' => 'https://facebook.com/'.$this->faker->slug(),
        ]);
        $url_google = \App\Models\Website::factory()->create([
            'contact_id' => $parish->id,
            'website_type' => 'Google',
            'url' => 'https://google.com/'.$this->faker->slug(),
        ]);
        $url_instagram = \App\Models\Website::factory()->create([
            'contact_id' => $parish->id,
            'website_type' => 'Instagram',
            'url' => 'https://instagram.com/'.$this->faker->slug(),
        ]);
        $url_linkedin = \App\Models\Website::factory()->create([
            'contact_id' => $parish->id,
            'website_type' => 'LinkedIn',
            'url' => 'https://linkedin.com/'.$this->faker->slug(),
        ]);
        $url_twitter = \App\Models\Website::factory()->create([
            'contact_id' => $parish->id,
            'website_type' => 'Twitter',
            'url' => 'https://twitter.com/'.$this->faker->slug(),
        ]);

        $response = $this->actingAs($user)->get(route('parish.edit', [$parish]));

        $response->assertOk();
        $response->assertViewIs('parishes.edit');
        $response->assertViewHas('parish');
        $response->assertViewHas('dioceses');
        $response->assertViewHas('pastors');
        $response->assertViewHas('states');
        $response->assertViewHas('countries');
        $response->assertViewHas('defaults');
        $response->assertSeeText($parish->organization_name);

        $this->assertTrue($this->findFieldValueInResponseContent('organization_name', $parish->organization_name, 'text', $response->getContent()));
        $this->assertTrue($this->findFieldValueInResponseContent('display_name', $parish->display_name, 'text', $response->getContent()));
        $this->assertTrue($this->findFieldValueInResponseContent('sort_name', $parish->sort_name, 'text', $response->getContent()));
        $this->assertTrue($this->findFieldValueInResponseContent('diocese_id', $parish->diocese_id, 'select', $response->getContent()));
        $this->assertTrue($this->findFieldValueInResponseContent('pastor_id', $parish->pastor_id, 'text', $response->getContent()));
        $this->assertTrue($this->findFieldValueInResponseContent('street_address', $parish->address_primary_street, 'text', $response->getContent()));
        $this->assertTrue($this->findFieldValueInResponseContent('city', $parish->address_primary_city, 'text', $response->getContent()));
        $this->assertTrue($this->findFieldValueInResponseContent('state_province_id', $parish->address_primary_state_id, 'select', $response->getContent()));
        $this->assertTrue($this->findFieldValueInResponseContent('postal_code', $parish->address_primary_postal_code, 'text', $response->getContent()));
        $this->assertTrue($this->findFieldValueInResponseContent('phone_main_phone', $parish->phone_main_phone_number, 'text', $response->getContent()));
        $this->assertTrue($this->findFieldValueInResponseContent('phone_main_fax', $parish->phone_main_fax_number, 'text', $response->getContent()));
        $this->assertTrue($this->findFieldValueInResponseContent('email_primary', $parish->email_primary_text, 'text', $response->getContent()));

        // urls
        $this->assertTrue($this->findFieldValueInResponseContent('url_main', $url_main->url, 'text', $response->getContent()));
        $this->assertTrue($this->findFieldValueInResponseContent('url_work', $url_work->url, 'text', $response->getContent()));
        $this->assertTrue($this->findFieldValueInResponseContent('url_facebook', $url_facebook->url, 'text', $response->getContent()));
        $this->assertTrue($this->findFieldValueInResponseContent('url_instagram', $url_instagram->url, 'text', $response->getContent()));
        $this->assertTrue($this->findFieldValueInResponseContent('url_linkedin', $url_linkedin->url, 'text', $response->getContent()));
        $this->assertTrue($this->findFieldValueInResponseContent('url_twitter', $url_twitter->url, 'text', $response->getContent()));
        // TODO: add note
    }

    #[Test]
    public function index_returns_an_ok_response(): void
    {
        $user = $this->createUserWithPermission('show-contact');
        $parish = \App\Models\Parish::factory()->create();

        $response = $this->actingAs($user)->get(route('parish.index'));

        $parishes = $response->viewData('parishes');

        $response->assertOk();
        $response->assertViewIs('parishes.index');
        $response->assertViewHas('parishes');
        $response->assertViewHas('dioceses');
        $response->assertViewHas('diocese');
        $this->assertGreaterThanOrEqual('1', $parishes->count());
    }

    #[Test]
    public function parish_index_by_diocese_returns_an_ok_response(): void
    {
        $user = $this->createUserWithPermission('show-contact');

        $diocese = \App\Models\Diocese::factory()->create([
            'contact_type' => config('polanco.contact_type.organization'),
            'subcontact_type' => config('polanco.contact_type.diocese'),
        ]);
        $parish = \App\Models\Parish::factory()->create();

        $relationship_diocese = \App\Models\Relationship::factory()->create([
            'contact_id_a' => $diocese->id,
            'contact_id_b' => $parish->id,
            'relationship_type_id' => config('polanco.relationship_type.diocese'),
        ]);

        $response = $this->actingAs($user)->get('parishes/diocese/'.$diocese->id);
        $parishes = $response->viewData('parishes');

        $response->assertOk();
        $response->assertViewIs('parishes.index');
        $response->assertViewHas('parishes');
        $response->assertViewHas('dioceses');
        $response->assertViewHas('diocese');
        $this->assertGreaterThanOrEqual('1', $parishes->count());
    }

    #[Test]
    public function show_returns_an_ok_response(): void
    {
        $user = $this->createUserWithPermission('show-contact');
        $parish = \App\Models\Parish::factory()->create();

        $response = $this->actingAs($user)->get(route('parish.show', [$parish]));

        $response->assertOk();
        $response->assertViewIs('parishes.show');
        $response->assertViewHas('parish');
        $response->assertViewHas('files');
        $response->assertViewHas('relationship_filter_types');
        $response->assertSeeText($parish->display_name);
    }

    #[Test]
    public function store_returns_an_ok_response(): void
    {
        $user = $this->createUserWithPermission('create-contact');
        $parish_name = 'St. '.$this->faker->firstName().' Parish';

        $response = $this->actingAs($user)->post(route('parish.store'), [
            'organization_name' => $parish_name,
            'display_name' => $parish_name,
            'sort_name' => $parish_name,
        ]);

        $response->assertRedirect(action([\App\Http\Controllers\ParishController::class, 'index']));
        $response->assertSessionHas('flash_notification');

        $this->assertDatabaseHas('contact', [
            'contact_type' => config('polanco.contact_type.organization'),
            'subcontact_type' => config('polanco.contact_type.parish'),
            'sort_name' => $parish_name,
            'display_name' => $parish_name,
            'organization_name' => $parish_name,
        ]);
    }

    #[Test]
    public function store_validates_with_a_form_request(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ParishController::class,
            'store',
            \App\Http\Requests\StoreParishRequest::class
        );
    }

    #[Test]
    public function update_returns_an_ok_response(): void
    {
        $user = $this->createUserWithPermission('update-contact');
        $parish = \App\Models\Parish::factory()->create();

        $original_sort_name = $parish->sort_name;
        $new_parish_name = 'St. '.$this->faker->firstName().' Parish of the Renewal';

        $response = $this->actingAs($user)->put(route('parish.update', [$parish]), [
            'sort_name' => $new_parish_name,
            'display_name' => $new_parish_name,
            'organization_name' => $new_parish_name,
            'id' => $parish->id,
        ]);

        $updated = \App\Models\Contact::findOrFail($parish->id);
        $response->assertSessionHas('flash_notification');
        $response->assertRedirect(action([\App\Http\Controllers\ParishController::class, 'show'], $parish->id));
        $this->assertEquals($updated->sort_name, $new_parish_name);
        $this->assertNotEquals($updated->sort_name, $original_sort_name);
    }

    #[Test]
    public function update_validates_with_a_form_request(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ParishController::class,
            'update',
            \App\Http\Requests\UpdateParishRequest::class
        );
    }

    // test cases...
}
