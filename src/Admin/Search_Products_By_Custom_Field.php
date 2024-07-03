<?php

namespace MM\WoocommercePromotedProduct\Admin;

class Search_Products_By_Custom_Field
{
    public static function searchProductsByCustomField()
    {
        $query = new \WP_Query([
            'post_type'      => 'product',
            'posts_per_page' => -1,
            'orderby'        => 'modified',
            'order'          => 'DESC',
            'post_status'    => 'publish',
            'meta_query'     => [
                [
                    'key'   => 'wpp_checkbox_1',
                    'value' => 'yes',
                ],
            ],
        ]);

        return $query;
    }
}
