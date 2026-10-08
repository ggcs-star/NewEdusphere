<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Register - EduSphere</title>
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
html,body{height:100%}
body{font-family:'Poppins',system-ui,sans-serif;color:var(--t);background:#fff;overflow-x:hidden}
.login-container{display:flex;min-height:100vh}

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
.brand-logo-icon{width:54px;height:54px;border-radius:14px;background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.15);backdrop-filter:blur(4px);display:grid;place-items:center;box-shadow:0 8px 20px rgba(0,0,0,.15)}
.brand-logo h1{font-size:28px;font-weight:800;line-height:1}
.brand-logo h1 span{color:var(--o)}
.brand-logo p{font-size:13px;opacity:.85;margin-top:4px}

.brand-body{display:flex;flex:1;align-items:center;gap:24px;position:relative;z-index:3}
.brand-text{flex:0 0 44%}
.brand-small-title{font-size:30px;font-weight:600;margin-top:24px}
.brand-heading h2{font-size:64px;font-weight:800;color:var(--o);line-height:1.05;margin-bottom:16px;text-shadow:0 4px 20px rgba(255,176,0,0.2)}
.brand-description{font-size:15px;line-height:1.7;opacity:.9;max-width:340px}
.brand-features{margin-top:30px;display:grid;gap:16px}
.feature-item{display:flex;align-items:center;gap:14px}
.feature-icon{width:44px;height:44px;border-radius:12px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.15);display:grid;place-items:center;flex-shrink:0;backdrop-filter:blur(4px)}
.feature-item h3{font-size:15px;font-weight:600}
.feature-item p{font-size:12.5px;opacity:.8}

/* Illustration container - pushed down slightly */
.brand-illustration{
  flex:1;
  position:relative;
  display:flex;
  align-items:flex-end; /* Push image to bottom */
  justify-content:center;
  padding-bottom: 20px; /* Space at bottom */
}

/* Remove white card / heavy shadow, blend naturally */
.brand-illustration {
    flex: 1;
    position: relative;
    display: flex;
    align-items: flex-end;
    justify-content: center;

    padding-bottom: 0;
}

.brand-illustration img {
    width: 120%;
    max-width: 740px;

    border-radius: 0;
    box-shadow: none;
    border: none;

    filter: drop-shadow(0 20px 40px rgba(0,0,0,0.3));

margin-bottom: -100px;
}

