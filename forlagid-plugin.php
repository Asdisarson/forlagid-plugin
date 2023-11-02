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

add_action('woocommerce_product_after_variable_attributes', 'variation_settings_fields', 10, 3);
add_action('woocommerce_save_product_variation', 'save_variation_settings_fields', 10, 2);

/**
 * Adds variation settings fields to the variation editor.
 *
 * This function adds text, number, and hidden fields to the variation editor. It sets the label, placeholder,
 * description, and default value for each field based on the provided variation data. The text and number fields
 * also have additional custom attributes such as step and minimum value.
 *
 * @param int $loop - The loop index of the variation.
 * @param array $variation_data - Array of data for the variation.
 * @param object $variation - The variation object being edited.
 *
 * @return void
 */

function variation_settings_fields(int $loop, array $variation_data, object $variation): void
{
    // Text Field
    woocommerce_wp_text_input(
        array(
            'id' => '_isbn_text_field[' . $variation->ID . ']',
            'label' => __('ISBNNúmer', 'woocommerce'),
            'placeholder' => 'ISBN Númer',
            'desc_tip' => 'true',
            'description' => __('ISBN númer vöru', 'woocommerce'),
            'value' => get_post_meta($variation->ID, '_isbn_text_field', true)
        )
    );
    // Number Field
    woocommerce_wp_text_input(
        array(
            'id' => '_bls_number_field[' . $variation->ID . ']',
            'label' => __('Blaðsíðutal', 'woocommerce'),
            'desc_tip' => 'true',
            'description' => __('Blaðsíðutal bókar.', 'woocommerce'),
            'value' => get_post_meta($variation->ID, '_bls_number_field', true),
            'custom_attributes' => array(
                'step' => 'any',
                'min' => '0'
            )
        )
    );
    woocommerce_wp_text_input(
        array(
            'id' => '_utg_number_field[' . $variation->ID . ']',
            'label' => __('Útgáfuár', 'woocommerce'),
            'desc_tip' => 'true',
            'description' => __('Útgáfuár bókar.', 'woocommerce'),
            'value' => get_post_meta($variation->ID, '_utg_number_field', true),
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
            'label' => __( 'ID fyrir hljóðbók', 'woocommerce' ),
            'desc_tip' => 'true',
            'description' => __( 'ID fyrir hljóðbók', 'woocommerce' ),
            'value' => get_post_meta( $variation->ID, 'epub_uuid', true ),
            'custom_attributes' => array(
                'step' => 'any',
                'min' => '0'
            )
        )
    );
    woocommerce_wp_hidden_input(
        array(
            'id' => '_hidden_field[' . $variation->ID . ']',
            'value' => 'hidden_value'
        )
    );
}
/**
 * Updates the meta fields of a WooCommerce product variation.
 *
 * This function retrieves form data submitted in the backend. This form data is associated with
 * additional product variation fields added in `variation_settings_fields()` function.
 * Currently, the form data that is retrieved includes:
 * - ISBN text field: ISBN number of the product.
 * - BLS number field: Number of pages in the book.
 * - UTG number field: Release year of the book.
 * - EPUB UUID: Identification for the electronic version of the book.
 * - Hidden field: Contains hidden data associated with the product variation.
 *
 * For each piece of data, it checks if the data is not empty, and if so, it updates the
 * corresponding meta field for the product variation. The esc_attr() function is used to
 * sanitize the text field before saving it.
 *
 * @param int $post_id The ID of the post for which the meta fields need to be updated.
 */

