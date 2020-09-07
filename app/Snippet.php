<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Snippet extends Model implements Auditable
{
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    public function language()
    {
        return $this->hasOne(Language::class, 'name', 'locale');
    }


    public function getLanguageLabelAttribute()
    {
        if (isset($this->language->label)) {
            return $this->language->label;
        } else {
            return 'Unknown language';
        }
    }
}
