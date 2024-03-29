<?php
/**
 * Register Ajax Actions
 */
if(!function_exists('elessi_ajax_actions')) :
    function elessi_ajax_actions($ajax_actions = array()) {
        $ajax_actions[] = 'nasa_update_wishlist';
        $ajax_actions[] = 'nasa_remove_from_wishlist';
        $ajax_actions[] = 'live_search_products';

        return $ajax_actions;
    }
endif;

/**
 * Map short code for ajax
 */
if(!function_exists('elessi_init_map_shortcode')) :
    function elessi_init_map_shortcode() {
        if (class_exists('WPBMap')) {
            WPBMap::addAllMappedShortcodes();
        }
    }
endif;

/**
 * Update Wishlist
 */
add_action('wp_ajax_nasa_update_wishlist', 'elessi_update_wishlist');
add_action('wp_ajax_nopriv_nasa_update_wishlist', 'elessi_update_wishlist');
if(!function_exists('elessi_update_wishlist')) :
    function elessi_update_wishlist(){
        $json = array(
            'list' => '',
            'count' => 0
        );
        
        $json['list'] = elessi_mini_wishlist_sidebar(true);
        $json['status_add'] = 'true';
        $count = function_exists('yith_wcwl_count_products') ? yith_wcwl_count_products() : 0;
        $nasaSl = (int) $count > 9 ? '9+' : (int) $count;
        $json['count'] = apply_filters('nasa_mini_wishlist_total_items', $nasaSl);

        die(json_encode($json));
    }
endif;

/**
 * Remove From Wishlist
 */
add_action('wp_ajax_nasa_remove_from_wishlist', 'elessi_remove_from_wishlist');
add_action('wp_ajax_nopriv_nasa_remove_from_wishlist', 'elessi_remove_from_wishlist');
if(!function_exists('elessi_remove_from_wishlist')) :
    function elessi_remove_from_wishlist(){
        $json = array(
            'error' => '1',
            'list' => '',
            'count' => 0,
            'mess' => ''
        );

        if(!NASA_WISHLIST_ENABLE) {
            die(json_encode($json));
        }

        $detail = array();
        $detail['remove_from_wishlist'] = isset($_REQUEST['pid']) ? (int) $_REQUEST['pid'] : 0;
        $detail['wishlist_id'] = isset($_REQUEST['wishlist_id']) ? (int) $_REQUEST['wishlist_id'] : 0;
        $detail['pagination'] = isset($_REQUEST['pagination']) ? (int) $_REQUEST['pagination'] : 'no';
        $detail['per_page'] = isset($_REQUEST['per_page']) ? (int) $_REQUEST['per_page'] : 5;
        $detail['current_page'] = isset($_REQUEST['current_page']) ? (int) $_REQUEST['current_page'] : 1;
        $detail['user_id'] = is_user_logged_in() ? get_current_user_id() : false;
        $nasa_wishlist = new YITH_WCWL($detail);
        $json['error'] = elessi_remove_wishlist_item($nasa_wishlist, true) ? '0' : '1';

        if($json['error'] == '0') {
            $json['list'] = elessi_mini_wishlist_sidebar(true);
            $count = $nasa_wishlist->count_products();
            $nasaSl = (int) $count > 9 ? '9+' : (int) $count;
            $json['count'] = apply_filters('nasa_mini_compare_total_items', $nasaSl);
            $json['mess'] = '<div id="yith-wcwl-message">' . esc_html__('Product successfully removed!', 'elessi-theme') . '</div>';
        }

        die(json_encode($json));
    }
endif;

/**
 * Remove Wishlist item
 */
if(!function_exists('elessi_remove_wishlist_item')) :
    function elessi_remove_wishlist_item($nasa_wishlist, $remove_force = false) {
        if (get_option('yith_wcwl_remove_after_add_to_cart') == 'yes' || $remove_force) {
            if(!$nasa_wishlist->details['user_id']){
                $wishlist = yith_getcookie('yith_wcwl_products');
                foreach($wishlist as $key => $item){
                    if($item['prod_id'] == $nasa_wishlist->details['remove_from_wishlist']){
                        unset($wishlist[$key]);
                    }
                }
                yith_setcookie('yith_wcwl_products', $wishlist);

                return true;
            }

            return $nasa_wishlist->remove();
        }

        return true;
    }
