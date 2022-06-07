<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;


class SsDonation extends Model implements Auditable
{
    use HasFactory;
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'ss_donation';

    protected $fillable = ['message_id'];

    public function message()
    {
        return $this->hasOne(Message::class, 'id', 'message_id');
    }

    public function event()
    {
        return $this->hasOne(Retreat::class, 'id', 'event_id');
    }

    public function donor()
    {
        return $this->hasOne(Contact::class, 'id', 'contact_id');
    }




}