function save_variation_settings_fields($post_id): void
{
    $text_field = $_POST['_isbn_text_field'][$post_id];
    if (!empty($text_field)) {
        update_post_meta($post_id, '_isbn_text_field', esc_attr($text_field));
    }
    $number_field = $_POST['_bls_number_field'][$post_id];
    if (!empty($number_field)) {
        update_post_meta($post_id, '_bls_number_field', esc_attr($number_field));
    }
    $number_field = $_POST['_utg_number_field'][$post_id];
    if (!empty($number_field)) {
        update_post_meta($post_id, '_utg_number_field', esc_attr($number_field));
    }
    $number_field = $_POST['epub_uuid'][ $post_id ];
    if ( ! empty( $number_field ) ) {
        update_post_meta( $post_id, 'epub_uuid', esc_attr( $number_field ) );
    }
    $hidden = $_POST['_hidden_field'][$post_id];
    if (!empty($hidden)) {
        update_post_meta($post_id, '_hidden_field', esc_attr($hidden));
    }
}
remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10);
add_action('add_meta_boxes', 'order_metabox');
/**
 * Adds a custom meta box to a shop_order post type.
 *
 * This function utilizes add_meta_box() WordPress function to append a custom meta box
 * to a 'shop_order' post type. The box is labeled 'Order Meta' and contains custom display
 * method 'display_custom_metabox' that will render HTML contents inside the meta box.
 *
 * @return void
 */

function order_metabox(): void
{
    add_meta_box(
        'order_custom_meta',
        'Order Meta',
        'display_custom_metabox',
        'shop_order'
    );
}
/**
 * This function extracts and displays metadata associated with a provided post object.
 *
 * The function gets the post metadata using the WordPress function get_post_meta(), using the ID property of the provided
 * post object as the argument. If the metadata is not empty, then the function iterates over every metadata key-value
 * pair and outputs them in 'key: value' wrapped in a paragraph HTML element.
 *
 * Specifically, for each key-value pair in the metadata array, the key is displayed in bold (enclosed in '<strong>' tags)
 * followed by a colon, and then the value is displayed. The key-value pair is enclosed within a paragraph tag '<p>' to
 * preserve the formatting.
 *
 * This function is typically used in the context of 'shop_order' parent posts in WooCommerce to display order metadata in
 * a readable manner within an added metabox.
 *
 * @param object $post Post object that will have its metadata retrieved and displayed.
 *
 * @return void
 */

function display_custom_metabox($post): void
{
    $meta_data = get_post_meta($post->ID);
    if (!empty($meta_data)) {
        foreach ($meta_data as $key => $value) {
            echo '<p><strong>' . $key . ':</strong> ' . $value[0] . '</p>';
        }
    }
}

add_filter('woocommerce_default_address_fields', 'forlagid_woocommerce_reorder_checkout_fields');
/**
 * Reorders Woocommerce's default checkout fields for the Forlagid online store.
 *
 * This function modifies the priority of Woocommerce's checkout fields to customize their order. Checkout field with
 * lower priority will be displayed before fields with higher priority.
 *
 * Specifically, the function changes the priorities of four checkout fields, namely 'address_1', 'address_2',
 * 'postcode' and 'country'.
 *
 * The function then returns the altered fields array.
 *
 * @param array $fields Default checkout fields provided by WooCommerce.
 *
 * @return array Modified checkout fields with updated priority.
 */

function forlagid_woocommerce_reorder_checkout_fields($fields): array
{
    $fields['address_1']['priority'] = 31;
    $fields['address_2']['priority'] = 32;
    $fields['postcode']['priority'] = 37;
    $fields['country']['priority'] = 71;
    return $fields;
}


add_filter('woocommerce_checkout_fields', 'kg_add_email_verification_field_checkout');
/**
 * Adds an email verification field to the checkout fields.
 *
 * This function expands the provided fields with an additional email verification field, along with altering the class
 * of the existing "billing_email" field. "billing_em_ver" is added with several properties including the label name,
 * requirement status, associated class, form clearing preference, and the priority level in display.
 *
 * @param array $fields Initial array of fields to expand with an extra field.
 * @return array $fields Array after inserting the email verification field.
 */

