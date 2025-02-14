<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class ActivityContact extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

    protected $table = 'activity_contact';

    public function activity(): HasOne
    {
        return $this->hasOne(Activity::class, 'id', 'activity_id');
    }

    public function contact(): HasOne
    {
        return $this->hasOne(Contact::class, 'id', 'contact_id');
    }

    public function getDetailsAttribute()
    {
        return $this->activity->details;
    }

    public function getTouchedAtAttribute()
    {
        return $this->activity->activity_date_time;
    }
}
