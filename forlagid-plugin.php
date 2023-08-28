<?php
/*
Plugin Name: Forlagid Plugin
Description: Site specific code changes for forlagid.is
*/
/* Start Adding Functions Below this Line */


// Register Custom Post Type

function book_post_type() {
	$labels = array(
		'name' => 'Bækur',
		'singular_name' => 'Bók',
		'menu_name' => 'Bækur',
		'parent_item_colon' => 'Parent Book:',
		'all_items' => 'Allar Bækur',
		'view_item' => 'Skoða bók',
		'add_new_item' => 'Bæta við bók',
		'add_new' => 'Ný bók',
		'edit_item' => 'Breyta bók',
		'update_item' => 'Uppfæra bók',
		'search_items' => 'Leita í bókum',
		'not_found' => 'Engar bækur fundust',
		'not_found_in_trash' => 'Engar bókur fundust í ruslakörfu',
	);

	$args = array(
		'label' => 'baekur',
		'description' => 'Upplýsingar um bók',
		'labels' => $labels,
		'supports' => array( 'title', 'thumbnail', ),
		'hierarchical' => true,
		'public' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'show_in_nav_menus' => true,
		'show_in_admin_bar' => true,
		'menu_position' => 5,
		'menu_icon' => 'dashicons-book-alt',
		'can_export' => true,
		'has_archive' => true,
		'exclude_from_search' => false,
		'publicly_queryable' => true,
		'capability_type' => 'page',
	);
	register_post_type( 'baekur', $args );
}
// Hook into the 'init' action
add_action( 'init', 'book_post_type', 0 );


// Register Custom Taxonomy

function book_cat() {
	$labels = array(
		'name' => 'Book Categories',
		'singular_name' => 'Book Category',
		'menu_name' => 'Bókaflokkar',
		'all_items' => 'All Book Categories',
		'parent_item' => 'Parent Book  Category',
		'parent_item_colon' => 'Parent Book Category:',
		'new_item_name' => 'New Book  Category Name',
		'add_new_item' => 'Add New Book Category',
		'edit_item' => 'Edit Book Category',
		'update_item' => 'Update Book  Category',
		'separate_items_with_commas' => 'Separate book categories with commas',
		'search_items' => 'Search book categories',
		'add_or_remove_items' => 'Add or remove book categories',
		'choose_from_most_used' => 'Choose from the most used book categories',
		'not_found' => 'Not Found',
	);

	$rewrite = array(
		'slug' => 'bokaflokkar',
		'with_front' => true,
		'hierarchical' => true,
	);

	$args = array(
		'labels' => $labels,
		'hierarchical' => true,
		'public' => true,
		'show_ui' => true,
		'show_admin_column' => true,
		'show_in_nav_menus' => true,
		'show_tagcloud' => true,
		'query_var' => true,
		'rewrite' => $rewrite,
		//'rewrite'           => array( 'slug' => 'bokaflokkar' ),
	);
	register_taxonomy( 'bokaflokkar', array( 'baekur' ), $args );
}
// Hook into the 'init' action
add_action( 'init', 'book_cat', 0 );


// Register Custom Taxonomy

function book_hofundur() {
	$labels = array(
		'name' => 'Listi yfir höfunda',
		'singular_name' => 'Höfundur',
		'menu_name' => 'Höfundar',
		'all_items' => 'Listi yfir alla höfunda',
		'new_item_name' => 'Nýr höfundur í lista',
		'add_new_item' => 'Bæta nýjum höfundi við lista',
		'edit_item' => 'Breyta nafni höfundar í lista',
		'update_item' => 'Uppfæra upplýsingar',
		'separate_items_with_commas' => 'Settu kommu á milli höfunda',
		'search_items' => 'Leita, í þessum lista, eftir höfundi',
		'add_or_remove_items' => 'Bæta við eða eyða höfundi úr lista',
		'choose_from_most_used' => 'Veldu úr nöfnum mest notuðu höfunda í þessum lista',
		'not_found' => 'Fannst ekki',
	);

	$rewrite = array(
		'slug' => 'hofundurbokar',
		'with_front' => true,
		'hierarchical' => true,
	);

	$args = array(
		'labels' => $labels,
		'hierarchical' => false,
		'public' => true,
		'show_ui' => true,
		'show_admin_column' => true,
		'show_in_nav_menus' => true,
		'show_tagcloud' => true,
		'query_var' => '',
		'rewrite' => $rewrite,
	);
	register_taxonomy( 'hofundurbokar', array( 'baekur' ), $args );
}
// Hook into the 'init' action
add_action( 'init', 'book_hofundur', 0 );

// Register Custom Post Type
function publisher_post_type() {
	$labels = array(
		'name' => 'Publishers',
		'singular_name' => 'Publisher',
		'menu_name' => 'Publishers',
		'parent_item_colon' => 'Parent Publisher:',
		'all_items' => 'All Publishers',
		'view_item' => 'View Publisher',
		'add_new_item' => 'Add New Publisher',
		'add_new' => 'New Publisher',
		'edit_item' => 'Edit Publisher',
		'update_item' => 'Update Publisher',
		'search_items' => 'Search publishers',
		'not_found' => 'No publishers found',
		'not_found_in_trash' => 'No publishers found in Trash',
	);

	$args = array(
		'label' => 'publisher',
		'description' => 'Publisher information pages',
		'labels' => $labels,
		'supports' => array( 'title', 'thumbnail', ),
		'hierarchical' => false,
		'public' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'show_in_nav_menus' => true,
		'show_in_admin_bar' => true,
		'menu_position' => 5,
		'menu_icon' => 'dashicons-groups',
		'can_export' => true,
		'has_archive' => true,
		'exclude_from_search' => false,
		'publicly_queryable' => true,
		'capability_type' => 'page',
	);
	register_post_type( 'publisher', $args );
}
// Hook into the 'init' action
add_action( 'init', 'publisher_post_type', 0 );


// Register Custom Taxonomy
function publisher_cat() {
	$labels = array(
		'name' => 'Publisher Categories',
		'singular_name' => 'Publisher Category',
		'menu_name' => 'Publisher Categories',
		'all_items' => 'All Publishers Categories',
		'parent_item' => 'Parent Publisher Category',
		'parent_item_colon' => 'Parent Publisher Category:',
		'new_item_name' => 'New Publisher Category Name',
		'add_new_item' => 'Add New Publisher Category',
		'edit_item' => 'Edit Publisher Category',
		'update_item' => 'Update Publisher Category',
		'separate_items_with_commas' => 'Separate publisher categories with commas',
		'search_items' => 'Search publisher categories',
		'add_or_remove_items' => 'Add or remove publisher categories',
		'choose_from_most_used' => 'Choose from the most used publisher categories',
		'not_found' => 'Not Found',
	);
	$rewrite = array(
		'slug' => 'publisher-cat',
		'with_front' => true,
		'hierarchical' => true,
	);
	$args = array(
		'labels' => $labels,
		'hierarchical' => true,
		'public' => true,
		'show_ui' => true,
		'show_admin_column' => true,
		'show_in_nav_menus' => true,
		'show_tagcloud' => true,
		'query_var' => '',
		'rewrite' => $rewrite,
	);
	register_taxonomy( 'publisher-cat', array( 'publisher' ), $args );
}
// Hook into the 'init' action
add_action( 'init', 'publisher_cat', 0 );

