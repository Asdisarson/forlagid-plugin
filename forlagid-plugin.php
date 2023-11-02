<?php
/**
 * Plugin Name: Forlagid Plugin [new]
 * Plugin URI: https://islandsvefir.is/
 * Description: Site Specific plugin
 * Version: 1.0.0
 * Author: Íslandsvefir
 * Author URI: https://islandsvefir.is/
 * Requires at least: 5.0
 * Requires PHP: 7.4
 *
 * WC requires at least: 5.7
 * WC tested up to: 6.3.1
 *
 * Text Domain: forlagid-plugin-new
 * Domain Path: /languages/
 *
 * @package Forlagid_Audiobooks
 */

// Add Variation Settings
add_action( 'woocommerce_product_after_variable_attributes', 'variation_settings_fields', 10, 3 );
// Save Variation Settings
add_action( 'woocommerce_save_product_variation', 'save_variation_settings_fields', 10, 2 );

////Create new fields for variations
function variation_settings_fields( $loop, $variation_data, $variation ) {
	// Text Field
	woocommerce_wp_text_input(
		array(
			'id' => '_isbn_text_field[' . $variation->ID . ']',
			'label' => __( 'ISBNNúmer', 'woocommerce' ),
			'placeholder' => 'ISBN Númer',
			'desc_tip' => 'true',
			'description' => __( 'ISBN númer vöru', 'woocommerce' ),
			'value' => get_post_meta( $variation->ID, '_isbn_text_field', true )
		)
	);
	// Number Field
	woocommerce_wp_text_input(
		array(
			'id' => '_bls_number_field[' . $variation->ID . ']',
			'label' => __( 'Blaðsíðutal', 'woocommerce' ),
			'desc_tip' => 'true',
			'description' => __( 'Blaðsíðutal bókar.', 'woocommerce' ),
			'value' => get_post_meta( $variation->ID, '_bls_number_field', true ),
			'custom_attributes' => array(
				'step' => 'any',
				'min' => '0'
			)
		)
	);
	// Number Field
	woocommerce_wp_text_input(
		array(
			'id' => '_utg_number_field[' . $variation->ID . ']',
			'label' => __( 'Útgáfuár', 'woocommerce' ),
			'desc_tip' => 'true',
			'description' => __( 'Útgáfuár bókar.', 'woocommerce' ),
			'value' => get_post_meta( $variation->ID, '_utg_number_field', true ),
			'custom_attributes' => array(
				'step' => 'any',
				'min' => '0'
			)
		)
	);
	// Number Field
	woocommerce_wp_text_input(
		array(
			'id' => 'epub_uuid[' . $variation->ID . ']',
			'label' => __( 'ID fyrir Hljóðbók', 'woocommerce' ),
			'desc_tip' => 'true',
			'description' => __( 'ID fyrir Hljóðbók', 'woocommerce' ),
			'value' => get_post_meta( $variation->ID, 'epub_uuid', true ),
			'custom_attributes' => array(
				'step' => 'any',
				'min' => '0'
			)
		)
	);

	// Hidden field
	woocommerce_wp_hidden_input(
		array(
			'id' => '_hidden_field[' . $variation->ID . ']',
			'value' => 'hidden_value'
		)
	);
}

//Save new fields for variations

function save_variation_settings_fields( $post_id ) {
	// Text Field
	$text_field = $_POST['_isbn_text_field'][ $post_id ];
	if ( ! empty( $text_field ) ) {
		update_post_meta( $post_id, '_isbn_text_field', esc_attr( $text_field ) );
	}
	// Number Field
	$number_field = $_POST['_bls_number_field'][ $post_id ];
	if ( ! empty( $number_field ) ) {
		update_post_meta( $post_id, '_bls_number_field', esc_attr( $number_field ) );
	}
	$number_field = $_POST['_utg_number_field'][ $post_id ];
	if ( ! empty( $number_field ) ) {
		update_post_meta( $post_id, '_utg_number_field', esc_attr( $number_field ) );
	}
	$number_field = $_POST['epub_uuid'][ $post_id ];
	if ( ! empty( $number_field ) ) {
		update_post_meta( $post_id, 'epub_uuid', esc_attr( $number_field ) );
	}
	
	// Hidden field
	$hidden = $_POST['_hidden_field'][ $post_id ];
	if ( ! empty( $hidden ) ) {
		update_post_meta( $post_id, '_hidden_field', esc_attr( $hidden ) );
	}
}

//hide Sale badge on products
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );

// Add Meta information box to Order page
add_action( 'add_meta_boxes', 'order_metabox');

function order_metabox() {
	add_meta_box(
		'order_custom_meta', 
		'Order Meta',
		'display_custom_metabox',
		'shop_order', 
		'advanced', 
		'default'
	);
}
	
function display_custom_metabox($post){
	$meta_data = get_post_meta($post->ID);
	if(!empty($meta_data)){
		foreach($meta_data as $key => $value){
			echo '<p><strong>'.$key.':</strong> '.$value[0].'</p>';
		}
	}
}

