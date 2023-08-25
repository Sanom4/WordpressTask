<div class="cd-cart cd-cart--empty js-cd-cart">
    <a href="#0" class="cd-cart__trigger text-replace">
        <ul class="cd-cart__count"> <!-- cart items count -->
            <li>0</li>
            <li>0</li>
        </ul> <!-- .cd-cart__count -->
    </a>

    <div class="cd-cart__content">
        <div class="cd-cart__layout">
            <header class="cd-cart__header">
                <h2>Cart</h2>
                <span class="cd-cart__undo">Item removed. <a href="#0">Undo</a></span>
            </header>

            <div class="cd-cart__body">
                <ul>
                    <!-- products added to the cart will be inserted here using JavaScript -->
                </ul>
            </div>

            <footer class="cd-cart__footer">
                <a href="<?php echo home_url('/checkout'); ?>" class="cd-cart__checkout">
                    <em>Checkout - $<span>0</span>
                        <svg class="icon icon--sm" viewBox="0 0 24 24">
                            <g fill="none" stroke="currentColor">
                                <line stroke-width="2" stroke-linecap="round" stroke-linejoin="round" x1="3" y1="12" x2="21" y2="12" />
                                <polyline stroke-width="2" stroke-linecap="round" stroke-linejoin="round" points="15,6 21,12 15,18 " />
                            </g>
                        </svg>
                    </em>
                </a>
            </footer>
        </div>
    </div> <!-- .cd-cart__content -->
</div> <!-- cd-cart -->
<footer class="bg-light footer">
    <div class="container py-3">
        <div class="row">
            <div class="col text-center">
                &copy; <?php echo date("Y"); ?> <?php bloginfo('name'); ?>
            </div>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>

</body>

</html>