<?php
function config_get_custom_field( $value ) {
	global $post; 
	$custom_field = get_post_meta( $post->ID, $value, true );
	if ( !empty( $custom_field ) )
		return is_array( $custom_field ) ? stripslashes_deep( $custom_field ) : stripslashes( wp_kses_decode_entities( $custom_field ) );
	return false;
}
add_filter( 'manage_discount_coupons_posts_columns', 'discount_coupons_columns' ) ;
add_filter('gettext','custom_enter_title');

function custom_enter_title( $input ) {

    global $post_type;

    if( is_admin() && 'Add title' == $input && 'discount_coupons' == $post_type )
        return 'Coupon Code';

    return $input;
}
function discount_coupons_columns( $columns ) {

	$columns = array(

		'cb' => '<input type="checkbox" />',

		'title' => __('Code'),

		'coupon_type' => __('Coupon Type'),

		'coupon_amount' => __( 'Coupon Amount' ),

		'description' => __( 'Description' ),

		'configurator_ids' => __( 'Configurator IDs' ),

		'limit' => __( 'Usage / Limit' ),

		'edate' => __( 'Expiry date' ),
	);
	return $columns;
}

add_filter( 'manage_discount_coupons_posts_custom_column', 'discount_coupons_requirment_columns_code', 10, 2 ) ;

function discount_coupons_requirment_columns_code($column, $post_id){

	switch ($column) {

		// case 'code':

		// 	$config_code = config_get_custom_field('config_code');

		// 	echo '$'.$config_code;

		// break;

		case 'coupon_type':

			$config_discount_type = config_get_custom_field('config_discount_type');

			echo $config_discount_type;

		break;

		case 'coupon_amount':

			$coupon_amount = config_get_custom_field('coupon_amount');

			echo $coupon_amount;

		break;
		case 'description':

			$config_desc = config_get_custom_field('config_desc');

			echo $config_desc;

		break;
		case 'configurator_ids':

			$config_ids = config_get_custom_field('config_ids');

			echo $config_ids;

		break;
		case 'limit':

			$config_usage_limit_per_user = config_get_custom_field('config_usage_limit_per_user');

			echo $config_usage_limit_per_user;

		break;

		case 'edate':

			$config_expiry_date = config_get_custom_field('config_expiry_date');

			echo $config_expiry_date;

		break;
	}
}


// Register the Metabox
add_action( 'add_meta_boxes', 'config_add_meta_boxes' );
function config_add_meta_boxes() {

	// add_meta_box( 'gresys-meta-box-0', __( 'Code', 'discount_coupons' ), 
	// 	'gresys_meta_box_code', 'discount_coupons', 'normal', 'high' 
	// );

	add_meta_box( 'gresys-meta-box-1', __( 'Coupon Data', 'discount_coupons' ), 
		'gresys_meta_box_coupon_data', 'discount_coupons', 'normal', 'high' 
	);
	
	
}


	

function gresys_meta_box_coupon_data() {
	wp_nonce_field( 'my_pb_meta_box_nonce', 'pb_meta_box_nonce' ); ?>
	<style type="text/css">
/* Vertical Tabs
----------------------------------*/
#gresys-meta-box-1 .inside {
	padding: 0;
	margin: 0;
}
#gresys-meta-box-1 {
	overflow: hidden;
}
  .ui-tabs-vertical .ui-tabs-nav { 
  	margin: 0;
    width: 20%;
    float: left;
    line-height: 1em;
    padding: 0 0 10px;
    position: relative;
    background-color: #fafafa;
    border-right: 1px solid #eee;
    box-sizing: border-box;

  }
 
.ui-tabs-vertical .ui-tabs-nav::after {
    content: "";
    display: block;
    width: 100%;
    height: 9999em;
    position: absolute;
    bottom: -9999em;
    left: 0;
    background-color: #fafafa;
    border-right: 1px solid #eee;
}
  .ui-tabs-vertical .ui-tabs-nav li { 
  	margin: 0;
    padding: 0;
    display: block;
    position: relative;

  }
  .ui-tabs-vertical .ui-tabs-nav li a {
  
	margin: 0;
	padding: 10px;
	display: block;
	box-shadow: none;
	text-decoration: none;
	line-height: 20px!important;
	border-bottom: 1px solid #eee;
  }

  .ui-tabs-vertical .ui-tabs-nav li.ui-tabs-active a {     
  
	color: #555;
	position: relative;
	background-color: #eee;

  }
  .ui-tabs-vertical .ui-tabs-panel { 
  	float: left;
    width: 80%;
}
 
  .custom-config-tabs .icon-config {
   
    margin-right: .500em;
    font-size: 18px;
    font-weight: 700;
}

.config_options_panel p {
    padding: 5px 20px 5px 162px!important;
}
.config_options_panel label {
    float: left;
    width: 150px;
    padding: 0;
    margin: 0 0 0 -150px;
}
.config_options_panel select {
    float: left;
}
.config_options_panel p {
    margin: 9px 0;
	font-size: 12px;

	line-height: 24px;
}
.config_options_panel p::after {
    content: ".";
    display: block;
    height: 0;
    clear: both;
    visibility: hidden;
}
.config_options_panel input[type=text], .config_options_panel input[type=number], .config_options_panel input[type=date], .config_options_panel select, .config_options_panel textarea {
    width: 50%;
    float: left;
 }
