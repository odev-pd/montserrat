<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Registration extends Model
{
    use SoftDeletes;
    protected $table = 'participant';
    protected $dates = ['register_date', 'registration_confirm_date', 'attendance_confirm_date', 'canceled_at', 'arrived_at','departed_at','updated_at', 'deleted_at','created_at'];

    public function setArrivedAtAttribute($date)
    {
        if (strlen($date)) {
            $this->attributes['arrived_at'] = Carbon::parse($date);
        } else {
            $this->attributes['arrived_at'] = null;
        }
    }
    public function setAttendanceConfirmDateAttribute($date)
    {
        if (strlen($date)) {
            $this->attributes['attendance_confirm_date'] = Carbon::parse($date);
        } else {
            $this->attributes['attendance_confirm_date'] = null;
            //dd($this->attributes['confirmattend']);
        }
    }
    public function setCanceledAtAttribute($date)
    {
        if (strlen($date)) {
            $this->attributes['canceled_at'] = Carbon::parse($date);
        } else {
            $this->attributes['canceled_at'] = null;
        }
    }
    public function setCreatedAtAttribute($date)
    {
        if (strlen($date)) {
            $this->attributes['created_at'] = Carbon::parse($date);
        } else {
            $this->attributes['created_at'] = null;
        }
    }
    public function setDeletedAtAttribute($date)
    {
        if (strlen($date)) {
            $this->attributes['deleted_at'] = Carbon::parse($date);
        } else {
            $this->attributes['deleted_at'] = null;
        }
    }
    public function setDepartedAtAttribute($date)
    {
        if (strlen($date)) {
            $this->attributes['departed_at'] = Carbon::parse($date);
        } else {
            $this->attributes['departed_at'] = null;
        }
    }
    public function setRegisterDateAttribute($date)
    {
        if (strlen($date)) {
            $this->attributes['register_date'] = Carbon::parse($date);
        } else {
            $this->attributes['register_date'] = null;
        }
    }
    public function setRegistrationConfirmDateAttribute($date)
    {
        if (strlen($date)) {
            $this->attributes['registration_confirm_date'] = Carbon::parse($date);
        } else {
            $this->attributes['registration_confirm_date'] = null;
        }
    }
    public function setUpdatedAtAttribute($date)
    {
        if (strlen($date)) {
            $this->attributes['updated_at'] = Carbon::parse($date);
        } else {
            $this->attributes['updated_at'] = null;
        }
    }

    public function getAttendanceConfirmDateTextAttribute()
    {
        if (isset($this->attendance_confirm_date)) {
            return date('F d, Y', strtotime($this->attendance_confirm_date));
        } else {
            return 'N/A';
        }
    }
    public function getDonationPledgeLinkAttribute()
    {
        if (!empty($this->donation_id)) {
            $path=url('donation/'.$this->donation_id);
            $pledged = is_null($this->donation) ? number_format(0,2) : number_format($this->donation->donation_amount,2);
            return '<a href="'.$path.'">'.$pledged.'</a>';
        } else {
            return number_format(0,2);
        }


    }
    public function getEventLinkAttribute()
    {
        if (!empty($this->event)) {
            $path=url('retreat/'.$this->event->id);
            return '<a href="'.$path.'">'.$this->event->title.'</a>';
        } else {
            return null;
        }
    }
    public function getEventNameAttribute()
    {
        if (!empty($this->event)) {
            return $this->event->title;
        } else {
            return 'N/A';
        }
    }
    public function getParticipantRoleNameAttribute()
    {
        if (isset($this->role_id)) {
            return $this->participant_role_type->name;
        } else {
            return 'Unassigned role';
        }
    }
    public function getParticipantStatusAttribute()
    {
        if (!is_null($this->canceled_at)) {
            return 'Canceled: '.$this->canceled_at;
        }
        if (!is_null($this->arrived_at)) {
            return 'Attended';
        }
        if (!is_null($this->registration_confirm_date)) {
            return 'Confirmed: '.$this->registration_confirm_date;
        }
        if (is_null($this->registration_confirm_date) && !is_null($this->register_date)) {
            return 'Registered:'.$this->register_date;
        }
        return 'Unspecified status';
    }

    public function getStatusNameAttribute()
    {
        if (isset($this->status_id)) {
            return $this->participant_status_type->name;
        } else {
            return 'Unassigned status';
        }
    }

    public function getRegistrationConfirmDateTextAttribute()
    {
        if (isset($this->registration_confirm_date)) {
            return date('F d, Y', strtotime($this->registration_confirm_date));
        } else {
            return 'N/A';
        }
    }
    public function getRegistrationStatusButtonsAttribute()
    {
        $status = '';
        if ((!isset($this->arrived_at)) && (!isset($this->canceled_at)) && (!isset($this->registration_confirm_date)) && ($this->status_id==config('polanco.registration_status_id.registered'))) {
            $status .= '<span class="btn btn-default"><a href="'.url("registration/".$this->id."/confirm").'">Confirmed</a></span>';
        }
        if (!isset($this->arrived_at) && (!isset($this->canceled_at)) && ($this->status_id==config('polanco.registration_status_id.registered'))) {
            $status .= '<span class="btn btn-success"><a href="'.url("registration/".$this->id."/arrive").'">Arrived</a></span>';
        }
        if (!isset($this->arrived_at) && (!isset($this->canceled_at)) && ($this->status_id==config('polanco.registration_status_id.registered'))) {
            $status .= '<span class="btn btn-danger"><a href="'.url("registration/".$this->id."/cancel").'">Canceled</a></span>';
        }
        if ((isset($this->arrived_at)) && (!isset($this->departed_at))&& ($this->status_id==config('polanco.registration_status_id.registered'))) {
            $status .= '<span class="btn btn-warning"><a href="'.url("registration/".$this->id."/depart").'">Departed</a></span>';
        }
        if (isset($this->canceled_at)) {
            $status .= 'Canceled at '.$this->canceled_at;
        }
        if (isset($this->departed_at)) {
            $status .= 'Departed at '.$this->departed_at;
        }
        if (($this->status_id == config('polanco.registration_status_id.waitlist'))  && (!isset($this->canceled_at))) {
            $status .= '<span class="btn btn-warning"><a href="'.url("registration/".$this->id."/offwaitlist").'">Take off Waitlist</a></span>';
        }
        return $status;
    }
    public function getRegistrationStatusAttribute()
    {
        $status = '';
        if (isset($this->register_date) && (!isset($this->canceled_at)) && (!isset($this->arrived_at)) && ($this->status_id==config('polanco.registration_status_id.registered'))) {
            $status .= '<span class="btn btn-default">Registered: '.$this->register_date.'</span>';
        }
        if (isset($this->registration_confirm_date) && (!isset($this->canceled_at)) && (!isset($this->arrived_at)) && ($this->status_id==config('polanco.registration_status_id.registered'))) {
            $status .= '<span class="btn btn-default">Confirmed: '.$this->registration_confirm_date.'</span>';
        }
        if (isset($this->arrived_at) && (!isset($this->canceled_at)) && ($this->status_id==config('polanco.registration_status_id.registered'))) {
            $status .= '<span class="btn btn-success">Arrived: '.$this->arrived_at.'</span>';
        }
        if (isset($this->canceled_at)) {
            $status .= '<span class="btn btn-danger">Canceled: '.$this->canceled_at.'</span>';
        }
        if (isset($this->departed_at)) {
            $status .= '<span class="btn btn-warning">Departed: '.$this->departed_at.'</span>';
        }
        if (($this->status_id == config('polanco.registration_status_id.waitlist')) && (!isset($this->canceled_at)) ) {
            $status .= '<span class="btn btn-warning">Waitlist: '.$this->register_date.'</span>';
        }

        return $status;
    }
    public function getRetreatNameAttribute()
    {
        if (!empty($this->retreat)) {
            return $this->retreat->title;
        } else {
            return 'N/A';
        }
    }
    public function getRetreatEndDateAttribute()
    {
        if (!empty($this->retreat->end_date)) {
            return $this->retreat->end_date;
        } else {
            return null;
        }
    }
    public function getRetreatStartDateAttribute()
    {
        if (!empty($this->retreat->start_date)) {
            return $this->retreat->start_date;
        } else {
            return null;
        }
    }
    public function getRetreatStartDateEsAttribute()
    {
	    if (!empty($this->retreat->start_date)) {
		    setlocale(LC_ALL,'es_US.utf8');
		    return $this->retreat->start_date->formatLocalized('%e de %B de %Y');
        } else {
            return null;
        }
    }


    public function getRoomNameAttribute()
    {
        if (isset($this->room->name)) {
            return $this->room->name;
        } else {
            return 'N/A';
        }
    }

    public function event()
    {
        return $this->hasOne(Retreat::class, 'id', 'event_id');
    }
    public function contact()
    {
        return $this->hasOne(Contact::class, 'id', 'contact_id');
    }
    public function participant_role_type()
    {
        return $this->hasOne(ParticipantRoleType::class, 'id', 'role_id');
    }
    public function participant_status_type()
    {
        return $this->hasOne(ParticipantStatus::class, 'id', 'status_id');
    }
    public function retreat()
    {
        return $this->belongsTo(Retreat::class, 'event_id', 'id');
    }
    public function retreatant()
    {
        return $this->belongsTo(Contact::class, 'contact_id', 'id');
    }
    public function retreatant_events()
    {
        return $this->hasOneThrough(Registration::class, Contact::class,'id','contact_id','contact_id','id');
    }
    public function room()
    {
        return $this->hasOne(Room::class, 'id', 'room_id');
    }
    public function donation()
    {
        return $this->hasOne(Donation::class, 'donation_id', 'donation_id');
    }
    public function getDonationPledgeAttribute() {

        if (!is_null($this->donation)) {
            return $this->donation->donation_amount;

        } else {
            return 0;
        }
    }
    public function getPaymentPaidAttribute() {

        if ((!is_null($this->donation) && (!is_null($this->donation->retreat_offering)))) {
            return $this->donation->retreat_offering->payment_amount;

        } else {
            return 0;
        }
    }
}
