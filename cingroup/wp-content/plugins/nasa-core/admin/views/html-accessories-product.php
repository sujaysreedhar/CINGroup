<div class="options_group show_if_simple">
    <p class="form-field">
        <label for="accessories_ids"><?php esc_html_e('Bought Together', 'nasa-core'); ?></label>
        <select class="wc-product-search" multiple="multiple" style="width: 50%;" id="accessories_ids" name="accessories_ids[]" data-placeholder="<?php esc_attr_e('Search for a product&hellip;', 'nasa-core'); ?>" data-action="woocommerce_json_search_products_and_variations" data-exclude="<?php echo intval($post->ID); ?>">
            <?php
            if (!empty($product_ids)) :
                foreach ($product_ids as $product_id) :
                    $product = wc_get_product($product_id);
                    if (is_object($product)) :
                        echo '<option value="' . esc_attr($product_id) . '"' . selected(true, true, false) . '>' . wp_kses_post($product->get_formatted_name()) . '</option>';
                    endif;
                endforeach;
            endif;
            ?>
        </select> <?php echo wc_help_tip(esc_html__('Accessories are suggested products purchased with the current product. Note: Only use for Simple product', 'nasa-core')); // WPCS: XSS ok. ?>
    </p>
</div>
