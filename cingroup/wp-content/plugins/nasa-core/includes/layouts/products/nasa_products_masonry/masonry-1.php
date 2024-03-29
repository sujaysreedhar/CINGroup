<?php
$k = 1;
$class_small = 'large-2 medium-3 small-6 columns';
$class_large = 'large-4 medium-6 small-6 columns';
$custom_class = isset($custom_class) ? $custom_class : '';

$cat_info = apply_filters('nasa_loop_categories_show', false);
$description_info = apply_filters('nasa_loop_short_description_show', false);

while ($loop->have_posts()) :
    $loop->the_post();

    $class_wrap = in_array($k, array(5, 6, 7, 8, 13, 14)) ? $class_large : $class_small;
    $class_wrap .= $custom_class ? ' ' . $custom_class : '';
    ?>
    <div class="nasa-masonry-item padding-left-5 padding-right-5 <?php echo $class_wrap; ?>">
        <?php 
        wc_get_template(
            'content-product.php',
            array(
                'wrapper' => 'div',
                'show_in_list' => false,
                'cat_info' => $cat_info,
                'description_info' => $description_info
            )
        );
        ?>
    </div>
<?php 
$k++;
endwhile;
