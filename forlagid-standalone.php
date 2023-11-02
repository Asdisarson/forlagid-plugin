<?php
/**
 * Plugin Name: Forlagid Audiobooks
 * Plugin URI: https://islandsvefir.is/
 * Description: A plugin designed for forlagid.is to handle various aspects of audiobook management. It does product variation management, handles checkbox settings, modifies tables for audiobooks, adjusts login redirects, and sends notifications for purchase or delivery issues. It customizes cart functions and payment gateways for audiobooks.
 * Version: 1.0.0
 * Author: Íslandsvefir
 * Author URI: https://islandsvefir.is/
 * Requires at least: 5.0
 * Requires PHP: 7.4
 *
 * WC requires at least: 5.7
 * WC tested up to: 6.3.1
 *
 * Text Domain: forlagid-audiobooks-new
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
 * Adds a 5-minute interval schedule to the given schedules array.
 *
 * @param array $schedules An array of schedules.
 *
 * @return array The updated schedules array.
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
 * Adds a cron job to fix connection errors with Forlagid.
 *
 * @return void
 */
function forlagid_add_connection_fix_cron() {
    if ( ! wp_next_scheduled( 'forlagid_attempt_connection_fix' ) ) {
        wp_schedule_event( time(), 'forlagid5min', 'forlagid_attempt_connection_fix' );
    }
}

/**
 * Attempts to fix connection errors with Foldagid Hlusta.
 *
 * Retrieves the list of connection errors from the Foldagid Hlusta plugin, and
 * iterates through each error. If the error is related to an audiobook, it
 * attempts to add the audiobook to the user's library by calling the
 * `add_audiobook_to_user` method of the `Forlagid_Audiobooks` class.
 *
 * @return void
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
 * Handles connection errors for Forlagid Hlusta.
 *
 * This function adds a connection error to the list of errors if it is a new error,
 * or increments the number of tries for an existing error. If an error has been tried
 * more than 3 times, it is removed from the list.
 *
 * @param string $type The type of error.
 * @param string $id The ID of the error.
 * @param int $order_id The ID of the order associated with the error.
 * @param int $item_id The ID of the item associated with the error.
 * @param int $product_id The ID of the product associated with the error.
 *
 * @return void
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

/**
 * Adds an audiobook link to the order.
 *
 * Retrieves the items from the order and iterates through each item. If the
 * item is an audiobook, it prints a message with a link to access the audiobook
 * in the Foldagid Hlusta app or web browser.
 *
 * @param WC_Order $order The order object.
 *
 * @return void
 */
function forlagid_add_audiobook_link($order ) {

    $items_rb = $order->get_items();
    foreach ( $items_rb as $item ) {
        /**
         * @var WC_Order_Item $item
         */
        if ( 'hljodbok' == $item->get_meta( 'pa_gerd' ) ) {
            echo'<h3>Streymis-hljóðbókin þín er nú aðgengileg í <a href="https://forlagid.is/hlusta">appinu</a> eða <a href="https://hlusta.forlagid.is">vafra.</a></h3> <br/>';
            echo 'Athugaðu að þú skráir þig inn þar með sömu notendaupplýsingum og á Forlagsvefnum.<br/>';
        }
    }
}
add_action( 'woocommerce_email_before_order_table', 'forlagid_add_audiobook_link', 11 );