function kg_add_email_verification_field_checkout(array $fields): array
{
    $fields['billing']['billing_email']['class'] = array('form-row-email');
    $fields['billing']['billing_em_ver'] = array(
        'label' => __('Staðfesta netfang <span class="english-checkout"> - Verify e-mail</span>', 'kg'),
        'required' => true,
        'class' => array('form-row-emailver'),
        'clear' => true,
        'priority' => 120,
    );
    return $fields;
}
add_action('woocommerce_checkout_process', 'kg_matching_email_addresses');
/**
 * Checks whether the provided 'billing_email' and 'billing_em_ver' are matching.
 *
 * The function 'kg_matching_email_addresses' retrieves the values of 'billing_email' and 'billing_em_ver' from
 * the '$_POST' global array. Then it compares the two email fields for equality. If the emails are not equal,
 * it uses the 'wc_add_notice' function to add a WooCommerce notice with a message of 'Innslegin netföng passa ekki'
 * which means "Entered emails do not match" in English and assigns it with an 'error' notice type.
 *
 * This function is typically associated with WooCommerce checkout, where it validates user provided billing email
 * addresses for a purchase. The function does not return anything as the notice is passed directly to WooCommerce
 * to handle the display.
 *
 * @return void
 */

function kg_matching_email_addresses(): void
{
    $email1 = $_POST['billing_email'];
    $email2 = $_POST['billing_em_ver'];
    if ($email2 !== $email1) {
        wc_add_notice(__('Innslegin netföng passa ekki', 'kg'), 'error');
    }
}
/**
 * Disables the BACS and COD gateways for virtual products.
 *
 * This function checks if the user is in the admin area or if the cart is null. If either condition is true,
 * the function returns the original array of available gateways. If the cart contains virtual products,
 * the BACS and COD gateways are unset from the available_gateways array.
 *
 * @param array $available_gateways Original array of available gateways.
 * @return array Modified array of available gateways after disabling BACS and COD for virtual products.
 */
function forlagid_disable_bacs_cod_for_virtual($available_gateways)
{
    if (is_admin() || is_null(WC()->cart)) {
        return $available_gateways;
    }
    if (woo_cart_has_virtual_product()) {
        unset($available_gateways['bacs'], $available_gateways['cod']);
    }
    return $available_gateways;
}

add_filter('woocommerce_available_payment_gateways', 'forlagid_disable_bacs_cod_for_virtual', 25);

/**
 * Checks if the WooCommerce cart contains any virtual products.
 *
 * This function retrieves all products in the cart using the WC() global instance. It then loops through each product
 * and checks if it is virtual by examining the '_virtual' post meta. If a virtual product is found, the function
 * immediately returns true. If no virtual products are found after looping through all cart products, the function
 * returns false.
 *
 * @return bool True if cart contains virtual product, false otherwise.
 */
function woo_cart_has_virtual_product(): bool
{
    // Get all products in cart
    $products = WC()->cart->get_cart();

    // Loop through cart products
    foreach ($products as $product) {
        // Get product ID and '_virtual' post meta
        $product = isset($product['variation_id'])
            ? new WC_Product_Variation($product['variation_id'])
            : wc_get_product($product['product_id']);

        // If the product is virtual, return true immediately
        if ($product && $product->is_virtual()) {
            return true;
        }
    }

    // If we got here, then no virtual products were found
    return false;
}


add_filter('woocommerce_thankyou_virtual', 'wdm_checkout_thankyou_virtual', 15);

/**
 * Checks if a thank you message should be displayed for virtual products in the checkout.
 *
 * This function iterates through each item in the given order and checks if any of the products are virtual.
 * If at least one virtual product is found and the order status is not "failed", a thank you message is echoed.
 *
 * @param WC_Order $order The order for which the thank you message should be checked.
 * @return void
 */
function wdm_checkout_thankyou_virtual(WC_Order $order): void
{

    $order_items = $order->get_items();

    $has_virtual_products = false;

    foreach ($order_items as $item) {
        $product = isset($item['variation_id'])
            ? new WC_Product_Variation($item['variation_id'])
            : wc_get_product($item['product_id']);

        if ($product->is_virtual()) {
            $has_virtual_products = true;
            break;
        }
    }

    if ($has_virtual_products && !$order->has_status('failed')) {
        echo "<p>Þú færð brátt tölvupóst frá okkur með hlekk sem vísar þér á vöruna þína.</p>";
    }
}


