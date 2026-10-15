<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#24105f">
    <title>Forgot Password - EduSphere</title>
    <style>
        * { box-sizing: border-box; }
        html, body { min-height: 100%; margin: 0; }
        body { font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; color: #1e1b3a; background: #fff; }
        a { color: #5b2ee0; font-weight: 650; text-decoration: none; }
        a:hover { text-decoration: underline; }
        button, input { font: inherit; }
        .login-container { display: flex; min-height: 100vh; }

        /* Lightweight shared brand panel */
        .login-brand-panel {
            position: relative; isolation: isolate; flex: 0 0 58%; min-width: 0;
            display: flex; flex-direction: column; overflow: hidden; padding: 34px 42px;
            color: #fff; background: linear-gradient(135deg, #24105f 0%, #3a168f 52%, #24105f 100%);
        }
        .login-brand-panel::before, .login-brand-panel::after {
            content: ""; position: absolute; z-index: -1; pointer-events: none; border-radius: 50%;
        }
        .login-brand-panel::before { width: 330px; height: 330px; right: -150px; top: -150px; background: rgba(255,176,0,.12); }
        .login-brand-panel::after { width: 280px; height: 280px; left: -150px; bottom: -160px; background: rgba(126,82,255,.18); }
        .brand-logo { display: flex; align-items: center; gap: 13px; position: relative; z-index: 2; }
        .brand-logo-icon { width: 48px; height: 48px; flex: 0 0 48px; display: grid; place-items: center; border-radius: 13px; background: #fff; }
        .brand-logo h1 { margin: 0; font-size: 25px; line-height: 1.1; font-weight: 800; letter-spacing: -.5px; }
        .brand-logo h1 span { color: #ffb020; }
        .brand-logo p { margin: 5px 0 0; font-size: 12px; opacity: .86; }
        .auth-back-website { position: relative; z-index: 3; margin: 22px 0 0; }
        .auth-back-website a { display: inline-flex; align-items: center; gap: 8px; padding: 9px 13px; border: 1px solid rgba(255,255,255,.35); border-radius: 10px; color: #fff; background: rgba(255,255,255,.08); font-size: 13px; font-weight: 650; transition: background .15s ease; }
        .auth-back-website a:hover { background: rgba(255,255,255,.16); text-decoration: none; }
        .brand-body { display: flex; flex: 1; align-items: center; gap: 22px; position: relative; z-index: 2; padding: 24px 0 10px; }
        .brand-text { flex: 1 1 52%; min-width: 0; }
        .brand-small-title { margin: 0 0 5px; font-size: 17px; font-weight: 650; }
        .brand-heading h2 { margin: 0 0 12px; color: #ffb020; font-size: clamp(30px, 3vw, 42px); line-height: 1.08; letter-spacing: -.8px; }
        .brand-description { max-width: 360px; margin: 0; color: rgba(255,255,255,.88); font-size: 14px; line-height: 1.7; }
        .brand-features { display: grid; gap: 13px; margin-top: 25px; }
        .feature-item { display: flex; align-items: center; gap: 12px; }
        .feature-icon { width: 40px; height: 40px; flex: 0 0 40px; display: grid; place-items: center; border-radius: 11px; background: #5b2ee0; }
        .feature-item h3 { margin: 0; font-size: 13px; font-weight: 700; }
        .feature-item p { margin: 3px 0 0; font-size: 11px; opacity: .78; }
        .brand-illustration { flex: 1 1 48%; min-width: 0; display: flex; align-items: center; justify-content: center; }
        .brand-illustration img { display: block; width: 100%; max-width: 430px; height: auto; object-fit: contain; border-radius: 16px; }
        .float-badge, .corner-badge-purple, .corner-badge-orange, .brand-decoration, .dots, .curve { display: none !important; }
        .journey-text { position: relative; z-index: 2; margin-top: auto; font-size: 18px; line-height: 1.4; color: rgba(255,255,255,.88); }
        .journey-text strong { color: #ffb020; }

        /* Form panel */
        .login-form-panel { flex: 1 1 42%; min-width: 0; display: flex; align-items: center; justify-content: center; padding: 38px clamp(22px, 4vw, 56px); background: #fff; }
        .login-form-wrapper { width: 100%; max-width: 410px; }
        .welcome-text { display: inline-block; margin-bottom: 7px; color: #d58b00; font-size: 13px; font-weight: 750; }
        .login-heading h2 { margin: 0 0 8px; color: #24105f; font-size: clamp(25px, 2.5vw, 31px); line-height: 1.2; letter-spacing: -.6px; }
        .login-heading p { margin: 0 0 24px; color: #6b6a80; font-size: 14px; line-height: 1.65; }
        .auth-alert { display: none; gap: 10px; align-items: flex-start; margin-bottom: 16px; padding: 12px 14px; border-radius: 10px; font-size: 13px; line-height: 1.5; }
        .auth-alert.show { display: flex; }
        .auth-alert-error { color: #a61b1f; background: #fff0f0; border: 1px solid #f8caca; }
        .auth-alert-success { color: #12632f; background: #edfbf1; border: 1px solid #bfe9cf; }
        .form-group { margin-bottom: 17px; }
        .form-group label { display: block; margin-bottom: 7px; color: #33304b; font-size: 13px; font-weight: 650; }
        .input-wrapper { position: relative; }
        .input-icon { position: absolute; top: 50%; left: 14px; display: flex; color: #9290a6; transform: translateY(-50%); pointer-events: none; }
        .input-wrapper input { width: 100%; height: 48px; padding: 0 14px 0 44px; border: 1px solid #e3e0ef; border-radius: 11px; outline: none; color: #1e1b3a; background: #fafaff; font-size: 14px; }
        .input-wrapper input:focus { border-color: #6b3ce8; background: #fff; box-shadow: 0 0 0 3px rgba(107,60,232,.1); }
        .input-wrapper input.invalid { border-color: #e5484d; }
        .field-error { display: block; min-height: 0; margin-top: 5px; color: #e5484d; font-size: 12px; }
        .btn { width: 100%; min-height: 48px; display: flex; align-items: center; justify-content: center; gap: 10px; border: 0; border-radius: 11px; cursor: pointer; font-size: 14px; font-weight: 700; }
        .btn-primary { color: #fff; background: #5b2ee0; box-shadow: 0 5px 12px rgba(75,32,196,.15); }
        .btn-primary:hover:not(:disabled) { background: #4b20c4; }
        .btn-primary:focus-visible { outline: 3px solid rgba(107,60,232,.3); outline-offset: 2px; }
        .btn-primary:disabled { opacity: .7; cursor: wait; }
        .spinner { display: none; width: 17px; height: 17px; border: 2px solid rgba(255,255,255,.45); border-top-color: #fff; border-radius: 50%; }
        .loading .spinner { display: inline-block; animation: spin .7s linear infinite; }
        .loading .btn-arrow { display: none; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .register-text { margin: 17px 0 0; color: #6b6a80; text-align: center; font-size: 13px; }
        .register-text + .register-text { margin-top: 10px; }

        @media (max-width: 1100px) {
            .login-brand-panel { flex-basis: 50%; padding: 30px; }
            .brand-illustration { display: none; }
            .brand-text { flex: 1; }
            .login-form-panel { padding: 32px 28px; }
        }
        @media (max-width: 760px) {
            .login-container { flex-direction: column; }
            .login-brand-panel { flex: none; padding: 24px 22px; }
            .brand-logo-icon { width: 42px; height: 42px; flex-basis: 42px; }
            .brand-logo h1 { font-size: 22px; }
            .auth-back-website { margin-top: 16px; }
            .brand-body { padding: 24px 0 12px; }
            .brand-small-title { font-size: 15px; }
            .brand-heading h2 { font-size: 29px; margin-bottom: 0; }
            .brand-description, .brand-features, .journey-text { display: none; }
            .login-form-panel { flex: 1; padding: 34px 22px 42px; }
            .login-form-wrapper { max-width: 460px; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { scroll-behavior: auto !important; animation-duration: .01ms !important; animation-iteration-count: 1 !important; transition-duration: .01ms !important; }
        }
    </style>
</head>
<body>
<div class="login-container">
    @include('auth.partials.brand-panel')

    <section class="login-form-panel" aria-labelledby="forgotTitle">
        <div class="login-form-wrapper">
            <div class="login-heading">
                <span class="welcome-text">Forgot Password</span>
                <h2 id="forgotTitle">Reset Your Password</h2>
                <p>Enter your email address and we'll send you a link to reset your password.</p>
            </div>

            <div class="auth-alert auth-alert-error" id="alertError" role="alert" aria-live="assertive"></div>
            <div class="auth-alert auth-alert-success" id="alertSuccess" role="status" aria-live="polite"></div>

            <form id="forgotForm" novalidate>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-wrapper">
                        <span class="input-icon" aria-hidden="true">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="5" width="18" height="14" rx="2"/>
                                <path d="m3 7 9 6 9-6"/>
                            </svg>
                        </span>
                        <input type="email" id="email" name="email" placeholder="name@example.com" autocomplete="email" required>
                    </div>
                    <span class="field-error" id="emailError"></span>
                </div>

                <button type="submit" class="btn btn-primary" id="forgotBtn">
                    <span class="spinner" aria-hidden="true"></span>
                    <span id="forgotBtnText">Send Reset Link</span>
                    <svg class="btn-arrow" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                        <path d="M5 12h14M13 6l6 6-6 6"/>
                    </svg>
                </button>
            </form>

            <p class="register-text">Remember your password? <a href="{{ route('login') }}">Back to Login</a></p>
            <p class="register-text">Don't have an account? <a href="{{ route('register') }}">Register</a></p>
        </div>
    </section>
</div>

<script>
(() => {
    'use strict';

    const FORGOT_URL = @json(url('/api/v1/auth/password/forgot'));
    const form = document.getElementById('forgotForm');
    const email = document.getElementById('email');
    const btn = document.getElementById('forgotBtn');
    const btnText = document.getElementById('forgotBtnText');
    const alertError = document.getElementById('alertError');
    const alertSuccess = document.getElementById('alertSuccess');
    const emailError = document.getElementById('emailError');

    function clearMessages() {
        [alertError, alertSuccess].forEach(el => {
            el.classList.remove('show');
            el.textContent = '';
        });
        emailError.textContent = '';
        email.classList.remove('invalid');
    }

    function showMessage(element, message) {
        element.textContent = message;
        element.classList.add('show');
    }

    function setLoading(loading) {
        btn.disabled = loading;
        btn.classList.toggle('loading', loading);
        btnText.textContent = loading ? 'Sending...' : 'Send Reset Link';
    }

    form.addEventListener('submit', async event => {
        event.preventDefault();
        clearMessages();

        const emailValue = email.value.trim();
        if (!emailValue) {
            email.classList.add('invalid');
            emailError.textContent = 'Email is required.';
            email.focus();
            return;
        }
        if (!email.validity.valid) {
            email.classList.add('invalid');
            emailError.textContent = 'Enter a valid email address.';
            email.focus();
            return;
        }

        setLoading(true);
        try {
            const response = await fetch(FORGOT_URL, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
                body: JSON.stringify({ email: emailValue })
            });

            let data = {};
            try { data = await response.json(); } catch (_) { /* Non-JSON API response */ }

            if (response.ok && data.success !== false) {
                showMessage(
                    alertSuccess,
                    data.message || 'If this email is registered, a password reset link has been sent.'
                );

                // Stop the spinner; keep the success label briefly, then restore the button.
                btn.classList.remove('loading');
                btnText.textContent = 'Link Sent';
                btn.disabled = true;

                window.setTimeout(() => {
                    btnText.textContent = 'Send Reset Link';
                    btn.disabled = false;
                }, 2000);
                return;
            }

            if (response.status === 422) {
                if (data.errors && data.errors.email) {
                    email.classList.add('invalid');
                    emailError.textContent = [].concat(data.errors.email)[0];
                }
                showMessage(alertError, data.message || 'Please enter a valid email address.');
            } else if (response.status === 429) {
                showMessage(alertError, data.message || 'Too many attempts. Please try again later.');
            } else if (response.status >= 500) {
                showMessage(alertError, 'Something went wrong on our side. Please try again.');
            } else {
                showMessage(alertError, data.message || 'Unable to send reset link. Please try again.');
            }
        } catch (_) {
            showMessage(alertError, 'Network error. Please check your connection and try again.');
        } finally {
            // Do not leave the spinner active after either success or failure.
            btn.classList.remove('loading');
            if (!btn.disabled || !alertSuccess.classList.contains('show')) {
                btn.disabled = false;
                if (!alertSuccess.classList.contains('show')) {
                    btnText.textContent = 'Send Reset Link';
                }
            }
        }
    });
})();
</script>
</body>
</html>