endif;

/**
 * Ajax search
 */
add_action('wp_ajax_nopriv_live_search_products', 'elessi_live_search_products');
add_action('wp_ajax_live_search_products', 'elessi_live_search_products');
if(!function_exists('elessi_live_search_products')) :
    function elessi_live_search_products() {
        global $nasa_opt, $woocommerce;

        $results = array();
        if (!$woocommerce || !isset($_REQUEST['s']) || trim($_REQUEST['s']) == '') {
            die(json_encode($results));
        }
        
        $data_store = WC_Data_Store::load('product');
        $post_id_in = $data_store->search_products(wc_clean($_REQUEST['s']), '', true, true);
        if (empty($post_id_in)) {
            die(json_encode($results));
        }

        $query_args = array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => (isset($nasa_opt['limit_results_search']) && (int) $nasa_opt['limit_results_search'] > 0) ? (int) $nasa_opt['limit_results_search'] : 5,
            'no_found_rows' => true
        );

        $query_args['meta_query'] = array();
        $query_args['meta_query'][] = $woocommerce->query->stock_status_meta_query();
        $query_args['meta_query'][] = $woocommerce->query->visibility_meta_query();
        
        $query_args['post__in'] = array_merge($post_id_in, array(0));
        $query_args['tax_query'] = array('relation' => 'AND');
        $product_visibility_terms = wc_get_product_visibility_term_ids();
        $arr_not_in = array($product_visibility_terms['exclude-from-catalog']);

        // Hide out of stock products.
        if ('yes' === get_option('woocommerce_hide_out_of_stock_items')) {
            $arr_not_in[] = $product_visibility_terms['outofstock'];
        }

        if (!empty($arr_not_in)) {
            $query_args['tax_query'][] = array(
                'taxonomy' => 'product_visibility',
                'field' => 'term_taxonomy_id',
                'terms' => $arr_not_in,
                'operator' => 'NOT IN',
            );
        }

        $search_query = new WP_Query(apply_filters('nasa_query_live_search_products', $query_args));
        if ($the_posts = $search_query->get_posts()) {
            foreach ($the_posts as $the_post) {
                $title = get_the_title($the_post->ID);
                if (has_post_thumbnail($the_post->ID)) {
                    $post_thumbnail_ID = get_post_thumbnail_id($the_post->ID);
                    $post_thumbnail_src = wp_get_attachment_image_src($post_thumbnail_ID, 'thumbnail');
                } else {
                    $size = wc_get_image_size('thumbnail');
                    $post_thumbnail_src = array(
                        wc_placeholder_img_src(),
                        esc_attr($size['width']),
                        esc_attr($size['height'])
                    );
                }

                if ($product = wc_get_product($the_post->ID)) {
                    $results[] = array(
                        'title' => html_entity_decode($title, ENT_QUOTES, 'UTF-8'),
                        'tokens' => explode(' ', $title),
                        'url' => get_permalink($the_post->ID),
                        'image' => $post_thumbnail_src[0],
                        'price' => $product->get_price_html()
                    );
                }
            }
        }
        wp_reset_postdata();

        die(json_encode($results));
    }
endif;

