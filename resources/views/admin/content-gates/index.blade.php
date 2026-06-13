@extends('layouts.admin', [
    'title' => 'Content Gates | CS2 PickLab',
    'pageTitle' => 'Content Gates',
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/admin-resource.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/content-gates.css') }}">
@endpush

@section('content')
<section class="admin-resource-page content-gates-page">
    <header class="admin-resource-header">
        <div>
            <p class="admin-resource-kicker">Access Control</p>
            <h2>Content Gates</h2>
            <p>Control which public sections require login or active subscription.</p>
        </div>
    </header>

    @if(session('success'))
        <div class="admin-resource-alert success">
            {{ session('success') }}
        </div>
    @endif

    <div class="content-gate-list">
        @foreach($gates as $gate)
            <form method="POST" action="{{ route('admin.content-gates.update', $gate) }}" class="admin-panel content-gate-form">
                @csrf
                @method('PUT')

                <div class="admin-form-grid two">
                    <div class="admin-field">
                        <label>Gate Key</label>

                        <input
                            value="{{ $gate->gate_key }}"
                            disabled
                            class="is-disabled"
                        >
                    </div>

                    <div class="admin-field">
                        <label for="label_{{ $gate->id }}">Label</label>

                        <input
                            id="label_{{ $gate->id }}"
                            name="label"
                            value="{{ old('label', $gate->label) }}"
                            required
                        >
                    </div>

                    <div class="admin-field full">
                        <label for="description_{{ $gate->id }}">Description</label>

                        <textarea
                            id="description_{{ $gate->id }}"
                            name="description"
                            rows="2"
                        >{{ old('description', $gate->description) }}</textarea>
                    </div>

                    <div class="admin-field full">
                        <label for="locked_message_{{ $gate->id }}">Locked Message</label>

                        <input
                            id="locked_message_{{ $gate->id }}"
                            name="locked_message"
                            value="{{ old('locked_message', $gate->locked_message) }}"
                        >
                    </div>
                </div>

                <div class="content-gate-options">
                    <label class="admin-check">
                        <input type="checkbox" name="is_enabled" value="1" @checked($gate->is_enabled)>
                        <span>Gate Enabled</span>
                    </label>

                    <label class="admin-check">
                        <input type="checkbox" name="requires_login" value="1" @checked($gate->requires_login)>
                        <span>Requires Login</span>
                    </label>

                    <label class="admin-check">
                        <input type="checkbox" name="requires_subscription" value="1" @checked($gate->requires_subscription)>
                        <span>Requires Subscription</span>
                    </label>
                </div>

                <div class="admin-form-actions">
                    <button type="submit" class="admin-button primary">
                        Save Gate
                    </button>
                </div>
            </form>
        @endforeach
    </div>
</section>
@endsection