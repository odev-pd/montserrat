<?php

namespace Tests\Feature\Http\Controllers;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

//TODO: Create unit tests for new admin.config pages (index, mail, google_calendar, etc.)

/**
 * @see \App\Http\Controllers\PageController
 */
class PageControllerTest extends TestCase
{
    // use DatabaseTransactions;
    use withFaker;

    /**
     * @test
     */
    public function about_displays_view()
    {
        $user = \App\Models\User::factory()->create();

        $response = $this->actingAs($user)->get(route('about'));

        $response->assertOk();
        $response->assertViewIs('pages.about');
    }

    /**
     * @test
     */
    public function bookstore_displays_view()
    {
        $user = \App\Models\User::factory()->create();

        $response = $this->actingAs($user)->get(route('bookstore'));

        $response->assertOk();
        $response->assertViewIs('pages.bookstore');
    }

    /**
     * @test
     */
    public function config_index_returns_an_ok_response()
    {
        $user = $this->createUserWithPermission('show-admin-menu');

        $response = $this->actingAs($user)->get(route('admin.config.index'));

        $response->assertOk();
        $response->assertViewIs('admin.config.index');
    }

    /**
     * @test
     */
    public function config_index_client_returns_403()
    {
        $user = \App\Models\User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.config.index'));
        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function config_application_returns_an_ok_response()
    {
        $user = $this->createUserWithPermission('show-admin-menu');

        $response = $this->actingAs($user)->get(route('admin.config.application'));

        $response->assertOk();
        $response->assertViewIs('admin.config.application');
    }

    /**
     * @test
     */
    public function config_application_client_returns_403()
    {
        $user = \App\Models\User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.config.application'));
        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function config_mail_returns_an_ok_response()
    {
        $user = $this->createUserWithPermission('show-admin-menu');

        $response = $this->actingAs($user)->get(route('admin.config.mail'));

        $response->assertOk();
        $response->assertViewIs('admin.config.mail');
    }

    /**
     * @test
     */
    public function config_mail_client_returns_403()
    {
        $user = \App\Models\User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.config.mail'));
        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function config_gate_returns_an_ok_response()
    {
        $user = $this->createUserWithPermission('show-admin-menu');

        $response = $this->actingAs($user)->get(route('admin.config.gate'));

        $response->assertOk();
        $response->assertViewIs('admin.config.gate');
    }

    /**
     * @test
     */
    public function config_gate_client_returns_403()
    {
        $user = \App\Models\User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.config.gate'));
        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function config_google_calendar_returns_an_ok_response()
    {
        $user = $this->createUserWithPermission('show-admin-menu');

        $response = $this->actingAs($user)->get(route('admin.config.google_calendar'));

        $response->assertOk();
        $response->assertViewIs('admin.config.google_calendar');
    }

    /**
     * @test
     */
    public function config_google_calendar_client_returns_403()
    {
        $user = \App\Models\User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.config.google_calendar'));
        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function config_google_client_returns_an_ok_response()
    {
        $user = $this->createUserWithPermission('show-admin-menu');

        $response = $this->actingAs($user)->get(route('admin.config.google_client'));

        $response->assertOk();
        $response->assertViewIs('admin.config.google_client');
    }

    /**
     * @test
     */
    public function config_google_client_returns_403()
    {
        $user = \App\Models\User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.config.google_client'));
        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function config_mailgun_displays_view()
    {
        $user = $this->createUserWithPermission('show-admin-menu');

        $response = $this->actingAs($user)->get(route('admin.config.mailgun'));

        $response->assertOk();
        $response->assertViewIs('admin.config.mailgun');
    }

    /**
     * @test
     */
    public function config_mailgun_returns_403()
    {
        $user = \App\Models\User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.config.mailgun'));

        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function config_twilio_displays_view()
    {
        $user = $this->createUserWithPermission('show-admin-menu');

        $response = $this->actingAs($user)->get(route('admin.config.twilio'));

        $response->assertOk();
        $response->assertViewIs('admin.config.twilio');
    }

    /**
     * @test
     */
    public function config_twilio_returns_403()
    {
        $user = \App\Models\User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.config.twilio'));

        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function contact_info_report_displays_view()
    {
        $user = $this->createUserWithPermission('show-contact');
        $contact = \App\Models\Contact::factory()->create();

        $response = $this->actingAs($user)->get('report/contact_info_report/'.$contact->id);

        $response->assertOk();
        $response->assertViewIs('reports.contact_info');
        $response->assertViewHas('person', $contact);
    }

    /**
     * @test
     */
    public function contact_info_returns_404()
    {
        $user = $this->createUserWithPermission('show-contact');

        $response = $this->actingAs($user)->get('report/contact_info_report/-1');

        $response->assertNotFound();
    }

    /**
     * @test
     */
    public function finance_displays_view()
    {
        $user = $this->createUserWithPermission('show-donation');

        $response = $this->actingAs($user)->get(route('finance'));

        $response->assertOk();
        $response->assertViewIs('pages.finance');
    }

    /**
     * @test
     */
    public function finance_returns_403()
    {
        $user = \App\Models\User::factory()->create();

        $response = $this->actingAs($user)->get(route('finance'));

        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function finance_agc_acknowledge_returns_an_ok_response()
    {
        $user = $this->createUserWithPermission('show-donation');
        $email = \App\Models\Email::factory()->create([
            'email' => $user->email,
        ]);

        $payment = \App\Models\Payment::factory()->create();
        $donation = \App\Models\Donation::findOrFail($payment->donation_id);
        $donation->donation_description = 'AGC - General';
        $donation->save();
        $contact = \App\Models\Contact::findOrFail($donation->contact_id);

        $response = $this->actingAs($user)->get('donation/'.$payment->donation_id.'/agc_acknowledge');

        $response->assertOk();
        $response->assertViewIs('reports.finance.agc_acknowledge');
        $response->assertViewHas('donation');
        $response->assertSeeText($contact->agc_household_name);
    }

    /**
     * @test
     *
     * when there is no contact associated with the current authenticated user, the AGC Acknowledgement letter touchpoint cannot be created
     * the user is redirected back to the page they came from and a flash error message tells them to create a contact associated with their email address
     */
    public function finance_agc_acknowledge_no_email_returns_a_flash_response()
    {
        $user = $this->createUserWithPermission('show-donation');
        $payment = \App\Models\Payment::factory()->create();

        $response = $this->actingAs($user)->from(URL('donation/'.$payment->donation_id))->get('donation/'.$payment->donation_id.'/agc_acknowledge');

        $response->assertSessionHas('flash_notification');
        $response->assertRedirect(route('donation.show', $payment->donation_id));
    }

    public function eoy_acknowledgment_returns_an_ok_response()
    {
        //$this->withoutExceptionHandling();
        $user = $this->createUserWithPermission('show-donation');
        $email = \App\Models\Email::factory()->create([
            'email' => $user->email,
        ]);

        $donation = \App\Models\Donation::factory()->create();
        $payments = \App\Models\Payment::factory()->count(3)->create(
            ['donation_id' => $donation->donation_id]
        );
        // dd($donation,$payments);
        $response = $this->actingAs($user)->get('person/'.$donation->contact_id.'/eoy_acknowledgment');

        $response->assertOk();
        // TODO: assert that the pdf file is created, consider making a seperate view for display and testing number of entries depending on start and end dates
    }

    /**
     * @test
     */
    public function finance_agc_acknowledge_returns_403()
    {
        $user = \App\Models\User::factory()->create();
        $payment = \App\Models\Payment::factory()->create();

        $response = $this->actingAs($user)->get('donation/'.$payment->donation_id.'/agc_acknowledge');

        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function eoy_acknowledgment_returns_403()
    {
        $user = \App\Models\User::factory()->create();
        $contact = \App\Models\Contact::factory()->create();
        $response = $this->actingAs($user)->get('person/'.$contact->id.'/eoy_acknowledgment');

        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function finance_cash_deposit_returns_an_ok_response()
    {
        $user = $this->createUserWithPermission('show-donation');

        $response = $this->actingAs($user)->get(route('report.finance.cash_deposit'));

        $response->assertOk();
        $response->assertViewIs('reports.finance.cash_deposit');
        $response->assertViewHas('report_date');
        $response->assertViewHas('grouped_payments');
        $response->assertViewHas('grand_total');
    }

    /**
     * @test
     */
    public function finance_cash_deposit_with_hyphenated_date_returns_an_ok_response()
    {
        $user = $this->createUserWithPermission('show-donation');
        $yesterday = Carbon::now()->subDay()->toDateString();
        $description = $this->faker->randomElement(['Cash', 'Check', 'Wire transfer']);

        $payment = \App\Models\Payment::factory()->create([
            'payment_date' => $yesterday,
            'payment_description' => $description,
        ]);

        //test with hyphens
        $response = $this->actingAs($user)->get('report/finance/cash_deposit/'.$yesterday);
        $response->assertOk();
        $response->assertViewIs('reports.finance.cash_deposit');
        $response->assertViewHas('report_date');
        $response->assertViewHas('grouped_payments');
        $response->assertViewHas('grand_total');
        $response->assertSeeText('Cash/Check Bank Deposit Report for');
        $response->assertSeeText(number_format($payment->donation->donation_amount, 2));
    }

    /**
     * @test
     */
    public function finance_cash_deposit_with_unhyphenated_date_returns_an_ok_response()
    {
        $user = $this->createUserWithPermission('show-donation');
        $yesterday = Carbon::now()->subDay()->toDateString();
        $description = $this->faker->randomElement(['Cash', 'Check', 'Wire transfer']);

        $payment = \App\Models\Payment::factory()->create([
            'payment_date' => $yesterday,
            'payment_description' => $description,
        ]);

        // remove hyphens
        $yesterday = str_replace('-', '', $yesterday);

        // test without hyphens
        $response = $this->actingAs($user)->get('report/finance/cash_deposit/'.$yesterday);
        $response->assertOk();
        $response->assertViewIs('reports.finance.cash_deposit');
        $response->assertViewHas('report_date');
        $response->assertViewHas('grouped_payments');
        $response->assertViewHas('grand_total');
        $response->assertSeeText('Cash/Check Bank Deposit Report for');
        $response->assertSeeText(number_format($payment->donation->donation_amount, 2));
    }

    /**
     * @test
     */
    public function finance_cc_deposit_returns_an_ok_response()
    {
        $user = $this->createUserWithPermission('show-donation');

        $response = $this->actingAs($user)->get(route('report.finance.cc_deposit'));

        $response->assertOk();
        $response->assertViewIs('reports.finance.cc_deposit');
        $response->assertViewHas('report_date');
        $response->assertViewHas('grouped_payments');
        $response->assertViewHas('grand_total');
    }

    /**
     * @test
     */
    public function finance_cc_deposit_with_hyphenated_date_returns_an_ok_response()
    {
        $user = $this->createUserWithPermission('show-donation');
        $yesterday = Carbon::now()->subDay()->toDateString();

        $description = 'Credit card';

        $payment = \App\Models\Payment::factory()->create([
            'payment_date' => $yesterday,
            'payment_description' => $description,
        ]);

        $response = $this->actingAs($user)->get(route('report.finance.cc_deposit', ['day' => $yesterday]));
        $response->assertOk();
        $response->assertViewIs('reports.finance.cc_deposit');
        $response->assertViewHas('report_date', function ($date) use ($yesterday) {
            return $date->toDateString() === $yesterday;
        });

        $response->assertViewHas('grouped_payments');
        $response->assertViewHas('grand_total');
        $response->assertSeeText('Credit Card (Internet) Bank Deposit Report');
        $response->assertSeeText($description);
        $response->assertSeeText(number_format($payment->donation->donation_amount, 2));
    }

    /**
     * @test
     */
    public function finance_cc_deposit_with_unhyphenated_date_returns_an_ok_response()
    {
        $user = $this->createUserWithPermission('show-donation');
        $yesterday = Carbon::now()->subDay()->toDateString();

        // remove hyphens
        $yesterday_unhyphenated = str_replace('-', '', $yesterday);

        $description = 'Credit card';

        $payment = \App\Models\Payment::factory()->create([
            'payment_date' => $yesterday,
            'payment_description' => $description,
        ]);

        $response = $this->actingAs($user)->get(route('report.finance.cc_deposit', ['day' => $yesterday_unhyphenated]));
        $response->assertOk();
        $response->assertViewIs('reports.finance.cc_deposit');
        $response->assertViewHas('report_date', function ($date) use ($yesterday) {
            return $date->toDateString() === $yesterday;
        });

        $response->assertViewHas('grouped_payments');
        $response->assertViewHas('grand_total');
        $response->assertSeeText('Credit Card (Internet) Bank Deposit Report');
        $response->assertSeeText($description);
        $response->assertSeeText(number_format($payment->donation->donation_amount, 2));
    }

    /**
     * @test
     */
    public function finance_deposits_returns_an_ok_response()
    {
        $user = $this->createUserWithPermission('show-donation');

        $response = $this->actingAs($user)->get('report/finance/deposits');

        $response->assertOk();
        $response->assertViewIs('reports.finance.deposits');
        $response->assertViewHas('grouped_payments');
        $response->assertViewHas('payments');
    }

    /**
     * @test
     */
    public function finance_invoice_report_displays_view()
    {
        // $this->withoutExceptionHandling();
        $user = $this->createUserWithPermission('show-donation');
        $donation = \App\Models\Donation::factory()->create();

        $response = $this->actingAs($user)->get('donation/'.$donation->donation_id.'/invoice');

        $response->assertOk();
        $response->assertViewIs('reports.finance.invoice');
        $response->assertViewHas('donation');
        $response->assertSee('Invoice #'.$donation->donation_id);
    }

    /**
     * @test
     */
    public function finance_invoice_returns_404()
    {
        $user = $this->createUserWithPermission('show-donation');

        $response = $this->actingAs($user)->get('donation/-1/invoice');

        $response->assertNotFound();
    }

    /**
     * @test
     */
    public function finance_reconcile_deposit_show_returns_an_ok_response()
    {
        $user = \App\Models\User::factory()->create();
        $user->assignRole('test-role:finance_reconcile_deposit_show');

        $registration = \App\Models\Registration::factory()->create([
            'event_id' => config('polanco.event.open_deposit'),
        ]);
        $response = $this->actingAs($user)->get(route('depositreconcile.show'));

        $response->assertOk();
        $response->assertViewIs('reports.finance.reconcile_deposits');
        $response->assertViewHas('diffpg');
        $response->assertViewHas('diffrg');
        $response->assertSeeText('Open Deposit Reconciliation Report');
        $response->assertSeeText(number_format($registration->deposit, 2));
    }

    /**
     * @test
     */
    public function finance_reconcile_deposit_show_with_event_id_returns_an_ok_response()
    {
        $user = \App\Models\User::factory()->create();
        $user->assignRole('test-role:finance_reconcile_deposit_show');

        $registration = \App\Models\Registration::factory()->create();
        $event_id = $registration->event_id;

        $response = $this->actingAs($user)->get('admin/deposit/reconcile/'.$event_id);

        $response->assertOk();
        $response->assertViewIs('reports.finance.reconcile_deposits');
        $response->assertViewHas('diffpg');
        $response->assertViewHas('diffrg');
        $response->assertSeeText('Open Deposit Reconciliation Report');
        $response->assertSeeText(number_format($registration->deposit, 2));
    }

    /**
     * @test
     */
    public function finance_retreatdonations_returns_an_ok_response()
    {
        $user = $this->createUserWithPermission('show-donation');
        $retreat = \App\Models\Retreat::factory()->create();
        $donation = \App\Models\Donation::factory()->create([
            'event_id' => $retreat->id,
        ]);

        $response = $this->actingAs($user)->get('report/finance/retreatdonations/'.$retreat->idnumber);

        $response->assertOk();
        $response->assertViewIs('reports.finance.retreatdonations');

        $response->assertViewHas('retreat', function ($r) use ($retreat) {
            return $r->idnumber == $retreat->idnumber;
        });

        $response->assertViewHas('grouped_donations');
        $response->assertViewHas('donations');
        $response->assertViewHas('donations', function ($donations) use ($donation) {
            return $donations->contains('donation_description', $donation->donation_description);
        });
        $response->assertSeeText('Donations for '.$retreat->title);
        $response->assertSeeText($retreat->idnumber);
    }

    /**
     * @test
     */
    public function grounds_displays_view()
    {
        $user = \App\Models\User::factory()->create();

        $response = $this->actingAs($user)->get(route('grounds'));

        $response->assertOk();
        $response->assertViewIs('pages.grounds');
    }

    /**
     * @test
     */
    public function housekeeping_displays_view()
    {
        $user = \App\Models\User::factory()->create();

        $response = $this->actingAs($user)->get(route('housekeeping'));

        $response->assertOk();
        $response->assertViewIs('pages.housekeeping');
    }

    /**
     * @test
     */
    public function kitchen_displays_view()
    {
        $user = \App\Models\User::factory()->create();

        $response = $this->actingAs($user)->get(route('kitchen'));

        $response->assertOk();
        $response->assertViewIs('pages.kitchen');
    }

    /**
     * @test
     */
    public function maintenance_displays_view()
    {
        $user = \App\Models\User::factory()->create();

        $response = $this->actingAs($user)->get(route('maintenance'));

        $response->assertOk();
        $response->assertViewIs('pages.maintenance');
    }

    /**
     * @test
     */
    public function reservation_displays_view()
    {
        $user = \App\Models\User::factory()->create();

        $response = $this->actingAs($user)->get(route('reservation'));

        $response->assertOk();
        $response->assertViewIs('pages.reservation');
    }

    /**
     * @test
     */
    public function restricted_displays_view()
    {
        $user = \App\Models\User::factory()->create();

        $response = $this->actingAs($user)->get(route('restricted'));

        $response->assertOk();
        $response->assertViewIs('pages.restricted');
    }

    /**
     * @test
     */
    public function retreat_displays_view()
    {
        $user = \App\Models\User::factory()->create();

        $response = $this->actingAs($user)->get(route('retreats'));

        $response->assertOk();
        $response->assertViewIs('pages.retreat');
    }

    /**
     * @test
     */
    public function retreatantinforeport_displays_view()
    {
        $user = \App\Models\User::factory()->create();
        $user->assignRole('test-role:retreatantinforeport');
        $retreat = \App\Models\Retreat::factory()->create();
        $registrants = \App\Models\Registration::factory()->count(3)->create([
            'event_id' => $retreat->id,
            'canceled_at' => null,
        ]);

        $response = $this->actingAs($user)->get('report/retreatantinfo/'.$retreat->idnumber);
        $response->assertOk();
        $response->assertViewIs('reports.retreatantinfo2');
        $response->assertViewHas('registrations');
        $registrations = $response->viewData('registrations');
        $this->assertCount(3, $registrations);
        $this->assertEquals($registrants->sortBy('id')->pluck('id'), $registrations->sortBy('id')->pluck('id'));
    }

    /**
     * @test
     */
    public function retreatlistingreport_returns_an_ok_response()
    {
        $user = $this->createUserWithPermission('show-contact');
        $retreat = \App\Models\Retreat::factory()->create();
        $registrants = \App\Models\Registration::factory()->count(2)->create([
            'event_id' => $retreat->id,
            'canceled_at' => null,
        ]);

        $response = $this->actingAs($user)->get('report/retreatlisting/'.$retreat->idnumber);

        $response->assertOk();
        $response->assertViewIs('reports.retreatlisting');
        $response->assertViewHas('registrations');
        $response->assertSee('Retreat Listing');
        $response->assertSee('Registered Retreatant');
    }

    /**
     * @test
     */
    public function retreatrosterreport_returns_an_ok_response()
    {
        $user = $this->createUserWithPermission('show-contact');
        $retreat = \App\Models\Retreat::factory()->create();

        $response = $this->actingAs($user)->get('report/retreatroster/'.$retreat->idnumber);

        $response->assertOk();
        $response->assertViewIs('reports.retreatroster');
        $response->assertViewHas('registrations');
    }

    /**
     * @test
     */
    public function retreatregistrationsreport_returns_an_ok_response()
    {
        $user = $this->createUserWithPermission('show-registration');
        $retreat = \App\Models\Retreat::factory()->create();

        $response = $this->actingAs($user)->get('report/retreatregistrations/'.$retreat->idnumber);

        $response->assertOk();
        $response->assertViewIs('reports.retreatregistrations');
        $response->assertViewHas('registrations');
    }

    /**
     * @test
     */
    public function support_displays_view()
    {
        $user = \App\Models\User::factory()->create();

        $response = $this->actingAs($user)->get(route('support'));

        $response->assertOk();
        $response->assertViewIs('pages.support');
        $response->assertSee('Support Page');
    }

    /**
     * @test
     */
    public function user_displays_view()
    {
        $user = \App\Models\User::factory()->create();

        $response = $this->actingAs($user)->get(route('users'));

        $response->assertOk();
        $response->assertViewIs('pages.user');
    }

    /**
     * @test
     */
    public function welcome_displays_view()
    {
        $user = \App\Models\User::factory()->create();

        $mock = new MockHandler([
            new Response(200, [], '<p><b>Hello</b>, World!</p>'),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);
        $this->swap(Client::class, $client);

        $response = $this->actingAs($user)->get(route('welcome'));

        $response->assertOk();
        $response->assertViewIs('welcome');
        $response->assertViewHas('quote');
    }
}