//hide menu items in admin panel
function remove_menus() {
	remove_menu_page( 'edit.php?post_type=avada_faq' );          //Faq
	remove_menu_page( 'edit.php?post_type=avada_portfolio' );    //Portfolio
}
add_action( 'admin_menu', 'remove_menus' );


//rearrange tabs on single product

add_filter( 'woocommerce_product_tabs', 'sb_woo_move_description_tab', 98 );
function sb_woo_move_description_tab( $tabs ) {
	$tabs['reviews']['priority']                = 1;
	$tabs['additional_information']['priority'] = 5;
	return $tabs;
}

// Hide price range Grafík Panda 31.08.22
//add_filter( 'woocommerce_variable_sale_price_html', 'kg_variation_price_format', 10, 2 );
//add_filter( 'woocommerce_variable_price_html', 'kg_variation_price_format', 10, 2 );

function kg_variation_price_format( $price, $product ) {
// Main Price
	$prices = array( $product->get_variation_price( 'min', true ), $product->get_variation_price( 'max', true ) );
	$price  = $prices[0] !== $prices[1] ? sprintf( __( 'frá %1$s', 'woocommerce' ), wc_price( $prices[0] ) ) : wc_price( $prices[0] );
	return $price;
}

//hide price on product page
//remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );

// Add Variation Settings
add_action( 'woocommerce_product_after_variable_attributes', 'variation_settings_fields', 10, 3 );
// Save Variation Settings
add_action( 'woocommerce_save_product_variation', 'save_variation_settings_fields', 10, 2 );

/**
 * Create new fields for variations
 *
 */
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
			'label' => __( 'ID fyrir Rafbók', 'woocommerce' ),
			'desc_tip' => 'true',
			'description' => __( 'ID fyrir Rafbók', 'woocommerce' ),
			'value' => get_post_meta( $variation->ID, 'epub_uuid', true ),
			'custom_attributes' => array(
				'step' => 'any',
				'min' => '0'
			)
		)
	);/*

	// Textarea

	woocommerce_wp_textarea_input(

		array(

			'id'          => '_textarea[' . $variation->ID . ']',

			'label'       => __( 'My Textarea', 'woocommerce' ),

			'placeholder' => '',

			'description' => __( 'Enter the custom value here.', 'woocommerce' ),

			'value'       => get_post_meta( $variation->ID, '_textarea', true ),

		)

	);



	// Select

	woocommerce_wp_select(

	array(

		'id'          => '_select[' . $variation->ID . ']',

		'label'       => __( 'My Select Field', 'woocommerce' ),

		'description' => __( 'Choose a value.', 'woocommerce' ),

		'value'       => get_post_meta( $variation->ID, '_select', true ),

		'options' => array(

			'one'   => __( 'Option 1', 'woocommerce' ),

			'two'   => __( 'Option 2', 'woocommerce' ),

			'three' => __( 'Option 3', 'woocommerce' )

			)

		)

	);

	// Checkbox

	woocommerce_wp_checkbox(

	array(

		'id'            => '_checkbox[' . $variation->ID . ']',

		'label'         => __('My Checkbox Field', 'woocommerce' ),

		'description'   => __( 'Check me!', 'woocommerce' ),

		'value'         => get_post_meta( $variation->ID, '_checkbox', true ),

		)

	);*/

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

	/*
	// Textarea
	$textarea = $_POST['_textarea'][ $post_id ];
	if( ! empty( $textarea ) ) {
		update_post_meta( $post_id, '_textarea', esc_attr( $textarea ) );
	}
	// Select
	$select = $_POST['_select'][ $post_id ];
	if( ! empty( $select ) ) {
		update_post_meta( $post_id, '_select', esc_attr( $select ) );
	}
	// Checkbox
	$checkbox = isset( $_POST['_checkbox'][ $post_id ] ) ? 'yes' : 'no';
	update_post_meta( $post_id, '_checkbox', $checkbox );
	*/
	// Hidden field
	$hidden = $_POST['_hidden_field'][ $post_id ];
	if ( ! empty( $hidden ) ) {
		update_post_meta( $post_id, '_hidden_field', esc_attr( $hidden ) );
	}
}

//hide Sale badge on products
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );

//Add custom field to woocommerce checkout (kennitala)

// Hook in
//add_filter( 'woocommerce_checkout_fields', 'custom_override_checkout_fields' );

// Our hooked in function - $fields is passed via the filter!
function custom_override_checkout_fields( $fields ) {
	$fields['billing']['billing_kennitala'] = array(
		'label'       => __( 'Kennitala', 'woocommerce' ),
		'placeholder' => _x( 'Kennitala greiðanda', 'placeholder', 'woocommerce' ),
		'required'    => true,
		'class'       => array( 'form-row-wide' ),
		'clear'       => true,
		'maxlength'   => 10,
		'priority' => 20,
	);

	return $fields;
}

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
//add_filter( "woocommerce_checkout_fields", "order_fields" );

//function order_fields( $fields ) {

//	$order = array(
//		"billing_first_name",
//		"billing_last_name",
//		"billing_company",
//		"billing_address_1",
//		"billing_address_2",
//		"billing_postcode",
//		"billing_country",
//		"billing_email",
//		"billing_phone"

//	);
//	foreach ( $order as $field ) {
//		$ordered_fields[ $field ] = $fields["billing"][ $field ];
//	}

//	$fields["billing"] = $ordered_fields;

//	return $fields;

//}

// Rearrange fields in checkout - as of 17.12.18
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

/* Custom by Supriyadi Widodo */

//remove_action( 'avada_header', 'avada_header' );

add_action( 'avada_logo_prepend', 'custom_logo_function', 0 );
add_action( 'wp', 'custom_logo_function', 0 );
function custom_logo_function() {
	if ( is_page( 'forlagid-utgafa' ) || is_page( 34 ) || is_page( 50757 ) || is_page( 'um-utgafuna' ) || is_page( 'leitarnidurstodur' ) || is_page( 'starfsfolk' ) || is_page( 'frettir' ) || is_page( 'fyrirtaekjathjonusta-forlagsins' ) || is_page( 'handritaskil' ) || is_page( '44775' ) || is_home() || is_category() || is_tax( 'category' ) || is_tax( 'bokaflokkar' ) || is_singular( array(
			'post',
			'hofundar',
			'baekur'
		) ) || is_tag() || is_post_type_archive( array( 'hofundar', 'baekur' ) ) ) {
		global $custom_logo;
		$custom_logo = true;
	}
}

add_action( 'avada_logo_prepend', 'custom_avada_before_logo' );
function custom_avada_before_logo( $custom_logo ) {
	global $custom_logo;
	if ( $custom_logo ) {
		echo '<div style="display:none;">';
	}
}

add_filter( 'body_class', 'forlagid_body_class' );
function forlagid_body_class( $classes ) {
	global $custom_logo;
	if ( $custom_logo ) {
		$classes[] = 'forlagid_custom_logo';
	}
	return $classes;
}

