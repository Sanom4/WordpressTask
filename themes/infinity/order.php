<?php
/*
Template Name: Order
*/
ob_start();
get_header();
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
                $OrderID = intval(get_query_var('id'));
                $orderItems = $Ecommerce->PublicOrderInfo($OrderID); // Replace this with the function you use to get cart items
                if (!$orderItems) {
                    wp_redirect(home_url('/orders'));
                    exit;
                }
                ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Image</th>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 0;
                        $total = 0;
                        foreach ($orderItems as $item) :
                            $i++ ?>
                            <tr>
                                <td><?php echo esc_html($i); ?></td>
                                <td><a href="/product/<?php echo $item['product_id']; ?>" target="_new"><img src="<?php echo esc_url($item['thumbnail']); ?>" width="100"></a></td>
                                <td><a href="/product/<?php echo $item['product_id']; ?>" target="_new"><?php echo esc_html($item['title']); ?></a></td>
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
    </div>
</div>
</div>

<?php
get_footer();
?>