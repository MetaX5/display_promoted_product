<?php

namespace MM\WoocommercePromotedProduct\Cron;

class ProductPromotionTime
{
    public function __construct()
    {
        add_action('wpp_cron_hook', array($this, 'wppCronExec'));
        add_filter('cron_schedules', array($this, 'cronInterval'));
        add_action('post_updated', array($this, 'afterPostSave'), 10, 3);
    }

    /**
     * Add custom cron interval
     *
     * @param [type] $schedules
     * @return void
     */
    public function cronInterval($schedules)
    {
        $schedules['one_minute'] = array(
            'interval' => 60,
            'display'  => esc_html__('Every Minute'),
        );
        return $schedules;
    }

    /**
     * Executes the cron to check if the product should be still promoted 
     * if not, removes the promotion, other related fields and unschedules the cron
     *
     * @return void
     */
    public function wppCronExec()
    {
        if (get_option('wpp_date_to_remove_promotion') === false) {
            $timestamp = wp_next_scheduled('wpp_cron_hook');
            wp_unschedule_event($timestamp, 'wpp_cron_hook');
            return;
        }

        $dateToRemovePromotion = get_option('wpp_date_to_remove_promotion');
        date_default_timezone_set('Europe/Warsaw');
        $actualDate = date('Y/m/d H:i');
        $actualDateTimestamp = strtotime($actualDate);
        $dateToRemovePromotionTimestamp = strtotime($dateToRemovePromotion);

        if ($actualDateTimestamp >= $dateToRemovePromotionTimestamp) {
            $productID = get_option('wpp_promoted_product_id');
            update_post_meta($productID, 'wpp_checkbox_1', 'no');
            update_post_meta($productID, 'wpp_checkbox_2', 'no');
            update_post_meta($productID, 'wpp_date_to_remove_promotion', '');
            delete_option('wpp_promoted_product_id');
            delete_option('wpp_previous_promoted_product_id');
            $timestamp = wp_next_scheduled('wpp_cron_hook');
            wp_unschedule_event($timestamp, 'wpp_cron_hook');
        }
    }

    /**
     * Check if cron is already scheduled
     * If not, schedule it
     * If yes, unschedule it and schedule it again
     * 
     * @param [type] $postID
     * @param [type] $postAfter
     * @param [type] $postBefore
     * @return void
     */
    public function afterPostSave($postID, $postAfter, $postBefore)
    {
        if ('product' !== $postAfter->post_type) {
            return;
        }
        if (!isset($_POST['wpp_date_to_remove_promotion'])) {
            return;
        }
        if (!isset($_POST['wpp_checkbox_1']) || !isset($_POST['wpp_checkbox_2'])) {
            return;
        }

        if (!wp_next_scheduled('wpp_cron_hook')) {
            wp_schedule_event(time(), 'one_minute', 'wpp_cron_hook');
        } else {
            $timestamp = wp_next_scheduled('wpp_cron_hook');
            wp_unschedule_event($timestamp, 'wpp_cron_hook');
            wp_schedule_event(time(), 'one_minute', 'wpp_cron_hook');
        }
    }
}
