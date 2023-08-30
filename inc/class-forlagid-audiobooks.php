<?php

/**
 * Class Forlagid_Audiobooks
 *
 * product 82240 used for testing
 */
class Forlagid_Audiobooks
{

    public $cart_has_audiobook;

    public function __construct()
    {

        add_action('woocommerce_product_after_variable_attributes', array($this, 'add_audiobook_checkbox'), 10, 3);

        add_action('woocommerce_save_product_variation', array($this, 'save_variation_settings_fields'), 15);

        add_filter('vartable_output_array', array($this, 'alter_table_for_audiobooks'), 10, 3);

        add_action('woocommerce_login_form', array($this, 'change_login_redirect'));

        add_action('woocommerce_init', array($this, 'login_notice'));

        add_filter('woocommerce_available_payment_gateways', array($this, 'available_gateways'));

        add_action('forlagid_ebook_link', array($this, 'display_audiobook_code'), 12);
        add_action('woocommerce_payment_complete', array($this, 'maybe_add_audiobook_to_user'), 7);

        add_action('rest_api_init', function () {
            register_rest_route('forlagid', '/signin', array(
                'methods' => 'GET, POST',
                'callback' => array($this, 'handle_authentication'),
            ));
        });

    }


    /**
     * @param WC_Order $order
     */
    public static function display_audiobook_code($order)
    {

        $items_rb = $order->get_items();
        foreach ($items_rb as $item_id => $item) {

            $is_audiobook = get_post_meta($item['variation_id'], 'forlagid_audiobook', true);

            if (empty($is_audiobook)) {
                continue;
            } else {
                echo '<p>';
                echo 'Streymis-hljóðbókin þín er nú aðgengileg í <a href="https://www.forlagid.is/hlusta">appinu</a> eða <a href="https://hlusta.forlagid.is/">vafra</a>.<br />';
                echo 'Athugaðu að þú skráir þig inn þar með sömu notendaupplýsingum og á Forlagsvefnum.<br />';


                echo '</p>';
                break;
            }
        }
    }


    /**
     * Authenticate the user.
     *
     * @param WP_REST_Request $request The request object.
     *
     * @return mixed
     */
    public function handle_authentication($request)
    {

        $method = $request->get_method();
        $username = $request->get_param('username');
        $password = $request->get_param('password');
        $epoch = $request->get_param('epoch');
        $auth = $request->get_param('auth');
        $store_secret = '8cc4fdb8-c8aa-11e7-8e93-8f9b0d7632d6';

        $parameters = http_build_query(array(
            'username' => $username,
            'password' => $password,
            'epoch' => $epoch,
        ));

        $check_auth = (base64_encode(hash_hmac('sha256', $parameters, $store_secret, true)));
        if ('POST' === $method) {
            $check_auth = urlencode($check_auth);
        }

        $response['RESULT'] = 'ERROR';

        if ($auth !== $check_auth) {
            $response['MESSAGE'] = 'Authentication failed';

            return $response;
        }

        $user = get_user_by('email', $username);
        if (!$user) {
            $user = get_user_by('login', $username);
        }
        if ($user && wp_check_password($password, $user->data->user_pass, $user->ID)) {
            $response['RESULT'] = 'SUCCESS';
            $response['DATA'] = [
                'fullname' => $user->display_name,
                'username' => $user->user_login,
            ];
        } else {
            $response['MESSAGE'] = 'Login failed. Invalid username/password';
        }

        return $response;
    }

    /**
     * @param          $audiobook_id
     * @param WC_Order $order
     *
     * @return bool|string
     */
    public static function make_request($audiobook_id, $order)
    {

        $url = 'https://hlusta.forlagid.is/api/admin/add_audiobook';
        $audiobook_id = urlencode($audiobook_id);
        $store_secret = urlencode("8cc4fdb8-c8aa-11e7-8e93-8f9b0d7632d6");
        $store_order_id = urlencode($order->get_id());
        $store_epoch = urlencode(time());
        $order_user = $order->get_user();
        $username = $order_user->user_login;

        $parameters = "username={$username}&audiobook_id={$audiobook_id}&store_order_id={$store_order_id}&store_epoch={$store_epoch}";
        $auth = urlencode(base64_encode(hash_hmac('sha256', $parameters, $store_secret, true)));

        $url = add_query_arg(array(
            'username' => $username,
            'audiobook_id' => $audiobook_id,
            'store_order_id' => $store_order_id,
            'store_epoch' => $store_epoch,
            'auth' => $auth,
        ), $url);

        $request = wp_remote_get($url);

        if (200 === wp_remote_retrieve_response_code($request)) {
            return self::handle_error(json_decode(wp_remote_retrieve_body($request)), $order->get_id());
        } else {
            return self::audiobook_handle_error('ERROR_CONNECTING_TO_SERVER', $order->get_id());
        }
    }

