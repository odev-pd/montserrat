<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class RelationshipType extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

    protected $table = 'relationship_type';

    public function getContactTypeAIdAttribute()
    {
        if (! empty($this->contacttype_a)) {
            return $this->contacttype_a->id;
        } else {
            return;
        }
    }

    public function getContactTypeBIdAttribute()
    {
        if (! empty($this->contacttype_b)) {
            return $this->contacttype_b->id;
        } else {
            return;
        }
    }

    public function getContactSubTypeAIdAttribute()
    {
        if (! empty($this->contactsubtype_a)) {
            return $this->contactsubtype_a->id;
        } else {
            return;
        }
    }

    public function getContactSubTypeBIdAttribute()
    {
        if (! empty($this->contactsubtype_b)) {
            return $this->contactsubtype_b->id;
        } else {
            return;
        }
    }

    public function contacttype_a(): HasOne
    {
        return $this->hasOne(ContactType::class, 'name', 'contact_type_a');
    }

    public function contacttype_b(): HasOne
    {
        return $this->hasOne(ContactType::class, 'name', 'contact_type_b');
    }

    public function contactsubtype_a(): HasOne
    {
        return $this->hasOne(ContactType::class, 'name', 'contact_sub_type_a');
    }

    public function contactsubtype_b(): HasOne
    {
        return $this->hasOne(ContactType::class, 'name', 'contact_sub_type_b');
    }
}
