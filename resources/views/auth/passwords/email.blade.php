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
  action="{{ route('password.email') }}"
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
    <button type="submit">
      {{ __('Send Password Reset Link') }}
    </button>
  </div>
</form>

@endsection
