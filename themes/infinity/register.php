<?php
/*
Template Name: Register
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
                    <li><a href='<?php echo esc_url(home_url('/login')); ?>'>Login</a></li>
                    <li class="active"><a href='<?php echo esc_url(home_url('/register')); ?>'>Register</a></li>

                </ul>
            </div>
        </div>
        <div class="col-md-8">
            <div class="registrationSection mb-3">
                <h3 class="mb-3">Registration</h3>
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="text" class="form-control" id="email" name="email">
                </div>
            </div>


            <!-- Place Order Button -->
            <div class="OrderPlaceholder">
                <button type="submit" class="btn btn-primary OrderButton">Register</button>
            </div>

        </div>
    </div>
</div>

<?php
get_footer();
?>