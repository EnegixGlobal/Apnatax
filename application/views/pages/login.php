
        <!-- PAGE -->
        <div class="page">
            <div class="employee-login-wrapper">
                <div class="employee-login-container">
                    <div class="employee-login-card" style="position:relative;">
                        <a href="<?= base_url('index.php'); ?>" class="employee-login-close-cross" aria-label="Close login"
                           title="Close login"
                           style="position:absolute; top:12px; right:25px; z-index:5; font-size:26px; line-height:1; text-decoration:none; color:#000;">
                            &times;
                        </a>
                        <div class="employee-login-logo">
                            <img src="<?= file_url('assets/images/logo.png'); ?>" alt="Logo" class="employee-logo-img">
                        </div>
                        <h2 class="employee-login-title">Employee Login</h2>
                        <?= form_open('login/validatelogin/','class="employee-login-form"'); ?>
                            <div class="employee-form-group">
                                <label for="username">Mobile No.</label>
                                <input type="text" name="username" id="username" class="employee-form-input" required />
                            </div>
                            <div class="employee-form-group">
                                <label for="password">Password</label>
                                <div class="employee-password-wrapper">
                                    <input type="password" name="password" id="password" class="employee-form-input employee-password-input" required/>
                                    <button type="button" class="employee-password-toggle" id="togglePassword" aria-label="Show password" style="color:#000; opacity:1;">
                                        <span id="eyeIcon" style="display:inline-flex;align-items:center;justify-content:center;color:#000;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"></path>
                                                <circle cx="12" cy="12" r="3"></circle>
                                            </svg>
                                        </span>
                                    </button>
                                </div>
                            </div>
                            <div class="employee-error-message"><?= $this->session->flashdata('logerr'); ?></div>
                            <button type="submit" name="login" class="employee-login-btn">Login</button>
                        <?= form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- End PAGE -->
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
