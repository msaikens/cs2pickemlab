@extends('layouts.public', [
    'title' => 'Inbox | CS2 PickLab',
    'pageTitle' => 'Inbox',
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/account-inbox.css') }}">
@endpush

@section('content')
<section class="account-inbox-page">
    <header class="account-inbox-hero">
        <div>
            <p class="account-inbox-kicker">Account Inbox</p>
            <h1>Inbox</h1>
            <p>Important account notices, marketplace updates, and moderation messages.</p>
        </div>

        <a href="{{ route('account.show') }}" class="account-inbox-button secondary">
            Back to Account
        </a>
    </header>

    @if(session('status'))
        <div class="account-inbox-alert success">
            {{ session('status') }}
        </div>
    @endif

    @if(session('warning'))
        <div class="account-inbox-alert warning">
            {{ session('warning') }}
        </div>
    @endif

    @if(session('error'))
        <div class="account-inbox-alert danger">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="account-inbox-alert danger">
            <strong>Something needs attention.</strong>

            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="account-inbox-list">
        @forelse($messages as $message)
            @php
                $incident = $message->moderationIncident;
                $canAppeal = $incident?->canBeAppealed() ?? false;

                $hasPendingAppeal = $incident
                    ? $incident->appeals()
                        ->where('user_id', auth()->id())
                        ->where('status', \App\Models\ModerationAppeal::STATUS_PENDING)
                        ->exists()
                    : false;
            @endphp

            <article class="account-inbox-message {{ $message->read_at ? 'read' : 'unread' }}">
                <div class="account-inbox-message-main">
                    <div class="account-inbox-message-title-row">
                        <h2>{{ $message->title }}</h2>

                        @unless($message->read_at)
                            <span class="account-inbox-badge unread">Unread</span>
                        @endunless

                        @if($incident?->isReversed())
                            <span class="account-inbox-badge reversed">Reversed</span>
                        @endif
                    </div>

                    <div class="account-inbox-message-body">
                        {!! nl2br(e($message->body)) !!}
                    </div>

                    <footer class="account-inbox-message-footer">
                        <span>{{ $message->created_at?->format('M j, Y g:i A') }}</span>

                        @if($incident?->incident_number)
                            <span>Incident: {{ $incident->incident_number }}</span>
                        @endif

                        @if($message->read_at)
                            <span>Read: {{ $message->read_at->format('M j, Y g:i A') }}</span>
                        @endif
                    </footer>

                    @if($canAppeal)
                        @if($hasPendingAppeal)
                            <div class="account-inbox-appeal-status">
                                Appeal pending review.
                            </div>
                        @else
                            <details class="account-inbox-appeal">
                                <summary>Appeal this action</summary>

                                <form method="POST" action="{{ route('account.moderation-appeals.store', $incident) }}">
                                    @csrf

                                    <label for="appeal-message-{{ $message->id }}">
                                        Appeal message
                                    </label>

                                    <textarea
                                        id="appeal-message-{{ $message->id }}"
                                        name="message"
                                        required
                                        minlength="20"
                                        maxlength="5000"
                                        placeholder="Explain why you believe this moderation action should be reviewed."
                                    >{{ old('message') }}</textarea>

                                    <button type="submit">
                                        Submit Appeal
                                    </button>
                                </form>
                            </details>
                        @endif
                    @endif
                </div>

                @unless($message->read_at)
                    <form method="POST" action="{{ route('account.inbox.read', $message) }}" class="account-inbox-read-form">
                        @csrf

                        <button type="submit">
                            Mark Read
                        </button>
                    </form>
                @endunless
            </article>
        @empty
            <div class="account-inbox-empty">
                No inbox messages yet.
            </div>
        @endforelse
    </section>

    @if($messages->hasPages())
        <div class="account-inbox-pagination">
            {{ $messages->links() }}
        </div>
    @endif
</section>
@endsection