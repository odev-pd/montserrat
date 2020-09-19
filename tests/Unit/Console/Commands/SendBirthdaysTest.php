<?php

namespace Tests\Unit\Console\Commands;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mail;
use Tests\TestCase;

/**
 * @see \App\Console\Commands\SendBirthdays
 */
class SendBirthdaysTest extends TestCase
{
    use WithFaker;

    /**
     * @test
     */
    public function it_runs_successfully()
    {
        // $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $person = factory(\App\Models\Contact::class)->create([
          'contact_type' => '1',
          'subcontact_type' => null,
          'birth_date' => now(),
        ]);
        $email = factory(\App\Models\Email::class)->create([
            'contact_id' => $person->id,
        ]);

        // Mail::fake();

        $this->artisan('email:birthdays')
            ->assertExitCode(0)
            ->run();

        /* TODO: test that the mail is sent
        *    return $mail->hasTo($email->email);
        * });
        */

        // TODO: perform additional assertions to ensure the command behaved as expected
    }
}
