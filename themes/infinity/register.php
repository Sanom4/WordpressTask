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
                    <li class="active"><a href='<?php echo esc_url(home_url('/register')); ?>'>Login</a></li>

                </ul>
            </div>
        </div>
        <div class="col-md-8">


        </div>
    </div>
</div>

<?php
get_footer();
?>