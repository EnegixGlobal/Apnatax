<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up | SoraTax</title>
    <link rel="icon" href="./images/logo.png">
    <?php include "./temp/inc.php" ?>
    <style>
        .login-btn{
            line-height: 1.5;
            color: #fff;
            width: 100%;
            height: 40px;
            display: -webkit-box;
            display: -webkit-flex;
            display: -moz-box;
            display: -ms-flexbox;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 0 25px;
            border-radius: 5px;
            -webkit-appearance: button;
            color: #fff !important;
            background: #ff5e3a !important;
            border-color: #ff5e3a !important;
        }
    </style>
</head>

<body>
    <?php include "./temp/navbar.php" ?>
    <section>
        <div class="common-background">
            <div class="container">
                <div class="about-page">
                    <h2>Sign Up</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Sign Up</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </section>
    <section>
        <div class="container">
            <div class="content-section">
                <div class="contact-form">
                    <div class="row">
                        <div class="col-12 col-md-4"></div>
                        <div class="col-12 col-md-4">
                            <div class="title">
                                <h2>Sign Up</h2>
                            </div>
                            <form action="login/register/" method="post" class="login100-form validate-form">
                                <div class="form-floating mb-3">
                                    <input type="text" name="name" id="name" placeholder="Name" class="input100 border-start-0 form-control ms-0" required />
                                    <label for="name">Name</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="text" name="mobile" id="mobile" placeholder="Mobile" class="input100 border-start-0 form-control ms-0" required maxlength="10" />
                                    <label for="mobile">Mobile</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="email" name="email" id="email" placeholder="E-mail" class="input100 border-start-0 form-control ms-0" required />
                                    <label for="email">E-mail</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="password" name="password" id="password" placeholder="Password" class="input100 border-start-0 form-control ms-0" required/>
                                    <label for="password">Password</label>
                                </div>
                                <div class="text-center text-danger"><?= $this->session->flashdata('logerr'); ?></div>
                                <div class="row">
                                    <div class="col-12">
                                        <input type="hidden" name="role" value="customer">
                                        <button type="submit" name="register" class="login-btn">Register</button>
                                    </div>
                                </div><br>
                                <p>Already have an Account? <a href="login.php">Login Now</a></p>
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