    public static function handle_error($response, $order_id)
    {

        if (isset($response->ERROR_CODE)) {
            return self::audiobook_handle_error($response->ERROR_CODE, $order_id);
        } else {
            return true;
        }

    }

    public function add_audiobook_checkbox($loop, $variation_data, $variation)
    {

        woocommerce_wp_checkbox(
            array(
                'id' => 'forlagid_audiobook[' . $variation->ID . ']',
                'label' => __('Audiobook?', 'woocommerce'),
                'desc_tip' => 'true',
                'description' => __('Settu​ ​hak​ ​í​ ​reitinn​ ​ef​ ​þetta​ ​er​ ​streymis​ ​hljóðbók.', 'woocommerce'),
                'cbvalue' => 1,
                'value' => get_post_meta($variation->ID, 'forlagid_audiobook', true),
            )
        );
    }

    public function save_variation_settings_fields($post_id)
    {

        if (isset($_POST['forlagid_audiobook'][$post_id])) {
            update_post_meta($post_id, 'forlagid_audiobook', 1);
        } else {
            delete_post_meta($post_id, 'forlagid_audiobook');
        }
    }

    public function alter_table_for_audiobooks($orderedcols, $variation, $attrnames)
    {

        if (!is_user_logged_in()) {
            $variation_id = intval($variation['variation_id']);
            $is_audiobook = get_post_meta($variation_id, 'forlagid_audiobook', true);

            if (!empty($is_audiobook)) {
                $login_url = get_permalink(get_option('woocommerce_myaccount_page_id'));
                $login_url = add_query_arg('fa_redirect', site_url($_SERVER['REQUEST_URI']), $login_url);
                $orderedcols['vartable_cart'] = '<td>';
                $orderedcols['vartable_cart'] .= '<a href="' . esc_url($login_url) . '" class="single_add_to_cart_button button alt">Setja í körfu</a>';
                $orderedcols['vartable_cart'] .= '</td>';
            }
        }

        return $orderedcols;

    }

    public function change_login_redirect()
    {

        if (isset($_GET['fa_redirect'])) {

            echo '<input type="hidden" name="redirect" value="' . esc_url($_GET['fa_redirect']) . '" />';

        }
    }

    public function login_notice()
    {
        if (isset($_GET['fa_redirect'])) {
            wc_add_notice('Ath. Þú þarft að skrá þig inn til að geta keypt streymishljóðbækur.', 'notice');
        }
    }

    public function available_gateways($gateways)
    {

        if ($this->cart_has_audiobooks()) {
            $orig_gateways = $gateways;
            $gateways = array();

            if (isset($orig_gateways['valitorpay'])) { // Lárus - breytt ur valitor yfir í valitorpay
                $gateways['valitorpay'] = $orig_gateways['valitorpay'];// Lárus - breytt ur valitor yfir í valitorpay
            }
            if (isset($orig_gateways['borgun'])) {
                $gateways['borgun'] = $orig_gateways['borgun'];
            }
            if (isset($orig_gateways['netgiro'])) {
                $gateways['netgiro'] = $orig_gateways['netgiro'];
            }
        }

        return $gateways;
    }

    public function cart_has_audiobooks()
    {

        if (!isset($this->cart_has_audiobook) && !is_admin() && !is_null(WC()->cart)) {

            $cart_items = WC()->cart->get_cart();

            foreach ($cart_items as $cart_item) {
                $product_id = isset($cart_item['variation_id']) ? $cart_item['variation_id'] : $cart_item['product_id'];
                $is_audiobook = empty(get_post_meta($product_id, 'forlagid_audiobook', true)) ? false : true;
                if ($is_audiobook) {
                    $this->cart_has_audiobook = true;

                    break;
                }
            }
        }

        return $this->cart_has_audiobook;

    }

