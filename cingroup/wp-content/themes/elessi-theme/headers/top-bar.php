<?php 
$topbar_left = !isset($topbar_left) ? '' : $topbar_left;
$class_topbar = !isset($class_topbar) ? '' : $class_topbar;
$class_topbar .= trim($topbar_left) != '' ? '' : ' hide-for-mobile';
?>
<div class="nasa-topbar-wrap<?php echo esc_attr($class_topbar); ?>">
    <div id="top-bar" class="top-bar">
    <marquee>Hoc HTML tai VietJack - Vi du cach su dung the marquee.</marquee>
        <?php if (!$mobile) : ?>
            <!-- Desktop | Responsive Top-bar -->
            <div class="row">
                <div class="large-12 columns">
                    <!-- <div class="left-text left rtl-right">
                        <div class="inner-block">
                            <?php  /* echo $topbar_left; */ ?>
                        </div>
                    </div>
                    <div class="right-text nasa-hide-for-mobile right rtl-left">
                        <div class="topbar-menu-container">
                            <?php /*do_action('nasa_support_multi_languages');*/ ?>
                            <?php /*elessi_get_menu('topbar-menu', 'nasa-topbar-menu', 1); */?>
                            <?php /*echo elessi_tiny_account(true); */?>
                        </div>
                    </div> -->
                </div>
            </div>
        <?php else : ?>
            <!-- Mobile Top-bar -->
            <div class="topbar-mobile-text">
                <?php echo $topbar_left; ?>
            </div>
            <div class="topbar-menu-container hidden-tag">
                <?php do_action('nasa_support_multi_languages'); ?>
                <?php elessi_get_menu('topbar-menu', 'nasa-topbar-menu', 1); ?>
            </div>
        <?php endif; ?>
    </div>
    
    <?php if (!$mobile) : ?>
        <div class="nasa-hide-for-mobile">
            <a class="nasa-icon-toggle" href="javascript:void(0);">
                <i class="nasa-topbar-up pe-7s-angle-up"></i>
                <i class="nasa-topbar-down pe-7s-angle-down"></i>
            </a>
        </div>
    <?php endif; ?>
</div>
