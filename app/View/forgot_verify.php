<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Verify OTP</title>
    <link rel="stylesheet" href="public/css/forgot.css">

    <style>
        #resendBtn.disabled {
            pointer-events: none;
            opacity: 0.5;
            cursor: not-allowed;
            color: #999;
        }
        
        #resendBtn {
            cursor: pointer;
            color: #007bff;
            text-decoration: none;
            transition: opacity 0.3s ease;
        }
        
        #resendBtn:hover:not(.disabled) {
            text-decoration: underline;
        }
        
        #timer {
            font-size: 14px;
            margin-top: 8px;
            color: #555;
        }
        
        .otp-input {
            text-align: center;
            letter-spacing: 8px;
            font-size: 24px;
            font-weight: bold;
        }

        .otp-input.error {
            border-color: #dc3545;
            animation: shake 0.5s;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }

        .attempts-info {
            font-size: 13px;
            color: #666;
            margin-top: 10px;
        }

        .attempts-info.warning {
            color: #ff6b6b;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="content">
        <h1>Verify OTP</h1>

        <?php if (!empty($message)): ?>
            <div class="alert <?= htmlspecialchars($messageType) ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
        

        <form method="POST" action="" id="otpForm">
            <label for="otp">Enter OTP</label>
            <input 
                type="text" 
                id="otp"
                name="otp" 
                class="otp-input <?= (!empty($messageType) && $messageType === 'failed') ? 'error' : '' ?>"
                placeholder="000000" 
                maxlength="6"
                pattern="[0-9]{6}"
                inputmode="numeric"
                autocomplete="one-time-code"
                required
            >

            <?php 
            if (isset($_SESSION['otp_attempts']) && $_SESSION['otp_attempts'] > 0): 
                $maxAttempts = 3; 
                $attemptsLeft = $maxAttempts - $_SESSION['otp_attempts'];
                $warningClass = $attemptsLeft <= 1 ? 'warning' : '';
            ?>
                <div class="attempts-info <?= $warningClass ?>">
                    <?php if ($attemptsLeft > 0): ?>
                        <?= $attemptsLeft ?> attempt<?= $attemptsLeft !== 1 ? 's' : '' ?> remaining
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <button type="submit">Verify</button>

            <!-- ✅ RESEND WITH TIMER -->
            <p>
                <a id="resendBtn" class="disabled" href="javascript:void(0)">Resend OTP</a>
                <div id="timer">Wait 30 seconds…</div>
            </p>
        </form>
    </div>

    <script>
        let timeLeft = 30;
        const timerDisplay = document.getElementById("timer");
        const resendBtn = document.getElementById("resendBtn");
        const otpInput = document.getElementById("otp");

        const startTime = sessionStorage.getItem('otpTimerStart');
        if (startTime) {
            const elapsed = Math.floor((Date.now() - parseInt(startTime)) / 1000);
            timeLeft = Math.max(0, 30 - elapsed);
        } else {
            sessionStorage.setItem('otpTimerStart', Date.now().toString());
        }

        function updateTimer() {
            if (timeLeft <= 0) {
                timerDisplay.innerHTML = "You can resend OTP now.";
                resendBtn.classList.remove("disabled");
                resendBtn.href = "index.php?page=forgot";
                sessionStorage.removeItem('otpTimerStart');
            } else {
                timerDisplay.innerHTML = "Resend available in " + timeLeft + "s";
                timeLeft -= 1;
                setTimeout(updateTimer, 1000);
            }
        }

        updateTimer();

        otpInput.focus();

        otpInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
            this.classList.remove('error');
        });

        document.getElementById('otpForm').addEventListener('submit', function(e) {
            const otpValue = otpInput.value;
            
            // Basic validation
            if (otpValue.length !== 6) {
                e.preventDefault();
                otpInput.classList.add('error');
                return false;
            }
        });

        <?php if (!empty($messageType) && $messageType === 'failed'): ?>
        setTimeout(function() {
            otpInput.value = '';
            otpInput.focus();
        }, 500);
        <?php endif; ?>
    </script>

</body>
</html>