// Rearrange fields in checkout
add_filter( 'woocommerce_default_address_fields', 'bbloomer_reorder_checkout_fields' );
function bbloomer_reorder_checkout_fields( $fields ) {
    // default priorities:
    // 'first_name' - 10
    // 'last_name' - 20
    // 'company' - 30
    // 'country' - 40
    // 'address_1' - 50
    // 'address_2' - 60
    // 'city' - 70
    // 'state' - 80
    // 'postcode' - 90
  $fields['address_1']['priority'] = 31;
  $fields['address_2']['priority'] = 32;
  $fields['postcode']['priority'] = 37;
  $fields['country']['priority'] = 71;
	return $fields;
}

// Adding an ACF Options page
if ( function_exists( 'acf_add_options_page' ) ) {
	acf_add_options_page();
}
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 ); //TODO




//add email verification
add_filter( 'woocommerce_checkout_fields', 'kg_add_email_verification_field_checkout' );
function kg_add_email_verification_field_checkout( $fields ) {
	$fields['billing']['billing_email']['class'] = array( 'form-row-email' );
	$fields['billing']['billing_em_ver'] = array(
		'label'    => __( 'Staðfesta netfang <span class="english-checkout"> - Verify e-mail</span>', 'kg' ),
		'required' => true,
		'class'    => array( 'form-row-emailver' ),
		'clear'    => true,
		'priority' => 120,
	);
	return $fields;
}

// ---------------------------------
// 3) Generate error message if field values are different

add_action( 'woocommerce_checkout_process', 'kg_matching_email_addresses' );
function kg_matching_email_addresses() {
	$email1 = $_POST['billing_email'];
	$email2 = $_POST['billing_em_ver'];
	if ( $email2 !== $email1 ) {
		wc_add_notice( __( 'Innslegin netföng passa ekki', 'kg' ), 'error' );
	}
}

function utgafa_disable_bacs_virtual( $available_gateways ) {
	global $woocommerce;
	if ( is_admin() || is_null( WC()->cart ) ) {
		return $available_gateways;
	}
	if ( woo_cart_has_virtual_product() && isset( $available_gateways['bacs'] ) ) {
		unset( $available_gateways['bacs'] );
	}

	return $available_gateways;
}


add_filter( 'woocommerce_available_payment_gateways', 'utgafa_disable_bacs_virtual', 25 );

/*Larus 13.feb 2023*/
function utgafa_disable_cod_virtual( $available_gateways ) {
	global $woocommerce;
	if ( is_admin() || is_null( WC()->cart ) ) {
		return $available_gateways;
	}
	if ( woo_cart_has_virtual_product() && isset( $available_gateways['cod'] ) ) {
		unset( $available_gateways['cod'] );
	}

	return $available_gateways;
}

add_filter( 'woocommerce_available_payment_gateways', 'utgafa_disable_cod_virtual', 25 );


/**
 * ath ef það er rafbók í körfunni = fela sendingarmölla
 *
 * @return bool
 */
function woo_cart_has_virtual_product() {


	// By default, no virtual product
	$has_virtual_products = false;

	// Default virtual products number
	$virtual_products = 0;

	// Get all products in cart
	$products = WC()->cart->get_cart();

	// Loop through cart products
	foreach ( $products as $product ) {

		// Get product ID and '_virtual' post meta
		if ( isset( $product['variation_id'] ) ) {
			$product = new WC_Product_Variation( $product['variation_id'] );
		} else {
			$product = wc_get_product( $product['product_id'] );
		}

		if ( $product->is_virtual() ) {
			$virtual_products += 1;
		}

	}

	if ( $virtual_products > 0 ) {

		$has_virtual_products = true;

	}

	return $has_virtual_products;
}


add_action( 'woocommerce_email_before_order_table', 'add_order_email_millif', 10, 2 );
function add_order_email_millif( $order, $sent_to_admin ) {

	if ( $sent_to_admin ) {
		return;
	}

	$local_pickup = false;
	foreach ( $order->get_shipping_methods() as $shipping_method ) {
		$shipping_method_id = current( explode( ':', $shipping_method['method_id'] ) );

		if ( 'local_pickup' == $shipping_method_id ) {
			$local_pickup = true;
			break;
		}
	}

	if ( $local_pickup ) {
		// shipping method
		$text = '<p>Þú valdir að sækja í verslun.
Bókabúð okkar á <a href="https://www.forlagid.is/bokabudin-a-fiskislod/">Fiskiskóð 39</a> er opin mán-fös. frá kl. 10-18 og lau. frá kl. 11-16. Framvísa þarf pöntunarnúmeri þegar pöntun er sótt.</p> <p>Sendingarinnar verður að vitja í verslun okkar innan fjögurra vikna frá pöntun. Að þeim tíma liðnum eru ósóttar pantanir bakfærðar á kennitölu viðskiptavinar sem inneign.</p>';
	} else {
		// other methods
		$text = '<p></p>';
	}

	echo $text;
}

/**
 * @param $order WC_Order
 */

