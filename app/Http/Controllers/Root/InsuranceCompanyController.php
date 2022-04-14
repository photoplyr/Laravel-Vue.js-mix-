<?php

namespace App\Http\Controllers\Root;

use App\Models\Company\Company;
use App\Models\Company\Insurance;

class InsuranceCompanyController extends \App\Http\Controllers\Controller
{

    protected $perPage = 15;

    /**
     * Show icompany list page
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $list = Insurance::orderBy('id', 'ASC')->get();

        $total = $list->count();

        return view('dashboard.root.insurance_company.index', [
            'icompanys' => $list->slice(0, $this->perPage)->toArray(),
            'pages'     => ceil($total / $this->perPage),
        ]);
    }

    /**
     * Filter insurance company list
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function search()
    {
        $companys = Company::all();

        if (request()->get('search')) {
            $companys = $companys->filter(function ($item) {
                return  stristr($item->name, request()->get('search'));
            });
        }

        $companys = $companys->pluck('id');

        $icompany = Insurance::whereIn('insurance_id', $companys)->orWhereIn('company_id', $companys)->get();

        $page = intval(request()->get('page')) > 0 ? intval(request()->get('page')) - 1 : 0;

        $total = $icompany->count();

        return response()->json([
            'success' => true,
            'list'    => $icompany->slice($page * $this->perPage, $this->perPage)->toArray(),
            'pages'   => ceil($total / $this->perPage),
        ]);
    }

    /**
     * Show create icompany page
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function create()
    {
        if (!auth()->user()->isRoot()) {
            return abort(403);
        }

        $companies = Company::all();

        return view('dashboard.root.insurance_company.manage', [
            'companies' => $companies
        ]);
    }

    /**
     * Show icompany edit page
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function edit($icompanyId)
    {
        if (!auth()->user()->isRoot()) {
            return abort(403);
        }

        $icompany = Insurance::whereId($icompanyId)->first();
        $companies = Company::all();

        if (!$icompany) {
            return abort(404);
        }

        return view('dashboard.root.insurance_company.manage', [
            'icompany'  => $icompany,
            'companies' => $companies
        ]);
    }

    /**
     * Save icompany
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function save()
    {
        request()->validate([
            'insurance_id' => 'required',
            'company_id'   => 'required|different:insurance_id'
        ]);

        $icompanyId = request()->has('id') ? request()->get('id') : null;

        if (!$icompanyId) {
            $icompany = Insurance::create(request()->all());
        } else {
            $icompany = Insurance::find($icompanyId);
            if (!$icompany) {
                return abort(404);
            }
            $icompany->update(request()->all());
        }

        return redirect(route('root.insurance_company'))->with('successMessage', 'Your updates have been saved');
    }
}
