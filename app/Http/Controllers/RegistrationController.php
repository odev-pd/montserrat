<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGroupRegistrationRequest;
use App\Http\Requests\StoreRegistrationRequest;
use App\Http\Requests\UpdateRegistrationRequest;
use App\Mail\RegistrationCanceledChange;
use App\Mail\RegistrationEventChange;
use App\Mail\RetreatRegistration;
use App\Models\Registration;
use App\Traits\SquareSpaceTrait;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    use SquareSpaceTrait;

    public function __construct()
    {
        $this->middleware('auth')->except('confirmAttendance');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $this->authorize('show-registration');

        $registrations = \App\Models\Registration::with('contact.suffix')->with('contact.prefix')
            ->whereHas('retreat', function ($query) {
                $query->where('end_date', '>=', date('Y-m-d'));
            })->orderBy('register_date', 'desc')->with('retreatant', 'retreat', 'room')
            ->paginate(25, ['*'], 'registrations');

        //dd($registrations);
        return view('registrations.index', compact('registrations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $this->authorize('create-registration');

        $retreats = \App\Models\Retreat::select(DB::raw('CONCAT(idnumber, "-", title, " (",DATE_FORMAT(start_date,"%m-%d-%Y"),")") as description'), 'id')->where('end_date', '>', Carbon::today()->subWeek())->where('is_active', '=', 1)->orderBy('start_date')->pluck('description', 'id');
        $retreats->prepend('Unassigned', 0);
        $retreatants = \App\Models\Contact::whereContactType(config('polanco.contact_type.individual'))->orderBy('sort_name')->pluck('sort_name', 'id');

        $rooms = \App\Models\Room::orderby('name')->pluck('name', 'id');
        $rooms->prepend('Unassigned', 0);

        $dt_today = Carbon::today();
        $defaults['today'] = $dt_today->month.'/'.$dt_today->day.'/'.$dt_today->year;
        $defaults['retreat_id'] = 0;
        $defaults['is_multi_registration'] = false;
        $defaults['registration_source'] = config('polanco.registration_source');
        $defaults['participant_status_type'] = \App\Models\ParticipantStatus::whereIsActive(1)->pluck('name', 'id');

        return view('registrations.create', compact('retreats', 'retreatants', 'rooms', 'defaults'));
    }

    public function add($id = null): View
    {
        $this->authorize('create-registration');
        $retreats = \App\Models\Retreat::select(DB::raw('CONCAT(idnumber, "-", title, " (",DATE_FORMAT(start_date,"%m-%d-%Y"),")") as description'), 'id')->where('end_date', '>', Carbon::today()->subWeek())->where('is_active', '=', 1)->orderBy('start_date')->pluck('description', 'id');
        $retreats->prepend('Unassigned', 0);
        $retreatant = \App\Models\Contact::findOrFail($id);

        $retreatants = collect();
        if ($retreatant->contact_type == config('polanco.contact_type.individual')) {
            $retreatants = $retreatant->pluck('sort_name', 'id');
        }
        if ($retreatant->contact_type == config('polanco.contact_type.organization')) {
            $retreatants = \App\Models\Contact::whereContactType(config('polanco.contact_type.organization'))->whereSubcontactType($retreatant->subcontact_type)->orderBy('sort_name')->pluck('sort_name', 'id');
        }

        $rooms = \App\Models\Room::orderby('name')->pluck('name', 'id');
        $rooms->prepend('Unassigned', 0);

        $defaults['contact_id'] = $id;
        $defaults['retreat_id'] = 0;
        $dt_today = Carbon::today();
        $defaults['today'] = $dt_today->month.'/'.$dt_today->day.'/'.$dt_today->year;
        $defaults['is_multi_registration'] = false;
        $defaults['registration_source'] = config('polanco.registration_source');
        $defaults['participant_status_type'] = \App\Models\ParticipantStatus::whereIsActive(1)->pluck('name', 'id');

        return view('registrations.create', compact('retreats', 'retreatants', 'rooms', 'defaults'));
    }

    public function add_group($id): View
    {
        $this->authorize('create-registration');

        $retreats = \App\Models\Retreat::select(DB::raw('CONCAT(idnumber, "-", title, " (",DATE_FORMAT(start_date,"%m-%d-%Y"),")") as description'), 'id')->where('end_date', '>', Carbon::today()->subWeek())->orderBy('start_date')->pluck('description', 'id');
        $retreats->prepend('Unassigned', 0);
        // if the $id parameter is not a valid group fail with 404
        $group = \App\Models\Group::findOrFail($id);

        $groups = \App\Models\Group::orderBy('title')->pluck('title', 'id');

        $rooms = \App\Models\Room::orderby('name')->pluck('name', 'id');
        $rooms->prepend('Unassigned', 0);

        $defaults['group_id'] = $id;
        $defaults['retreat_id'] = 0;
        $dt_today = Carbon::today();
        $defaults['today'] = $dt_today->month.'/'.$dt_today->day.'/'.$dt_today->year;
        $defaults['registration_source'] = config('polanco.registration_source');
        $defaults['participant_status_type'] = \App\Models\ParticipantStatus::whereIsActive(1)->pluck('name', 'id');

        return view('registrations.add_group', compact('retreats', 'groups', 'rooms', 'defaults'));
        //dd($retreatants);
    }

    public function register($retreat_id = 0, $contact_id = 0): View
    {
        $this->authorize('create-registration');

        if ($retreat_id > 0) {
            $retreats = \App\Models\Retreat::select(DB::raw('CONCAT(idnumber, "-", title, " (",DATE_FORMAT(start_date,"%m-%d-%Y"),")") as description'), 'id')->whereId($retreat_id)->orderBy('start_date')->pluck('description', 'id');
        } else {
            $retreats = \App\Models\Retreat::select(DB::raw('CONCAT(idnumber, "-", title, " (",DATE_FORMAT(start_date,"%m-%d-%Y"),")") as description'), 'id')->where('end_date', '>', Carbon::today())->orderBy('start_date')->pluck('description', 'id');
        }
        $retreats->prepend('Unassigned', 0);
        /* get the current retreat to determine the type of retreat
         * based on the type of retreat, determine if we should allow multiple registrations
         * multiple registrations should not have a room assignment (use assign rooms if needed)
         */
        $retreat = \App\Models\Retreat::findOrFail($retreat_id);

        // Day , Conference, Contract, Diocesan, Meeting, Workshop
        $multi_registration_event_types = [config('polanco.event_type.day'), config('polanco.event_type.contract'), config('polanco.event_type.conference'), config('polanco.event_type.diocesan'), config('polanco.event_type.meeting'), config('polanco.event_type.workshop'), config('polanco.event_type.jesuit')];
        if (in_array($retreat->event_type_id, $multi_registration_event_types)) {
            $defaults['is_multi_registration'] = true;
        } else {
            $defaults['is_multi_registration'] = false;
        }
        if ($contact_id > 0) {
            $retreatants = \App\Models\Contact::whereId($contact_id)->orderBy('sort_name')->pluck('sort_name', 'id');
        } else {
            $retreatants = \App\Models\Contact::orderBy('sort_name')->pluck('sort_name', 'id');
        }
        $retreatants->prepend('Unassigned', 0);

        $rooms = \App\Models\Room::orderby('name')->pluck('name', 'id');
        $rooms->prepend('Unassigned', 0);

        $dt_today = Carbon::today();
        $defaults['retreat_id'] = $retreat_id;
        $defaults['contact_id'] = $contact_id;
        $defaults['today'] = $dt_today->month.'/'.$dt_today->day.'/'.$dt_today->year;
        $defaults['registration_source'] = config('polanco.registration_source');
        $defaults['participant_status_type'] = \App\Models\ParticipantStatus::whereIsActive(1)->pluck('name', 'id');

        return view('registrations.create', compact('retreats', 'retreatants', 'rooms', 'defaults'));
        //dd($retreatants);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRegistrationRequest $request): RedirectResponse
    {
        $this->authorize('create-registration');
        $rooms = $request->input('rooms');
        $num_registrants = $request->input('num_registrants');
        //TODO: Should we check and verify that the contact type is an organization to allow multiselect or just allow any registration to book multiple rooms?
        $retreat = \App\Models\Retreat::findOrFail($request->input('event_id'));
        $contact = \App\Models\Contact::findOrFail($request->input('contact_id'));
        /*
         * Used primarily for registering groups
         * If a number of registrants is selected, then add that many registrations
         * num_registrants causes rooms to be ignored (either use num_registrants and assign rooms later
         * or reserve rooms - for double occupancy rooms you have to do this twice to get the number or retreatants correct
         *
         */
        if ($num_registrants > 0) {
            for ($i = 1; $i <= $num_registrants; $i++) {
                $registration = new \App\Models\Registration;
                $registration->event_id = $request->input('event_id');
                $registration->contact_id = $request->input('contact_id');
                $registration->source = $request->input('source');
                $registration->status_id = $request->input('status_id');
                $registration->register_date = $request->input('register_date');
                $registration->attendance_confirm_date = $request->input('attendance_confirm_date');
                if (! empty($request->input('canceled_at'))) {
                    $registration->canceled_at = $request->input('canceled_at');
                }
                if (! empty($request->input('arrived_at'))) {
                    $registration->arrived_at = $request->input('arrived_at');
                }
                if (! empty($request->input('departed_at'))) {
                    $registration->departed_at = $request->input('departed_at');
                }
                $registration->room_id = null;
                $registration->registration_confirm_date = $request->input('registration_confirm_date');
                $registration->confirmed_by = $request->input('confirmed_by');
                $registration->deposit = $request->input('deposit');
                $registration->notes = $request->input('notes');
                $registration->save();
            }
        } else {
            foreach ($rooms as $room) {
                //ensure that it is a valid room (not N/A)
                $registration = new \App\Models\Registration;
                $registration->event_id = $request->input('event_id');
                $registration->contact_id = $request->input('contact_id');
                $registration->source = $request->input('source');
                $registration->status_id = $request->input('status_id');
                $registration->register_date = $request->input('register_date');
                $registration->attendance_confirm_date = $request->input('attendance_confirm_date');
                if (! empty($request->input('canceled_at'))) {
                    $registration->canceled_at = $request->input('canceled_at');
                }
                if (! empty($request->input('arrived_at'))) {
                    $registration->arrived_at = $request->input('arrived_at');
                }
                if (! empty($request->input('departed_at'))) {
                    $registration->departed_at = $request->input('departed_at');
                }
                $registration->room_id = $room;
                $registration->registration_confirm_date = $request->input('registration_confirm_date');
                $registration->confirmed_by = $request->input('confirmed_by');
                $registration->deposit = $request->input('deposit');
                $registration->notes = $request->input('notes');
                $registration->remember_token = Str::random(60);
                $registration->save();
                //TODO: verify that the newly created room assignment does not conflict with an existing one
            }
        }

        flash('Registration #: <a href="'.url('/registration/'.$registration->id).'">'.$registration->id.'</a> added')->success();

        return redirect(url($contact->contact_url));
        // return Redirect::action([\App\Http\Controllers\PersonController::class, 'show'], $registration->contact_id);
    }

    public function store_group(StoreGroupRegistrationRequest $request): RedirectResponse
    {
        $this->authorize('create-registration');

        $retreat = \App\Models\Retreat::findOrFail($request->input('event_id'));
        $group = \App\Models\Group::findOrFail($request->input('group_id'));
        $group_members = \App\Models\GroupContact::whereGroupId($group->id)->whereStatus('Added')->get();
        foreach ($group_members as $group_member) {
            //ensure that it is a valid room (not N/A)
            $registration = new \App\Models\Registration;
            $registration->event_id = $retreat->id;
            $registration->contact_id = $group_member->contact_id;
            $registration->status_id = $request->input('status_id');
            $registration->register_date = $request->input('register_date');
            $registration->attendance_confirm_date = $request->input('attendance_confirm_date');
            if (! empty($request->input('canceled_at'))) {
                $registration->canceled_at = $request->input('canceled_at');
            }
            if (! empty($request->input('arrived_at'))) {
                $registration->arrived_at = $request->input('arrived_at');
            }
            if (! empty($request->input('departed_at'))) {
                $registration->departed_at = $request->input('departed_at');
            }
            $registration->room_id = 0;
            $registration->registration_confirm_date = $request->input('registration_confirm_date');
            $registration->confirmed_by = $request->input('confirmed_by');
            $registration->deposit = $request->input('deposit');
            $registration->notes = $request->input('notes');
            $registration->save();
            //TODO: verify that the newly created room assignment does not conflict with an existing one
        }
        flash('Registration(s) added to '.$retreat->title.'for members of group: <a href="'.url('/group/'.$group->id).'">'.$group->name.'</a>')->success();

        return Redirect::action([\App\Http\Controllers\RetreatController::class, 'show'], $retreat->id);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): View
    {
        $this->authorize('show-registration');
        $registration = \App\Models\Registration::with('retreat', 'retreatant', 'room')->findOrFail($id);

        return view('registrations.show', compact('registration')); //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $this->authorize('update-registration');

        $registration = \App\Models\Registration::with('retreatant', 'retreat', 'room')->findOrFail($id);
        $retreatant = \App\Models\Contact::findOrFail($registration->contact_id);
        $retreats = \App\Models\Retreat::select(DB::raw('CONCAT(idnumber, "-", title, " (",DATE_FORMAT(start_date,"%m-%d-%Y"),")") as description'), 'id')->where('end_date', '>', Carbon::today())->orderBy('start_date')->pluck('description', 'id');

        //TODO: we will want to be able to switch between types when going from a group registration to individual room assignment
        if ($retreatant->contact_type == config('polanco.contact_type.individual')) {
            $retreatants = \App\Models\Contact::whereContactType(config('polanco.contact_type.individual'))->orderBy('sort_name')->pluck('sort_name', 'id');
        }
        if ($retreatant->contact_type == config('polanco.contact_type.organization')) {
            $retreatants = \App\Models\Contact::whereContactType(config('polanco.contact_type.organization'))->whereSubcontactType($retreatant->subcontact_type)->orderBy('sort_name')->pluck('sort_name', 'id');
        }

        $rooms = \App\Models\Room::orderby('name')->pluck('name', 'id');
        $rooms->prepend('Unassigned', 0);

        /* Check to see if the current registration is for a past retreat and if so, add it to the collection */
        // $retreats[0] = 'Unassigned';

        if ($registration->retreat->end < Carbon::now()) {
            $retreats[$registration->event_id] = $registration->retreat->idnumber.'-'.$registration->retreat->title.' ('.date('m-d-Y', strtotime($registration->retreat->start_date)).')';
        }

        $defaults['registration_source'] = config('polanco.registration_source');

        // prevent dataloss of existing sources; shouldn't really be necessary with use of polanco.registration_source but just in case this will help prevent unintended data loss
        foreach ($defaults['registration_source'] as $source) {
            if (! Arr::has($defaults['registration_source'], $registration->source)) {
                $defaults['registration_source'][$registration->source] = $registration->source;
            }
        }

        $defaults['participant_status_type'] = \App\Models\ParticipantStatus::whereIsActive(1)->pluck('name', 'id');

        return view('registrations.edit', compact('registration', 'retreats', 'rooms', 'defaults'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRegistrationRequest $request, int $id)
    {
        $this->authorize('update-registration');

        $registration = \App\Models\Registration::findOrFail($request->input('id'));
        $retreat = \App\Models\Retreat::findOrFail($request->input('event_id'));
        $contact = \App\Models\Contact::findOrFail($registration->contact_id);

        $registration->event_id = $request->input('event_id');
        // TODO: pull this from the retreat's start_date and end_date
        //$registration->start = $retreat->start;
        //$registration->end = $retreat->end;
        //$registration->contact_id= $request->input('contact_id');
        $registration->status_id = $request->input('status_id');
        $registration->register_date = $request->input('register_date');
        $registration->attendance_confirm_date = $request->input('attendance_confirm_date');
        $registration->registration_confirm_date = $request->input('registration_confirm_date');
        $registration->source = $request->input('source');
        $registration->confirmed_by = $request->input('confirmed_by');
        $registration->deposit = $request->input('deposit');
        $registration->notes = $request->input('notes');
        $registration->canceled_at = $request->input('canceled_at');
        $registration->arrived_at = $request->input('arrived_at');
        $registration->departed_at = $request->input('departed_at');

        $registration->room_id = $request->input('room_id');

        // email finance if a (registration's event_id changes) or (the event is canceled and the registration has a deposit amount)
        if (config('polanco.notify_registration_event_change')) { // if finance notification is enabled
            $finance_email = config('polanco.finance_email');
            $original_event = \App\Models\Retreat::findOrFail($registration->getOriginal('event_id'));

            if ($registration->isDirty('event_id')) {
                // dd($registration,$registration->event_id,$registration->getOriginal('event_id'));
                // return view('emails.registration-event-change', compact('registration', 'retreat', 'original_event'));
                try {
                    Mail::to($finance_email)->send(new RegistrationEventChange($registration, $retreat, $original_event));
                } catch (\Exception $e) { //failed to send finance notification of event_id change on registration
                    flash('Email notification NOT sent to finance regarding event change to Registration #: <a href="'.url('/registration/'.$registration->id).'">'.$registration->id.'</a>')->warning();
                }
                flash('Email notification sent to finance regarding event change to Registration #: <a href="'.url('/registration/'.$registration->id).'">'.$registration->id.'</a>')->success();
            }

            if (($registration->deposit > 0) && ($registration->status_id == config('polanco.registration_status_id.canceled')) && $registration->isDirty('status_id')) {
                try {
                    Mail::to($finance_email)->send(new RegistrationCanceledChange($registration, $retreat));
                } catch (\Exception $e) { //failed to send finance notification of event_id change on registration
                    dd($e);
                }
                flash('Email notification sent to finance regarding cancelation (with deposit) of Registration #: <a href="'.url('/registration/'.$registration->id).'">'.$registration->id.'</a>')->success();
            }
        }

        if ($registration->event_id == config('polanco.event.open_deposit')) {
            $registration->room_id = 0;
        }

        $registration->save();

        flash('Registration #: <a href="'.url('/registration/'.$registration->id).'">'.$registration->id.'</a> updated')->success();

        return redirect(url($contact->contact_url));

        //        return Redirect::action([\App\Http\Controllers\PersonController::class, 'show'], $registration->contact_id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->authorize('delete-registration');

        $registration = \App\Models\Registration::findOrFail($id);
        $retreat = \App\Models\Retreat::findOrFail($registration->event_id);

        \App\Models\Registration::destroy($id);
        $countregistrations = \App\Models\Registration::where('event_id', '=', $registration->event_id)->count();
        //$retreat->attending = $countregistrations;
        $retreat->save();

        flash('Registration #: '.$registration->id.' deleted')->warning()->important();

        return Redirect::action([self::class, 'index']);
    }

    public function confirm($id): RedirectResponse
    {
        $this->authorize('update-registration');

        $registration = \App\Models\Registration::findOrFail($id);
        $registration->registration_confirm_date = Carbon::now();
        $registration->save();

        return redirect()->back();
    }

    public function attend($id): RedirectResponse
    {
        $this->authorize('update-registration');
        $registration = \App\Models\Registration::findOrFail($id);
        $registration->attendance_confirm_date = Carbon::now();
        $registration->save();

        return redirect()->back();
    }

    public function arrive($id): RedirectResponse
    {
        $this->authorize('update-registration');
        $registration = \App\Models\Registration::findOrFail($id);
        $registration->arrived_at = Carbon::now();
        $registration->save();

        return redirect()->back();
    }

    public function depart($id): RedirectResponse
    {
        $this->authorize('update-registration');
        $registration = \App\Models\Registration::findOrFail($id);
        $registration->departed_at = Carbon::now();
        $registration->save();

        return redirect()->back();
    }

    public function cancel($id): RedirectResponse
    {
        $this->authorize('update-registration');
        $registration = \App\Models\Registration::findOrFail($id);
        $registration->canceled_at = Carbon::now();
        $registration->save();

        return redirect()->back();
    }

    public function waitlist($id): RedirectResponse
    {
        $this->authorize('update-registration');
        $registration = \App\Models\Registration::findOrFail($id);
        $registration->status_id = config('polanco.registration_status_id.waitlist');
        $registration->save();

        return redirect()->back();
    }

    public function offwaitlist($id): RedirectResponse
    {
        $this->authorize('update-registration');
        $registration = \App\Models\Registration::findOrFail($id);
        $registration->status_id = config('polanco.registration_status_id.registered');
        $registration->save();

        return redirect()->back();
    }

    public function registrationEmail(Registration $participant): RedirectResponse
    {
        $this->authorize('show-registration');

        // 1. Get a primary email address for participant.
        $primaryEmail = $participant->contact->primaryEmail()->first();

        if ($primaryEmail) {
            // 2. Setup infomration to be used with touchpoint for sending out registration email.
            $touchpoint = new \App\Models\Touchpoint;
            $touchpoint->person_id = $participant->contact->id;
            $touchpoint->staff_id = config('polanco.self.id');
            $touchpoint->touched_at = Carbon::now();
            $touchpoint->type = 'Email';

            // 4. Only send out email if registration email is missing.
            $missingRegistrationEmail = $touchpoint->missingRegistrationEmail($participant->contact->id, $participant->retreat->idnumber);

            if ($missingRegistrationEmail) {
                try {
                    Mail::to($primaryEmail)->send(new RetreatRegistration($participant));
                } catch (\Exception $e) {
                    $touchpoint->notes = $participant->retreat->idnumber.' registration email failed.';
                }
                $touchpoint->notes = $participant->retreat->idnumber.' registration email sent.';
                $touchpoint->save();
            }
        }

        return redirect('person/'.$participant->contact->id);
    }

    public function send_confirmation_email($id): RedirectResponse
    {
        $this->authorize('update-registration');
        $registration = \App\Models\Registration::findOrFail($id);
        $current_user = Auth::user();
        $primary_email = $registration->retreatant->email_primary_text;

        $success_message = 'Confirmation email has been sent for retreat #'.$registration->event_idnumber;
        $error_message = 'Confirmation email failed to send for retreat #'.$registration->event_idnumber.': ';

        if (! empty($primary_email) && $registration->contact->do_not_email == 0) { // the retreatant does not have a primary email address to send to
            if (! isset($registration->registration_confirm_date)) { // ensure that the retreatant has not already confirmed
                // For registration emails, remember_token must be set for the retreat participant.
                if ($registration->remember_token == null) {
                    $registration->remember_token = Str::random(60);
                    $registration->save();
                }

                // Setup touchpoint for this Registrations Confirmation Email
                $touchpoint = new \App\Models\Touchpoint;
                $touchpoint->person_id = $registration->contact_id;
                $touchpoint->staff_id = (isset($current_user->contact_id)) ? $current_user->contact_id : config('polanco.self.id');
                $touchpoint->touched_at = Carbon::now();
                $touchpoint->type = 'Email';

                try {
                    Mail::to($primary_email)->queue(new \App\Mail\RetreatConfirmation($registration));
                    $touchpoint->notes = $success_message;
                    $touchpoint->save();
                    flash('Confirmation email sent to '.$registration->contact_sort_name.' for Retreat #'.$registration->event_idnumber)->success();
                } catch (\Exception $e) {
                    $touchpoint->notes = $error_message.$e->getMessage();
                    $touchpoint->save();
                    flash('Confirmation email failed to send. See <a href="/touchpoint/'.$touchpoint->id.'">touchpoint</a> for details.')->warning();
                }
            } else {
                flash('Confirmation email not sent. It appears that the retreatant ('.$registration->contact_sort_name.') has already confirmed his/her attendance for Retreat #'.$registration->event_idnumber)->info();
            }
        } else {
            flash('Confirmation email not sent because the retreatant ('.$registration->contact_sort_name.') does not appear to have a primary email address or has requested NOT to receive emails.')->warning();
        }

        return redirect('registration/'.$registration->id);
    }

    public function confirmAttendance($token): RedirectResponse
    {
        $registration = \App\Models\Registration::where('remember_token', $token)->first();

        if ($registration) {
            $registration->registration_confirm_date = Carbon::now();
            $registration->remember_token = null;
            $registration->save();
        }

        return redirect()->away('https://montserratretreat.org/retreat-attendance');
    }
}
