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

        //Public part of plugin
        // Run this function when plugin is activated
        register_activation_hook(__FILE__, 'create_users_table');
    }


function create_users_table()
{
    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE {$wpdb->prefix}users (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        username varchar(55) NOT NULL,
        password varchar(255) NOT NULL,
        email varchar(100) DEFAULT '' NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Custom Registration Shortcode
function Registration()
{
    global $wpdb;

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reg_username'])) {
        $username = sanitize_text_field($_POST['reg_username']);
        $password = password_hash($_POST['reg_password'], PASSWORD_DEFAULT);
        $email = sanitize_email($_POST['reg_email']);

        $wpdb->insert("{$wpdb->prefix}users", array(
            'username' => $username,
            'password' => $password,
            'email' => $email
        ));
    }
}

// Custom Login Shortcode
function Login()
{
    global $wpdb;

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login_username'])) {
        $username = sanitize_text_field($_POST['login_username']);
        $password = $_POST['login_password'];

        $user = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}users WHERE username = %s", $username));

        if ($user && password_verify($password, $user->password)) {
            // Set user session or cookie here...
            $_SESSION['wp_task_user'] = $user->id;

            if (isset($_SESSION['wp_task_user'])) {
                $user_id = $_SESSION['wp_task_user'];

                // Associate the cart with the custom user ID
                $table_name = $wpdb->prefix . 'cart';
                $wpdb->update(
                    $table_name,
                    array('user_id' => $user_id), // This is now the ID from your custom users table
                    array('user_id' => session_id())
                );
            }   
        
        } else {
            echo "Invalid credentials.";
        }
    }

}
}

