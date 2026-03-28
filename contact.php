<?php
$__dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$__dir = ($__dir === '/' || $__dir === '.' || $__dir === '') ? '' : rtrim($__dir, '/');
$submit_contact_url = $__dir . '/website/submit_contact';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ApnoTax</title>
    <link rel="icon" href="./images/logo.png">
    <?php include "./temp/inc.php" ?>
</head>

<body>
    <?php include "./temp/navbar.php" ?>
    <section class="contact-hero-section">
        <div class="contact-hero-background">
            <div class="container">
                <div class="row align-items-center min-vh-100">
                    <div class="col-lg-6 col-md-12 contact-left-content">
                        <h1 class="contact-hero-heading">Let's Start the Conversation</h1>
                        <p class="contact-hero-description">Ready to take the next step? Schedule a complimentary consultation with our team and discover how we can help you achieve your goals.</p>
                        <div class="contact-info-list">
                            <div class="contact-info-item">
                                <i class="fa-solid fa-phone"></i>
                                <span>9874563210</span>
                            </div>
                            <div class="contact-info-item">
                                <i class="fa-solid fa-envelope"></i>
                                <span>info@apnotax.com</span>
                            </div>
                            <div class="contact-info-item">
                                <i class="fa-solid fa-location-dot"></i>
                                <span>Near High school stadium Suriya District Giridih Jharkhand 825320</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12 contact-right-form">
                        <div class="contact-form-wrapper">
                            <form id="contactForm" action="#" method="POST">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="fullName">Full Name</label>
                                            <input type="text" class="form-control" id="fullName" name="full_name" placeholder="Jane Smith" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="email">Email</label>
                                            <input type="email" class="form-control" id="email" name="email" placeholder="jane@gmail.com" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="phone">Phone</label>
                                            <input type="tel" class="form-control" id="phone" name="phone" placeholder="+123 478 9789" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="selectDate">Select Date</label>
                                            <div class="date-input-wrapper">
                                                <input type="date" class="form-control" id="selectDate" name="select_date" required>
                                                <i class="fa-solid fa-calendar"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="companyName">Your Company Name</label>
                                            <input type="text" class="form-control" id="companyName" name="company_name" placeholder="Company Name">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="serviceInterest">Service You Are Interested In</label>
                                            <div class="select-wrapper">
                                                <select class="form-control" id="serviceInterest" name="service_interest" required>
                                                    <option value="">Select...</option>
                                                    <option value="gst-accountancy">GST Accountancy</option>
                                                    <option value="it-accountancy">IT Accountancy</option>
                                                    <option value="premium">Premium</option>
                                                    <option value="prime">Prime</option>
                                                    <option value="other">Other</option>
                                                </select>
                                                <i class="fa-solid fa-chevron-down"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-4">
                                    <label for="message">How Can I Help You?</label>
                                    <textarea class="form-control" id="message" name="message" rows="4" placeholder="I need help with..."></textarea>
                                </div>
                                <button type="submit" class="btn-submit-form">
                                    Submit Form
                                    <i class="fa-solid fa-arrow-right"></i>
                                </button>
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
        $(document).ready(function() {
            var submitUrl = <?php echo json_encode($submit_contact_url, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
            $('#contactForm').on('submit', function(e) {
                e.preventDefault();
                var $btn = $(this).find('button[type="submit"]');
                var formData = {
                    full_name: $('#fullName').val(),
                    email: $('#email').val(),
                    phone: $('#phone').val(),
                    select_date: $('#selectDate').val(),
                    company_name: $('#companyName').val(),
                    service_interest: $('#serviceInterest').val(),
                    message: $('#message').val()
                };
                if (!formData.full_name || !formData.email || !formData.phone || !formData.select_date || !formData.service_interest) {
                    alert('Please fill in all required fields.');
                    return;
                }
                $btn.prop('disabled', true);
                $.ajax({
                    url: submitUrl,
                    method: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response && response.status) {
                            alert(response.message || 'Thank you! Your message has been sent.');
                            $('#contactForm')[0].reset();
                        } else {
                            alert((response && response.message) ? response.message : 'Sorry, something went wrong.');
                        }
                    },
                    error: function(xhr) {
                        var msg = 'Sorry, there was an error. Please try again.';
                        try {
                            var j = xhr.responseJSON;
                            if (j && j.message) { msg = j.message; }
                        } catch (err) {}
                        alert(msg);
                    },
                    complete: function() {
                        $btn.prop('disabled', false);
                    }
                });
            });
        });
    </script>
</body>

</html>