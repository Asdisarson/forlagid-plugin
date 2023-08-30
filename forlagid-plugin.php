<?php
/*
Plugin Name: Forlagid Plugin
Description: Site specific code changes for forlagid.is

*/
const DEVELOPMENT = TRUE;  // Email address to developer -> if active

const DEVELOPER = 'islandsvefir@islandsvefir.is';  // Email address to developer -> if active
//rearrange tabs on single product

add_filter('woocommerce_product_tabs', 'sb_woo_move_description_tab', 98);
/**
 * Move the description tab in the WooCommerce product page.
 *
 * @param array $tabs Array of tabs in the product page.
 * @return array Modified array of tabs with the description tab moved to a new position.
 */
function sb_woo_move_description_tab(array $tabs): array
{
    $tabs['reviews']['priority'] = 1;
    $tabs['additional_information']['priority'] = 5;
    return $tabs;
}


// Add Variation Settings
add_action('woocommerce_product_after_variable_attributes', 'variation_settings_fields', 10, 3);
// Save Variation Settings
add_action('woocommerce_save_product_variation', 'save_variation_settings_fields', 10, 2);

/**
 * Renders the HTML for variation settings fields.
 *
 * @param int $loop The loop counter for the variation.
 * @param array $variation_data The variation data.
 * @param object $variation The variation object.
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
    // Number Field
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

    // Hidden field
    woocommerce_wp_hidden_input(
        array(
            'id' => '_hidden_field[' . $variation->ID . ']',
            'value' => 'hidden_value'
        )
    );
}

/**
 * Save new fields for variations
 *

 */

function save_variation_settings_fields($post_id): void
{
    // Text Field
    $text_field = $_POST['_isbn_text_field'][$post_id];
    if (!empty($text_field)) {
        update_post_meta($post_id, '_isbn_text_field', esc_attr($text_field));
    }
    // Number Field
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


    // Hidden field
    $hidden = $_POST['_hidden_field'][$post_id];
    if (!empty($hidden)) {
        update_post_meta($post_id, '_hidden_field', esc_attr($hidden));
    }
}

//hide Sale badge on products
remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10);

//TODO: Spurning hvort við  notum þetta. Fer eftir

add_action('add_meta_boxes', 'order_metabox');

function order_metabox(): void
{
    add_meta_box(
        'order_custom_meta',
        'Order Meta',
        'display_custom_metabox',
        'shop_order'
    );
}

function display_custom_metabox($post): void
{
    $meta_data = get_post_meta($post->ID);
    if (!empty($meta_data)) {
        foreach ($meta_data as $key => $value) {
            echo '<p><strong>' . $key . ':</strong> ' . $value[0] . '</p>';
        }
    }
}