#edit-slug-box {
	display: none;
}
.read-only-class {   
   cursor: not-allowed;
}
</style> 
	<div id="coupon_type_metabox">
		<?php
		global $wpdb;
		$config_ids = config_get_custom_field('config_ids');

	
		$all_config_id = $wpdb->get_results("SELECT meta_value, post_id FROM $wpdb->postmeta where meta_key = 'config_ids'");

		$new_result = [];
		$wp_trash_meta_status = [];
		
		foreach($all_config_id as $key => $all_id) {
			$wp_trash_meta_status[] = get_post_meta($all_id->post_id, '_wp_trash_meta_status', true);
		
			if(empty($wp_trash_meta_status[$key])) {
				if($config_ids != $all_id->meta_value) {
					$new_result[] = $all_id->meta_value;
				}
			}
			
		}
	
		
		$config_discount_type = config_get_custom_field('config_discount_type');
		$config_expiry_date = config_get_custom_field('config_expiry_date');
		$config_usage_limit_per_user = config_get_custom_field('config_usage_limit_per_user');
		
		$config_desc = config_get_custom_field('config_desc');
		$coupon_amount = config_get_custom_field('coupon_amount');
		$discount_options = ['percent_discount' => 'Percentage discount', 'fixed_discount' => 'Fixed discount']

		?>
		<div id="mytabs">
			<ul class="category-tabs">
				<li><a href="#general" class="custom-config-tabs"><span class="icon-config">&#9881;</span> <span>General</span></a></li>
				<!-- <li><a href="#ures" class="custom-config-tabs"><span class="icon-config">&#x26A0;</span> <span>Usage restriction</span></a></li> -->
				<li><a href="#ulimit" class="custom-config-tabs"><span class="icon-config">&#8800;</span> <span>Usage limits</span></a></li>
			</ul>
			
			<div id="general" class="config_options_panel">
				<p class=" form-field config_discount_type_field">
					<label for="config_discount_type">Discount type</label>
					<select id="config_discount_type" name="config_discount_type" class="select short" required>
						<option value="" >Select</option>
						 <?php foreach($discount_options as $key => $discount_option): ?>
	                        
	                        <option value="<?php echo $key; ?>" <?php if(@$config_discount_type == $key){?> selected="selected" <?php } ?>>  
	                        	<?php echo $discount_option; ?></option>
	                                   
	                    <?php endforeach; ?>
					</select>
				</p>
				<p class="form-field coupon_amount_field ">
					<label for="coupon_amount">Coupon amount</label>
					
					<input type="text" class="short" name="coupon_amount" id="coupon_amount" placeholder="0" required value="<?php echo @$coupon_amount ? @$coupon_amount: '0'; ?>">
				</p>
				<p class="form-field config_expiry_date_field ">
					<label for="config_expiry_date">Coupon expiry date</label>
					<input type="date" class="cdate-picker " name="config_expiry_date"  placeholder="YYYY-MM-DD" required value="<?php echo @$config_expiry_date; ?>"> 
				</p>


				
				<p class=" form-field config_ids_field">
					<label for="config_ids">Configurator Type</label>
					<select id="config_ids" name="config_ids" class="select short " required>
						 <option value="">Select</option>

	                    <?php foreach(get_configutor_texonomys() as $key => $configrator): ?>
	                      
	                        <option value="<?php echo $configrator->term_id; ?>" 
	                        	<?php if(@$config_ids == $configrator->term_id){?> selected="selected" <?php } if (in_array($configrator->term_id, $new_result)) {  ?> disabled="disabled"
	                        		
	                        	<?php } 	
	                        	 ?>>  
	                        	<?php echo $configrator->name; ?></option>
	                                   
	                    <?php endforeach; ?>
                	</select>
				</p>
				<p class=" form-field config_ids_field">
					<label for="config_desc">Description</label>
					<textarea rows="4" cols="50" name="config_desc"><?php echo @$config_desc; ?></textarea>
				</p>
			</div>
			
			<div class="hidden config_options_panel" id="ulimit">
				<p class="form-field usage_limit_per_user_field ">
					<label for="config_usage_limit_per_user">Usage limit per user</label>
					<input type="number" class="short" name="config_usage_limit_per_user" id="config_usage_limit_per_user" value="<?php echo @$config_usage_limit_per_user; ?>" placeholder="Unlimited usage" step="1" min="0">
				</p>
			</div>
		</div>
	</div><?php 
}


function config_meta_box_save( $post_id ) {

	// Stop the script when doing autosave

	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

		// Verify the nonce. If insn't there, stop the script

	if( !isset( $_POST['pb_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['pb_meta_box_nonce'], 'my_pb_meta_box_nonce' ) ) return;

		
	if( isset( $_POST['config_discount_type'] ) ){

		update_post_meta( $post_id, 'config_discount_type', $_POST['config_discount_type'] );

	}

	if( isset( $_POST['coupon_amount'] ) ){

		update_post_meta( $post_id, 'coupon_amount', $_POST['coupon_amount'] );

	}

	if( isset( $_POST['config_expiry_date'] ) ){

		update_post_meta( $post_id, 'config_expiry_date', $_POST['config_expiry_date'] );

	}

	if( isset( $_POST['config_ids'] ) ){

		update_post_meta( $post_id, 'config_ids', $_POST['config_ids'] );

	}

	if( isset( $_POST['config_desc'] ) ){

		update_post_meta( $post_id, 'config_desc', $_POST['config_desc'] );

	}

	if( isset( $_POST['config_usage_limit_per_user'] ) ){

		update_post_meta( $post_id, 'config_usage_limit_per_user', $_POST['config_usage_limit_per_user'] );

	}
}

add_action( 'save_post_discount_coupons', 'config_meta_box_save');