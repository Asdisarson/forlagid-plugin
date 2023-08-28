<?php

/**
 * Class Forlagid_Audiobooks
 *
 * product 82240 used for testing
 */
class Forlagid_Audiobooks {

	public $cart_has_audiobook;

	public function __construct() {

		add_action( 'woocommerce_product_after_variable_attributes', array( $this, 'add_audiobook_checkbox' ), 10, 3 );

		add_action( 'woocommerce_save_product_variation', array( $this, 'save_variation_settings_fields' ), 15 );

		add_filter( 'vartable_output_array', array( $this, 'alter_table_for_audiobooks' ), 10, 3 );

		add_action( 'woocommerce_login_form', array( $this, 'change_login_redirect' ) );

		add_action( 'woocommerce_init', array( $this, 'login_notice' ) );

		add_filter( 'woocommerce_available_payment_gateways', array( $this, 'available_gateways' ) );

		add_action( 'forlagid_ebook_link', array( $this, 'display_audiobook_code' ), 12 );
		add_action( 'woocommerce_payment_complete', array( $this, 'maybe_add_audiobook_to_user' ), 7 );
//		add_action( 'woocommerce_thankyou_valitor', array( $this, 'maybe_add_audiobook_to_user' ), 7 );
//		add_action( 'woocommerce_thankyou_borgun', array( $this, 'maybe_add_audiobook_to_user' ), 7 );
//		add_action( 'woocommerce_order_details_after_customer_details', array( $this, 'maybe_add_audiobook_to_user' ), 12 );

//		add_filter( 'woocommerce_account_menu_items', array( $this, 'add_audiobooks_menu_item' ) );

//		add_action( 'woocommerce_account_audiobooks_endpoint', array( $this, 'audiobooks_endpoint' ) );

//		add_action( 'init', array( $this, 'add_audiobooks_endpoint' ) );

//		add_filter( 'query_vars', array( $this, 'audiobooks_query_vars' ), 0 );

//		add_action( 'woocommerce_admin_order_data_after_billing_address', array( $this, 'display_audiobook_info') );

		add_action( 'rest_api_init', function () {
			register_rest_route( 'forlagid', '/signin', array(
				'methods'  => 'GET, POST',
				'callback' => array( $this, 'handle_authentication' ),
			) );
		} );

	}

	public function display_audiobook_info( $order ) {

		$user = wp_get_current_user();

		if ( 'mircea' === $user->user_login ) {
			$items_rb = $order->get_items();
			foreach ( $items_rb as $item_id => $item ) {
				$sent = get_metadata( 'order_item', $item_id );
				echo '<pre>';
				var_dump( $sent );
				echo '</pre>';
			}
		}

	}

