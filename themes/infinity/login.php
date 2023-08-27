<?php
/*
Template Name: Login
*/
ob_start();
get_header();
?>

<br>
<br>
<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="sideBlock bg-white p-3">
                <ul class="list-unstyled">
                    <li class="active"><a href='<?php echo esc_url(home_url('/login')); ?>'>Login</a></li>
                    <li><a href='<?php echo esc_url(home_url('/register')); ?>'>Register</a></li>

                </ul>
            </div>
        </div>
        <div class="col-md-8">
            <div class="loginSection mb-3">
                <h3 class="mb-3">Login</h3>
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="usernameLogin">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="passwordLogin">
                </div>
            </div>

            <!-- Place Order Button -->
            <div class="OrderPlaceholder">
                <button type="submit" class="btn btn-primary OrderButton">Login</button>
            </div>
        </div>
    </div>
</div>

<?php
get_footer();
?>