//TODO: Rearrange with plugin... Sjá uppröðun ---> setja land aftast í uppröðunina - mögulega taka út state
// Setja city næst aftast
//
// Rearrange fields in checkout - as of 17.12.18
add_filter('woocommerce_default_address_fields', 'bbloomer_reorder_checkout_fields');
function bbloomer_reorder_checkout_fields($fields): array
{
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

// Adding an ACF Options page -  //TODO:spurning hvort þetta þurfi
if (function_exists('acf_add_options_page')) {
    acf_add_options_page();
}

//TODO: Þetta er eitthvað LEGACY, er ekki viss um að þetta sé notað á síðunni

//Breyta úr Kr í kr
//TODO: Ég veit ekki afhverju?
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
//TODO: Er þetta notað núna ?  Kannnski að hafa skýrara nafn
function custom_book_author(): void
{
    list_book_author();
}

// MN: 2020.10.12,  hætti að nota wordpress+sql, not forlagidsearch í staðinn.
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

//TODO: Ef það er verið að nota owl á síðunni þá láta þetta vera annnars má þetta fara
add_action('wp_enqueue_scripts', 'my_styles_method');
function my_styles_method(): void
{
    wp_enqueue_style(
        'custom-style',
        get_bloginfo('url') . '/wp-content/plugins/forlagid-plugin/owl-carousel/owl.carousel.css'
    );
    wp_enqueue_style(
        'custom-owl-style',
        get_bloginfo('url') . '/wp-content/plugins/forlagid-plugin/owl-carousel/owl.theme.css'
    );

    wp_enqueue_style(
        'custom-grid-style',
        get_bloginfo('url') . '/wp-content/plugins/forlagid-plugin/gridism.css'
    );
    wp_register_script('owl-js', get_bloginfo('url') . '/wp-content/plugins/forlagid-plugin/owl-carousel/jquery-1.9.1.min.js', array('jquery'), '1', false);
    wp_register_script('owl-carousel-js', get_bloginfo('url') . '/wp-content/plugins/forlagid-plugin/owl-carousel/owl.carousel.js', array('jquery'), '1', false);
    wp_register_script('slick-js', '//code.jquery.com/jquery-2.2.0.min.js', array('jquery'), '1', false);
    wp_register_script('slick-carousel-js', '//cdn.jsdelivr.net/jquery.slick/1.6.0/slick.min.js', array('jquery'), '1', false);
    wp_enqueue_script('owl-js');
    wp_enqueue_script('owl-carousel-js');
    //wp_enqueue_script('slick-carousel-js');
}

// ---------------------------------
// ---------------------------------
// ---------------------------------

//TODO: Email secondary field verification - hægt er að gera þetta með plugini
// jafnvel endurskrifa og gera ajax eða bara jquery
//adds a secondary email field to verify that email address
//add email verification
add_filter('woocommerce_checkout_fields', 'kg_add_email_verification_field_checkout');
/**
 * Adds email verification field to the checkout form.
 *
 * @param array $fields An array of checkout fields.
 * @return array An updated array of checkout fields.
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

//TODO: Framhald af sama kóða
//hugsanlega betra að gera þetta í jquery
// 3) Generate error message if field values are different

add_action('woocommerce_checkout_process', 'kg_matching_email_addresses');
function kg_matching_email_addresses(): void
{
    $email1 = $_POST['billing_email'];
    $email2 = $_POST['billing_em_ver'];
    if ($email2 !== $email1) {
        wc_add_notice(__('Innslegin netföng passa ekki', 'kg'), 'error');
    }
}

// ---------------------------------
// ---------------------------------
// ---------------------------------

//TODO: Fela ákveðna greiðslumáta eftir skilyrðum, gera með pluginni
/**
 * @param array $available_gateways
 * @return array
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
        // Hide Valitor, Millifærsla, Borgun, and Netgiro when Póstkrafa table rate is chosen
        'table_rate:6:8' => ['bacs', 'valitor', 'borgun', 'netgiro'],
        'table_rate:6:7' => ['bacs', 'valitor', 'borgun', 'netgiro'],
        'table_rate:6:9' => ['bacs', 'valitor', 'borgun', 'netgiro'],
        'table_rate:15:28' => ['bacs', 'valitor', 'borgun', 'netgiro'],
        'table_rate:15:29' => ['bacs', 'valitor', 'borgun', 'netgiro'],
        'table_rate:15:30' => ['bacs', 'valitor', 'borgun', 'netgiro'],

        // Hide Póstkrafa when Póstsending, Sótt í verslun and Flýtiþjónusta are chosen
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

        // Hide Póstkrafa and Bacs when Póstsending, Sótt í verslun and Flýtiþjónusta are chosen
        'table_rate:12:22' => ['cod', 'bacs'],

        'table_rate:12:23' => ['cod'],

        // Hide Póstkrafa when Free shipping is chosen
        'free_shipping:18' => ['cod'],

        // Hide Póstkrafa when Rest of Europe is chosen
        'table_rate:11:16' => ['cod'],
        'table_rate:11:17' => ['cod'],
        'table_rate:11:18' => ['cod'],

        // Hide Póstkrafa when Denmark is chosen
        'table_rate:8:15' => ['cod'],
        'table_rate:8:14' => ['cod'],
        'table_rate:8:13' => ['cod'],

        // Hide Póstkrafa when USA is chosen
        'table_rate:7:10' => ['cod'],
        'table_rate:7:11' => ['cod'],
        'table_rate:7:12' => ['cod'],

        // Hide Póstkrafa when Rest of world is chosen
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

//Grafík Panda - Email um Hljóðbók

/**
 * Add audiobook link to order if item is categorized as "hljodbok".
 *
 * @param WC_Order $order
 */
function forlagid_add_audiobook_link(WC_Order $order): void
{
    $items_rb = $order->get_items();

    foreach ($items_rb as $item) {
        /**
         * Class Item
         *
         * Represents an item in a system.
         *
         * @property int $id The unique identifier for the item.
         * @property string $name The name of the item.
         * @property float $price The price of the item.
         * @property int $quantity The current quantity of the item in stock.
         */
        if ('hljodbok' === $item->get_meta('pa_gerd')) {
            $message = '<h3>Streymis-hljóðbókin þín er nú aðgengileg í <a href="https://forlagid.is/hlusta">appinu</a> eða <a href="https://hlusta.forlagid.is">vafra.</a></h3> <br/>';
            $message .= 'Athugaðu að þú skráir þig inn þar með sömu notendaupplýsingum og á Forlagsvefnum.<br/>';

            echo $message;
        }
    }
}

add_action('woocommerce_email_before_order_table', 'forlagid_add_audiobook_link', 11);
/**
 * @param WC_Order $order
 */
function forlagid_add_thankyou_ebook_link(WC_Order $order): void
{

    $items_rb = $order->get_items();
    foreach ($items_rb as $item) {
        process_item($item);
    }
}

/**
 * @param WC_Order_Item $item
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
 * @param array $response
 * @param int $order_id
 *
 * @return bool|string
 */
/**
 * @param array $response
 * @param int $order_id
 *
 * @return bool|string
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
 * Returns the error message according to the error type
 *
 * @param string $error
 *
 * @return string
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
 * Sends an email to the administrator when an error occurs
 *
 * @param array $response
 * @param string $message
 * @param int $order_id
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
/**
 * Hide shipping methods when certain shipping is available.
 * - Póstkrafa í Flýtiþjónusta when free shipping is available.
 * - Póstkrafa when free shipping is available.
 * - Other shipping when free shipping is available.
 */

add_filter('woocommerce_package_rates', 'hide_shipping_when_free_available', 10, 2);

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
 * Returns the tooltip text for a given shipping method.
 *
 * @param object $method The shipping method object.
 * @param int $index The index of the shipping method in the list.
 *
 * @return void
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
 * Force the url for the "continue shopping" link in the cart notification to the shop page.
 *
 * @param $link
 *
 * @return string
 */
function forlagid_force_continue_shopping_shop_page($link): string
{
    return wc_get_page_permalink('shop');
}

add_filter('woocommerce_continue_shopping_redirect', 'forlagid_force_continue_shopping_shop_page');

//Includes
include_once('inc/class-forlagid-audiobooks.php');

add_filter('validate_username', 'custom_validate_username', 10, 2);
function custom_validate_username($valid, $username)
{
    if (preg_match("/\\s/", $username)) {
        // there are spaces
        return false;
    }
    return $valid;
}
/* Stop Adding Functions Below this Line */
