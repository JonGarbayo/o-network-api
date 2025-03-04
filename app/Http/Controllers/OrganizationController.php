<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;
use App\Http\Resources\OrganizationResource;
use App\Http\Requests\OrganizationRequest;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class OrganizationController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Organization::class, 'organization');
    }

    /**
     * Override the default mapping of the resource policies methods to remove
     * the create one.
     * This create action must be available for unauthenticated user, and so not
     * being checked by the policies system. A policy requires an authentication
     * to work, or it will systematically return a 403 error.
     * (the resourceAbilityMap() method comes from the
     * AuthorizesRequests trait, imported in the Controller parent class).
     *
     * @return array
     */
    protected function resourceAbilityMap(): array
    {
        return array_filter(parent::resourceAbilityMap(), function ($ability) {
            return $ability !== 'create';
        });
    }

    /**
     * Return all the organizations of the database. But in this app MVP, no
     * user with any role can access that full list, it's blocked by the
     * OrganizationPolicy.
     * This method is only here to avoid an error when requesting the
     * /organizations URI with the GET verb.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Organization::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OrganizationRequest $request)
    {
        $this->checkNameConflict($request);

        return new OrganizationResource(Organization::create($request->validated()));
    }

    /**
     * Validate the organization without storing it.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function check(OrganizationRequest $request)
    {
        $this->checkNameConflict($request);
        return response()->noContent();
    }

    /**
     * Check if there is already an organization with the same name than the one
     * sent with the request. Send a 409 response if it's the case.
     * This treatment could have been done in the FormRequest classes, but they
     * return a 422 HTTP status code, whereas a 409 is more appropriate in this
     * case.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    private function checkNameConflict(Request $request)
    {
        $query = Organization::where('name', $request->input('name'));
        $conflicts = $query->exists();

        if ($conflicts) {
            throw ValidationException::
                withMessages(['name' => "Cette organisation existe déjà. Merci de choisir un autre nom."])
                ->status(Response::HTTP_CONFLICT);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Http\Response
     */
    public function show(Organization $organization)
    {
        return new OrganizationResource($organization);
    }
}
