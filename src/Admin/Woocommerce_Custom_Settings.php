<?php

namespace MM\WoocommercePromotedProduct\Admin;

class Woocommerce_Custom_Settings
{
    public function __construct()
    {
        // add new section
        add_filter('woocommerce_get_sections_products', array($this, 'addSection'), 10, 1);

        // add new settings to the created section
        add_filter('woocommerce_get_settings_products', array($this, 'woocommerceProductsSettingsTabContent'), 10, 2);

        add_action('woocommerce_admin_field_link', array($this, 'adminFieldLink'), 10, 1);
    }

    /**
     * Add new section to the woocommerce settings products tab
     *
     * @param [type] $sections
     * @return void
     */
    public function addSection($sections)
    {

        $sections['wc_promotion'] = esc_html__('Product Promotion', WPP_TEXT_DOMAIN);

        return $sections;
    }

    /**
     * Add new fields to the created section
     *
     * @param [type] $settings
     * @param [type] $currentSection
     * @return void
     */
    public function woocommerceProductsSettingsTabContent($settings, $currentSection)
    {
        if ('wc_promotion' !== $currentSection) {
            return $settings;
        }

        $settingsArray = array();

        // Add Title to the Settings

        $settingsArray[] = array('name' => esc_html__('Product Promotion', WPP_TEXT_DOMAIN), 'type' => 'title', 'desc' => __('The following options are used to configure Product Promotion', WPP_TEXT_DOMAIN), 'id' => 'wc_promotion');


        // add text field
        $settingsArray[] = array(
            'name' => esc_html__('Title for a promoted product', WPP_TEXT_DOMAIN),
            'desc_tip' => __('This is the title for a promoted product', WPP_TEXT_DOMAIN),
            'id' => 'wc_promotion_text',
            'type' => 'text',

        );

        // add colorpicker for background color
        $settingsArray[] = array(
            'name' => esc_html__('Background color', WPP_TEXT_DOMAIN),
            'id' => 'wc_promotion_bg_color',
            'type' => 'color',

        );

        // add colorpicker for text color
        $settingsArray[] = array(
            'name' => esc_html__('Text color', WPP_TEXT_DOMAIN),
            'id' => 'wc_promotion_text_color',
            'type' => 'color',

        );

        // add information about promoted product
        if (get_option('wpp_promoted_product_id') !== false) {
            $productID = get_option('wpp_promoted_product_id');
            $productLink = get_edit_post_link($productID);
            $productTitle = get_the_title($productID);
            $settingsArray[] = array(
                'title' => esc_html__('Promoted product', WPP_TEXT_DOMAIN),
                'id' => 'wc_promotion_product_link',
                'type' => 'link',
                'link' => $productLink,
                'text' => $productTitle,
            );
        }

        $settingsArray[] = array('type' => 'sectionend', 'id' => 'wc_promotion');
        return $settingsArray;
    }

    public function adminFieldLink($value)
    {
?>
        <tr valign="top">
            <th scope="row" class="titledesc">
                <p>
                    <?php echo esc_html($value['title']); ?>
                </p>
            </th>
            <td class="forminp forminp-<?php echo esc_attr(sanitize_title($value['type'])); ?>">
                <a name="<?php echo esc_attr($value['field_name']); ?>" id="<?php echo esc_attr($value['id']); ?>" href="<?php echo esc_url($value['link']); ?>"><?php echo esc_textarea($value['text']); ?></a>
            </td>
        </tr>
<?php
    }
}
