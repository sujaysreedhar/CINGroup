<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>" />
<meta http-equiv="X-UA-Compatible" content="IE=Edge" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<?php if (function_exists('wp_site_icon')) : ?>
    <link rel="shortcut icon" href="<?php echo (isset($nasa_opt['site_favicon']) && $nasa_opt['site_favicon']) ? esc_attr($nasa_opt['site_favicon']) : ELESSI_THEME_URI . '/favicon.ico'; ?>" />
<?php endif; ?>
    
<title><?php echo bloginfo('name'); ?> - <?php echo esc_html__('Coming Soon', 'elessi-theme'); ?></title>
<link rel="stylesheet" href="<?php echo ELESSI_THEME_URI; ?>/assets/font-awesome-4.7.0/css/font-awesome.min.css" />
<?php
$type_font_select = isset($nasa_opt['type_font_select']) ? $nasa_opt['type_font_select'] : '';
$type_headings = isset($nasa_opt['type_headings']) ? $nasa_opt['type_headings'] : '';
$type_texts = isset($nasa_opt['type_texts']) ? $nasa_opt['type_texts'] : '';
$custom_font = isset($nasa_opt['custom_font']) ? $nasa_opt['custom_font'] : '';
$fontFamily = '';
$fontHeading = '';
$fontSets = '';
    
/**
 * Select Font custom use load site
 */
if($type_font_select == 'custom' && $custom_font) {
    global $nasa_upload_dir;

    $nasa_upload_dir = !isset($nasa_upload_dir) ? wp_upload_dir() : $nasa_upload_dir;

    if(is_file($nasa_upload_dir['basedir'] . '/nasa-custom-fonts/' . $custom_font . '/' . $custom_font . '.css')) {
        $fontSets = $nasa_upload_dir['baseurl'] . '/nasa-custom-fonts/' . $custom_font . '/' . $custom_font . '.css';
    }
    
    $fontFamily = $fontHeading = $custom_font;
}

/**
 * Select Google Font use load site
 */
elseif ($type_font_select == 'google') {
    $default_fonts = array(
        "Open Sans",
        "Helvetica",
        "Arial",
        "Sans-serif"
    );

    $googlefonts = array();

    if($type_headings) {
        $googlefonts[] = $type_headings;
        $fontHeading = $type_headings;
    }
    if($type_texts) {
        $googlefonts[] = $type_texts;
        $fontFamily = $type_texts;
    }

    $nasa_font_family = array();
    $nasa_font_set = array('latin');

    if (!empty($nasa_opt['type_subset'])) {
        foreach ($nasa_opt['type_subset'] as $key => $val) {
            if($val && !in_array($key, $nasa_font_set)) {
                $nasa_font_set[] = $key;
            }
        }
    }

    foreach ($googlefonts as $googlefont) {
        if (!in_array($googlefont, $default_fonts)) {
            $default_fonts[] = $googlefont;
            $nasa_font_family[] = $googlefont . ':400,400italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic';
        }
    }

    if (!empty($nasa_font_family) && !empty($nasa_font_set)) {
        $fontSets = elessi_google_fonts_url($nasa_font_family, $nasa_font_set);
    }
}

if ($fontSets) {
    echo '<link rel="stylesheet" href="' . $fontSets . '" />';
}
?>
<style>
    body {
        font-family: <?php echo $fontFamily ? '"' . $fontFamily . '", ' : ''; ?>helvetica, arial, sans-serif;
        text-align: center;
        color: #333;
        font-size: 20px;
        padding: 0;
        margin: 0;
        direction: <?php echo isset($nasa_opt['nasa_rtl']) && $nasa_opt['nasa_rtl'] ? 'rtl' : 'ltr'; ?>
    }
    
    h1, h2, h3, h4, h5, h6 {
        font-family: <?php echo $fontHeading ? '"' . $fontHeading . '", ' : ''; ?>helvetica, arial, sans-serif;
        font-weight: 900;
    }
    
    .comming-soon-info {
        color: #8D8D8D;
        font-size: 15px;
        line-height: 1.6;
        margin-bottom: 50px;
    }
    
    .img-coming-soon {
        max-width: 100%;
    }
    
    .follow-icon {
        margin: 30px auto;
    }
    
    a.icon {
        color: #8D8D8D;
        margin: 0 10px;
        font-size: 15px;
    }
    
    a.icon:hover {
        color: #333;
    }
    
    .main-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px 0;
        position: relative;
        z-index: 2;
    }
    
    .countdown-row {
        margin: 0 -5px;
    }
    
    .countdown-section {
        margin: 0 40px;
        display: inline-block;
    }
    
    .countdown-amount {
        display: block;
        font-size: 45px;
        line-height: 1;
        font-weight: 800;
    }
    
    .countdown-period {
        display: block;
        font-size: 15px;
        line-height: 1;
        color: #8D8D8D;
    }
    
    @media screen and (max-width: 640px) {
        .countdown-section {
            margin: 0 15px;
        }
        
        .countdown-amount {
            font-size: 30px;
        }
    }
</style>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
</head>

<body>
    <?php
    echo isset($nasa_opt['coming_soon_title']) ? '<h1>' . $nasa_opt['coming_soon_title'] . '</h1>' : '';
    echo isset($nasa_opt['coming_soon_info']) ? '<p class="comming-soon-info">' . $nasa_opt['coming_soon_info'] . '</p>' : '';
    ?>
    
    <?php if (!empty($nasa_opt['coming_soon_img'])) : ?>
        <img class="img-coming-soon" src="<?php echo $nasa_opt['coming_soon_img']; ?>" />
    <?php else : ?>
        <img class="img-coming-soon" src="<?php echo ELESSI_THEME_URI; ?>/assets/images/commingsoon.jpg" />
    <?php endif; ?>
    <div class="main-container">
        <?php if ($time) :
            echo '<h3>' . esc_html__('New Store we be Launched in', 'elessi-theme') . '</h3>';
            echo '<span class="countdown" data-countdown="' . esc_attr(get_date_from_gmt(date('Y-m-d H:i:s', $time), 'M j Y H:i:s O')) . '"></span>';
        endif; ?>
    </div>
    
    <div class="comingsoon-follow-icons"><?php echo shortcode_exists('nasa_follow') ? do_shortcode('[nasa_follow]') : ''; ?></div>
    
    <script>var nasa_countdown_l10n = <?php echo json_encode(
        array(
            'days'      => esc_html__('days', 'elessi-theme'),
            'months'    => esc_html__('months', 'elessi-theme'),
            'weeks'     => esc_html__('weeks', 'elessi-theme'),
            'years'     => esc_html__('years', 'elessi-theme'),
            'hours'     => esc_html__('hours', 'elessi-theme'),
            'minutes'   => esc_html__('mins', 'elessi-theme'),
            'seconds'   => esc_html__('secs', 'elessi-theme'),
            'day'       => esc_html__('day', 'elessi-theme'),
            'month'     => esc_html__('month', 'elessi-theme'),
            'week'      => esc_html__('week', 'elessi-theme'),
            'year'      => esc_html__('year', 'elessi-theme'),
            'hour'      => esc_html__('hour', 'elessi-theme'),
            'minute'    => esc_html__('min', 'elessi-theme'),
            'second'    => esc_html__('sec', 'elessi-theme')
        )
    ); ?></script>
    <script src="<?php echo ELESSI_THEME_URI . '/assets/js/min/countdown.min.js'; ?>"></script>
    <script src="<?php echo ELESSI_THEME_URI . '/assets/js/min/coming-soon.min.js'; ?>"></script>
</body>
</html>
