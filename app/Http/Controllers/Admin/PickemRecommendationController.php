<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PickemRecommendationController extends Controller
{
    public function index(): View
    {
        return view('admin.pickem.index');
    }
}
