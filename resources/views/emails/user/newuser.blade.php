@extends('emails.template')

@section('content')
    <p>Welcome to DOMIL</p>

    <p>
        You're one step away from making money.
        Click <a href="http://{{ request()->server('HTTP_HOST') }}/api/user/verify/email?email={{ $user->email }}&activation_code={{ $user->email_verification_code }}">here</a>
        to verify your email address.
    </p>
@endsection
