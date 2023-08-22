<?php
/*
Template Name: Orders
*/
get_header();
?>

<?php
if (!is_user_logged_in()) {
?>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-4">
                <div class="sideBlock p-3 mb-3" style="background: #FFF;">
                    <ul>
                        <li><a href="/orders">Orders</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-md-8">
                <h2 class="mb-4">Checkout</h2>
                <p>You must be logged in to view orders.</p>
            </div>
        </div>
    </div>
    </div>

<?php
} else {
?>



    <div class="container mt-5">
        <div class="row">
            <div class="col-md-4">
                <div class="sideBlock p-3 mb-3" style="background: #FFF;">
                    <ul>
                        <li><a href="/orders">Orders</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-md-8">
                <h2 class="mb-4">Orders</h2>
                <div class="order-summary mb-4">
                    <h3 class="mb-3">Order Summary</h3>
                    <?php
                    $orders = $Ecommerce->PublicOrders(); // Replace this with the function you use to get cart items
                    ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Address</th>
                                <th>Order Date</th>
                                <th>Payment Status</th>
                                <th>Order Status</th>
                                <th>Total Amount</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $item) : ?>
                                <tr>
                                    <td><?php echo esc_html($item['order_id']); ?></td>
                                    <td><?php echo esc_html($item['customer_address']); ?></td>
                                    <td><?php echo esc_html($item['order_date']); ?></td>
                                    <td><?php echo esc_html($item['payment_status']); ?></td>
                                    <td><?php echo esc_html($item['order_status']); ?></td>
                                    <td><?php echo esc_html($item['total_amount']); ?></td>
                                    <td><a href="/order/<?php echo esc_html($item['order_id']); ?>">View</a></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
    </div>

<?php
}
get_footer();
?>