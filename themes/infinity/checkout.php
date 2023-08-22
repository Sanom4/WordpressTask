<?php
/*
Template Name: Checkout
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
                <p>You must be logged in to checkout.</p>
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
                <h2 class="mb-4">Checkout</h2>
                <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
                    <input type="hidden" name="action" value="process_checkout">
                    <div class="order-summary mb-4">
                        <h3 class="mb-3">Order Summary</h3>
                        <?php
                        $cart_items = $Ecommerce->get_cart_items_checkout(); // Replace this with the function you use to get cart items
                        $total = 0;
                        ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cart_items as $item) : ?>
                                    <tr>
                                        <td><?php echo esc_html($item['title']); ?></td>
                                        <td><?php echo esc_html($item['quantity']); ?></td>
                                        <td><?php echo esc_html($item['price']); ?></td>
                                        <td><?php echo esc_html($item['price'] * $item['quantity']); ?></td>
                                    </tr>
                                    <?php $total += $item['price'] * $item['quantity']; ?>
                                <?php endforeach; ?>
                                <tr>
                                    <td colspan="3">Total:</td>
                                    <td><?php echo esc_html($total); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

            </div>
            <div class="OrderPlaceholder">
                <button type="submit" class="btn btn-primary OrderButton">Place Order</button>
            </div>
            </form>
        </div>
    </div>
    </div>

<?php
}
get_footer();
?>