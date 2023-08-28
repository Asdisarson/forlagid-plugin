<?php

/**
 * @param int $post_id
 * @param WP_Post $post
 */
function forlagid_associate_product_taxonomy( $post_id, $post ) {

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	$to_associate = array(
		'product',
		'baekur',
	);
	if ( in_array( $post->post_type, $to_associate, true ) ) {
		$author_ids   = get_post_meta( $post_id, 'hofundur', true );
		$new_taxonomy = array();

		if ( is_array( $author_ids ) ) {
			foreach ( $author_ids as $author_id ) {
				$tax_id = get_post_meta( $author_id, 'author_taxonomy', true );
				if ( ! empty( $tax_id ) ) {
					$new_taxonomy[] = intval( $tax_id );
				}
			}
		}

		if ( ! empty( $new_taxonomy ) ) {
			wp_set_post_terms( $post_id, $new_taxonomy, 'hofundar' );
		}
	}

}

add_action( 'save_post', 'forlagid_associate_product_taxonomy', 15, 2 );

/**
 * @param int $post_id
 * @param WP_Post $post
 */
function forlagid_associate_author_taxoonomy( $post_id, $post ) {

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( 'hofundar' === $post->post_type ) {
		$author_taxonomy = get_post_meta( get_the_ID(), 'author_taxonomy', true );

		if ( empty( $author_taxonomy ) ) {
			$author_title = get_the_title( $post );
			$new_term  = term_exists( $author_title, 'hofundar' );
			if ( ! isset( $new_term['term_id'] ) ) {
				$new_term     = wp_insert_term( $author_title, 'hofundar' );
			}
			if ( ! is_wp_error( $new_term ) ) {
				add_post_meta( $post_id, 'author_taxonomy', $new_term['term_id'] );
			}
		}
	}

}

add_action( 'save_post', 'forlagid_associate_author_taxoonomy', 15, 2 );

// Register Custom Taxonomy
function forlagid_register_author_tax() {

	$labels = array(
		'name'          => _x( 'Höfundar', 'Taxonomy General Name', 'forlagid' ),
		'singular_name' => _x( 'Höfundur', 'Taxonomy Singular Name', 'forlagid' ),
		'menu_name'     => __( 'Höfundar', 'forlagid' ),
	);
	$args   = array(
		'labels'            => $labels,
		'hierarchical'      => true,
		'public'            => false,
		'show_ui'           => true,
		'show_admin_column' => true,
		'show_in_nav_menus' => false,
		'show_tagcloud'     => false,
	);
	register_taxonomy( 'hofundar', array( 'product', 'baekur' ), $args );

}

add_action( 'init', 'forlagid_register_author_tax', 0 );