	/**
	 * @param WC_Order $order
	 */
	public static function display_audiobook_code( $order ) {

		$items_rb = $order->get_items();
		foreach ( $items_rb as $item_id => $item ) {

			$is_audiobook = get_post_meta( $item['variation_id'], 'forlagid_audiobook', true );

			if ( empty( $is_audiobook ) ) {
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
	public function handle_authentication( $request ) {

		$method       = $request->get_method();
		$username     = $request->get_param( 'username' );
		$password     = $request->get_param( 'password' );
		$epoch        = $request->get_param( 'epoch' );
		$auth         = $request->get_param( 'auth' );
		$store_secret = '8cc4fdb8-c8aa-11e7-8e93-8f9b0d7632d6';

		$parameters = http_build_query( array(
			'username' => $username,
			'password' => $password,
			'epoch'    => $epoch,
		) );

//		$parameters = 'username=' . $username . '&password=' . $password . '&epoch=' . $epoch;

		$check_auth = ( base64_encode( hash_hmac( 'sha256', $parameters, $store_secret, true ) ) );
		if ( 'POST' === $method ) {
			$check_auth = urlencode( $check_auth );
		}

		$response['RESULT'] = 'ERROR';

		if ( $auth !== $check_auth ) {
			$response['MESSAGE'] = 'Authentication failed';

			return $response;
		}

		$user = get_user_by( 'email', $username );
		if ( ! $user ) {
			$user = get_user_by( 'login', $username );
		}
		if ( $user && wp_check_password( $password, $user->data->user_pass, $user->ID ) ) {
			$response['RESULT'] = 'SUCCESS';
			$response['DATA']   = [
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
	public static function make_request( $audiobook_id, $order ) {

		$url            = 'https://hlusta.forlagid.is/api/admin/add_audiobook';
		$audiobook_id   = urlencode( $audiobook_id );
		$store_secret   = urlencode( "8cc4fdb8-c8aa-11e7-8e93-8f9b0d7632d6" );
		$store_order_id = urlencode( $order->get_id() );
		$store_epoch    = urlencode( time() );
		$order_user     = $order->get_user();
		$username       = $order_user->user_login;

		$parameters = "username={$username}&audiobook_id={$audiobook_id}&store_order_id={$store_order_id}&store_epoch={$store_epoch}";
		$auth       = urlencode( base64_encode( hash_hmac( 'sha256', $parameters, $store_secret, true ) ) );

		$url = add_query_arg( array(
			'username'       => $username,
			'audiobook_id'   => $audiobook_id,
			'store_order_id' => $store_order_id,
			'store_epoch'    => $store_epoch,
			'auth'           => $auth,
		), $url );

		$request = wp_remote_get( $url );

		if ( 200 === wp_remote_retrieve_response_code( $request ) ) {
			return self::handle_error( json_decode( wp_remote_retrieve_body( $request ) ), $order->get_id() );
		} else {
			return self::audiobook_handle_error( 'ERROR_CONNECTING_TO_SERVER', $order->get_id() );
		}
	}

	public static function handle_error( $response, $order_id ) {

		if ( isset( $response->ERROR_CODE ) ) {
			return self::audiobook_handle_error( $response->ERROR_CODE, $order_id );
		} else {
			return true;
		}

	}

	public function add_audiobook_checkbox( $loop, $variation_data, $variation ) {

		woocommerce_wp_checkbox(
			array(
				'id'          => 'forlagid_audiobook[' . $variation->ID . ']',
				'label'       => __( 'Audiobook?', 'woocommerce' ),
				'desc_tip'    => 'true',
				'description' => __( 'Settu​ ​hak​ ​í​ ​reitinn​ ​ef​ ​þetta​ ​er​ ​streymis​ ​hljóðbók.', 'woocommerce' ),
				'cbvalue'     => 1,
				'value'       => get_post_meta( $variation->ID, 'forlagid_audiobook', true ),
			)
		);
	}

	public function save_variation_settings_fields( $post_id ) {

		if ( isset( $_POST['forlagid_audiobook'][ $post_id ] ) ) {
			update_post_meta( $post_id, 'forlagid_audiobook', 1 );
		} else {
			delete_post_meta( $post_id, 'forlagid_audiobook' );
		}
	}

	public function alter_table_for_audiobooks( $orderedcols, $variation, $attrnames ) {

		if ( ! is_user_logged_in() ) {
			$variation_id = intval( $variation['variation_id'] );
			$is_audiobook = get_post_meta( $variation_id, 'forlagid_audiobook', true );

			if ( ! empty( $is_audiobook ) ) {
				$login_url                    = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) );
				$login_url                    = add_query_arg( 'fa_redirect', site_url( $_SERVER['REQUEST_URI'] ), $login_url );
				$orderedcols['vartable_cart'] = '<td>';
				$orderedcols['vartable_cart'] .= '<a href="' . esc_url( $login_url ) . '" class="single_add_to_cart_button button alt">Setja í körfu</a>';
				$orderedcols['vartable_cart'] .= '</td>';
			}
		}

		return $orderedcols;

	}

	public function change_login_redirect() {

		if ( isset( $_GET['fa_redirect'] ) ) {

			echo '<input type="hidden" name="redirect" value="' . esc_url( $_GET['fa_redirect'] ) . '" />';

		}
	}

	public function login_notice() {
		if ( isset( $_GET['fa_redirect'] ) ) {
			wc_add_notice( 'Ath. Þú þarft að skrá þig inn til að geta keypt streymishljóðbækur.', 'notice' );
		}
	}

	public function available_gateways( $gateways ) {

		if ( $this->cart_has_audiobooks() ) {
			$orig_gateways = $gateways;
			$gateways      = array();

			if ( isset( $orig_gateways['valitorpay'] ) ) { // Lárus - breytt ur valitor yfir í valitorpay
				$gateways['valitorpay'] = $orig_gateways['valitorpay'];// Lárus - breytt ur valitor yfir í valitorpay
			}
			if ( isset( $orig_gateways['borgun'] ) ) {
				$gateways['borgun'] = $orig_gateways['borgun'];
			}
			if ( isset( $orig_gateways['netgiro'] ) ) {
				$gateways['netgiro'] = $orig_gateways['netgiro'];
			}
		}

		return $gateways;
	}

	public function cart_has_audiobooks() {

		if ( ! isset( $this->cart_has_audiobook ) && ! is_admin() && ! is_null( WC()->cart ) ) {

			$cart_items = WC()->cart->get_cart();

			foreach ( $cart_items as $cart_item ) {
				$product_id   = isset( $cart_item['variation_id'] ) ? $cart_item['variation_id'] : $cart_item['product_id'];
				$is_audiobook = empty( get_post_meta( $product_id, 'forlagid_audiobook', true ) ) ? false : true;
				if ( $is_audiobook ) {
					$this->cart_has_audiobook = true;

					break;
				}
			}
		}

		return $this->cart_has_audiobook;

	}

	public static function add_audiobook_to_user( $item_id, $order, $product_id = false ) {

		$audiobook_sent = wc_get_order_item_meta( $item_id, '_audiobook_sent', true );

		if ( empty( $audiobook_sent ) ) {
			if ( false === $product_id ) {
				$product_id = wc_get_order_item_meta( $item_id, '_variation_id', true );
				if ( 0 === intval( $product_id ) ) {
					$product_id = wc_get_order_item_meta( $item_id, '_product_id', true );
				}
			}

			$epub_id          = get_post_meta( $product_id, 'epub_uuid', true );
			$audibook_request = self::make_request( $epub_id, $order );

			if ( true === $audibook_request ) {
				wc_add_order_item_meta( $item_id, '_audiobook_sent', 1 );
			} else {
				wc_add_order_item_meta( $item_id, 'Audiobook error', $audibook_request, true );
			}


		}
	}

	/**
	 * @param WC_Order $order
	 */
	public static function maybe_add_audiobook_to_user( $order ) {

		if ( is_int( $order ) ) {
			$order = wc_get_order( $order );
		}
//
//		if ( ! $order->is_paid() ) {
//			return;
//		}

		$items_rb           = $order->get_items();
		$is_audiobook_order = false;
		foreach ( $items_rb as $item_id => $item ) {

			$is_audiobook = get_post_meta( $item['variation_id'], 'forlagid_audiobook', true );

			if ( empty( $is_audiobook ) ) {
				continue;
			} else {
				$is_audiobook_order = true;
				update_post_meta( $order->get_id(), 'forlagid_has_audiobook', true );
			}

			self::add_audiobook_to_user( $item_id, $order, $item['variation_id'] );
		}

		if ( $is_audiobook_order ) {
			self::display_audiobook_text( $order );
		}
	}

	public static function display_audiobook_text( $order ) {

		?>
		<p>Þú hlustar á streymis-hljóðbókina þína í <a href="https://www.forlagid.is/hlusta">appi</a> eða
			<a href="https://hlusta.forlagid.is/">vafra</a>.
			Athugaðu að þú skráir þig inn þar með sömu notendaupplýsingum og á Forlagsvefnum.</p>
		<?php

	}


	public static function audiobook_handle_error( $error, $order_id = 0 ) {

		$error = trim( $error );

		$message = false;
		if ( 'ERROR' === $error ) {
			$message = 'Wrong response format';
		}
		if ( 'AUDIOBOOK_ID_INVALID' === $error ) {
			$message = 'Auðkenni gallað';
		}
		if ( 'AUDIOBOOK_DOES_NOT_EXISTS' === $error ) {
			$message = 'Auðkenni verks er ekki til';
		}
		if ( 'PARAM_NO_GOOD' === $error ) {
			$message = 'Færibreytu vantar í fyrirspurn';
		}
		if ( 'EPOCH_NO_GOOD' === $error ) {
			$message = 'Tími ekki réttur. Tíminn sem er gefinn upp í epoch færibreytunni má hvorki vera 10 mínútur of fljótur eða seinn miðað við tíman á vefþjóni hlusta.forlagid.is';
		}
		if ( 'PARAMETERS_DO_NOT_MATCH_AUTH' === $error ) {
			$message = 'Villa í auth streng';
		}
		if ( 'NO_SECRET' === $error ) {
			$message = 'Verslun vantar leyndarmál';
		}
		if ( 'AUDIOBOOK_NOT_FOR_SALE' === $error ) {
			$message = 'Hljóðbók ekki til sölu';
		}
		if ( 'ERROR_CONNECTING_TO_SERVER' === $error ) {
			$message = 'Kemst ekki í samband við hljóðbókaserver';
		}
		if ( empty( $error ) ) {
			$message = 'Svar tómt';
		}

		if ( $message ) {
			$message = 'Streymis-hljóðbók þín barst ekki. <br /> Vinsamlega hafðu samband við okkur og segðu okkur að villan "' . $message . '", hafi komið upp.';
		}

		if ( 0 !== $order_id && $message ) {

			// Send admin email
			$notification_sent = get_post_meta( $order_id, 'forlagid_audiobook_error_notification_sent', true );
			if ( empty( $notification_sent ) ) {
				$to         = get_option( 'admin_email' );
				$email_text = "<p>Hæ!</p>
<p>Það kom upp villa við afhendingu rafbókar í pöntun númer: <b>{$order_id}</b>.</p>
<p>Villan er eftirfarandi: <b>{$message}</b>.</p><br />
<p>Kveðja</p><p>Vefurinn.</p>";
				$headers = array(
					'Content-Type: text/html; charset=UTF-8',
					'Cc: marino@snara.is',
					'Cc: kristin@dottirwebdesign.is',
					'Bcc: mirceas17@gmail.com',
				);

				wp_mail( $to, 'Villa við afhendingu á rafbók', $email_text, $headers );
				add_post_meta( $order_id, 'forlagid_audiobook_error_notification_sent', 1 );
			}
		}

		return $message;

	}

	public function add_audiobooks_menu_item( $menu_items ) {

		$insert_after  = 'orders';
		$ordered_items = array();

		foreach ( $menu_items as $key => $item ) {

			$ordered_items[ $key ] = $item;
			if ( $insert_after === $key ) {
				$ordered_items['audiobooks'] = esc_html__( 'Hljóðbækur', 'forlagid' );
			}

		}

		return $ordered_items;

	}

	function add_audiobooks_endpoint() {
		add_rewrite_endpoint( 'audiobooks', EP_ROOT | EP_PAGES );
	}

	function audiobooks_query_vars( $vars ) {
		$vars[] = 'audiobooks';

		return $vars;
	}

	public function audiobooks_endpoint() {

		$orders_query = array(
			'post_type'   => 'shop_order',
			'post_author' => get_current_user_id(),
			'meta_query'  => array(
				array(
					'key'     => 'forlagid_has_audiobook',
					'compare' => 'EXISTS',
				)
			),
			'post_status' => 'any',
		);
		$orders       = new WP_Query( $orders_query );

		if ( $orders->have_posts() ) {

			?>
			<table class="table">
				<thead>
				<tr>
					<th></th>
					<th><?php esc_html_e( 'Titill', 'forlagid' ); ?></th>
					<th><?php esc_html_e( 'Höfundur', 'forlagid' ); ?></th>
					<th><?php esc_html_e( 'Kóði', 'forlagid' ); ?></th>
				</tr>
				</thead>
				<tbody>
				<?php

				while ( $orders->have_posts() ) {
					$orders->the_post();

					$order       = wc_get_order( get_the_ID() );
					$order_items = $order->get_items();
					foreach ( $order_items as $item_id => $order_item ) {
						/**
						 * @var WC_Order_Item $order_item
						 */
						$item_audiobook_code = $order_item->get_meta( 'Audiobook code', true );
						if ( ! empty( $item_audiobook_code ) ) {
							/**
							 * @var WC_Product_Variation $product
							 */
							$product = $order_item->get_product();
							?>
							<tr>
								<td>
									<?php echo get_the_post_thumbnail( $product->get_parent_id(), 'thumbnail' ); ?>
								</td>
								<td>
									<a href="<?php echo esc_url( get_permalink( $product->get_parent_id() ) ); ?>"><?php echo esc_html( $product->get_title() ); ?></a>
								</td>
								<td>
									<?php
									$posts = get_field( 'hofundur', $product->get_parent_id() );

									if ( $posts ): ?>
										<?php
										$hofundar_count     = 0;
										$hofundar_separator = ' ';
										foreach ( $posts as $p ) {
											$hofundar_count ++;
											if ( $hofundar_count > 1 ) {
												$hofundar_separator = ', ';
											}
											if ( get_post_status( $p->ID ) == 'publish' ) {

												echo '<span>' . $hofundar_separator . '<a href="' . get_permalink( $p->ID ) . '">' . get_the_title( $p->ID ) . '</a></span>';

											} else {

												echo '<span>' . $hofundar_separator . get_the_title( $p->ID ) . '</span>';

											}
										} ?>
									<?php endif; ?>
								</td>
								<td>
									<strong><?php echo esc_html( $item_audiobook_code ); ?></strong>
								</td>
							</tr>
							<?php
						}
					}
				}
				?>
				</tbody>
			</table>
			<?php
		} else {
			?>
			<h3><?php esc_html_e( 'No audiobook orders found', 'forlagid' ); ?></h3>
			<?php
		}

	}


}

new Forlagid_Audiobooks();
