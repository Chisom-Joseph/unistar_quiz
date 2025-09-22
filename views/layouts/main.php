<?php
// views/layouts/main.php
require_once 'config/constants.php';
require_once 'includes/auth.php';
require_once 'classes/User.php';

$user = new User();
$isLoggedIn = isset($_SESSION['user_id']);
$userData = $isLoggedIn ? $user->getUserData($_SESSION['user_id']) : null;
$isAdmin = $isLoggedIn && $userData['role'] === 'admin';
$currentPage = isset($_GET['page']) ? $_GET['page'] : ''; 
?>
<!doctype html>
<html class="no-js" lang="zxx">
<head>
   <meta charset="utf-8">
   <meta http-equiv="x-ua-compatible" content="ie=edge">
   <title>Educate - University, Online Courses, School & Education Template</title>
   <meta name="description" content="">
   <meta name="viewport" content="width=device-width, initial-scale=1"> 



   <!-- Place favicon.ico in the root directory -->
   <link rel="shortcut icon" type="image/x-icon" href="/public/img/logo/favicon.png">

   <!-- CSS here -->
   <link rel="stylesheet" href="/public/css/bootstrap.min.css">
   <link rel="stylesheet" href="/public/css/animate.css">
   <link rel="stylesheet" href="/public/css/custom-animation.css">
   <link rel="stylesheet" href="/public/css/slick.css">
   <link rel="stylesheet" href="/public/css/nice-select.css">
   <link rel="stylesheet" href="/public/css/flaticon_xoft.css">
   <link rel="stylesheet" href="/public/css/swiper-bundle.css">
   <link rel="stylesheet" href="/public/css/meanmenu.css">
   <link rel="stylesheet" href="/public/css/font-awesome-pro.css">
   <link rel="stylesheet" href="/public/css/magnific-popup.css">
   <link rel="stylesheet" href="/public/css/spacing.css">
   <link rel="stylesheet" href="/public/css/main.css">
</head>

