<?php
if (!function_exists('base_url')) {
   function base_url($uri)
   {
      return "https://soratax.com/" . $uri;
   }
}
?>
<header>
   <div class="header-bg">
      <div class="container">
         <div class="row">
            <div class="col-lg-6 col-md-6">
               <ul class="left-top-menu">
                  <li>
                     <div class="top-icon">
                        <i class="fa fa-phone" aria-hidden="true"></i>
                        <span>91 9876543210</span>
                     </div>
                  </li>
                  <li>|</li>
                  <li>
                     <div class="top-icon">
                        <i class="fa fa-envelope" aria-hidden="true"></i>
                        <span>abc@gmail.com</span>
                     </div>
                  </li>
               </ul>
            </div>
            <div class="col-lg-6 col-md-6 d-none d-lg-block d-md-block">
               <div class="top-social-icon">
                  <ul class="top-social-media">
                     <li class="follow">Follow Us</li>
                     <li>
                        <a href="#">
                           <i class="fab fa-youtube" aria-hidden="true"></i>
                        </a>
                     </li>
                     <li><a href="#"><i class="fab fa-facebook-f" aria-hidden="true"></i></a></li>
                     <li><a href="#"><i class="fab fa-instagram" aria-hidden="true"></i></a></li>
                  </ul>
               </div>
            </div>
         </div>
      </div>
   </div>
</header>
<nav class="navbar navbar-expand-lg p-0 taxefiNavbar">
   <div class="container">
      <a class="navbar-brand" href="index.php"><img src="./images/logo.png" alt="ApnoTax Logo"></a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
         <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
         <ul class="navbar-nav mx-auto mb-2 mb-lg-0 nav-taxefi">
            <li class="nav-item">
               <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], "index.php") !== FALSE || strpos($_SERVER['PHP_SELF'], "home.php") !== FALSE ? 'active' : ''; ?>" aria-current="page" href="index.php">HOME</a>
            </li>
            <li class="nav-item">
               <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], "about.php") !== FALSE ? 'active' : ''; ?>" aria-current="page" href="about.php">ABOUT</a>
            </li>
            <li class="nav-item dropdown">
               <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                  ACCOUNTANCY
               </a>
               <ul class="dropdown-menu taxefi-dropdown">
                  <li>
                     <a class="dropdown-item taxefi-item" href="gst-accountancy.php">
                        Gst Accountancy
                     </a>
                  </li>
                  <li>
                     <a class="dropdown-item taxefi-item" href="#">
                        IT Accountancy &raquo;
                     </a>
                     <ul class="dropdown-menu dropdown-submenu">
                        <li>
                           <a class="dropdown-item taxefi-item" href="premium.php">Premium</a>
                        </li>
                        <li>
                           <a class="dropdown-item taxefi-item" href="prime.php">Prime</a>
                        </li>
                     </ul>
                  </li>
               </ul>
            </li>
            <li class="nav-item">
               <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], "login.php") !== FALSE ? 'active' : ''; ?>" aria-current="page" href="login.php">CUSTOMER LOGIN</a>
            </li>
            <li class="nav-item">
               <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], "login/") !== FALSE ? 'active' : ''; ?>" aria-current="page" href="login/">EMPLOYEE LOGIN</a>
            </li>
            <li class="nav-item">
               <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], "contact.php") !== FALSE ? 'active' : ''; ?>" aria-current="page" href="contact.php">CONTACT US</a>
            </li>
         </ul>
         <div class="taxefi-btn d-flex">
            <a class="btn btn-dark download-app-btn" href="<?= base_url('download/soratax-latest.apk'); ?>" target="_blank">DOWNLOAD APP</a>
         </div>
      </div>
   </div>
</nav>