@extends('layouts.app', ['title' => $product->name . ' | CS2 PickLab'])

@section('content')
<section class="mx-auto grid max-w-7xl gap-10 px-4 py-12 lg:grid-cols-2">
    <div>
        <div class="flex h-96 items-center justify-center rounded-2xl border border-slate-800 bg-slate-900 text-slate-600">
            Product Image
        </div>
    </div>

    <div>
        <p class="text-sm font-bold uppercase tracking-widest text-cyan-400">{{ ucfirst($product->product_type) }}</p>
        <h1 class="mt-3 text-4xl font-black text-white">{{ $product->name }}</h1>
        <p class="mt-4 text-slate-300">{{ $product->description }}</p>

        <p class="mt-6 text-3xl font-black text-cyan-300">${{ $product->base_price_dollars }}</p>

        <div class="mt-8 rounded-2xl border border-slate-800 bg-slate-900 p-5">
            <h2 class="text-xl font-black text-white">Customization options</h2>

            <form class="mt-5 space-y-5" method="POST" action="#" enctype="multipart/form-data">
                @csrf

                @foreach($product->options as $option)
                    <div>
                        <label class="mb-2 block font-bold text-white">
                            {{ $option->name }}
                            @if($option->is_required)
                                <span class="text-cyan-400">*</span>
                            @endif
                        </label>

                        @if($option->help_text)
                            <p class="mb-2 text-sm text-slate-400">{{ $option->help_text }}</p>
                        @endif

                        @if(in_array($option->type, ['select', 'radio']))
                            <select name="options[{{ $option->id }}]" class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-white">
                                <option value="">Choose {{ $option->name }}</option>
                                @foreach($option->values as $value)
                                    <option value="{{ $value->id }}">
                                        {{ $value->label }}
                                        @if($value->price_delta > 0)
                                            (+${{ $value->price_delta_dollars }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        @elseif($option->type === 'textarea')
                            <textarea name="options[{{ $option->id }}]" rows="4" class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-white"></textarea>
                        @elseif($option->type === 'file')
                            <input type="file" name="uploads[{{ $option->id }}]" class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-white">
                        @else
                            <input type="{{ $option->type === 'number' ? 'number' : 'text' }}" name="options[{{ $option->id }}]" class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-white">
                        @endif
                    </div>
                @endforeach

                <button type="button" class="w-full rounded-lg bg-cyan-400 px-5 py-3 font-black text-slate-950">
                    Checkout coming next
                </button>
            </form>
        </div>

        <p class="mt-5 text-sm text-slate-500">
            Custom products use original designs only. Do not upload official Counter-Strike, Valve, Steam, tournament, or pro team artwork unless you own the rights.
        </p>
    </div>
</section>
@endsection