<body>

   <!-- preloader -->
   <div id="preloader">
      <div class="preloader">
         <span></span>
         <span></span>
      </div>
   </div>
   <!-- preloader end  -->

   <!-- back-to-top-start  -->
   <button class="scroll-top scroll-to-target" data-target="html">
      <i class="far fa-angle-double-up"></i>
   </button>
   <!-- back-to-top-end  -->

   <!-- it-offcanvus-area-start -->
   <div class="it-offcanvas-area">
      <div class="itoffcanvas">
         <div class="it-offcanva-bottom-shape d-none d-xxl-block">
         </div>
         <div class="itoffcanvas__close-btn">
            <button class="close-btn"><i class="fal fa-times"></i></button>
         </div>
         <div class="itoffcanvas__logo">
            <a href="/">
               <img src="/public/img/logo/logo-white.png" alt="">
            </a>
         </div>
         <div class="itoffcanvas__text">
            <p>Suspendisse interdum consectetur libero id. Fermentum leo vel orci porta non. Euismod viverra nibh
               cras pulvinar suspen.</p>
         </div>
         <div class="it-menu-mobile"></div>
         <div class="itoffcanvas__info">
            <h3 class="offcanva-title">Get In Touch</h3>
            <div class="it-info-wrapper mb-20 d-flex align-items-center">
               <div class="itoffcanvas__info-icon">
                  <a href="#"><i class="fal fa-envelope"></i></a>
               </div>
               <div class="itoffcanvas__info-address">
                  <span>Email</span>
                  <a href="maito:hello@yourmail.com">hello@yourmail.com</a>
               </div>
            </div>
            <div class="it-info-wrapper mb-20 d-flex align-items-center">
               <div class="itoffcanvas__info-icon">
                  <a href="#"><i class="fal fa-phone-alt"></i></a>
               </div>
               <div class="itoffcanvas__info-address">
                  <span>Phone</span>
                  <a href="tel:(00)45611227890">(00) 456 1122 7890</a>
               </div>
            </div>
            <div class="it-info-wrapper mb-20 d-flex align-items-center">
               <div class="itoffcanvas__info-icon">
                  <a href="#"><i class="fas fa-map-marker-alt"></i></a>
               </div>
               <div class="itoffcanvas__info-address">
                  <span>Location</span>
                  <a href="htits://www.google.com/maps/@37.4801311,22.8928877,3z" target="_blank">Riverside 255,
                     San Francisco, USA </a>
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="body-overlay d-none"></div>
   <!-- it-offcanvus-area-end -->

   <header class="it-header-height">

         <!-- header-top-area-start -->
         <div class="it-header-2-top-area it-header-2-top-style it-header-inner-style  black-bg">
            <div class="container">
               <div class="row align-items-center">
                  <div class="col-xl-8 col-lg-6 col-md-5 col-sm-7">
                     <div class="it-header-2-top-left">
                        <ul class="text-center text-sm-start">
                           <li class="d-none d-xl-inline-block">
                              <a href="tel:(00)8757845682">
                                 <span>
                                    <i class="fa-light fa-phone-volume"></i>
                                 </span>
                                 (00) 875 784 5682
                              </a>
                           </li>
                           <li class="d-none d-xl-inline-block">
                              <a href="mailto:pacargoinfo@gmail.com">
                                 <span>
                                    <i class="fa-light fa-envelope-open-text"></i>
                                 </span>pacargoinfo@gmail.com
                              </a>
                           </li>
                           <li>
                              <a href="#">
                                 <span>
                                    <i class="fal fa-map-marker-alt"></i>
                                 </span>
                                 Hudson, Wisconsin(WI), 54016</a>
                           </li>
                        </ul>
                     </div>
                  </div>
                  <div class="col-xl-4 col-lg-6 col-md-7 col-sm-5 d-none d-sm-block">
                     <div class="it-header-2-top-right-wrap text-end">
                        <div class="it-header-2-top-right">
                           <ul>
                              <?php if (isset($_SESSION['user_id'])): ?>
                           <li class="it-header-2-top-right-text d-none d-md-inline-block">
                              <div class="it-header-3-top-right">
                                 <span>
                                    <span class="icon"><i class="fab fa-solid fa-user"></i></span>
                                    <a href="<?php echo htmlspecialchars($userData['role']) == "admin" ? "/?page=admin_dashboard" : "?page=dashboard"; ?>" class="text">Dashboard</a>/
                                    <a href="/?page=logout" class="text">Logout</a>
                                 </span>
                              </div>
                           </li>
                           <?php else: ?>
                            <li class="it-header-2-top-right-text d-none d-md-inline-block">
                               <div class="it-header-3-top-right">
                                  <span class="it-header-2-top-social">
                                     <a class="icon"><i class="fab fa-solid fa-user"></i></a>
                                     <a href="/?page=login" class="text">Login</a>
                                     <a>/</a>
                                     <a href="/?page=register" class="text">Register</a>
                                  </span>
                               </div>
                            </li>
                           <?php endif; ?>
                              <li>
                                 <div class="it-header-2-top-social">
                                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                                    <a href="#"><i class="fab fa-twitter"></i></a>
                                    <a href="#"><i class="fab fa-skype"></i></a>
                                    <a href="#"><i class="fab fa-linkedin"></i></a>
                                 </div>
                              </li>
                           </ul>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <!-- header-top-area-end -->
   
         <!-- header-area-start -->
         <div id="header-sticky" class="it-header-5-area">
            <div class="container">
               <div class="it-header-wrap p-relative">
                  <div class="row align-items-center">
                     <div class="col-xl-2 col-6">
                        <div class="it-header-5-logo">
                           <a href="/"><img src="/public/img/logo/logo-black.png" alt=""></a>
                        </div>
                     </div>
                     <div class="col-xl-7 d-none d-xl-block">
                        <div class="it-header-2-main-menu">
                           <nav class="it-menu-content">
                              <ul>
                                 <li><a href="/">Home</a></li>
                                 <li><a href="/?page=about">about us</a></li>
                                 <li><a href="/?page=contact">contact</a></li>
                                 <li><a href="/?page=register">Register</a></li>
                                 <li><a href="/?page=login">Login</a></li>
                              </ul>
                           </nav>
                        </div>
                     </div>
                     <div class="col-xl-3 col-6">
                        <div class="it-header-2-right d-flex align-items-center justify-content-end">
                           <div class="it-header-2-button d-none d-md-block">
                              <a class="it-btn" href="contact.html">
                                 <span>
                                    Contact Us
                                    <svg width="17" height="14" viewBox="0 0 17 14" fill="none"
                                       xmlns="http://www.w3.org/2000/svg">
                                       <path d="M11 1.24023L16 7.24023L11 13.2402" stroke="currentcolor"
                                          stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"
                                          stroke-linejoin="round" />
                                       <path d="M1 7.24023H16" stroke="currentcolor" stroke-width="1.5"
                                          stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                 </span>
                              </a>
                           </div>
                           <div class="it-header-2-bar d-xl-none">
                              <button class="it-menu-bar"><i class="fa-solid fa-bars"></i></button>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <!-- header-area-end -->

   </header>
   
    <?php echo $content ?? 'No content available'; ?>

   <footer>
      <!-- footer-area-start -->
      <div class="it-footer-area it-footer-bg black-bg pt-120 pb-70" data-background="/public/img/footer/bg-1-1.jpg">
         <div class="container">
            <div class="row">
               <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 mb-50">
                  <div class="it-footer-widget footer-col-1">
                     <div class="it-footer-logo pb-25">
                        <a href="/"><img src="/public/img/logo/logo-white.png" alt=""></a>
                     </div>
                     <div class="it-footer-text pb-5">
                        <p>Interdum velit laoreet id donec ultrices <br> tincidunt arcu. Tincidunt tortor aliquam nulla facilisi cras fermentum odio eu.</p>
                     </div>
                     <div class="it-footer-social">
                        <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#"><i class="fa-brands fa-pinterest-p"></i></a>
                        <a href="#"><i class="fa-brands fa-twitter"></i></a>
                     </div>
                  </div>
               </div>
               <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-50">
                  <div class="it-footer-widget footer-col-2">
                     <h4 class="it-footer-title">our services:</h4>
                     <div class="it-footer-list">
                        <ul>
                           <li><a href="#"><i class="fa-regular fa-angle-right"></i>Web development</a></li>
                           <li><a href="#"><i class="fa-regular fa-angle-right"></i>UI/UX Design</a></li>
                           <li><a href="#"><i class="fa-regular fa-angle-right"></i>Management</a></li>
                           <li><a href="#"><i class="fa-regular fa-angle-right"></i>Digital Marketing</a></li>
                           <li><a href="#"><i class="fa-regular fa-angle-right"></i>Blog News</a></li>
                        </ul>
                     </div>
                  </div>
               </div>
               <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 mb-50">
                  <div class="it-footer-widget footer-col-3">
                     <h4 class="it-footer-title">quick links:</h4>
                     <div class="it-footer-list">
                        <ul>
                           <li><a href="#"><i class="fa-regular fa-angle-right"></i>templates</a></li>
                           <li><a href="#"><i class="fa-regular fa-angle-right"></i>blog and article</a></li>
                           <li><a href="#"><i class="fa-regular fa-angle-right"></i>integrations</a></li>
                           <li><a href="#"><i class="fa-regular fa-angle-right"></i>webinars</a></li>
                           <li><a href="#"><i class="fa-regular fa-angle-right"></i>privacy & policy</a></li>
                        </ul>
                     </div>
                  </div>
               </div>
               <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-50">
                  <div class="it-footer-widget footer-col-4">
                     <h4 class="it-footer-title">Gallery</h4>
                     <div class="it-footer-gallery-box">
                        <div class="row gx-5">
                           <div class="col-md-4 col-4">
                              <div class="it-footer-thumb mb-10">
                                 <img src="/public/img/footer/thumb-1-1.png" alt="">
                              </div>
                           </div>
                           <div class="col-md-4 col-4">
                              <div class="it-footer-thumb mb-10">
                                 <img src="/public/img/footer/thumb-1-2.png" alt="">
                              </div>
                           </div>
                           <div class="col-md-4 col-4 mb-10">
                              <div class="it-footer-thumb">
                                 <img src="/public/img/footer/thumb-1-3.png" alt="">
                              </div>
                           </div>
                           <div class="col-md-4 col-4">
                              <div class="it-footer-thumb">
                                 <img src="/public/img/footer/thumb-1-4.png" alt="">
                              </div>
                           </div>
                           <div class="col-md-4 col-4">
                              <div class="it-footer-thumb">
                                 <img src="/public/img/footer/thumb-1-5.png" alt="">
                              </div>
                           </div>
                           <div class="col-md-4 col-4">
                              <div class="it-footer-thumb">
                                 <img src="/public/img/footer/thumb-1-6.png" alt="">
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- footer-area-end -->

      <!-- copy-right area start -->
      <div class="it-copyright-area it-copyright-height">
         <div class="container">
            <div class="row">
               <div class="col-12 wow itfadeUp" data-wow-duration=".9s" data-wow-delay=".3s">
                  <div class="it-copyright-text text-center">
                     <p>Copyright © 2023  <a href="#">Educate </a> || All Rights Reserved</p>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- copy-right area end -->

   </footer>


   <!-- JS here -->
   <script src="/public/js/jquery.js"></script>
   <script src="/public/js/waypoints.js"></script>
   <script src="/public/js/bootstrap.bundle.min.js"></script>
   <script src="/public/js/slick.min.js"></script>
   <script src="/public/js/magnific-popup.js"></script>
   <script src="/public/js/purecounter.js"></script>
   <script src="/public/js/wow.js"></script>
   <script src="/public/js/nice-select.js"></script>
   <script src="/public/js/swiper-bundle.js"></script>
   <script src="/public/js/isotope-pkgd.js"></script>
   <script src="/public/js/imagesloaded-pkgd.js"></script>
   <script src="/public/js/ajax-form.js"></script>
   <script src="/public/js/main.js"></script>



</body>
</html>