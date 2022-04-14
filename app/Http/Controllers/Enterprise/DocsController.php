<?php

namespace App\Http\Controllers\Enterprise;

use Illuminate\Http\Request;

class DocsController extends \App\Http\Controllers\Controller
{

    /**
     * Show documents list
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('dashboard.club.docs', [
            'directories' => auth()->user()->company->directories,
        ]);
    }
}
