<?php
/*
Template Name: Checkout
*/
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
            <h2 class="mb-4">Checkout</h2>
            <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
                <input type="hidden" name="action" value="process_checkout">

                <!-- User Action Selection -->
                <div class="mb-3">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="userAction" id="registerRadio" value="register" checked>
                        <label class="form-check-label" for="registerRadio">Register</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="userAction" id="loginRadio" value="login">
                        <label class="form-check-label" for="loginRadio">Login</label>
                    </div>
                </div>
                <div class="registrationSection mb-3">
                    <h3 class="mb-3">Registration</h3>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="text" class="form-control" id="email" name="email">
                    </div>
                </div>

                <div class="loginSection mb-3" style="display:none;">
                    <h3 class="mb-3">Login</h3>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="usernameLogin">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="passwordLogin">
                    </div>
                </div>


                <!-- Credit Card Information Inputs -->
                <div class="mb-3">
                    <h3 class="mb-3">Credit Card Information</h3>
                    <div class="mb-3">
                        <label for="cardName" class="form-label">Name on Card</label>
                        <input type="text" class="form-control" id="cardName" name="cardName" required>
                    </div>
                    <div class="mb-3">
                        <label for="cardNumber" class="form-label">Card Number</label>
                        <input type="text" class="form-control" id="cardNumber" name="cardNumber" required>
                    </div>
                    <div class="mb-3">
                        <label for="cardExpiry" class="form-label">Expiry Date</label>
                        <input type="text" class="form-control" id="cardExpiry" name="cardExpiry" placeholder="MM/YY" required>
                    </div>
                    <div class="mb-3">
                        <label for="cardCVV" class="form-label">CVV</label>
                        <input type="text" class="form-control" id="cardCVV" name="cardCVV" required>
                    </div>
                </div>

                <!-- Order Summary -->
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
                                    <td><?php echo home_url('/product/') . $item['product_id']; ?><?php echo esc_html($item['title']); ?></td>
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

                <!-- Place Order Button -->
                <div class="OrderPlaceholder">
                    <button type="submit" class="btn btn-primary OrderButton">Place Order</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- jQuery Toggling Script -->
<script>
    jQuery(document).ready(function($) {
        // Hide/Show Sections based on Radio Selection
        $('input[name="userAction"]').on('change', function() {
            if ($('#registerRadio').is(':checked')) {
                $('.loginSection').hide();
                $('.registrationSection').show();
            } else if ($('#loginRadio').is(':checked')) {
                $('.registrationSection').hide();
                $('.loginSection').show();
            }
        });
    });
</script>

<?php
get_footer();
?>