<?php
/**
 * The Sidebar containing the main widget areas.
 *
 * @package nasatheme
 */
?>

<div id="secondary" class="widget-area">
    <?php
    do_action('before_sidebar');
    dynamic_sidebar('blog-sidebar');
    do_action('after_sidebar');
    ?>
</div>