add_action('wp_head', 'elessi_search_live_options', 0, 0);
if(!function_exists('elessi_search_live_options')) :
    function elessi_search_live_options() {
        global $nasa_opt;

        if ($enable = isset($nasa_opt['enable_live_search']) ? $nasa_opt['enable_live_search'] : true) {
            wp_enqueue_script('nasa-typeahead-js', ELESSI_THEME_URI . '/assets/js/min/typeahead.bundle.min.js', array('jquery'), null, true);
            wp_enqueue_script('nasa-handlebars', ELESSI_THEME_URI . '/assets/js/min/handlebars.min.js', array('nasa-typeahead-js'), null, true);
        }

        $search_options = array(
            'live_search_template' =>
                '<div class="item-search">' .
                    '<a href="{{url}}" class="nasa-link-item-search" title="{{title}}">' .
                        '<img src="{{image}}" class="nasa-item-image-search rtl-right" height="60" width="60" />' .
                        '<div class="nasa-item-title-search rtl-right">' .
                            '<p class="nasa-title-item">{{title}}</p>' .
                            '<div class="price text-left rtl-text-right">{{{price}}}</div>' .
                        '</div>' .
                    '</a>' .
                '</div>',
            'enable_live_search' => $enable,
            'limit_results' => (isset($nasa_opt['limit_results_search']) && (int) $nasa_opt['limit_results_search'] > 0) ? (int) $nasa_opt['limit_results_search'] : 5,
        );

        echo '<script>var search_options=';
        echo ($enable) ? json_encode($search_options) : '"0"';
        echo ';</script>';
    }
endif;

// Login Ajax
add_action('wp_ajax_nopriv_nasa_process_login', 'elessi_process_login');
add_action('wp_ajax_nasa_process_login', 'elessi_process_login');
if(!function_exists('elessi_process_login')) :
    function elessi_process_login() {
        $mess = array('error' => '1', 'mess' => esc_html__('Error.', 'elessi-theme'), '_wpnonce' => '0');
        !empty($_REQUEST['data']) or die(json_encode($mess));
        
        $input = array();
        foreach ($_REQUEST['data'] as $values) {
            if(isset($values['name']) && isset($values['value'])) {
                $input[$values['name']] = $values['value'];
            }
        }

        if(isset($input['woocommerce-login-nonce'])) {
            $nonce_value = $input['woocommerce-login-nonce'];
        } else {
            $nonce_value = isset($input['_wpnonce']) ? $input['_wpnonce'] : '';
        }

        // Check _wpnonce
        if(!wp_verify_nonce($nonce_value, 'woocommerce-login')) {
            $mess['_wpnonce'] = 'error';
            die(json_encode($mess));
        }

        if (!empty($_REQUEST['login'])) {
            $creds    = array();
            $username = trim($input['nasa_username']);

            $validation_Obj = new WP_Error();
            $validation_error = apply_filters('woocommerce_process_login_errors', $validation_Obj, $input['nasa_username'], $input['nasa_username']);

            // Login error
            if ($validation_error->get_error_code()) {
                $mess['mess'] = '<strong>' . esc_html__('Error', 'elessi-theme') . ':</strong> ' . $validation_error->get_error_message();

                die(json_encode($mess));
            }

            // Require username
            if (empty($username)) {
                $mess['mess'] = '<strong>' . esc_html__('Error', 'elessi-theme') . ':</strong> ' . esc_html__('Username is required.', 'elessi-theme');

                die(json_encode($mess));
            }

            // Require Password
            if (empty($input['nasa_password'])) {
                $mess['mess'] = '<strong>' . esc_html__('Error', 'elessi-theme') . ':</strong> ' . esc_html__('Password is required.', 'elessi-theme');

                die(json_encode($mess));
            }

            if (is_email($username) && apply_filters('woocommerce_get_username_from_email', true)) {
                $user = get_user_by('email', $username);

                if (!isset($user->user_login)) {
                    // Email error
                    $mess['mess'] = '<strong>' . esc_html__('Error', 'elessi-theme') . ':</strong> ' . esc_html__('A user could not be found with this email address.', 'elessi-theme');

                    die(json_encode($mess));
                }

                $creds['user_login'] = $user->user_login;
            } else {
                $creds['user_login'] = $username;
            }

            $creds['user_password'] = $input['nasa_password'];
            $creds['remember'] = isset($input['nasa_rememberme']);
            $secure_cookie = is_ssl() ? true : false;
            $user = wp_signon(apply_filters('woocommerce_login_credentials', $creds), $secure_cookie);

            if (is_wp_error($user)) {
                // Other Error
                $message = $user->get_error_message();
                $mess['mess'] = str_replace(
                    '<strong>' . esc_html($creds['user_login']) . '</strong>',
                    '<strong>' . esc_html($username) . '</strong>',
                    $message
                );

                die(json_encode($mess));
            } else {
                // Login success
                $mess['error'] = '0';
                if (! empty($input['nasa_redirect'])) {
                    $redirect = $input['nasa_redirect'];
                } elseif (wp_get_referer()) {
                    $redirect = wp_get_referer();
                } else {
                    $redirect = NASA_WOO_ACTIVED ? wc_get_page_permalink('myaccount') : home_url('/');
                }

                $mess['mess'] = esc_html__('Login success.', 'elessi-theme');
                $mess['redirect'] = $redirect;
            }
        }

        die(json_encode($mess));
    }
