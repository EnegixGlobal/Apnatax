
        <!-- PAGE -->
        <div class="page">
            <div class="employee-login-wrapper">
                <div class="employee-login-container">
                    <div class="employee-login-card">
                        <div class="employee-login-logo">
                            <img src="<?= file_url('assets/images/logo.png'); ?>" alt="Logo" class="employee-logo-img">
                        </div>
                        <h2 class="employee-login-title">Login</h2>
                        <?= form_open('login/validatelogin/','class="employee-login-form"'); ?>
                            <div class="employee-form-group">
                                <label for="username">Mobile No.</label>
                                <input type="text" name="username" id="username" class="employee-form-input" required />
                            </div>
                            <div class="employee-form-group">
                                <label for="password">Password</label>
                                <div class="employee-password-wrapper">
                                    <input type="password" name="password" id="password" class="employee-form-input employee-password-input" required/>
                                    <button type="button" class="employee-password-toggle" id="togglePassword" aria-label="Show password">
                                        <i class="zmdi zmdi-eye" id="eyeIcon"></i>
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
