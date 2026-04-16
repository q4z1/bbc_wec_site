@extends('layouts.app')

@section('content')
<div class="auth-page">
    <div class="auth-card">
        <div class="auth-card__header">{{ __('Reset Password') }}</div>
        <div class="auth-card__body">
            @if (session('status'))
                <el-alert type="success" :closable="false" style="margin-bottom:1rem;">
                    {{ session('status') }}
                </el-alert>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="auth-form-item">
                    <label for="email">{{ __('E-Mail Address') }}</label>
                    <input id="email" class="bbc-input{{ $errors->has('email') ? ' is-error' : '' }}"
                        name="email" type="email"
                        value="{{ old('email') }}"
                        required autocomplete="email" autofocus>
                    @error('email')
                        <div class="auth-form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="auth-form-actions">
                    <button type="submit" class="bbc-btn bbc-btn--primary">{{ __('Send Password Reset Link') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

