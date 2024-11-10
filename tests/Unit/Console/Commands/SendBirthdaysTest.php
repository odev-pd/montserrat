<?php

namespace Tests\Unit\Console\Commands;

use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Mail;
use Tests\TestCase;

/**
 * @see \App\Console\Commands\SendBirthdays
 */
class SendBirthdaysTest extends TestCase
{
    // use DatabaseTransactions;
    use withFaker;

    #[Test]
    public function it_runs_successfully(): void
    {
        // $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $person = \App\Models\Contact::factory()->create([
            'contact_type' => '1',
            'subcontact_type' => null,
            'birth_date' => now(),
        ]);
        $email = \App\Models\Email::factory()->create([
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
