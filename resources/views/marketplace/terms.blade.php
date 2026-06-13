@extends('layouts.app')

@section('title', 'Marketplace Terms')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/marketplace-terms.css') }}">
@endpush

@section('content')
<section style="max-width: 900px; margin: 40px auto; padding: 24px;">
    <h1>Marketplace Terms</h1>

    <p>
        Before using the marketplace, you must agree to trade responsibly, verify item details,
        and understand that Steam trades are completed through Steam.
    </p>

    <ul>
        <li>You are responsible for verifying trade offers before accepting them.</li>
        <li>You must not list items you do not own or cannot trade.</li>
        <li>You must not impersonate another Steam user.</li>
        <li>You understand trade holds or restrictions may apply through Steam.</li>
        <li>You agree not to use the marketplace for fraud, gambling, laundering, or prohibited activity.</li>
    </ul>

    <form method="POST" action="{{ route('marketplace.terms.accept') }}">
        @csrf

        <button type="submit">
            I Accept Marketplace Terms
        </button>
    </form>
</section>
@endsection