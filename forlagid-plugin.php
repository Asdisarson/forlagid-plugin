<?php
/*
Plugin Name: Forlagid Plugin
Description: Site specific code changes for forlagid.is

*/
const DEVELOPMENT = TRUE;  // Email address to developer -> if active
const DEVELOPER = 'islandsvefir@islandsvefir.is';  // Email address to developer -> if active

add_filter('woocommerce_product_tabs', 'sb_woo_move_description_tab', 98);
function sb_woo_move_description_tab(array $tabs): array
{
    $tabs['reviews']['priority'] = 1;
    $tabs['additional_information']['priority'] = 5;
    return $tabs;
}
add_action('woocommerce_product_after_variable_attributes', 'variation_settings_fields', 10, 3);
add_action('woocommerce_save_product_variation', 'save_variation_settings_fields', 10, 2);

/**
 * Customizes Woocommerce product variation settings with additional fields.
 * 
 * This function adds custom fields to the settings of each Woocommerce product variation. The function is specifically
 * designed for adding unique identifiers related to product variations, including ISBN numbers, page number count, 
 * publication years, e-book IDs, and hidden data.
 * 
 * Five different sources of data are covered, each catering for a different field. These sources include a Text Field, 
 * Number Fields and a hidden input field.
 * 
 * All fields implement the Woocommerce wp_text_input method which is a utility function provided by Woocommerce to create
 * input fields. Each field is configured with a unique 'id', 'label', 'desc_tip' status, 'description' and a 'value' 
 * derived from post meta data. The number fields also possess 'custom_attributes' for additional html field attributes.
 * 
 * @param int $loop Index of the current instance in the loop from the calling function.
 * @param array $variation_data Array of metadata for each product variation.
 * @param object $variation A WP_Post instance representing the current product variation.
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
    woocommerce_wp_text_input(
        array(
            'id' => 'epub_uuid[' . $variation->ID . ']',
            'label' => __('ID fyrir Rafbók', 'woocommerce'),
            'desc_tip' => 'true',
            'description' => __('ID fyrir Rafbók', 'woocommerce'),
            'value' => get_post_meta($variation->ID, 'epub_uuid', true),
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
    $number_field = $_POST['epub_uuid'][$post_id];
    if (!empty($number_field)) {
        update_post_meta($post_id, 'epub_uuid', esc_attr($number_field));
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

if (function_exists('acf_add_options_page')) {
    acf_add_options_page();
}
/**
 * Returns the Icelandic currency symbol for the specified currency.
 *
 * This function checks if the passed currency is Icelandic króna ('ISK'). If yes, it sets the currency symbol to 'kr.'.
 * In case of different currency, function doesn't modify the $currency_symbol and returns its initial value.
 *
 * @param string $currency_symbol Initial value of the currency symbol.
 * @param string $currency The currency to display.
 * @return string The currency symbol for the passed currency.
 */

function icelandic_currency_symbol($currency_symbol, $currency)
{
    if ($currency == 'ISK') {
        $currency_symbol = 'kr.';
    }

    return $currency_symbol;
}
add_filter('woocommerce_currency_symbol', 'icelandic_currency_symbol', 30, 2);
remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10);
add_action('woocommerce_after_single_product_summary', 'custom_book_author', 15);
/**
 * The custom_book_author function, provided by the Forlagid Plugin, is responsible for displaying the author of a book product. 
 * This function utilizes the list_book_author() function (not documented here), which presumably retrieves and organizes 
 * the information of the author associated with a particular book and then displays it to the user. 
 * Please note that the list_book_author function needs to be defined in the global scope for custom_book_author to function properly.
 *
 * As it is attached to 'woocommerce_after_single_product_summary' action hook with a priority of 15, 
 * this function is executed after the single product summary in WooCommerce, aiding in the display of book author information.
 *
 * This function does not accept any parameters and does not return any value.
 *
 * @return void
 */

function custom_book_author(): void
{
    list_book_author();
}
/**
 * Renders HTML section and initiates JavaScript function getDatalab() from FS object.
 * 
 * The function creates an HTML div container with an id "forlagidsearch_datalab". This container acts as a hook
 * for display components generated from the JS function.
 * 
 * Once the HTML document is fully loaded, the jQuery $(document).ready() statement is used to execute a 
 * JavaScript anonymous function. Within this, the getDatalab() function from the FS object is invoked.
 * The input to getDatalab() is PHP's get_the_ID() function, used to fetch the current WordPress post ID.
 * 
 * @return void
 */

