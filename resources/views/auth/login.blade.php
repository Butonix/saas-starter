@extends('layouts.app')

@section('content')

<h2>
  {{ __('Login') }}
</h2>

<form
  action="{{ route('login') }}"
  method="POST"
>
  @csrf

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
    <label>
      <input
        type="checkbox"
        name="remember"
        {{ old('remember') ? 'checked' : '' }}
      >
      {{ __('Remember Me') }}
    </label>
  </div>

  <div>
    <button type="submit">
      {{ __('Log In') }}
    </button>
  </div>

  <a href="{{ route('password.request') }}">
      {{ __('Forgot Your Password?') }}
  </a>
</form>

@endsection
