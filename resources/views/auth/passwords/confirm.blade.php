@extends('layouts.app')

@section('content')
<div class="auth-page">
    <div class="auth-card">
        <div class="auth-card__header">{{ __('Confirm Password') }}</div>
        <div class="auth-card__body">
            <p>{{ __('Please confirm your password before continuing.') }}</p>

            <form method="POST" action="{{ route('password.confirm') }}">
                @csrf

                <div class="auth-form-item">
                    <label for="password">{{ __('Password') }}</label>
                    <input id="password" class="bbc-input{{ $errors->has('password') ? ' is-error' : '' }}"
                        name="password" type="password"
                        required autocomplete="current-password">
                    @error('password')
                        <div class="auth-form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="auth-form-actions">
                    <button type="submit" class="bbc-btn bbc-btn--primary">{{ __('Confirm Password') }}</button>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}">{{ __('Forgot Your Password?') }}</a>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

