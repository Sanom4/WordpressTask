<?php
get_header(); ?>
<br>
<br>
<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="sideBlock" style="background: #FFF; padding: 10px;">
                <ul>
                    <li><a href='/'>Display All</a></li>
                    <?php
                    $Categories = $Ecommerce->fetchCategories();

                    foreach ($Categories as $category) :
                    ?>
                        <li><a href='<?php home_url('/?category' .  $category); ?>'><?php echo ucfirst(str_replace('-', ' ', $category)); ?> </a></li>

                    <?php
                    endforeach;
                    ?>
                </ul>
            </div>
        </div>
        <div class="col-md-8">
            <div class="row">
                <?php
                $category = isset($_GET['category']) ? sanitize_text_field($_GET['category']) : '';

                $products = $Ecommerce->fetchProducts($category); // Call the function from our plugin
                if ($products) : // Check if there are products
                    foreach ($products['products'] as $product) :
                ?>

                        <div class="col-md-6">
                            <div class="product text-center"> <a href='<?php home_url('/product/' . $product['id']); ?>'>
                                    <img class="productImage" src="<?php echo $product['thumbnail']; ?>" width="250"></a>
                                <div class="about text-left px-3">
                                    <h4><a href='/product/<?php home_url('/product/' . $product['id']); ?>'><?php echo $product['title']; ?></a>
                                        <h3><?php echo $product['price']; ?>$</h3>
                                </div>
                                <a href="#" class="btn btn-primary cd-add-to-cart js-cd-add-to-cart" data-price="<?php echo $product['price']; ?>" data-id="<?php echo $product['id']; ?>">Add to Cart</a>
                            </div>
                        </div>

                    <?php
                    endforeach;
                    ?>
            </div>
        </div>
    </div>
</div>

<?php
                endif;
                get_footer(); ?>