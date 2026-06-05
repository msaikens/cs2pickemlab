<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class MatchController extends Controller
{
    public function index(): View
    {
        return view('admin.matches.index');
    }
}