    public static function add_audiobook_to_user($item_id, $order, $product_id = false)
    {

        $audiobook_sent = wc_get_order_item_meta($item_id, '_audiobook_sent', true);

        if (empty($audiobook_sent)) {
            if (false === $product_id) {
                $product_id = wc_get_order_item_meta($item_id, '_variation_id', true);
                if (0 === intval($product_id)) {
                    $product_id = wc_get_order_item_meta($item_id, '_product_id', true);
                }
            }

            $epub_id = get_post_meta($product_id, 'epub_uuid', true);
            $audibook_request = self::make_request($epub_id, $order);

            if (true === $audibook_request) {
                wc_add_order_item_meta($item_id, '_audiobook_sent', 1);
            } else {
                wc_add_order_item_meta($item_id, 'Audiobook error', $audibook_request, true);
            }


        }
    }

    /**
     * @param WC_Order $order
     */
    public static function maybe_add_audiobook_to_user($order)
    {

        if (is_int($order)) {
            $order = wc_get_order($order);
        }
//
//		if ( ! $order->is_paid() ) {
//			return;
//		}

        $items_rb = $order->get_items();
        $is_audiobook_order = false;
        foreach ($items_rb as $item_id => $item) {

            $is_audiobook = get_post_meta($item['variation_id'], 'forlagid_audiobook', true);

            if (empty($is_audiobook)) {
                continue;
            } else {
                $is_audiobook_order = true;
                update_post_meta($order->get_id(), 'forlagid_has_audiobook', true);
            }

            self::add_audiobook_to_user($item_id, $order, $item['variation_id']);
        }

        if ($is_audiobook_order) {
            self::display_audiobook_text($order);
        }
    }

    public static function display_audiobook_text($order)
    {

        ?>
        <p>Þú hlustar á streymis-hljóðbókina þína í <a href="https://www.forlagid.is/hlusta">appi</a> eða
            <a href="https://hlusta.forlagid.is/">vafra</a>.
            Athugaðu að þú skráir þig inn þar með sömu notendaupplýsingum og á Forlagsvefnum.</p>
        <?php

    }


    public static function audiobook_handle_error($error, $order_id = 0)
    {
        $error = trim($error);

        $message = self::getErrorMessage($error);

        if ($message && $order_id !== 0) {
            self::sendErrorNotification($order_id, $message);
        }

        return $message;
    }

    private static function getErrorMessage($error)
    {
        $messages = [
            'ERROR' => 'Wrong response format',
            'AUDIOBOOK_ID_INVALID' => 'Auðkenni gallað',
            'AUDIOBOOK_DOES_NOT_EXISTS' => 'Auðkenni verks er ekki til',
            'PARAM_NO_GOOD' => 'Færibreytu vantar í fyrirspurn',
            'EPOCH_NO_GOOD' => 'Tími ekki réttur. Tíminn sem er gefinn upp í epoch færibreytunni má hvorki vera 10 mínútur of fljótur eða seinn miðað við tíman á vefþjóni hlusta.forlagid.is',
            'PARAMETERS_DO_NOT_MATCH_AUTH' => 'Villa í auth streng',
            'NO_SECRET' => 'Verslun vantar leyndarmál',
            'AUDIOBOOK_NOT_FOR_SALE' => 'Hljóðbók ekki til sölu',
            'ERROR_CONNECTING_TO_SERVER' => 'Kemst ekki í samband við hljóðbókaserver',
            '' => 'Svar tómt'
        ];

        $message = $messages[$error] ?? false;

        return $message ? 'Streymis-hljóðbók þín barst ekki. <br /> Vinsamlega hafðu samband við okkur og segðu okkur að villan "' . $message . '", hafi komið upp.' : false;
    }

    private static function sendErrorNotification($orderId, $message)
    {
        // Send admin email
        $notification_sent = get_post_meta($orderId, 'forlagid_audiobook_error_notification_sent', true);
        if (empty($notification_sent)) {
            $to = get_option('admin_email');
            $email_text = "<p>Hæ!</p>
        <p>Það kom upp villa við afhendingu rafbókar í pöntun númer: <b>{$orderId}</b>.</p>
        <p>Villan er eftirfarandi: <b>{$message}</b>.</p><br />
        <p>Kveðja</p><p>Vefurinn.</p>";
            $headers = [
                'Content-Type: text/html; charset=UTF-8',
                'Cc: marino@snara.is',
                'Cc: kristin@dottirwebdesign.is',
                'Bcc: mirceas17@gmail.com'
            ];

            wp_mail($to, 'Villa við afhendingu á rafbók', $email_text, $headers);
            add_post_meta($orderId, 'forlagid_audiobook_error_notification_sent', 1);
        }
    }
}

new Forlagid_Audiobooks();
