<?php

namespace App\Http\Controllers\Root;

use App\Models\Partner;

class PartnersController extends \App\Http\Controllers\Controller
{

    /**
     * Show partners list page
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $list = Partner::orderBy('priority', 'ASC')->get();

        return view('dashboard.root.partners.index', [
            'partners' => $list,
        ]);
    }

    /**
     * Show create partner page
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function create()
    {
        return view('dashboard.root.partners.manage');
    }

    /**
     * Show partner edit page
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function edit($partnerId)
    {
        $partner = Partner::whereId($partnerId)->first();

        if (!$partner) {
            return abort(404);
        }

        return view('dashboard.root.partners.manage', [
            'partner' => $partner,
        ]);
    }

    /**
     * Save Partner item
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function save()
    {
        request()->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'link' => 'required|string|max:255',
            'icon' => 'required|string|max:255',
            'priority' => 'required|numeric',
        ]);

        $partnerId = request()->has('id') ? request()->get('id') : null;

        if ($partnerId) {
            $partner = Partner::where('id', $partnerId)->first();

            if (!$partner) {
                return abort(404);
            }
        } else {
            $partner = new Partner;
        }

        $partner->name        = trim(request()->get('name'));
        $partner->description = trim(request()->get('description'));
        $partner->link        = trim(request()->get('link'));
        $partner->icon        = trim(request()->get('icon'));
        $partner->priority    = intval(request()->get('priority'));
        $partner->is_active   = request()->get('is_active') ? 1 : 0;
        $partner->save();

        return redirect(route('root.partners'))->with('successMessage', 'Your updates have been saved');
    }
}
