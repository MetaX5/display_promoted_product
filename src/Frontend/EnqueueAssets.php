<?php

namespace MM\WoocommercePromotedProduct\Frontend;

class EnqueueAssets
{
    public function __construct()
    {
        add_action('wp_enqueue_scripts', array($this, 'enqueueAssets'));
    }

    public function enqueueAssets()
    {
        wp_enqueue_style('wpp-style', WPP_PLUGIN_DIR . 'assets/public/css/wpp_styles.css', array(), false, 'all');
    }
}
