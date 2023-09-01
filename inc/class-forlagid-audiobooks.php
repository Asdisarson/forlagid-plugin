<?php

/**
 * Class Forlagid_Audiobooks
 *
 * product 82240 used for testing
 */

/**
 * This class includes functionalities related to Forlagid_Audiobooks.
 *
 * The class contains following public properties:
 *      $cart_has_audiobook - holds information if the cart has an audiobook
 *
 * Following public methods are available:
 * 1. __construct - constructor of the class
 * 2. handle_authentication - handles authentication of the user
 * 3. make_request - makes a request for audiobooks
 * 4. handle_error - handles response errors
 * 5. add_audiobook_checkbox - add audiobook checkbox during variation product creation
 * 6. save_variation_settings_fields - saves checkbox setting for product variation
 * 7. alter_table_for_audiobooks - changes the table for audiobooks
 * 8. change_login_redirect - changes the redirect after login
 * 9. login_notice - displays a notice during login
 * 10. available_gateways - changes available payment gateways if the cart has audiobook
 * 11. cart_has_audiobooks - checks if the cart contains audiobooks
 * 12. maybe_add_audiobook_to_user - check and add audiobook to user after payment
 * 
 * The rest of the methods are static and are being used for handling errors, adding audiobooks to user and doing actions on complete payment and display audiobook code.
 * 
 * NOTE: Detailed comments for each method are available in the method's description.
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
    /**
     * Displays the audiobook code when triggered.
     * 
     * This static function operates by taking an object, $order (of type WC_Order), loops through its items and checks
     * if an item is an audiobook using specific post meta data. If the item is not an audiobook, it is skipped, and the loop continues.
     * If the item is an audiobook, it displays a message in HTML format, acknowledging the availability of the audiobook 
     * and providing links to access it. The loop then stops.
     * 
     * @param WC_Order $order An instance of the WC_Order class.
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
    /**
     * This function handles the authentication for the user.
     * The function receives a request as a parameter.
     * From the request, it is able to get several parameters necessary for the authentication process.
     * As a part of the authentication process, the method, username, password, epoch time, and authentication hash are retrieved from the request.
     * 
     * For a more secure authentication process, a "store_secret" is used and combined with the user's credentials to authenticate the incoming request.
     * This authentication hash is then compared with the hash from the request.
     * 
     * If the authentication fails, the function returns an error response. If the authentication is successful, the function attempts to fetch 
     * the user's details.
     * An initial attempt is made to fetch the user's details by their email. If no such user exists, an attempt is then made to fetch the user by their
     * login credentials.
     * 
     * If the user's details are fetched successfully and their password is verified, a success response is returned along with some user data.
     * If the user's details aren't fetched successfully or their password isn't verified, an error response is returned.
     * 
     * @param WP_REST_Request $request The request object received to be handled by the function.
     * @return array $response An associative array containing a message or data relative to the authentication process.
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
    /** 
     * Makes a request for audiobooks.
     * 
     * This method initiates a GET request to an audiobooks providing service for certain user and single audiobook.
     * It formulates the URL for the request using user and audiobook data and includes necessary parameters for the request
     * (user’s identification data, order data, time-stamp of the request, encoded authorization signature).
     * URL is generated incrementally, then a request to a URL has been made.
     * The method then checks if the response has a status code of 200. 
     * If it does, it will pass the body of the response to the audiobook handling error function and return its result.
     * If the status code is not 200, it informs directly about connection failure to the service.
     * 
     * @param string  $audiobook_id The id of the specified audiobook for the order.
     * @param object  $order An instance of the order object.
     * @return mixed  Returns the result of the error handler in case of a successful response, 
     *                otherwise returns result of audiobook_handle_error function.
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

    /**
     * Handles error for the response to an HTTP request.
     *
     * This method takes in a $response object and an $order_id. It calls the audiobook_handle_error function if the 
     * $response object contains an error code. If there is an error code, it would then use the said error code and 
     * the $order_id as the arguments in calling the audiobook_handle_error function. However, if there is no error code, 
     * the method simply returns true, thus indicating that there were no errors encountered in the process.
     *
     * @param object $response The HTTP response object from the request made.
     * @param string $order_id The id of the order to which the request was related.
     * @return bool|mixed Returns true if there is no error code in the response, otherwise calls the audiobook_handle_error function.
     */
    
    public static function handle_error($response, $order_id)
    {

        if (isset($response->ERROR_CODE)) {
            return self::audiobook_handle_error($response->ERROR_CODE, $order_id);
        } else {
            return true;
        }

    }

    /**
     * Adds an audiobook checkbox during WooCommerce variation product creation.
     *
     * This method included in the Forlagid_Audiobooks class allows users to select whether their created product is an audiobook
     * or not, right during the product variation creation in the WooCommerce platform. This is done using a checkbox.
     *
     * Here's a quick breakdown of the passed parameters:
     * @param mixed   $loop            The index of the current variation loop in the product editing screen in WooCommerce.
     * @param array   $variation_data  The data of the current variation.
     * @param WP_Post $variation       The WordPress-based post object that holds the data and attributes of the current variation.
     *
     * By invoking the woocommerce_wp_checkbox function, a checkbox is created which once checked indicates that this
     * particular product variation is an audiobook. The checkbox's state is saved to post meta data associated with the variation ID.
     * A 'true' returned value indicates that this product variation is considered an audiobook, and 'null' or empty indicates otherwise.
     *
     * The method does not return anything. The goal is purely to facilitate the creation of an audiobook-marked product variation.
     */
    
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

    /**
     * Method save_variation_settings_fields
     *
     * This method checks if the $_POST array contains a 'forlagid_audiobook' key with the current $post_id as value.
     * If such key-value pair exists, then 'forlagid_audiobook' post meta is updating to be 1 for the provided $post_id.
     * If such key-value pair does not exist, then 'forlagid_audiobook' post meta for the provided $post_id is deleted.
     *
     * @param int $post_id The id of the post for which to save variation settings fields
     * @return void
     */
    
    public function save_variation_settings_fields($post_id)
    {

        if (isset($_POST['forlagid_audiobook'][$post_id])) {
            update_post_meta($post_id, 'forlagid_audiobook', 1);
        } else {
            delete_post_meta($post_id, 'forlagid_audiobook');
        }
    }

    /**
     * Function: alter_table_for_audiobooks
     * 
     * This function alters the table specifically for audiobooks.
     *
     * @param array $orderedcols Array of ordered columns
     * @param array $variation Contains variation details
     * @param string $attrnames Names of the attributes
     *
     * @return array Returns array of ordered columns
     * 
     * The method first checks if the user is logged in. If it's not, the method retrieves metadata for audiobooks using
     * the variation id. This metadata is used to check if the current object is an audiobook.
     * If it is, the function generates a login URL and adds an HTML anchor tag in the 'vartable_cart' of $orderedcols array
     * with a link to the login URL.
     * 
     * The method is primarily used to protect access to audiobooks by ensuring that only logged-in users can add the audiobook
     * to the cart. This ensures that any queries or operations on the audiobooks are done by authenticated users, hence
     * enhancing security.
     * 
     * This function doesn't receive any constants to influence the functionality. It is a part of 'vartable_output_array' filter.
     *
     * @since version <insert version where this method was introduced>
     * 
     */
    
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

    /**
     * The `change_login_redirect` method is a part of Forlagid_Audiobooks class. 
     *
     * This function is used to customize the redirect URL in the login form. The function checks if the 'fa_redirect' parameter 
     * is set in the URL. If it is set, the function embeds it as a hidden input field in the login form. This way, users are 
     * redirected to the specified URL after successful login.
     *
     * The method doesn't take any parameters and doesn't return any value. It prints an HTML string directly in the login form.
     *
     * Note: This method needs to be attached to the 'woocommerce_login_form' action hook to function properly. The echo statement
     * inside this method directly outputs a string, so it should be used in a context where this output would become part of 
     * the final HTML code, specifically within a form.
     */
    
    public function change_login_redirect()
    {

        if (isset($_GET['fa_redirect'])) {

            echo '<input type="hidden" name="redirect" value="' . esc_url($_GET['fa_redirect']) . '" />';

        }
    }

    /**
     * Displays a notice during the login process.
     * 
     * This method checks if a 'fa_redirect' parameter is set in the GET request. If the parameter is set, it calls wc_add_notice
     * to display a notice to the user saying that they need to log in to purchase streamed audio books.
     */
    
    public function login_notice()
    {
        if (isset($_GET['fa_redirect'])) {
            wc_add_notice('Ath. Þú þarft að skrá þig inn til að geta keypt streymishljóðbækur.', 'notice');
        }
    }

    /**
     * Changes available payment gateways if the cart contains an audiobook.
     *
     * This method takes an associative array $gateways as an argument, containing all available payment gateways. 
     * Initially, checks whether the cart has any audiobooks through the invoking of the cart_has_audiobooks() method.
     * If true, then it only includes 'valitorpay', 'borgun', and 'netgiro' gateways, given they exist in $gateways.
     * It then returns the filtered $gateways array.
     *
     * @param array $gateways An associative array containing all available payment gateways.
     * @return array Returns an associative array that includes only 'valitorpay', 'borgun', and 'netgiro' gateways if the cart has audiobooks, otherwise returns the original $gateways array.
     */
    
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

    /**
     * Checks if the customer's cart contains any audiobooks.
     *
     * This method first checks if the $cart_has_audiobook property is not yet set, and if the current user is not an admin, and the cart is not null.
     * If this condition is met, it initialises $cart_items with the list of items in the cart. Then, it begins a loop over the items.
     * Each iteration checks if a product variation_id exists and sets $product_id to it else uses product_id. Then it checks
     * if the item is an audiobook using get_post_meta function.
     * If $is_audiobook evaluates to true, it sets $cart_has_audiobook property to true and breaks out of the loop.
     * At the end, the method returns the $cart_has_audiobook property.
     *
     * @return bool $this->cart_has_audiobook True if the cart has audiobooks, false otherwise.
     */
    
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

    /**
     * Method to add an audiobook to a user.
     *
     * This method is used to add an audiobook to a user's order. It accepts three parameters - 
     * the ID of the order item ($item_id), the order object itself ($order) and the ID of the product ($product_id).
     * The $product_id is optional and if it is not passed or is explicitly set to false, it is obtained from the order item's metadata. 
     * If '_variation_id' metadata is empty, '_product_id' metadata is used. 
     * With the product's ID, this method makes a request using the 'epub_uuid' metadata of the product which represents the id of the audiobook in the epub system. 
     * If the request is successful, '_audiobook_sent' metadata of the item is set to 1 (true), indicating that the audiobook has been successfully sent to the user. 
     * If the request is unsuccessful, this method saves the error as 'Audiobook error' metadata of the item for further debugging or user notification.
     *
     * @param int      $item_id    The ID of the order item
     * @param WC_Order $order      The order containing the item
     * @param false|int $product_id ID of the product (audiobook). Default is false
     */
    
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
    /**
     * @method maybe_add_audiobook_to_user
     *
     * The maybe_add_audiobook_to_user checks if an order contains audiobook items. Given an order either as an ID(int) or an 
     * object instance of WC_Order, this method will first ensure it has a WC_Order object. It proceeds to retrieve all items 
     * in the order. If an order's item is an audiobook (determined by the 'forlagid_audiobook' metadata), the order's post 
     * metadata is updated to indicate the presence of an audiobook. The audiobook is then added to the user. If at least one 
     * audiobook is found in the order, a message will be displayed to the user.
     *
     * @param mixed $order The WooCommerce order object or ID to be checked.
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

    /**
     * This method, display_audiobook_text, is a public and static function of the Forlagid_Audiobooks class. It generates sections
     * within an HTML webpage, specifically paragraphs and hyperlinks, with predefined text. However, it does not take any parameters
     * nor does it return any specific output. Essentially, this method forms part of the frontend functionality intended to deliver 
     * instructions to the user for listening to audiobooks. The user is provided links to either the app or the browser for listening.
     * The text within the method is hardcoded in Icelandic language.
     */
    
    public static function display_audiobook_text($order)
    {

        ?>
        <p>Þú hlustar á streymis-hljóðbókina þína í <a href="https://www.forlagid.is/hlusta">appi</a> eða
            <a href="https://hlusta.forlagid.is/">vafra</a>.
            Athugaðu að þú skráir þig inn þar með sömu notendaupplýsingum og á Forlagsvefnum.</p>
        <?php

    }


    /**
     * Handles the given error originated from the audiobook service.
     * 
     * This is a public static method of the Forlagid_Audiobooks class. The function first trims the provided error message to ensure no leading or 
     * trailing white spaces. Then it retrieves a more readable message associated with the given error code by calling the getErrorMessage function. 
     * If this readable error message exists and if the supplied order ID is different from 0, meaning if the function knows which order led to the 
     * error, it sends a notification about the error with the order ID and the readable error message, by calling the sendErrorNotification function. 
     * Finally, it simply returns the readable message to the caller.
     * 
     * @param string $error     The specific error that needs to be handled. This is the error message or code from the audiobook service.
     * @param int    $order_id  The ID of the order that led to the error. Default value is 0, which means it could be any order.
     * 
     * @return string           The readable error message associated with the given error code.
     */
    
    public static function audiobook_handle_error($error, $order_id = 0)
    {
        $error = trim($error);

        $message = self::getErrorMessage($error);

        if ($message && $order_id !== 0) {
            self::sendErrorNotification($order_id, $message);
        }

        return $message;
    }

    /**
     * Retrieves error messages based on error key.
     *
     * This private and static function getErrorMessage($error) accepts an error key as a parameter. It has a list of predefined 
     * error messages in an associative array format where the key is the error type and the value is the error message.
     * It uses the error parameter passed as the key to fetch the relevant error message from the array.
     *
     * If the passed error key exists in the predefined list, it returns an error message string that instructs the user to contact
     * the support team with information about the occurred error. If the error key does not exist, the function returns false.
     *
     * @param string $error Error key to fetch relevant error message.
     * @return string|false Returns a string with error message if error key is found, otherwise returns false.
     */
    
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

    /**
     * Sends a error notification email on failed attempt to deliver an audiobook.
     *
     * This function is responsible for sending error notifications via email when
     * there's an issue with delivering an audiobook. The function first checks if a 
     * notification was previously sent by fetching the post meta data using the given 
     * $orderId and a specific key. If no previous notifications were sent, get_option 
     * function is used to fetch the admin's email address.
     *
     * An email text is prepared, containing both the $orderId and $message passed to 
     * the function. An array of headers for the email is set, containing content type
     * and multiple carbon copy (Cc) and blind carbon copy (Bcc) recipients. 
     *
     * wp_mail function is then used to send out the prepared email. Upon successful 
     * sending of the email, the function sets a meta key on the post signifying that a 
     * notification has been sent.
     *
     * @param int $orderId The ID of the order.
     * @param string $message The content of the error message.
     * @return void
     */
     
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
