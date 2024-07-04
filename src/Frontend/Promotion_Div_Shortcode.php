<?php

namespace MM\WoocommercePromotedProduct\Frontend;

class Promotion_Div_Shortcode
{
    public function __construct()
    {
        //add_action('woocommerce_before_main_content', array($this, 'contentContentBeforeMainContent'));

        add_shortcode('wpp_promotion', array($this, 'promotionDivShortcode'));
    }

    /**
     * Add shortcode to display the promoted product div
     *
     * @return void
     */
    public function promotionDivShortcode()
    {
        $bgColor = (get_option('wc_promotion_bg_color')) ? get_option('wc_promotion_bg_color') : 'inherit';
        $textColor = (get_option('wc_promotion_text_color')) ? get_option('wc_promotion_text_color') : 'inherit';

        $styles = "background-color: $bgColor; color: $textColor;";

        if (get_option('wpp_promoted_product_id') !== false) {
            $html = '<div style="' . $styles . '" class="promotionDiv">';
            $productId = get_option('wpp_promoted_product_id');
            $promotedTitle = get_option('wc_promotion_text', 'Flash Sale');

            $productTitle = get_the_title();

            if ('' !== trim(get_post_meta($productId, 'wpp_text_field_title', true)) && null !== get_post_meta($productId, 'wpp_text_field_title', true)) {
                $productTitle = get_post_meta($productId, 'wpp_text_field_title', true);
            }

            $html .= '
                            <span>' . $promotedTitle . ': ' . $productTitle . ' </span>
                        ';

            $html .= '</div>';
            return $html;
        }
    }
}