add_action( 'avada_logo_append', 'custom_avada_after_logo' );
function custom_avada_after_logo( $custom_logo ) {
	global $custom_logo;
	if ( $custom_logo ) {
		echo '</div>';
		$custom_logo_url = get_field( 'custom_logo_utgafa', 'option' );
		?>
		<a class="fusion-logo-link" href="<?php echo esc_url(  get_bloginfo( 'url' ) ); ?>/forlagid-utgafa">
			<img src="<?php echo esc_url_raw( $custom_logo_url ); ?>" width="115" height="82" style="width:115px; max-height: 82px; height: auto;" srcset="<?php echo esc_attr( $custom_logo_url ); ?>" alt="<?php bloginfo( 'name' ); ?> <?php esc_attr_e( 'Logo', 'Avada' ); ?>" class="fusion-standard-logo" />

			<!-- mobile logo -->
			<img src="<?php echo esc_url_raw( $custom_logo_url ); ?>" width="115" height="82" style="width:115px; max-height: 82px; height: auto;" srcset="<?php echo esc_attr( $custom_logo_url ); ?>" alt="<?php bloginfo( 'name' ); ?> <?php esc_attr_e( 'Logo', 'Avada' ); ?>" class="fusion-mobile-logo" />
		</a>
		<?php
	}
}

add_action( 'avada_logo_append', 'custom_avada_banner_after_logo' );
function custom_avada_banner_after_logo( $custom_logo ) {
	global $custom_logo;
	if ( $custom_logo ) {
		echo get_field( 'top_image_pub', 'option' );
	} else {
		echo get_field( 'top_image_store', 'option' );
	}
}

add_action( 'avada_header', 'custom_do_avada_menu' );
function custom_do_avada_menu() {
	$args['menu'] = 'valmynd-utgafa';
}

add_filter( 'wp_nav_menu_args', 'custom_main_menu_args' );

function custom_main_menu_args( $args ) {
	if ( is_singular( array( 'hofundar' ) ) || is_page( 50757 ) || is_page( 44775 ) || is_post_type_archive( array(
			'bokaflokkar',
			'hofundar',
			'baekur'
		) ) || is_singular( array( 'baekur', 'post' ) ) || is_tax( array( 'hofundar', 'bokaflokkar' ) ) ) {

		if ( get_field( 'custom_main_menu', 'option' ) && get_field( 'custom_main_menu', 'option' ) != 'default' && ( $args['theme_location'] == 'main_navigation' || $args['theme_location'] == 'sticky_navigation' ) ) {
			$menu         = get_field( 'custom_main_menu', 'option' );
			$args['menu'] = $menu;
		}
		/*} else {

            global $post;

            $c_pageID = Avada::c_pageID();

            if ( get_post_meta( $c_pageID, 'pyre_displayed_menu', true ) != '' && get_post_meta( $c_pageID, 'pyre_displayed_menu', true ) != 'default' && ( $args['theme_location'] == 'main_navigation' || $args['theme_location'] == 'sticky_navigation' ) ) {
                $menu = get_post_meta( $c_pageID, 'pyre_displayed_menu', true );
                $args['menu'] = $menu;
            }*/

	}

	return $args;
}


function carousel_book() {
	global $post;
	$args_book = array(
		'post_type' => 'baekur',
	);
	$carousel_category = get_post_meta( $post->ID, 'category_carousel', true );
	if ( ! empty( $carousel_category ) ) {
		foreach ( $carousel_category as $category ) {
			$tax_query[] = array(
				'taxonomy' => 'bokaflokkar',
				'terms'    => $category,
			);
		}
		$args_book['tax_query'] = $tax_query;
	}
	$query_book = new WP_Query( $args_book );
	if ( $query_book->have_posts() ) {
		$content = '<div class="fusion-woo-product-slider fusion-woo-slider">
            <div class="fusion-carousel fusion-carousel-title-below-image" data-metacontent="yes" data-autoplay="no" data-columns="6" data-itemmargin="13" data-itemwidth="180" data-touchscroll="no" data-imagesize="auto">
            <div class="fusion-carousel-positioner">
            <ul class="fusion-carousel-holder">';
		while ( $query_book->have_posts() ) {
			$query_book->the_post();
			$prmlnk   = get_permalink();
			$linkbook = str_replace( 'baekur', 'vara', $prmlnk );

			//$content .= '<li>' . get_the_title() . '</li>';
			$content .= '<li class="fusion-carousel-item">
                <div class="fusion-clean-product-image-wrapper"><div class="fusion-carousel-item-wrapper">
                <div class="fusion-image-wrapper" aria-haspopup="true">
                <a href="' . get_the_permalink() . '">' . get_the_post_thumbnail( null, 'shop_catalog' ) . '</a>
                </div>
                <h4 class="fusion-carousel-title">
                    <a href="' . get_the_permalink() . '" target="_self">' . get_the_title() . '</a>
                    <span><a href="' . $linkbook . '" class="book-button2"><span class="book-button-text1"></span></a></span>
                </h4>
                </div></li>';
		}
		$content .= '</ul>
            <div class="fusion-carousel-nav">
                <span class="fusion-nav-prev"></span>
                <span class="fusion-nav-next"></span>
            </div>
            </div></div></div>';
	} else {
		// no posts found
	}
	/* Restore original Post Data */
	wp_reset_postdata();
	return $content;
}

// no longer used button
/*  <div class="fusion-carousel-meta"><div class="fusion-carousel-price">
<div class="fusion-book" style="text-align:center;"><p><a href="'.$linkbook.'" class="book-button"><span class="book-button-text">Bókabúð</span></a></p><div style="clear:both;"></div></div>
</div></div>*/


/* einarornth begin

//þetta fall breytir bókabúðarhlekknum ef verið er að skoða ákveðna bók
//og vísar þá á rétta bók í bókabúðarhlutanum
function change_bookstore_link_eot( $atts, $item, $args )
{
	$menu_items = array(50);
	if (in_array($item->ID, $menu_items))
	{
	  //print_r($atts);
	  $cur_page = get_the_permalink();
	  if (strpos($cur_page, 'baekur') == FALSE)
	  {
	  	//óbreytt slóð. Hér má bæta við ef við viljum hafa aðra custom slóð
	  }
	  else
	  {
	  	//breytum slóðinni til að vísa á bókina sem verið er að skoða
	  	$atts['href'] = str_replace('baekur', 'vara', $cur_page);
	  }
	}

    return $atts;
}
add_filter( 'nav_menu_link_attributes', 'change_bookstore_link_eot', 10, 3 );


/* einarornth end */

//Breyta úr Kr í kr
function icelandic_currency_symbol( $currency_symbol, $currency ) {
	switch ( $currency ) {
		case 'ISK':
			$currency_symbol = 'kr.';
			break;
	}

	return $currency_symbol;
}
add_filter( 'woocommerce_currency_symbol', 'icelandic_currency_symbol', 30, 2 );

function description_hofundur_excerpt() {
	global $post;
	$text = get_field( 'description_author' ); //Replace 'your_field_name'
	if ( '' != $text ) {
		$text           = strip_shortcodes( $text );
		$text           = apply_filters( 'the_content', $text );
		$text           = str_replace( ']]&gt;', ']]&gt;', $text );
		$permalink      = get_permalink( $post->ID );
		$excerpt_length = 40; // 40 words
		$excerpt_more   = apply_filters( 'excerpt_more', ' ' . '<br /><a href="' . $permalink . '" rel="nofollow">Nánar um höfund</a>' );
		$text           = wp_trim_words( $text, $excerpt_length, $excerpt_more );
	}
	return apply_filters( 'the_excerpt', $text );
}

