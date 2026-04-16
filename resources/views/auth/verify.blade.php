@extends('layouts.app')

@section('content')
<div class="auth-page">
    <div class="auth-card">
        <div class="auth-card__header">{{ __('Verify Your Email Address') }}</div>
        <div class="auth-card__body">
            @if (session('resent'))
                <el-alert type="success" :closable="false" style="margin-bottom:1rem;">
                    {{ __('A fresh verification link has been sent to your email address.') }}
                </el-alert>
            @endif

            <p>
                {{ __('Before proceeding, please check your email for a verification link.') }}
                {{ __('If you did not receive the email') }},
                <form style="display:inline;" method="POST" action="{{ route('verification.resend') }}">
                    @csrf
                    <button type="submit" class="bbc-btn bbc-btn--link">{{ __('click here to request another') }}</button>.
                </form>
            </p>
        </div>
    </div>
</div>
@endsection

