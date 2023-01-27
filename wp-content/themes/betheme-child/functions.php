<?php
/**
 * Betheme Child Theme
 *
 * @package Betheme Child Theme
 * @author Muffin group
 * @link https://muffingroup.com
 * @extended by Bilal Ahmad
 */

/**
 * Child Theme constants
 * You can change below constants
 */

// white label

define('WHITE_LABEL', false);

/**
 * Enqueue Styles
 */
session_start();
function mfnch_enqueue_styles()
{
	// enqueue the parent stylesheet
	// however we do not need this if it is empty
	// wp_enqueue_style('parent-style', get_template_directory_uri() .'/style.css');

	// enqueue the parent RTL stylesheet

	if (is_rtl()) {
		wp_enqueue_style('mfn-rtl', get_template_directory_uri() . '/rtl.css');
	}

	// enqueue the child stylesheet

	wp_dequeue_style('style');
	wp_enqueue_style('style', get_stylesheet_directory_uri() .'/style.css');
}
add_action('wp_enqueue_scripts', 'mfnch_enqueue_styles', 101);

/**
 * Load Textdomain
 */

function mfnch_textdomain()
{
	load_child_theme_textdomain('betheme', get_stylesheet_directory() . '/languages');
	load_child_theme_textdomain('mfn-opts', get_stylesheet_directory() . '/languages');
}
add_action('after_setup_theme', 'mfnch_textdomain');

/*************************************************************************************
 * Customization of configurator start here
 ************************************************************************************/

include('configurator/custom-taxonomy-field.php');
include('configurator/register-texonomy.php');
include('configurator/woo-commerce.php');
include('configurator/component_conditions.php');
include('configurator/discount-coupons.php');
include('recaptchalib.php');
include('lib/vendor/autoload.php');
// Custom Post Type as Product

// include('configurator/configurator-custom-products.php');
// include('configurator/configurator-custom-widgets.php');


//add custom top bar menu
add_action( 'init', 'config_custom_top_bar_menu' );
function config_custom_top_bar_menu() 
{
	register_nav_menu('custom-top-bar-menu',__( 'Top Bar' ));
}

//show custom menu on all pages and shop page
function custom_top_bar_menu()
{
    wp_nav_menu(
		[
			'theme_location' => 'custom-top-bar-menu', 
			'container_class' => 'top-bar-s'
		]
	);
}

//register custom styles
add_action( 'wp_enqueue_scripts', 'register_configurator_assets' );
function register_configurator_assets() 
{
	if ( get_page_template_slug() == 'configurator-page.php' )
	{
		wp_enqueue_style('bootstrap-lib-4.5.2', get_stylesheet_directory_uri() .'/assets/css/bootstrap.min.css');
		wp_enqueue_style('configurator-1.0', get_stylesheet_directory_uri() .'/assets/css/confgurator.css');
		// enqueue bootstrap js for popup modal
		wp_enqueue_script( 'popper-js', 'https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js', array( 'jquery') );

    	wp_enqueue_script( 'bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js', array( 'jquery','popper-js' ) );

		wp_enqueue_script( 'connfigurator-js-1.0', get_stylesheet_directory_uri() . '/assets/js/custom_script.js', array( 'jquery' ) );

		wp_localize_script( 'connfigurator-js-1.0', 'config_ajax_req', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ),'we_value' => 1234));
		wp_enqueue_script( 'connfigurator-js-1.0' );
    }
    // check datasheet slug
    if(is_page('datasheet-overview')) {
    	wp_enqueue_style('bootstrap-css', get_stylesheet_directory_uri() .'/assets/data-sheet/css/bootstrap.min.css');
    	wp_enqueue_style('custom-css', get_stylesheet_directory_uri() .'/assets/data-sheet/css/custom.css');
    	wp_enqueue_style('theme-css', get_stylesheet_directory_uri() .'/assets/data-sheet/css/theme.css');
		wp_enqueue_script( 'popper-js', 'https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js', array( 'jquery') );

		wp_enqueue_script( 'bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js', array( 'jquery','popper-js' ) );

    }
    wp_enqueue_style('uislider-custom-css', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');
    
    wp_enqueue_script('uislider-custom-js', 'https://code.jquery.com/ui/1.12.1/jquery-ui.js', array( 'jquery'));
    wp_enqueue_style('all-page-css', get_stylesheet_directory_uri() .'/assets/css/all_page_css.css');
    wp_enqueue_script('all-page-js', get_stylesheet_directory_uri() . '/assets/js/all_page_scripts.js', array( 'jquery'));
	wp_localize_script( 'all-page-js', 'ajax_object', array(
		'ajax_url' => admin_url( 'admin-ajax.php' ),
		'site_url' => get_site_url(),
	) );
}


