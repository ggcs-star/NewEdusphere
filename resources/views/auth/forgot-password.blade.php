<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Forgot Password - EduSphere</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Caveat:wght@600&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0}
:root{
  --p-deep:#24105F;
  --p-mid:#3A168F;
  --p-bright:#5B2EE0;
  --p-hover:#6B3CE8;
  --o:#FFB000;
  --o2:#FF8A00;
  --t:#1e1b3a;
  --m:#6b6a80;
  --b:#E3E0EF;
  --err:#e5484d;
  --ok:#16a34a;
}
html,body{height:100%;overflow:hidden}
body{font-family:'Poppins',system-ui,sans-serif;color:var(--t);background:#fff}
.login-container{display:flex;height:100vh}

/* =========================================
   LEFT PANEL
   ========================================= */
.login-brand-panel{
  position:relative;
  flex:0 0 60%;
  background:
    radial-gradient(circle at 75% 35%, rgba(126, 82, 255, 0.55) 0%, rgba(91, 46, 224, 0.20) 28%, transparent 55%),
    radial-gradient(circle at 20% 80%, rgba(91, 46, 224, 0.35) 0%, transparent 45%),
    linear-gradient(135deg, #24105F 0%, #3A168F 45%, #24105F 100%);
  color:#fff;
  overflow:hidden;
  padding:48px 56px;
  display:flex;
  flex-direction:column;
}

/* Decorative organic shapes & lighting */
.login-brand-panel::before {
  content: "";
  position: absolute;
  width: 600px; height: 600px;
  background: radial-gradient(circle, rgba(126,82,255,0.25) 0%, transparent 70%);
  top: -100px; right: -200px;
  border-radius: 50%;
  pointer-events: none;
}
.login-brand-panel::after {
  content: "";
  position: absolute;
  width: 800px; height: 800px;
  background: radial-gradient(circle, rgba(58,22,143,0.6) 0%, transparent 70%);
  bottom: -300px; left: -300px;
  border-radius: 50%;
  pointer-events: none;
}

.brand-logo{display:flex;align-items:center;gap:14px;position:relative;z-index:3}
.brand-logo-icon{width:54px;height:54px;border-radius:14px;background:#fff;display:grid;place-items:center;box-shadow:0 8px 20px rgba(0,0,0,.2)}
.brand-logo h1{font-size:28px;font-weight:800;line-height:1}
.brand-logo h1 span{color:var(--o)}
.brand-logo p{font-size:13px;opacity:.85;margin-top:4px}

.brand-body{display:flex;flex:1;align-items:center;gap:24px;position:relative;z-index:3}
.brand-text{flex:0 0 44%}
.brand-small-title{font-size:20px;font-weight:600;margin-top:12px}
.brand-heading h2{font-size:38px;font-weight:800;color:var(--o);line-height:1.1;margin-bottom:10px;text-shadow:0 4px 20px rgba(255,176,0,0.2)}
.brand-description{font-size:15px;line-height:1.7;opacity:.9;max-width:340px}
.brand-features{margin-top:30px;display:grid;gap:16px}
.feature-item{display:flex;align-items:center;gap:14px}
.feature-icon{width:44px;height:44px;border-radius:12px;background:var(--p-bright);box-shadow:0 6px 16px rgba(91,46,224,0.35);display:grid;place-items:center;flex-shrink:0}
.feature-item h3{font-size:15px;font-weight:600}
.feature-item p{font-size:12.5px;opacity:.8}

/* Illustration container - image pushed to bottom, blended naturally */
.brand-illustration{
  flex:1;
  position:relative;
  display:flex;
  align-items:flex-end;
  justify-content:center;
  padding-bottom:0;
}

.brand-illustration img{
  width:120%;
  max-width:740px;
  border-radius:0;
  box-shadow:none;
  border:none;
  filter:drop-shadow(0 20px 40px rgba(0,0,0,0.3));
  margin-bottom:-100px;
}

/* Floating elements */
.float-badge{position:absolute;width:56px;height:56px;border-radius:16px;display:grid;place-items:center;box-shadow:0 12px 24px rgba(0,0,0,.25);animation:float 4s ease-in-out infinite;z-index:4}
.fb-1{top:8%;left:4%;background:var(--o);border:2px solid rgba(255,255,255,0.2)}
.fb-2{bottom:12%;right:2%;background:#fff;animation-delay:1.5s}
@keyframes float{50%{transform:translateY(-10px)}}

/* Corner badge cluster — purple circle + orange square, floating independently top-right */
.corner-badge-purple{position:absolute;top:5%;right:9%;width:76px;height:76px;border-radius:50%;background:var(--p-bright);display:grid;place-items:center;box-shadow:0 16px 32px rgba(91,46,224,0.45);z-index:4;animation:float 4.5s ease-in-out infinite}
.corner-badge-orange{position:absolute;top:19%;right:3%;width:52px;height:52px;border-radius:14px;background:var(--o);display:grid;place-items:center;box-shadow:0 12px 24px rgba(0,0,0,.2);z-index:4;animation:float 4.5s ease-in-out infinite;animation-delay:1.2s}

.journey-text{font-family:'Caveat',cursive;font-size:28px;line-height:1.2;position:relative;z-index:3;text-shadow:0 2px 10px rgba(0,0,0,0.2);margin-top:18px}
.journey-text strong{color:var(--o)}

/* Decorative Elements */
.brand-decoration{position:absolute;border-radius:50%;z-index:1;pointer-events:none}
.decoration-one{width:380px;height:380px;background:rgba(255,255,255,.03);bottom:-140px;left:-120px;filter:blur(40px)}
.decoration-two{width:220px;height:220px;background:var(--o2);opacity:.8;top:-90px;right:-70px;filter:blur(30px)}
.decoration-three{width:200px;height:200px;background:var(--o2);opacity:.55;top:-80px;left:-80px;filter:blur(25px)}
.dots{position:absolute;z-index:1;width:120px;height:90px;background-image:radial-gradient(rgba(255,255,255,.35) 2px,transparent 2px);background-size:16px 16px;opacity:0.6}
.dots-1{top:110px;right:42%}
.dots-2{bottom:40px;right:60px}
.curve{position:absolute;z-index:1;bottom:70px;left:30%;opacity:.4}

/* =========================================
   RIGHT PANEL
   ========================================= */
.login-form-panel{
  flex:1;
  display:flex;
  align-items:center;
  justify-content:center;
  padding:clamp(10px,3vh,32px) 32px;
  background: linear-gradient(135deg, #ffffff 0%, #FCFBFF 100%);
  position:relative;
  overflow:hidden;
}

/* Subtle decorative circles on right panel */
.login-form-panel::before {
  content: "";
  position: absolute;
  width: 180px; height: 180px;
  background: var(--o);
  border-radius: 50%;
  top: -60px; right: -60px;
  opacity: 0.08;
  pointer-events: none;
}
.login-form-panel::after {
  content: "";
  position: absolute;
  width: 250px; height: 250px;
  background: var(--p-bright);
  border-radius: 50%;
  bottom: -100px; right: -80px;
  opacity: 0.04;
  pointer-events: none;
}

.login-form-wrapper{width:100%;max-width:420px;position:relative;z-index:2}

/* Welcome Text - no pill background */
.welcome-text{
  display:inline-block;
  font-size:13px;
  font-weight:600;
  color: #F5A400;
  background: transparent;
  padding: 0;
  border-radius: 0;
  margin-bottom: 6px;
}

.login-heading h2{font-size:28px;font-weight:700;margin:0 0 6px;color: #24105F; letter-spacing:-0.5px;}
.login-heading p{color:var(--m);font-size:14px;line-height:1.5;margin-bottom:16px}

/* Alerts */
.auth-alert{display:none;gap:10px;align-items:flex-start;padding:12px 14px;border-radius:12px;font-size:13.5px;margin-bottom:18px;line-height:1.5}
.auth-alert.show{display:flex}
.auth-alert-error{background:#fdecec;color:#a61b1f;border:1px solid #f8caca}
.auth-alert-success{background:#e8f8ee;color:#12632f;border:1px solid #bfe9cf}

/* Form Inputs */
.form-group{margin-bottom:12px}
.form-group label{display:block;font-size:13px;font-weight:500;margin-bottom:6px;color:#333}
.input-wrapper{position:relative}
.input-icon{position:absolute;left:16px;top:50%;transform:translateY(-50%);color:#9a98ad;display:flex;pointer-events:none}
.input-wrapper input{
  width:100%;
  height:46px;
  border:1px solid #E3E0EF;
  border-radius:12px;
  padding:0 48px;
  font:inherit;
  font-size:14.5px;
  color:var(--t);
  background:#fafaff;
  transition:border-color .2s,box-shadow .2s,background .2s;
}
.input-wrapper input:focus{
  outline:none;
  border-color:#6B3CE8;
  background:#fff;
  box-shadow:0 0 0 4px rgba(107, 60, 232, 0.08);
}
.input-wrapper input.invalid{border-color:var(--err)}
.field-error{display:block;color:var(--err);font-size:12.5px;margin-top:6px;min-height:0}

a{color:var(--p-bright);font-weight:600;text-decoration:none}
a:hover{text-decoration:underline}

/* Buttons */
.btn{width:100%;height:46px;border-radius:12px;font:inherit;font-size:15px;font-weight:600;display:flex;align-items:center;justify-content:center;gap:10px;cursor:pointer;transition:all .2s}
.btn-primary{
  border:0;
  color:#fff;
  background: linear-gradient(135deg, #5B2EE0 0%, #4B20C4 50%, #3A1AA8 100%);
  box-shadow: 0 8px 20px rgba(75, 32, 196, 0.25);
}
.btn-primary:hover:not(:disabled){
  background: linear-gradient(135deg, #6B3CE8, #4B20C4);
  transform:translateY(-1px);
  box-shadow: 0 12px 24px rgba(75, 32, 196, 0.35);
}
.btn-primary:focus-visible{outline:3px solid rgba(107, 60, 232, 0.35);outline-offset:2px}
.btn-primary:disabled{opacity:.7;cursor:not-allowed}

.spinner{width:18px;height:18px;border:2.5px solid rgba(255,255,255,.4);border-top-color:#fff;border-radius:50%;animation:spin .7s linear infinite;display:none}
.loading .spinner{display:block}
.loading .btn-arrow{display:none}
@keyframes spin{to{transform:rotate(360deg)}}

.register-text{text-align:center;margin-top:14px;font-size:14px;color:var(--m)}

/* =========================================
   RESPONSIVE
   ========================================= */
@media (max-width:1200px){
  .brand-illustration{display:none}
  .brand-text{flex:1}
  .login-brand-panel{flex-basis:50%}
}
@media (max-width:860px){
 html,body{overflow:auto}
 .login-container{flex-direction:column;height:auto;min-height:100vh}
 .login-brand-panel{flex:none;padding:28px 24px}
 .brand-features,.brand-description,.journey-text,.dots,.curve{display:none}
 .brand-small-title{font-size:16px;margin-top:14px}
 .brand-heading h2{font-size:30px;margin:0}
 .login-form-panel{padding:32px 20px 48px;overflow:visible}
}
</style>
</head>
<body>
<div class="login-container">

  @include('auth.partials.brand-panel')

  {{-- RIGHT FORGOT PASSWORD PANEL --}}
  <section class="login-form-panel">
    <div class="login-form-wrapper">
      <div class="login-heading">
        <span class="welcome-text">Forgot Password</span>
        <h2>Reset Your Password</h2>
        <p>Enter your email address and we'll send you a link to reset your password.</p>
      </div>

      <div class="auth-alert auth-alert-error" id="alertError" role="alert" aria-live="assertive"></div>
      <div class="auth-alert auth-alert-success" id="alertSuccess" role="status" aria-live="polite"></div>

      <form id="forgotForm" novalidate>
        <div class="form-group">
          <label for="email">Email Address</label>
          <div class="input-wrapper">
            <span class="input-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg></span>
            <input type="email" id="email" name="email" placeholder="Email Address" autocomplete="email" autofocus>
          </div>
          <span class="field-error" id="emailError"></span>
        </div>

        <button type="submit" class="btn btn-primary" id="forgotBtn" style="margin-top: 10px;">
          <span class="spinner"></span>
          <span id="forgotBtnText">Send Reset Link</span>
          <svg class="btn-arrow" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </button>
      </form>

      <p class="register-text">Remember your password? <a href="{{ url('/login') }}">Back to Login</a></p>
      <p class="register-text" style="margin-top: 10px;">Don't have an account? <a href="{{ url('/register') }}">Register</a></p>
    </div>
  </section>
</div>

<script>
(function () {
const FORGOT_URL = @json(url('/api/v1/auth/password/forgot'));
  const form = document.getElementById('forgotForm');
  const email = document.getElementById('email');
  const btn = document.getElementById('forgotBtn');
  const btnText = document.getElementById('forgotBtnText');
  const alertError = document.getElementById('alertError');
  const alertSuccess = document.getElementById('alertSuccess');
  const emailError = document.getElementById('emailError');

  function esc(s) { const d = document.createElement('div'); d.textContent = String(s); return d.innerHTML; }

  function clearMessages() {
    [alertError, alertSuccess].forEach(a => { a.classList.remove('show'); a.innerHTML = ''; });
    emailError.textContent = '';
    email.classList.remove('invalid');
  }
  function showError(msg) {
    alertError.innerHTML = '<strong>!</strong><div>' + esc(msg) + '</div>';
    alertError.classList.add('show');
  }
  function showSuccess(msg) {
    alertSuccess.innerHTML = '<strong>✓</strong><div>' + esc(msg) + '</div>';
    alertSuccess.classList.add('show');
  }
  function fieldError(input, el, msg) { input.classList.add('invalid'); el.textContent = msg; }

  function setLoading(on) {
    btn.disabled = on;
    btn.classList.toggle('loading', on);
    btnText.textContent = on ? 'Sending...' : 'Send Reset Link';
  }

  form.addEventListener('submit', async function (e) {
    e.preventDefault();
    clearMessages();

    const emailVal = email.value.trim();
    let ok = true;

    if (!emailVal) { fieldError(email, emailError, 'Email is required.'); ok = false; }
    if (!ok) return;

    setLoading(true);
    let res, data = {};
    try {
      res = await fetch(FORGOT_URL, {
        method: 'POST',
        headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
        body: JSON.stringify({ email: emailVal })
      });
      try { data = await res.json(); } catch (_) { data = {}; }
    } catch (err) {
      setLoading(false);
      showError('Network error. Please try again.');
      return;
    }

    if (res.ok && data.success !== false) {
      showSuccess(data.message || 'Password reset link sent! Please check your email.');
      btnText.textContent = 'Link Sent';
      setTimeout(() => { btnText.textContent = 'Send Reset Link'; btn.disabled = false; }, 3000);
      return;
    }

    setLoading(false);
    const errors = data.errors || {};

    if (res.status === 422 && Object.keys(errors).length) {
      if (errors.email) fieldError(email, emailError, [].concat(errors.email)[0]);
      showError(data.message || 'Please fix the highlighted fields.');
    } else if (res.status === 429) {
      showError(data.message || 'Too many attempts. Please try again later.');
    } else if (res.status >= 500) {
      showError('Something went wrong on our side. Please try again.');
    } else {
      showError(data.message || 'Unable to send reset link. Please try again.');
    }
  });
})();
</script>
</body>
</html>