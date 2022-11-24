<?php

namespace App\Models;

use Carbon\Carbon;
use Html;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use OwenIt\Auditing\Contracts\Auditable;

class Retreat extends Model implements Auditable
{
    use HasFactory;
    use HasFactory;
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'event';

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'disabled_at' => 'datetime',
    ];  //

    public function setStartDateAttribute($date)
    {
        $this->attributes['start_date'] = Carbon::parse($date);
    }

    public function setEndDateAttribute($date)
    {
        $this->attributes['end_date'] = Carbon::parse($date);
    }

    public function getParticipantCountAttribute()
    {
        // returns count of retreatants and participating ambassadors - does not return directors, innkeepers or assistants
        if ($this->is_active) {
            return $this->participants->count();
        } else {
            return 0;
        }
    }

    public function getHasDepositsAttribute()
    {
        // keep in mind that if/when innkeeper and other not retreatant roles are added will not to use where clause to keep the count accurate and exclude non-participating participants
        $deposits = $this->donations->where('donation_description','=',"Retreat Deposits");
        return $deposits->count() > 0 ? TRUE : FALSE;
    }


    public function getDonationsPledgedSumAttribute()
    {
        // keep in mind that if/when innkeeper and other not retreatant roles are added will not to use where clause to keep the count accurate and exclude non-participating participants
        return $this->donations->sum('donation_amount');
    }

    public function getPaymentsPaidSumAttribute()
    {
        return $this->donations->sum('payments_sum_payment_amount');
    }

    public function getPercentPaidAttribute()
    {
        if ($this->donations_pledged_sum > 0) { //avoid divide by 0 cases
            return number_format((($this->payments_paid_sum / $this->donations_pledged_sum) * 100), 0);
        } else {
            return 0;
        }
    }

    public function getAveragePaidPerNightAttribute()
    {
        if ($this->people_nights > 0) { //avoid divide by 0 cases
            return $this->payments_paid_sum / $this->people_nights;
        } else {
            return 0;
        }
    }

    public function getNightsAttribute()
    {
        $start = $this->start_date->setHour(0)->setMinute(0)->setSecond(0);
        $end = $this->end_date->setHour(0)->setMinute(0)->setSecond(0);
        // keep in mind that if/when innkeeper and other not retreatant roles are added will not to use where clause to keep the count accurate and exclude non-participating participants
        return $start->diffInDays($end);
    }

    public function getPeopleNightsAttribute()
    {
        return $this->nights * $this->participant_count;
    }

    public function getRegistrationCountAttribute()
    {
        // keep in mind that if/when innkeeper and other not retreatant roles are added will not to use where clause to keep the count accurate and exclude non-participating participants
        return $this->registrations->count();
    }

    public function getRetreatantCountAttribute()
    {
        // keep in mind that if/when innkeeper and other not retreatant roles are added will not to use where clause to keep the count accurate and exclude non-participating participants
        return $this->retreatants->count();
    }

    public function getRetreatantWaitlistCountAttribute()
    {
        // keep in mind that if/when innkeeper and other not retreatant roles are added will not to use where clause to keep the count accurate and exclude non-participating participants
        return $this->retreatants_waitlist->count();
    }

    public function assistants()
    {   // TODO: evaluate whether the assumption that this is an individual makes a difference, currently retreat factory will force individual to avoid undefined variable on retreat.show
        return $this->hasMany(Registration::class, 'event_id', 'id')->whereRoleId(config('polanco.participant_role_id.assistant'));
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'entity_id', 'id')->whereEntity('event');
    }

    public function ambassadors()
    {
        // TODO: handle with participants of role Retreat Director or Master - be careful with difference between (registration table) retreat_id and (participant table) event_id
        return $this->hasMany(Registration::class, 'event_id', 'id')->whereRoleId(config('polanco.participant_role_id.ambassador'));
    }

    public function innkeepers()
    {   // TODO: evaluate whether the assumption that this is an individual makes a difference, currently retreat factory will force individual to avoid undefined variable on retreat.show
        return $this->hasMany(Registration::class, 'event_id', 'id')->whereRoleId(config('polanco.participant_role_id.innkeeper'));
    }

    public function event_type()
    {
        return $this->hasOne(EventType::class, 'id', 'event_type_id');
    }

    public function donations()
    {
        return $this->hasMany(Donation::class, 'event_id', 'id')->withSum('payments', 'payment_amount');
    }

    public function participants()
    {
        return $this->registrations()->whereCanceledAt(null)->whereIn('role_id', [config('polanco.participant_role_id.retreatant'), config('polanco.participant_role_id.ambassador')])->whereStatusId(config('polanco.registration_status_id.registered'));
    }

    public function retreatmasters()
    {
        // TODO: handle with participants of role Retreat Director or Master - be careful with difference between (registration table) retreat_id and (participant table) event_id
        return $this->hasMany(Registration::class, 'event_id', 'id')->whereRoleId(config('polanco.participant_role_id.director'));
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class, 'event_id', 'id');
    }

    public function retreatants()
    {
        return $this->registrations()->whereCanceledAt(null)->whereRoleId(config('polanco.participant_role_id.retreatant'))->whereStatusId(config('polanco.registration_status_id.registered'));
    }

    public function retreatants_waitlist()
    {
        return $this->registrations()->whereCanceledAt(null)->whereStatusId(config('polanco.registration_status_id.waitlist'));
    }

    public function getEmailRegisteredRetreatantsAttribute()
    {
        $bcc_list = '';
        foreach ($this->registrations as $registration) {
            if ($registration->status_id == config('polanco.registration_status_id.registered')) {
                if (! empty($registration->retreatant->email_primary_text) && is_null($registration->canceled_at)) {
                    $bcc_list .= $registration->retreatant->email_primary_text.',';
                }
            }
        }

        return 'mailto:?bcc='.$bcc_list;
    }

    public function getEmailWaitlistRetreatantsAttribute()
    {
        $bcc_list = '';
        foreach ($this->registrations as $registration) {
            if ($registration->status_id == config('polanco.registration_status_id.waitlist')) {
                if (! empty($registration->retreatant->email_primary_text) && is_null($registration->canceled_at)) {
                    $bcc_list .= $registration->retreatant->email_primary_text.',';
                }
            }
        }

        return 'mailto:?bcc='.$bcc_list;
    }

    public function getRetreatTypeAttribute()
    {
        //dd($this->event_type);
        if (isset($this->event_type)) {
            return $this->event_type->name;
        } else {
            return;
        }
    }

    public function getRetreatNameAttribute()
    {
        //dd($this->event_type);
        if (isset($this->title)) {
            return $this->title;
        } else {
            return;
        }
    }

    public function getRetreatScheduleLinkAttribute()
    {
        if (Storage::has('event/'.$this->id.'/schedule.pdf')) {
            $img = Html::image('images/schedule.png', 'Schedule', ['title'=>'Schedule']);
            $link = '<a href="'.url('retreat/'.$this->id.'/schedule" ').'class="btn btn-default" style="padding: 3px;">'.$img.'Schedule</a>';

            return $link;
        } else {
            return;
        }
    }

    public function getRetreatContractLinkAttribute()
    {
        if (Storage::has('event/'.$this->id.'/contract.pdf')) {
            $img = Html::image('images/contract.png', 'Contract', ['title'=>'Contract']);
            $link = '<a href="'.url('retreat/'.$this->id.'/contract" ').'class="btn btn-default" style="padding: 3px;">'.$img.'Contract</a>';

            return $link;
        } else {
            return;
        }
    }

    public function getRetreatEvaluationsLinkAttribute()
    {
        if (Storage::has('event/'.$this->id.'/evaluations.pdf')) {
            $img = Html::image('images/evaluation.png', 'Evaluations', ['title'=>'Evaluations']);
            $link = '<a href="'.url('retreat/'.$this->id.'/evaluations" ').'class="btn btn-default" style="padding: 3px;">'.$img.'Evaluation</a>';

            return $link;
        } else {
            return;
        }
    }

    public function getRetreatTeamAttribute()
    {
        $team = '';
        $directors = $this->retreatmasters()->get();
        $innkeepers = $this->innkeepers()->get();
        $assistants = $this->assistants()->get();

        foreach ($directors as $director) {
            if (! empty($director->contact->last_name)) {
                $team .= $director->contact->last_name.'(D) ';
            }
        }

        foreach ($innkeepers as $innkeeper) {
            if (! empty($innkeeper->contact->last_name)) {
                $team .= $innkeeper->contact->last_name.'(I) ';
            }
        }

        foreach ($assistants as $assistant) {
            if (! empty($assistant->contact->last_name)) {
                $team .= $assistant->contact->last_name.'(A) ';
            }
        }

        return $team;
    }

    /*
     * Returns an array of attendee email addresses to be added to a Google Calendar event
     * see https://developers.google.com/google-apps/calendar/create-events (for PHP section)
     *  'attendees' => array(
            array('email' => 'lpage@example.com'),
            array('email' => 'sbrin@example.com'),
        )
     */
    public function getRetreatAttendeesAttribute()
    {
        $attendees = [];
        $directors = $this->retreatmasters()->get();
        //dd($directors);
        foreach ($directors as $director) {
            if (! empty($director->email_primary->email)) {
                array_push($attendees, ['email'=>$director->email_primary->email]);
            }
        }
        $innkeeper = $this->innkeeper()->first();
        //dd($innkeeper->last_name);
        if (! empty($innkeeper->email_primary->email)) {
            array_push($attendees, ['email'=>$innkeeper->email_primary->email]);
        }
        $assistant = $this->assistant()->first();
        if (! empty($assistant->email_primary->email)) {
            array_push($attendees, ['email'=>$assistant->email_primary->email]);
        }

        return $attendees;
    }

    public function scopeType($query, $event_type_id)
    {
        return $query->where('event_type_id', $event_type_id);
    }

    public function scopeFiltered($query, $filters)
    {
        foreach ($filters->query as $filter => $value) {
            if ($filter == 'begin_date' && ! empty($value)) {
                $begin_date = Carbon::parse($value);
                $query->where('start_date', '>=', $begin_date);
            }
            if ($filter == 'end_date' && ! empty($value)) {
                $end_date = Carbon::parse($value);
                $query->where('start_date', '<=', $end_date);
            }

            if ($filter == 'title' && ! empty($value)) {
                $query->where($filter, 'like', '%'.$value.'%');
            }
            if ($filter == 'idnumber' && ! empty($value)) {
                $query->where($filter, 'like', '%'.$value.'%');
            }
            if ($filter == 'event_type_id' && ! empty($value)) {
                $query->where($filter, '=', $value);
            }
        }

        return $query;
    }
}
