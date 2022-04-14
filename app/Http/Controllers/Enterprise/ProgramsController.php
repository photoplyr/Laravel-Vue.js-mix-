<?php

namespace App\Http\Controllers\Enterprise;

use App\Models\Company\Company;
use App\Models\Program;
use App\Models\Company\CompanyProgram;
use App\Models\Company\CompanyProgramTier;
use App\Models\Company\CompanyProgramSector;
use App\Models\Company\CompanyMasterProgram;

class ProgramsController extends \App\Http\Controllers\Controller
{
    protected $perPage = 15;

    /**
     * Show programs list
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $company = auth()->user()->company;
        if (!$company) {
            return abort(404);
        }

        $companyProgram = CompanyMasterProgram::with(['tier', 'sector'])->orderBy('company_id')->get();

        $total = $companyProgram->count();

        return view('dashboard.enterprise.programs.index', [
            'programs' => $companyProgram->slice(0, $this->perPage)->map(function($item) {
                return (object) [
                    'id'          => $item->id,
                    'company'     => $item->company ? trim($item->company->name) : 'None',
                    'name'        => $item->program ? trim($item->program->name) : 'None',
                    'tier'        => $item->tier && $item->tier->name ? $item->tier->name : 'None',
                    'sector'      => $item->sector && $item->sector->name ? $item->sector->name : 'None',
                    'rate'        => '$'.number_format($item->rate, 2),
                    'program_id'  => $item->program_id,
                    'hourly_rate' => '$'.number_format($item->hourly_rate, 2),
                    'daily_rate'  => '$'.number_format($item->daily_rate, 2),
                    'allowance'   => $item->allowance,
                    'restriction' => $item->restriction,
                    'status'      => $item->status,
                ];
            }),
            'pages'    => ceil($total / $this->perPage),
        ]);
    }

    /**
     * Filter programs list
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function search()
    {
        $programs = CompanyMasterProgram::with(['tier', 'sector'])->orderBy('company_id')->get();

        if (request()->get('search')) {
            $programs = $programs->filter(function ($item) {
                $valid = false;
                if ($item->company) $valid = stristr($item->company->name, request()->get('search'));
                if (!$valid && $item->program) $valid = stristr($item->program->name, request()->get('search'));

                return $valid;
            });
        }

        $page = intval(request()->get('page')) > 0 ? intval(request()->get('page')) - 1 : 0;

        $total = $programs->count();

        return response()->json([
            'success' => true,
            'list'    => $programs->slice($page * $this->perPage, $this->perPage)->map(function($item) {
                return (object) [
                    'id'          => $item->id,
                    'company'     => $item->company ? trim($item->company->name) : 'None',
                    'name'        => $item->program ? trim($item->program->name) : 'None',
                    'tier'        => $item->tier && $item->tier->name ? $item->tier->name : 'None',
                    'sector'      => $item->sector && $item->sector->name ? $item->sector->name : 'None',
                    'rate'        => '$'.number_format($item->rate, 2),
                    'program_id'  => $item->program_id,
                    'hourly_rate' => '$'.number_format($item->hourly_rate, 2),
                    'daily_rate'  => '$'.number_format($item->daily_rate, 2),
                    'allowance'   => $item->allowance,
                    'restriction' => $item->restriction,
                    'status'      => $item->status,
                ];
            }),
            'pages'   => ceil($total / $this->perPage),
        ]);
    }

    /**
     * Show add program list
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function add()
    {
        $company = auth()->user()->company;
        if (!$company) {
            return abort(404);
        }

        $allPrograms = Program::whereNotIn('id', [0])
                              ->where('type', 1)
                              ->where('status', 1)
                              ->where('new', 0)
                              ->get();

        $list = [];
        foreach ($allPrograms as $program) {
            $list[] = (object) [
                'id'   => $program->id,
                'name' => $program->name,
            ];
        }

        return view('dashboard.enterprise.programs.add', [
            'programs' => $list,
        ]);
    }

    /**
     * Show edit program page
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function edit($id)
    {
        // $company = auth()->user()->company;
        // if (!$company) {
        //     return abort(404);
        // }

        // $location = auth()->user()->location;

        // if (!$location) {
        //     return abort(404);
        // }

        $program = CompanyMasterProgram::where('id', $id)
                                 // ->where('company_id', $company->id)
                                 ->first();

        if (!$program) {
            return abort(404);
        }

        $programs = Program::whereNotIn('id', [0])
                              ->where('type', 1)
                              ->where('status', 1)
                              ->where('new', 0)
                              ->pluck('name', 'id');

        $companys = Company::where('status', 1)->pluck('name', 'id');
        $tiers   = CompanyProgramTier::where('status', 1)->pluck('name', 'id');
        $sectors = CompanyProgramSector::where('status', 1)->pluck('name', 'id');

        return view('dashboard.enterprise.programs.manage', [
            'program' => $program,
            'programs' => $programs,
            'companys' => $companys,
            'tiers'   => $tiers,
            'sectors' => $sectors,
        ]);
    }

    /**
     * Save program
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function save($id)
    {
        // $company = auth()->user()->company;
        // if (!$company) {
        //     return abort(404);
        // }

        // $location = auth()->user()->location;
        // if (!$location) {
        //     return abort(404);
        // }

        $program = CompanyMasterProgram::where('id', $id)
                                 // ->where('company_id', $company->id)
                                 ->first();

        if (!$program) {
            return abort(404);
        }

        $companyIds   = Company::where('status', 1)->pluck('id')->toArray();
        $tiersIds   = CompanyProgramTier::where('status', 1)->pluck('id')->toArray();
        $sectorsIds = CompanyProgramSector::where('status', 1)->pluck('id')->toArray();

        request()->validate([
            'rate'        => 'required|numeric',
            'daily_rate'  => 'required|numeric',
            'hourly_rate' => 'required|numeric',
            'allowance'   => 'required|numeric',
            'restriction' => 'required|numeric',
            'company_id'  => 'required|in:'.implode(',', $companyIds),
            'tier_id'     => 'required|in:'.implode(',', $tiersIds),
            'sector_id'   => 'required|in:'.implode(',', $sectorsIds),
            'status'      => 'nullable',
        ]);

        $program->rate        = request()->get('rate');
        $program->daily_rate  = request()->get('daily_rate');
        $program->hourly_rate = request()->get('hourly_rate');
        $program->allowance   = request()->get('allowance');
        $program->restriction = request()->get('restriction');
        $program->status      = request()->get('status') ? 1 : 0;
        $program->sector_id   = request()->get('sector_id');
        $program->tier_id     = request()->get('tier_id');
        $program->company_id  = request()->get('company_id');
        $program->save();

        return redirect(route('enterprise.programs'))->with('successMessage', 'Your updates have been saved');
    }

    /**
     * Connect program for company
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function connect()
    {
        $programId = request()->get('program_id');

        $rate = request()->get('rate-'.$programId);

        $program = Program::where('id', $programId)->where('type', 1)->first();
        if ($program) {
            $companyProgram = CompanyMasterProgram::create([
                'company_id'  => null,
                'program_id'  => $program->id,
                'status' => 0,
            ]);

            $companyProgram->rate = $rate && intval($rate) > 0 ? $rate : 0;
            $companyProgram->save();

            return redirect(route('enterprise.programs'))->with('successMessage', 'Program successfully added');
        }

        return redirect(route('enterprise.programs'));
    }

    /**
     * Disable program for company
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function disable($id)
    {
        CompanyMasterProgram::find($id)->delete();
        CompanyProgram::where('company_master_id', $id)->delete();

        return redirect(route('enterprise.programs'))->with('successMessage', 'Program successfully removed');
    }
}