add_action('woocommerce_email_before_order_table', 'add_email_to_order_millif', 10, 2);
/**
 * Adds email to the order and displays the shipping text.
 *
 * This function checks if the order has been sent to the admin. If not, it determines if the local pickup is selected as
 * the shipping method. Then, it generates the shipping text using the $local_pickup parameter. Finally, the shipping
 * text is echoed.
 *
 * @param WC_Order $order The order object.
 * @param bool $sent_to_admin A flag indicating if the order has been sent to the admin.
 *
 * @return void
 */
function add_email_to_order_millif(WC_Order $order, bool $sent_to_admin): void
{
    $order = wc_get_order($order);
    if ($sent_to_admin) {
        return;
    }

    $local_pickup = has_local_pickup_as_shipping_method($order->get_shipping_methods());
    $text = generate_shipping_text($local_pickup);

    echo $text;
}

/**
 * Checks if the given array of shipping methods contains the local pickup method.
 *
 * This function iterates through each shipping method in the provided array and checks if the method ID is
 * "local_pickup". If a match is found, the function immediately returns true. If no match is found after
 * iterating through all the shipping methods, the function returns false.
 *
 * @param array $shipping_methods Array of shipping methods to check.
 * @return bool Returns true if the local pickup method is found, false otherwise.
 */
function has_local_pickup_as_shipping_method(array $shipping_methods): bool
{

    foreach ($shipping_methods as $shipping_method) {
        $shipping_method_id = current(explode(':', $shipping_method['method_id']));

        if ('local_pickup' === $shipping_method_id) {
            return true;
        }
    }

    return false;

}

/**
 * Generate the shipping text based on the provided boolean flag.
 *
 * This function generates the shipping text based on whether the given boolean flag is true or false. If the flag is true
 * (indicating local pickup), the text will contain information about the pickup location, opening hours, and the timeline
 * for picking up the order. Otherwise, if the flag is false (indicating other shipping methods), an empty paragraph is
 * returned.
 *
 * @param bool $is_local_pickup Flag indicating whether local pickup is chosen.
 * @return string The generated shipping text.
 */
function generate_shipping_text(bool $is_local_pickup): string
{

    if ($is_local_pickup) {
        // shipping method
        $text = '<p>Þú valdir að sækja í verslun. Bókabúð okkar á <a href="https://www.forlagid.is/bokabudin-a-fiskislod/">Fiskiskóð 39</a> er opin mán-fös. frá kl. 10-18 og lau. frá kl. 11-16. Framvísa þarf pöntunarnúmeri þegar pöntun er sótt.</p>';
        $text .= '<p>Sendingarinnar verður að vitja í verslun okkar innan fjögurra vikna frá pöntun. Að þeim tíma liðnum eru ósóttar pantanir bakfærðar á kennitölu viðskiptavinar sem inneign.</p>';
    } else {
        // If other methods are used, we return an empty paragraph for now.
        $text = '<p></p>';
    }

    return $text;

}

/**
 * Adds an ebook download link for a specific item in an order.
 *
 * This function retrieves the order items and checks if there is an item with the product type "rafbok".
 * If such an item is found, it retrieves the ebook's unique identifier and uses it to obtain the ebook download link.
 * The function then checks the response from the download link request and handles any errors that occur.
 * Finally, it echoes the ebook download link along with additional information about the ebook.
 *
 * @param WC_Order $order The order object.
 * @return void
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
/**
 * Displays a custom message with links to the app and browser for audio books in a customer's order, if any exist.
 *
 * forlagid_add_audiobook_link() is a function that iterates through all items in a customer's order.
 * If an item is an audiobook (detected by the 'pa_gerd' meta data having a value of 'hljodbok'),
 * a predefined HTML message is echoed to the user. This message includes links to an app and a web application
 * where the audiobook can be streamed.
 *
 * The inclusion of the ':void' at the end of the function declaration indicates that this function does not
 * return any value.
 *
 */
