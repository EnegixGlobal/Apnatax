<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | ApnoTax</title>
    <link rel="icon" href="./images/logo.png">
    <?php include "./temp/inc.php" ?>
</head>

<body>
    <?php include "./temp/navbar.php" ?>
    <section class="auth-hero-section">
        <div class="auth-hero-background">
            <div class="container">
                <div class="row align-items-center justify-content-center min-vh-100">
                    <div class="col-12 col-md-8 col-lg-5 col-xl-4">
                        <div class="auth-form-card">
                            <h2 class="auth-form-title">Reset Password</h2>
                            <form action="login/resetpassword/" method="post" class="auth-form">
                                <div class="form-group mb-3">
                                    <label for="password">New Password</label>
                                    <div class="auth-password-wrapper">
                                        <input type="password" name="password" id="password" class="form-control auth-input auth-password-input" required />
                                        <button type="button" class="auth-password-toggle" id="togglePassword" aria-label="Show password" style="color:#000; opacity:1;">
                                            <span id="eyeIcon" style="display:inline-flex;align-items:center;justify-content:center;color:#000;">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                            </span>
                                        </button>
                                    </div>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="repassword">Confirm Password</label>
                                    <div class="auth-password-wrapper">
                                        <input type="password" name="repassword" id="repassword" class="form-control auth-input auth-password-input" required />
                                        <button type="button" class="auth-password-toggle" id="toggleRepassword" aria-label="Show password" style="color:#000; opacity:1;">
                                            <span id="eyeIconConfirm" style="display:inline-flex;align-items:center;justify-content:center;color:#000;">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                            </span>
                                        </button>
                                    </div>
                                </div>
                                <div class="text-center text-danger mb-3"><?= $this->session->flashdata('logerr'); ?></div>
                                <button type="submit" name="updatepassword" class="auth-submit-btn">Update Password</button>
                                <p class="auth-link-text">Back to <a href="login.php">Login</a></p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php include "./temp/footer.php" ?>
    <?php include "./temp/vendor.php" ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const eyeOpenSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
            const eyeClosedSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a21.77 21.77 0 0 1 5.06-5.94"></path><path d="M9.9 4.24A10.94 10.94 0 0 1 12 4c7 0 11 8 11 8a21.75 21.75 0 0 1-3.22 4.21"></path><path d="M14.12 14.12a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>';

            function bindPasswordToggle(toggleBtn, input, eyeEl) {
                if (!toggleBtn || !input || !eyeEl) return;
                toggleBtn.addEventListener('click', function() {
                    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                    input.setAttribute('type', type);
                    eyeEl.innerHTML = type === 'text' ? eyeClosedSvg : eyeOpenSvg;
                });
            }

            bindPasswordToggle(document.getElementById('togglePassword'), document.getElementById('password'), document.getElementById('eyeIcon'));
            bindPasswordToggle(document.getElementById('toggleRepassword'), document.getElementById('repassword'), document.getElementById('eyeIconConfirm'));
        });
    </script>
</body>

</html>
