<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Verify Device - EduSphere</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background:
                radial-gradient(
                    circle at top left,
                    rgba(91, 46, 224, 0.15),
                    transparent 35%
                ),
                #f7f5ff;
            color: #24105F;
            padding: 20px;
        }

        .verify-card {
            width: 100%;
            max-width: 460px;
            background: #fff;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(36, 16, 95, 0.12);
        }

        .icon {
            width: 70px;
            height: 70px;
            margin: 0 auto 22px;
            border-radius: 18px;
            display: grid;
            place-items: center;
            background: #f0ebff;
            color: #5B2EE0;
        }

        h1 {
            text-align: center;
            font-size: 27px;
            margin-bottom: 10px;
        }

        .description {
            text-align: center;
            color: #6b6a80;
            font-size: 14px;
            line-height: 1.7;
            margin-bottom: 28px;
        }

        .email {
            text-align: center;
            font-weight: 600;
            color: #3A168F;
            margin-bottom: 24px;
            word-break: break-word;
        }

        .alert {
            display: none;
            padding: 12px 14px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 18px;
        }

        .alert.show {
            display: block;
        }

        .alert.error {
            background: #fdecec;
            border: 1px solid #f8caca;
            color: #a61b1f;
        }

        .alert.success {
            background: #e8f8ee;
            border: 1px solid #bfe9cf;
            color: #12632f;
        }

        .otp-input {
            width: 100%;
            height: 58px;
            border: 1px solid #e3e0ef;
            border-radius: 12px;
            background: #fafaff;
            text-align: center;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 8px;
            color: #24105F;
            outline: none;
            transition: .2s;
        }

        .otp-input:focus {
            border-color: #6B3CE8;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(107, 60, 232, .08);
        }

        .btn {
            width: 100%;
            height: 54px;
            margin-top: 18px;
            border: 0;
            border-radius: 12px;
            background: linear-gradient(
                135deg,
                #5B2EE0,
                #3A1AA8
            );
            color: #fff;
            font-family: inherit;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
        }

        .btn:disabled {
            opacity: .65;
            cursor: not-allowed;
        }

        .resend {
            text-align: center;
            margin-top: 18px;
            font-size: 13px;
            color: #6b6a80;
        }

        .resend button {
            border: 0;
            background: transparent;
            color: #5B2EE0;
            font-family: inherit;
            font-weight: 600;
            cursor: pointer;
        }

        .resend button:disabled {
            color: #aaa;
            cursor: not-allowed;
        }

        .back-login {
            display: block;
            text-align: center;
            margin-top: 25px;
            color: #5B2EE0;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
        }

        @media (max-width: 520px) {
            .verify-card {
                padding: 28px 22px;
            }

            h1 {
                font-size: 23px;
            }
        }
    </style>
</head>

<body>

<div class="verify-card">

    <div class="icon">
        <svg
            width="34"
            height="34"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
        >
            <rect x="5" y="2" width="14" height="20" rx="2"></rect>
            <path d="M9 18h6"></path>
            <path d="M8 6h8"></path>
        </svg>
    </div>

    <h1>Verify Your Device</h1>

    <p class="description">
        We've sent a verification OTP to your registered email address.
        Enter the 6-digit OTP below to trust this device.
    </p>

    <div class="email" id="userEmail">
        Your registered email
    </div>

    <div class="alert error" id="errorMessage"></div>

    <div class="alert success" id="successMessage"></div>

    <input
        type="text"
        id="otp"
        class="otp-input"
        maxlength="6"
        inputmode="numeric"
        autocomplete="one-time-code"
        placeholder="••••••"
    >

    <button
        type="button"
        id="verifyBtn"
        class="btn"
    >
        Verify Device
    </button>

    <div class="resend">
        Didn't receive the OTP?

        <button
            type="button"
            id="resendBtn"
        >
            Resend OTP
        </button>
    </div>

    <a
        href="{{ route('login') }}"
        class="back-login"
    >
        ← Back to Login
    </a>

</div>

