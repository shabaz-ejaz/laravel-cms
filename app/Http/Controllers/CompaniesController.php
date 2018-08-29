<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\CompanyService;
use App\Http\Requests\CompanyCreateRequest;
use App\Http\Requests\CompanyUpdateRequest;
use Illuminate\Routing\Redirector;

class CompaniesController extends Controller
{
    public function __construct(CompanyService $company_service)
    {
        $this->service = $company_service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $companies = $this->service->paginated();
        $industries = \Config::get('enums.industry_options');
        return view('companies.index', compact('companies', 'industries'));
    }

    /**
     * Display a listing of the resource searched.
     *
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $companies = $this->service->paginated();
        $industries = \Config::get('enums.industry_options');
        return view('companies.index', compact('companies', 'industries'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $industries = array_flip (\Config::get('enums.industry_options'));
        return view('companies.create', compact('industries'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\CompanyCreateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CompanyCreateRequest $request)
    {
        $result = $this->service->create($request->except('_token'));

        if ($result) {
            return redirect(route('companies.edit', ['id' => $result->id]))->with('message', 'Successfully created');
        }

        return redirect(route('companies.index'))->withErrors('Failed to create');
    }

    /**
     * Display the company.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $company = $this->service->find($id);
        return view('companies.show')->with('company', $company);
    }

    /**
     * Show the form for editing the company.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $company = $this->service->find($id);
        $industries = array_flip(\Config::get('enums.industry_options'));
        return view('companies.edit', compact('company', 'industries'));
    }

    /**
     * Update the companies in storage.
     *
     * @param  App\Http\Requests\CompanyUpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(CompanyUpdateRequest $request, $id)
    {
        $result = $this->service->update($id, $request->except('_token'));

        if ($result) {
            return back()->with('message', 'Successfully updated');
        }

        return back()->withErrors('Failed to update');
    }

    /**
     * Remove the companies from storage.
     *
     * @param  int  $id
     * @return Redirector
     */
    public function destroy($id)
    {
        $result = $this->service->destroy($id);

        if ($result) {
            return redirect(route('companies.index'))->with('message', 'Successfully deleted');
        }

        return redirect(route('companies.index'))->withErrors('Failed to delete');
    }
}
