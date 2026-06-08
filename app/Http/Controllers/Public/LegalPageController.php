<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class LegalPageController extends Controller
{
    public function privacyPolicy(): View
    {
        return view('legal.privacy-policy');
    }

    public function dataUsageCollectionPolicy(): View
    {
        return view('legal.data-usage-collection-policy');
    }

    public function termsOfService(): View
    {
        return view('legal.terms-of-service');
    }

    public function affiliateDisclosures(): View
    {
        return view('legal.affiliate-disclosures');
    }

    public function disclaimer(): View
    {
        return view('legal.disclaimer');
    }
}