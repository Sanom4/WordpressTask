<?php
/*
Plugin Name: Registration and Login
Description: Made for wordpress task.
Version: 1.0
Author: Alexander Gonzalez
*/

class UserRL
{

    public function __construct()
    {

        //Actions
        add_action('admin_post_nopriv_login', array($this, 'Login'));
        add_action('admin_post_nopriv_register', array($this, 'Registration'));
    }

    // WordPress Standard Registration
    function Registration()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['username']) && isset($_POST['password']) && isset($_POST['email'])) {
            $username = sanitize_text_field($_POST['username']);
            $password = $_POST['password'];
            $urltoredirect = sanitize_text_field($_POST['urltoredirect']);
            $email = sanitize_email($_POST['email']);

            $userdata = array(
                'user_login' => $username,
                'user_pass'  => $password,
                'user_email' => $email,
                'role'       => 'subscriber'
            );

            $user_id = wp_insert_user($userdata);

            if (is_wp_error($user_id)) {
                echo $user_id->get_error_message();
            } else {
                wp_redirect($urltoredirect); // Redirect to the desired URL
                exit; // It's a good practice to exit after redirecting
            }
        }
    }

    // WordPress Standard Login
    function Login()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['usernameLogin']) && isset($_POST['passwordLogin'])) {
            $creds = array();
            $creds['user_login'] = sanitize_text_field($_POST['usernameLogin']);
            $creds['user_password'] = $_POST['passwordLogin'];
            $urltoredirect = sanitize_text_field($_POST['urltoredirect']);
            $creds['remember'] = true;  // Set this to false if you don't want to remember the login

            $user = wp_signon($creds, false);

            if (is_wp_error($user_id)) {
                echo $user_id->get_error_message();
            } else {
                wp_redirect($urltoredirect); // Redirect to the desired URL
                exit; // It's a good practice to exit after redirecting
            }
        }
    }
}

$userRL = new UserRL();
