<?php
/*
Template Name: Product
*/
ob_start();
get_header();
?>

<br>
<br>
<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="sideBlock bg-white p-3">
                <ul class="list-unstyled">
                    <li><a href='/'>Display All</a></li>
                    <?php
                    $Categories = $Ecommerce->fetchCategories();
                    foreach ($Categories as $category) :
                    ?>
                        <li><a href='<?php echo esc_url(home_url('/')); ?>?category=<?php echo $category; ?>'><?php echo ucfirst(str_replace('-', ' ', $category)); ?></a></li>
                    <?php
                    endforeach;
                    ?>
                </ul>
            </div>
        </div>
        <div class="col-md-8">
            <?php
            // Fetch the product ID from the URL
            $product_id = intval(get_query_var('id'));
            $product = $Ecommerce->fetchSingleProduct($product_id);

            if (!$product_id || !$product) {
                wp_redirect(home_url());
                exit;
            }
            ?>
            <div class="card">
                <div class="row">
                    <div class="col-md-6">
                        <img class="card-img-top" src="<?php echo esc_url($product['thumbnail']); ?>" alt="<?php echo esc_attr($product['title']); ?>">
                        <!-- You can also add additional images in a carousel format or as thumbnails below the main image -->
                    </div>
                    <div class="col-md-6">
                        <div class="card-body">
                            <h2 class="card-title"><?php echo $product['title']; ?></h2>
                            <p class="card-text"><?php echo $product['description']; ?></p>
                            <p class="card-text"><strong>Price:</strong> $<?php echo $product['price']; ?></p>
                            <a href="#" class="btn btn-primary  cd-add-to-cart js-cd-add-to-cart" data-price="<?php echo $product['price']; ?>" data-id="<?php echo $product['id']; ?>">Add to Cart</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
get_footer();
?>