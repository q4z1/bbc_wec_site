@extends('layouts.app')

@section('content')
<div class="auth-page">
    <div class="auth-card">
        <div class="auth-card__header">{{ __('Reset Password') }}</div>
        <div class="auth-card__body">
            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="auth-form-item">
                    <label for="email">{{ __('E-Mail Address') }}</label>
                    <input id="email" class="bbc-input{{ $errors->has('email') ? ' is-error' : '' }}"
                        name="email" type="email"
                        value="{{ $email ?? old('email') }}"
                        required autocomplete="email" autofocus>
                    @error('email')
                        <div class="auth-form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="auth-form-item">
                    <label for="password">{{ __('Password') }}</label>
                    <input id="password" class="bbc-input{{ $errors->has('password') ? ' is-error' : '' }}"
                        name="password" type="password"
                        required autocomplete="new-password">
                    @error('password')
                        <div class="auth-form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="auth-form-item">
                    <label for="password-confirm">{{ __('Confirm Password') }}</label>
                    <input id="password-confirm" class="bbc-input"
                        name="password_confirmation" type="password"
                        required autocomplete="new-password">
                </div>

                <div class="auth-form-actions">
                    <button type="submit" class="bbc-btn bbc-btn--primary">{{ __('Reset Password') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

