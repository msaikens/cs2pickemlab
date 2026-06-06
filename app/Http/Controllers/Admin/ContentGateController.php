<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentGate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContentGateController extends Controller
{
    public function index(): View
    {
        $gates = ContentGate::query()
            ->orderBy('label')
            ->get();

        return view('admin.content-gates.index', compact('gates'));
    }

    public function update(Request $request, ContentGate $contentGate): RedirectResponse
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'locked_message' => ['nullable', 'string', 'max:255'],
        ]);

        $contentGate->update([
            'label' => $data['label'],
            'description' => $data['description'] ?? null,
            'locked_message' => $data['locked_message'] ?? null,
            'is_enabled' => $request->boolean('is_enabled'),
            'requires_login' => $request->boolean('requires_login'),
            'requires_subscription' => $request->boolean('requires_subscription'),
        ]);

        return redirect()
            ->route('admin.content-gates.index')
            ->with('success', 'Content gate updated.');
    }
}