function description_baekur_excerpt() {
	global $post;
	$text = get_field( 'description_author' ); //Replace 'your_field_name'
	if ( '' != $text ) {
		$text           = strip_shortcodes( $text );
		$text           = apply_filters( 'the_content', $text );
		$text           = str_replace( ']]&gt;', ']]&gt;', $text );
		$permalink      = get_permalink( $post->ID );
		$excerpt_length = 15; // 15 words
		$excerpt_more   = apply_filters( 'excerpt_more', ' ' . '<br /><a href="' . $permalink . '" rel="nofollow">Meira um bók</a>' );
		$text           = wp_trim_words( $text, $excerpt_length, $excerpt_more );
	}
	return apply_filters( 'the_excerpt', $text );
}


//remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
//add_action( 'woocommerce_sidebar', 'yourtheme_get_sidebar', 10 );

function yourtheme_get_sidebar() {
	wc_get_template( 'global/sidebar.php' );
}
// Grafík panda 31.08.22
//add_filter( 'woocommerce_get_price_html', 'bbloomer_price_prefix_suffix', 100, 2 );
/**
 * @param string $price
 * @param WC_Product $product
 *
 * @return string
 */
/**function bbloomer_price_prefix_suffix( $price, $product ) {
	if ( false === strpos( $price, 'Frá' ) ) {
		$price = 'Verð ' . $price;
	}
	return apply_filters( 'woocommerce_get_price', $price );
}
**/

remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );

add_action( 'woocommerce_after_single_product_summary', 'custom_book_author', 15 );
function custom_book_author() {
	list_book_author();
	//echo carousel_book_author();
	//echo do_shortcode('[wpb-latest-product title="Latest Product"]');
}

// MN: 2020.10.12,  hætti að nota wordpress+sql, not forlagidsearch í staðinn.
function list_book_author() {
  ?>
  <div id="forlagidsearch_datalab"></div>
  <script>
    $(document).ready(() => {
      FS.getDatalab(<?php echo get_the_ID(); ?>);
    });
  </script>
  <?php
}
/*function list_book_author_NOTUSED_02() {
  ?>
  <div id="forlagidsearch_bySameAuthor"></div>
  <script>
    $(document).ready(() => {
      FS.getSameAuthorsBooksOverHttp(<?php echo get_the_ID(); ?>);
    });
  </script>
  <?php
}*/
/*function list_book_author_NOTUSED_01() {
	$authors_taxonomy = wp_get_post_terms( get_the_ID(), 'hofundar', array(
		'fields' => 'ids',
	) );
	if ( empty( $authors_taxonomy ) ) {
		return false;
	}
	$authors_count = count( $authors_taxonomy );
	$args = array(
		'post_type'      => 'product',
		'posts_per_page' => 100,
		'post__not_in'   => array( get_the_ID() ),
		'orderby'        => 'rand',
		'tax_query'      => array(
			array(
				'taxonomy' => 'hofundar',
				'terms'    => $authors_taxonomy,
			),
		),
	);
	$loop = new WP_Query( $args );
	if ( $loop->have_posts() ) { ?>
		<div class="book-by-author clearfix">
		<div class="fusion-title fusion-title-size-three sep-double" style="">
			<h3 class="heading-fleiri-baekur"><?php echo _n( 'Eftir sama höfund', 'Eftir sömu höfunda', $authors_count ); ?></h3>
			<div class="title-sep-container">
				<div class="title-sep sep-double"></div>
			</div>
		</div>
<!--<?php var_dump($authors_taxonomy); ?>-->
		<ul class="products clearfix products-6">
		<?php
		while ( $loop->have_posts() ) : $loop->the_post();
			wc_get_template_part( 'content', 'product' );
		endwhile;
	}
	wp_reset_postdata();
	?>
	</ul>
	</div>
	<div id="forlagidsearch_bySameAuthor"></div>
	<script>
	  $(document).ready(() => {
	    S.getSameAuthorsBooksOverHttp(<?php echo get_the_ID(); ?>);
	  });
	</script>
	<?php
}*/

add_action( 'wp_enqueue_scripts', 'my_styles_method' );
function my_styles_method() {
	wp_enqueue_style(
		'custom-style',
		get_bloginfo( 'url' ) . '/wp-content/plugins/forlagid-plugin/owl-carousel/owl.carousel.css'
	);
	wp_enqueue_style(
		'custom-owl-style',
		get_bloginfo( 'url' ) . '/wp-content/plugins/forlagid-plugin/owl-carousel/owl.theme.css'
	);
	/*wp_enqueue_style(
      'custom-slick-style',
      get_bloginfo('url') . '/wp-content/plugins/forlagid-plugin/slick/slick.css'
    );

    wp_enqueue_style(
      'custom-style',
      get_bloginfo('url') . '/wp-content/plugins/forlagid-plugin/slick/slick-theme.css'
    );*/

	wp_enqueue_style(
		'custom-grid-style',
		get_bloginfo( 'url' ) . '/wp-content/plugins/forlagid-plugin/gridism.css'
	);
	wp_register_script( 'owl-js', get_bloginfo( 'url' ) . '/wp-content/plugins/forlagid-plugin/owl-carousel/jquery-1.9.1.min.js', array( 'jquery' ), '1', false );
	wp_register_script( 'owl-carousel-js', get_bloginfo( 'url' ) . '/wp-content/plugins/forlagid-plugin/owl-carousel/owl.carousel.js', array( 'jquery' ), '1', false );
	wp_register_script( 'slick-js', '//code.jquery.com/jquery-2.2.0.min.js', array( 'jquery' ), '1', false );
	wp_register_script( 'slick-carousel-js', '//cdn.jsdelivr.net/jquery.slick/1.6.0/slick.min.js', array( 'jquery' ), '1', false );
	wp_enqueue_script( 'owl-js' );
	wp_enqueue_script( 'owl-carousel-js' );
	//wp_enqueue_script('slick-carousel-js');
}


/* Pagination */
function kriesi_pagination( $pages = '', $range = 2 ) {
	$showitems = ( $range * 2 ) + 1;
	global $paged;
	if ( empty( $paged ) ) {
		$paged = 1;
	}
	if ( $pages == '' ) {
		global $wp_query;
		$pages = $wp_query->max_num_pages;
		if ( ! $pages ) {
			$pages = 1;
		}
	}

	if ( 1 != $pages ) {
		echo "<div class='pagination'>";
		if ( $paged > 2 && $paged > $range + 1 && $showitems < $pages ) {
			echo "<a href='" . get_pagenum_link( 1 ) . "'>&laquo;</a>";
		}
		if ( $paged > 1 && $showitems < $pages ) {
			echo "<a href='" . get_pagenum_link( $paged - 1 ) . "'>&lsaquo;</a>";
		}

		for ( $i = 1; $i <= $pages; $i ++ ) {
			if ( 1 != $pages && ( ! ( $i >= $paged + $range + 1 || $i <= $paged - $range - 1 ) || $pages <= $showitems ) ) {
				echo ( $paged == $i ) ? "<span class='current'>" . $i . "</span>" : "<a href='" . get_pagenum_link( $i ) . "' class='inactive' >" . $i . "</a>";
			}
		}

		if ( $paged < $pages && $showitems < $pages ) {
			echo "<a href='" . get_pagenum_link( $paged + 1 ) . "'>&rsaquo;</a>";
		}
		if ( $paged < $pages - 1 && $paged + $range - 1 < $pages && $showitems < $pages ) {
			echo "<a href='" . get_pagenum_link( $pages ) . "'>&raquo;</a>";
		}
		echo "</div>\n";
	}
}


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


