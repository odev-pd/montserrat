<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Laravel\Cashier\Billable;
use OwenIt\Auditing\Contracts\Auditable;

class Contact extends Model implements Auditable
{
    use Billable;
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

    protected $table = 'contact';

    protected $appends = ['full_name_with_city', 'agc_household_name'];

    protected $with = ['prefix', 'suffix'];

    protected function casts(): array
    {
        return [
            'birth_date' => 'datetime',
            'deceased_date' => 'datetime',
            'created_date' => 'datetime',
            'modified_date' => 'datetime',
            'contact_type' => 'integer',
            'subcontact_type' => 'integer',
        ];
    }

    public function generateTags(): array
    {
        return [
            $this->sort_name,
        ];
    }

    // TODO: refactor to lookup based on relationship
    //TODO: rename person_id to contact_id
    /*    public function retreatmasters() {
            return $this->belongsToMany('\App\Retreat','retreatmasters','person_id','retreat_id');
        }
    */
    public function a_relationships(): HasMany
    {
        return $this->hasMany(Relationship::class, 'contact_id_a', 'id');
    }

    public function b_relationships(): HasMany
    {
        return $this->hasMany(Relationship::class, 'contact_id_b', 'id');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class, 'contact_id', 'id');
    }

    public function address_primary(): HasOne
    {
        return $this->hasOne(Address::class, 'contact_id', 'id')->whereIsPrimary(1);
    }

    public function getPrimaryEmailLocationNameAttribute()
    {
        if (isset($this->email_primary)) {
            return $this->email_primary->location_type_name;
        } else {
            return 'N/A';
        }
    }

    public function getPrimaryEmailLocationTypeIdAttribute()
    {
        if (isset($this->email_primary)) {
            return $this->email_primary->location_type_id;
        } else {
            return 0;
        }
    }

    public function getPrimaryAddressLocationNameAttribute()
    {
        if (isset($this->address_primary)) {
            return $this->address_primary->location_type_name;
        } else {
            return 'N/A';
        }
    }

    public function getPrimaryAddressLocationTypeIdAttribute()
    {
        if (isset($this->address_primary)) {
            return $this->address_primary->location_type_id;
        } else {
            return 0;
        }
    }

    public function getPrimaryPhoneLocationNameAttribute()
    {
        if (isset($this->phone_primary) && ! empty($this->phone_primary->phone)) {
            return $this->phone_primary->location_type_name;
        } else {
            return 'N/A';
        }
    }

    public function getPrimaryPhoneLocationTypeIdAttribute()
    {
        if (isset($this->phone_primary) && ! empty($this->phone_primary->phone)) {
            return $this->phone_primary->location_type_id;
        } else {
            return 0;
        }
    }

    public function getPrimaryPhoneTypeAttribute()
    {
        if (isset($this->phone_primary) && ! empty($this->phone_primary->phone)) {
            return $this->phone_primary->phone_type;
        } else {
            return;
        }
    }

    public function bishops(): HasMany
    {
        return $this->hasMany(Relationship::class, 'contact_id_a', 'id')->whereRelationshipTypeId(config('polanco.relationship_type.bishop'));
    }

    public function primary_bishop(): HasOne
    {
        return $this->hasOne(Relationship::class, 'contact_id_a', 'id')->whereRelationshipTypeId(config('polanco.relationship_type.bishop'))->whereIsActive(1);
    }

    public function ambassador_events(): HasMany
    {
        return $this->hasMany(Registration::class, 'contact_id', 'id');
    }

    public function contacttype(): HasOne
    {
        return $this->hasOne(ContactType::class, 'id', 'contact_type');
    }

    public function subcontacttype(): HasOne
    {
        return $this->hasOne(ContactType::class, 'id', 'subcontact_type');
    }

    public function diocese(): HasOne
    {
        return $this->hasOne(Relationship::class, 'contact_id_b', 'id')->whereRelationshipTypeId(config('polanco.relationship_type.diocese'));
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class, 'contact_id', 'id');
    }

    public function emails(): HasMany
    {
        return $this->hasMany(Email::class, 'contact_id', 'id');
    }

    public function primaryEmail(): HasMany
    {
        return $this->hasMany(Email::class, 'contact_id', 'id')
            ->where('is_primary', 1);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class, 'entity_id', 'id')->whereEntity('contact');
    }

    public function agc2019(): HasOne
    {
        return $this->hasOne(Agc2019::class, 'contact_id', 'id');
    }

    public function avatar(): HasOne
    {
        return $this->hasOne(Attachment::class, 'entity_id', 'id')->whereEntity('contact')->whereFileTypeId(config('polanco.file_type.contact_avatar'));
    }

    public function email_primary(): HasOne
    {
        return $this->hasOne(Email::class, 'contact_id', 'id')->whereIsPrimary(1);
    }

    public function emergency_contact(): HasOne
    {
        return $this->hasOne(EmergencyContact::class, 'contact_id', 'id');
    }

    public function ethnicity(): HasOne
    {
        return $this->hasOne(Ethnicity::class, 'id', 'ethnicity_id');
    }

    public function getAddressPrimaryStreetAttribute()
    {
        if (isset($this->address_primary->street_address)) {
            return $this->address_primary->street_address;
        } else {
            return;
        }
    }

    public function getAddressPrimarySupplementalAddressAttribute()
    {
        if (isset($this->address_primary->supplemental_address_1)) {
            return $this->address_primary->supplemental_address_1;
        } else {
            return;
        }
    }

    public function getAddressPrimaryCityAttribute()
    {
        if (isset($this->address_primary->city)) {
            return $this->address_primary->city;
        } else {
            return;
        }
    }

    public function getAddressPrimaryStateAttribute()
    {
        if (isset($this->address_primary->state->abbreviation)) {
            return $this->address_primary->state->abbreviation;
        } else {
            return;
        }
    }

    public function getAddressPrimaryStateIdAttribute()
    {
        if (isset($this->address_primary->state_province_id)) {
            return $this->address_primary->state_province_id;
        } else {
            return;
        }
    }

    public function getAddressPrimaryCountryAttribute()
    {
        if (isset($this->address_primary->country_name)) {
            return $this->address_primary->country_name;
        } else {
            return 0;
        }
    }

    public function getAddressPrimaryCountryAbbreviationAttribute()
    {
        if (isset($this->address_primary->country_abbreviation)) {
            return $this->address_primary->country_abbreviation;
        } else {
            return null;
        }
    }

    public function getAddressPrimaryCountryIdAttribute()
    {
        if (isset($this->address_primary->country_id)) {
            return $this->address_primary->country_id;
        } else {
            return 0;
        }
    }

    public function getAddressPrimaryPostalCodeAttribute()
    {
        if (isset($this->address_primary->postal_code)) {
            return $this->address_primary->postal_code;
        } else {
            return;
        }
    }

    public function getAddressPrimaryGoogleMapAttribute()
    {
        if (isset($this->address_primary->google_map)) {
            return $this->address_primary->google_map;
        } else {
            return;
        }
    }

    public function getPreferredLanguageValueAttribute()
    {
        if (isset($this->language_pref->value)) {
            return $this->language_pref->value;
        } else {
            return;
        }
    }

    public function getPreferredLanguageIdAttribute()
    {
        if (! empty($this->language_pref)) {
            return $this->language_pref->id;
        } else {
            return;
        }
    }

    public function getPreferredLanguageLabelAttribute()
    {
        if (! empty($this->language_pref)) {
            return $this->language_pref->label;
        } else {
            return;
        }
    }

    public function getOrganizationNameAndCityAttribute()
    {
        if (isset($this->address_primary->city)) {
            return $this->display_name.' ('.$this->address_primary->city.')';
        } else {
            return $this->display_name;
        }
    }

    public function getAvatarLargeLinkAttribute()
    {
        if (Storage::has('contact/'.$this->id.'/avatar.png')) {
            return "<img src='".url('avatar/'.$this->id)."' class='rounded-circle' style='height: 150px; padding:5px;'>";
        } else {
            if ($this->is_deceased) {
                return "<img src='".url('images/dead.png')."' class='rounded-circle' style='height: 150px; padding:5px;'>";
            } else {
                return "<img src='".url('images/default.png')."' class='rounded-circle' style='height: 150px; padding:5px;'>";
            }
        }
    }

    public function getSignatureAttribute()
    {
        $file_name = storage_path().'/app/contact/'.config('polanco.self.id').'/signature.png';

        return "<img src='".$file_name."' style='width:320px; height:100px;'>";
        /*
        if (Storage::has('contact/'.$this->id.'/signature.png')) {
            return "<img src='".url('signature/'.$this->id)."'  style='width:320px; height:100px;'>";
        } else {
            return;
        } */
    }

    public function getAvatarSmallLinkAttribute()
    {
        if (Storage::has('contact/'.$this->id.'/avatar.png')) {
            return "<img src='".url('avatar/'.$this->id)."' class='rounded-circle' style='height: 75px; padding:5px;'>";
        } else {
            if ($this->is_deceased) {
                return "<img src='".url('images/dead.png')."' class='rounded-circle' style='height: 75px; padding:5px;'>";
            } else {
                return "<img src='".url('images/default.png')."' class='rounded-circle' style='height: 75px; padding:5px;'>";
            }
        }
    }

    public function getBirthdayAttribute()
    {
        if (isset($this->birth_date)) {
            return $this->birth_date->format('m-d-Y');
        } else {
            return;
        }
    }

    public function getContactLinkAttribute()
    {
        switch ($this->subcontact_type) {
            case config('polanco.contact_type.parish'):
                $path = url('parish/'.$this->id);
                break;
            case config('polanco.contact_type.diocese'):
                $path = url('diocese/'.$this->id);
                break;
            case config('polanco.contact_type.vendor'):
                $path = url('vendor/'.$this->id);
                break;
            default:
                $path = url('organization/'.$this->id);
        }

        if ($this->contact_type == config('polanco.contact_type.individual')) {
            $path = url('person/'.$this->id);
        }

        return "<a href='".$path."'>".$this->display_name.'</a>';
    }

    public function getContactUrlAttribute()
    {
        switch ($this->subcontact_type) {
            case config('polanco.contact_type.parish'):
                $path = url('parish/'.$this->id);
                break;
            case config('polanco.contact_type.diocese'):
                $path = url('diocese/'.$this->id);
                break;
            case config('polanco.contact_type.vendor'):
                $path = url('vendor/'.$this->id);
                break;
            default:
                $path = url('organization/'.$this->id);
        }

        if ($this->contact_type == config('polanco.contact_type.individual')) {
            $path = url('person/'.$this->id);
        }

        return $path;
    }

    public function getContactLinkFullNameAttribute()
    {
        switch ($this->subcontact_type) {
            case config('polanco.contact_type.parish'):
                $path = url('parish/'.$this->id);
                break;
            case config('polanco.contact_type.diocese'):
                $path = url('diocese/'.$this->id);
                break;
            case config('polanco.contact_type.vendor'):
                $path = url('vendor/'.$this->id);
                break;
            default:
                $path = url('organization/'.$this->id);
        }
        if ($this->contact_type == config('polanco.contact_type.individual')) {
            $path = url('person/'.$this->id);
        }
        if ($this->contact_type == config('polanco.contact_type.household')) {
            $path = url('person/'.$this->id);
        }

        return "<a href='".$path."'>".$this->full_name.'</a>';
    }

    public function getContactTypeLabelAttribute()
    {
        if (isset($this->contacttype->label)) {
            return $this->contacttype->label;
        } else {
            return 'N/A';
        }
    }

    public function getSubcontactTypeLabelAttribute()
    {
        if (isset($this->subcontacttype->label)) {
            return $this->subcontacttype->label;
        } else {
            return 'N/A';
        }
    }

    public function getDioceseIdAttribute()
    {
        if (isset($this->diocese->contact_id_a)) {
            return $this->diocese->contact_id_a;
        } else {
            return;
        }
    }

    public function getDioceseNameAttribute()
    {
        if (isset($this->diocese->contact_a->organization_name)) {
            return $this->diocese->contact_a->organization_name;
        } else {
            return;
        }
    }

    public function getDonationsTotalAttribute()
    {
        if (isset($this->donations)) {
            return $this->donations->sum('donation_amount');
        } else {
            return 0;
        }
    }

    public function getEmailPrimaryTextAttribute()
    {
        if (! empty($this->email_primary->email)) {
            return $this->email_primary->email;
        } else {
            return;
        }
    }

    public function getEmergencyContactNameAttribute()
    {
        if (! empty($this->emergency_contact->name)) {
            return $this->emergency_contact->name;
        } else {
            return;
        }
    }

    public function getEmergencyContactRelationshipAttribute()
    {
        if (! empty($this->emergency_contact->relationship)) {
            return $this->emergency_contact->relationship;
        } else {
            return;
        }
    }

    public function getEmergencyContactPhoneAttribute()
    {
        if (! empty($this->emergency_contact->phone)) {
            return $this->emergency_contact->phone;
        } else {
            return;
        }
    }

    public function getEmergencyContactPhoneAlternateAttribute()
    {
        if (! empty($this->emergency_contact->phone_alternate)) {
            return $this->emergency_contact->phone_alternate;
        } else {
            return;
        }
    }

    public function getEthnicityNameAttribute()
    {
        if (isset($this->ethnicity_id) && ($this->ethnicity_id > 0)) {
            return $this->ethnicity->ethnicity;
        } else {
            return;
        }
    }

    public function getFullNameAttribute()
    {
        $full_name = '';
        if ($this->contact_type == config('polanco.contact_type.individual')) {
            if (isset($this->prefix->name)) {
                $full_name .= $this->prefix->name.' ';
            }

            if (isset($this->first_name)) {
                $full_name .= $this->first_name.' ';
            }
            if (isset($this->nick_name)) {
                $full_name .= '"'.$this->nick_name.'" ';
            }
            if (isset($this->middle_name)) {
                $full_name .= $this->middle_name.' ';
            }
            if (isset($this->last_name)) {
                $full_name .= $this->last_name;
            }
            if (isset($this->suffix->name)) {
                $full_name .= ', '.$this->suffix->name;
            }
            if (trim($full_name) == '') {
                $full_name = (isset($this->display_name)) ? $this->display_name : 'N/A';
            }
        }
        if ($this->contact_type == config('polanco.contact_type.organization')) {
            $full_name = $this->display_name;
        }
        if ($this->contact_type == config('polanco.contact_type.household')) {
            $full_name = $this->display_name;
        }

        return $full_name;
    }

    public function getFullNameWithCityAttribute()
    {
        $full_name = $this->full_name;
        if (isset($this->address_primary->city)) {
            $full_name .= ' ('.$this->address_primary->city.')';
        }

        return $full_name;
    }

    public function getGenderNameAttribute()
    {
        if (isset($this->gender_id) && ($this->gender_id > 0)) {
            return $this->gender->name;
        } else {
            return;
        }
    }

    public function getIsDonorAttribute()
    {
        if (! empty($this->relationship_mjrh_donor->id)) {
            return true;
        } else {
            return false;
        }
    }

    /*


    SELECT p.id, p.contact_id, p.event_id, e.title, e.idnumber, c.sort_name, d.donation_amount
    FROM participant as p
    LEFT JOIN event as e ON (p.event_id = e.id)
    LEFT JOIN Donations as d ON (d.contact_id = p.contact_id AND p.event_id = d.event_id)
    LEFT JOIN contact as c ON (p.contact_id = c.id)
    WHERE p.deleted_at IS NULL AND p.status_id=1 AND p.role_id = 5 AND p.canceled_at IS NULL
    AND e.deleted_at IS NULL AND e.event_type_id = 7 AND e.end_date < NOW() AND YEAR(e.start_date)>=2019
    AND d.donation_amount<=50 AND d.deleted_at IS NULL AND d.donation_description="Retreat Funding";
    */
    public function getIsFreeLoaderAttribute()
    {
        $is_free_loader = 0;

        if ($this->contact_type == 1) { // only individuals
            $registrations = $this->event_registrations()->whereStatusId(1)->whereRoleId(5)->whereNull('canceled_at')->get();
            foreach ($registrations as $registration) {
                if ($registration->event->retreat_type == 'Ignatian' && isset($registration->event->start_date) && isset($registration->event->end_date)) {
                    if ($registration->event->end_date < now() && $registration->event->start_date->year >= date('Y') - 3) {
                        if ($registration->event->nights == 2 && $registration->retreat_offering <= 130) {
                            $is_free_loader = 1;
                        }
                        if ($registration->event->nights == 3 && $registration->retreat_offering <= 195) {
                            $is_free_loader = 1;
                        }
                    }
                }
            }
        } else { // if vendor, organization, etc. they cannot be a free loader. Helps to avoid JCP or Diocese of Fort Worth where there may be many registrations.
            $is_free_loader = 0;
        }

        return $is_free_loader;
    }

    public function getIsRetreatantAttribute()
    {
        if (! empty($this->relationship_mjrh_retreatant->id)) {
            return true;
        } else {
            return false;
        }
    }

    public function getIsAmbassadorAttribute()
    {
        if (isset($this->group_ambassador->id)) {
            return true;
        } else {
            return false;
        }
    }

    public function getIsHLM2017Attribute()
    {
        if (isset($this->group_hlm2017->id)) {
            return true;
        } else {
            return false;
        }
    }

    public function getIsVolunteerAttribute()
    {
        if (isset($this->group_volunteer->id)) {
            return true;
        } else {
            return false;
        }
    }

    public function getIsBishopAttribute()
    {
        if (isset($this->group_bishop->id)) {
            return true;
        } else {
            return false;
        }
    }

    public function getIsPriestAttribute()
    {
        if (isset($this->group_priest->id)) {
            return true;
        } else {
            return false;
        }
    }

    public function getIsDeaconAttribute()
    {
        if (isset($this->group_deacon->id)) {
            return true;
        } else {
            return false;
        }
    }

    public function getIsPastorAttribute()
    {
        if (isset($this->group_pastor->id)) {
            return true;
        } else {
            return false;
        }
    }

    public function getIsJesuitAttribute()
    {
        if (isset($this->group_jesuit->id)) {
            return true;
        } else {
            return false;
        }
    }

    public function getIsProvincialAttribute()
    {
        if (isset($this->group_provincial->id)) {
            return true;
        } else {
            return false;
        }
    }

    public function getIsSuperiorAttribute()
    {
        if (isset($this->group_superior->id)) {
            return true;
        } else {
            return false;
        }
    }

    public function getIsBoardMemberAttribute()
    {
        if (isset($this->group_board_member->id)) {
            return true;
        } else {
            return false;
        }
    }

    public function getIsFormerBoardMemberAttribute()
    {
        if (isset($this->relationship_mjrh_former_board_member->id)) {
            return true;
        } else {
            return false;
        }
    }

    public function getIsStaffAttribute()
    {
        if (isset($this->group_staff->id)) {
            return true;
        } else {
            return false;
        }
    }

    public function getIsStewardAttribute()
    {
        if (isset($this->group_steward->id)) {
            return true;
        } else {
            return false;
        }
    }

    public function getIsRetreatDirectorAttribute()
    {
        if (isset($this->relationship_mjrh_retreat_director->id)) {
            return true;
        } else {
            return false;
        }
    }

    public function getIsRetreatInnkeeperAttribute()
    {
        if (isset($this->relationship_mjrh_retreat_innkeeper->id)) {
            return true;
        } else {
            return false;
        }
    }

    public function getIsRetreatAssistantAttribute()
    {
        if (isset($this->relationship_mjrh_retreat_assistant->id)) {
            return true;
        } else {
            return false;
        }
    }

    public function getNoteDietaryTextAttribute()
    {
        if (isset($this->note_dietary->note)) {
            return $this->note_dietary->note;
        } else {
            return;
        }
    }

    public function getNoteDioceseTextAttribute()
    {
        if (isset($this->note_diocese->note)) {
            return $this->note_diocese->note;
        } else {
            return;
        }
    }

    public function getNoteHealthTextAttribute()
    {
        if (isset($this->note_health->note)) {
            return $this->note_health->note;
        } else {
            return;
        }
    }

    public function getNoteGeneralTextAttribute()
    {
        if (isset($this->note_general->note)) {
            return $this->note_general->note;
        } else {
            return;
        }
    }

    public function getNoteOrganizationTextAttribute()
    {
        if (isset($this->note_organization->note)) {
            return $this->note_organization->note;
        } else {
            return;
        }
    }

    public function getNoteParishTextAttribute()
    {
        if (isset($this->note_parish->note)) {
            return $this->note_parish->note;
        } else {
            return;
        }
    }

    public function getNoteRegistrationTextAttribute()
    {
        if (isset($this->note_registration->note)) {
            return $this->note_registration->note;
        } else {
            return;
        }
    }

    public function getAgcHouseholdNameAttribute()
    {
        if ($this->contact_type == 1) {
            return (isset($this->agc2019->household_name)) ? $this->agc2019->household_name : $this->full_name;
        } else {
            return $this->full_name;
        }
    }

    public function getNoteRoomPreferenceTextAttribute()
    {
        if (isset($this->note_room_preference->note)) {
            return $this->note_room_preference->note;
        } else {
            return;
        }
    }

    public function getNoteVendorTextAttribute()
    {
        if (isset($this->note_vendor->note)) {
            return $this->note_vendor->note;
        } else {
            return;
        }
    }

    public function getOccupationNameAttribute()
    {
        if (isset($this->occupation_id) && ($this->occupation_id > 0)) {
            return $this->occupation->name;
        } else {
            return;
        }
    }

    public function getParishIdAttribute()
    {
        if (isset($this->parish->contact_id_a) && ($this->parish->contact_id_a > 0)) {
            return $this->parish->contact_id_a;
        } else {
            return;
        }
    }

    public function getParishNameAttribute()
    {
        if (isset($this->parish->contact_id_a) && ($this->parish->contact_id_a > 0)) {
            return $this->parish->contact_a->display_name.' ('.$this->parish->contact_a->address_primary_city.')';
        } else {
            return;
        }
    }

    public function getParishLinkAttribute()
    {
        if (isset($this->parish->contact_id_a) && ($this->parish->contact_id_a > 0)) {
            $path = url('parish/'.$this->parish->contact_a->id);

            return "<a href='".$path."'>".$this->parish->contact_a->display_name.' ('.$this->parish->contact_a->address_primary_city.')'.'</a>';
        } else {
            return;
        }
    }

    public function getParticipantCountAttribute()
    {
        if (isset($this->event_registrations)) {
            return $this->event_registrations->count();
        } else {
            return 0;
        }
    }

    public function getPhoneHomeMobileNumberAttribute()
    {
        if (isset($this->phone_home_mobile->phone)) {
            return $this->phone_home_mobile->phone.$this->phone_home_mobile->phone_extension;
        } else {
            return;
        }
    }

    public function getPhoneHomePhoneNumberAttribute()
    {
        if (isset($this->phone_home_phone->phone)) {
            return $this->phone_home_phone->phone.$this->phone_home_phone->phone_extension;
        } else {
            return;
        }
    }

    public function getPhoneWorkPhoneNumberAttribute()
    {
        if (isset($this->phone_work_phone)) {
            return $this->phone_work_phone->phone.$this->phone_work_phone->phone_extension;
        } else {
            return;
        }
    }

    public function getPhoneMainPhoneNumberAttribute()
    {
        if (isset($this->phone_main_phone)) {
            return $this->phone_main_phone->phone.$this->phone_main_phone->phone_extension;
        } else {
            return;
        }
    }

    public function getPhoneMainFaxNumberAttribute()
    {
        if (isset($this->phone_main_fax)) {
            return $this->phone_main_fax->phone.$this->phone_main_fax->phone_extension;
        } else {
            return;
        }
    }

    public function getPrefixNameAttribute()
    {
        if (isset($this->prefix_id) && ($this->prefix_id > 0)) {
            return $this->prefix->name;
        } else {
            return;
        }
    }

    public function getPrimaryPhoneNumberLinkAttribute()
    {
        if (isset($this->phone_primary->phone)) {
            $phone_number = $this->phone_primary->phone.$this->phone_primary->phone_extension;

            return '<a href="tel:'.$phone_number.'">'.$phone_number.'</a>';
        } else {
            return;
        }
    }

    public function getPrimaryPhoneNumberAttribute()
    {
        if (isset($this->phone_primary->phone)) {
            return $this->phone_primary->phone.$this->phone_primary->phone_extension;
        } else {
            return;
        }
    }

    public function getReligionNameAttribute()
    {
        if (isset($this->religion_id) && ($this->religion_id > 0)) {
            return $this->religion->label;
        } else {
            return;
        }
    }

    public function getSuffixNameAttribute()
    {
        if (isset($this->suffix_id) && ($this->suffix_id > 0)) {
            return $this->suffix->name;
        } else {
            return;
        }
    }

    public function gender(): HasOne
    {
        return $this->hasOne(Gender::class, 'id', 'gender_id');
    }

    public function group_ambassador(): HasOne
    {
        return $this->hasOne(GroupContact::class, 'contact_id', 'id')->whereGroupId(config('polanco.group_id.ambassador'));
    }

    public function group_hlm2017(): HasOne
    {
        return $this->hasOne(GroupContact::class, 'contact_id', 'id')->whereGroupId(config('polanco.group_id.hlm2017'));
    }

    public function group_volunteer(): HasOne
    {
        return $this->hasOne(GroupContact::class, 'contact_id', 'id')->whereGroupId(config('polanco.group_id.volunteer'));
    }

    public function group_bishop(): HasOne
    {
        return $this->hasOne(GroupContact::class, 'contact_id', 'id')->whereGroupId(config('polanco.group_id.bishop'));
    }

    public function group_priest(): HasOne
    {
        return $this->hasOne(GroupContact::class, 'contact_id', 'id')->whereGroupId(config('polanco.group_id.priest'));
    }

    public function group_deacon(): HasOne
    {
        return $this->hasOne(GroupContact::class, 'contact_id', 'id')->whereGroupId(config('polanco.group_id.deacon'));
    }

    public function group_pastor(): HasOne
    {
        return $this->hasOne(GroupContact::class, 'contact_id', 'id')->whereGroupId(config('polanco.group_id.pastor'));
    }

    public function group_jesuit(): HasOne
    {
        return $this->hasOne(GroupContact::class, 'contact_id', 'id')->whereGroupId(config('polanco.group_id.jesuit'));
    }

    public function group_provincial(): HasOne
    {
        return $this->hasOne(GroupContact::class, 'contact_id', 'id')->whereGroupId(config('polanco.group_id.provincial'));
    }

    public function group_superior(): HasOne
    {
        return $this->hasOne(GroupContact::class, 'contact_id', 'id')->whereGroupId(config('polanco.group_id.superior'));
    }

    public function group_board_member(): HasOne
    {
        return $this->hasOne(GroupContact::class, 'contact_id', 'id')->whereGroupId(config('polanco.group_id.board'))->whereStatus('Added');
    }

    public function group_staff(): HasOne
    {
        return $this->hasOne(GroupContact::class, 'contact_id', 'id')->whereGroupId(config('polanco.group_id.staff'));
    }

    public function group_steward(): HasOne
    {
        return $this->hasOne(GroupContact::class, 'contact_id', 'id')->whereGroupId(config('polanco.group_id.steward'));
    }

    public function groups(): HasMany
    {
        return $this->hasMany(GroupContact::class, 'contact_id', 'id')->whereStatus('Added');
    }

    public function jobs_owned(): HasMany
    {
        return $this->hasMany(AssetJob::class, 'assigned_to_id', 'id');
    }

    public function languages(): BelongsToMany
    {
        return $this->belongsToMany(Language::class, 'contact_languages', 'contact_id', 'language_id');
    }

    public function language_pref(): HasOne
    {
        return $this->hasOne(Language::class, 'name', 'preferred_language');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class, 'entity_id', 'id')->whereEntityTable('contact');
    }

    public function note_dietary(): HasOne
    {
        return $this->hasOne(Note::class, 'entity_id', 'id')->whereEntityTable('contact')->whereSubject('Dietary Note');
    }

    public function note_health(): HasOne
    {
        return $this->hasOne(Note::class, 'entity_id', 'id')->whereEntityTable('contact')->whereSubject('Health Note');
    }

    public function note_room_preference(): HasOne
    {
        return $this->hasOne(Note::class, 'entity_id', 'id')->whereEntityTable('contact')->whereSubject('Room Preference');
    }

    public function note_vendor(): HasOne
    {
        return $this->hasOne(Note::class, 'entity_id', 'id')->whereEntityTable('contact')->whereSubject('Vendor note');
    }

    public function note_general(): HasOne
    {
        return $this->hasOne(Note::class, 'entity_id', 'id')->whereEntityTable('contact')->whereSubject('Contact Note');
    }

    public function note_organization(): HasOne
    {
        return $this->hasOne(Note::class, 'entity_id', 'id')->whereEntityTable('contact')->whereSubject('Organization Note');
    }

    public function note_parish(): HasOne
    {
        return $this->hasOne(Note::class, 'entity_id', 'id')->whereEntityTable('contact')->whereSubject('Parish Note');
    }

    public function note_diocese(): HasOne
    {
        return $this->hasOne(Note::class, 'entity_id', 'id')->whereEntityTable('contact')->whereSubject('Diocese Note');
    }

    public function note_registration(): HasOne
    {
        return $this->hasOne(Note::class, 'entity_id', 'id')->whereEntityTable('contact')->whereSubject('Registration Note');
    }

    public function occupation(): HasOne
    {
        return $this->hasOne(Ppd_occupation::class, 'id', 'occupation_id');
    }

    public function parish(): HasOne
    {
        return $this->hasOne(Relationship::class, 'contact_id_b', 'id')->whereRelationshipTypeId(config('polanco.relationship_type.parishioner'));
    }

    public function parishes(): HasMany
    {
        return $this->hasMany(Relationship::class, 'contact_id_a', 'id')->whereRelationshipTypeId(config('polanco.relationship_type.diocese'));
    }

    public function parishioners(): HasMany
    {
        return $this->hasMany(Relationship::class, 'contact_id_a', 'id')->whereRelationshipTypeId(config('polanco.relationship_type.parishioner'));
    }

    public function pastor(): HasOne
    {
        return $this->hasOne(Relationship::class, 'contact_id_a', 'id')->whereRelationshipTypeId(config('polanco.relationship_type.pastor'));
    }

    public function getPastorIdAttribute()
    {
        if (isset($this->pastor->contact_id_b)) {
            return $this->pastor->contact_id_b;
        } else {
            return 0;
        }
    }

    public function phones(): HasMany
    {
        return $this->hasMany(Phone::class, 'contact_id', 'id');
    }

    public function phone_primary(): HasOne
    {
        return $this->hasOne(Phone::class, 'contact_id', 'id')->whereIsPrimary(1);
    }

    public function phone_main_phone(): HasOne
    {
        return $this->hasOne(Phone::class, 'contact_id', 'id')->wherePhoneType('Phone')->whereLocationTypeId(config('polanco.location_type.main'));
    }

    public function phone_main_mobile(): HasOne
    {
        return $this->hasOne(Phone::class, 'contact_id', 'id')->wherePhoneType('Mobile')->whereLocationTypeId(config('polanco.location_type.main'));
    }

    public function phone_main_fax(): HasOne
    {
        return $this->hasOne(Phone::class, 'contact_id', 'id')->wherePhoneType('Fax')->whereLocationTypeId(config('polanco.location_type.main'));
    }

    public function phone_home_phone(): HasOne
    {
        return $this->hasOne(Phone::class, 'contact_id', 'id')->wherePhoneType('Phone')->whereLocationTypeId(config('polanco.location_type.home'));
    }

    public function phone_home_mobile(): HasOne
    {
        return $this->hasOne(Phone::class, 'contact_id', 'id')->wherePhoneType('Mobile')->whereLocationTypeId(config('polanco.location_type.home'));
    }

    public function phone_home_fax(): HasOne
    {
        return $this->hasOne(Phone::class, 'contact_id', 'id')->wherePhoneType('Fax')->whereLocationTypeId(config('polanco.location_type.home'));
    }

    public function phone_work_phone(): HasOne
    {
        return $this->hasOne(Phone::class, 'contact_id', 'id')->wherePhoneType('Phone')->whereLocationTypeId(config('polanco.location_type.work'));
    }

    public function phone_work_mobile(): HasOne
    {
        return $this->hasOne(Phone::class, 'contact_id', 'id')->wherePhoneType('Mobile')->whereLocationTypeId(config('polanco.location_type.work'));
    }

    public function phone_work_fax(): HasOne
    {
        return $this->hasOne(Phone::class, 'contact_id', 'id')->wherePhoneType('Fax')->whereLocationTypeId(config('polanco.location_type.work'));
    }

    public function phone_other_phone(): HasOne
    {
        return $this->hasOne(Phone::class, 'contact_id', 'id')->wherePhoneType('Phone')->whereLocationTypeId(config('polanco.location_type.other'));
    }

    public function phone_other_mobile(): HasOne
    {
        return $this->hasOne(Phone::class, 'contact_id', 'id')->wherePhoneType('Mobile')->whereLocationTypeId(config('polanco.location_type.other'));
    }

    public function phone_other_fax(): HasOne
    {
        return $this->hasOne(Phone::class, 'contact_id', 'id')->wherePhoneType('Fax')->whereLocationTypeId(config('polanco.location_type.other'));
    }

    public function prefix(): HasOne
    {
        return $this->hasOne(Prefix::class, 'id', 'prefix_id');
    }

    public function referrals(): BelongsToMany
    {
        return $this->belongsToMany(Referral::class, 'contact_referral', 'contact_id', 'referral_id');
    }

    public function retreat_assistants(): HasMany
    {
        return $this->hasMany(Relationship::class, 'contact_id_a', 'id')->whereRelationshipTypeId(config('polanco.relationship_type.retreat_assistant'))->whereIsActive(1);
    }

    public function retreat_directors(): HasMany
    {
        return $this->hasMany(Relationship::class, 'contact_id_a', 'id')->whereRelationshipTypeId(config('polanco.relationship_type.retreat_director'));
    }

    public function retreat_innkeepers(): HasMany
    {
        return $this->hasMany(Relationship::class, 'contact_id_a', 'id')->whereRelationshipTypeId(config('polanco.relationship_type.retreat_innkeeper'));
    }

    public function retreat_ambassadors(): HasMany
    {
        // TODO: handle with participants of role Retreat Director or Master - be careful with difference between (registration table) retreat_id and (participant table) event_id
        return $this->hasMany(Relationship::class, 'contact_id_a', 'id')->whereRelationshipTypeId(config('polanco.relationship_type.ambassador'));
    }

    public function event_registrations(): HasMany
    {
        // the events (retreats) for which this contact has participated
        return $this->hasMany(Registration::class, 'contact_id', 'id');
    }

    public function event_ambassadors(): HasMany
    {
        // the events (retreats) for which this contact has been a retreatant
        return $this->hasMany(Registration::class, 'contact_id', 'id')->whereRoleId(config('polanco.participant_role_id.ambassador'));
    }

    public function event_retreatants(): HasMany
    {
        // the events (retreats) for which this contact has been a retreatant
        return $this->hasMany(Registration::class, 'contact_id', 'id')->whereRoleId(config('polanco.participant_role_id.retreatant'));
    }

    public function relationship_mjrh_former_board_member(): HasOne
    {
        return $this->hasOne(Relationship::class, 'contact_id_b', 'id')->whereRelationshipTypeId(config('polanco.relationship_type.board_member'))->whereNotNull('end_date');
    }

    public function relationship_mjrh_donor(): HasOne
    {
        return $this->hasOne(Relationship::class, 'contact_id_b', 'id')->whereRelationshipTypeId(config('polanco.relationship_type.donor'));
    }

    public function relationship_mjrh_retreatant(): HasOne
    {
        return $this->hasOne(Relationship::class, 'contact_id_b', 'id')->whereRelationshipTypeId(config('polanco.relationship_type.retreatant'))->whereIsActive(1)->whereContactIdA(config('polanco.contact.montserrat'));
    }

    public function relationship_mjrh_retreat_assistant(): HasOne
    {
        return $this->hasOne(Relationship::class, 'contact_id_b', 'id')->whereRelationshipTypeId(config('polanco.relationship_type.retreat_assistant'))->whereContactIdA(config('polanco.contact.montserrat'))->whereIsActive(1);
    }

    public function relationship_mjrh_retreat_director(): HasOne
    {
        return $this->hasOne(Relationship::class, 'contact_id_b', 'id')->whereRelationshipTypeId(config('polanco.relationship_type.retreat_director'))->whereContactIdA(config('polanco.contact.montserrat'))->whereIsActive(1);
    }

    public function relationship_mjrh_retreat_innkeeper(): HasOne
    {
        return $this->hasOne(Relationship::class, 'contact_id_b', 'id')->whereRelationshipTypeId(config('polanco.relationship_type.retreat_innkeeper'))->whereContactIdA(config('polanco.contact.montserrat'))->whereIsActive(1);
    }

    public function religion(): HasOne
    {
        return $this->hasOne(Religion::class, 'id', 'religion_id');
    }

    public function setNickNameAttribute($nick_name)
    {
        $this->attributes['nick_name'] = trim($nick_name) !== '' ? $nick_name : null;
    }

    public function setMiddleNameAttribute($middle_name)
    {
        $this->attributes['middle_name'] = trim($middle_name) !== '' ? $middle_name : null;
    }

    public function suffix(): HasOne
    {
        return $this->hasOne(Suffix::class, 'id', 'suffix_id');
    }

    public function touchpoints(): HasMany
    {
        return $this->hasMany(Touchpoint::class, 'person_id', 'id');
    }

    public function touchpoints_owned(): HasMany
    {
        return $this->hasMany(Touchpoint::class, 'staff_id', 'id');
    }

    public function websites(): HasMany
    {
        return $this->hasMany(Website::class, 'contact_id', 'id');
    }

    public function website_main(): HasOne
    {
        return $this->hasOne(Website::class, 'contact_id', 'id')->whereWebsiteType('Main');
    }

    public function scopeOrganizations_Generic($query)
    {
        return $query->where([
            ['contact_type', '>=', config('polanco.contact_type.organization')],
            ['subcontact_type', '>=', config('polanco.contact_type.province')],
        ]);
    }

    public function scopeVendors($query)
    {
        return $query->whereContactType(config('polanco.contact_type.organization'))->whereSubcontactType(config('polanco.contact_type.vendor'));
    }

    public function scopeFiltered($query, $filters)
    {
        foreach ($filters->query as $filter => $value) {
            if ($filter == 'prefix_id' && $value > 0) {
                $query->where($filter, $value);
            }
            if ($filter == 'first_name' && ! empty($value)) {
                $query->where($filter, 'like', '%'.$value.'%');
            }
            if ($filter == 'middle_name' && ! empty($value)) {
                $query->where($filter, 'like', '%'.$value.'%');
            }
            if ($filter == 'last_name' && ! empty($value)) {
                $query->where($filter, 'like', '%'.$value.'%');
            }
            if ($filter == 'suffix_id' && $value > 0) {
                $query->where($filter, $value);
            }
            if ($filter == 'nick_name' && ! empty($value)) {
                $query->where($filter, 'like', '%'.$value.'%');
            }
            if ($filter == 'display_name' && ! empty($value)) {
                $query->where($filter, 'like', '%'.$value.'%');
            }
            if ($filter == 'sort_name' && ! empty($value)) {
                $query->where($filter, 'like', '%'.$value.'%');
            }

            if ($filter == 'contact_type' && $value > 0) {
                $query->where($filter, $value);
            }
            if ($filter == 'subcontact_type' && $value > 0) {
                $query->where($filter, $value);
            }
            if ($filter == 'gender_id' && $value > 0) {
                $query->where($filter, $value);
            }

            if ($filter == 'religion_id' && $value > 0) {
                $query->where($filter, $value);
            }
            if ($filter == 'preferred_language_id' && $value > 0) {
                $lang = \App\Models\Language::findOrFail($value);
                $query->wherePreferredLanguage($lang->name);
            }
            if ($filter == 'occupation_id' && $value > 0) {
                $query->where($filter, $value);
            }
            if ($filter == 'ethnicity_id' && $value > 0) {
                $query->where($filter, $value);
            }
            if ($filter == 'is_deceased' && $value >= 0) {
                $query->where($filter, $value);
            }
            // ignore year but get everyone born on that month/day
            if ($filter == 'birth_date' && ! empty($value)) {
                $dob = Carbon::parse($value);
                $query->whereMonth('birth_date', '=', $dob->month);
                $query->whereDay('birth_date', '=', $dob->day);
            }
            if ($filter == 'deceased_date' && ! empty($value)) {
                $dod = Carbon::parse($value);
                $query->whereMonth('deceased_date', '=', $dod->month);
                $query->whereDay('deceased_date', '=', $dod->day);
            }
            if ($filter == 'phone' && ! empty($value)) {
                $value = str_replace(' ', '', $value);
                $value = str_replace('(', '', $value);
                $value = str_replace(')', '', $value);
                $value = str_replace('-', '', $value);

                $query->whereHas('phones', function ($q) use ($value) {
                    $q->where('phone_numeric', 'like', '%'.$value.'%');
                });
            }
            if ($filter == 'do_not_phone' && ! empty($value)) {
                $query->where($filter, $value);
            }
            if ($filter == 'do_not_sms' && ! empty($value)) {
                $query->where($filter, $value);
            }

            if ($filter == 'email' && ! empty($value)) {
                $query->whereHas('emails', function ($q) use ($value) {
                    $q->where('email', 'like', '%'.$value.'%');
                });
            }
            if ($filter == 'do_not_email' && ! empty($value)) {
                $query->where($filter, $value);
            }

            if ($filter == 'street_address' && ! empty($value)) {
                $query->whereHas('addresses', function ($q) use ($value) {
                    $q->where('street_address', 'like', '%'.$value.'%');
                });
            }
            if ($filter == 'city' && ! empty($value)) {
                $query->whereHas('addresses', function ($q) use ($value) {
                    $q->where('city', 'like', '%'.$value.'%');
                });
            }
            if ($filter == 'state_province_id' && ! empty($value)) {
                $query->whereHas('addresses', function ($q) use ($value) {
                    $q->where('state_province_id', '=', $value);
                });
            }
            if ($filter == 'postal_code' && ! empty($value)) {
                $query->whereHas('addresses', function ($q) use ($value) {
                    $q->where('postal_code', 'like', '%'.$value.'%');
                });
            }
            if ($filter == 'touchpoint_notes' && ! empty($value)) {
                $query->whereHas('touchpoints', function ($q) use ($value) {
                    $q->where('notes', 'like', '%'.$value.'%');
                });
            }
            if ($filter == 'touched_at' && ! empty($value)) {
                $query->whereHas('touchpoints', function ($q) use ($value) {
                    $q->whereDate('touched_at', $value);
                });
            }
            if ($filter == 'do_not_mail' && ! empty($value)) {
                $query->where($filter, $value);
            }

            if ($filter == 'languages' && ! empty($value)) {
                foreach ($value as $language) {
                    if ($language > 0) {
                        $query->whereHas('languages', function ($q) use ($language) {
                            $q->whereLanguageId($language);
                        });
                    }
                }
            }
            if ($filter == 'referrals' && ! empty($value)) {
                foreach ($value as $referral) {
                    if ($referral > 0) {
                        $query->whereHas('referrals', function ($q) use ($referral) {
                            $q->whereReferralId($referral);
                        });
                    }
                }
            }
            if ($filter == 'groups' && ! empty($value)) {
                foreach ($value as $group) {
                    if ($group > 0) {
                        $query->whereHas('groups', function ($q) use ($group) {
                            $q->whereGroupId($group);
                        });
                    }
                }
            }
            if ($filter == 'parish_id' && ! empty($value)) {
                $query->whereHas('parish', function ($q) use ($value) {
                    $q->where('contact_id_a', '=', $value);
                });
            }
            if ($filter == 'has_attachment' && ! empty($value)) {
                $query->whereHas('attachments', function ($q) {
                    $q->where('uri', '!=', 'avatar.png');
                });
            }
            if ($filter == 'attachment_description' && ! empty($value)) {
                $query->whereHas('attachments', function ($q) use ($value) {
                    $q->where('description', 'LIKE', '%'.$value.'%');
                });
            }
            if ($filter == 'has_avatar' && ! empty($value)) {
                $query->has('avatar');
            }
            if ($filter == 'note_health' && ! empty($value)) {
                $query->whereHas('note_health', function ($q) use ($value) {
                    $q->where('note', 'LIKE', '%'.$value.'%');
                });
            }
            if ($filter == 'note_dietary' && ! empty($value)) {
                $query->whereHas('note_dietary', function ($q) use ($value) {
                    $q->where('note', 'LIKE', '%'.$value.'%');
                });
            }
            if ($filter == 'note_general' && ! empty($value)) {
                $query->whereHas('note_general', function ($q) use ($value) {
                    $q->where('note', 'LIKE', '%'.$value.'%');
                });
            }
            if ($filter == 'note_room_preference' && ! empty($value)) {
                $query->whereHas('note_room_preference', function ($q) use ($value) {
                    $q->where('note', 'LIKE', '%'.$value.'%');
                });
            }
            if ($filter == 'emergency_contact_name' && ! empty($value)) {
                $query->whereHas('emergency_contact', function ($q) use ($value) {
                    $q->where('name', 'LIKE', '%'.$value.'%');
                });
            }
            if ($filter == 'emergency_contact_relationship' && ! empty($value)) {
                $query->whereHas('emergency_contact', function ($q) use ($value) {
                    $q->where('relationship', 'LIKE', '%'.$value.'%');
                });
            }
            if ($filter == 'emergency_contact_phone' && ! empty($value)) {
                $query->whereHas('emergency_contact', function ($q) use ($value) {
                    $q->where('phone', 'LIKE', '%'.$value.'%')->orWhere('phone_alternate', 'LIKE', '%'.$value.'%');
                });
            }
            if ($filter == 'url' && ! empty($value)) {
                $query->whereHas('websites', function ($q) use ($value) {
                    $q->where('url', 'like', '%'.$value.'%');
                });
            }
        }

        return $query;
    }

    public function birthdayEmailReceivers()
    {
        $birthdays = Contact::whereRaw('DAY(birth_date) = DAY(now())')
            ->whereRaw('MONTH(birth_date) = MONTH(now())')
            ->where('do_not_email', '<>', 1)
            ->whereIsDeceased(0)
            ->whereNull('deceased_date')
            ->with('email_primary')
            ->select('id', 'display_name', 'birth_date', 'nick_name', 'first_name', 'preferred_language')
            ->whereHas('email_primary', function ($query) {
                return $query->whereNotNull('email')->select('email');
            })
            ->get();

        return $birthdays;
    }
}
