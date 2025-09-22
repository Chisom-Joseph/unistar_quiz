<?php
// views/layouts/main.php
ob_start();
require_once 'config/constants.php';
require_once 'includes/auth.php';
require_once 'classes/User.php';

$user = new User();
$isLoggedIn = isset($_SESSION['user_id']);
$userData = null;
if ($user->isLoggedIn()) {
    try {
        $userData = $user->getUserData($_SESSION['user_id']);
    } catch (Exception $e) {
        error_log("Error fetching user data on home page: " . $e->getMessage());
    }
}
$isAdmin = $isLoggedIn && $userData['role'] === 'admin';
$currentPage = isset($_GET['page']) ? $_GET['page'] : ''; 

?>
<?php require_once 'views/partials/header.php'; ?>
<main>
<!-- Installation Success Modal -->
    <?php if (isset($_GET['message']) && $_GET['message'] === 'installed'): ?>
        <div class="modal fade" id="installSuccessModal" tabindex="-1" aria-labelledby="installSuccessModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="installSuccessModalLabel">Installation Successful</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-success mb-0" role="alert">
                            Admin account created: <strong>admin@quizapp.test</strong> / <strong>admin123</strong>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn it-btn large" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var installModal = new bootstrap.Modal(document.getElementById('installSuccessModal'), {
                    backdrop: true,
                    keyboard: true
                });
                installModal.show();
            });
        </script>
    <?php endif; ?>

      <!-- hero-area-start -->
      <div class="it-hero-3-area theme-bg-2">
         <div class="it-hero-3-shape-1">
            <img src="/public/img/hero/hero-3-shape1.png" alt="">
         </div>
         <div class="it-hero-3-shape-2">
            <img src="/public/img/hero/hero-3-shape2.png" alt="">
         </div>
         <div class="it-hero-3-shape-3 d-none d-lg-block">
            <img src="/public/img/hero/hero-3-shape3.png" alt="">
         </div>
         <div class="it-hero-3-shape-4 d-none d-xxl-block">
            <img src="/public/img/hero/hero-3-shape4.png" alt="">
         </div>
         <div class="it-hero-3-shape-5 d-none d-xxl-block">
            <img src="/public/img/hero/hero-3-shape5.png" alt="">
         </div>
         <div class="container">
            <div class="row align-items-end">
               <div class="col-xl-6">
                     <div class="it-hero-3-title-wrap it-hero-3-ptb">
                     <div class="it-hero-3-title-box">
                     <h1 class="it-hero-3-title">Challenge your mind with fun <span>quizzes.</span></h1>
                     <p>Test your knowledge, learn new facts, and track your progress <br>
                        with our interactive and engaging quiz platform.</p>
                     </div>

                     <div class="it-hero-3-btn-box d-flex align-items-center">
                     <a class="it-btn-white" href="/?page=login">
                        <span>
                           Take a Quiz
                           <svg width="17" height="14" viewBox="0 0 17 14" fill="none"
                           xmlns="http://www.w3.org/2000/svg">
                           <path d="M11 1.24023L16 7.24023L11 13.2402" stroke="currentcolor" stroke-width="1.5"
                              stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                           <path d="M1 7.24023H16" stroke="currentcolor" stroke-width="1.5" stroke-miterlimit="10"
                              stroke-linecap="round" stroke-linejoin="round" />
                           </svg>
                        </span>
                     </a>
                     <div class="it-hero-3-client-box d-flex align-items-center">
                        <span>Happy <br> Players</span>
                        <img src="/public/img/hero/hero-3-client-img.png" alt="">
                     </div>
                     </div>

                  </div>
               </div>
               <div class="col-xl-6">
                  <div class="it-hero-3-thumb">
                     <img src="/public/img/hero/hero-3-img.png" alt="">
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- hero-area-end -->

      <!-- about-area-start -->
      <div class="it-about-3-area pt-120 pb-120 p-relative">
         <div class="it-about-3-left-shape-4 d-none d-lg-block">
            <img src="/public/img/about/about-3-shap-4.png" alt="">
         </div>
         <div class="it-about-3-left-shape-5 d-none d-lg-block">
            <img src="/public/img/about/about-3-shap-5.png" alt="">
         </div>
         <div class="container">
            <div class="row g-0 align-items-center">
               <div class="col-xl-6 col-lg-6">
                  <div class="it-about-3-left-box text-end p-relative">
                     <div class="it-about-3-left-shape-1 d-none d-lg-block">
                        <img src="/public/img/about/about-3-shap-1.png" alt="">
                     </div>
                     <div class="it-about-3-left-shape-2 d-none d-lg-block">
                        <img src="/public/img/about/about-3-shap-2.png" alt="">
                     </div>
                     <div class="it-about-3-thumb">
                        <img src="/public/img/about/about-3-img.png" alt="">
                     </div>
                  </div>
               </div>
               <div class="col-xl-6 col-lg-6">
                  <div class="it-about-3-title-box">
                  <span class="it-section-subtitle-3">
                     <img src="/public/img/about/bg.svg" alt="">
                     about us
                  </span>
                  <h2 class="it-section-title-3 pb-30">
                     Boost your knowledge with <span>interactive quizzes</span>
                  </h2>
                  <p>
                     We make learning fun and engaging through well-crafted quizzes that test, 
                     challenge, and improve your understanding across different topics.
                  </p>
                  </div>

                  <div class="it-about-3-mv-box">
                  <div class="row">
                     <div class="col-xl-6 col-md-6">
                        <div class="it-about-3-mv-item">
                        <span class="it-about-3-mv-title">OUR MISSION:</span>
                        <p>
                           To make learning exciting by providing quizzes that inspire curiosity, 
                           sharpen knowledge, and encourage continuous growth.
                        </p>
                        </div>
                     </div>
                     <div class="col-xl-6 col-md-6">
                        <div class="it-about-3-mv-item">
                        <span class="it-about-3-mv-title">OUR VISION:</span>
                        <p>
                           To become the leading quiz platform where students, learners, and 
                           enthusiasts can test their skills, track progress, and enjoy learning 
                           through fun challenges.
                        </p>
                        </div>
                     </div>
                  </div>
                  </div>

                  <div class="it-about-3-btn-box p-relative">
                  <a class="it-btn-yellow" href="about-us.html">
                     <span>
                        Start Playing
                        <svg width="17" height="14" viewBox="0 0 17 14" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M11 1.24023L16 7.24023L11 13.2402" stroke="currentcolor" stroke-width="1.5"
                           stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M1 7.24023H16" stroke="currentcolor" stroke-width="1.5" stroke-miterlimit="10"
                           stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                     </span>
                  </a>
                  <div class="it-about-3-left-shape-3 d-none d-md-block">
                     <img src="/public/img/about/about-3-shap-3.png" alt="">
                  </div>
                  </div>

               </div>
            </div>
         </div>
      </div>
      <!-- about-area-end -->

      <!-- course-area-start -->
      <!---<div class="it-course-area it-course-style-3 it-course-bg p-relative grey-bg pt-120 pb-120"
         data-background="/public/img/course/course-bg.png">
         <div class="container">
            <div class="it-course-title-wrap mb-60">
               <div class="row align-items-end">
                  <div class="col-xl-7 col-lg-7 col-md-8">
                     <div class="it-course-title-box">
                        <span class="it-section-subtitle-3">
                           <img src="/public/img/about/bg.svg" alt="">
                           Top Popular Course
                        </span>
                        <h4 class="it-section-title-3">Check out educate features <br> win any exam</h4>
                     </div>
                  </div>
                  <div class="col-xl-5 col-lg-5 col-md-4">
                     <div class="it-course-button text-start text-md-end pt-25">
                        <a class="it-btn-theme-2" href="#">
                           <span>
                              Load More Course
                              <svg width="17" height="14" viewBox="0 0 17 14" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                 <path d="M11 1.24023L16 7.24023L11 13.2402" stroke="currentcolor" stroke-width="1.5"
                                    stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                 <path d="M1 7.24023H16" stroke="currentcolor" stroke-width="1.5" stroke-miterlimit="10"
                                    stroke-linecap="round" stroke-linejoin="round" />
                              </svg>
                           </span>
                        </a>
                     </div>
                  </div>
               </div>
            </div>
            <div class="row">
               <div class="col-xl-4 col-lg-4 col-md-6 mb-30">
                  <div class="it-course-item">
                     <div class="it-course-thumb mb-20 p-relative">
                        <a href="course-details.html"><img src="/public/img/course/course-1-1.jpg" alt=""></a>
                        <div class="it-course-thumb-text">
                           <span>Development</span>
                        </div>
                     </div>
                     <div class="it-course-content">
                        <div class="it-course-rating mb-10">
                           <i class="fa-sharp fa-solid fa-star"></i>
                           <i class="fa-sharp fa-solid fa-star"></i>
                           <i class="fa-sharp fa-solid fa-star"></i>
                           <i class="fa-sharp fa-solid fa-star"></i>
                           <i class="fa-sharp fa-regular fa-star"></i>
                           <span>(4.7)</span>
                        </div>
                        <h4 class="it-course-title pb-5"><a href="course-details.html">It statistics data science and Business
                              analysis</a></h4>
                        <div class="it-course-info pb-15 mb-25 d-flex justify-content-between">
                           <span><i class="fa-light fa-file-invoice"></i>Lesson 10</span>
                           <span><i class="fa-sharp fa-regular fa-clock"></i>19h 30m</span>
                           <span><i class="fa-light fa-user"></i>Students 20+</span>
                        </div>
                        <div class="it-course-author pb-15">
                           <img src="/public/img/course/avata-1.png" alt="">
                           <span>By <i>Angela</i> in <i>Development</i></span>
                        </div>
                        <div class="it-course-price-box d-flex justify-content-between">
                           <span><i>$60</i> $120</span>
                           <a href="cart.html"><i class="fa-light fa-cart-shopping"></i>Add to cart</a>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="col-xl-4 col-lg-4 col-md-6 mb-30">
                  <div class="it-course-item">
                     <div class="it-course-thumb mb-20 p-relative">
                        <a href="course-details.html"><img src="/public/img/course/course-1-2.jpg" alt=""></a>
                        <div class="it-course-thumb-text">
                           <span>Development</span>
                        </div>
                     </div>
                     <div class="it-course-content">
                        <div class="it-course-rating mb-10">
                           <i class="fa-sharp fa-solid fa-star"></i>
                           <i class="fa-sharp fa-solid fa-star"></i>
                           <i class="fa-sharp fa-solid fa-star"></i>
                           <i class="fa-sharp fa-solid fa-star"></i>
                           <i class="fa-sharp fa-regular fa-star"></i>
                           <span>(4.7)</span>
                        </div>
                        <h4 class="it-course-title pb-5"><a href="course-details.html">It statistics data science and Business
                              analysis</a></h4>
                        <div class="it-course-info pb-15 mb-25 d-flex justify-content-between">
                           <span><i class="fa-light fa-file-invoice"></i>Lesson 10</span>
                           <span><i class="fa-sharp fa-regular fa-clock"></i>19h 30m</span>
                           <span><i class="fa-light fa-user"></i>Students 20+</span>
                        </div>
                        <div class="it-course-author pb-15">
                           <img src="/public/img/course/avata-1.png" alt="">
                           <span>By <i>Angela</i> in <i>Development</i></span>
                        </div>
                        <div class="it-course-price-box d-flex justify-content-between">
                           <span><i>$60</i> $120</span>
                           <a href="cart.html"><i class="fa-light fa-cart-shopping"></i>Add to cart</a>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="col-xl-4 col-lg-4 col-md-6 mb-30">
                  <div class="it-course-item">
                     <div class="it-course-thumb mb-20 p-relative">
                        <a href="course-details.html"><img src="/public/img/course/course-1-3.jpg" alt=""></a>
                        <div class="it-course-thumb-text">
                           <span>Development</span>
                        </div>
                     </div>
                     <div class="it-course-content">
                        <div class="it-course-rating mb-10">
                           <i class="fa-sharp fa-solid fa-star"></i>
                           <i class="fa-sharp fa-solid fa-star"></i>
                           <i class="fa-sharp fa-solid fa-star"></i>
                           <i class="fa-sharp fa-solid fa-star"></i>
                           <i class="fa-sharp fa-regular fa-star"></i>
                           <span>(4.7)</span>
                        </div>
                        <h4 class="it-course-title pb-5"><a href="course-details.html">Bilginer Adobe Illustrator for
                              Graphic Design</a></h4>
                        <div class="it-course-info pb-15 mb-25 d-flex justify-content-between">
                           <span><i class="fa-light fa-file-invoice"></i>Lesson 10</span>
                           <span><i class="fa-sharp fa-regular fa-clock"></i>19h 30m</span>
                           <span><i class="fa-light fa-user"></i>Students 20+</span>
                        </div>
                        <div class="it-course-author pb-15">
                           <img src="/public/img/course/avata-1.png" alt="">
                           <span>By <i>Angela</i> in <i>Development</i></span>
                        </div>
                        <div class="it-course-price-box d-flex justify-content-between">
                           <span><i>$60</i> $120</span>
                           <a href="cart.html"><i class="fa-light fa-cart-shopping"></i>Add to cart</a>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>-->
      <!-- course-area-end -->

      <!-- valu-area-start -->
      <div class="it-value-area pt-120 pb-120 p-relative fix">
         <div class="it-value-shape-1 d-none d-xxl-block">
            <img src="/public/img/value/value-shape-3.jpg" alt="">
         </div>
         <div class="it-value-shape-2 d-none d-xl-block">
            <img src="/public/img/value/value-shape-4.jpg" alt="">
         </div>
         <div class="container">
            <div class="row align-items-center">
               <div class="col-xl-6 col-lg-6">
                  <div class="it-value-title-box">
                     <span class="it-section-subtitle-3">
                        <img src="/public/img/about/bg.svg" alt="">
                        Top Popular Quiz
                     </span>
                     <h4 class="it-section-title-3 pb-25">Our <span>Quiz</span> is very
                        <span> different</span> than all others</h4>
                     <p>Our quiz are tailored to help you prepare for exams and pass with ease and less stress.</p>
                  </div>
                  <div class="it-progress-bar-wrap">
                     <div class="it-progress-bar-item">
                        <label>Case study success</label>
                        <div class="it-progress-bar">
                           <div class="progress">
                              <div class="progress-bar wow slideInLeft" data-wow-delay=".1s" data-wow-duration="2s"
                                 role="progressbar" data-width="90%" aria-valuenow="90" aria-valuemin="0"
                                 aria-valuemax="100"
                                 style="width: 90%; visibility: visible; animation-duration: 2s; animation-delay: 0.1s; animation-name: slideInLeft;">
                                 <span>90%</span>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="it-progress-bar-item">
                        <label>Happy student</label>
                        <div class="it-progress-bar">
                           <div class="progress">
                              <div class="progress-bar wow slideInLeft" data-wow-delay=".1s" data-wow-duration="2s"
                                 role="progressbar" data-width="82%" aria-valuenow="82" aria-valuemin="0"
                                 aria-valuemax="100"
                                 style="width: 82%; visibility: visible; animation-duration: 2s; animation-delay: 0.1s; animation-name: slideInLeft;">
                                 <span>82%</span>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="it-progress-bar-item">
                        <label>Engaging</label>
                        <div class="it-progress-bar">
                           <div class="progress">
                              <div class="progress-bar wow slideInLeft" data-wow-delay=".1s" data-wow-duration="2s"
                                 role="progressbar" data-width="65%" aria-valuenow="58" aria-valuemin="0"
                                 aria-valuemax="100"
                                 style="width: 65%; visibility: visible; animation-duration: 2s; animation-delay: 0.1s; animation-name: slideInLeft;">
                                 <span>65%</span>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="it-progress-bar-item">
                        <label>Student Community</label>
                        <div class="it-progress-bar">
                           <div class="progress">
                              <div class="progress-bar wow slideInLeft" data-wow-delay=".1s" data-wow-duration="2s"
                                 role="progressbar" data-width="58%" aria-valuenow="58" aria-valuemin="0"
                                 aria-valuemax="100"
                                 style="width: 58%; visibility: visible; animation-duration: 2s; animation-delay: 0.1s; animation-name: slideInLeft;">
                                 <span>98%</span>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="col-xl-6 col-lg-6">
                  <div class="it-value-right-wrap text-end p-relative">
                     <div class="it-value-right-img p-relative">
                        <img src="/public/img/value/value-1.jpg" alt="">
                        <a class="it-value-play-btn" href="#"><i class="fa-sharp fa-solid fa-play"></i></a>
                     </div>
                     <div class="it-value-img-shape d-none d-xl-block">
                        <img src="/public/img/value/value-shape-2.jpg" alt="">
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- valu-area-end -->

      <!-- feature-area-start -->
      <div class="it-feature-3-area it-feature-3-bg grey-bg pt-120 pb-90"
         data-background="/public/img/feature/feature-bg.png">
         <div class="container">
            <div class="row justify-content-center">
               <div class="col-xl-8 col-lg-7 col-md-8">
                  <div class="it-feature-3-title-box text-center mb-60">
                     <span class="it-section-subtitle-3">
                        <img src="/public/img/about/bg.svg" alt="">
                        UNISTAR FEATURE
                        <img src="/public/img/about/bg.svg" alt="">
                     </span>
                     <h4 class="it-section-title-3">Check out quiz features <br> win any exam</h4>
                  </div>
               </div>
            </div>
            <div class="row">
               <div class="col-xl-3 col-lg-6 col-md-6">
                  <div class="it-feature-3-item mb-30 text-center">
                     <div class="it-feature-3-icon">
                        <span><i class="flaticon-coach"></i></span>
                     </div>
                     <div class="it-feature-3-content">
                        <h4 class="it-feature-3-title"><a href="service-details.html">Best Coaching</a></h4>
                        <p>Get expert-designed study guides and resources that make learning easy, fun, and effective for all exams..</p>
                     </div>
                     <div class="it-feature-3-btn">
                        <a class="it-btn-theme-sm" href="#">
                           <span>
                              view details
                              <svg width="17" height="14" viewBox="0 0 17 14" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                 <path d="M11 1.24023L16 7.24023L11 13.2402" stroke="currentcolor" stroke-width="1.5"
                                    stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                 <path d="M1 7.24023H16" stroke="currentcolor" stroke-width="1.5"
                                    stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                              </svg>
                           </span>
                        </a>
                     </div>
                  </div>
               </div>
               <div class="col-xl-3 col-lg-6 col-md-6">
                  <div class="it-feature-3-item mb-30 text-center">
                     <div class="it-feature-3-icon">
                        <span><i class="flaticon-study"></i></span>
                     </div>
                     <div class="it-feature-3-content">
                        <h4 class="it-feature-3-title"><a href="service-details.html">Smart Practice</a></h4>
                        <p>Access a wide range of quizzes and mock tests to sharpen your knowledge and boost your confidence.</p>
                     </div>
                     <div class="it-feature-3-btn">
                        <a class="it-btn-theme-sm" href="#">
                           <span>
                              view details
                              <svg width="17" height="14" viewBox="0 0 17 14" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                 <path d="M11 1.24023L16 7.24023L11 13.2402" stroke="currentcolor" stroke-width="1.5"
                                    stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                 <path d="M1 7.24023H16" stroke="currentcolor" stroke-width="1.5"
                                    stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                              </svg>
                           </span>
                        </a>
                     </div>
                  </div>
               </div>
               <div class="col-xl-3 col-lg-6 col-md-6">
                  <div class="it-feature-3-item mb-30 text-center">
                     <div class="it-feature-3-icon">
                        <span><i class="flaticon-booking"></i></span>
                     </div>
                     <div class="it-feature-3-content">
                        <h4 class="it-feature-3-title"><a href="service-details.html">Exam Strategies</a></h4>
                        <p>Learn proven tips, tricks, and strategies to excel in competitive tests and achieve top results.</p>
                     </div>
                     <div class="it-feature-3-btn">
                        <a class="it-btn-theme-sm" href="#">
                           <span>
                              view details
                              <svg width="17" height="14" viewBox="0 0 17 14" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                 <path d="M11 1.24023L16 7.24023L11 13.2402" stroke="currentcolor" stroke-width="1.5"
                                    stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                 <path d="M1 7.24023H16" stroke="currentcolor" stroke-width="1.5"
                                    stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                              </svg>
                           </span>
                        </a>
                     </div>
                  </div>
               </div>
               <div class="col-xl-3 col-lg-6 col-md-6">
                  <div class="it-feature-3-item mb-30 text-center">
                     <div class="it-feature-3-icon">
                        <span><i class="flaticon-video"></i></span>
                     </div>
                     <div class="it-feature-3-content">
                        <h4 class="it-feature-3-title"><a href="service-details.html">Guaranteed Growth</a></h4>
                        <p>Track your progress, identify your strengths, and improve faster with personalized learning tools.</p>
                     </div>
                     <div class="it-feature-3-btn">
                        <a class="it-btn-theme-sm" href="#">
                           <span>
                              view details
                              <svg width="17" height="14" viewBox="0 0 17 14" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                 <path d="M11 1.24023L16 7.24023L11 13.2402" stroke="currentcolor" stroke-width="1.5"
                                    stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                 <path d="M1 7.24023H16" stroke="currentcolor" stroke-width="1.5"
                                    stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                              </svg>
                           </span>
                        </a>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- feature-area-end -->

      <!-- video-area-start -->
      <div class="it-video-area it-video-bg it-video-color p-relative fix pt-100 pb-95"
         data-background="/public/img/video/bg-1-1.jpg">
         <div class="it-video-shape-1 d-none d-lg-block">
            <img src="/public/img/video/shape-1-1.png" alt="">
         </div>
         <div class="it-video-shape-2 d-none d-lg-block">
            <img src="/public/img/video/shape-1-8.png" alt="">
         </div>
         <div class="it-video-shape-3 d-none d-xl-block">
            <img src="/public/img/video/shape-1-3.png" alt="">
         </div>
         <div class="it-video-shape-4 d-none d-lg-block">
            <img src="/public/img/video/shape-1-4.png" alt="">
         </div>
         <div class="it-video-shape-5 d-none d-lg-block">
            <img src="/public/img/video/shape-1-5.png" alt="">
         </div>
         <div class="container">
            <div class="row align-items-center">
               <div class="col-xl-7 col-lg-7 col-md-9 col-sm-9">
                  <div class="it-video-content">
                     <span>Join Our New Session</span>
                     <h3 class="it-video-title">Call To Enroll Your Child <br> <a
                           href="tel:+91958423452">(+91)958423452</a></h3>
                     <div class="it-video-button">
                        <a class="it-btn-theme-2" href="#">
                           <span>
                              Join With us
                              <svg width="17" height="14" viewBox="0 0 17 14" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                 <path d="M11 1.24023L16 7.24023L11 13.2402" stroke="currentcolor" stroke-width="1.5"
                                    stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                 <path d="M1 7.24023H16" stroke="currentcolor" stroke-width="1.5"
                                    stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                              </svg>
                           </span>
                        </a>
                     </div>
                  </div>
               </div>
               <div class="col-xl-5 col-lg-5 col-md-3 col-sm-3">
                  <div
                     class="it-video-play-wrap d-flex justify-content-start justify-content-md-end align-items-center">
                     <div class="it-video-play text-center">
                        <a class="popup-video play" href="https://www.youtube.com/watch?v=PO_fBTkoznc"><i
                              class="fas fa-play"></i></a>
                        <a class="text" href="#">watch now</a>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- video-area-end -->

      <!-- career-area-start -->
      <div class="it-career-area it-career-style-3 it-career-bg p-relative pb-100 pt-120">
         <div class="it-career-shape-3 d-none d-xl-block">
            <img src="/public/img/career/shape-1-2.png" alt="">
         </div>
         <div class="it-career-shape-6 d-none d-xl-block">
            <img src="/public/img/career/shape-1-5.png" alt="">
         </div>
         <div class="container">
            <div class="row justify-content-center">
               <div class="col-xl-8">
                  <div class="it-course-title-box text-center mb-60">
                     <span class="it-section-subtitle-3">
                        <img src="/public/img/about/bg.svg" alt="">
                        Top Popular Course
                        <img src="/public/img/about/bg.svg" alt="">
                     </span>
                     <h4 class="it-section-title-3">Annual Exam Preparation</h4>
                  </div>
               </div>
            </div>
            <div class="row">
               <div class="col-xl-6 col-lg-6 mb-30">
                  <div class="it-career-item p-relative fix">
                     <div class="it-career-content">
                        <span>Medical Exam</span>
                        <p>Lorem ipsum dolor sit amet, consectetur
                           adipiscing elit sed.</p>
                        <a class="it-btn-yellow mr-15" href="#">
                           <span>
                              Join now
                              <svg width="17" height="14" viewBox="0 0 17 14" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                 <path d="M11 1.24023L16 7.24023L11 13.2402" stroke="currentcolor" stroke-width="1.5"
                                    stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                 <path d="M1 7.24023H16" stroke="currentcolor" stroke-width="1.5"
                                    stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                              </svg>
                           </span>
                        </a>
                     </div>
                     <div class="it-career-thumb">
                        <img src="/public/img/career/thumb-1.png" alt="">
                     </div>
                     <div class="it-career-shape-1">
                        <img src="/public/img/career/shape-1.png" alt="">
                     </div>
                  </div>
               </div>
               <div class="col-xl-6 col-lg-6 mb-30">
                  <div class="it-career-item p-relative fix">
                     <div class="it-career-content">
                        <span>BCS Exam</span>
                        <p>Lorem ipsum dolor sit amet, consectetur
                           adipiscing elit sed.</p>
                        <a class="it-btn-yellow mr-15" href="#">
                           <span>
                              Join now
                              <svg width="17" height="14" viewBox="0 0 17 14" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                 <path d="M11 1.24023L16 7.24023L11 13.2402" stroke="currentcolor" stroke-width="1.5"
                                    stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                 <path d="M1 7.24023H16" stroke="currentcolor" stroke-width="1.5"
                                    stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                              </svg>
                           </span>
                        </a>
                     </div>
                     <div class="it-career-thumb">
                        <img src="/public/img/career/thumb-2.png" alt="">
                     </div>
                     <div class="it-career-shape-1">
                        <img src="/public/img/career/shape-1.png" alt="">
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- career-area-end -->

      <!-- testimonial-area-start -->
      <div class="it-testimonial-3-area" data-background="/public/img/testimonial/bg-2.png">
         <div class="container">
            <div class="row align-items-center">
               <div class="col-xl-5 col-lg-4 d-none d-lg-block">
                  <div class="it-testimonial-3-thumb">
                     <img src="/public/img/testimonial/thumb-2.png" alt="">
                  </div>
               </div>
               <div class="col-xl-7 col-lg-8">
                  <div class="it-testimonial-3-box z-index p-relative">
                     <div class="it-testimonial-3-shape-1">
                        <img src="/public/img/testimonial/shape-3-1.png" alt="">
                     </div>
                     <div class="it-testimonial-3-wrapper p-relative"
                        data-background="/public/img/testimonial/bg-3.png">
                        <div class="it-testimonial-3-quote d-none d-md-block">
                           <img src="/public/img/testimonial/quot.png" alt="">
                        </div>
                        <div class="swiper-container it-testimonial-3-active">
                           <div class="swiper-wrapper">
                              <div class="swiper-slide">
                                 <div class="it-testimonial-3-item">
                                    <div class="it-testimonial-3-content">
                                       <p>Unistar is the best exam prep tool I’ve ever used. The study resources and strategies gave me an edge, and I finally achieved the grades I was aiming for.</p>
                                       <div class="it-testimonial-3-author-box d-flex align-items-center">
                                          <div class="it-testimonial-3-avata">
                                             <img src="/public/img/avatar/avatar-3-1.png" alt="">
                                          </div>
                                          <div class="it-testimonial-3-author-info">
                                             <h5>Emeka Nwankwo</h5>
                                             <span>Law Student</span>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                              <div class="swiper-slide">
                                 <div class="it-testimonial-3-item">
                                    <div class="it-testimonial-3-content">
                                       <p>Unistar made my MBBS exams so much easier. The quizzes helped me practice daily, and I went into my exams with full confidence. I passed with excellent results!</p>
                                       <div class="it-testimonial-3-author-box d-flex align-items-center">
                                          <div class="it-testimonial-3-avata">
                                             <img src="/public/img/avatar/avatar-2.png" alt="">
                                          </div>
                                          <div class="it-testimonial-3-author-info">
                                             <h5>Chiamaka Okafor</h5>
                                             <span>Medical Student</span>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                              <div class="swiper-slide">
                                 <div class="it-testimonial-3-item">
                                    <div class="it-testimonial-3-content">
                                       <p>Before Unistar, I struggled with time management in tests. The mock exams helped me master speed and accuracy. Now, I feel more prepared than ever</p>
                                       <div class="it-testimonial-3-author-box d-flex align-items-center">
                                          <div class="it-testimonial-3-avata">
                                             <img src="/public/img/avatar/avatar-1.png" alt="">
                                          </div>
                                          <div class="it-testimonial-3-author-info">
                                             <h5>Tunde Adebayo</h5>
                                             <span>Computer Science Student</span>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="test-slider-dots d-none d-sm-block"></div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- testimonial-area-end -->

      <!-- event-area-start -->
      <div class="it-event-area p-relative pt-120 pb-120">
         <div class="it-event-shape-3 d-none d-xxl-block">
            <img src="/public/img/event/shape-1-1.png" alt="">
         </div>
         <div class="it-event-shape-4 d-none d-xl-block">
            <img src="/public/img/event/shape-1-2.png" alt="">
         </div>
         <div class="it-event-shape-5">
            <img src="/public/img/event/shape-1-3.png" alt="">
         </div>
         <div class="it-event-shape-6">
            <img src="/public/img/event/shape-1-4.png" alt="">
         </div>
         <div class="container">
            <div class="row align-items-center">
               <div class="col-xl-7 col-lg-7">
                  <div class="it-event-left">
                     <div class="it-event-title-box">
                        <span class="it-section-subtitle-3">
                           <img src="/public/img/about/bg.svg" alt="">
                           explore Events
                        </span>
                        <h2 class="it-section-title-3 pb-20">our upcoming quiz competition events</h2>
                     </div>
                     <div class="it-event-content">
                        <span>Dont miss any quiz competition.</span>
                        <p>Get ready to test your knowledge, challenge your peers, and win exciting prizes! At Unistar, we organize interactive quiz competitions designed to sharpen your skills, boost confidence, and make learning fun. Whether you’re preparing for school exams, professional tests, or just love a good challenge, our events are the perfect opportunity to showcase your brilliance and grow.</p>
                        <a class="it-btn-theme-2" href="/?page=register">
                           <span>Register Now
                              <svg width="17" height="14" viewBox="0 0 17 14" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                 <path d="M11 1.24023L16 7.24023L11 13.2402" stroke="currentcolor" stroke-width="1.5"
                                    stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                 <path d="M1 7.24023H16" stroke="currentcolor" stroke-width="1.5"
                                    stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                              </svg>
                           </span>
                        </a>
                     </div>
                  </div>
               </div>
               <div class="col-xl-5 col-lg-5">
                  <div class="it-event-thumb-box text-center text-lg-start p-relative">
                     <div class="it-event-shape-1 d-none d-lg-block">
                        <img src="/public/img/event/shape-1-5.png" alt="">
                     </div>
                     <div class="it-event-shape-2">
                        <img src="/public/img/event/shape-1-6.png" alt="">
                     </div>
                     <div class="it-event-thumb">
                        <img src="/public/img/event/thumb-1.png" alt="">
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- event-area-end -->

      <!-- team-area-start -->
      <!---<div class="it-team-3-area p-relative z-index pt-110 pb-90">
         <div class="it-team-3-bg" data-background="/public/img/team/bg-3.png"></div>
         <div class="container">
            <div class="row">
               <div class="col-xl-12">
                  <div class="it-event-title-box text-center pb-40">
                     <span class="it-section-subtitle-3 text-white">
                        <img src="/public/img/about/bg-2.svg" alt="">
                        Teacher
                        <img src="/public/img/about/bg-2.svg" alt="">
                     </span>
                     <h2 class="it-section-title-3 text-white">meet our expert Instructor</h2>
                  </div>
               </div>
            </div>
            <div class="row">
               <div class="col-xl-3 col-lg-4 col-md-6 mb-30">
                  <div class="it-team-3-item text-center">
                     <div class="it-team-3-thumb fix">
                        <img src="/public/img/team/team-3-1.jpg" alt="">
                     </div>
                     <div class="it-team-3-content">
                        <div class="it-team-3-social-box p-relative">
                           <button>
                              <i class="fa-light fa-share-nodes"></i>
                           </button>
                           <div class="it-team-3-social-wrap">
                              <a href="#"><i class="fa-brands fa-instagram"></i></a>
                              <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                              <a href="#"><i class="fa-brands fa-pinterest-p"></i></a>
                              <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
                           </div>
                        </div>
                        <div class="it-team-3-author-box">
                           <h4 class="it-team-3-title"><a href="teacher-details.html">Nathan Allen</a></h4>
                           <span>Teacher</span>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="col-xl-3 col-lg-4 col-md-6 mb-30">
                  <div class="it-team-3-item text-center">
                     <div class="it-team-3-thumb fix">
                        <img src="/public/img/team/team-3-2.jpg" alt="">
                     </div>
                     <div class="it-team-3-content">
                        <div class="it-team-3-social-box p-relative">
                           <button>
                              <i class="fa-light fa-share-nodes"></i>
                           </button>
                           <div class="it-team-3-social-wrap">
                              <a href="#"><i class="fa-brands fa-instagram"></i></a>
                              <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                              <a href="#"><i class="fa-brands fa-pinterest-p"></i></a>
                              <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
                           </div>
                        </div>
                        <div class="it-team-3-author-box">
                           <h4 class="it-team-3-title"><a href="teacher-details.html">Esther Boyd</a></h4>
                           <span>Teacher</span>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="col-xl-3 col-lg-4 col-md-6 mb-30">
                  <div class="it-team-3-item text-center">
                     <div class="it-team-3-thumb fix">
                        <img src="/public/img/team/team-3-3.jpg" alt="">
                     </div>
                     <div class="it-team-3-content">
                        <div class="it-team-3-social-box p-relative">
                           <button>
                              <i class="fa-light fa-share-nodes"></i>
                           </button>
                           <div class="it-team-3-social-wrap">
                              <a href="#"><i class="fa-brands fa-instagram"></i></a>
                              <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                              <a href="#"><i class="fa-brands fa-pinterest-p"></i></a>
                              <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
                           </div>
                        </div>
                        <div class="it-team-3-author-box">
                           <h4 class="it-team-3-title"><a href="teacher-details.html">Jamie Keller</a></h4>
                           <span>Teacher</span>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="col-xl-3 col-lg-4 col-md-6 mb-30">
                  <div class="it-team-3-item text-center">
                     <div class="it-team-3-thumb fix">
                        <img src="/public/img/team/team-3-4.jpg" alt="">
                     </div>
                     <div class="it-team-3-content">
                        <div class="it-team-3-social-box p-relative">
                           <button>
                              <i class="fa-light fa-share-nodes"></i>
                           </button>
                           <div class="it-team-3-social-wrap">
                              <a href="#"><i class="fa-brands fa-instagram"></i></a>
                              <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                              <a href="#"><i class="fa-brands fa-pinterest-p"></i></a>
                              <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
                           </div>
                        </div>
                        <div class="it-team-3-author-box">
                           <h4 class="it-team-3-title"><a href="teacher-details.html">Jesus Pendley</a></h4>
                           <span>Teacher</span>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>--->
      <!-- team-area-end -->

      <!-- blog-area-start -->
      <!---- <div class="it-blog-area it-blog-color pb-90">
         <div class="container">
            <div class="it-blog-title-wrap mb-80">
               <div class="row align-items-end">
                  <div class="col-xl-7 col-lg-7 col-md-8">
                     <div class="it-course-title-box">
                        <span class="it-section-subtitle-3">
                           <img src="/public/img/about/bg.svg" alt="">
                           Top Popular Course
                        </span>
                        <h4 class="it-section-title-3">Check out educate features <br> win any exam</h4>
                     </div>
                  </div>
                  <div class="col-xl-5 col-lg-5 col-md-4">
                     <div class="it-course-button text-start text-md-end pt-25">
                        <a class="it-btn-theme-2" href="blog-2.html">
                           <span>
                              all blog post
                              <svg width="17" height="14" viewBox="0 0 17 14" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                 <path d="M11 1.24023L16 7.24023L11 13.2402" stroke="currentcolor" stroke-width="1.5"
                                    stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                 <path d="M1 7.24023H16" stroke="currentcolor" stroke-width="1.5"
                                    stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                              </svg>
                           </span>
                        </a>
                     </div>
                  </div>
               </div>
            </div>
            <div class="row">
               <div class="col-xl-4 col-lg-4 col-md-6 mb-30">
                  <div class="it-blog-item-box" data-background="/public/img/blog/bg-1.jpg">
                     <div class="it-blog-item">
                        <div class="it-blog-thumb fix">
                           <a href="blog-details.html"><img src="/public/img/blog/blog-1-1.jpg" alt=""></a>
                        </div>
                        <div class="it-blog-meta pb-15">
                           <span>
                              <i class="fa-solid fa-calendar-days"></i>
                              14 June 2023</span>
                           <span>
                              <i class="fa-light fa-messages"></i>
                              Comment (06)</span>
                        </div>
                        <h4 class="it-blog-title"><a href="blog-details.html">velit esse cillum dolore eu fugiat
                              nulla pariatur. Excepteur sint occaecat cupidatat</a></h4>
                        <a class="it-btn-theme-sm" href="blog-details.html">
                           <span>
                              read more
                              <svg width="17" height="14" viewBox="0 0 17 14" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                 <path d="M11 1.24023L16 7.24023L11 13.2402" stroke="currentcolor" stroke-width="1.5"
                                    stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                 <path d="M1 7.24023H16" stroke="currentcolor" stroke-width="1.5"
                                    stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                              </svg>
                           </span>
                        </a>
                     </div>
                  </div>
               </div>
               <div class="col-xl-4 col-lg-4 col-md-6 mb-30">
                  <div class="it-blog-item-box" data-background="/public/img/blog/bg-1.jpg">
                     <div class="it-blog-item">
                        <div class="it-blog-thumb fix">
                           <a href="blog-details.html"><img src="/public/img/blog/blog-1-2.jpg" alt=""></a>
                        </div>
                        <div class="it-blog-meta pb-15">
                           <span>
                              <i class="fa-solid fa-calendar-days"></i>
                              14 June 2023</span>
                           <span>
                              <i class="fa-light fa-messages"></i>
                              Comment (06)</span>
                        </div>
                        <h4 class="it-blog-title"><a href="blog-details.html">velit esse cillum dolore eu fugiat
                              nulla pariatur. Excepteur sint occaecat cupidatat</a></h4>
                        <a class="it-btn-theme-sm" href="blog-details.html">
                           <span>
                              read more
                              <svg width="17" height="14" viewBox="0 0 17 14" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                 <path d="M11 1.24023L16 7.24023L11 13.2402" stroke="currentcolor" stroke-width="1.5"
                                    stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                 <path d="M1 7.24023H16" stroke="currentcolor" stroke-width="1.5"
                                    stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                              </svg>
                           </span>
                        </a>
                     </div>
                  </div>
               </div>
               <div class="col-xl-4 col-lg-4 col-md-6 mb-30">
                  <div class="it-blog-item-box" data-background="/public/img/blog/bg-1.jpg">
                     <div class="it-blog-item">
                        <div class="it-blog-thumb fix">
                           <a href="blog-details.html"><img src="/public/img/blog/blog-1-3.jpg" alt=""></a>
                        </div>
                        <div class="it-blog-meta pb-15">
                           <span>
                              <i class="fa-solid fa-calendar-days"></i>
                              14 June 2023</span>
                           <span>
                              <i class="fa-light fa-messages"></i>
                              Comment (06)</span>
                        </div>
                        <h4 class="it-blog-title"><a href="blog-details.html">velit esse cillum dolore eu fugiat
                              nulla pariatur. Excepteur sint occaecat cupidatat</a></h4>
                        <a class="it-btn-theme-sm" href="blog-details.html">
                           <span>
                              read more
                              <svg width="17" height="14" viewBox="0 0 17 14" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                 <path d="M11 1.24023L16 7.24023L11 13.2402" stroke="currentcolor" stroke-width="1.5"
                                    stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                 <path d="M1 7.24023H16" stroke="currentcolor" stroke-width="1.5"
                                    stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                              </svg>
                           </span>
                        </a>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>-->
      <!-- blog-area-end -->


   </main>
<?php require_once 'views/partials/footer.php'; ?>