endif;

// Register Ajax
add_action('wp_ajax_nopriv_nasa_process_register', 'elessi_process_register');
add_action('wp_ajax_nasa_process_register', 'elessi_process_register');
if(!function_exists('elessi_process_register')) :
    function elessi_process_register() {
        !empty($_REQUEST['data']) or die;
        $mess = array('error' => '1', 'mess' => esc_html__('Error.', 'elessi-theme'), '_wpnonce' => '0');
        $input = array();
        
        foreach ($_REQUEST['data'] as $values) {
            if(isset($values['name']) && isset($values['value'])) {
                $input[$values['name']] = $values['value'];
            }
        }
        
        if(isset($input['woocommerce-register-nonce'])) {
            $nonce_value = $input['woocommerce-register-nonce'];
        } else {
            $nonce_value = isset($input['_wpnonce']) ? $input['_wpnonce'] : '';
        }
        
        // Check _wpnonce
        if(!wp_verify_nonce($nonce_value, 'woocommerce-register')) {
            $mess['_wpnonce'] = 'error';
            die(json_encode($mess));
        }

        if (! empty($_REQUEST['register'])) {
            $username = 'no' === get_option('woocommerce_registration_generate_username') ? $input['nasa_username'] : '';
            $password = 'no' === get_option('woocommerce_registration_generate_password') ? $input['nasa_password'] : '';
            $email    = $input['nasa_email'];

            $validation_Obj = new WP_Error();
            $validation_error = apply_filters('woocommerce_process_registration_errors', $validation_Obj, $username, $password, $email);

            if ($validation_error->get_error_code()) {
                $mess['mess'] = $validation_error->get_error_message();
                die(json_encode($mess));
            }

            $new_customer = wc_create_new_customer(sanitize_email($email), wc_clean($username), $password);

            if (is_wp_error($new_customer)) {
                $mess['mess'] = $new_customer->get_error_message();
                die(json_encode($mess));
            }

            if (apply_filters('woocommerce_registration_auth_new_customer', true, $new_customer)) {
                wc_set_customer_auth_cookie($new_customer);
            }

            // Register success.
            $mess['error'] = '0';
            $mess['mess'] = esc_html__('Register success.', 'elessi-theme');
            $mess['redirect'] = apply_filters('woocommerce_registration_redirect', wp_get_referer() ? wp_get_referer() : (NASA_WOO_ACTIVED ? wc_get_page_permalink('myaccount') : home_url('/')));
        }

        die(json_encode($mess));
    }
endif;

// **********************************************************************//
//	Support Multi currency - AJAX
// **********************************************************************//
if(class_exists('WCML_Multi_Currency')) :
    add_filter('wcml_multi_currency_ajax_actions', 'elessi_multi_currency_ajax', 10, 1);
    if(!function_exists('elessi_multi_currency_ajax')) :
        function elessi_multi_currency_ajax($ajax_actions) {
            return elessi_ajax_actions($ajax_actions);
        }
    endif;
endif;