/***Checkout customizations****/

/* Grafík Panda - 7. des 2021
add_filter( 'woocommerce_checkout_fields', 'yourplugin_move_checkout_fields' );
function yourplugin_move_checkout_fields( $fields ) {
	// Author: apppresser.com
	// Move these around as necessary. You'll see we added email first.
	$billing_order = array(
		"billing_first_name",
		"billing_last_name",
		"billing_company",
		"billing_kennitala",
		"billing_address_1",
		"billing_address_2",
		"billing_postcode",
		"billing_city",
		"billing_country",
		"billing_email",
		"billing_em_ver",
		"billing_phone"
	);

	// This sets the billing fields in the order above
	foreach ( $billing_order as $billing_field ) {
		$billing_fields[ $billing_field ] = $fields["billing"][ $billing_field ];
	}

	$fields["billing"] = $billing_fields;

	// Move these around as necessary
	$shipping_order = array(
		"shipping_first_name",
		"shipping_last_name",
		"shipping_company",
		"shipping_address_1",
		"shipping_address_2",
		"shipping_postcode",
		"shipping_city",
		"shipping_country",
		"shipping_phone",

	);

	// This sets the shipping fields in the order above
	foreach ( $shipping_order as $shipping_field ) {
		if ( isset( $fields["shipping"][ $shipping_field ] ) ) {
			$shipping_fields[ $shipping_field ] = $fields["shipping"][ $shipping_field ];
		}
	}

	$fields["shipping"] = $shipping_fields;

	return $fields;
}


add_filter( 'woocommerce_checkout_fields', 'custom_edit_checkout_fields' );

function custom_edit_checkout_fields( $fields ) {
	// Author: apppresser.com

	// Change labels
	$fields['billing']['billing_first_name']['label'] = 'Fornafn<span class="english-checkout"> - First name</span>';
	$fields['billing']['billing_last_name']['label']  = 'Eftirnafn<span class="english-checkout"> - Last name</span>';
	$fields['billing']['billing_company']['label']    = 'Fyrirtæki <span class="english-checkout"> - Company</span>';
	$fields['billing']['billing_address_1']['label']  = 'Heimilisfang<span class="english-checkout"> - Address</span>';
	$fields['billing']['billing_postcode']['label']   = 'Póstnúmer<span class="english-checkout"> - Zipcode</span>';
	$fields['billing']['billing_city']['label']       = 'Borg/bær<span class="english-checkout"> - Town/city</span>';
	$fields['billing']['billing_country']['label']    = 'Land<span class="english-checkout"> - Country</span>';
	$fields['billing']['billing_email']['label']      = 'Netfang<span class="english-checkout"> - E-mail</span>';
	$fields['billing']['billing_phone']['label']      = 'Símanúmer<span class="english-checkout"> - Phone number</span>';


	$fields['shipping']['shipping_first_name']['label'] = 'Fornafn<span class="english-checkout"> - First name</span>';
	$fields['shipping']['shipping_last_name']['label']  = 'Eftirnafn<span class="english-checkout"> - Last name</span>';
	$fields['shipping']['shipping_company']['label']    = 'Fyrirtæki<span class="english-checkout"> - Company</span>';
	$fields['shipping']['shipping_address_1']['label']  = 'Heimilisfang<span class="english-checkout"> - Address</span>';
	$fields['shipping']['shipping_city']['label']       = 'Borg/bær<span class="english-checkout"> - City</span>';
	$fields['shipping']['shipping_postcode']['label']   = 'Póstnúmer<span class="english-checkout"> - Zipcode</span>';
	$fields['shipping']['shipping_country']['label']    = 'Land<span class="english-checkout"> - Country</span>';
	$fields['shipping']['shipping_phone']['label']      = 'Símanúmer<span class="english-checkout"> - Phone number</span>';

	$fields['shipping_method']['table_rate:5:5']['label'] = 'Símanúmer<span class="english-checkout"> - Phone number</span>';
	return $fields;
}
*/

/**
 * Filter payment gatways
 */
 
