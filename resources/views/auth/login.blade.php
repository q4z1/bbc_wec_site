@extends('layouts.app')

@section('content')
<div class="auth-page">
    <div class="auth-card">
        <div class="auth-card__header">{{ __('Login') }}</div>
        <div class="auth-card__body">
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="auth-form-item">
                    <label for="login">{{ __('Username or Email') }}</label>
                    <input id="login" class="bbc-input{{ ($errors->has('username') || $errors->has('email')) ? ' is-error' : '' }}"
                        name="login" type="text"
                        value="{{ old('username') ?: old('email') }}"
                        required autofocus autocomplete="username">
                    @if ($errors->has('username') || $errors->has('email'))
                        <div class="auth-form-error">{{ $errors->first('username') ?: $errors->first('email') }}</div>
                    @endif
                </div>

                <div class="auth-form-item">
                    <label for="password">{{ __('Password') }}</label>
                    <input id="password" class="bbc-input{{ $errors->has('password') ? ' is-error' : '' }}"
                        name="password" type="password"
                        required autocomplete="current-password">
                    @error('password')
                        <div class="auth-form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="auth-form-item auth-form-item--inline">
                    <label>
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        {{ __('Remember Me') }}
                    </label>
                </div>

                <div class="auth-form-actions">
                    <button type="submit" class="bbc-btn bbc-btn--primary">{{ __('Login') }}</button>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}">{{ __('Forgot Your Password?') }}</a>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

