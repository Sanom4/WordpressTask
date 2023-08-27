<?php

/**
 * Plugin Name: Ecommerce
 * Description: A plugin that interacts with a remote products API.
 * Version: 1.0
 * Author: Alexander Gonzalez
 */
class Ecommerce
{
    public $user_id;

    public function __construct()
    {

        //Public part of plugin
        add_action('init', array($this, 'init_session'));
        add_action('wp_login', array($this, 'associate_cart_with_user'), 10, 2);
        add_action('wp_ajax_add_to_cart', array($this, 'add_to_cart'));
        add_action('wp_ajax_nopriv_add_to_cart', array($this, 'add_to_cart'));
        add_action('wp_ajax_remove_from_cart', array($this, 'remove_from_cart'));
        add_action('wp_ajax_nopriv_remove_from_cart', array($this, 'remove_from_cart'));
        add_action('wp_ajax_get_cart_items', array($this, 'get_cart_items'));
        add_action('wp_ajax_nopriv_get_cart_items', array($this, 'get_cart_items'));
        add_action('wp_ajax_update_cart_quantity', array($this, 'update_cart_quantity'));
        add_action('wp_ajax_nopriv_update_cart_quantity', array($this, 'update_cart_quantity'));

        //checkout
        add_action('admin_post_process_checkout', array($this, 'handle_checkout'));
        add_action('admin_post_nopriv_process_checkout', array($this, 'handle_checkout'));


        //Admin part of plugin
        add_action('admin_menu', array($this, 'products_api_admin_menu'));

        //table creation
        register_activation_hook(
            __FILE__,
            array($this, 'create_tables')
        );
    }

    public function init_session()
    {
        if (!session_id()) {
            session_start();
        }
        $this->user_id = is_user_logged_in() ? get_current_user_id() : session_id();
    }

    public function create_tables()
    {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $cart_table_name = $wpdb->prefix . 'cart';
        $orders_table_name = $wpdb->prefix . 'orders';
        $order_items_table_name = $wpdb->prefix . 'order_items';

        $cart_sql = "CREATE TABLE $cart_table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        user_id varchar(255) NOT NULL,
        product_id mediumint(9) NOT NULL,
        price float(10, 2) NOT NULL,
        quantity smallint(5) NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

        $orders_sql = "CREATE TABLE $orders_table_name (
        order_id INT NOT NULL AUTO_INCREMENT,
        customer_id INT NOT NULL,
        customer_name VARCHAR(255),
        customer_email VARCHAR(255),
        customer_address VARCHAR(255),
        cardName VARCHAR(255),
        cardNumber VARCHAR(255),
        cardExpiry VARCHAR(255),
        cardCVV VARCHAR(255),
        total_amount DECIMAL(10, 2),
        order_date DATETIME,
        payment_status VARCHAR(50),
        order_status VARCHAR(50),
        PRIMARY KEY (order_id)
    ) $charset_collate;";

