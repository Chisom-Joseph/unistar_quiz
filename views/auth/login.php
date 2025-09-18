<?php
// views/auth/login.php
ob_start();
require_once 'config/constants.php';
require_once 'includes/auth.php';
error_log("Loading login page, error=" . ($_GET['error'] ?? 'none') . ", session_id=" . session_id());
?>
<main>


       <!-- signup-area-start -->
       <div class="it-signup-area pt-120 pb-120">
         <div class="container">
            <div class="it-signup-bg p-relative">
               <div class="it-signup-thumb d-none d-lg-block">
                  <img src="/public/img/contact/signup.jpg" alt="">
               </div>
               <div class="row">
                  <div class="col-xl-6 col-lg-6">
                     <form method="POST" action="?page=login">
                        <div class="it-signup-wrap">
                            <h4 class="it-signup-title">sign In</h4>
                                <?php if (isset($error)): ?>
                                    <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error); ?></div>
                                <?php elseif (isset($_GET['message']) && $_GET['message'] === 'logged_out'): ?>
                                    <div class="alert alert-success" role="alert">You have been logged out successfully.</div>
                                <?php elseif (isset($_GET['error']) && $_GET['error'] === 'not_logged_in'): ?>
                                    <div class="alert alert-danger" role="alert">Please log in to access the dashboard.</div>
                                <?php endif; ?>
                           <div class="it-signup-input-wrap">
                            <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                              <div class="it-signup-input mb-20">
                                 <input type="email" placeholder="Email *" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                              </div>
                              <div class="it-signup-input mb-20">
                                 <input type="password" placeholder="Password *"name="password" required>
                              </div>
                           </div>
                           <div class="it-signup-forget d-flex justify-content-between flex-wrap">
                              <a class="mb-20" href="?page=forgot">Forgot Password?</a>
                              <div class="it-signup-agree mb-20">
                                 <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                                    <label class="form-check-label" for="flexCheckDefault">
                                       Remember me
                                    </label>
                                 </div>
                              </div>
                           </div>
                           <div class="it-signup-btn mb-40">
                              <button class="it-btn large">sign In</button>
                           </div>
                           <div class="it-signup-text">
                              <span>Don't have an account?<a href="?page=register">Sign Up</a></span>
                           </div>
                        </div>
                     </form>
                  </div>
               </div>
            </div>
         </div>
       </div>
      <!-- signup-area-end -->

   </main>
<?php
$content = ob_get_clean();
include 'views/layouts/main.php';
?>