function forlagid_available_payment_gateways( $gateways ) {

	if ( is_admin() || is_null( WC()->session ) ) {
		return $gateways;
	}
	$chosen_shipping_rates = WC()->session->get( 'chosen_shipping_methods' );

	if ( !is_array( $chosen_shipping_rates ) ) {
		return $gateways;
	}

	// Hide Valitor and Millifærsla when Póstkrafa table rate is chosen
	if ( in_array( 'table_rate:6:8', $chosen_shipping_rates ) ) :

		// Remove bank transfer payment gateway
		unset( $gateways['bacs'] );
		unset( $gateways['valitor'] );
		unset( $gateways['borgun'] );
		unset( $gateways['netgiro'] );


	endif;

	// Hide Valitor and Millifærsla when Póstkrafa table rate is chosen
	if ( in_array( 'table_rate:6:7', $chosen_shipping_rates ) ) :

		// Remove bank transfer payment gateway
		unset( $gateways['bacs'] );
		unset( $gateways['valitor'] );
		unset( $gateways['borgun'] );
		unset( $gateways['netgiro'] );


	endif;
	// Hide Valitor and Millifærsla when Póstkrafa table rate is chosen
	if ( in_array( 'table_rate:6:9', $chosen_shipping_rates ) ) :

		// Remove bank transfer payment gateway
		unset( $gateways['bacs'] );
		unset( $gateways['valitor'] );
		unset( $gateways['borgun'] );
		unset( $gateways['netgiro'] );


	endif;

	// Hide Valitor and Millifærsla when Póstkrafa table rate is chosen
	if ( in_array( 'table_rate:15:28', $chosen_shipping_rates ) ) :

		// Remove bank transfer payment gateway
		unset( $gateways['bacs'] );
		unset( $gateways['valitor'] );
		unset( $gateways['borgun'] );
		unset( $gateways['netgiro'] );


	endif;


// Hide Valitor and Millifærsla when Póstkrafa table rate is chosen
	if ( in_array( 'table_rate:15:29', $chosen_shipping_rates ) ) :

		// Remove bank transfer payment gateway
		unset( $gateways['bacs'] );
		unset( $gateways['valitor'] );
		unset( $gateways['borgun'] );
		unset( $gateways['netgiro'] );


	endif;


	// Hide Valitor and Millifærsla when Póstkrafa table rate is chosen
	if ( in_array( 'table_rate:15:30', $chosen_shipping_rates ) ) :

		// Remove bank transfer payment gateway
		unset( $gateways['bacs'] );
		unset( $gateways['valitor'] );
		unset( $gateways['borgun'] );
		unset( $gateways['netgiro'] );


	endif;


// Hide Póstkrafa when Póstsending, Sótt í verslun and Flýtiþjónusta are chosen
	if ( in_array( 'local_pickup:3', $chosen_shipping_rates ) ) :

		// Remove bank transfer payment gateway
		unset( $gateways['cod'] );


	endif;


	// Hide Póstkrafa when Póstsending, Sótt í verslun and Flýtiþjónusta are chosen
	if ( in_array( 'local_pickup:14', $chosen_shipping_rates ) ) :

		// Remove bank transfer payment gateway
		unset( $gateways['cod'] );


	endif;


	// Hide Póstkrafa when Póstsending, Sótt í verslun and Flýtiþjónusta are chosen
	if ( in_array( 'table_rate:4:3', $chosen_shipping_rates ) ) :

		// Remove bank transfer payment gateway
		unset( $gateways['cod'] );


	endif;

// Hide Póstkrafa when Póstsending, Sótt í verslun and Flýtiþjónusta are chosen
	if ( in_array( 'table_rate:4:1', $chosen_shipping_rates ) ) :

		// Remove bank transfer payment gateway
		unset( $gateways['cod'] );


	endif;

// Hide Póstkrafa when Póstsending, Sótt í verslun and Flýtiþjónusta are chosen
	if ( in_array( 'table_rate:4:2', $chosen_shipping_rates ) ) :

		// Remove bank transfer payment gateway
		unset( $gateways['cod'] );


	endif;

// Hide Póstkrafa when Póstsending, Sótt í verslun and Flýtiþjónusta are chosen
	if ( in_array( 'table_rate:5:4', $chosen_shipping_rates ) ) :

		// Remove bank transfer payment gateway
		unset( $gateways['cod'] );


	endif;

// Hide Póstkrafa when Póstsending, Sótt í verslun and Flýtiþjónusta are chosen
	if ( in_array( 'table_rate:5:5', $chosen_shipping_rates ) ) :

		// Remove bank transfer payment gateway
		unset( $gateways['cod'] );


	endif;

// Hide Póstkrafa when Póstsending, Sótt í verslun and Flýtiþjónusta are chosen
	if ( in_array( 'table_rate:5:6', $chosen_shipping_rates ) ) :

		// Remove bank transfer payment gateway
		unset( $gateways['cod'] );


	endif;

	// Hide Póstkrafa when Póstsending, Sótt í verslun and Flýtiþjónusta are chosen
	if ( in_array( 'table_rate:13:27', $chosen_shipping_rates ) ) :

		// Remove bank transfer payment gateway
		unset( $gateways['cod'] );


	endif;

// Hide Póstkrafa when Póstsending, Sótt í verslun and Flýtiþjónusta are chosen
	if ( in_array( 'table_rate:13:26', $chosen_shipping_rates ) ) :

		// Remove bank transfer payment gateway
		unset( $gateways['cod'] );


	endif;

// Hide Póstkrafa when Póstsending, Sótt í verslun and Flýtiþjónusta are chosen
	if ( in_array( 'table_rate:13:25', $chosen_shipping_rates ) ) :

		// Remove bank transfer payment gateway
		unset( $gateways['cod'] );


	endif;

	// Hide Póstkrafa when Póstsending, Sótt í verslun and Flýtiþjónusta are chosen
	if ( in_array( 'table_rate:12:24', $chosen_shipping_rates ) ) :

		// Remove bank transfer payment gateway
		unset( $gateways['cod'] );


	endif;

	// Hide Póstkrafa when Póstsending, Sótt í verslun and Flýtiþjónusta are chosen
	if ( in_array( 'table_rate:12:22', $chosen_shipping_rates ) ) :

		// Remove bank transfer payment gateway
		unset( $gateways['cod'] );
		unset( $gateways['bacs'] );


	endif;

	// Hide Póstkrafa when Póstsending, Sótt í verslun and Flýtiþjónusta are chosen
	if ( in_array( 'table_rate:12:23', $chosen_shipping_rates ) ) :

		// Remove bank transfer payment gateway
		unset( $gateways['cod'] );


	endif;
	
	// Hide Póstkrafa when Free shipping is chosen
	if ( in_array( 'free_shipping:18', $chosen_shipping_rates ) ) :

		// Remove bank transfer payment gateway
		unset( $gateways['cod'] );


	endif;


	// Hide Póstkrafa when Rest of Europe is chosen
	if ( in_array( 'table_rate:11:16', $chosen_shipping_rates ) ) :

		// Remove bank transfer payment gateway
		unset( $gateways['cod'] );


	endif;

	// Hide Póstkrafa when Rest of Europe is chosen
	if ( in_array( 'table_rate:11:17', $chosen_shipping_rates ) ) :

		// Remove bank transfer payment gateway
		unset( $gateways['cod'] );


	endif;

	// Hide Póstkrafa when Rest of Europe is chosen
	if ( in_array( 'table_rate:11:18', $chosen_shipping_rates ) ) :

		// Remove bank transfer payment gateway
		unset( $gateways['cod'] );


	endif;
	// Hide Póstkrafa when Denmark is chosen
	if ( in_array( 'table_rate:8:15', $chosen_shipping_rates ) ) :

		// Remove bank transfer payment gateway
		unset( $gateways['cod'] );


	endif;

	// Hide Póstkrafa when Denmark is chosen
	if ( in_array( 'table_rate:8:14', $chosen_shipping_rates ) ) :

		// Remove bank transfer payment gateway
		unset( $gateways['cod'] );


	endif;

	// Hide Póstkrafa when Denmark is chosen
	if ( in_array( 'table_rate:8:13', $chosen_shipping_rates ) ) :

		// Remove bank transfer payment gateway
		unset( $gateways['cod'] );


	endif;

	// Hide Póstkrafa when USA is chosen
	if ( in_array( 'table_rate:7:10', $chosen_shipping_rates ) ) :

		// Remove bank transfer payment gateway
		unset( $gateways['cod'] );


	endif;

	// Hide Póstkrafa when USA is chosen
	if ( in_array( 'table_rate:7:11', $chosen_shipping_rates ) ) :

		// Remove bank transfer payment gateway
		unset( $gateways['cod'] );


	endif;

	// Hide Póstkrafa when USA is chosen
	if ( in_array( 'table_rate:7:12', $chosen_shipping_rates ) ) :

		// Remove bank transfer payment gateway
		unset( $gateways['cod'] );


	endif;

	// Hide Póstkrafa when Rest of world is chosen
	if ( in_array( 'table_rate:16:32', $chosen_shipping_rates ) ) :

		// Remove bank transfer payment gateway
		unset( $gateways['cod'] );


	endif;

	// Hide Póstkrafa when Rest of world is chosen
	if ( in_array( 'table_rate:16:31', $chosen_shipping_rates ) ) :

		// Remove bank transfer payment gateway
		unset( $gateways['cod'] );


	endif;

	// Hide Póstkrafa when Rest of world is chosen
	if ( in_array( 'table_rate:16:33', $chosen_shipping_rates ) ) :

		// Remove bank transfer payment gateway
		unset( $gateways['cod'] );


	endif;


	return $gateways;

}

