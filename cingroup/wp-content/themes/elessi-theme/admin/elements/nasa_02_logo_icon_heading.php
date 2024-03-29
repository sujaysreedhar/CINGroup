<?php
add_action('init', 'elessi_logo_icon_heading');
if (!function_exists('elessi_logo_icon_heading')) {
    function elessi_logo_icon_heading() {
        
        /**
         * The Options Array
         * Set the Options Array
         */
        global $of_options;
        if(empty($of_options)) {
            $of_options = array();
        }
        
        $of_options[] = array(
            "name" => esc_html__("Logo and Favicon", 'elessi-theme'),
            "target" => 'logo-icons',
            "type" => "heading"
        );

        $of_options[] = array(
            "name" => esc_html__("Logo", 'elessi-theme'),
            "id" => "site_logo",
            "std" => ELESSI_THEME_URI . "/assets/images/logo.png",
            "type" => "media",
            "mod" => "min"
        );
        
        $of_options[] = array(
            "name" => esc_html__("Retina Logo", 'elessi-theme'),
            "id" => "site_logo_retina",
            "std" => ELESSI_THEME_URI . "/assets/images/logo-retina.png",
            "type" => "media",
            "mod" => "min"
        );

        $of_options[] = array(
            "name" => esc_html__("Max Height Logo", 'elessi-theme'),
            "id" => "max_height_logo",
            "std" => "40px",
            "type" => "text"
        );
        
        $of_options[] = array(
            "name" => esc_html__("Max Height Logo in Mobile", 'elessi-theme'),
            "id" => "max_height_mobile_logo",
            "std" => "25px",
            "type" => "text"
        );
        
        $of_options[] = array(
            "name" => esc_html__("Max Height Logo in Header Sticky", 'elessi-theme'),
            "id" => "max_height_sticky_logo",
            "std" => "25px",
            "type" => "text"
        );

        $of_options[] = array(
            "name" => esc_html__("Favicon", 'elessi-theme'),
            "desc" => esc_html__("Add your custom Favicon image. 16x16 - 32x32 *.ico or *.png required. (Recommended 16x16 *.ico)", 'elessi-theme'),
            "id" => "site_favicon",
            "std" => "",
            "type" => "media",
            "mod" => "min"
        );
    }
}
