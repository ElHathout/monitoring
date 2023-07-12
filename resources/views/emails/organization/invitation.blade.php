@component('mail::message')
Dear,

You were invited to join the organization **{{ $organization->name  }}**.

@component('mail::button', ['url' => route("dashboard")])
Login
@endcomponent

Best regards,

@endcomponent