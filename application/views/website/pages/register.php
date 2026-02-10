<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up | SoraTax</title>
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
                            <h2 class="auth-form-title">Sign-Up</h2>
                            <form action="login/register/" method="post" class="auth-form">
                                <div class="form-group mb-3">
                                    <label for="name">Name</label>
                                    <input type="text" name="name" id="name" class="form-control auth-input" required />
                                </div>
                                <div class="form-group mb-3">
                                    <label for="mobile">Mobile no.</label>
                                    <input type="text" name="mobile" id="mobile" class="form-control auth-input" required maxlength="10" />
                                </div>
                                <div class="form-group mb-3">
                                    <label for="email">Email id</label>
                                    <input type="email" name="email" id="email" class="form-control auth-input" required />
                                </div>
                                <div class="form-group mb-3">
                                    <label for="password">Password</label>
                                    <input type="password" name="password" id="password" class="form-control auth-input" required/>
                                </div>
                                <div class="text-center text-danger mb-3"><?= $this->session->flashdata('logerr'); ?></div>
                                <input type="hidden" name="role" value="customer">
                                <button type="submit" name="register" class="auth-submit-btn">Register</button>
                                <p class="auth-link-text">Already have account? <a href="login.php">Login here</a></p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php include "./temp/footer.php" ?>
    <?php include "./temp/vendor.php" ?>
</body>

</html>