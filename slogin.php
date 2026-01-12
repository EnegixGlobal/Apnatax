<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SoraTax</title>
    <link rel="icon" href="./images/logo.png">
    <?php include "./temp/inc.php" ?>
</head>

<body>
    <?php include "./temp/navbar.php" ?>
    <section>
        <div class="common-background">
            <div class="container">
                <div class="about-page">
                    <h2>Login</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Login</li>
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
                                <h2>Login</h2>
                            </div>
                            <form action="login/validatelogin/" method="post" class="login100-form validate-form">
                                <div class="form-floating mb-3">
                                    <input type="text" name="username" id="username" placeholder="Username" class="input100 border-start-0 form-control ms-0" required />
                                    <label for="username">Username</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="password" name="password" id="password" placeholder="Password" class="input100 border-start-0 form-control ms-0" required/>
                                    <label for="password">Password</label>
                                </div>
                                <div class="text-center text-danger"><?php print_r($_SESSION); ?></div>
                                <div class="form-floating ">
                                    <input type="hidden" name="role" value="customer">
                                    <button type="submit" name="login" class="btn btn-success">Login</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-4 col-lg-5"></div>
                    <div class="col-12 col-md-4 col-lg-2">
                        <form action="login/validatelogin/" class="login100-form validate-form">
                            <span class="login100-form-title pb-0 text-white">
                                Login
                            </span>
                            <div class="panel panel-primary">
                                <div class="panel-body tabs-menu-body p-0 pt-2">
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="tab5">
                                            <div class="wrap-input100 validate-input input-group" data-bs-validate="Valid email is required: ex@abc.xyz">
                                                <a href="javascript:void(0)" class="input-group-text bg-white text-muted">
                                                    <i class="zmdi zmdi-email text-muted" aria-hidden="true"></i>
                                                </a>
                                                <input type="text" name="username" placeholder="Username" class="input100 border-start-0 form-control ms-0" required />
                                            </div>
                                            <div class="wrap-input100 validate-input input-group" id="Password-toggle">
                                                <a href="javascript:void(0)" class="input-group-text bg-white text-muted">
                                                    <i class="zmdi zmdi-eye text-muted" aria-hidden="true"></i>
                                                </a>
                                                <input type="password" name="password" placeholder="Password" class="input100 border-start-0 form-control ms-0" required/>
                                            </div>
                                            <div class="text-end pt-4 d-none">
                                                <p class="mb-0"><a href="forgot-password.html" class="text-primary ms-1">Forgot Password?</a></p>
                                            </div>
                                            <div class="text-danger text-center"></div>
                                            <div class="container-login100-form-btn">
                                                <button type="submit" name="login" class="login100-form-btn btn-primary">Login</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="gst-accountancy">

                </div>

            </div>
        </div>
    </section>
    <?php include "./temp/footer.php" ?>
    <?php include "./temp/vendor.php" ?>
</body>

</html>