<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\UserInboxMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InboxController extends Controller
{
    public function index(Request $request): View
    {
        $messages = $request->user()
            ->inboxMessages()
            ->with('moderationIncident')
            ->latest()
            ->paginate(20);

        return view('account.inbox.index', [
            'messages' => $messages,
        ]);
    }

    public function markRead(Request $request, UserInboxMessage $message): RedirectResponse
    {
        abort_unless($message->user_id === $request->user()->id, 403);

        $message->markRead();

        return back()->with('status', 'Message marked as read.');
    }
}