function forlagid_add_thankyou_ebook_link($order)
{        $order = wc_get_order($order);
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
 * Function to remove Bank Account Clearing System (BACS) details from the WooCommerce thank you page.
 *
 * Initially, this function checks if the necessary WooCommerce function 'WC()' exists. If it doesn't, the function
 * immediately returns to the calling code without executing the rest of its logic. This step ensures that further
 * execution will only occur in the context of a WooCommerce-enabled website.
 *
 * It then attempts to get the BACS payment gateway from WooCommerce's registered payment gateways. If it cannot
 * find the gateway (possibly due to it being unregistered or disabled), it again exits immediately.
 *
 * Finally, assuming all previous checks have passed, it removes the action bound to the 'woocommerce_thankyou_bacs'
 * hook. This action is responsible for showing the BACS details on the WooCommerce thank you page. As a result,
 * executing this function will cause those details to no longer be displayed.
 *
 * It should be noted that the effect of this function is temporary and lasts only for the duration of the current
 * request. To permanently hide the details, this function should be called in response to a relevant event or
 * within the context of a plugin.
 *
 * @return void
 */

function remove_bacs_details_on_thankyou_page(): void
{
    // Check if WC function exists.
    if (!function_exists('WC')) {
        return;
    }

    // Get the BACS payment gateway.
    $gateway = WC()->payment_gateways()->payment_gateways()['bacs'] ?? null;

    // Exit, if the gateway is not available.
    if (null === $gateway) {
        return;
    }

    // Remove the action which shows the BACS details on the thank you page.
    remove_action('woocommerce_thankyou_bacs', [$gateway, 'thankyou_page']);
}

add_action('woocommerce_loaded', 'remove_bacs_details_on_thankyou_page');

/**
 * `forlagid_bacs_just_bank_info` alters the instructions of the 'bacs' payment gateway for a specific order.
 *
 * This function retrieves available payment gateways in WooCommerce and checks whether the 'bacs' gateway is available.
 * If it is, the function disables the instructions of the 'bacs' gateway and triggers the 'thank you' page using the
 * order ID provided in the parameter.
 *
 * Note: The 'bacs' payment gateway is also known as Bank Account Clearing System, and it is a popular payment gateway
 * in WooCommerce which allows payments directly via bank account.
 *
 * @param int $order_id ID of the WooCommerce order for which the payment gateway instructions will be modified.
 * @return void
 */
function forlagid_bacs_just_bank_info($order_id): void
{
    $available_gateways = WC()->payment_gateways()->get_available_payment_gateways();
    $gateway = $available_gateways['bacs'] ?? false;

    if ($gateway) {
        $gateway->instructions = false;
        $gateway->thankyou_page($order_id);
    }
}

add_action('woocommerce_thankyou_bacs', 'forlagid_bacs_just_bank_info');


/**
 * Forces the woocommerce shop page to redirect to the shop page after the user adds a product to the cart.
 *
 * This function ensures that the user is redirected to the shop page every time they add a product
 * to the cart instead of the default woocommerce behavior that directs them to the cart page. It utilizes
 * the wc_get_page_permalink function from Woocomerce that retrieves a permalink for a Woocommerce page,
 * in this case 'shop'. It currently doesn't utilise the $link argument.
 *
 * @param string $link The original link that you are replacing. Not used in current implementation.
 * @return string The url to the shop page.
 */

function forlagid_force_continue_shopping_shop_page($link): string
{
    return wc_get_page_permalink('shop');
}

add_filter('woocommerce_continue_shopping_redirect', 'forlagid_force_continue_shopping_shop_page');


add_filter('validate_username', 'custom_validate_username', 10, 2);
/**
 * Validates a username to ensure it contains no spaces.
 *
 * This function checks if a given username contains any spaces using a regular expression. If the username string
 * contains a space, the function will immediately return false, invalidating the username. If no spaces are found,
 * the function returns the original validation state.
 *
 * @param bool $valid The original validation state.
 * @param string $username The username to validate.
 *
 * @return bool Returns false if the username contains spaces, otherwise returns the original validation state.
 */

function custom_validate_username($valid, $username)
{
    if (preg_match("/\\s/", $username)) {
        // there are spaces
        return false;
    }
    return $valid;
}
/* Stop Adding Functions Below this Line */
