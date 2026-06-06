@extends('layouts.admin', [
    'title' => 'Content Gates | CS2 PickLab',
    'pageTitle' => 'Content Gates',
])

@section('content')
<div class="page-header">
    <div>
        <h2 class="page-title">Content Gates</h2>
        <p class="page-subtitle">Control which public sections require login or active subscription.</p>
    </div>
</div>

@if(session('success'))
    <div class="mb-6 rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 font-bold text-emerald-200">
        {{ session('success') }}
    </div>
@endif

<div class="space-y-6">
    @foreach($gates as $gate)
        <form method="POST" action="{{ route('admin.content-gates.update', $gate) }}" class="panel space-y-5">
            @csrf
            @method('PUT')

            <div class="grid gap-5 lg:grid-cols-2">
                <div>
                    <label class="form-label">Gate Key</label>
                    <input class="form-input opacity-70" value="{{ $gate->gate_key }}" disabled>
                </div>

                <div>
                    <label class="form-label" for="label_{{ $gate->id }}">Label</label>
                    <input id="label_{{ $gate->id }}" name="label" class="form-input" value="{{ old('label', $gate->label) }}" required>
                </div>

                <div class="lg:col-span-2">
                    <label class="form-label" for="description_{{ $gate->id }}">Description</label>
                    <textarea id="description_{{ $gate->id }}" name="description" rows="2" class="form-input">{{ old('description', $gate->description) }}</textarea>
                </div>

                <div class="lg:col-span-2">
                    <label class="form-label" for="locked_message_{{ $gate->id }}">Locked Message</label>
                    <input id="locked_message_{{ $gate->id }}" name="locked_message" class="form-input" value="{{ old('locked_message', $gate->locked_message) }}">
                </div>
            </div>

            <div class="flex flex-wrap gap-5">
                <label class="inline-flex items-center gap-2 text-sm font-bold text-slate-300">
                    <input type="checkbox" name="is_enabled" value="1" @checked($gate->is_enabled)>
                    Gate Enabled
                </label>

                <label class="inline-flex items-center gap-2 text-sm font-bold text-slate-300">
                    <input type="checkbox" name="requires_login" value="1" @checked($gate->requires_login)>
                    Requires Login
                </label>

                <label class="inline-flex items-center gap-2 text-sm font-bold text-slate-300">
                    <input type="checkbox" name="requires_subscription" value="1" @checked($gate->requires_subscription)>
                    Requires Subscription
                </label>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn-primary">Save Gate</button>
            </div>
        </form>
    @endforeach
</div>
@endsection
