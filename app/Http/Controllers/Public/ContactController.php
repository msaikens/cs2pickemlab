<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactRequest;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function create(): View
    {
        return view('public.contact', [
            'turnstileSiteKey' => config('services.turnstile.site_key'),
            'formStartedAt' => time(),
        ]);
    }

    public function store(ContactRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $spamReason = $this->detectSpam($request, $validated);

        $contactMessage = ContactMessage::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'subject' => $validated['subject'] ?? null,
            'message' => $validated['message'],
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
            'is_spam' => $spamReason !== null,
            'spam_reason' => $spamReason,
            'sent_at' => null,
        ]);

        if ($spamReason !== null) {
            return back()
                ->withInput($request->except(['website']))
                ->with('status', 'Your message could not be submitted. Please try again.');
        }

        Mail::send('emails.contact-submission', [
            'contactMessage' => $contactMessage,
        ], function ($mail) use ($contactMessage) {
            $subject = $contactMessage->subject ?: 'Contact form submission';

            $mail->to('support@cs2picklabs.com')
                ->from('no-reply@cs2picklabs.com', 'CS2 PickLab')
                ->replyTo($contactMessage->email, $contactMessage->name)
                ->subject('[CS2 PickLab] ' . $subject);
        });

        $contactMessage->forceFill([
            'sent_at' => now(),
        ])->save();

        return redirect()
            ->route('contact.create')
            ->with('status', 'Your message was sent successfully.');
    }

    private function detectSpam(ContactRequest $request, array $validated): ?string
    {
        if (! empty($request->input('website'))) {
            return 'honeypot_filled';
        }

        $startedAt = (int) ($validated['form_started_at'] ?? 0);
        $secondsToSubmit = time() - $startedAt;

        if ($secondsToSubmit < 3) {
            return 'submitted_too_fast';
        }

        $message = strtolower((string) $validated['message']);

        $linkCount = substr_count($message, 'http://') + substr_count($message, 'https://');

        if ($linkCount > 3) {
            return 'too_many_links';
        }

        if ($this->turnstileIsEnabled() && ! $this->turnstileIsValid($request)) {
            return 'turnstile_failed';
        }

        return null;
    }

    private function turnstileIsEnabled(): bool
    {
        return filled(config('services.turnstile.secret_key'));
    }

    private function turnstileIsValid(ContactRequest $request): bool
    {
        $response = $request->input('cf-turnstile-response');

        if (! $response) {
            return false;
        }

        $result = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret' => config('services.turnstile.secret_key'),
            'response' => $response,
            'remoteip' => $request->ip(),
        ]);

        if (! $result->ok()) {
            return false;
        }

        return (bool) $result->json('success');
    }
}