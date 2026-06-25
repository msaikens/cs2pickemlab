@extends('layouts.public', [
    'title' => 'Account Suspended | CS2 PickLab',
    'pageTitle' => 'Account Suspended',
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/account-restricted.css') }}">
@endpush

@section('content')
@php
    $canAppeal = $incident?->canBeAppealed() ?? false;

    $hasPendingAppeal = $incident
        ? $incident->appeals()
            ->where('user_id', auth()->id())
            ->where('status', \App\Models\ModerationAppeal::STATUS_PENDING)
            ->exists()
        : false;
@endphp

<section class="account-restricted-page">
    <article class="account-restricted-card warning">
        <p class="account-restricted-kicker">Account Temporarily Suspended</p>

        <h1>Your account is temporarily suspended.</h1>

        <p>
            This suspension was issued by {{ $adminName }}.
        </p>

        @if($user->site_suspended_until)
            <p>
                Suspension ends:
                <strong>{{ $user->site_suspended_until->format('M j, Y g:i A') }}</strong>
            </p>
        @endif

        <p>
            If you feel this is an error, contact
            <a href="mailto:support@cs2picklabs.com">support@cs2picklabs.com</a>
            with your incident number
            <strong>{{ $incidentNumber }}</strong>.
        </p>

        @if($incident?->user_message)
            <div class="account-restricted-message">
                <strong>Message from CS2 PickLab</strong>
                <p>{!! nl2br(e($incident->user_message)) !!}</p>
            </div>
        @endif

        @if(session('status'))
            <div class="account-restricted-alert success">
                {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="account-restricted-alert danger">
                <strong>Appeal could not be submitted.</strong>

                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($canAppeal)
            <div class="account-restricted-message">
                <strong>Appeal this suspension</strong>

                @if($hasPendingAppeal)
                    <p>Your appeal is pending review.</p>
                @else
                    <p>
                        You may submit one appeal explaining why you believe this suspension should be reviewed.
                    </p>

                    <form method="POST" action="{{ route('account.moderation-appeals.store', $incident) }}">
                        @csrf

                        <label for="suspension-appeal-message">
                            Appeal message
                        </label>

                        <textarea
                            id="suspension-appeal-message"
                            name="message"
                            required
                            minlength="20"
                            maxlength="5000"
                            placeholder="Explain why you believe this suspension should be reviewed."
                        >{{ old('message') }}</textarea>

                        <button type="submit">
                            Submit Appeal
                        </button>
                    </form>
                @endif
            </div>
        @endif

        <form method="POST" action="{{ route('logout') }}" class="account-restricted-actions">
            @csrf

            <button type="submit">
                Log Out
            </button>
        </form>
    </article>
</section>
@endsection