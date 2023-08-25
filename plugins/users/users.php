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
        add_action('init', array($this, 'custom_logout'));
    }

    // WordPress Standard Registration
    function Registration()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['username']) && isset($_POST['password']) && isset($_POST['email'])) {
            $username = sanitize_text_field($_POST['username']);
            $password = $_POST['password'];
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
            $creds['remember'] = true;  // Set this to false if you don't want to remember the login

            $user = wp_signon($creds, false);

            if (is_wp_error($user)) {
                echo $user->get_error_message();
            }
        }
    }

    function custom_logout()
    {
        if (isset($_GET['custom_logout']) && $_GET['custom_logout'] == 'true') {
            wp_logout();
            wp_redirect(home_url());
            exit;
        }
    }
}

$userRL = new UserRL();
