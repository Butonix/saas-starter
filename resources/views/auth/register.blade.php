@extends('layouts.app')

@section('content')

<h2>
  {{ __('Create an account') }}
</h2>

<form
  action="{{ route('register') }}"
  method="POST"
>
  @csrf

  <div>
    <label for="name">
      {{ __('Name') }}
    </label>
    <input
      type="text"
      name="name"
      id="name"
      placeholder="Max Mustermann"
      value="{{ old('name') }}"
      required
      autofocus
    >
    @if ($errors->has('name'))
      <span>
        {{ $errors->first('name') }}
      </span>
    @endif
  </div>

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
    <label>
      <input
        type="checkbox"
        name="terms"
      >
      {{ __('Terms Of Service') }}
    </label>
    @if ($errors->has('terms'))
      <span>
        {{ $errors->first('terms') }}
      </span>
    @endif
  </div>

  <div>
    <button type="submit">
      {{ __('Create Account') }}
    </button>
  </div>
</form>

@endsection
