New CS2 PickLab contact form submission

Name:
{{ $contactMessage->name }}

Email:
{{ $contactMessage->email }}

Subject:
{{ $contactMessage->subject ?: 'No subject provided' }}

Message:
{{ $contactMessage->message }}

Submitted:
{{ $contactMessage->created_at?->toDateTimeString() }}

IP Address:
{{ $contactMessage->ip_address ?: 'Unknown' }}

User Agent:
{{ $contactMessage->user_agent ?: 'Unknown' }}