add_action( 'admin_enqueue_scripts', 'register_admin_script' );
function register_admin_script()
{
	// <link href="" rel="stylesheet" />
	// <script src=""></script>

    // wp_register_style( 'select2css', 'http://cdnjs.cloudflare.com/ajax/libs/select2/3.4.8/select2.css', false, '1.0', 'all' );
	// wp_register_script( 'select2', 'http://cdnjs.cloudflare.com/ajax/libs/select2/3.4.8/select2.js', array( 'jquery' ), '1.0', true );
	
 	wp_register_style( 'select2css', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css', false, '1.0', 'all' );
	wp_register_script( 'select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js', array( 'jquery' ), '1.0', true );
	wp_register_script( 'jvalidate', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js', array( 'jquery' ), '1.19.2', true );
	
    wp_enqueue_script( 'jvalidate' );
	
    wp_enqueue_style( 'select2css' );
	wp_enqueue_script( 'select2' );

	wp_enqueue_script( 'admin-connfigurator-js-1.1', get_stylesheet_directory_uri() . '/assets/js/admin_script.js', array( 'jquery' ) );

	wp_localize_script( 'admin-connfigurator-js-1.1', 'config_admin_ajax_req', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ),'we_value' => 1234));
	wp_enqueue_script( 'admin-connfigurator-js-1.1' );

	wp_enqueue_script( 'admin-component-condition-js-1.0', get_stylesheet_directory_uri() . '/assets/js/component_condition.js', array( 'jquery' ) );
	wp_enqueue_script( 'admin-component-condition-js-1.0' );

	wp_enqueue_script( 'admin-woo-commerce-kits-js-1.0', get_stylesheet_directory_uri() . '/assets/js/admin-kits-woocommerce.js', array( 'jquery' ) );
	wp_enqueue_script( 'admin-woo-commerce-kits-js-1.0' );	
	

	wp_dequeue_style('custom-admin-style');
	wp_enqueue_style('custom-admin-style', get_stylesheet_directory_uri() .'/assets/css/custom-admin-style.css');
}

function filter_colors($index)
{
	$colors =  ["rgb(254, 165, 0)" => "#000000","rgb(166, 219, 171)" => "#000000","#08165D" => "#fff","rgb(188, 4, 4)" => "#fff","rgb(65, 65, 65)" => "#fff","rgb(0, 181, 51)" => "#000000"];
	if(isset(array_keys($colors)[$index]))
	{
		return ["background" => array_keys($colors)[$index] , "color" => $colors[array_keys($colors)[$index]]];
	}
	else
	{
		return ["background" => array_keys($colors)[0] , "color" => $colors[0] ];
	}
}


function add_menu_parent_class( $items ) {
$parents = array();
foreach ( $items as $item ) {
    //Check if the item is a parent item
    if ( $item->menu_item_parent && $item->menu_item_parent > 0 ) {
        $parents[] = $item->menu_item_parent;
  
    }
}

foreach ( $items as $item ) {
    if ( in_array( $item->ID, $parents ) ) {
        //Add "menu-parent-item" class to parents
        $item->classes[] = 'parent-menu-config';

    }
}

return $items;
}

//add_menu_parent_class to menu
add_filter( 'wp_nav_menu_objects', 'add_menu_parent_class' ); 



// function custom_pre_get_posts_query( $q ) {

//     $tax_query = (array) $q->get( 'tax_query' );
    

//     $tax_query[] = array(
//           'taxonomy' => 'product_cat',
//           'field' => 'slug',
//           'terms' => array( 'uncategorized', 'kits' ), 
//           'operator' => 'NOT IN'
//     );



//     $q->set( 'tax_query', $tax_query );

// }
// add_action( 'woocommerce_product_query', 'custom_pre_get_posts_query' );  


add_filter( 'get_terms', 'ts_get_subcategory_terms', 10, 3 );
function ts_get_subcategory_terms( $terms, $taxonomies, $args ) {
$new_terms = array();
// if it is a product category and on the shop page
if ( in_array( 'product_cat', $taxonomies ) && ! is_admin() &&is_shop() ) {
foreach( $terms as $key => $term ) {
if ( !in_array( $term->slug, array( 'uncategorized', 'kits' ) ) ) { //pass the slug name here
$new_terms[] = $term;
}}
$terms = $new_terms;
}
return $terms;
}



function update_config_tax_edit_form() {

    echo ' enctype="multipart/form-data"';

} // end update_edit_form
add_action('configurator-category_term_edit_form_tag', 'update_config_tax_edit_form');