<script>
(function () {

    const API_BASE = @json(url('/api/v1'));

    const SEND_OTP_URL =
        API_BASE + '/device/send-otp';

    const VERIFY_OTP_URL =
        API_BASE + '/device/verify-otp';

    const SESSION_URL =
        @json(url('/web/session'));

    const DASHBOARD_URL =
        @json(url('/dashboard'));

    const LOGIN_URL =
        @json(route('login'));

    const CSRF_TOKEN =
        document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute('content');

    const otpInput =
        document.getElementById('otp');

    const verifyBtn =
        document.getElementById('verifyBtn');

    const resendBtn =
        document.getElementById('resendBtn');

    const errorMessage =
        document.getElementById('errorMessage');

    const successMessage =
        document.getElementById('successMessage');

    const userEmail =
        document.getElementById('userEmail');


    /*
     * -----------------------------------------
     * DEVICE ID
     * -----------------------------------------
     */

    function getDeviceId() {

        let deviceId =
            localStorage.getItem('device_id') ||
            localStorage.getItem('deviceId') ||
            localStorage.getItem('device_id_value');

        if (!deviceId) {

            if (
                window.crypto &&
                crypto.randomUUID
            ) {
                deviceId = crypto.randomUUID();
            } else {
                deviceId =
                    'web-' +
                    Date.now() +
                    '-' +
                    Math.random()
                        .toString(36)
                        .substring(2, 15);
            }

            localStorage.setItem(
                'device_id',
                deviceId
            );

            localStorage.setItem(
                'deviceId',
                deviceId
            );
        }

        return deviceId;
    }


    const DEVICE_ID = getDeviceId();


    /*
     * -----------------------------------------
     * ACCESS TOKEN
     * -----------------------------------------
     */

    const ACCESS_TOKEN =
        localStorage.getItem(
            'passport_access_token'
        );


    /*
     * -----------------------------------------
     * USER
     * -----------------------------------------
     */

    try {

        const storedUser =
            localStorage.getItem(
                'passport_user'
            );

        if (storedUser) {

            const user =
                JSON.parse(storedUser);

            if (user.email) {
                userEmail.textContent =
                    user.email;
            }
        }

    } catch (_) {}


    /*
     * -----------------------------------------
     * CHECK TOKEN
     * -----------------------------------------
     */

    if (!ACCESS_TOKEN) {

        window.location.href =
            LOGIN_URL;

        return;
    }


    /*
     * -----------------------------------------
     * MESSAGES
     * -----------------------------------------
     */

    function clearMessages() {

        errorMessage.classList.remove('show');
        successMessage.classList.remove('show');

        errorMessage.textContent = '';
        successMessage.textContent = '';
    }


    function showError(message) {

        successMessage.classList.remove('show');

        errorMessage.textContent =
            message ||
            'Something went wrong.';

        errorMessage.classList.add('show');
    }


    function showSuccess(message) {

        errorMessage.classList.remove('show');

        successMessage.textContent =
            message ||
            'Success.';

        successMessage.classList.add('show');
    }


    /*
     * -----------------------------------------
     * API HEADERS
     * -----------------------------------------
     */

    function apiHeaders() {

        return {
            'Accept': 'application/json',
            'Content-Type': 'application/json',

            'Authorization':
                'Bearer ' + ACCESS_TOKEN,

            'X-Device-ID':
                DEVICE_ID,

            'X-Requested-With':
                'XMLHttpRequest'
        };
    }


    /*
     * -----------------------------------------
     * SEND OTP
     * -----------------------------------------
     */

    async function sendOtp() {

        clearMessages();

        resendBtn.disabled = true;

        try {

            const response =
                await fetch(
                    SEND_OTP_URL,
                    {
                        method: 'POST',

                        headers: apiHeaders(),

                        credentials:
                            'same-origin',

                        body: JSON.stringify({
                            device_id:
                                DEVICE_ID
                        })
                    }
                );

            let data = {};

            try {
                data = await response.json();
            } catch (_) {}


            /*
             * Device already trusted
             */

            if (
                response.ok &&
                (
                    data.trust_level === 'trusted' ||
                    data.data?.trust_level === 'trusted'
                )
            ) {

                await createWebSession();

                return;
            }


            if (!response.ok) {

                showError(
                    data.message ||
                    'Unable to send OTP.'
                );

                resendBtn.disabled = false;

                return;
            }


            showSuccess(
                data.message ||
                'Verification OTP sent successfully.'
            );

            startResendTimer(
                Number(
                    data.expires_in_seconds ||
                    60
                )
            );

        } catch (error) {

            showError(
                'Network error. Please try again.'
            );

            resendBtn.disabled = false;
        }
    }


    /*
     * -----------------------------------------
     * VERIFY OTP
     * -----------------------------------------
     */

    verifyBtn.addEventListener(
        'click',
        async function () {

            clearMessages();

            const otp =
                otpInput.value.trim();

            if (!/^\d{6}$/.test(otp)) {

                showError(
                    'Please enter a valid 6-digit OTP.'
                );

                return;
            }


            verifyBtn.disabled = true;

            verifyBtn.textContent =
                'Verifying...';


            try {

                const response =
                    await fetch(
                        VERIFY_OTP_URL,
                        {
                            method: 'POST',

                            headers: apiHeaders(),

                            credentials:
                                'same-origin',

                            body: JSON.stringify({
                                otp: otp,

                                device_id:
                                    DEVICE_ID
                            })
                        }
                    );

                let data = {};

                try {
                    data =
                        await response.json();
                } catch (_) {}


                if (!response.ok) {

                    verifyBtn.disabled =
                        false;

                    verifyBtn.textContent =
                        'Verify Device';

                    showError(
                        data.message ||
                        'Invalid OTP.'
                    );

                    return;
                }


                showSuccess(
                    data.message ||
                    'Device verified successfully.'
                );


                /*
                 * Device trusted successfully.
                 * Now create Laravel web session.
                 */

                await createWebSession();

            } catch (error) {

                verifyBtn.disabled =
                    false;

                verifyBtn.textContent =
                    'Verify Device';

                showError(
                    'Network error. Please try again.'
                );
            }
        }
    );


    /*
     * -----------------------------------------
     * CREATE WEB SESSION
     * -----------------------------------------
     */

    async function createWebSession() {

        try {

            const response =
                await fetch(
                    SESSION_URL,
                    {
                        method: 'POST',

                        headers: {
                            'Accept':
                                'application/json',

                            'Content-Type':
                                'application/json',

                            'Authorization':
                                'Bearer ' +
                                ACCESS_TOKEN,

                            'X-Device-ID':
                                DEVICE_ID,

                            'X-CSRF-TOKEN':
                                CSRF_TOKEN || '',

                            'X-Requested-With':
                                'XMLHttpRequest'
                        },

                        credentials:
                            'same-origin',

                        body: JSON.stringify({
                            device_id:
                                DEVICE_ID
                        })
                    }
                );


            let data = {};

            try {
                data =
                    await response.json();
            } catch (_) {}


            if (!response.ok) {

                showError(
                    data.message ||
                    'Unable to create web session.'
                );

                verifyBtn.disabled =
                    false;

                verifyBtn.textContent =
                    'Verify Device';

                return;
            }


            showSuccess(
                'Device verified. Redirecting...'
            );


            setTimeout(
                function () {
                    window.location.href =
                        DASHBOARD_URL;
                },
                500
            );

        } catch (error) {

            showError(
                'Unable to create web session.'
            );

            verifyBtn.disabled =
                false;

            verifyBtn.textContent =
                'Verify Device';
        }
    }


    /*
     * -----------------------------------------
     * RESEND TIMER
     * -----------------------------------------
     */

    let resendTimer = null;

    function startResendTimer(seconds) {

        clearInterval(resendTimer);

        let remaining =
            Math.min(seconds, 60);

        resendBtn.disabled =
            true;

        resendBtn.textContent =
            'Resend OTP (' +
            remaining +
            's)';

        resendTimer =
            setInterval(
                function () {

                    remaining--;

                    if (remaining <= 0) {

                        clearInterval(
                            resendTimer
                        );

                        resendBtn.disabled =
                            false;

                        resendBtn.textContent =
                            'Resend OTP';

                        return;
                    }

                    resendBtn.textContent =
                        'Resend OTP (' +
                        remaining +
                        's)';

                },
                1000
            );
    }


    /*
     * -----------------------------------------
     * RESEND OTP BUTTON
     * -----------------------------------------
     */

    resendBtn.addEventListener(
        'click',
        function () {

            if (!resendBtn.disabled) {
                sendOtp();
            }

        }
    );


    /*
     * -----------------------------------------
     * OTP INPUT
     * -----------------------------------------
     */

    otpInput.addEventListener(
        'input',
        function () {

            this.value =
                this.value
                    .replace(/\D/g, '')
                    .substring(0, 6);

        }
    );


    /*
     * -----------------------------------------
     * AUTO SEND OTP
     * -----------------------------------------
     */

    sendOtp();

})();
</script>

</body>
</html>