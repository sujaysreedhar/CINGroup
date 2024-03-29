<?php

/* Mega menu use front-end */

class Nasa_Nav_Menu extends Walker_Nav_Menu {

    const DF_MEGA_COLS = 3; // Default mega columns
    private $_mega = array();
    private $_colums = 0;
    private $_even = true;

    private function getOption($itemID, $field = '') {
        return get_post_meta($itemID, '_menu_item_nasa_' . $field, true);
    }

    public function start_lvl(&$output, $depth = 0, $args = array()) {
        $class_names = $depth == '0' ? 'nav-dropdown' : 'nav-column-links';
        
        $clss_lvl = '';
        if($this->_colums) {
            $clss_lvl .= ' large-block-grid-' . $this->_colums . ' medium-block-grid-' . $this->_colums . ' small-block-grid-' . $this->_colums;
        }
        
        $this->_colums = 0;
        
        $output .= '<div class="' . $class_names . '"><ul class="sub-menu' . $clss_lvl . '">';
    }

    public function end_lvl(&$output, $depth = 0, $args = array()) {
        $output .= '</ul></div>';
    }

    public function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
        if($item->post_type != 'nav_menu_item') {
            return;
        }
        
        $megamenu_class = $megacolumns = $mega_top = $hr = '';
        $megamenu = false;
        $class_even_odd = '';
        
        if ($depth == 0) {
            $megamenu = $this->getOption($item->ID, 'enable_mega');
            $megamenu_class = ' default-menu root-item';
            $class_even_odd = $this->_even ? ' nasa_even' : ' nasa_odd';
            $this->_even = !$this->_even;
        }

        if ($megamenu) {
            $megamenu_class = ' nasa-megamenu root-item';
            $megacolumnsfix = $this->getOption($item->ID, 'columns_mega');
            $megacolumns = !$megacolumnsfix ? ' cols-' . self::DF_MEGA_COLS : ' cols-' . $megacolumnsfix;
            $this->_colums = !$megacolumnsfix ? self::DF_MEGA_COLS : $megacolumnsfix;
            $full = $this->getOption($item->ID, 'enable_fullwidth');
            $megacolumns .= $full ? ' fullwidth' : '';
            $this->_mega[] = $item->ID;
        }

        $image_mega_id = $position = $bg = $image_mega = '';
        $title_menu = apply_filters('the_title', $item->title, $item->ID);
        $title_disable = false;
        
        if ($this->getOption($item->ID, 'image_mega_enable')) {
            $image_mega_id = $this->getOption($item->ID, 'image_mega');
            
            if ($image_mega_id) {
                $title_disable = $this->getOption($item->ID, 'disable_title_image_mega');
                $position = $this->getOption($item->ID, 'position_image_mega');
                $dimentions = $image_mega_src = '';
                
                if (is_numeric($image_mega_id)) {
                    $image = wp_get_attachment_image_src($image_mega_id, 'full');
                    if (isset($image[0])) {
                        $image_mega_src = $image[0];
                        $dimentions .= isset($image[1]) ? ' width="' . $image[1] . '"' : '';
                        $dimentions .= isset($image[2]) ? ' height="' . $image[2] . '"' : '';
                    }
                } else {
                    $image_mega_src = $image_mega_id;
                }
                
                if ($image_mega_src) {
                    if ($position == 'bg') {
                        $bg = ' style="background: url(\'' . esc_url($image_mega_src) . '\') center center no-repeat"';
                    } else {
                        $image_mega = '<img src="' . esc_url($image_mega_src) . '" alt="' . esc_attr($title_menu) . '"' . $dimentions . ' />';
                    }
                }
            }
        }

        $menu_icon = $this->getOption($item->ID, 'icon_menu');
        $icon = $menu_icon ? '<i class="nasa-menu-item-icon ' . esc_attr($menu_icon) . '"></i>' : '';

        if ($depth == 1 && in_array($item->menu_item_parent, $this->_mega)) {
            $mega_top = ' megatop';
            $hr = '<hr class="hr-nasa-megamenu" />';
        }

        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $has_child = in_array('menu-item-has-children', $classes);
        
        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item));
        $el_class = trim($this->getOption($item->ID, 'el_class'));
        $class_names .= $el_class != '' ? ' ' . $el_class : '';
        
        $class_names = ' class="' . esc_attr($class_names) . $megamenu_class . $megacolumns . $mega_top . $class_even_odd . '"';
        $item_output = '<li' . $class_names . $bg . '>';

        $attributes = ' title="' . (!empty($item->attr_title) ? esc_attr($item->attr_title) : esc_attr($title_menu)) . '"';
        
        $attributes .= !empty($item->target) ? ' target="' . esc_attr($item->target) . '"' : '';
        $attributes .= !empty($item->xfn) ? ' rel="' . esc_attr($item->xfn) . '"' : '';
        
        $_href = !empty($item->url) ? esc_url($item->url) : 'javascript:void(0);';
        $attributes .= ' href="' . $_href . '"';

        $description = ($depth != 0) ? '' : (
            !empty($item->description) ? '<span class="hidden-tag nasa-menu-description">' . esc_attr($item->description) . '</span>' : ''
        );

        $prepend = '';
        $prepend .= !empty($item->menu_icon) ? '<span class="' . esc_attr($item->menu_icon) . ' nasa-menu_icon"></span>' : '';

        $item_output .= ($position == 'before' && $image_mega) ? '<a class="nasa-hide-for-mobile" ' . $attributes . '>' . $image_mega . '</a>' : '';

        $item_output .= isset($args->before) ? $args->before : '';
        
        $classItem = !$title_disable ? '' : ' class="hidden-tag"';
        $title_menu = '<span class="nasa-text-menu">' . $title_menu . '</span>';
        $item_output .= '<a' . $attributes . $classItem . '>';
        $item_output .= $icon;
        $item_output .= $depth == 0 ? '<i class="pe-7s-angle-down nasa-open-child"></i>' : '';
        $item_output .= isset($args->link_before) ? $args->link_before . $prepend . $title_menu : '';
        $item_output .= $has_child ? '<i class="fa fa-angle-right nasa-has-items-child"></i>' : '';
        $item_output .= '</a>';

        $item_output .= isset($args->link_after) ? $description . $args->link_after : '';
        $item_output .= !$title_disable ? $hr : '';
        $item_output .= isset($args->after) ? $args->after : '';
        $item_output .= ($position == 'after' && $image_mega) ? '<a class="nasa-hide-for-mobile" ' . $attributes . '>' . $image_mega . '</a>' : '';
        
        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args, $id);
    }
    
    public function end_el(&$output, $item, $depth = 0, $args = array()) {
        if($item->post_type != 'nav_menu_item') {
            return;
        }
        
        $n = "\n";
        if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
            $n = '';
        }
        
        $output .= "</li>{$n}";
    }

}
