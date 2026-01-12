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
                    <h2>Contact</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Contact</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </section>
    <section>
        <div class="container">
            <div class="content-section">
                <div class="row">
                    <div class="col-lg-4 col-md-6">
                        <div class="contact-card">
                            <div class="contact-icon">
                                <i class="fa-solid fa-location-dot"></i>
                            </div>
                            <h4>Our Location</h4>
                            <p>Ranchi, Jharkhand, India</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="contact-card">
                            <div class="contact-icon">
                                <i class="fa-solid fa-phone-volume"></i>
                            </div>
                            <h4>Phone Number</h4>
                            <p>+91-9874563210</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="contact-card">
                            <div class="contact-icon">
                                <i class="fa-solid fa-envelope-circle-check"></i>
                            </div>
                            <h4>Mail us</h4>
                            <p>Taxefi12@gmail.com</p>
                        </div>
                    </div>
                </div>
                <div class="contact-form">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="contact-img">
                                <img src="./images/contact-img.webp" alt="contact images">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="title">
                                <h2>Get In Touch with Us</h2>
                            </div>
                            <form action="#">
                                <div class="form-floating mb-3">
                                    <input type="email" class="form-control" id="floatingInput" placeholder="name@example.com">
                                    <label for="floatingPassword">Your Name</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="password" class="form-control" id="floatingPassword" placeholder="Password">
                                    <label for="floatingPassword">Your Phone</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="password" class="form-control" id="floatingPassword" placeholder="Password">
                                    <label for="floatingPassword">Your Email</label>
                                </div>
                                <div class="form-floating mb-4">
                                    <textarea class="form-control" placeholder="Leave a comment here" id="floatingTextarea"></textarea>
                                    <label for="floatingTextarea">Your Message</label>
                                </div>
                                <a href="#" class="texefibtn">Submit</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="map-section">
            <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d468895.11916624743!2d85.321326!3d23.343205000000005!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x39f4e0035ace6e73%3A0x41f4e59a6d674446!2sJagannath%20Mandir!5e0!3m2!1sen!2sin!4v1699519542628!5m2!1sen!2sin" width="100%" height="350" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </section>
    <?php include "./temp/footer.php" ?>
    <?php include "./temp/vendor.php" ?>
</body>

</html>