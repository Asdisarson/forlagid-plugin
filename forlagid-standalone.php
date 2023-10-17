<?php
/**
 * Plugin Name: Forlagid Audiobooks
 * Plugin URI: https://islandsvefir.is/
 * Description: A plugin designed for forlagid.is to handle various aspects of audiobook management. It does product variation management, handles checkbox settings, modifies tables for audiobooks, adjusts login redirects, and sends notifications for purchase or delivery issues. It customizes cart functions and payment gateways for audiobooks.
 * Version: 1.0.0
 * Author: Íslandsvefir
 * Author URI: https://islandsvefir.is/
 * Requires at least: 7.4
 * Requires PHP: 8.0
 *
 * WC requires at least: 5.7
 * WC tested up to: 6.3.1
 *
 * Text Domain: forlagid-audiobooks
 * Domain Path: /languages/
 *
 * @package Forlagid_Audiobooks
 */

add_action('plugins_loaded', function() {
    if (class_exists('WooCommerce')) {
        include_once plugin_dir_path(__FILE__) . 'inc/class-forlagid-audiobooks.php';
    }
});


/**
 * Logic for cron to handle failed requests to the epub server.
 */

/**
 * Add a custom interval for 5 minutes.
 *
 * @var array $schedules The current schedules
 *
 * @return array
 */
function forlagid_add_5min_interval( $schedules ) {

    if ( ! array_key_exists( 'forlagid5min', $schedules ) ) {
        $schedules['forlagid5min'] = array(
            'interval' => 5 * 60,
            'display'  => __( 'Every 5 minutes', 'forlagid' )
        );
    }

    return $schedules;
}

add_filter( 'cron_schedules', 'forlagid_add_5min_interval' );


register_activation_hook( __FILE__, 'forlagid_add_connection_fix_cron' );

/**
 * Add cron every 5 minutes to attempt to fix errors, if any.
 */
function forlagid_add_connection_fix_cron() {
    if ( ! wp_next_scheduled( 'forlagid_attempt_connection_fix' ) ) {
        wp_schedule_event( time(), 'forlagid5min', 'forlagid_attempt_connection_fix' );
    }
}

/**
 * Grab all errors and try to call again in order to fix problem.
 */
function forlagid_attempt_fix_connection_error() {
    $errors = get_option( 'foldagid_hlusta_errors', array() );

    foreach ( $errors as $error ) {

        if ( 'audiobook' === $error['type'] ) {
            $order = wc_get_order( $error['order_id'] );
            if ( $order ) {
                Forlagid_Audiobooks::add_audiobook_to_user( $error['item_id'], $order, $order['product_id'] );
            }
        }

    }

}
add_action( 'forlagid_attempt_connection_fix', 'forlagid_attempt_fix_connection_error' );

/**
 * Store the request which threw an error.
 *
 * @param $type
 * @param $id
 * @param $order_id
 * @param $item_id
 * @param $product_id
 */
function forlagid_hlusta_connection_error( $type, $id, $order_id, $item_id, $product_id ) {
    $errors = get_option( 'foldagid_hlusta_errors', array() );
    $new    = true;
    foreach ( $errors as $error_key => $error ) {
        if ( $error['order_id'] === $order_id && $error['id'] === $id ) {
            // Existing error, increment or remove.
            $new = false;
            if ( intval( $error['tries'] ) > 3 ) {
                // If already tried 3 times, give up.
                unset( $errors[ $error_key ] );
                break;
            } else {
                // Increment tries number and continue.
                $errors[ $error_key ]['tries'] = intval( $error['tries'] ) + 1;
                break;
            }
        }
    }
    if ( $new ) {
        $errors[] = array(
            'type'       => $type,
            'id'         => $id,
            'order_id'   => $order_id,
            'item_id'    => $item_id,
            'product_id' => $product_id,
            'tries'      => 1,
        );
    }
    update_option( 'foldagid_hlusta_errors', $errors );
}
