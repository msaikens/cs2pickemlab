<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\ModerationIncident;
use App\Services\CrackdownService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ModerationAppealController extends Controller
{
    public function __construct(
        private readonly CrackdownService $crackdown
    ) {
    }

    public function store(Request $request, ModerationIncident $incident): RedirectResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'min:20', 'max:5000'],
        ]);

        $appeal = $this->crackdown->appealIncident(
            user: $request->user(),
            incident: $incident,
            message: $validated['message'],
        );

        return back()->with(
            'status',
            "Appeal submitted. Appeal #{$appeal->id} is pending review."
        );
    }
}