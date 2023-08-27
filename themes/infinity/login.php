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
            <div class="mb-3">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="userAction" id="registerRadio" value="register" checked>
                    <label class="form-check-label" for="registerRadio">Register</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="userAction" id="loginRadio" value="login">
                    <label class="form-check-label" for="loginRadio">Login</label>
                </div>
            </div>

        </div>
    </div>
</div>

<?php
get_footer();
?>