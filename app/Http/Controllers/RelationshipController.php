<?php

namespace App\Http\Controllers;

use App\Models\Relationship;
use DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class RelationshipController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $this->authorize('show-relationship');
        $relationships = \App\Models\Relationship::paginate(25, ['*'], 'relationships');

        return view('relationships.index', compact('relationships'));   //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): RedirectResponse
    {   // TODO: stub: re-evaluate handling of relationships to refactor person controller to avoid repetition
        $this->authorize('create-relationship');
        flash('Relationships cannot be directly created as they are managed via contacts')->error();

        return Redirect::action([self::class, 'index']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {   // relationships are not created directly here; they are created through the person controller
        // TODO: stub: re-evaluate handling of relationships to refactor person controller to avoid repetition
        $this->authorize('create-relationship');
        flash('Relationships cannot be directly stored as they are managed via contacts')->error();

        return Redirect::action([self::class, 'index']);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): View
    {
        $this->authorize('show-relationship');
        $relationship = \App\Models\Relationship::findOrFail($id);

        return view('relationships.show', compact('relationship'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): RedirectResponse
    {   // TODO: stub: re-evaluate handling of relationships to refactor person controller to avoid repetition
        $this->authorize('update-relationship');
        flash('Relationships cannot be directly edited as they are managed via contacts')->error();

        return Redirect::action([self::class, 'show'], $id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): RedirectResponse
    {   // TODO: stub: re-evaluate handling of relationships to refactor person controller to avoid repetition
        $this->authorize('update-relationship');
        flash('Relationships cannot be directly updated as they are managed via contacts')->error();

        return Redirect::action([self::class, 'show'], $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->authorize('delete-relationship');

        \App\Models\Relationship::destroy($id);

        flash('Relationship ID#: '.$id.' deleted')->warning()->important();

        return redirect()->back();
    }

    public function disjoined(): View
    {
        $this->authorize('update-contact');
        $couples = DB::table('relationship as r')
            ->select('r.id', 'r.contact_id_a as husband_id', 'h.sort_name as husband_name', 'r.contact_id_b as wife_id', 'w.sort_name as wife_name', 'ha.street_address as husband_address', 'ha.city as husband_city', 'ha.postal_code as husband_zip', 'wa.street_address as wife_address', 'wa.city as wife_city', 'wa.postal_code as wife_zip')
            ->leftJoin('contact as h', 'r.contact_id_a', '=', 'h.id')
            ->leftJoin('contact as w', 'r.contact_id_b', '=', 'w.id')
            ->leftJoin('address as ha', 'r.contact_id_a', '=', 'ha.contact_id')
            ->leftJoin('address as wa', 'r.contact_id_b', '=', 'wa.contact_id')
            ->where('r.relationship_type_id', '=', 2)
            ->where('ha.is_primary', '=', 1)
            ->where('wa.is_primary', '=', 1)
            ->whereNull('r.deleted_at')
            ->whereNull('ha.deleted_at')
            ->whereNull('wa.deleted_at')
            ->whereNull('w.deleted_at')
            ->whereNull('h.deleted_at')
            ->whereRaw('ha.street_address <> wa.street_address')
            ->orderBy('husband_name')
            ->get();

        return view('relationships.disjoined', compact('couples'));
    }

    public function rejoin($id, $dominant): RedirectResponse
    {
        $this->authorize('update-contact');
        $relationship = \App\Models\Relationship::with('contact_a.address_primary', 'contact_b.address_primary')->findOrFail($id);
        switch ($dominant) {
            case $relationship->contact_id_a:
                $relationship->contact_b->address_primary->street_address = $relationship->contact_a->address_primary->street_address;
                $relationship->contact_b->address_primary->city = $relationship->contact_a->address_primary->city;
                $relationship->contact_b->address_primary->state_province_id = $relationship->contact_a->address_primary->state_province_id;
                $relationship->contact_b->address_primary->postal_code = $relationship->contact_a->address_primary->postal_code;
                $relationship->contact_b->address_primary->country_id = $relationship->contact_a->address_primary->country_id;
                $relationship->contact_b->address_primary->save();
                break;
            case $relationship->contact_id_b:
                $relationship->contact_a->address_primary->street_address = $relationship->contact_b->address_primary->street_address;
                $relationship->contact_a->address_primary->city = $relationship->contact_b->address_primary->city;
                $relationship->contact_a->address_primary->state_province_id = $relationship->contact_b->address_primary->state_province_id;
                $relationship->contact_a->address_primary->postal_code = $relationship->contact_b->address_primary->postal_code;
                $relationship->contact_a->address_primary->country_id = $relationship->contact_b->address_primary->country_id;
                $relationship->contact_a->address_primary->save();
                break;
            default: // do not do anything as there is a relationship mismatch error
        }

        return Redirect::back();
    }
}