.float-badge{position:absolute;width:56px;height:56px;border-radius:16px;display:grid;place-items:center;box-shadow:0 12px 24px rgba(0,0,0,.25);animation:float 4s ease-in-out infinite;z-index:4}
.fb-1{top:8%;left:4%;background:var(--o);border:2px solid rgba(255,255,255,0.2)}
.fb-2{bottom:12%;right:2%;background:#fff;animation-delay:1.5s}
@keyframes float{50%{transform:translateY(-10px)}}

.journey-text{font-family:'Caveat',cursive;font-size:28px;line-height:1.2;position:relative;z-index:3;text-shadow:0 2px 10px rgba(0,0,0,0.2)}
.journey-text strong{color:var(--o)}

/* Decorative Elements */
.brand-decoration{position:absolute;border-radius:50%;z-index:1;pointer-events:none}
.decoration-one{width:380px;height:380px;background:rgba(255,255,255,.03);bottom:-140px;left:-120px;filter:blur(40px)}
.decoration-two{width:220px;height:220px;background:var(--o2);opacity:.8;top:-90px;right:-70px;filter:blur(30px)}
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
  padding:40px 32px;
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

.login-heading h2{font-size:30px;font-weight:700;margin:0 0 8px;color: #24105F; letter-spacing:-0.5px;}
.login-heading p{color:var(--m);font-size:14.5px;line-height:1.6;margin-bottom:28px}

/* Alerts */
.auth-alert{display:none;gap:10px;align-items:flex-start;padding:12px 14px;border-radius:12px;font-size:13.5px;margin-bottom:18px;line-height:1.5}
.auth-alert.show{display:flex}
.auth-alert-error{background:#fdecec;color:#a61b1f;border:1px solid #f8caca}
.auth-alert-success{background:#e8f8ee;color:#12632f;border:1px solid #bfe9cf}

/* Form Inputs */
.form-group{margin-bottom:18px}
.form-group label{display:block;font-size:13.5px;font-weight:500;margin-bottom:8px;color:#333}
.input-wrapper{position:relative}
.input-icon{position:absolute;left:16px;top:50%;transform:translateY(-50%);color:#9a98ad;display:flex;pointer-events:none}
.input-wrapper input{
  width:100%;
  height:54px;
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
.toggle-password{position:absolute;right:12px;top:50%;transform:translateY(-50%);border:0;background:none;cursor:pointer;color:#9a98ad;padding:6px;border-radius:8px;display:flex}
.toggle-password:hover,.toggle-password:focus-visible{color:var(--p-bright);outline:none;background:#f1edff}
.field-error{display:block;color:var(--err);font-size:12.5px;margin-top:6px;min-height:0}

.form-row{display:flex;justify-content:space-between;align-items:center;margin:4px 0 24px;font-size:13.5px}
.remember{display:flex;align-items:center;gap:8px;cursor:pointer;color:var(--m)}
.remember input{width:17px;height:17px;accent-color:var(--p-bright)}
a{color:var(--p-bright);font-weight:600;text-decoration:none}
a:hover{text-decoration:underline}

/* Buttons */
.btn{width:100%;height:54px;border-radius:12px;font:inherit;font-size:15.5px;font-weight:600;display:flex;align-items:center;justify-content:center;gap:10px;cursor:pointer;transition:all .2s}
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

.divider{display:flex;align-items:center;gap:14px;color:#a3a1b5;font-size:12.5px;font-weight:500;margin:22px 0}
.divider:before,.divider:after{content:"";flex:1;height:1px;background:var(--b)}

.btn-google{
  background:#fff;
  border:1px solid #E4E1EE;
  color:var(--t);
  text-decoration:none;
  box-shadow: 0 2px 6px rgba(0,0,0,0.02);
}
.btn-google:hover{
  background:#f8f7fd;
  border-color:#cfcbe6;
  text-decoration:none;
  box-shadow: 0 4px 12px rgba(0,0,0,0.04);
}

.register-text{text-align:center;margin-top:26px;font-size:14px;color:var(--m)}

/* =========================================
   RESPONSIVE
   ========================================= */
@media (max-width:1200px){
  .brand-illustration{display:none}
  .brand-text{flex:1}
  .login-brand-panel{flex-basis:50%}
}
@media (max-width:860px){
 .login-container{flex-direction:column}
 .login-brand-panel{flex:none;padding:28px 24px}
 .brand-features,.brand-description,.journey-text,.dots,.curve{display:none}
 .brand-small-title{font-size:18px;margin-top:18px}
 .brand-heading h2{font-size:38px;margin:0}
 .login-form-panel{padding:32px 20px 48px}
}
</style>
</head>
<body>
<div class="login-container">

  {{-- LEFT BRANDING PANEL --}}
  <section class="login-brand-panel" aria-label="EduSphere">
    <div class="brand-logo">
      <div class="brand-logo-icon">
        <svg width="30" height="30" viewBox="0 0 24 24" fill="#5b2ee0"><path d="M12 3 1 9l11 6 9-4.91V17h2V9L12 3z"/><path d="M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z" fill="#ffb020"/></svg>
      </div>
      <div><h1>Edu<span>Sphere</span></h1><p>Learn &bull; Grow &bull; Build Your Future</p></div>
    </div>

    <div class="brand-body">
      <div class="brand-text">
        <div class="brand-heading">
          <p class="brand-small-title">Start Your</p>
          <h2>Journey</h2>
          <p class="brand-description">Create an account to access a wide range of courses, track your progress and achieve your goals with EduSphere.</p>
        </div>
        <div class="brand-features">
          <div class="feature-item"><div class="feature-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="#ffb020"><path d="M12 3 1 9l11 6 9-4.91V17h2V9L12 3z"/></svg></div><div><h3>Expert Instructors</h3><p>Learn from industry professionals</p></div></div>
          <div class="feature-item"><div class="feature-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="#ffb020"><path d="M8 5v14l11-7z"/></svg></div><div><h3>Video Lessons</h3><p>Learn at your own pace</p></div></div>
          <div class="feature-item"><div class="feature-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#ffb020" stroke-width="2"><circle cx="12" cy="9" r="6"/><path d="M8.5 14 7 22l5-3 5 3-1.5-8"/></svg></div><div><h3>Certificates</h3><p>Showcase your skills</p></div></div>
          <div class="feature-item"><div class="feature-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="#ffb020"><path d="M4 20h3V10H4v10zm6 0h3V4h-3v16zm6 0h3v-7h-3v7z"/></svg></div><div><h3>Track Progress</h3><p>Stay on top of your learning</p></div></div>
        </div>
      </div>

      <div class="brand-illustration">
        <div class="float-badge fb-1"><svg width="28" height="28" viewBox="0 0 24 24" fill="#fff"><path d="M12 3 1 9l11 6 9-4.91V17h2V9L12 3z"/></svg></div>
        <img src="{{ asset('images/hero-student.jpg') }}" alt="Student learning on a laptop with books and course dashboard">
        <div class="float-badge fb-2"><svg width="26" height="26" viewBox="0 0 24 24" fill="#5b2ee0"><path d="M8 5v14l11-7z"/></svg></div>
      </div>
    </div>

    <div class="journey-text">Your Learning Journey<br><strong>Starts Here &rarr;</strong></div>

    <div class="brand-decoration decoration-one"></div>
    <div class="brand-decoration decoration-two"></div>
    <div class="dots dots-1"></div><div class="dots dots-2"></div>
    <svg class="curve" width="260" height="80" viewBox="0 0 260 80" fill="none"><path d="M2 70C60 10 140 90 258 8" stroke="#fff" stroke-width="2" stroke-dasharray="6 8"/></svg>
  </section>

  {{-- RIGHT REGISTER PANEL --}}
  <section class="login-form-panel">
    <div class="login-form-wrapper">
      <div class="login-heading">
        <span class="welcome-text">Get Started</span>
        <h2>Create an Account</h2>
        <p>Join EduSphere and start learning today.</p>
      </div>

      <div class="auth-alert auth-alert-error" id="alertError" role="alert" aria-live="assertive"></div>
      <div class="auth-alert auth-alert-success" id="alertSuccess" role="status" aria-live="polite"></div>

      <form id="registerForm" novalidate>
        <div class="form-group">
          <label for="name">Full Name</label>
          <div class="input-wrapper">
            <span class="input-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>
            <input type="text" id="name" name="name" placeholder="Full Name" autocomplete="name" autofocus>
          </div>
          <span class="field-error" id="nameError"></span>
        </div>

        <div class="form-group">
          <label for="email">Email Address</label>
          <div class="input-wrapper">
            <span class="input-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg></span>
            <input type="email" id="email" name="email" placeholder="Email Address" autocomplete="email">
          </div>
          <span class="field-error" id="emailError"></span>
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <div class="input-wrapper">
            <span class="input-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg></span>
            <input type="password" id="password" name="password" placeholder="Password" autocomplete="new-password">
            <button type="button" class="toggle-password" id="togglePassword" aria-label="Show password">
              <svg id="eyeIcon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
          </div>
          <span class="field-error" id="passwordError"></span>
        </div>

        <div class="form-group">
          <label for="password_confirmation">Confirm Password</label>
          <div class="input-wrapper">
            <span class="input-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg></span>
            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm Password" autocomplete="new-password">
          </div>
          <span class="field-error" id="passwordConfirmationError"></span>
        </div>

        <button type="submit" class="btn btn-primary" id="registerBtn" style="margin-top: 10px;">
          <span class="spinner"></span>
          <span id="registerBtnText">Register</span>
          <svg class="btn-arrow" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </button>
      </form>

      <div class="divider">OR</div>

      <a href="{{ url('/api/v1/auth/social/google/redirect') }}" class="btn btn-google">
        <svg width="20" height="20" viewBox="0 0 48 48"><path fill="#FFC107" d="M43.6 20.5H42V20H24v8h11.3C33.7 32.7 29.2 36 24 36c-6.6 0-12-5.4-12-12s5.4-12 12-12c3.1 0 5.8 1.2 7.9 3l5.7-5.7C34 6.1 29.3 4 24 4 12.9 4 4 12.9 4 24s8.9 20 20 20 20-8.9 20-20c0-1.3-.1-2.4-.4-3.5z"/><path fill="#FF3D00" d="m6.3 14.7 6.6 4.8C14.7 15.1 19 12 24 12c3.1 0 5.8 1.2 7.9 3l5.7-5.7C34 6.1 29.3 4 24 4 16.3 4 9.7 8.3 6.3 14.7z"/><path fill="#4CAF50" d="M24 44c5.2 0 9.9-2 13.4-5.2l-6.2-5.2C29.2 35.1 26.7 36 24 36c-5.2 0-9.6-3.3-11.3-8l-6.5 5C9.5 39.6 16.2 44 24 44z"/><path fill="#1976D2" d="M43.6 20.5H42V20H24v8h11.3c-.8 2.2-2.2 4.2-4.1 5.6l6.2 5.2C37 39.2 44 34 44 24c0-1.3-.1-2.4-.4-3.5z"/></svg>
        Sign up with Google
      </a>

      <p class="register-text">Already have an account? <a href="{{ url('/login') }}">Login</a></p>
    </div>
  </section>
</div>

<script>
(function () {
  const REGISTER_URL = @json(url('/api/v1/auth/register'));
  const DASHBOARD_URL = @json(url('/dashboard'));

  const form = document.getElementById('registerForm');
  const name = document.getElementById('name');
  const email = document.getElementById('email');
  const password = document.getElementById('password');
  const passwordConfirmation = document.getElementById('password_confirmation');
  const btn = document.getElementById('registerBtn');
  const btnText = document.getElementById('registerBtnText');
  const alertError = document.getElementById('alertError');
  const alertSuccess = document.getElementById('alertSuccess');
  const nameError = document.getElementById('nameError');
  const emailError = document.getElementById('emailError');
  const passwordError = document.getElementById('passwordError');
  const passwordConfirmationError = document.getElementById('passwordConfirmationError');
  const toggle = document.getElementById('togglePassword');
  const eye = document.getElementById('eyeIcon');

  const EYE = '<path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/>';
  const EYE_OFF = '<path d="M17.9 17.9A10 10 0 0 1 12 19C5 19 1 12 1 12a18 18 0 0 1 5.1-5.9M9.9 5.2A9 9 0 0 1 12 5c7 0 11 7 11 7a18 18 0 0 1-2.2 3.2M14.1 14.1a3 3 0 1 1-4.2-4.2"/><path d="m1 1 22 22"/>';

  toggle.addEventListener('click', function () {
    const show = password.type === 'password';
    password.type = show ? 'text' : 'password';
    eye.innerHTML = show ? EYE_OFF : EYE;
    toggle.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
  });

  function esc(s) { const d = document.createElement('div'); d.textContent = String(s); return d.innerHTML; }

  function clearMessages() {
    [alertError, alertSuccess].forEach(a => { a.classList.remove('show'); a.innerHTML = ''; });
    nameError.textContent = ''; emailError.textContent = '';
    passwordError.textContent = ''; passwordConfirmationError.textContent = '';
    name.classList.remove('invalid'); email.classList.remove('invalid');
    password.classList.remove('invalid'); passwordConfirmation.classList.remove('invalid');
  }
  function showError(msg) {
    alertError.innerHTML = '<strong>!</strong><div>' + esc(msg) + '</div>';
    alertError.classList.add('show');
  }
  function fieldError(input, el, msg) { input.classList.add('invalid'); el.textContent = msg; }

  function setLoading(on) {
    btn.disabled = on;
    btn.classList.toggle('loading', on);
    btnText.textContent = on ? 'Registering...' : 'Register';
  }

  form.addEventListener('submit', async function (e) {
    e.preventDefault();
    clearMessages();

    const nameVal = name.value.trim();
    const emailVal = email.value.trim();
    const passVal = password.value;
    const passConfVal = passwordConfirmation.value;
    let ok = true;

    if (!nameVal) { fieldError(name, nameError, 'Full name is required.'); ok = false; }
    if (!emailVal) { fieldError(email, emailError, 'Email is required.'); ok = false; }
    if (!passVal) { fieldError(password, passwordError, 'Password is required.'); ok = false; }
    if (!passConfVal) { fieldError(passwordConfirmation, passwordConfirmationError, 'Please confirm your password.'); ok = false; }
    if (passVal && passConfVal && passVal !== passConfVal) {
      fieldError(passwordConfirmation, passwordConfirmationError, 'Passwords do not match.');
      ok = false;
    }
    if (!ok) return;

    setLoading(true);
    let res, data = {};
    try {
      res = await fetch(REGISTER_URL, {
        method: 'POST',
        headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
        body: JSON.stringify({ name: nameVal, email: emailVal, password: passVal, password_confirmation: passConfVal })
      });
      try { data = await res.json(); } catch (_) { data = {}; }
    } catch (err) {
      setLoading(false);
      showError('Network error. Please try again.');
      return;
    }

    if (res.ok && data.success !== false) {
      alertSuccess.textContent = data.message || 'Registration successful. Redirecting...';
      alertSuccess.classList.add('show');
      btnText.textContent = 'Redirecting...';
      setTimeout(function () { window.location.href = DASHBOARD_URL; }, 900);
      return;
    }

    setLoading(false);
    const errors = data.errors || {};

    if (res.status === 422 && Object.keys(errors).length) {
      if (errors.name) fieldError(name, nameError, [].concat(errors.name)[0]);
      if (errors.email) fieldError(email, emailError, [].concat(errors.email)[0]);
      if (errors.password) fieldError(password, passwordError, [].concat(errors.password)[0]);
      if (errors.password_confirmation) fieldError(passwordConfirmation, passwordConfirmationError, [].concat(errors.password_confirmation)[0]);
      const other = Object.keys(errors).filter(k => !['name', 'email', 'password', 'password_confirmation'].includes(k));
      showError(other.length ? [].concat(errors[other[0]])[0] : (data.message || 'Please fix the highlighted fields.'));
    } else if (res.status === 429) {
      showError(data.message || 'Too many attempts. Please try again later.');
    } else if (res.status >= 500) {
      showError('Something went wrong on our side. Please try again.');
    } else {
      showError(data.message || 'Registration failed. Please try again.');
    }
  });
})();
</script>
</body>
</html>