@extends('layouts.app')

@section('content')

<h2>
  {{ __('Reset Password') }}
</h2>

@if (session('status'))
  <div>
    {{ session('status') }}
  </div>
@endif

<form
  action="{{ route('password.request') }}"
  method="POST"
>
  @csrf

  <input type="hidden" name="token" value="{{ $token }}">

  <div>
    <label for="email">
      {{ __('Email Address') }}
    </label>
    <input
      type="email"
      name="email"
      id="email"
      placeholder="max@mustermann.at"
      value="{{ old('email') }}"
      required
    >
    @if ($errors->has('email'))
      <span>
        {{ $errors->first('email') }}
      </span>
    @endif
  </div>

  <div>
    <label for="password">
      {{ __('Password') }}
    </label>
    <input
      type="password"
      name="password"
      id="password"
      placeholder="********"
      required
    >
    @if ($errors->has('password'))
      <span>
        {{ $errors->first('password') }}
      </span>
    @endif
  </div>

  <div>
    <label for="password_confirmation">
      {{ __('Password Confirmation') }}
    </label>
    <input
      type="password"
      name="password_confirmation"
      id="password_confirmation"
      placeholder="********"
      required
    >
    @if ($errors->has('password_confirmation'))
      <span>
        {{ $errors->first('password_confirmation') }}
      </span>
    @endif
  </div>

  <div>
    <button type="submit">
      {{ __('Reset Password') }}
    </button>
  </div>
</form>

@endsection
