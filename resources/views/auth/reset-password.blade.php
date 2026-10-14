<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset Password - EduSphere</title>
    <style>
        :root {
            --primary:#5b2ee0;
            --primary-dark:#4720b9;
            --accent:#ffb020;
            --text:#171725;
            --muted:#77778a;
            --border:#e7e7ef;
            --bg:#f7f7fb;
            --danger:#c62828;
            --success:#19733e;
        }
        *{box-sizing:border-box}
        html,body{margin:0;min-height:100%;font-family:Inter,Arial,Helvetica,sans-serif;color:var(--text);background:var(--bg)}
        body{min-height:100vh}
        button,input{font:inherit}
        .login-container{min-height:100vh;display:flex;background:#fff}
        .login-brand-panel{position:relative;overflow:hidden;flex:0 0 53%;min-height:100vh;padding:42px 58px;color:#fff;background:linear-gradient(145deg,#3f1cb0 0%,#5b2ee0 52%,#7448ed 100%)}
        .login-brand-panel:before{content:"";position:absolute;width:520px;height:520px;border-radius:50%;right:-220px;top:-170px;background:rgba(255,255,255,.08)}
        .login-brand-panel:after{content:"";position:absolute;width:420px;height:420px;border-radius:50%;left:-260px;bottom:-230px;background:rgba(255,176,32,.12)}
        .brand-logo{position:relative;z-index:2;display:flex;align-items:center;gap:13px}
        .brand-logo-icon{width:54px;height:54px;border-radius:16px;display:grid;place-items:center;background:#fff;box-shadow:0 14px 35px rgba(0,0,0,.16)}
        .brand-logo h1{margin:0;font-size:28px;line-height:1;font-weight:800}.brand-logo h1 span{color:#ffcf63}.brand-logo p{margin:7px 0 0;font-size:12px;color:rgba(255,255,255,.78)}
        .brand-body{position:relative;z-index:2;display:flex;align-items:center;min-height:calc(100vh - 120px)}
        .brand-text{max-width:610px}
        .brand-small-title{margin:0 0 6px;font-size:20px;font-weight:700;color:#ffd37a}.brand-heading h2{margin:0;font-size:62px;line-height:1.02;font-weight:850}.brand-description{max-width:510px;margin:22px 0 0;color:rgba(255,255,255,.82);font-size:16px;line-height:1.7}
        .brand-features{display:grid;gap:16px;margin-top:34px}.feature-item{display:flex;gap:14px;align-items:center}.feature-icon{width:43px;height:43px;flex:0 0 43px;border-radius:13px;background:rgba(255,255,255,.12);display:grid;place-items:center}.feature-item h3{margin:0 0 3px;font-size:15px}.feature-item p{margin:0;color:rgba(255,255,255,.66);font-size:12px}
        .journey-text{position:absolute;z-index:2;right:45px;bottom:45px;text-align:right;font-size:14px;color:rgba(255,255,255,.65);line-height:1.6}.journey-text strong{font-size:19px;color:#fff}.brand-decoration{position:absolute;border:1px solid rgba(255,255,255,.16);border-radius:50%}.decoration-one{width:260px;height:260px;right:30px;bottom:130px}.decoration-two{width:170px;height:170px;right:75px;bottom:175px}.dots{position:absolute;width:5px;height:5px;border-radius:50%;background:#ffb020;box-shadow:20px 0 #ffb020,40px 0 #ffb020,0 20px #ffb020,20px 20px #ffb020,40px 20px #ffb020,0 40px #ffb020,20px 40px #ffb020,40px 40px #ffb020;opacity:.7}.dots-1{right:100px;top:160px}.dots-2{left:70px;bottom:90px;opacity:.35}.curve{position:absolute;right:30px;bottom:35px;opacity:.5}
        .login-form-panel{flex:1;display:flex;align-items:center;justify-content:center;padding:50px 42px;background:#fff}.login-form-wrapper{width:100%;max-width:510px}.login-heading{margin-bottom:27px}.welcome-text{display:inline-block;margin-bottom:7px;color:var(--primary);font-size:13px;font-weight:800;text-transform:uppercase;letter-spacing:1.4px}.login-heading h2{margin:0;font-size:34px;line-height:1.2}.login-heading p{margin:10px 0 0;color:var(--muted);font-size:14px;line-height:1.6}
        .auth-alert{display:none;align-items:flex-start;gap:10px;padding:13px 14px;border-radius:12px;margin:0 0 18px;font-size:13px;line-height:1.5}.auth-alert.show{display:flex}.auth-alert-error{background:#fff1f1;border:1px solid #ffd2d2;color:var(--danger)}.auth-alert-success{background:#effaf3;border:1px solid #c8ebd5;color:var(--success)}
        .form-group{margin-bottom:18px}.form-group label{display:block;margin-bottom:8px;font-size:13px;font-weight:700}.input-wrapper{position:relative}.input-wrapper input{width:100%;height:52px;border:1px solid var(--border);border-radius:12px;background:#fff;padding:0 48px 0 45px;outline:none;color:var(--text);transition:.2s}.input-wrapper input:focus{border-color:#8060e9;box-shadow:0 0 0 4px rgba(91,46,224,.09)}.input-wrapper input.invalid{border-color:#e04444;box-shadow:0 0 0 4px rgba(224,68,68,.07)}.input-icon{position:absolute;left:15px;top:50%;transform:translateY(-50%);color:#9696a8;display:flex;pointer-events:none}.toggle-password{position:absolute;right:10px;top:50%;transform:translateY(-50%);border:0;background:transparent;width:36px;height:36px;border-radius:9px;color:#858596;cursor:pointer;display:grid;place-items:center}.toggle-password:hover{background:#f3f1fb;color:var(--primary)}.field-error{display:block;min-height:16px;margin-top:5px;color:#d13232;font-size:12px}
        .btn{width:100%;height:52px;border:0;border-radius:12px;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:10px}.btn-primary{color:#fff;background:linear-gradient(135deg,#5b2ee0,#7047e8);font-weight:800;box-shadow:0 12px 25px rgba(91,46,224,.22);transition:.2s}.btn-primary:hover:not(:disabled){transform:translateY(-1px);box-shadow:0 15px 30px rgba(91,46,224,.28)}.btn-primary:disabled{opacity:.7;cursor:not-allowed;transform:none}.spinner{display:none;width:18px;height:18px;border:2px solid rgba(255,255,255,.35);border-top-color:#fff;border-radius:50%;animation:spin .7s linear infinite}.loading .spinner{display:block}.loading .btn-arrow{display:none}@keyframes spin{to{transform:rotate(360deg)}}
        .register-text{text-align:center;margin:23px 0 0;color:var(--muted);font-size:13px}.register-text a{color:var(--primary);font-weight:800;text-decoration:none}.register-text a:hover{text-decoration:underline}
        .password-hint{margin:-4px 0 16px;color:#9292a2;font-size:11px;line-height:1.5}
        @media(max-width:900px){.login-container{flex-direction:column}.login-brand-panel{min-height:auto;flex:none;padding:30px 25px}.brand-body{min-height:auto;padding:45px 0 25px}.brand-heading h2{font-size:44px}.brand-description,.brand-features,.journey-text,.dots,.curve,.brand-decoration{display:none}.login-form-panel{padding:38px 22px 50px}.brand-logo h1{font-size:24px}}
        @media(max-width:500px){.login-heading h2{font-size:28px}.brand-heading h2{font-size:38px}.login-brand-panel{padding:25px 20px}.login-form-panel{padding:30px 18px 42px}}
    </style>
</head>
<body>
<div class="login-container">
    <section class="login-brand-panel" aria-label="EduSphere">
        <div class="brand-logo">
            <div class="brand-logo-icon">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="none">
                    <path d="M12 3 1 9l11 6 9-4.91V17h2V9L12 3z" fill="#5b2ee0"/>
                    <path d="M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z" fill="#ffb020"/>
                </svg>
            </div>
            <div><h1>Edu<span>Sphere</span></h1><p>Learn &bull; Grow &bull; Build Your Future</p></div>
        </div>

        <div class="brand-body">
            <div class="brand-text">
                <div class="brand-heading">
                    <p class="brand-small-title">New Password</p>
                    <h2>Reset<br>Password</h2>
                    <p class="brand-description">Create a strong new password and securely regain access to your EduSphere account.</p>
                </div>
                <div class="brand-features">
                    <div class="feature-item"><div class="feature-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#ffb020" stroke-width="2"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg></div><div><h3>Secure Account</h3><p>Protect your learning account</p></div></div>
                    <div class="feature-item"><div class="feature-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#ffb020" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg></div><div><h3>Choose a New Password</h3><p>Use a password you have not used before</p></div></div>
                    <div class="feature-item"><div class="feature-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#ffb020" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="m8 12 2.5 2.5L16 9"/></svg></div><div><h3>Continue Learning</h3><p>Get back to your courses safely</p></div></div>
                </div>
            </div>
        </div>
        <div class="journey-text">Your Learning Journey<br><strong>Starts Here &rarr;</strong></div>
        <div class="brand-decoration decoration-one"></div><div class="brand-decoration decoration-two"></div>
        <div class="dots dots-1"></div><div class="dots dots-2"></div>
        <svg class="curve" width="260" height="80" viewBox="0 0 260 80" fill="none"><path d="M2 70C60 10 140 90 258 8" stroke="#fff" stroke-width="2" stroke-dasharray="6 8"/></svg>
    </section>

    <section class="login-form-panel">
        <div class="login-form-wrapper">
            <div class="login-heading">
                <span class="welcome-text">New Password</span>
                <h2>Reset Your Password</h2>
                <p>Enter your new password below to regain access to your account.</p>
            </div>

            <div class="auth-alert auth-alert-error" id="alertError" role="alert" aria-live="assertive"></div>
            <div class="auth-alert auth-alert-success" id="alertSuccess" role="status" aria-live="polite"></div>

            <form id="resetForm" novalidate>
                <input type="hidden" name="token" value="{{ $token ?? request()->query('token') }}">

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg></span>
                        <input type="email" id="email" name="email" value="{{ old('email', $email ?? request()->query('email')) }}" placeholder="Email Address" autocomplete="email" readonly style="background:#f2f2f7;cursor:not-allowed;">
                    </div>
                    <span class="field-error" id="emailError"></span>
                </div>

                <div class="form-group">
                    <label for="password">New Password</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg></span>
                        <input type="password" id="password" name="password" placeholder="New Password" autocomplete="new-password">
                        <button type="button" class="toggle-password" id="togglePassword" aria-label="Show password">
                            <svg id="eyeIcon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                    <span class="field-error" id="passwordError"></span>
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirm New Password</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg></span>
                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm New Password" autocomplete="new-password">
                    </div>
                    <span class="field-error" id="passwordConfirmationError"></span>
                </div>

                <p class="password-hint">Use at least 8 characters. For better security, use a combination of letters, numbers and symbols.</p>

                <button type="submit" class="btn btn-primary" id="resetBtn">
                    <span class="spinner"></span>
                    <span id="resetBtnText">Reset Password</span>
                    <svg class="btn-arrow" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                </button>
            </form>

            <p class="register-text">Remember your password? <a href="{{ url('/login') }}">Back to Login</a></p>
        </div>
    </section>
</div>

<script>
(function () {
    'use strict';

    const form = document.getElementById('resetForm');
    const email = document.getElementById('email');
    const password = document.getElementById('password');
    const passwordConfirmation = document.getElementById('password_confirmation');
    const tokenInput = document.querySelector('input[name="token"]');
    const btn = document.getElementById('resetBtn');
    const btnText = document.getElementById('resetBtnText');
    const alertError = document.getElementById('alertError');
    const alertSuccess = document.getElementById('alertSuccess');
    const emailError = document.getElementById('emailError');
    const passwordError = document.getElementById('passwordError');
    const passwordConfirmationError = document.getElementById('passwordConfirmationError');
    const toggle = document.getElementById('togglePassword');
    const eye = document.getElementById('eyeIcon');

    if (!form || !email || !password || !passwordConfirmation || !btn) {
        console.error('Reset password form could not be initialized.');
        return;
    }

    const EYE = '<path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/>';
    const EYE_OFF = '<path d="M17.9 17.9A10 10 0 0 1 12 19C5 19 1 12 1 12a18 18 0 0 1 5.1-5.9M9.9 5.2A9 9 0 0 1 12 5c7 0 11 7 11 7a18 18 0 0 1 2.2 3.2M14.1 14.1a3 3 0 1 1-4.2-4.2"/><path d="m1 1 22 22"/>';

    if (toggle && eye) {
        toggle.addEventListener('click', function () {
            const show = password.type === 'password';
            password.type = show ? 'text' : 'password';
            eye.innerHTML = show ? EYE_OFF : EYE;
            toggle.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
        });
    }

    function esc(value) {
        const div = document.createElement('div');
        div.textContent = String(value ?? '');
        return div.innerHTML;
    }

    function clearMessages() {
        if (alertError) { alertError.classList.remove('show'); alertError.innerHTML = ''; }
        if (alertSuccess) { alertSuccess.classList.remove('show'); alertSuccess.innerHTML = ''; }
        if (emailError) emailError.textContent = '';
        if (passwordError) passwordError.textContent = '';
        if (passwordConfirmationError) passwordConfirmationError.textContent = '';
        email.classList.remove('invalid');
        password.classList.remove('invalid');
        passwordConfirmation.classList.remove('invalid');
    }

    function showError(message) {
        if (!alertError) return;
        alertError.innerHTML = '<strong>!</strong><div>' + esc(message) + '</div>';
        alertError.classList.add('show');
        if (alertSuccess) { alertSuccess.classList.remove('show'); alertSuccess.innerHTML = ''; }
    }

    function showSuccess(message) {
        if (!alertSuccess) return;
        alertSuccess.innerHTML = '<strong>✓</strong><div>' + esc(message) + '</div>';
        alertSuccess.classList.add('show');
        if (alertError) { alertError.classList.remove('show'); alertError.innerHTML = ''; }
    }

    function fieldError(input, element, message) {
        if (input) input.classList.add('invalid');
        if (element) element.textContent = message;
    }

    function setLoading(loading) {
        btn.disabled = loading;
        btn.classList.toggle('loading', loading);
        if (btnText) btnText.textContent = loading ? 'Resetting...' : 'Reset Password';
    }

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        clearMessages();

        const emailValue = email.value.trim();
        const passwordValue = password.value;
        const passwordConfirmationValue = passwordConfirmation.value;
        const tokenValue = tokenInput ? tokenInput.value.trim() : '';

        let valid = true;

        if (!emailValue) {
            fieldError(email, emailError, 'Email address is required.');
            valid = false;
        }

        if (!passwordValue) {
            fieldError(password, passwordError, 'Password is required.');
            valid = false;
        } else if (passwordValue.length < 8) {
            fieldError(password, passwordError, 'Password must be at least 8 characters.');
            valid = false;
        }

        if (!passwordConfirmationValue) {
            fieldError(passwordConfirmation, passwordConfirmationError, 'Please confirm your password.');
            valid = false;
        } else if (passwordValue !== passwordConfirmationValue) {
            fieldError(passwordConfirmation, passwordConfirmationError, 'Passwords do not match.');
            valid = false;
        }

        if (!tokenValue) {
            showError('The password reset token is missing or invalid. Please request a new reset link.');
            valid = false;
        }

        if (!valid) return;

        setLoading(true);

        try {
            const response = await fetch('{{ url('/api/v1/auth/password/reset') }}', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    token: tokenValue,
                    email: emailValue,
                    password: passwordValue,
                    password_confirmation: passwordConfirmationValue
                })
            });

            let data = {};
            const contentType = response.headers.get('content-type') || '';

            if (contentType.includes('application/json')) {
                try { data = await response.json(); } catch (_) { data = {}; }
            } else {
                const text = await response.text();
                if (text) data.message = text;
            }

            if (response.ok) {
                showSuccess(data.message || 'Your password has been reset successfully.');
                btn.disabled = true;
                btnText.textContent = 'Password Reset';

                setTimeout(function () {
                    window.location.href = '{{ route('login') }}';
                }, 1500);
                return;
            }

            if (response.status === 422) {
                if (data.errors) {
                    if (data.errors.email) fieldError(email, emailError, data.errors.email[0]);
                    if (data.errors.password) fieldError(password, passwordError, data.errors.password[0]);
                    if (data.errors.password_confirmation) fieldError(passwordConfirmation, passwordConfirmationError, data.errors.password_confirmation[0]);
                    if (data.errors.token) showError(data.errors.token[0]);
                } else {
                    showError(data.message || 'The password reset information is invalid.');
                }
                setLoading(false);
                return;
            }

            if (response.status === 400 || response.status === 401 || response.status === 404) {
                showError(data.message || 'This password reset link is invalid or has expired.');
                setLoading(false);
                return;
            }

            showError(data.message || 'Unable to reset your password. Please try again.');
            setLoading(false);
        } catch (error) {
            console.error('Password reset error:', error);
            showError('Network error. Please check your connection and try again.');
            setLoading(false);
        }
    });
})();
</script>
</body>
</html>
