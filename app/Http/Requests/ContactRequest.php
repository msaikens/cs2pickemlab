<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'email' => ['required', 'email:rfc,dns', 'max:255'],
            'subject' => ['nullable', 'string', 'max:160'],
            'message' => ['required', 'string', 'min:20', 'max:5000'],

            'website' => ['nullable', 'max:0'],

            // Used for minimum form-fill time.
            'form_started_at' => ['required', 'integer'],

            // Cloudflare Turnstile token.
            'cf-turnstile-response' => [config('services.turnstile.secret_key') ? 'required' : 'nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'website.max' => 'Your message could not be submitted.',
            'message.min' => 'Please include a little more detail in your message.',
            'form_started_at.required' => 'Your message could not be submitted. Please reload the page and try again.',
        ];
    }
}