function list_book_author(): void
{
    ?>
    <div id="forlagidsearch_datalab"></div>
    <script>
        $(document).ready(() => {
            FS.getDatalab(<?php echo get_the_ID(); ?>);
        });
    </script>
    <?php
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
 * This function modifies the list of available gateways based on the customer's billing country.
 *
 * Specifically, if the customer's billing country is Iceland (expressed by the country code 'IS'), the 'bacs' gateway
 * (which stands for Bank Account Clearing System) will not be made available to the customer.
 * 
 * It first checks if the customer object exists and if the page accessing this function is not an admin page.
 * It also checks for the existence of the 'bacs' gateway in the list of available gateways.
 * If any of these conditions fail, or if the customer's billing country is Iceland, it returns the available gateways
 * without any modification.
 *
 * Otherwise, it removes the 'bacs' gateway from the list and then returns the modified list.
 *
 * @param array $available_gateways An associative array where keys are gateway ids and values are gateway objects.
 * @return array The modified array of available gateways.
 */

function payment_gateway_disable_country(array $available_gateways): array
{
    $customer = WC()->customer;

    if ($customer === null || is_admin() || !isset($available_gateways['bacs']) || $customer->get_billing_country() == 'IS') {
        return $available_gateways;
    }

    unset($available_gateways['bacs']);
    return $available_gateways;
}

add_filter('woocommerce_available_payment_gateways', 'payment_gateway_disable_country');

/**
 * Adjusts available payment gateways based on chosen shipping methods.
 *
 * This function checks if it's running in an admin context or if the WooCommerce session is non-existent. If so, it
 * returns the available payment gateways without any modifications.
 *
 * It retrieves the customer's chosen shipping methods from the WooCommerce session. If no shipping methods are chosen
 * (i.e., the retrieved value is not an array), the function again returns the available payment gateways without any
 * modifications.
 *
 * Otherwise, it checks each chosen shipping method against a list of shipping methods associated with specific payment
 * gateways to remove. If a chosen shipping method matches a method in this list, then the associated payment gateways
 * are removed from the set of available payment gateways.
 *
 * Finally, it returns the possibly modified set of available payment gateways.
 *
 * @param array $gateways The list of payment gateways passed in for possible modification.
 * @return array The possibly modified list of payment gateways.
 */

function forlagid_available_payment_gateways($gateways)
{
    if (is_admin() || is_null(WC()->session)) {
        return $gateways;
    }

    $chosen_shipping_rates = WC()->session->get('chosen_shipping_methods');
    if (!is_array($chosen_shipping_rates)) {
        return $gateways;
    }
    $shippingRatesToRemoveSpecificGateways = [
        'table_rate:6:8' => ['bacs', 'valitor', 'borgun', 'netgiro'],
        'table_rate:6:7' => ['bacs', 'valitor', 'borgun', 'netgiro'],
        'table_rate:6:9' => ['bacs', 'valitor', 'borgun', 'netgiro'],
        'table_rate:15:28' => ['bacs', 'valitor', 'borgun', 'netgiro'],
        'table_rate:15:29' => ['bacs', 'valitor', 'borgun', 'netgiro'],
        'table_rate:15:30' => ['bacs', 'valitor', 'borgun', 'netgiro'],
        'local_pickup:3' => ['cod'],
        'local_pickup:14' => ['cod'],
        'table_rate:4:3' => ['cod'],
        'table_rate:4:1' => ['cod'],
        'table_rate:4:2' => ['cod'],
        'table_rate:5:4' => ['cod'],
        'table_rate:5:5' => ['cod'],
        'table_rate:5:6' => ['cod'],
        'table_rate:13:27' => ['cod'],
        'table_rate:13:26' => ['cod'],
        'table_rate:13:25' => ['cod'],
        'table_rate:12:24' => ['cod'],
        'table_rate:12:22' => ['cod', 'bacs'],
        'table_rate:12:23' => ['cod'],
        'free_shipping:18' => ['cod'],
        'table_rate:11:16' => ['cod'],
        'table_rate:11:17' => ['cod'],
        'table_rate:11:18' => ['cod'],
        'table_rate:8:15' => ['cod'],
        'table_rate:8:14' => ['cod'],
        'table_rate:8:13' => ['cod'],

        'table_rate:7:10' => ['cod'],
        'table_rate:7:11' => ['cod'],
        'table_rate:7:12' => ['cod'],

        'table_rate:16:32' => ['cod'],
        'table_rate:16:31' => ['cod'],
        'table_rate:16:33' => ['cod'],
    ];

    foreach ($shippingRatesToRemoveSpecificGateways as $rate => $gatewaysToRemove) {
        if (in_array($rate, $chosen_shipping_rates)) {
            foreach ($gatewaysToRemove as $gatewayToRemove) {
                unset($gateways[$gatewayToRemove]);
            }
        }
    }

    return $gateways;
}

add_filter('woocommerce_available_payment_gateways', 'forlagid_available_payment_gateways', 20);

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
 * Check if the cart has any virtual products.
 *
 * @return bool True if the cart has virtual products, false otherwise.
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
 * Calculates the number of virtual products in an order and displays a message if there are any.
 *
 * @param WC_Order $order The order object.
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
 * Adds an additional text to the order confirmation email based on the shipping method.
 *
 * @param WC_Order $order The order object.
 * @param bool $sent_to_admin Whether the email is being sent to the admin.
 */
function add_email_to_order_millif(WC_Order $order, bool $sent_to_admin): void
{

    if ($sent_to_admin) {
        return;
    }

    $local_pickup = has_local_pickup_as_shipping_method($order->get_shipping_methods());
    $text = generate_shipping_text($local_pickup);

    echo $text;
}

/**
 * Check if local pickup is included in the array of shipping methods
 *
 * @param array $shipping_methods An array of shipping methods
 * @return bool Whether local pickup is included in the shipping methods
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
 * Generates the shipping text based on the shipping method.
 *
 * @param bool $is_local_pickup Set to true if local pickup is chosen, false otherwise.
 *
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
 * @param $order WC_Order
 */

function forlagid_add_ebook_link(WC_Order $order): void
{

    $items_rb = $order->get_items();
    foreach ($items_rb as $item) {
        if ('rafbok' === $item->get_meta('pa_gerd')) {
            $link_error = false;
            $epub_rafbokar = get_post_meta($item['variation_id'], 'epub_uuid', true);
            $rafbok_link = forlagid_get_ebook_link($epub_rafbokar, $order->get_id(), 'd5a88916-da1b-11e5-a852-002354962104');
            $response = json_decode(wp_remote_retrieve_body($rafbok_link));
            if (isset($response->download_link)) {
                $rafbok_link = $response->download_link;
            } else {
                $link_error = forlagid_epub_handle_error($rafbok_link, $order->get_id());
            }
            echo '<h3>Hér er rafbókin þín! Smelltu á hlekkinn „Sækja rafbók“ hér fyrir neðan til að nálgast eintakið þitt</h3> <br/>';
            echo 'Hlekkur þessi gildir í 7 daga – að þeim tíma liðnum verður hann óvirkur.
Eintak rafbókarinnar er merkt þér með stafrænni vatnsmerkingu og rekjanlegt til þín.  Dreifing skrárinnar eða efnis hennar er með öllu óheimil. <br/>';
            echo '<a href="http://www-01.forlagid.is/spurt-og-svarad-um-rafbaekur">Sjá nánar um kaup á rafbókum</a><br/><br/>';
            if ($link_error) {
                echo '<b>' . $link_error . '</b>';
            } else {
                echo '<h3><a href="' . $rafbok_link . '"><strong>Sækja rafbók: ' . $item['name'] . '</strong></a></h3><br/><br/>';
            }
        }
    }
}

add_action('woocommerce_email_after_order_table', 'forlagid_add_ebook_link', 11);
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
 * @param WC_Order $order A WooCommerce Order object which encapsulate all the order data retrieved from storage.
 * @return void
 */

function forlagid_add_audiobook_link(WC_Order $order): void
{
    $items_rb = $order->get_items();

    foreach ($items_rb as $item) {

        if ('hljodbok' === $item->get_meta('pa_gerd')) {
            $message = '<h3>Streymis-hljóðbókin þín er nú aðgengileg í <a href="https://forlagid.is/hlusta">appinu</a> eða <a href="https://hlusta.forlagid.is">vafra.</a></h3> <br/>';
            $message .= 'Athugaðu að þú skráir þig inn þar með sömu notendaupplýsingum og á Forlagsvefnum.<br/>';

            echo $message;
        }
    }
}

add_action('woocommerce_email_before_order_table', 'forlagid_add_audiobook_link', 11);

/**
 * Adds a thank you note with ebook link after successfully placing an order.
 *
 * This function gets all items from an order then process them one-by-one. The order type should be WC_Order,
 * a class provided by Woocommerce. Each item will be passed over to the 'process_item' function. The function 
 * doesn't return anything hence the return type is 'void'. 
 *
 * @param WC_Order $order The order in which all items were placed.
 *
 * @return void
 */

function forlagid_add_thankyou_ebook_link(WC_Order $order): void
{

    $items_rb = $order->get_items();
    foreach ($items_rb as $item) {
        process_item($item);
    }
}


/**
 * Processes a WooCommerce Order Item. 
 *
 * This function checks if the meta character for the order item matches 'rafbok'. If it does, two messages are printed out. 
 * These messages contain the validity of the order (which is 7 days) and the digital rights associated when the customer 
 * purchases a digital Book copy. This digital copy is now marked to the customer with a digital watermark.
 * The customer also receives an email from the company with these details.
 * 
 * @param WC_Order_Item $item The WooCommerce Order Item object to process.
 *
 * @return void
 */

function process_item(WC_Order_Item $item): void
{
    if ('rafbok' === $item->get_meta('pa_gerd')) {

        ?>
        <p>Hlekkur þessi gildir í 7 daga – að þeim tíma liðnum verður hann óvirkur. Eintak rafbókarinnar ermerkt þér með
            stafrænni vatnsmerkingu og rekjanlegt til þín. Dreifing skrárinnar eða efnis hennar er með öllu óheimil.</p>
        <p>Þú færð einnig tölvupóst frá okkur með þessum upplýsingum.</p>
        <?php
    }
}

add_action('woocommerce_thankyou_virtual', 'forlagid_add_thankyou_ebook_link', 11);

/**
 * Generates the ebook download link.
 * 
 * This function is designed to generate a secure download link for a given ebook. It takes three parameters as input:
 * an ID for the ebook file (epub_uuid), an order ID from the store (store_order_id), and a secret code from the store
 * (store_secret).
 * 
 * These parameters are URL encoded for safe transmission and combined into a string of parameters, which is then hashed
 * using HMAC with the SHA256 algorithm. The resulting hash is combined with the original parameters to form the full
 * parameter list for the API call.
 * 
 * A GET request is then made to the ebook API with the parameter string, and the output is processed to retrieve the 
 * download link.
 * 
 * If the download link is not found in the API response, an error is logged, and the function returns false. If the
 * download link is found, it is returned by the function.
 * 
 * @param string $epub_uuid  The UUID of the ebook file.
 * @param string $store_order_id  The ID of the current order.
 * @param string $store_secret  The secret associated with the store that is making the request.
 * @return string|false The ebook download link on success, false on failure.
 */

function forlagid_get_ebook_link($epub_uuid, $store_order_id, $store_secret)
{
    $store_id = urlencode("forlagid");
    $store_secret = urlencode($store_secret);
    $epub_uuid = urlencode($epub_uuid);
    $store_order_id = urlencode($store_order_id);
    $store_epoch = urlencode(time());

    $parameters = "store_id=$store_id&epub_uuid=$epub_uuid&store_order_id=$store_order_id&store_epoch=$store_epoch";
    $auth = urlencode(base64_encode(hash_hmac('sha256', $parameters, $store_secret, true)));
    $parameters = "$parameters&auth=$auth";

    $response = wp_remote_get("http://epub.is/api/get_link?$parameters");

    if (is_wp_error($response)) {
        // Log the error and return an error state
        error_log("Error in request: " . $response->get_error_message());
        return false;
    }

    $decoded_response = json_decode(wp_remote_retrieve_body($response));

    // If the download link is set, return it. Otherwise, return the whole response body
    return $decoded_response->download_link ?? wp_remote_retrieve_body($response);
}

/**
 * Handles the occurrence of an error during the ePub process for a specified order. The function retrieves and formats 
 * the error message from the given response, then sends an administrative email if an order ID is supplied.
 *
 * First, the function retrieves the body of the error response and trims any whitespace. The resulting string is then
 * passed to another function get_error_message, which retrieves the human-readable error message.
 *
 * If a message is received and the provided order ID is not 0 (indicating the absence of an order), an administrative 
 * email is sent with the details of the response, the error message, and the order ID using the function 
 * send_admin_email.
 *
 * The function returns the error message, allowing the caller to handle response and message.
 *
 * @param array $response Array containing the response from an attempt to handle ePub for an order. 
 * @param int $order_id (optional) The order ID associated with the response. Default is 0, indicating no actual order.
 *
 * @return bool|string The message indicating the error from the response. If no error is present, the function returns 
 *                     FALSE indicating that no error occurred.
 */

function forlagid_epub_handle_error(array $response, int $order_id = 0): bool|string
{
    $error = trim(wp_remote_retrieve_body($response));
    $message = get_error_message($error);

    if ($message && $order_id !== 0) {
        send_admin_email($response, $message, $order_id);
    }

    return $message;
}


/**
 * Fetches the error message for a given error.
 *
 * This function uses the error string passed to it to return the corresponding error message
 * from an associative array of error messages. If the error string is not found in 
 * the associative array, a default error message is returned.
 *
 * @param string $error The error for which an error message is needed.
 * @return string The corresponding error message or a default message in case of non-existence.
 */

function get_error_message(string $error): string
{
    $errorMessages = [
        'ERROR_NO_STORE' => 'Auðkenni verslunar rangt eða ekki til staðar',
        'ERROR_NOT_STORE' => 'Verslun/fyrirtæki er ekki verslun',
        'ERROR_NO_SECRET' => 'Verslun vantar leyndarmál',
        'ERROR_EPUB_NOT_FOR_SALE' => 'Rafbók ekki til sölu',
        'ERROR_EPUB_UUID_INVALID' => 'Auðkenni gallað',
        'ERROR_EPUB_DOES_NOT_EXISTS' => 'Auðkenni verks er ekki til',
        'ERROR_PARAM' => 'Færibreytu vantar í fyrirspurn',
        'ERROR_PARAMETERS_DO_NOT_MATCH_AUTH' => 'Villa í auth streng',
    ];

    return $errorMessages[$error] ?? 'The response from the server was empty';
}


/**
 * Sends an email to the administration/main developer in case of an error during the book's delivery process.
 *
 * This function sends an email notification to either the main developer or the admin email(as defined in WordPress options), 
 * notifying of a delivery error with specific information such as error message and the server response code. This
 * notification is only sent if a delivery error was not already sent(process validated through a post meta) for the given 
 * order. The email contents are then prepared and sent out via the wp_mail function.
 *
 * @param array $response server response which contains the status of the delivery process.
 * @param string $message error message that describes the failed delivery.
 * @param int $order_id the unique identification number of the errored order.
 * 
 * @return void
 */

function send_admin_email(array $response, string $message, int $order_id): void
{
    $notification_sent = get_post_meta($order_id, 'forlagid_error_notification_sent', true);

    if (empty($notification_sent)) {
        $to = get_option('admin_email');
        $email_text = "Hæ! Það kom upp villa við afhendingu rafbókar í pöntun númer: <b>$order_id</b>. Villan er eftirfarandi: <b>$message</b>. Server response code: <b>" . wp_remote_retrieve_response_code($response) . "</b>";

        $headers = [
            'Content-Type: text/html; charset=UTF-8',
            'Cc: ' . (defined('DEVELOPMENT') && DEVELOPMENT ? DEVELOPER : $to),
        ];

        wp_mail($to, 'Villa við afhendingu á rafbók', $email_text, $headers);
        add_post_meta($order_id, 'forlagid_error_notification_sent', 1);
    }
}


add_filter('woocommerce_package_rates', 'hide_shipping_when_free_available', 10, 2);

/**
 * Function: hide_shipping_when_free_available
 *
 * This is a custom function used for managing shipping methods in WooCommerce.
 * It checks for available shipping methods and hides the others, prioritising free shipping.
 * This function works based on a predefined set of shipping rules.
 *
 * Shipping rules are an associative array wherein each key-value pair represents a rule. 
 * The key is the free shipping method identifier, whereas its corresponding value is an array of
 * shipping method identifiers that should be removed if the free shipping method is available.
 *
 * The function iterates over the defined shipping rules. 
 * If a free shipping method is available in the rates, its associated shipping methods, 
 * defined in the rules, are removed from the rates.
 *
 * @param array $rates Array of available shipping rates where key is shipping method ID and value is WC_Shipping_Rate instance
 * @param array $package Contains an array of the package contents, its subtotal, etc
 * @return array Returns modified array of shipping rates
 */
function hide_shipping_when_free_available($rates, $package)
{
    $shippingRules = [
        'free_shipping:31' => [
            'table_rate:13:25',
            'table_rate:13:26',
            'table_rate:13:27',
            'table_rate:12:22',
            'table_rate:12:23',
            'table_rate:12:24',
        ],
        'free_shipping:32' => [
            'table_rate:4:1',
            'table_rate:4:2',
            'table_rate:4:3',
        ],
        'free_shipping:18' => [
            'table_rate:4:1',
            'local_pickup:3',
            'table_rate:6:7',
            'table_rate:4:2',
            'table_rate:4:3',
            'table_rate:6:8',
            'table_rate:6:9',
        ],
    ];

    foreach ($shippingRules as $freeShipping => $methodsToRemove) {
        if (isset($rates[$freeShipping])) {
            foreach ($methodsToRemove as $methodToRemove) {
                unset($rates[$methodToRemove]);
            }
        }
    }

    return $rates;
}

add_action('woocommerce_thankyou', 'forlagid_woocommerce_auto_complete_free_order');

/**
 * Completes free WooCommerce orders automatically.
 *
 * This function checks if the passed order ID is valid. If not, it returns immediately.
 * If the order ID is valid, it fetches the order details using the WooCommerce function wc_get_order() and then 
 * attempts to auto-complete the order using the forlagid_woocommerce_auto_complete_if_free() function.
 *
 * @param int $orderId ID of the order to be processed.
 *
 * @return void
 */

function forlagid_woocommerce_auto_complete_free_order(int $orderId): void
{
   if (!$orderId) {
        return;
    }

    $order = wc_get_order($orderId);
    forlagid_woocommerce_auto_complete_if_free($order);
}

function forlagid_woocommerce_auto_complete_if_free(WC_Order $order): void
{
    if ((int)$order->get_total() === 0) {
        $order->update_status('completed');
    }
}



/**
 * Provides tooltips for various shipping methods to end users.
 *
 * This function maps specific shipping methods to their relevant tooltips. The mapping includes:
 * - Póstsending to Standard mail
 * - Sótt í verslun to Local Pickup
 * - Póstkrafa to Cash on delivery
 * - Flýtiþjónusta to Express delivery
 *
 * It generates a tooltip text based on $method object input. If the tooltip text is identified (is not empty), the function
 * will output a span element with class name 'tooltip', enclosing the tooltip text. The text is also escaped for safety
 * to avoid cross-site scripting (XSS) vulnerabilities.
 *
 * @param object $method The object presenting shipping method.
 * @param integer $index It refers to the $index of the method in the input array.
 * @return void A tooltip text wrapped inside a span HTML element, if tooltip text exists.
 */

function forlagid_shipping_method_tooltip_text(object $method, int $index): void
{
    $methodToTooltipMap = [
        'Póstsending' => 'Standard mail', // Póstsending
        'Sótt í verslun' => 'Local Pickup', //  Sótt í verslun
        'Póstkrafa' => 'Cash on delivery',
        'Flýtiþjónusta' => 'Express delivery',
    ];

    $tooltip_text = $methodToTooltipMap[$method->label] ?? '';

    if (!empty($tooltip_text)) {
        echo '<span class="tooltip">' . esc_html($tooltip_text) . '</span>';
    }
}

add_action('woocommerce_after_shipping_rate', 'forlagid_shipping_method_tooltip_text', 10, 2);

/*
 * Displays custom information before Woocommerce terms based on chosen shipping method.
 *
 * This function checks the chosen shipping method in Woocommerce session data and if it matches 'table_rate:12:22', 
 * a specific message informing the user about the specifics of this shipping service (express service, same day delivery 
 * if ordered before 12PM on weekdays, and delivered by Post after 4PM) is displayed. In case the recipient isn't 
 * available at the time of the delivery, the package is moved to the post office.
 *
 * @return void
 */

function forlagid_before_terms_info(): void
{
    $chosen_shipping_rates = WC()->session->get('chosen_shipping_methods');
    if (in_array('table_rate:12:22', $chosen_shipping_rates)) :
        $infoBoxContent = 'Þú hefur valið flýtiþjónustu, sem felur í sér að pöntun er afhent samdægurs ef hún er gerð fyrir kl. 12 á virkum degi. Pantanir sem berast eftir þann tíma eða um helgi eru sendar út næsta virka dag. Flýtiþjónustupantanir eru keyrðar út af Póstinum eftir kl. 16. Ef viðtakandi er ekki við þegar sending berst er pakkinn fluttur á pósthús.';
        echo "<div class='infobox'><p>$infoBoxContent</p></div>";
    endif;
}

add_action('woocommerce_checkout_terms_and_conditions', 'forlagid_before_terms_info');
/**
 * Removes the BACS details on the WooCommerce thank you page.
 */
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

//Includes
include_once('inc/class-forlagid-audiobooks.php');

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