add_filter( 'woocommerce_available_payment_gateways', 'forlagid_available_payment_gateways', 20 );


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



/***** Viðbætur við tölvupóstsendingar úr kerfinu***/
// changed 271117 add_action( 'woocommerce_email_before_order_table', 'wdm_add_shipping_method_to_order_email', 10, 2 );

/**
 * @param $order WC_Order
 * @param $sent_to_admin
 */
// changed 271117 function wdm_add_shipping_method_to_order_email( $order, $sent_to_admin ) {

//	$order_items = $order->get_items();
//
//	$virtual_products = 0;
//
//	foreach($order_items as $item) {
//
//		if ( isset($item['variation_id'])  ) {
//			$product = new WC_Product_Variation($item['variation_id']);
//		} else {
//			$product = wc_get_product($item['product_id']);
//		}
//		if ( $product->is_virtual() ) {
//			$virtual_products++;
//		}
//
//	}

	// changed 271117 if ( $sent_to_admin ) {
	// changed 271117 	return;
	// changed 271117 }
	// changed 271117 echo "<p>Hafir þú keypt hefðbundna bók berst hún þér í pósti innan þriggja virkra daga. </p>";

// changed 271117 }

add_filter( 'woocommerce_thankyou_virtual', 'wdm_checkout_thankyou_virtual', 15 );

/**
 * @param $order WC_Order
 */
