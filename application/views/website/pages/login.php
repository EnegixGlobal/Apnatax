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
                        <div class="auth-form-card">
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
                                        <button type="button" class="auth-password-toggle" id="togglePassword" aria-label="Show password">
                                            <i class="zmdi zmdi-eye" id="eyeIcon"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="text-center text-danger mb-3"><?= $this->session->flashdata('logerr'); ?></div>
                                <input type="hidden" name="role" value="customer">
                                <button type="submit" name="login" class="auth-submit-btn">Login</button>
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

            if (togglePassword && passwordInput && eyeIcon) {
                togglePassword.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);

                    // Toggle eye icon (using zmdi icons)
                    if (type === 'text') {
                        eyeIcon.classList.remove('zmdi-eye');
                        eyeIcon.classList.add('zmdi-eye-off');
                    } else {
                        eyeIcon.classList.remove('zmdi-eye-off');
                        eyeIcon.classList.add('zmdi-eye');
                    }
                });
            }
        });
    </script>
</body>

</html>