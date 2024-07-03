<?php

namespace MM\WoocommercePromotedProduct\Frontend;

use MM\WoocommercePromotedProduct\Admin\Search_Products_By_Custom_Field;

class Promotion_Div_Shortcode
{
    public function __construct()
    {
        //add_action('woocommerce_before_main_content', array($this, 'contentContentBeforeMainContent'));
        /*add_filter('posts_request', function ($sql) {
            error_log('SQL=' . $sql);
            return $sql;
        });*/
        add_shortcode('wpp_promotion', array($this, 'promotionDivShortcode'));
    }

    public function promotionDivShortcode()
    {
        $productsQuery = Search_Products_By_Custom_Field::searchProductsByCustomField();
        if ($productsQuery->have_posts()) {
            $html = '<div class="promotionDiv">';
            while ($productsQuery->have_posts()) {
                $productsQuery->the_post();

                $productId = get_the_ID();

                $promotedTitle = get_option('wc_promotion_text', 'Flash Sale');

                $productTitle = get_the_title();

                if ('' !== trim(get_post_meta($productId, 'wpp_text_field_title', true)) && null !== get_post_meta($productId, 'wpp_text_field_title', true)) {
                    $productTitle = get_post_meta($productId, 'wpp_text_field_title', true);
                }

                $html .= '
                            <p>' . $promotedTitle . ': ' . $productTitle . ' </p>
                        ';

                $html .= '</div>';
            }
            return $html;
            wp_reset_postdata();
        }
    }
}
