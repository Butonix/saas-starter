@extends('layouts.app')

@section('content')

<h2>
  {{ __('Account Setup') }}
</h2>

<form
  action="{{ route('setup') }}"
  method="POST"
>
  @csrf

  <div>
    <label for="subdomain">
      {{ __('Subdomain') }}
    </label>
    <input
      type="text"
      name="subdomain"
      id="subdomain"
      placeholder="{{ __('Your subdomain') }}"
      value="{{ old('subdomain') }}"
      required
      autofocus
    >
    @if ($errors->has('subdomain'))
      <span>
        {{ $errors->first('subdomain') }}
      </span>
    @endif
  </div>

  <div>
    <button type="submit">
      {{ __('Set Up') }}
    </button>
  </div>
</form>

@endsection
