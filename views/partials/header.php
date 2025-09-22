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
                                 <?php if (!$isLoggedIn): ?>
                                    <li><a href="/?page=register">Register</a></li>
                                    <li><a href="/?page=login">Login</a></li>
                                <?php endif; ?>
                              </ul>
                           </nav>
                        </div>
                     </div>
                     <div class="col-xl-3 col-6">
                        <div class="it-header-2-right d-flex align-items-center justify-content-end">
                           <div class="it-header-2-button d-none d-md-block">
                            <?php if ($isLoggedIn && $userData): ?>
                                <div class="nav-item dropdown header-profile">
                                    <a class="nav-link d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                                        <!-- Profile Image -->
                                        <img src="<?php echo SITE_URL . "/public/uploads/" . htmlspecialchars($userData['profile_pic']); ?>" 
                                            width="40" height="40" 
                                            alt="Profile" 
                                            class="rounded-circle me-2" 
                                            style="object-fit:cover;">

                                        <!-- Name & Role -->
                                        <div class="">
                                            <span class="fw-semibold p-0"><?php echo htmlspecialchars($userData['full_name']); ?></span>
                                        </div>

                                        <!-- Caret Icon -->
                                        <i class="fa fa-caret-down ms-2" aria-hidden="true"></i>
                                    </a>

                                    <div class="dropdown-menu dropdown-menu-end">
                                       <?php if ($isAdmin): ?>
                                        <a href="/?page=admin_dashboard" class="dropdown-item ai-icon">
                                            <svg id="icon-dashboard" xmlns="http://www.w3.org/2000/svg" 
                                                class="text-primary" width="18" height="18" 
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" 
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M3 13h8V3H3v10zM3 21h8v-6H3v6zM13 21h8V11h-8v10zM13 3v6h8V3h-8z"></path>
                                            </svg>
                                            <span class="ms-2">Dashboard</span>
                                          </a>
                                          <?php endif; ?>
                                        <a href="/?page=profile" class="dropdown-item ai-icon">
                                            <svg id="icon-user1" xmlns="http://www.w3.org/2000/svg" 
                                                class="text-primary" width="18" height="18" 
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" 
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                                <circle cx="12" cy="7" r="4"></circle>
                                            </svg>
                                            <span class="ms-2">Profile</span>
                                        </a>
                                        <a href="/?page=logout" class="dropdown-item ai-icon">
                                            <svg id="icon-logout" xmlns="http://www.w3.org/2000/svg" 
                                                class="text-danger" width="18" height="18" 
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" 
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                                <polyline points="16 17 21 12 16 7"></polyline>
                                                <line x1="21" y1="12" x2="9" y2="12"></line>
                                            </svg>
                                            <span class="ms-2">Logout</span>
                                        </a>
                                    </div>
                                </div>
                            <?php else: ?>
                              <a class="it-btn" href="/?page=contact">
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
                            <?php endif; ?>
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