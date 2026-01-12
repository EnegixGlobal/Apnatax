<?php
if(!function_exists('base_url')){
   function base_url($uri){
      return "https://soratax.com/".$uri;
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
                        <span>+91-9874563210</span>
                     </div>
                  </li>
                  <li>|</li>
                  <li>
                     <div class="top-icon">
                        <i class="fa fa-envelope" aria-hidden="true"></i>
                        <span>info@soratax.com</span>
                     </div>
                  </li>
               </ul>
            </div>
            <div class="col-lg-6 col-md-6 d-none d-lg-block d-md-block">
               <div class="top-social-icon">
                  <ul class="top-social-media">
                     <li class="follow">Follow us : </li>
                     <li>
                        <a href="">
                           <i class="fab fa-facebook-f" aria-hidden="true"></i>
                        </a>
                     </li>
                     <li><a href="#"><i class="fab fa-twitter" aria-hidden="true"></i></a></li>
                     <li><a href="#"><i class="fab fa-instagram" aria-hidden="true"></i></a></li>
                     <li><a href="#"><i class="fab fa-youtube" aria-hidden="true"></i></a></li>
                  </ul>
               </div>
            </div>
         </div>
      </div>
   </div>
</header>
<nav class="navbar navbar-expand-lg p-0 taxefiNavbar">
   <div class="container">
      <a class="navbar-brand" href="index.php"><img src="./images/logo.png" alt=""></a>
      <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight" aria-expanded="false" aria-label="Toggle navigation">
         <span class="navbar-toggler-icon"></span>
      </button>
      <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
         <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasRightLabel">SoraTax</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
         </div>
         <div class="offcanvas-body">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 nav-taxefi">
               <li class="nav-item">
                  <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], "about.php") !== FALSE ? 'active' : ''; ?>" aria-current="page" href="about.php">About Us</a>
               </li>
               <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                     Accountancy
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
               </li>
            </ul>
            </li>
            <li class="nav-item">
               <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], "#") !== FALSE ? 'active' : ''; ?>" aria-current="page" href="#">Registation</a>
            </li>
            <li class="nav-item">
               <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], "#") !== FALSE ? 'active' : ''; ?>" aria-current="page" href="#">Returns</a>
            </li>
            <li class="nav-item">
               <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], "#") !== FALSE ? 'active' : ''; ?>" aria-current="page" href="#">Pricing</a>
            </li>
            <li class="nav-item">
               <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], "contact.php") !== FALSE ? 'active' : ''; ?>" aria-current="page" href="contact.php">Contact Us</a>
            </li>
            <li class="nav-item">
               <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], "login.php") !== FALSE ? 'active' : ''; ?>" aria-current="page" href="login.php">Customer Login</a>
            </li>
            <li class="nav-item">
               <a class="nav-link " aria-current="page" href="login/">Employee Login</a>
            </li>
            <li class="nav-item"><a class="nav-link text-danger" href="<?= base_url('download/soratax-latest.apk'); ?>" target="_blank">Download App</a></li>
            </ul>
            </li>
            </ul>
            <div class="taxefi-btn d-flex"></div>
         </div>
      </div>
   </div>
</nav>