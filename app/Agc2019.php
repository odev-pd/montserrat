<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Agc2019 extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'agc_household_name';
    protected $dates = ['last_event', 'last_payment'];
    protected $fillable = ['contact_id'];
    protected $primaryKey = 'contact_id';
    public $timestamps = false;
}
