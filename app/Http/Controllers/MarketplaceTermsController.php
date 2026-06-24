<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MarketplaceTermsController extends Controller
{
    public function show(): View
    {
        return view('marketplace.terms');
    }

    public function accept(Request $request): RedirectResponse
    {
        $request->user()->forceFill([
            'marketplace_terms_accepted_at' => now(),
        ])->save();

        return redirect()
            ->route('marketplace.index')
            ->with('success', 'Marketplace terms accepted.');
    }
}