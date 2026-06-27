<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ModerationIncident;
use App\Models\User;
use App\Services\CrackdownService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\ModerationAppeal;

class CrackdownController extends Controller
{
    public function __construct(
        private readonly CrackdownService $crackdown
    ) {
    }

    public function index(Request $request): View
    {
        $users = User::query()
            ->with(['profile', 'steamAccount'])
            ->withCount(['inboxMessages', 'moderationIncidents'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->query('search'));

                $query->where(function ($inner) use ($search) {
                    $inner
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('site_ban_incident_number', 'like', "%{$search}%")
                        ->orWhere('site_suspension_incident_number', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $latestIncidents = ModerationIncident::query()
            ->with(['subjectUser.profile', 'adminUser.profile'])
            ->latest()
            ->limit(10)
            ->get();

        $pendingAppeals = ModerationAppeal::query()
            ->with(['user.profile', 'incident.adminUser'])
            ->where('status', ModerationAppeal::STATUS_PENDING)
            ->latest()
            ->limit(20)
            ->get();

        return view('admin.crackdown.index', [
            'users' => $users,
            'latestIncidents' => $latestIncidents,
            'pendingAppeals' => $pendingAppeals,
            
        ]);
    }

    public function warn(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'user_message' => ['required', 'string', 'max:5000'],
            'admin_note' => ['nullable', 'string', 'max:5000'],
        ]);

        $incident = $this->crackdown->warn($user, $request->user(), $validated);

        return back()->with(
            'status',
            "Warning issued. Incident {$incident->incident_number}."
        );
    }

    public function suspend(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'user_message' => ['required', 'string', 'max:5000'],
            'admin_note' => ['nullable', 'string', 'max:5000'],
            'ends_at' => ['required', 'date', 'after:now'],
        ]);

        $incident = $this->crackdown->suspend($user, $request->user(), $validated);

        return back()->with(
            'status',
            "User suspended. Incident {$incident->incident_number}."
        );
    }

    public function ban(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'user_message' => ['required', 'string', 'max:5000'],
            'admin_note' => ['nullable', 'string', 'max:5000'],
        ]);

        $incident = $this->crackdown->ban($user, $request->user(), $validated);

        return back()->with(
            'status',
            "User banned. Incident {$incident->incident_number}."
        );
    }

    public function removeListings(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'user_message' => ['required', 'string', 'max:5000'],
            'admin_note' => ['nullable', 'string', 'max:5000'],
        ]);

        $incident = $this->crackdown->removeListings($user, $request->user(), $validated);

        return back()->with(
            'status',
            "Listings removed where applicable. Incident {$incident->incident_number}."
        );
    }
    public function reverseIncident(Request $request, ModerationIncident $incident): RedirectResponse
    {
        $validated = $request->validate([
            'reversal_reason' => ['required', 'string', 'min:10', 'max:5000'],
        ]);

        $incident = $this->crackdown->reverseIncident(
            incident: $incident,
            adminUser: $request->user(),
            reason: $validated['reversal_reason'],
        );

        return back()->with(
            'status',
            "Incident {$incident->incident_number} reversed."
        );
    }

    public function approveAppeal(Request $request, ModerationAppeal $appeal): RedirectResponse
    {
        $validated = $request->validate([
            'review_note' => ['required', 'string', 'min:10', 'max:5000'],
        ]);

        $appeal->loadMissing('incident');

        $incident = $this->crackdown->reverseIncident(
            incident: $appeal->incident,
            adminUser: $request->user(),
            reason: $validated['review_note'],
            appeal: $appeal,
        );

        return back()->with(
            'status',
            "Appeal approved and incident {$incident->incident_number} reversed."
        );
    }

    public function denyAppeal(Request $request, ModerationAppeal $appeal): RedirectResponse
    {
        $validated = $request->validate([
            'review_note' => ['required', 'string', 'min:10', 'max:5000'],
        ]);

        $appeal = $this->crackdown->denyAppeal(
            appeal: $appeal,
            adminUser: $request->user(),
            reviewNote: $validated['review_note'],
        );

        return back()->with(
            'status',
            "Appeal #{$appeal->id} denied."
        );
    }
    public function deleteUser(Request $request, User $user): RedirectResponse
{
    $validated = $request->validate([
        'delete_reason' => ['required', 'string', 'min:10', 'max:5000'],
    ]);

    $this->crackdown->deleteUser(
        subjectUser: $user,
        adminUser: $request->user(),
        reason: $validated['delete_reason'],
    );

    return redirect()
        ->route('admin.crackdown.index')
        ->with('status', "User #{$user->id} deleted.");
}
}