function wdm_checkout_thankyou_virtual( $order ) {

	$order_items = $order->get_items();

	$virtual_products = 0;

	foreach ( $order_items as $item ) {

		if ( isset( $item['variation_id'] ) ) {
			$product = new WC_Product_Variation( $item['variation_id'] );
		} else {
			$product = wc_get_product( $item['product_id'] );
		}
		if ( $product->is_virtual() ) {
			$virtual_products ++;
		}

	}

	if ( $virtual_products > 0 && ! $order->has_status( 'failed' ) ) {

		echo "<p>Þú færð brátt tölvupóst frá okkur með hlekk sem vísar þér á vöruna þína.</p>";
	}


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


function payment_gateway_disable_country( $available_gateways ) {

	if ( is_null( WC()->customer ) ) {
		return $available_gateways;
	}

	if ( ! is_admin() && isset( $available_gateways['bacs'] ) && WC()->customer && WC()->customer->get_billing_country() != 'IS' ) {
		unset( $available_gateways['bacs'] );
	}

	return $available_gateways;
}

add_filter( 'woocommerce_available_payment_gateways', 'payment_gateway_disable_country' );


/**
 * @param $order WC_Order
 */

function forlagid_add_ebook_link( $order ) {

	$items_rb = $order->get_items();
	foreach ( $items_rb as $item ) {
		/**
		 * @var WC_Order_Item $item
		 */
		if ( 'rafbok' === $item->get_meta( 'pa_gerd' ) ) {
			$link_error = false;
			$epub_rafbokar = get_post_meta( $item['variation_id'], 'epub_uuid', true );
			$rafbok_link   = forlagid_get_ebook_link( $epub_rafbokar, $order->get_id(), 'd5a88916-da1b-11e5-a852-002354962104' );
			$response       = json_decode( wp_remote_retrieve_body( $rafbok_link ) );
			if ( isset( $response->download_link ) ) {
				$rafbok_link = $response->download_link;
			} else {
				$link_error = forlagid_epub_handle_error( $rafbok_link, $order->get_id() );
			}
			echo'<h3>Hér er rafbókin þín! Smelltu á hlekkinn „Sækja rafbók“ hér fyrir neðan til að nálgast eintakið þitt</h3> <br/>';
			echo 'Hlekkur þessi gildir í 7 daga – að þeim tíma liðnum verður hann óvirkur.
Eintak rafbókarinnar er merkt þér með stafrænni vatnsmerkingu og rekjanlegt til þín.  Dreifing skrárinnar eða efnis hennar er með öllu óheimil. <br/>';
			echo '<a href="http://www-01.forlagid.is/spurt-og-svarad-um-rafbaekur">Sjá nánar um kaup á rafbókum</a><br/><br/>';
			if ( $link_error ) {
				echo '<b>' . $link_error . '</b>';
			} else {
				echo '<h3><a href="' . $rafbok_link . '"><strong>Sækja rafbók: ' . $item['name'] . '</strong></a></h3><br/><br/>';
			}
		}
	}
}
add_action( 'woocommerce_email_after_order_table', 'forlagid_add_ebook_link', 11 );

//Grafík Panda - Email um Hljóðbók

function forlagid_add_audiobook_link( $order ) {

	$items_rb = $order->get_items();
	foreach ( $items_rb as $item ) {
		/**
		 * @var WC_Order_Item $item
		 */
		if ( 'hljodbok' === $item->get_meta( 'pa_gerd' ) ) {
			echo'<h3>Streymis-hljóðbókin þín er nú aðgengileg í <a href="https://forlagid.is/hlusta">appinu</a> eða <a href="https://hlusta.forlagid.is">vafra.</a></h3> <br/>';
			echo 'Athugaðu að þú skráir þig inn þar með sömu notendaupplýsingum og á Forlagsvefnum.<br/>';
		}
	}
}
add_action( 'woocommerce_email_before_order_table', 'forlagid_add_audiobook_link', 11 );




//add_action( 'forlagid_ebook_link', 'forlagid_add_ebook_link', 11 );

/* REMOVED BY KRISTIN ON 12.07.18 - request from Forlagid to not have the text display in email notification
function forlagid_ebook_before_id( $order, $sent_to_admin, $plain_text, $email ) {
	$items_rb = $order->get_items();
	foreach ( $items_rb as $item ) {
		/**
		 * @var WC_Order_Item $item
		 */
		/*if ( 'rafbok' === $item->get_meta( 'pa_gerd' ) ) {
			?>
			<h3>Hér er rafbókin þín! Smelltu á hlekkinn „Sækja rafbók“ hér fyrir neðan til að nálgast eintakið þitt</h3>
			<?php
		}
	}
}


add_action( 'woocommerce_email_before_order_table', 'forlagid_ebook_before_id', 15, 4 );
*/

function forlagid_add_thankyou_ebook_link( $order ) {

	$items_rb = $order->get_items();
	foreach ( $items_rb as $item ) {
		/**
		 * @var WC_Order_Item $item
		 */
		if ( 'rafbok' === $item->get_meta( 'pa_gerd' ) ) {

			?>
			<p>Hlekkur þessi gildir í 7 daga – að þeim tíma liðnum verður hann óvirkur. Eintak rafbókarinnar ermerkt þér með stafrænni vatnsmerkingu og rekjanlegt til þín. Dreifing skrárinnar eða efnis hennar ermeð öllu óheimil.</p>
			<p>Þú færð einnig tölvupóst frá okkur með þessum upplýsingum.</p>
			<?php
		}
	}
}

add_action( 'woocommerce_thankyou_virtual', 'forlagid_add_thankyou_ebook_link', 11 );

function forlagid_get_ebook_link( $epub_uuid, $store_order_id, $store_secret ) {

	$store_id       = urlencode( "forlagid" );
	$store_secret   = urlencode( $store_secret );
	$epub_uuid      = urlencode( $epub_uuid );
	$store_order_id = urlencode( $store_order_id );
	$store_epoch    = urlencode( time() );
	$parameters     = "store_id=$store_id&epub_uuid=$epub_uuid&store_order_id=$store_order_id&store_epoch=$store_epoch";
	$auth           = urlencode( base64_encode( hash_hmac( 'sha256', $parameters, $store_secret, true ) ) );
	$parameters     = "$parameters&auth=$auth";
	$request        = wp_remote_get( "http://epub.is/api/get_link?$parameters" );

	return $request;
	$response       = json_decode( wp_remote_retrieve_body( $request ) );
	if ( isset( $response->download_link ) ) {
		$link = $response->download_link;
	} else {
		return wp_remote_retrieve_body( $request );
	}

	return $link;
}

/**
 * @param array $response
 * @param int $order_id
 *
 * @return bool|string
 */
function forlagid_epub_handle_error( $response, $order_id = 0 ) {

	$error = wp_remote_retrieve_body( $response );
	$error = trim( $error );

	$message = false;
	if ( 'ERROR_NO_STORE' === $error ) {
		$message = 'Auðkenni verslunar rangt eða ekki til staðar';
	}
	if ( 'ERROR_NOT_STORE' === $error ) {
		$message = 'Verslun/fyrirtæki er ekki verslun';
	}
	if ( 'ERROR_NO_SECRET' === $error ) {
		$message = 'Verslun vantar leyndarmál';
	}
	if ( 'ERROR_EPUB_NOT_FOR_SALE' === $error ) {
		$message = 'Rafbók ekki til sölu';
	}
	if ( 'ERROR_EPUB_UUID_INVALID' === $error ) {
		$message = 'Auðkenni gallað';
	}
	if ( 'ERROR_EPUB_DOES_NOT_EXISTS' === $error ) {
		$message = 'Auðkenni verks er ekki til';
	}
	if ( 'ERROR_PARAM' === $error ) {
		$message = 'Færibreytu vantar í fyrirspurn';
	}
	if ( 'ERROR_EPOCH' === $error ) {
		$message = 'Tími ekki réttur. Tíminn sem er gefinn upp í epoch færibreytunni má hvorki vera 10 mínútur of fljótur eða seinn miðað við tíman á vefþjóni epub.is';
	}
	if ( 'ERROR_PARAMETERS_DO_NOT_MATCH_AUTH' === $error ) {
		$message = 'Villa í auth streng';
	}
	if ( empty( $error ) ) {
		$message = 'The response from the server was empty';
	}

	if ( $message ) {
		$message = 'Rafbókin þín barst ekki. <br /> Vinsamlega hafðu samband við okkur og segðu okkur að villan "' . $message . '", hafi komið upp.';
	}

	if ( 0 !== $order_id && $message ) {

		// Send admin email
		$notification_sent = get_post_meta( $order_id, 'forlagid_error_notification_sent', true );
		if ( empty( $notification_sent ) ) {
			$to         = get_option( 'admin_email' );
			$email_text = "<p>Hæ!</p>
<p>Það kom upp villa við afhendingu rafbókar í pöntun númer: <b>{$order_id}</b>.</p> 
<p>Villan er eftirfarandi: <b>{$message}</b>.</p><br />
<p>Server response code: <b>" . wp_remote_retrieve_response_code( $response ) . "</b></p><br />
<p>Kveðja</p><p>Vefurinn.</p>";
			$headers    = array(
				'Content-Type: text/html; charset=UTF-8',
				'Cc: marino@snara.is',
				'Cc: kristin@dottirwebdesign.is',
				'Bcc: mirceas17@gmail.com',
			);

			wp_mail( $to, 'Villa við afhendingu á rafbók', $email_text, $headers );
			add_post_meta( $order_id, 'forlagid_error_notification_sent', 1 );
		}
	}

	return $message;

}


/**
 * Hide Póstkrafa í Flýtiþjónusta when free shipping is available
 */

add_filter( 'woocommerce_package_rates', 'hide_shipping_when_free_flyti', 10, 2 );

function hide_shipping_when_free_flyti( $rates, $package ) {


	if ( isset( $rates['free_shipping:31'] ) ) {
		unset( $rates['table_rate:13:25'] );
		unset( $rates['table_rate:13:26'] );
		unset( $rates['table_rate:13:27'] );
		unset( $rates['table_rate:12:22'] );
		unset( $rates['table_rate:12:23'] );
		unset( $rates['table_rate:12:24'] );


	}

	return $rates;

}


/**
 * Hide Póstkrafa when free shipping is available
 */

add_filter( 'woocommerce_package_rates', 'hide_shipping_when_free', 10, 2 );

function hide_shipping_when_free( $rates, $package ) {


	if ( isset( $rates['free_shipping:32'] ) ) {

		unset( $rates['table_rate:4:1'] );
		unset( $rates['table_rate:4:2'] );
		unset( $rates['table_rate:4:3'] );


	}

	return $rates;

}

/**
 * Hide other shipping when free shipping is available
 */

add_filter( 'woocommerce_package_rates', 'hide_shipping_when_free_2020', 10, 2 );

function hide_shipping_when_free_2020( $rates, $package ) {


	if ( isset( $rates['free_shipping:18'] ) ) {

		unset( $rates['table_rate:4:1'] );
		unset( $rates['local_pickup:3'] );
		unset( $rates['table_rate:6:7'] );
		unset( $rates['table_rate:4:2'] );
		unset( $rates['table_rate:4:3'] );
		unset( $rates['table_rate:6:8'] );
		unset( $rates['table_rate:6:9'] );


	}

	return $rates;

}





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
function forlagid_shipping_method_text( $method, $index ) {

	$tooltip_text = '';

	switch ( $method->label ) {
		case 'Póstsending': // Póstsending
			$tooltip_text = 'Standard mail ';
			break;
		case 'Sótt í verslun': //  Sótt í verslun
			$tooltip_text = 'Local Pickup';
			break;
		case 'Póstkrafa':
			$tooltip_text = 'Cash on delivery';
			break;
		case 'Flýtiþjónusta':
			$tooltip_text = 'Express delivery';
			break;
	}

	if ( ! empty( $tooltip_text ) ) {
		echo '<span class="tooltip">' . esc_html( $tooltip_text ) . '</span>';
	}

}

add_action( 'woocommerce_after_shipping_rate', 'forlagid_shipping_method_text', 10, 2 );

function forlagid_before_terms_info() {

	$chosen_shipping_rates = WC()->session->get( 'chosen_shipping_methods' );
	if ( in_array( 'table_rate:12:22', $chosen_shipping_rates ) ) :

		echo '<div class="infobox" style="display: block">';
		echo '<p>Þú hefur valið flýtiþjónustu, sem felur í sér að pöntun er afhent samdægurs ef hún er gerð fyrir kl. 12 á virkum degi. Pantanir sem berast eftir þann tíma eða um helgi eru sendar út næsta virka dag. Flýtiþjónustupantanir eru keyrðar út af Póstinum eftir kl. 16. Ef viðtakandi er ekki við þegar sending berst er pakkinn fluttur á pósthús.</p>';
		echo '</div>';

	endif;
}

add_action( 'woocommerce_checkout_terms_and_conditions', 'forlagid_before_terms_info' );

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
