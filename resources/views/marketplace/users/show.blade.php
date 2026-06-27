@extends('layouts.public', [
    'title' => $profileUser->publicDisplayName($viewer) . ' | CS2 PickLab',
    'pageTitle' => 'Marketplace Profile',
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/marketplace-profile.css') }}">
@endpush

@section('content')
<section class="marketplace-profile-page">
    <header class="marketplace-profile-hero">
        <div>
            <p class="marketplace-profile-kicker">Marketplace Profile</p>

            <h1>{{ $profileUser->publicDisplayName($viewer) }}</h1>

            @if($profileUser->canShowRealNameTo($viewer))
                <p>Real name visible.</p>
            @else
                <p>This user keeps their real name private.</p>
            @endif
        </div>

        <div class="marketplace-profile-rating-summary">
            <strong>
                {{ $averageRating ? number_format($averageRating, 1) : '—' }}
            </strong>

            <span>
                {{ $ratingCount }} rating{{ $ratingCount === 1 ? '' : 's' }}
            </span>
        </div>
    </header>

    <section class="marketplace-profile-card">
        <h2>Trade Reputation</h2>

        @forelse($ratings as $rating)
            <article class="marketplace-profile-rating">
                <div>
                    <strong>
                        {{ str_repeat('★', $rating->rating) }}{{ str_repeat('☆', 5 - $rating->rating) }}
                    </strong>

                    <p>
                        Rated by {{ $rating->rater?->publicDisplayName($viewer) ?? 'A marketplace user' }}
                    </p>
                </div>

                @if($rating->comment)
                    <p>{!! nl2br(e($rating->comment)) !!}</p>
                @endif
            </article>
        @empty
            <p>No marketplace ratings yet.</p>
        @endforelse
    </section>
</section>
@endsection