function forlagid_add_order_email_epub_description(WC_Order $order): void
{
	$teljari = 0;
    $items_rb = $order->get_items();
    foreach ($items_rb as $item) {
        if ('rafbok' === $item->get_meta('pa_gerd')) {
           $teljari++;
        }
		
    }
	if($teljari>0){
			 echo '<h3>Hér er rafbókin þín! Smelltu á hlekkinn „EPUB“ hér fyrir ofan til að nálgast eintakið þitt</h3> <br/>';
            echo 'Hlekkur þessi gildir í 1 ár – að þeim tíma liðnum verður hann óvirkur.
Eintak rafbókarinnar er merkt þér með stafrænni vatnsmerkingu og rekjanlegt til þín.  Dreifing skrárinnar eða efnis hennar er með öllu óheimil. <br/>';
            echo '<a href="http://www-01.forlagid.is/spurt-og-svarad-um-rafbaekur">Sjá nánar um kaup á rafbókum</a><br/><br/>';
			return;
		}
}

add_action('woocommerce_email_before_order_table', 'forlagid_add_order_email_epub_description', 11);

//Grafík Panda - Email um Hljóðbók

function forlagid_add_audiobook_link( $order ) {

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

function forlagid_add_thankyou_ebook_link($order)
{		$order = wc_get_order($order);
		$teljari = 0;
        $items_rb = $order->get_items();
    foreach ($items_rb as $item) {
        if ('rafbok' === $item->get_meta('pa_gerd')) {
		$teljari++;
        }
		
    }
	if($teljari>0){
		
            echo '<h3>Hér er rafbókin þín! Smelltu á hlekkinn „EPUB“ hér fyrir neðan til að nálgast eintakið þitt</h3> <br/>';
            echo 'Hlekkur þessi gildir í 1 ár – að þeim tíma liðnum verður hann óvirkur.
Eintak rafbókarinnar er merkt þér með stafrænni vatnsmerkingu og rekjanlegt til þín.  Dreifing skrárinnar eða efnis hennar er með öllu óheimil. <br/>';
            echo '<a href="http://www-01.forlagid.is/spurt-og-svarad-um-rafbaekur">Sjá nánar um kaup á rafbókum</a><br/><br/>';
	}
}

add_action( 'woocommerce_before_thankyou', 'forlagid_add_thankyou_ebook_link');


add_action( 'woocommerce_thankyou', 'forlagid_woocommerce_auto_complete_order' );
function forlagid_woocommerce_auto_complete_order( $order_id ) {
	if ( ! $order_id ) {
		return;
	}

	$order = wc_get_order( $order_id );
	if ( (int) $order->order_total === 0 ) {
		$order->update_status( 'completed' );
	}
}

function forlagid_change_image_size() {
	remove_image_size( 'portfolio-five' );
	add_image_size( 'portfolio-five', 300, 429, true );
}

add_action( 'init', 'forlagid_change_image_size', 150 );


/**
 * @param WC_Shipping_Method $method
 * @param int $index
 */


function forlagid_switch_thankyou_bacs() {

	// Bail, if we don't have WC function
	if ( ! function_exists( 'WC' ) ) {
		return;
	}

	// Get all available gateways
	$available_gateways = WC()->payment_gateways()->get_available_payment_gateways();

	// Get the Bacs gateway class
	$gateway = isset( $available_gateways['bacs'] ) ? $available_gateways['bacs'] : false;

	// We won't do anything if the gateway is not available
	if ( false == $gateway ) {
		return;
	}

	// Remove the action, which places the BACS details on the thank you page
	remove_action( 'woocommerce_thankyou_bacs', array( $gateway, 'thankyou_page' ) );
}

add_action( 'wp_loaded', 'forlagid_switch_thankyou_bacs', 100 );

function forlagid_bacs_just_bank_info( $order_id ) {
	$available_gateways = WC()->payment_gateways()->get_available_payment_gateways();
	$gateway = isset( $available_gateways['bacs'] ) ? $available_gateways['bacs'] : false;

	if ( $gateway ) {
		$gateway->instructions = false;
		$gateway->thankyou_page( $order_id );
	}
}

add_action( 'woocommerce_thankyou_bacs', 'forlagid_bacs_just_bank_info' );

/**
 * Force the url for the "continue shopping" link in the cart notification to the shop page.
 *
 * @param $link
 *
 * @return string
 */
function forlagid_force_continue_shopping_shop_page( $link ) {
	return wc_get_page_permalink( 'shop' );
}

add_filter( 'woocommerce_continue_shopping_redirect', 'forlagid_force_continue_shopping_shop_page' );


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

// Attempt to improve curl connections.
//add_filter( 'https_local_ssl_verify', '__return_true' );
//Includes
include_once( 'inc/author-taxonomy.php' );
include_once( 'inc/hofundar-post-type.php' );
include_once( 'inc/class-forlagid-audiobooks.php' );

add_filter('validate_username' , 'custom_validate_username', 10, 2);
function custom_validate_username($valid, $username ) {
		if (preg_match("/\\s/", $username)) {
   			// there are spaces
			return $valid=false;
		}
	return $valid;
}
/* Stop Adding Functions Below this Line */
