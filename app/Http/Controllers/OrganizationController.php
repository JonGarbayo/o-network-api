<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;
use App\Http\Resources\OrganizationResource;
use App\Http\Requests\StoreOrganizationRequest;
use App\Http\Requests\UpdateOrganizationRequest;
use App\Http\Requests\ValidateOrganizationRequest;
use Illuminate\Http\Response;

class OrganizationController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Organization::class, 'organization');
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
    public function store(StoreOrganizationRequest $request)
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
    public function check(ValidateOrganizationRequest $request)
    {
        $this->checkNameConflict($request);
    }

    /**
     * Check if there is already an organization with the same name than the one
     * sent with the request. Send a 409 response if it's the case.
     * This treatment could have been done in the FormRequest classes, but they
     * return a 422 HTTP status code, whereas a 409 is more appropriate in this
     * case.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Organization  $organization  In case of an update, the concerned organization.
     * @return \Illuminate\Http\Response
     */
    private function checkNameConflict(Request $request, ?Organization $organization = null)
    {
        $name = $request->input('name');
        $query = Organization::where('name', $name);

        // In case of an update, avoids throwing the error if the concerned
        // organization kept the same name
        if ($organization) {
            $query->where('id', '!=', $organization->id);
        }

        $conflicts = $query->exists();

        if ($conflicts) {
            response()
                ->json(['message' => "The organization '$name' already exists."], Response::HTTP_CONFLICT)
                ->throwResponse();
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
