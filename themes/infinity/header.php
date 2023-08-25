<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary', // The location in the theme where the menu is registered
                    'container' => false, // Don't wrap the ul element with a div
                    'menu_class' => 'navbar-nav mr-auto', // Add Bootstrap classes
                    'fallback_cb' => false, // Don't show a fallback menu if no menu is found
                ));

                if (is_user_logged_in()) {
                    $current_user = wp_get_current_user();
                    echo '<ul class="navbar-nav">';
                    echo '<li class="nav-item dropdown">';
                    echo '<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                    echo esc_html($current_user->display_name);
                    echo '</a>';
                    echo '<div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">';
                    echo '<a class="dropdown-item" href="/orders/">Orders</a>';
                    echo '<a class="dropdown-item" href="' . esc_url(wp_logout_url(home_url())) . '">Logout</a>';
                    echo '</div>';
                    echo '</li>';
                    echo '</ul>';
                } else {
                    echo '<ul class="navbar-nav">';
                    echo '<li class="nav-item">';
                    echo '<a class="nav-link" href="' . esc_url(home_url('/login')) . '" id="navbarDropdownMenuLink" role="button">';
                    echo 'Login';
                    echo '</a>';
                    echo '</li>';
                    echo '<li class="nav-item">';
                    echo '<a class="nav-link" href="' . esc_url(home_url('/register')) . '" id="navbarDropdownMenuLink" role="button">';
                    echo 'Register';
                    echo '</a>';
                    echo '</li>';
                    echo '</ul>';
                }
                ?>
            </div>
        </nav>
    </header>