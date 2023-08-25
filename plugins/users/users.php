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
        register_activation_hook(__FILE__, array($this, 'create_users_table'));
        // Hook for logout action
        add_action('init', array($this, 'custom_logout'));
    }


    function create_users_table()
    {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $members_table = $wpdb->prefix . 'members';

        $members_sql = "CREATE TABLE $members_table (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        username varchar(55) NOT NULL,
        password varchar(255) NOT NULL,
        email varchar(100) DEFAULT '' NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($members_sql);
    }

    // Custom Registration Shortcode
    function Registration()
    {
        global $wpdb;

        $members_table = $wpdb->prefix . 'members';

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['username'])) {
            $username = sanitize_text_field($_POST['username']);
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $email = sanitize_email($_POST['email']);

            $wpdb->insert($members_table, array(
                'username' => $username,
                'password' => $password,
                'email' => $email
            ));

            $user_id = $wpdb->insert_id;
            $_SESSION['wp_task_user'] = $user_id;

            // Associate the cart with the custom user ID
            $table_name = $wpdb->prefix . 'cart';
            $wpdb->update(
                $table_name,
                array('user_id' => $user_id), // This is now the ID from your custom users table
                array('user_id' => session_id())
            );
        }
    }

    // Custom Login Shortcode
    function Login()
    {
        global $wpdb;

        $members_table = $wpdb->prefix . 'members';

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['usernameLogin'])) {
            $username = sanitize_text_field($_POST['passwordLogin']);
            $password = $_POST['password'];

            $user = $wpdb->get_row($wpdb->prepare("SELECT * FROM $members_table WHERE username = %s", $username));

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

    function custom_logout()
    {
        if (isset($_GET['custom_logout']) && $_GET['custom_logout'] == 'true') {
            unset($_SESSION['wp_task_user']); // Destroys the user session
            wp_redirect(home_url()); // Redirects to homepage after logout. You can change this to redirect wherever you like.
            exit;
        }
    }
}
$userRL = new UserRL();
