<?php
// overwrite functions from WC_Product
class CG_Woo_Product extends WC_Product  {

    protected $post_type = 'configurator';

    public function get_type() {
        return 'configurator';
    }

    public function __construct( $product = 0 ) {
        $this->supports[]   = 'ajax_add_to_cart';

        parent::__construct( $product );


    }


}
// overwrite WC_Product_Data_Store_CPT
class CG_Data_Store_CPT extends WC_Product_Data_Store_CPT {

    public function read( &$product ) { // this is required
        $product->set_defaults();
        $post_object = get_post( $product->get_id() );

        if ( ! $product->get_id() || ! $post_object || 'configurator' !== $post_object->post_type ) {

            throw new Exception( __( 'Invalid product.', 'woocommerce' ) );
        }

        $product->set_props(
            array(
                'name'              => $post_object->post_title,
                'slug'              => $post_object->post_name,
                'date_created'      => 0 < $post_object->post_date_gmt ? wc_string_to_timestamp( $post_object->post_date_gmt ) : null,
                'date_modified'     => 0 < $post_object->post_modified_gmt ? wc_string_to_timestamp( $post_object->post_modified_gmt ) : null,
                'status'            => $post_object->post_status,
                'description'       => $post_object->post_content,
                'short_description' => $post_object->post_excerpt,
                'parent_id'         => $post_object->post_parent,
                'menu_order'        => $post_object->menu_order,
                'reviews_allowed'   => 'open' === $post_object->comment_status,
            )
        );

        $this->read_attributes( $product );
        $this->read_downloads( $product );
        $this->read_visibility( $product );
        $this->read_product_data( $product );
        $this->read_extra_data( $product );
        $product->set_object_read( true );
    }

}


class CG_WC_Order_Item_Product extends WC_Order_Item_Product {
    public function set_product_id( $value ) {
        if ( $value > 0 && 'configurator' !== get_post_type( absint( $value ) ) ) {
            $this->error( 'order_item_product_invalid_product_id', __( 'Invalid product ID', 'woocommerce' ) );
        }
        $this->set_prop( 'product_id', absint( $value ) );
    }

}


function CG_woocommerce_data_stores( $stores ) {
    // the search is made for product-$post_type so note the required 'product-' in key name
    $stores['product-configurator'] = 'CG_Data_Store_CPT';
    return $stores;
}
add_filter( 'woocommerce_data_stores', 'CG_woocommerce_data_stores' , 11, 1 );


function CG_woo_product_class( $class_name ,  $product_type ,  $product_id ) {
    if ($product_type == 'configurator')
        $class_name = 'CG_Woo_Product';
    return $class_name; 
}
add_filter('woocommerce_product_class','CG_woo_product_class',25,3 );



function cg_woocommerce_product_get_price( $price, $product ) {

    if ($product->get_type() == 'configurator' ) {
    	// get price from custom post type
        $price = get_post_meta($product->get_id(), "addon_price", true);
        if(!isset($price)) {
        	 $price = get_post_meta($product->get_id(), "addon_actual_price", true);
        }
    }
    return $price;
}
add_filter('woocommerce_product_get_price','cg_woocommerce_product_get_price',20,2);
add_filter('woocommerce_product_get_price', 'cg_woocommerce_product_get_price', 10, 2 );


// required function for allowing posty_type to be added; maybe not the best but it works
function CG_woo_product_type($false,$product_id) { 
    if ($false === false) { // don't know why, but this is how woo does it
        global $post;
        // maybe redo it someday?!
        if (is_object($post) && !empty($post)) { // post is set
            if ($post->post_type == 'configurator' && $post->ID == $product_id) 
                return 'configurator';
            else {
                $product = get_post( $product_id );
                if (is_object($product) && !is_wp_error($product)) { // post not set but it's a configurator
                    if ($product->post_type == 'configurator') 
                        return 'configurator';
                } // end if 
            }    
        } else if(wp_doing_ajax()) { // has post set (usefull when adding using ajax)
            $product_post = get_post( $product_id );
            if ($product_post->post_type == 'configurator') 
                return 'configurator';
        } else { 
            $product = get_post( $product_id );
            if (is_object($product) && !is_wp_error($product)) { // post not set but it's a configurator
                if ($product->post_type == 'configurator') 
                    return 'configurator';
            } // end if 

        } // end if  // end if 

    } // end if 
    return false;
}
add_filter('woocommerce_product_type_query','CG_woo_product_type',12,2 );

function CG_woocommerce_checkout_create_order_line_item_object($item, $cart_item_key, $values, $order) {
    $product = $values['data'];
    if ($product->get_type() == 'configurator') {
        return new CG_WC_Order_Item_Product();
    } // end if 
    return $item ;
}   
add_filter( 'woocommerce_checkout_create_order_line_item_object', 'CG_woocommerce_checkout_create_order_line_item_object', 20, 4 );
function cod_woocommerce_checkout_create_order_line_item($item,$cart_item_key,$values,$order) {
    if ($values['data']->get_type() == 'configurator') {
        $item->update_meta_data( '_configurator', 'yes' ); // add a way to recognize custom post type in ordered items
        return;
    } // end if 

}
add_action( 'woocommerce_checkout_create_order_line_item', 'cod_woocommerce_checkout_create_order_line_item', 20, 4 );

function CG_woocommerce_get_order_item_classname($classname, $item_type, $id) {
    global $wpdb;
    $is_CG = $wpdb->get_var("SELECT meta_value FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE order_item_id = {$id} AND meta_key = '_configurator'");


    if ('yes' === $is_CG) { // load the new class if the item is our custom post
        $classname = 'CG_WC_Order_Item_Product';
    } // end if 
    return $classname;
}
add_filter( 'woocommerce_get_order_item_classname', 'CG_woocommerce_get_order_item_classname', 20, 3 );




