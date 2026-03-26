<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | ApnoTax</title>
    <link rel="icon" href="./images/logo.png">
    <?php include "./temp/inc.php" ?>
</head>

<body>
    <?php include "./temp/navbar.php" ?>
    <section class="auth-hero-section">
        <div class="auth-hero-background">
            <div class="container">
                <div class="row align-items-center justify-content-center min-vh-100">
                    <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                        <div class="auth-form-card" style="position:relative;">
                            <a href="<?= base_url('index.php'); ?>" class="auth-login-close-cross" aria-label="Close login"
                               title="Close login"
                               style="position:absolute; top:12px; right:25px; z-index:5; font-size:26px; line-height:1; text-decoration:none; color:#000;">
                                &times;
                            </a>
                            <div class="auth-form-logo">
                                <img src="./images/logo.png" alt="Logo" class="auth-logo-img">
                            </div>
                            <h2 class="auth-form-title">Customer Login</h2>
                            <form action="login/validatelogin/" method="post" class="auth-form">
                                <div class="form-group mb-3">
                                    <label for="username">Mobile No.</label>
                                    <input type="text" name="username" id="username" class="form-control auth-input" required />
                                </div>
                                <div class="form-group mb-3">
                                    <label for="password">Password</label>
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
                                <div class="text-center text-danger mb-3"><?= $this->session->flashdata('logerr'); ?></div>
                                <input type="hidden" name="role" value="customer">
                                <button type="submit" name="login" class="auth-submit-btn">Login</button>
                                <p class="auth-link-text"><a href="forgotpassword.php">Forgot Password?</a></p>
                                <p class="auth-link-text">New Customer? <a href="register.php">Register Now</a></p>
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
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            const eyeOpenSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
            const eyeClosedSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a21.77 21.77 0 0 1 5.06-5.94"></path><path d="M9.9 4.24A10.94 10.94 0 0 1 12 4c7 0 11 8 11 8a21.75 21.75 0 0 1-3.22 4.21"></path><path d="M14.12 14.12a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>';

            if (togglePassword && passwordInput && eyeIcon) {
                togglePassword.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);

                    if (type === 'text') {
                        eyeIcon.innerHTML = eyeClosedSvg;
                    } else {
                        eyeIcon.innerHTML = eyeOpenSvg;
                    }
                });
            }
        });
    </script>
</body>

</html>