        $order_items_sql = "CREATE TABLE $order_items_table_name (
        item_id INT NOT NULL AUTO_INCREMENT,
        order_id INT NOT NULL,
        product_id INT NOT NULL,
        product_name VARCHAR(255),
        quantity INT,
        price DECIMAL(10, 2),
        subtotal DECIMAL(10, 2),
        PRIMARY KEY (item_id)
    ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($cart_sql);
        dbDelta($orders_sql);
        dbDelta($order_items_sql);
    }

    public function fetchProducts($category = '')
    {
        $api_url = 'https://dummyjson.com/products';

        // Append category to the API URL if it's not empty
        if (!empty($category)) {
            $api_url .= '/category/' . $category;
        }

        // Send GET request to the API
        $response = wp_remote_get($api_url);

        // If the API request returns an error, return an empty array
        if (is_wp_error($response)) {
            return array();
        }

        $body = wp_remote_retrieve_body($response);

        $data = json_decode($body, true);

        return $data;
    }

    public function fetchSingleProduct($id)
    {
        $response = wp_remote_get('https://dummyjson.com/products/' . $id);

        if (is_wp_error($response)) {
            return false;
        }

        $body = wp_remote_retrieve_body($response);

        $data = json_decode($body, true);

        return $data;
    }

    public function fetchCategories()
    {
        $response = wp_remote_get('https://dummyjson.com/products/categories');

        if (is_wp_error($response)) {
            return false;
        }

        $body = wp_remote_retrieve_body($response);

        $data = json_decode($body);

        return $data;
    }

    public function add_to_cart()
    {
        global $wpdb;

        // Get the product ID and price from the AJAX request
        $product_id = intval($_POST['product_id']);
        $SingleProduct = $this->fetchSingleProduct($product_id);

        $SingleProduct = array_merge($SingleProduct, array('product_id' => $product_id));


        $table_name = $wpdb->prefix . 'cart';

        $TotalData = $wpdb->get_row($wpdb->prepare(
            "SELECT SUM(quantity * price) as total FROM $table_name WHERE user_id = %s",
            $this->user_id,
        ));

        if ($TotalData && isset($TotalData->total)) {
            $TotalPrice = number_format($TotalData->total, 2);
        }

        $existing_product = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE user_id = %s AND product_id = %d",
            $this->user_id,
            $product_id
        ));

        if ($existing_product) {
            $wpdb->update(
                $table_name,
                array('quantity' => $existing_product->quantity + 1),
                array('id' => $existing_product->id)
            );
            $quantity = $existing_product->quantity + 1;
        } else {
            $wpdb->insert(
                $table_name,
                array(
                    'user_id' => $this->user_id,
                    'product_id' => $SingleProduct['id'],
                    'price' => $SingleProduct['price'],
                    'quantity' => 1,
                ),
                array(
                    '%s', // user_id
                    '%d', // product_id
                    '%f', // price
                    '%d', // quantity
                )
            );
        }

        wp_send_json_success(array('message' => 'Product added to cart.', 'product' => $SingleProduct, 'total' => $TotalPrice, 'quantity' => $quantity));
    }

    public function remove_from_cart()
    {
        global $wpdb;

        // Get the product ID from the AJAX request
        $product_id = intval($_POST['product_id']);

        // Check if the product is in the cart
        $table_name = $wpdb->prefix . 'cart';
        $existing_product = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE user_id = %s AND product_id = %d",
            $this->user_id,
            $product_id
        ));

        // If the quantity is 1, remove the product from the cart
        $wpdb->delete($table_name, array('id' => $existing_product->id));

        wp_send_json_success(array('message' => 'Product removed from cart.'));
    }


    public function update_cart_quantity()
    {
        global $wpdb;

        // Get the product ID and new quantity from the AJAX request
        $product_id = intval($_POST['product_id']);
        $new_quantity = intval($_POST['quantity']);

        // Check if the product is in the cart
        $table_name = $wpdb->prefix . 'cart';
        $existing_product = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE user_id = %s AND product_id = %d",
            $this->user_id,
            $product_id
        ));

        if ($existing_product) {
            // Update the quantity of the product in the cart
            $wpdb->update(
                $table_name,
                array('quantity' => $new_quantity),
                array('id' => $existing_product->id)
            );
            wp_send_json_success(array('message' => 'Cart quantity updated.'));
        } else {
            wp_send_json_error(array('message' => 'Product not found in cart.'));
        }
    }

    public function get_cart_items()
    {
        global $wpdb;  // Global WordPress database object

        // Fetch cart items from the database
        $table_name = $wpdb->prefix . 'cart';
        $cart_items = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$table_name} WHERE user_id = %s",
                $this->user_id
            )
        );

        // Convert cart items to an associative array
        $cart_items_arr = [];
        foreach ($cart_items as $item) {
            $cart_items_arr[] = [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'title' => $this->fetchSingleProduct($item->product_id)['title'],
                'thumbnail' => $this->fetchSingleProduct($item->product_id)['thumbnail'],
                'price' => $this->fetchSingleProduct($item->product_id)['price'],
                'quantity' => $item->quantity,
            ];
        }

        // Send the data back to the JavaScript
        echo json_encode($cart_items_arr);

        // Always die in functions echoing AJAX content
        die();
    }

    public function get_cart_items_checkout()
    {
        global $wpdb;  // Global WordPress database object

        // Fetch cart items from the database
        $table_name = $wpdb->prefix . 'cart';
        $cart_items = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$table_name} WHERE user_id = %s",
                $this->user_id
            )
        );

        // Convert cart items to an associative array
        $cart_items_arr = [];
        foreach ($cart_items as $item) {
            $cart_items_arr[] = [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'title' => $this->fetchSingleProduct($item->product_id)['title'],
                'thumbnail' => $this->fetchSingleProduct($item->product_id)['thumbnail'],
                'price' => $this->fetchSingleProduct($item->product_id)['price'],
                'quantity' => $item->quantity,
            ];
        }

        // Send the data back to the JavaScript
        return $cart_items_arr;
    }

    // checkout
    public function process_checkout()
    {
        global $wpdb;
        if (class_exists('UserRL')) {
            $UserRL = new UserRL();
        }

        $cardName = sanitize_text_field($_POST['cardName']);
        $cardNumber = sanitize_text_field($_POST['cardNumber']);
        $cardExpiry = sanitize_text_field($_POST['cardExpiry']);
        $cardCVV = sanitize_text_field($_POST['cardCVV']);

        if (!is_user_logged_in()) {
            $userAction = sanitize_text_field($_POST['userAction']);

            if ($userAction == 'register') {
                $UserRL->Registration();
            } elseif ($userAction == 'login') {
                $UserRL->Login();
            }
        }

        $user_id = get_current_user_id();
        $customer = wp_get_current_user();

        

        $cart_items = $this->get_cart_items_checkout($user_id);
        if (!$cart_items) {
            return; // No items in cart
        }

        print_r($user_id);
        echo '<br>';
        print_r($customer);
        echo '<br>';
        print_r($cart_items);

        exit;

        $total_amount = array_reduce($cart_items, function ($sum, $item) {
            return $sum + $item['price'] * $item['quantity'];
        }, 0);

        $orders_table_name = $wpdb->prefix . 'orders';

        $wpdb->insert(
            $orders_table_name,
            array(
                'customer_id' => $user_id,
                'customer_name' => $customer->display_name,
                'customer_email' => $customer->user_email,
                'customer_address' => $customer->customer_address,
                'cardName' => $cardName,
                'cardNumber' => $cardNumber,
                'cardExpiry' => $cardExpiry,
                'cardCVV' => $cardCVV,
                'total_amount' => $total_amount,
                'order_date' => current_time('mysql'),
                'payment_status' => 'Pending',
                'order_status' => 'Processing'
            )
        );

        $order_id = $wpdb->insert_id;
        $order_items_table_name = $wpdb->prefix . 'order_items';

        foreach ($cart_items as $item) {
            $wpdb->insert(
                $order_items_table_name,
                array(
                    'order_id' => $order_id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['price'] * $item['quantity']
                )
            );
        }

        // Clear the cart
        $cart_table_name = $wpdb->prefix . 'cart';
        $wpdb->delete($cart_table_name, array('user_id' => $user_id));
    }


    function associate_cart_with_user($user_login, $user)
    {
        global $wpdb;

        // Get the session ID
        if (!session_id()) {
            session_start();
        }
        $session_id = session_id();

        // Get the user ID
        $user_id = $user->ID;

        // Associate the cart with the user ID
        $table_name = $wpdb->prefix . 'cart';
        $wpdb->update(
            $table_name,
            array('user_id' => $user_id),
            array('user_id' => $session_id)
        );
    }

    function handle_checkout()
    {
        $this->process_checkout();

        wp_redirect(home_url('/orders')); // Redirect to the orders page
        exit;
    }



    //Orders



    public function PanelOrders()
    {
        global $wpdb;  // Global WordPress database object

        // Define the orders table name
        $table_name = $wpdb->prefix . 'orders';


        $orders = $wpdb->get_results("SELECT * FROM {$table_name}");


        // Convert orders to an associative array
        $orders_arr = [];
        foreach ($orders as $order) {
            $orders_arr[] = [
                'order_id' => $order->order_id,
                'customer_id' => $order->customer_id,
                'cardName' => $order->cardName,
                'cardNumber' => $order->cardNumber,
                'cardExpiry' => $order->cardExpiry,
                'total_amount' => $order->total_amount,
                'order_date' => $order->order_date,
                'payment_status' => $order->payment_status,
                'order_status' => $order->order_status
            ];
        }

        // Send the data back to the JavaScript
        return $orders_arr;
    }

    public function PanelOrderInfo($OrderID)
    {

        global $wpdb;  // Global WordPress database object

        // Define the orders table name
        $table_name = $wpdb->prefix . 'order_items';

        // Fetch orders from the database
        $orderItems = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$table_name} WHERE order_id = %s",
                $OrderID
            )
        );

        // Convert cart items to an associative array
        $cart_items_arr = [];
        foreach ($orderItems as $item) {
            $OrderItemsArr[] = [
                'id' => $item->item_id,
                'product_id' => $item->product_id,
                'title' => $this->fetchSingleProduct($item->product_id)['title'],
                'thumbnail' => $this->fetchSingleProduct($item->product_id)['thumbnail'],
                'price' => $this->fetchSingleProduct($item->product_id)['price'],
                'quantity' => $item->quantity,
            ];
        }

        // Send the data back to the JavaScript
        return $OrderItemsArr;
    }

    public function PublicOrders($OrderID = null)
    {
        global $wpdb;  // Global WordPress database object

        // Define the orders table name
        $table_name = $wpdb->prefix . 'orders';

        if ($OrderID) {
            $SQL = 'AND order_id = ' . $OrderID;
        } else {
            $SQL = '';
        }

        $orders = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$table_name} WHERE customer_id = %s {$SQL}",
                $this->user_id
            )
        );

        // Convert orders to an associative array
        $orders_arr = [];
        foreach ($orders as $order) {
            $orders_arr[] = [
                'order_id' => $order->order_id,
                'customer_id' => $order->customer_id,
                'cardName' => $order->customer_name,
                'cardNumber' => $order->customer_email,
                'cardExpiry' => $order->customer_address,
                'total_amount' => $order->total_amount,
                'order_date' => $order->order_date,
                'payment_status' => $order->payment_status,
                'order_status' => $order->order_status
            ];
        }

        // Send the data back to the JavaScript
        return $orders_arr;
    }

    public function PublicOrderInfo($OrderID)
    {

        global $wpdb;  // Global WordPress database object

        // Define the orders table name
        $table_name = $wpdb->prefix . 'order_items';

        // Fetch orders from the database
        $orderItems = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$table_name} WHERE order_id = %s",
                $OrderID
            )
        );

        // Convert cart items to an associative array
        $cart_items_arr = [];
        foreach ($orderItems as $item) {
            $OrderItemsArr[] = [
                'id' => $item->item_id,
                'product_id' => $item->product_id,
                'title' => $this->fetchSingleProduct($item->product_id)['title'],
                'thumbnail' => $this->fetchSingleProduct($item->product_id)['thumbnail'],
                'price' => $this->fetchSingleProduct($item->product_id)['price'],
                'quantity' => $item->quantity,
            ];
        }

        // Send the data back to the JavaScript
        return $OrderItemsArr;
    }


    //ADMIN PART OF PLUGIN

    public function products_api_admin_menu()
    {
        // Main Menu
        add_menu_page(
            'Products', // page_title
            'Ecommerce', // menu_title
            'manage_options', // capability
            'ecommerce-products', // menu_slug
            array($this, 'products_api_admin_page'), // function
            'dashicons-tickets', // icon_url
            6 // position
        );

        // This function adds the menu item and the submenu
        add_submenu_page(
            'ecommerce-products', // parent_slug
            'Categories', // page_title
            'Categories', // menu_title
            'manage_options', // capability
            'products-api-categories', // menu_slug
            array($this, 'products_api_admin_page_categories') // function
        );

        add_submenu_page(
            'ecommerce-products', // parent_slug
            'Orders', // page_title
            'Orders', // menu_title
            'manage_options', // capability
            'products-api-orders', // menu_slug
            array($this, 'products_orders_page_content') // function
        );

        // Add submenu page for Order Details
        add_submenu_page(
            'ecommerce-products', // parent_slug
            'Order Details', // page_title
            'Order Details', // menu_title
            'manage_options', // capability
            'products-api-order-details', // menu_slug
            array($this, 'products_order_details_page_content') // function
        );
    }

    function products_orders_page_content()
    {
        // Query the database for orders and display them here.
        echo '<h1>Orders</h1>';
        $orders = $this->PanelOrders();
        if (!empty($orders)) {
            echo '<table class="widefat fixed" cellspacing="0">';
            echo '<thead><tr><th>ID</th><th>Name</th><th>CardNumber</th><th>CardExpiry</th><th>Status</th><th>Total Amount</th><th>Details</th></tr></thead>';
            echo '<tbody>';
            foreach ($orders as $orders) {
                echo '<tr>';
                echo '<td>' . esc_html($orders['order_id']) . '</td>';
                echo '<td>' . esc_html($orders['cardName']) . '</td>';
                echo '<td>' . esc_html($orders['cardNumber']) . '</td>';
                echo '<td>' . esc_html($orders['cardExpiry']) . '</td>';
                echo '<td>' . esc_html($orders['order_date']) . '</td>';
                echo '<td>' . esc_html($orders['order_status']) . '</td>';
                echo '<td>' . esc_html($orders['total_amount']) . '</td>';
                echo '<td><a href="?page=products-api-order-details&order_id=' . esc_html($orders['order_id']) . '">Details</a></td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
        }
?>
        </div>
    <?php
    }

    function products_order_details_page_content()
    {
        if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
            echo '<p>Invalid order ID.</p>';
            return;
        }

        $order_id = intval($_GET['order_id']);
        $order_items = $this->PanelOrderInfo($order_id);
        if (empty($order_items)) {
            echo '<p>No order details found for this order ID.</p>';
            return;
        }

        // Display order details
        echo '<h1>Order Details for Order ID: ' . esc_html($order_id) . '</h1>';
        echo '<table class="widefat fixed" cellspacing="0">';
        echo '<thead><tr><th>ID</th><th>Product ID</th><th>Title</th><th>Thumbnail</th><th>Price</th><th>Quantity</th></tr></thead>';
        echo '<tbody>';
        foreach ($order_items as $item) {
            echo '<tr>';
            echo '<td>' . esc_html($item['id']) . '</td>';
            echo '<td>' . esc_html($item['product_id']) . '</td>';
            echo '<td>' . esc_html($item['title']) . '</td>';
            echo '<td><img src="' . esc_url($item['thumbnail']) . '" alt="' . esc_attr($item['title']) . '" width="50"></td>';
            echo '<td>' . esc_html($item['price']) . '</td>';
            echo '<td>' . esc_html($item['quantity']) . '</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
    }


    public function products_api_admin_page()
    {
    ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <h2>Products</h2>
            <?php
            $products = $this->fetchProducts();
            if (!empty($products)) {
                echo '<table class="widefat fixed" cellspacing="0">';
                echo '<thead><tr><th>ID</th><th>Name</th><th>Description</th></tr></thead>';
                echo '<tbody>';
                foreach ($products['products'] as $product) {
                    echo '<tr>';
                    echo '<td>' . esc_html($product['id']) . '</td>';
                    echo '<td>' . esc_html($product['title']) . '</td>';
                    echo '<td>' . esc_html($product['description']) . '</td>';
                    echo '</tr>';
                }
                echo '</tbody>';
                echo '</table>';
            }
            ?>
        </div>
    <?php
    }

    public function products_api_admin_page_categories()
    {
        $categories = $this->fetchCategories();
    ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <h2>Product Categories</h2>
            <?php
            if (!empty($categories)) {
                echo '<ul>';
                foreach ($categories as $category) {
                    echo '<li>' . esc_html($category) . '</li>';
                }
                echo '</ul>';
            } else {
                echo '<p>No categories found.</p>';
            }
            ?>
        </div>
<?php

    }
}

$Ecommerce = new Ecommerce();
