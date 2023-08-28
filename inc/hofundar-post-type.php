<?php
// Register Custom Post Type

function writer_post_type() {


$labels = array(

'name' => 'Forlags Höfundar',

'singular_name' => 'Forlags Höfundur',

'menu_name' => 'Forlags Höfundar',

'parent_item_colon' => 'Parent Author:',

'all_items' => 'Allir höfundar',

'view_item' => 'Skoða höfund',

'add_new_item' => 'Bæta við nýjum höfund',

'add_new' => 'Nýr höfundur',

'edit_item' => 'Breyta höfund',

'update_item' => 'Uppfæra höfund',

'search_items' => 'Leita að höfundum',

'not_found' => 'Engir höfundar fundust',

'not_found_in_trash' => 'Engir höfundar fundust í ruslakörfu',

);

$args = array(

'label' => 'hofundar',

'description' => 'Upplýsingar um höfund',

'labels' => $labels,

'supports' => array( 'title', 'thumbnail', ),

'hierarchical' => false,

'public' => true,

'show_ui' => true,

'show_in_menu' => true,

'show_in_nav_menus' => true,

'show_in_admin_bar' => true,

'menu_position' => 5,

'menu_icon' => 'dashicons-businessman',

'can_export' => true,

'has_archive' => true,

'exclude_from_search' => false,

'publicly_queryable' => true,

'capability_type' => 'page',

);

register_post_type( 'hofundar', $args );


}


// Hook into the 'init' action

add_action( 'init', 'writer_post_type', 0 );