<?php



// register custom post type for configurator

add_action('init', 'register_configurator_post_type', 0);

function register_configurator_post_type()

{

	$labels = [

		'name' => _x('Configurators', 'Post Type General Name', 'text_domain'),

		'singular_name' => _x('Configurator', 'Post Type Singular Name', 'text_domain'),

		'menu_name' => __('Configurators', 'text_domain'),

		'parent_item_colon' => __('Parent Item:', 'text_domain'),

		'all_items' => __('All Add-ons', 'text_domain'),

		'view_item' => __('View Add-on', 'text_domain'),

		'add_new_item' => __('Add New Add-on', 'text_domain'),

		'add_new' => __('Add New Add-on', 'text_domain'),

		'edit_item' => __('Edit Add-on', 'text_domain'),

		'update_item' => __('Update Add-on', 'text_domain'),

		'search_items' => __('Search Add-on', 'text_domain'),

		'not_found' => __('Not found', 'text_domain'),

		'not_found_in_trash' => __('Not found in Trash', 'text_domain'),

	];



	$args = [

		'label' => __('Configurator', 'text_domain'),

		'description' => __('Create Configurator', 'text_domain'),

		'labels' => $labels,

		'supports' => ['title', 'editor', 'thumbnail', 'revisions', 'excerpt'],

		'taxonomies' => ['configurator-category', 'configurator-category'],

		'hierarchical' => false,

		'public' => true,

		'show_ui' => true,

		'show_in_menu' => true,

		'show_in_nav_menus' => false,

		'show_in_admin_bar' => true,

		'menu_position' => 5,

		'menu_icon' => 'dashicons-laptop',

		'can_export' => true,

		'has_archive' => true,

		'exclude_from_search' => false,

		'publicly_queryable' => true,

		'capability_type' => 'post', //'page'

	];

	register_post_type('configurator', $args);

	register_post_type( 'discount_coupons',



		array(



			'labels' => array(



				'all_items' => 'Discount Coupons',



				'name_admin_bar' => 'all',



				'name' => 'Coupons',



				'singular_name' => 'Coupon',



				'add_new' => 'Add New',



				'add_new_item' => 'Add New Coupon',



				'edit' => 'Edit',



				'edit_item' => 'Edit Coupon',



				'new_item' => 'New Coupon',



				'view' => 'View',



				'view_item' => 'View Coupon',



				'search_items' => 'Search Coupons',



				'not_found' => 'No Coupons found',



				'not_found_in_trash' => 'No Coupons found in Trash',



				'parent' => 'Parent'



			),



			'public' => true,



			'menu_position' => 85,



			'show_ui' => true,



			'show_in_menu' => 'edit.php?post_type=configurator',



			'supports' => array( 'title' ),



			'has_archive' => true,

		)

	);

}



// register custom texonomy for configurator post type

add_action('init', 'register_configurator_taxonomy');

function register_configurator_taxonomy() {

	register_taxonomy(

		'configurator-category',

		'configurator',

		[

			'label' => __('Configurator Category'),

			'rewrite' => array('slug' => 'configurator-category'),

			'hierarchical' => true,

		]

	);



	register_taxonomy(

		'configurator-manufacturer',

		'configurator',

		[

			'label' => __('Configurator Manufacturer'),

			'rewrite' => array('slug' => 'configurator-manufacturer'),

			'hierarchical' => true,

		]

	);

}



//get custom texonomy of configurator

function get_configutor_texonomys() {

	$texonomy = get_terms(

		[

			'taxonomy' => 'configurator-category',

			'hide_empty' => false,

			'parent' => 0,

		]

	);



	return $texonomy;

}

//get custom chile texonomy of configurator

function get_configutor_child_texonomys($id)

{

	$child_texonomy = get_terms(

		[

			'taxonomy' => 'configurator-category',

			'hide_empty' => false,

			'parent' => $id,

		]

	);



	return $child_texonomy;

}



//get configurator and componenets fro frot-end side

add_action('wp_ajax_get_configurat_components', 'get_configurat_components');

add_action('wp_ajax_nopriv_get_configurat_components', 'get_configurat_components');

function get_configurat_components()

{

	if ($_POST['configurator_type'] == 'component') {

		include 'pages-inner/components.php';

	} elseif ($_POST['configurator_type'] == 'config_overview') {

		include 'pages-inner/configurator-overview.php';

	}

	exit();

}



//create meta boxes for variants

add_action('add_meta_boxes', 'config_create_variant_metabox');

/* Do something with the data entered */

add_action('save_post', 'config_save_variants_data');



/* Adds a box to the main column on the Post and Page edit screens */

function config_create_variant_metabox()

{

	add_meta_box(

		'dynamic_sectionid',

		__('Filters and Component Relation', 'myplugin_textdomain'),

		'config_create_variants_fields',

		'configurator',

		'normal',

		'high'

	);

}



/* Prints the metabox content */

function config_create_variants_fields()

{

	global $post;

	// Use nonce for verification

	wp_nonce_field(plugin_basename(__FILE__), 'config_variant_nonce');

	?>

	<div id="meta_inner">

		<input type="hidden" name="curr_post_id" id="curr_post_id" value="<?php echo $post->ID; ?>" />

		<p class="form-field">

			<label class="custom-post-label-block-s" for="configurator_id"><?php _e('Configurator Types'); ?></label>

			<select class="" style="width:50%" id="configurator_id" name="configurator_id">

				<?php $conf_id = get_post_meta($post->ID, 'configurator_id', true); ?>

				<?php $configurators = get_configutor_texonomys(); ?>

				<?php if (!empty($configurators)): ?>

					<?php foreach ($configurators as $key => $configrator): ?>

						<?php

						$selected = '';

						if ($conf_id) {

							if ($configrator->term_id == $conf_id) {

								$selected = 'selected';

							}

						} elseif ($key == 0) {

							$selected = 'selected';

						}

						?>

						<option value="<?php echo $configrator->term_id; ?>" <?php if ($conf_id) {

							;

						} ?> 

						<?php echo $selected; ?>><?php echo $configrator->name; ?></option>

					<?php endforeach; ?>

				<?php endif; ?>

			</select>

		</p>

		<p class="form-field">

			<label class="custom-post-label-block-s" for="configurator_component"><?php _e('Components'); ?></label>

			<select class="" style="width:50%" id="configurator_component" name="configurator_component">

				<option value=""></option>

			</select>

			<!-- <a href="javascript:void(0)" class="show-relation-block">Link Components</a> -->

		</p>





		<p class="form-field component-child-d" style="display: none;">

			<label class="custom-post-label-block-s" for="component_child"><?php _e('Child Components'); ?></label>

			<select class="" style="width:50%" id="component_child" name="component_child">

				<option value=""></option>

			</select>

		</p>

		<p style="border-bottom: 1px solid #ddd;clear:both;margin: 0px;"></p>

		<?php

		$post_title_tags = get_post_meta($post->ID, 'post_title_tags', true);

		$tags_counter = 1;

		if (!empty($post_title_tags)) {

			$post_title_tags = explode(',', $post_title_tags);

			$tags_counter = count($post_title_tags);

		}

		?>

		<div style="width:100%">

			<input type="hidden" id="current_post_filter_tags" value="<?php echo $tags_counter; ?>" />

			<input type="hidden" id="component_id" name="component_id" value="<?php echo get_post_meta($post->ID, 'component_id', true); ?>" />

			<div style="clear: both;height:5px; margin:0px;"></div>

			<div class="select-container">

				<div style="clear:both; height:5px;"></div>

				<div class="row" style="margin:0px;">

					<label class="custom-post-label-block-s">Filters</label>

					<select multiple="multiple" style="width:70%" class="post_tags" name="post_title_tags[]">

						<option value=""></option>

					</select>

				</div>

				<div style="clear:both; height:5px;"></div>

			</div>

		</div>



		<span id="here"></span>

		<!-- <span class="button addalbum"><?php //_e('Add Album'); ?></span>

		<span class="button addtracks"><?php //_e('Add Tracks'); ?></span> -->

		<script>

			var $ =jQuery.noConflict();

			$(document).ready(function() {

				$(".addtracks").click(function() {

					count = count + 1;

					$('#here').append('<p> Track number : <input type="text" name="album['+count+'][track]" value="" /><span class="removetrack">Remove Track</span></p>' );

					return false;

				});



				$(".removealbum").on('click', function() {

					$(this).parent().remove();

				});

			});

		</script>

	</div>

	<?php

}



/* When the post is saved, saves our custom data */

function config_save_variants_data($post_id)

{

	// verify if this is an auto save routine.

	// If it is our form has not been submitted, so we dont want to do anything

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {

		return;

	}

	// verify this came from the our screen and with proper authorization,

	// because save_post can be triggered at other times

	if (!isset($_POST['config_variant_nonce'])) {

		return;

	}



	if (!wp_verify_nonce($_POST['config_variant_nonce'], plugin_basename(__FILE__))) {

		return;

	}





	update_post_meta($post_id, 'component_id', $_POST['component_id']);

	update_post_meta($post_id, 'source_selected_tags', wp_slash(json_encode($_POST['source_selected_tags'])));

	update_post_meta($post_id, 'post_relation', wp_slash(json_encode($_POST['post_relation'])));

	update_post_meta($post_id, 'configurator_id', $_POST['configurator_id']);

	update_post_meta($post_id, 'configurator_component', $_POST['configurator_component']);



	update_post_meta($post_id, 'component_child', $_POST['component_child']);



	if (isset($_POST['post_title_tags'][0]) && !empty($_POST['post_title_tags'][0])) {

		update_post_meta($post_id, 'post_title_tags', implode(",", $_POST['post_title_tags']));

	} else {

		update_post_meta($post_id, 'post_title_tags', '');

	}

}



//multipart form editc custom post type

function update_edit_form() {

	echo ' enctype="multipart/form-data"';

} // end update_edit_form

add_action('post_edit_form_tag', 'update_edit_form');





//create meta boxes for add-on price

add_action('add_meta_boxes', 'config_create_price_metabox');

/* Do something with the data entered */

add_action('save_post', 'config_save_price_data');



/* Adds a box to the main column on the Post and Page edit screens */

function config_create_price_metabox()

{

	add_meta_box(

		'config_prices_id',

		__('Price', 'config_textdomain'),

		'config_create_prices_fields',

		'configurator',

		'normal',

		'high'

	);

}



/* Prints the metabox content */

function config_create_prices_fields()

{

	global $post;



	$terms = get_the_terms($post->ID, 'configurator-category' );

	foreach ($terms as $term) {

		$product_cat_slug = $term->slug;

		

	}

	$relation_bases = [];

	$decode_relation_bases = [];

	$camera_rel_bases = [];

	

	$relation_bases[] = get_post_meta($post->ID, 'relation_bases', true);

	



	foreach($relation_bases as $rbases) {

		$decode_relation_bases[] = json_decode($rbases, true);

	}

	foreach($decode_relation_bases as $rel_bases) {

		foreach($rel_bases as $rel_b) {

			$camera_rel_bases = array_merge($camera_rel_bases, $rel_b); 

		}  

	}

  

	// Use nonce for verification

	wp_nonce_field(plugin_basename(__FILE__), 'config_price_nonce');

	?>

	<div id="meta_inner_price">

		<p class="form-field">

			<label><?php _e('Sku'); ?></label>

			<input type="text" name="addon_sku" value="<?php echo get_post_meta($post->ID, 'addon_sku', true); ?>" />

		</p>

		<p class="form-field">

			<label><?php _e('Actual Price'); ?></label>

			<input type="text" name="addon_actual_price" value="<?php echo get_post_meta($post->ID, 'addon_actual_price', true); ?>" />

		</p>

		<p class="form-field">

			<label><?php _e('Sale Price'); ?></label>

			<input type="text" name="addon_price" value="<?php echo get_post_meta($post->ID, 'addon_price', true); ?>" />

		</p>

		<p class="form-field">

			<?php 

				$get_attach = get_post_meta($post->ID, 'wp_custom_attachment', true);

				if(isset($get_attach['url']) && !empty($get_attach['url'])) {

					echo "<a href=".$get_attach['url']." target='_blank'>View Attachment</a><br>";

				}

			?>

			<label><?php _e('Upload your PDF here'); ?></label>

			<!-- show only pdf files -->

			<input type="file" id="wp_custom_attachment" name="wp_custom_attachment" value="" accept="application/pdf"/>

		</p>

		<?php if($product_cat_slug == 'camera') {?>

		 <p class="form-field">

		   <?php

		   $bas_args = array(

			'post_type' => 'configurator',

			'order' => 'DESC',

			'post_status' => 'publish',

			'tax_query' => array(

					array(

						'taxonomy' => 'configurator-category',

						'field' => 'slug',

						'terms'    => 'bases'

					),

				),

			);

			$base_query = query_posts($bas_args); 



			?>

			<label><?php _e('Select Bases '); ?></label><br>

			 <select name="relation_bases[<?php echo $post->ID;?>][]" multiple="multiple" class="prefix-kit-addon-d">

			<!-- <option>Select Bases</option> -->

			<?php foreach($base_query as $key => $query): 

					$selected = ''; 

					if(in_array($query->ID, $camera_rel_bases)) {  

						$selected = 'selected';

					} ?>

				<option value="<?php echo  $query->ID; ?>" <?php echo $selected; ?>><?php echo  $query->post_title; ?></option>

			<?php endforeach;

		   ?>

		   </select>

		 

			   

				

			

		</p>

	<?php } ?>

	</div>

	<?php

}



/* When the post is saved, saves our custom data */

function config_save_price_data($post_id)

{

	// verify if this is an auto save routine.

	// If it is our form has not been submitted, so we dont want to do anything

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {

		return;

	}



	// verify this came from the our screen and with proper authorization,

	// because save_post can be triggered at other times

	if (!isset($_POST['config_price_nonce'])) {

		return;

	}



	if (!wp_verify_nonce($_POST['config_price_nonce'], plugin_basename(__FILE__))) {

		return;

	}



	// OK, we're authenticated: we need to find and save the data

	update_post_meta($post_id, 'addon_sku', $_POST['addon_sku']);

	update_post_meta($post_id, 'addon_price', $_POST['addon_price']);

	update_post_meta($post_id, 'addon_actual_price', $_POST['addon_actual_price']);



	if(isset($_POST['relation_bases'])) {

		update_post_meta($post_id, 'relation_bases', wp_slash(json_encode($_POST['relation_bases'])));

	}



   

   // Make sure the file array isn't empty

   if(!empty($_FILES['wp_custom_attachment']['name'])) {

		 

	// Setup the array of supported file types. In this case, it's just PDF.

	$supported_types = array('application/pdf');

	 

	// Get the file type of the upload

	$arr_file_type = wp_check_filetype(basename($_FILES['wp_custom_attachment']['name']));

	$uploaded_type = $arr_file_type['type'];

	 

	// Check if the type is supported. If not, throw an error.

	if(in_array($uploaded_type, $supported_types)) {



		// Use the WordPress API to upload the file

		$upload = wp_upload_bits($_FILES['wp_custom_attachment']['name'], null, file_get_contents($_FILES['wp_custom_attachment']['tmp_name']));



		if(isset($upload['error']) && $upload['error'] != 0) {

			wp_die('There was an error uploading your file. The error is: ' . $upload['error']);

		} else {

			add_post_meta($post_id, 'wp_custom_attachment', $upload);

			update_post_meta($post_id, 'wp_custom_attachment', $upload);     

		} // end if/else



	} else {

		wp_die("The file type that you've uploaded is not a PDF.");

	} // end if/else

	 

} // end if

}



//get componenets for admin side side [configurator custom post type in post]

add_action('wp_ajax_get_admin_components', 'get_admin_components');

add_action('wp_ajax_nopriv_get_admin_components', 'get_admin_components');

function get_admin_components()

{

	global $wpdb;



	$id = $_POST['configurator_id'];



	$components = get_configutor_child_texonomys($id);

	$source_options = '';

	$dest_option = '';

	$source_selected_opt = '';

	$options['source_selected_tags'] = '';

	$options['dest_option'] = '';

	$options = [];



	if (!empty($components)) {

		foreach ($components as $component) {

			$selected = '';

			$source_options .= '<option value="' . $component->term_id . '" ' . $selected . ' >' . $component->name . '</option>';



			if (isset($_POST['component']) && $_POST['component'] != $component->term_id) {

				$store['id'] = $component->term_id;

				$store['text'] = $component->name;

				$store['selected'] = isset($selected) && !empty($selected) ? true : false;

				$options['dest_option'][] = $store;

			}



			if (isset($_POST['component']) && $_POST['component'] == $component->term_id) {

				$source_selected_opt .= '<option selected value="' . $component->term_id . '" ' . $selected . ' >' . $component->name . '</option>';

			}

		}



		if (isset($_POST['component']) && !empty($_POST['component'])) {

			$tags = $wpdb->get_results("SELECT id,tag as text FROM wp_configurator_tags

			WHERE component = " . $_POST['component'] . " ");

			$results = (array)$tags;



			$source_selected_tags = get_term_meta($_POST['component'], 'source_selected_tags', true);



			if (!empty($source_selected_tags) && ($source_selected_tags != null && $source_selected_tags != 'null')) {

				$source_selected_tags = json_decode($source_selected_tags, true);

				foreach ($source_selected_tags as $key => $sourceTag) {

					foreach ($results as $res) {

						$selected = 0;

						if (in_array($res->id, $sourceTag)) {

							$selected = 1;

						}

						$store['id'] = $res->id;

						$store['text'] = $res->text;

						$store['selected'] = $selected;

						$options['source_selected_tags'][$key][] = $store;

					}

				}

			} else {



				foreach ($results as $res) {

					$selected = 0;

					$store['id'] = $res->id;

					$store['text'] = $res->text;

					$store['selected'] = $selected;

					$options['source_selected_tags'][0][] = $store;

				}

			}

		}

	}



	$options['source_components'] = $source_options;

	$options['source_selected_tags'] = $options['source_selected_tags'];

	// $options['post_relation'] = $options['post_relation'];



	$options['source_selected_option'] = $source_selected_opt;

	$options['dest_components'] = $options['dest_option'];

	$options['dest_component_tags'] = $dest_option;



	//$post_relation = get_post_meta($_POST['current_post_id'],'post_relation',true);

	$post_relation = get_term_meta($_POST['component'], 'post_relation', true);

	if (!empty($post_relation) && ($post_relation != null && $post_relation != 'null')) {

		$post_relation = json_decode($post_relation, true);

		foreach ($post_relation as $key => $val) {

			$create_comp_tags = [];

			foreach ($val as $value) {

				if (isset($value['component'][0]) && !empty($value['component'][0])) {

					$store_post['component'] = $value['component'][0];

					$get_tags_by_comp_id = get_tags_by_comp_id($value['component'][0]);

					if (!empty($get_tags_by_comp_id)) {

						$store_post['tags'] = $get_tags_by_comp_id;

					} else {

						$store_post['tags'] = [];

					}



					if (isset($value['tag'])) {

						$store_post['selected_tags'] = $value['tag'];

					} else {

						$store_post['selected_tags'] = [];

					}

					$create_comp_tags[] = $store_post;

				}

			}

			$options['relation_component_tags'][$key] = $create_comp_tags;

		}

	} else {

		$options['relation_component_tags'] = false;

	}



	echo json_encode($options);

	exit();

}



//get componenets for admin side side [configurator custom post type in post]

add_action('wp_ajax_get_post_admin_components', 'get_post_admin_components');

add_action('wp_ajax_nopriv_get_post_admin_components', 'get_post_admin_components');

function get_post_admin_components()

{

	global $wpdb;



	$id = $_POST['configurator_id'];

	$components = get_configutor_child_texonomys($id);

	$source_options = '';



	$options = [];



	if (!empty($components)) {

		$post_component = get_post_meta($_POST['current_post_id'], 'configurator_component', true);

		foreach ($components as $component) {

			$selected = '';

			if ($post_component) {

				if ($component->term_id == $post_component) {

					$selected = 'selected';

				}

			}

			$source_options .= '<option value="' . $component->term_id . '" ' . $selected . ' >' . $component->name . '</option>';

		}

	}

	$options['source_components'] = $source_options;

	echo json_encode($options);

	exit();

}



//get component tags by component id

function get_tags_by_comp_id($id)

{

	global $wpdb;



	$tags = $wpdb->get_results("SELECT id,tag as text FROM wp_configurator_tags

	WHERE component = " . $id . " ");

	$results = (array)$tags;



	return $results;

}



//get component tags by component id

function get_tags_by_id($id)

{

	global $wpdb;



	$tags = $wpdb->get_results("SELECT id,tag as text FROM wp_configurator_tags

	WHERE id = " . $id . " ");

	$results = (array)$tags;



	return $results;

}



//get componenets for admin side side [configurator custom post type in post]

add_action('wp_ajax_config_filter_tags', 'config_filter_tags');

add_action('wp_ajax_nopriv_config_filter_tags', 'config_filter_tags');

function config_filter_tags()

{

	global $wpdb;

   

	// $wpdb->delete('wp_configurator_tags', array( 

	//     'configurator_id' =>  $_POST['configurator_id'], 

	//     'component' => $_POST['component'] 

	// ) );

	$tags = $wpdb->get_results("SELECT id,tag as text FROM wp_configurator_tags

	WHERE configurator_id = " . $_POST['configurator_id'] . " and component = " . $_POST['component'] . " and tag = '" . $_POST['tag'] . "' ");



	// if (empty($tags)) {

		$selected_tag = [];

		$saveTags =

			[

			'post_id' => $_POST['component'], //$_POST['post_id'],

			 'configurator_id' => $_POST['configurator_id'],

			'component' => $_POST['component'],

			'tag' => $_POST['tag'],

		];



		$wpdb->insert('wp_configurator_tags', $saveTags);

		$last_id = $wpdb->insert_id;

		foreach ($_POST['selected_post_tags'] as $value) {

			if (strpos($value, '(New)') !== false) {

				$selected_tag[] = $last_id;

			} else {

				$selected_tag[] = $value;

			}

		}

		

		$tags = $wpdb->get_results("SELECT id,tag as text FROM wp_configurator_tags

		WHERE configurator_id = " . $_POST['configurator_id'] . " and component = " . $_POST['component'] . " ");

		$results = (array)$tags;

		$options = [];



		foreach ($results as $res) {

			$selected = false;

			if (in_array($res->id, $selected_tag)) {

				$selected = true;

			}

			$store['id'] = $res->id;

			$store['text'] = $res->text;

			$store['selected'] = $selected;

			$options['component_tags'][] = $store;

		}

		echo json_encode($options);

	// }

	exit();

}



//get componenets tags for admin side [configurator custom post type in post]

add_action('wp_ajax_get_component_tags', 'get_component_tags');

add_action('wp_ajax_nopriv_get_component_tags', 'get_component_tags');

function get_component_tags()

{

	global $wpdb;



	//

	$tags = $wpdb->get_results("SELECT id,tag as text FROM wp_configurator_tags

	WHERE component = " . $_POST['component'] . " ");



	$results = (array)$tags;



	$options = [];

	if (!empty($results)) {

		foreach ($results as $res) {

			$store['id'] = $res->id;

			$store['text'] = $res->text;

			$store['selected'] = false;

			$options['component_tags'][] = $store;

		}

	}



	$post_title_tags = get_term_meta($_POST['component'], 'post_title_tags', true);

	if (!empty($post_title_tags)) {

		$post_title_tags = json_decode($post_title_tags, true);

		foreach ($post_title_tags as $key => $post_title) {

			$store['title'] = $post_title['title'][0];

			$store['tags_id'] = $post_title['tags'];

			$store['filter_show'] = $post_title['filter_show'];

			$options['term_component_tags'][] = $store;

		}

	}



	echo json_encode($options);

	exit();

}



//get componenets tags for admin side [configurator custom post type in post]

add_action('wp_ajax_get_post_component_tags', 'get_post_component_tags');

add_action('wp_ajax_nopriv_get_post_component_tags', 'get_post_component_tags');

function get_post_component_tags()

{

	global $wpdb;



	$tags = $wpdb->get_results("SELECT id,tag as text FROM wp_configurator_tags

	WHERE component = " . $_POST['component'] . " ");

	$child_tags = '';

	$child_results = '';



	$results = (array)$tags;

	$childComp = get_configutor_child_texonomys($_POST['component']);

	$options = [];

	if (isset($_POST['child_component']) && !empty($_POST['child_component']) || empty($childComp)) {

		if(!empty($_POST['child_component'])) {



			$child_tags = $wpdb->get_results("SELECT id,tag as text FROM wp_configurator_tags WHERE component = " . $_POST['child_component'] . " ");

			$child_results = (array)$child_tags;

		  

		}

		 // sub child results 

		if (!empty($child_results)) {



			$post_title_tags = get_post_meta($_POST['post_id'], 'post_title_tags', true);

			$con_component = get_post_meta($_POST['post_id'], 'configurator_component', true);

			$post_tags_title = get_term_meta($_POST['child_component'], 'post_title_tags', true);

			$post_tags_title = json_decode($post_tags_title, true);

			if (!empty($post_title_tags)) {

				$post_title_tags = explode(',', $post_title_tags);

			}

		   

			foreach($child_results as $index => $result) {

				foreach($post_tags_title as $post_title) {

					foreach($post_title['tags'] as $pt) {

						if($result->id == $pt) {

							$child_results[$index]->title = $post_title['title'][0];

						}

					}

				}

			}

			foreach ($child_results as $res) {

				$selected = false;

				if (!empty($post_title_tags) && in_array($res->id, $post_title_tags)) {

					$selected = true;

				}

				$store['id'] = $res->id;

				$store['text'] = $res->text;

				$store['title'] = $res->title;

				$store['selected'] = $selected;

				$options['component_tags'][] = $store;

			}

		} else {

		   

				if (!empty($results)) {



					$post_title_tags = get_post_meta($_POST['post_id'], 'post_title_tags', true);

					$con_component = get_post_meta($_POST['post_id'], 'configurator_component', true);

					$post_tags_title = get_term_meta($con_component, 'post_title_tags', true);

					$post_tags_title = json_decode($post_tags_title, true);

					if (!empty($post_title_tags)) {

						$post_title_tags = explode(',', $post_title_tags);

					}

				   

					foreach($results as $index => $result) {

						foreach($post_tags_title as $post_title) {

							foreach($post_title['tags'] as $pt) {

								if($result->id == $pt) {

								 $results[$index]->title = $post_title['title'][0];

							 }

						 }

					 }

				 }



				 foreach ($results as $res) {

					$selected = false;

					if (!empty($post_title_tags) && in_array($res->id, $post_title_tags)) {

						$selected = true;

					}

					$store['id'] = $res->id;

					$store['text'] = $res->text;

					$store['title'] = $res->title;

					$store['selected'] = $selected;

					$options['component_tags'][] = $store;

				}

			}

		}

	} else {

		$component_child = get_post_meta($_POST['post_id'], 'component_child', true);

		if (!empty($childComp)) {

			$store = [];

			foreach ($childComp as $val) {

				$selected = false;

				if (!empty($component_child) && $component_child == $val->term_id) {

					$selected = true;

				}

				$child['id'] = $val->term_id;

				$child['text'] = $val->name;

				$child['selected'] = $selected;



				$store['child_comp'][] = $child;

			}

			$options['component_tags'][] = $store;

		}

	}



	echo json_encode($options);

	exit();

}



//get componenets and tags with relationship for admin side [configurator custom post type in post]

add_action('wp_ajax_get_component_and_tags_with_relation', 'get_component_and_tags_with_relation');

add_action('wp_ajax_nopriv_get_component_and_tags_with_relation', 'get_component_and_tags_with_relation');

function get_component_and_tags_with_relation()

{

	global $wpdb;

	$components = get_configutor_child_texonomys($_POST['configurator_id']);



	$options = [];

	if (!empty($components)) {

		foreach ($components as $res) {

			if ($_POST['component'] != $res->term_id) {

				$store['id'] = $res->term_id;

				$store['text'] = $res->name;



				$options['relation_component'][] = $store;

			}

		}

	}

	echo json_encode($options);

	exit();

}



//get componenets and tags with relationship for admin side [configurator custom post type in post]

add_action('wp_ajax_get_tags_by_components', 'get_tags_by_components');

add_action('wp_ajax_nopriv_get_tags_by_components', 'get_tags_by_components');

function get_tags_by_components()

{

	global $wpdb;

	$tags = $wpdb->get_results("SELECT id,tag as text FROM wp_configurator_tags

	WHERE component = " . $_POST['component'] . " ");

	$results = (array)$tags;



	$options = [];

	if (!empty($results)) {

		foreach ($results as $res) {

			if ($_POST['component'] != $res->id) {

				$store['id'] = $res->id;

				$store['text'] = $res->text;

				$options['relation_component_tags'][] = $store;

			}

		}

	}

	echo json_encode($options);

	exit();

}



//get componenets and tags with relationship for admin side [configurator custom post type in post]

add_action('wp_ajax_component_block', 'component_block');

add_action('wp_ajax_nopriv_component_block', 'component_block');

function component_block()

{

	include 'pages-inner/component-filter.php';

	exit();

}



function get_config_posts_by_comp_id($id, $post = '')

{

	global $wpdb;

	$args = [
		'orderby'     => 'meta_value_num',
		'meta_key'   => 'addon_price',

		'order' => 'ASC',
		'ignore_custom_sort' => true,

		'posts_per_page' => -1,

		'post_type' => 'configurator',

		'post_status' => 'publish',


		'search_component_post_title' => isset($post['s']) ? $post['s'] : '',

		 // Add search manufactuerer

		'search_manufactuerer' => isset($post['manufactuerer']) ? $post['manufactuerer'] : '',

		'tax_query' => [

			'relation' => 'AND',

			[

				'taxonomy' => 'configurator-category',

				'field' => 'term_id',

				'terms' => !empty($id) ? $id : $post['comp_id'],

				'include_children' => false,

			],

		],

	];




	// Checking manufactuerer filter with relation or without relation, with selected filter or without 

	if (isset($post['manufactuerer']) && !empty($post['manufactuerer']) && !empty($post['term_filter'])) {

		add_filter( 'posts_clauses', 'regex_manufactuerer_query',10, 2);



	} elseif (isset($post['manufactuerer']) && !empty($post['manufactuerer']) && empty($post['term_filter'])) {

		if (isset($post['relation']) && !empty($post['relation'])) {



		  

			$reltion_arr = str_replace("\\", "", $post['relation']);

			$reltion_arr = json_decode($reltion_arr, true);

			

			if(!empty($reltion_arr) && $reltion_arr[$post['selected_configurator']] != $post['comp_id']) {

			  

				foreach ($reltion_arr[$post['selected_configurator']] as $rel) {



					if ($rel['position'] == 1) {

						if($post['comp_id'] != $rel['comp_id']) {

							$post_title_tag = get_post_meta($rel['addon'], 'post_title_tags', true);



							foreach (explode(",", $post_title_tag) as $tag) {

								$source_selected_tags = get_term_meta($rel['comp_id'], 'source_selected_tags', true);

					   

								if (!empty($source_selected_tags)) {

								  

									 add_filter( 'posts_clauses', 'regex_manufactuerer_query',10, 2);

									

								} else {

									$args['tax_query'][] =

									[

										'taxonomy' => 'configurator-manufacturer',

										'field' => 'term_id',

										'terms' => $post['manufactuerer'],

										'include_children' => false,

									];

								} 

							}

						} else {

							$args['tax_query'][] =

							[

								'taxonomy' => 'configurator-manufacturer',

								'field' => 'term_id',

								'terms' => $post['manufactuerer'],

								'include_children' => false,

							];

						}

					}

				}

			} else {

				  

				$args['tax_query'][] =

				[

					'taxonomy' => 'configurator-manufacturer',

					'field' => 'term_id',

					'terms' => $post['manufactuerer'],

					'include_children' => false,

				];

			}

		} else {

				  // first time

			$args['tax_query'][] =

			[

				'taxonomy' => 'configurator-manufacturer',

				'field' => 'term_id',

				'terms' => $post['manufactuerer'],

				'include_children' => false,

			];

		}

	 

	}

	  

	
	if (isset($post['relation']) && !empty($post['relation'])) {



		$reltion_arr = str_replace("\\", "", $post['relation']);

		$reltion_arr = json_decode($reltion_arr, true);



		$options['relation_component_tags'] = [];

		$prepare_search_tag = [];

		$post_title_tag = [];



		if (is_array($reltion_arr) && $reltion_arr[$post['selected_configurator']] != $post['comp_id']) {

			foreach ($reltion_arr[$post['selected_configurator']] as $rel) {

				if ($rel['position'] == 1) {

					foreach($rel['addon'] as $key => $addon) {

						$post_title_tag[$key] = get_post_meta($addon, 'post_title_tags', true);

						foreach($post_title_tag as  $title_tag) {

							foreach (explode(",", $title_tag) as $tag) {



								$source_selected_tags = get_term_meta($rel['comp_id'], 'source_selected_tags', true);



								if (!empty($source_selected_tags)) {

									$source_selected_tags = json_decode($source_selected_tags, true);



									$relation_filter_arr = [];

									foreach ($source_selected_tags as $key => $value) {

										// check in array && also check count of both equals of relation component ID

										if (in_array($tag, $value) && count($value) == count($tag)) {



											$post_relation = get_term_meta($rel['comp_id'], 'post_relation', true);





											if (!empty($post_relation) && ($post_relation != null && $post_relation != 'null')) {

												$post_relation = json_decode($post_relation, true);

												// Filter Post relation 

												if (in_array($tag, $value)) {

													$relation_filter_arr[$key] =  $post_relation[$key];

												}



												// foreach ($post_relation as $key => $val) {

												foreach ($relation_filter_arr as $key => $val) {

													$create_comp_tags = [];

													foreach ($val as $value) {

														if (isset($value['component'][0])) {

															$store_post['component'] = $value['component'][0];

															if (isset($value['tag'])) {

																$store_post['selected_tags'] = $value['tag'];

															} else {

																$store_post['selected_tags'] = [];

															}



															$create_comp_tags[] = $store_post;

														}

													}

													$options['relation_component_tags'][$key] = $create_comp_tags;

												}

											}

											if (!empty($options['relation_component_tags'])) {

												foreach ($options['relation_component_tags'] as $key => $main) {

													foreach ($main as $Innerkey => $iner) {

														if ($post['comp_id'] == $iner['component']) {

															if (!empty($iner['selected_tags'])) {

																$prepare_search_tag[] = $iner['selected_tags'];

															}

														}

													}

												}

											}

										}

									}

								}

							}

						}

					}

				}

				//break;

			}

		}

	}

	$post_title_tags = get_term_meta($_POST['comp_id'], 'post_title_tags', true);



	if (!empty($post_title_tags)) {

		$post_title_tags = json_decode($post_title_tags, true);

		$filter_regex = [];

		$component_relation_filter = [];

		$termf = $post['term_filter'];

		

		

	

		foreach ($post_title_tags as $index => $filter_data) {

			$filter_regex[$filter_data['title'][0]] = $filter_data['tags'];

		}

	}





	// Filter Relation Tags Check

	if(!empty($options['relation_component_tags'])) {

		foreach($options['relation_component_tags'] as $i => $relation_com) {

			foreach($relation_com as $com_relation) {

				// check relation component id exit or not with selected component

				if($_POST['comp_id'] == $com_relation['component']){

					foreach($com_relation['selected_tags'] as $component_rel) {

						$component_relation_filter[] = $component_rel;

					}

				}

			}

		}

	}

	$comp_rel_title_filter = [];

	$com_relation_f = '';

	if(!empty($component_relation_filter)) {

		foreach ($post_title_tags as $index => $filter_data) {

			if(array_intersect($component_relation_filter, $filter_data['tags'])) {



				$comp_rel_title_filter[$filter_data['title'][0]] = array_intersect($component_relation_filter, $filter_data['tags']);

			} else {

				$comp_rel_title_filter[$filter_data['title'][0]] =  $filter_data['tags'];

			}

		}

	   $com_relation_f =  array_filter($comp_rel_title_filter); 

	}



	// check term filter and relation filter condition

	if (isset($post['term_filter']) && !empty($post['term_filter']) || !empty($component_relation_filter)) {



		$args['meta_query'] = [

			 'filter_regex' => $filter_regex,

			 'sterm_filter' => $post['term_filter'],

			 'component_relation_filter' => $component_relation_filter,

			 'comp_rel_title_filter' => $com_relation_f

		];

		add_filter('pre_get_posts', 'regex_filter_query');

	}

	

	add_filter('posts_where', 'title_filter', 10, 2);


	$wp_query = new WP_Query($args);
  


 

	remove_filter('posts_where', 'title_filter', 10, 2);

	// Remove Filters

	remove_filter('posts_where', 'regex_filter_query');

	remove_filter( 'posts_clauses', 'regex_manufactuerer_query',10, 2);





	if (isset($wp_query->posts) && !empty($wp_query->posts)) {

		$preate_search_arrr = [];

		if (!empty($prepare_search_tag)) {

			foreach ($prepare_search_tag as $key => $value) {

				foreach ($value as $val) {

					$preate_search_arrr[] = $val;

				}

			}

		}



		return ['posts' => $wp_query->posts, 'searchrelation' => $preate_search_arrr];

	} else {

		return array();

	}

}



function regex_filter_query( $wp_query ) {

   

	$wp_query->set( 'meta_key', 'post_title_tags' );    

	add_filter( 'posts_where', 'filter_meta_query',10, 2);

}

function filter_meta_query( $where = '', &$wp_query ) {



	global $wpdb;



	$where = '';

	if ($wp_query->query['meta_query']) {

		$filter = $wp_query->query['meta_query']['filter_regex'];

		$sql_query = '';

		$sterm_filter = $wp_query->query['meta_query']['sterm_filter'];

		$component_relation_filter = $wp_query->query['meta_query']['component_relation_filter'];

		$comp_rel_title_filter = $wp_query->query['meta_query']['comp_rel_title_filter'];



		$search_term = $wp_query->get('search_component_post_title');

		$search_query = '';

		

		$search_bracket = '';

		 if (isset($search_term) && !empty($search_term)) {

		   

			 $search_query .= ' ) AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql(like_escape($search_term)) . '%\'';

		}

		$comp_relation_filter = [];



		foreach($comp_rel_title_filter as $rel_comp_ids) {

			if(array_intersect($rel_comp_ids,$sterm_filter)) {

			   $comp_relation_filter[] =  array_intersect($rel_comp_ids,$sterm_filter);

			} else {

				$comp_relation_filter[] = $rel_comp_ids;

			}

		   

		   

		}

		

		$f=0;

		$orc = 0;

		$store_unselected =[];

		$group_not_selected =[];

		if(!empty($component_relation_filter) && !empty($sterm_filter)) {

			foreach($comp_relation_filter as $checked) {

				$flag = 0;

				$iterate = 0;



				$group_selected = [];

				

			   

				foreach($checked as $index => $c) {



					$or = 'OR';

					if ($index == end($checked)){

						$or = '';

					}

					if(in_array($c,array_merge($sterm_filter,$component_relation_filter))) {



						if(isset($search_term) && !empty($search_term)) {

							if($orc < 1) {

								$search_bracket = '(';

							} else {

								$search_bracket = '';

							}

						}





						$orc++;

						if($iterate < 1)

						{

							$sql_query .= " AND $search_bracket ( ( $wpdb->postmeta.meta_key = 'post_title_tags' AND $wpdb->postmeta.meta_value REGEXP '(^|,)$c(,|$)' )"; 

						}

						else

						{

							$sql_query .= "OR ( $wpdb->postmeta.meta_key = 'post_title_tags' AND $wpdb->postmeta.meta_value REGEXP '(^|,)$c(,|$)' ) ";

						}

						$iterate++;



						$flag = 1;

						$f++;

						$group_selected[] = $c;



					}



				}



				if($flag) {

					

					if(!empty($group_selected))

					{

						$arra_diff = array_diff($checked,$group_selected );

						foreach($arra_diff as $a_diff){

							$group_not_selected[] = $a_diff;

						}

					}

					$sql_query .= ' ) ' ;

				}

				else

				{

					$store_unselected[] = $checked;

				}

			}

		} else {

			foreach($filter as $checked) {

				$flag = 0;

				$iterate = 0;



				$group_selected = [];

				

			   

				foreach($checked as $index => $c) {



					$or = 'OR';

					if ($index == end($checked)){

						$or = '';

					}

					// Check select filter is not empty and relation filter is empty then enter

					if(!empty($sterm_filter) && empty($component_relation_filter)) {

						if(in_array($c,$sterm_filter)) {



							//search 

							if(isset($search_term) && !empty($search_term)) {

								if($orc < 1) {

									$search_bracket = '(';

								} else {

									$search_bracket = '';

								}

							}

							$orc++;

							if($iterate < 1)

							{

								$sql_query .= " AND $search_bracket ( ( $wpdb->postmeta.meta_key = 'post_title_tags' AND $wpdb->postmeta.meta_value REGEXP '(^|,)$c(,|$)' )"; 

							}

							else

							{

								$sql_query .= "OR ( $wpdb->postmeta.meta_key = 'post_title_tags' AND $wpdb->postmeta.meta_value REGEXP '(^|,)$c(,|$)' ) ";

							}

							$iterate++;



							$flag = 1;

							$f++;

							$group_selected[] = $c;

						}

					}

					 // Check relation filter is not empty and select filter is empty then enter

					elseif(!empty($component_relation_filter) && empty($sterm_filter)) {



						if(in_array($c,$component_relation_filter)) {

				  

							if(isset($search_term) && !empty($search_term)) {



								if($orc < 1) {

									$search_bracket = '(';

								} else {

									$search_bracket = '';

								}

							}



							$orc++;

							if($iterate < 1)

							{

								$sql_query .= " AND $search_bracket ( ( $wpdb->postmeta.meta_key = 'post_title_tags' AND $wpdb->postmeta.meta_value REGEXP '(^|,)$c(,|$)' )"; 

							}

							else

							{

								$sql_query .= "OR ( $wpdb->postmeta.meta_key = 'post_title_tags' AND $wpdb->postmeta.meta_value REGEXP '(^|,)$c(,|$)' ) ";

							}

							$iterate++;



							$flag = 1;

							$f++;

							$group_selected[] = $c;

						}



						  // Check both condition at once if both not empty

					} 



				}



				if($flag) {

					

					if(!empty($group_selected))

					{

						$arra_diff = array_diff($checked,$group_selected );

						foreach($arra_diff as $a_diff){

							$group_not_selected[] = $a_diff;

						}

					}

					$sql_query .= ' ) ' ;

				}

				else

				{

					$store_unselected[] = $checked;

				}

			}

		}

	  

			

		



		if(!empty($group_not_selected))

		{

			$i = 0;

			foreach ($group_not_selected as $key => $value) {

				$sql_query .= " and ( $wpdb->postmeta.meta_key = 'post_title_tags' AND $wpdb->postmeta.meta_value NOT REGEXP '(^|,)$value(,|$)' ) ";

			}

		}



		$where .= $sql_query;

	}

	//return $where;



	return $where.''.$search_query;

}



function title_filter($where, &$wp_query) {



	global $wpdb;

	

	if(empty($wp_query->query['meta_query']['filter_regex'])) {



		if ($search_term = $wp_query->get('search_component_post_title')) {

			$where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql(like_escape($search_term)) . '%\'';

		}

	}



	return $where;

	

}

// Add Filter for manufactuerer search

function regex_manufactuerer_query($clauses, $wp_query) {



	  global $wpdb;

	  $manufactuerer = $wp_query->get('search_manufactuerer');

	  

	  if(!empty($manufactuerer)) {

		$clauses['join'] .= " LEFT JOIN $wpdb->term_taxonomy  AS tt ON $wpdb->term_relationships.term_taxonomy_id = tt.term_taxonomy_id 

		LEFT JOIN $wpdb->terms AS t ON tt.term_id = t.term_id

		";

		$clauses['where'] .= " AND (( tt.taxonomy IN ('configurator-manufacturer')  AND t.term_id in ($manufactuerer)))";

	}

	

		return $clauses;



}

function group_config_tags_manufacturer($id, $post = '')

{

	$get_posts = get_config_posts_by_comp_id($id, $post);



	$return = [];

	if (isset($get_posts['posts'])) {

		foreach ($get_posts as $get_post) {

			foreach($get_post as $p) {

				$term = wp_get_post_terms($p->ID, 'configurator-manufacturer');

				if (!empty($term)) {

					foreach ($term as $mft) {

						$return['manufacturer'][$mft->term_id] = ['id' => $mft->term_id, 'text' => $mft->name];

					}

				}

			}

		}

	}



	$return['searchrelation'] = [];

	if (isset($get_posts['searchrelation']) && !empty($get_posts['searchrelation'])) {

		$return['searchrelation'] = $get_posts['searchrelation'];

	}



	$post_title_tags = get_term_meta($id, 'post_title_tags', true);



	if (!empty($post_title_tags)) {

		$post_title_tags = json_decode($post_title_tags, true);

		foreach ($post_title_tags as $filter_data) {

			if (!empty($filter_data['tags'])) {

				$filter['title'] = $filter_data['title'][0];

				$filter['show_filter'] = $filter_data['filter_show'];

				$filter['tag'] = get_tags_by_in_ids($filter_data['tags']);

				$return['tags'][] = $filter;

			}

		}

	}

	return $return;

}



function get_tags_by_in_ids($ids)

{

	global $wpdb;



	$tags = $wpdb->get_results("SELECT id,tag as text FROM wp_configurator_tags

	WHERE id IN(" . implode(',', $ids) . ") ");

	$results = (array)$tags;



	return $results;

}



add_action('wp_ajax_search_component_posts', 'search_component_posts');

add_action('wp_ajax_nopriv_search_component_posts', 'search_component_posts');

function search_component_posts()

{

	include 'pages-inner/component-filter-posts.php';

	exit();

}



function get_term_by_id($id)

{

	return get_term($id);

}



add_action('wp_ajax_get_child_component', 'get_child_component');

add_action('wp_ajax_nopriv_get_child_component', 'get_child_component');

function get_child_component()

{

	if ($_POST['type'] == 'child_component') {

		include 'pages-inner/component-children.php';

	} elseif ($_POST['type'] == 'config_child_overview') {

		include 'pages-inner/configurator-child-overview.php';

	}



	exit();

}



function validate_component($post)

{

  

	global $wpdb;



	if (!empty($post['configurator_id']) && !empty($post['component']) && !empty($post['relation'])) {

	

		$reltion_arr = str_replace("\\", "", $post['relation']);

		$reltion_arr = json_decode($reltion_arr, true);



		$component_validate = [];

		$post_title_tag = [];

	

		$position_one_component = '';

		if (is_array($reltion_arr) && isset($reltion_arr[$post['configurator_id']]) && !empty($reltion_arr[$post['configurator_id']])) {

			foreach ($reltion_arr[$post['configurator_id']] as $key => $value) {

			   

				// foreach ($value as $rel) {

					// validate based on position 1 component

					if ($value['position'] == 1) {

				

						// multiple addon 

						foreach($value['addon'] as $key => $addon) {

						$post_title_tag[$key] = get_post_meta($addon, 'post_title_tags', true);

					   

							if (!empty($post_title_tag)) {

								// multiple addons post title tag

								foreach($post_title_tag as  $title_tag) {

									$title_tags = explode(",", $title_tag);

									foreach ($title_tags as $val) {

										$comp = $wpdb->get_results("SELECT * FROM wp_configurator_validate_condition

											WHERE configurator_id = " . $_POST['configurator_id'] . " and component = " . $value['comp_id'] . " and find_in_set(" . $val . ",filter) ");

										if(empty($comp)) {

											 $comp = $wpdb->get_results("SELECT * FROM wp_configurator_validate_condition

											WHERE configurator_id = " . $_POST['configurator_id'] . " and child_component = " . $value['comp_id'] . " and find_in_set(" . $val . ",filter) ");

										}

								   

										$results = (array)$comp;

										if (isset($results[0]->id)) {

											$component_validate[] = $results;

											break;

										}

									}

								}

							}

						}

					}  

				// }

				// break;

			}

		}

		return $component_validate;

	}

}



//front-end

// get pre-selected components and addons kits

function pre_selected_kits($prod_id, $type = '') {

	$return = [];



	$configurator = get_post_meta($prod_id, 'prefix_kit_configurator', true);



	if ($configurator && $type == '') {

		$return['configurator'] = $configurator;

		$component = get_post_meta($prod_id, 'prefix_kit_component', true);

		if (!empty($component)) {

			$selected_comp = json_decode($component, true);

			if (!empty($selected_comp)) {

				$return['component'] = $selected_comp;

			}

		}



		$child_component = get_post_meta($prod_id, 'prefix_kit_child_component', true);

		if (!empty($child_component)) {

			$selected_c_comp = json_decode($child_component, true);

			if (!empty($selected_c_comp)) {

				$return['child_component'] = $selected_c_comp;

			}

		}



		$addon = get_post_meta($prod_id, 'prefix_kit_addon', true);

		if (!empty($addon)) {

			$addon = json_decode($addon, true);

			$return['addon'] = $addon;

		}

	} elseif ($type == 'lock') {

		$lock = get_post_meta($prod_id, 'prefix_kit_lock_component', true);

		$lock = json_decode($lock, true);

		if (!empty($lock)) {

			$return['lock'] = $lock;

		}

	} elseif ($type == 'quantity') {

		$quantity = get_post_meta($prod_id, 'prefix_kit_quantity_component', true);

		$quantity = json_decode($quantity, true);

		if (!empty($quantity)) {

			$return['quantity'] = $quantity;

		}

	}  elseif ($type == 'addon') {

		$addon = get_post_meta($prod_id, 'prefix_kit_addon', true);

		$addon = json_decode($addon, true);

		if (!empty($addon)) {

			$return['addon'] = $addon;

		}

	} elseif ($type == 'position') {

		$position = get_post_meta($prod_id, 'cprefix_kit_position', true);

		$position = json_decode($position, true);

		if (!empty($position)) {

			$return['position'] = $position;

		}

	}



	return $return;

}



//front-end

add_action('wp_head', 'pre_select_component_addonds');

function pre_select_component_addonds() {

	$product_object = [];

	$kits_object = 'var kits_pre_selected = {};';

	if (isset($_GET['config_link_id']) && !empty($_GET['config_link_id'])) {

		$pre_selected = pre_selected_kits($_GET['config_link_id']);

		$pre_selected_quantity = pre_selected_kits($_GET['config_link_id'],'quantity');

		$pre_selected_position = pre_selected_kits($_GET['config_link_id'],'position');

	

	   

		if (isset($pre_selected['configurator']) && !empty($pre_selected['configurator'])

			&& isset($pre_selected['component']) && !empty($pre_selected['component'])

			&& isset($pre_selected['addon']) && !empty($pre_selected['addon'])) {



			foreach ($pre_selected['component'] as $keys => $comp) {

				// check child multi demensional or not

				if(isset($pre_selected['child_component']) && !empty($pre_selected['child_component'])) {

					if (count($pre_selected['child_component']) != count($pre_selected['child_component'], COUNT_RECURSIVE)) {



						if(!empty($pre_selected['child_component'])) {

						   foreach($pre_selected['child_component'][$comp] as $mi => $multi_child) {

							$pre_child_comp[$multi_child] = $pre_selected['addon'][$multi_child];

							$caddon_qty[] = $pre_selected_quantity['quantity'][$multi_child];

						}

							foreach($pre_child_comp as $key => $comp_addon_id) {

								foreach($comp_addon_id as $comp_child_multi_id) {

									$caddon_price[$key][] = get_post_meta($comp_child_multi_id, 'addon_price', true);

									$caddon_title[$key][] = get_the_title($comp_child_multi_id);

									// $caddon_qty[$key][] =  $pre_selected_quantity['quantity'][$multi_child][0];;



								}

							}

							foreach($pre_selected['child_component'][$comp] as $mi => $multi_sub_child) {

								$product_object[$pre_selected['configurator']][$multi_sub_child] = [

									'comp_id' => $multi_sub_child,

									'addon' => $pre_child_comp[$multi_sub_child],

									'price' => $caddon_price[$multi_sub_child],

									'position' => $pre_selected_position['position'][$keys],

									'qty' => $caddon_qty[$mi],

									'title' => $caddon_title[$multi_sub_child]

								];





							}

						}

					}   else {

						if(isset($pre_selected['child_component'][$comp]) && 

							!empty($pre_selected['child_component'][$comp]) && 

							isset($pre_selected['addon'][$pre_selected['child_component'][$comp]]) && 

							!empty($pre_selected['addon'][$pre_selected['child_component'][$comp]])) {



							$pre_child_comp = $pre_selected['addon'][$pre_selected['child_component'][$comp]];

						 

							$counter = 0;

							$caddon_price = [];

							$caddon_title = [];

							$caddon_qty = $pre_selected_quantity['quantity'][$comp];

							// child addons price and title

							foreach($pre_child_comp as $key => $comp_addon_id) {



								$caddon_price[] = get_post_meta($comp_addon_id, 'addon_price', true);

								$caddon_title[] = get_the_title($comp_addon_id);

								// $caddon_qty[$key][] =   $pre_selected_quantity['quantity'][$comp][0];



							}



							$product_object[$pre_selected['configurator']][$pre_selected['child_component'][$comp]] = [

								'comp_id' => $pre_selected['child_component'][$comp],

								'addon' => $pre_child_comp,

								'price' => $caddon_price,

								'position' => $pre_selected_position['position'][$keys],

								'qty' => $caddon_qty,

								'title' => $caddon_title

							];



						} 

					} 

				} else if(isset($pre_selected['addon'][$comp]) && !empty($pre_selected['addon'][$comp])) {

					   

							$pre_select_comp = $pre_selected['addon'][$comp];

							$caddon_price = [];

							$caddon_title = [];

							$caddon_qty = $pre_selected_quantity['quantity'][$comp];

							// kits multiple addons

							foreach($pre_select_comp as $ki => $comp_addon_id) {

								$caddon_price[] = get_post_meta($comp_addon_id, 'addon_price', true);

								$caddon_title[] = get_the_title($comp_addon_id);

								// $caddon_qty[] = $pre_selected_quantity['quantity'][$comp][0];

							}

							$product_object[$pre_selected['configurator']][$comp] = [

								'comp_id' => $comp,

								'addon' => $pre_select_comp,

								'price' => $caddon_price,

								'position' => $pre_selected_position['position'][$keys],

								'qty' => $caddon_qty,

								'title' => $caddon_title

							];

						} 

			  

			}

		}

		if (!empty($product_object)) {

			$kits_object = 'var kits_pre_selected = {"kits":' . json_encode($product_object) . ', "config_link_id":"' . $_GET['config_link_id'] . '"};';

		}

	}



   

	$config_oject = get_config_default_product();



	$configurator_object = 'var config_pre_selected = {"configurator":' . json_encode($config_oject) . '};';



	echo "<script type='text/javascript'>\n";

	echo "/* <![CDATA[ */\n";

	echo $kits_object;

	echo "\n";

	echo $configurator_object;

	echo "\n/* ]]> */\n";

	echo "</script>\n";

}



//configrator items addd to cart



add_action('wp_ajax_configurator_cart', 'configurator_cart');

add_action('wp_ajax_nopriv_configurator_cart', 'configurator_cart');

function configurator_cart() {

	global $woocommerce;

	$product_cart_key = $_POST['edit_product'];



	if(isset($product_cart_key) && !empty($product_cart_key)) {

		foreach ( WC()->cart->get_cart() as $key => $cart_item ){

			if($key == $product_cart_key) {

				WC()->cart->remove_cart_item($product_cart_key);

			}

		}

	}

  

	$post = $_POST;

	$qty = 1;

	$woocommerce->cart->add_to_cart($post['product_id'], $qty);



}



// Logic to Save products custom fields values into the cart item data

//This captures additional posted information (all sent in one array)

add_filter('woocommerce_add_cart_item_data', 'configurator_add_item_data', 1, 10);

function configurator_add_item_data($cart_item_data, $product_id)

{



	if (!isset($_POST['configurator_detail']) && isset($_POST['product_id'])) {



		$new_value = array();



		$pre_selected = pre_selected_kits($_POST['product_id']);

		$pre_selected_qty = pre_selected_kits($_POST['product_id'],'quantity');

		$pre_selected_addon = pre_selected_kits($_POST['product_id'],'addon');

		

	   

		





		if (isset($pre_selected['configurator']) && !empty($pre_selected['configurator'])

			&& isset($pre_selected['component']) && !empty($pre_selected['component'])

			&& isset($pre_selected['addon']) && !empty($pre_selected['addon'])) {

			foreach ($pre_selected['component'] as $comp) {



				if(isset($pre_selected['child_component']) && !empty($pre_selected['child_component'])) {



					if (isset($pre_selected['addon'][$pre_selected['child_component'][$comp]]) && !empty($pre_selected['addon'][$pre_selected['child_component'][$comp]])) {

						foreach($pre_selected['addon'][$pre_selected['child_component'][$comp]] as $pre_comp) {

						  

							$new_value['config_component_object'][$pre_selected['child_component'][$comp]][] = [

								'component_name' => get_term_by_id($comp)->name,

								'addon_name' => get_the_title($pre_comp),

								'addon_price' => get_post_meta($pre_comp, 'addon_price', true),

								'qty' => 10,

								'total_price' => get_post_meta($pre_comp, 'addon_price', true),

							];

						}

					} else {

						foreach($pre_selected['child_component'][$comp] as $child_comp) {

							foreach($pre_selected['addon'][$child_comp] as $add_p) {

								$price = get_post_meta($add_p, 'addon_price', true);

								$total_price = 0;

								if ($price > 0) {

									$total_price = $price *1;

								}

								$new_value['config_component_object'][$comp][] = [

									'component_name' => get_term_by_id($comp)->name,

									'addon_name' => get_the_title($add_p),

									'addon_price' => $price,

									'qty' => 1,

									'total_price' => $total_price,

								];

							}

							

								  

						}

					}

				} else {

			   

						if (isset($pre_selected['addon'][$comp]) && !empty($pre_selected['addon'][$comp])) {

							foreach($pre_selected['addon'][$comp] as $i => $pre_comp) {

									$price = get_post_meta($pre_comp, 'addon_price', true);

									$total_price = 0;

									if ($price > 0) {

									$total_price = $price * $pre_selected_qty['quantity'][$comp][0];

									}

								  

								  





								$new_value['config_component_object'][$comp][] = [

									'component_name' => get_term_by_id($comp)->name,

									'addon_name' => get_the_title($pre_comp),

									'addon_price' => $price,

									'qty' => $pre_selected_qty['quantity'][$comp][0],

									'total_price' => $total_price,

								];

							}

						} 

				}

			}



			

		}

	} else {



		$reltion_arr = str_replace("\\", "", $_POST['configurator_detail']);

		$config_cart = json_decode($reltion_arr, true);

		 

		// current config id

		$config_id = $_POST['selected_configurator'];



		if (isset($config_cart[$config_id])) {

			$new_value = array();

			foreach ($config_cart[$config_id] as $key => $value) {

			 // multiple addons in at to cart

				foreach($value['addon'] as $i => $addon) {

					$price = get_post_meta($addon, 'addon_price', true);

					$total_price = 0;

					if ($price > 0) {

						$total_price = $price * $value['qty'][$i];

					}

					$new_value['config_component_object'][$key][] = [

						'component_name' => get_term_by_id($key)->name,

						'addon_name' => get_the_title($addon),

						'addon_price' => $price,

						'qty' => $value['qty'][$i],

						'total_price' => $total_price,

					];



				}

			}



			$new_value['config_object'] = $config_cart;

		}

	}



  

	if (empty($cart_item_data)) {

		return $new_value;

	} else {



		return array_merge($cart_item_data, $new_value);

	}

}



//This captures the information from the previous function and attaches it to the item.

add_filter('woocommerce_get_cart_item_from_session', 'configurator_get_cart_items_from_session', 1, 3);

function configurator_get_cart_items_from_session($item, $values, $key)

{

	if (array_key_exists('config_component_object', $values)) {

		$item['config_component_object'] = $values['config_component_object'];

	}

	return $item;

}



//This displays extra information on cart & checkout from within the added info that was attached to the item.

add_filter('woocommerce_cart_item_name', 'configurator_add_usr_custom_session', 1, 3);

function configurator_add_usr_custom_session($product_name, $values, $cart_item_key)

{



	$html = '';

	$html .= '<div>';

	if(isset($values['config_component_object']))

	{



		// multiple addons component, name, price 

		foreach ($values['config_component_object'] as $comp_obj) {

			foreach($comp_obj as $obj) {

				$html .= '<div class="component-block-s">';

				$html .= '<span><strong>Component: </strong> ' . $obj['component_name'] . ' </span>';

				$html .= '<span><strong>Product: </strong> ' . $obj['addon_name'] . '</span>';

				$html .= '<span><strong>Price: </strong> ' . $obj['qty'] . ' X ' . config_number_format($obj['addon_price']) . get_woocommerce_currency_symbol() . ' = '. config_number_format($obj['total_price']). get_woocommerce_currency_symbol() . ' </span>';

				$html .= '</div>';

			}



		}

		$html .= '</div>';

	}

	$return_string = $product_name . "<br />" . $html;



	return $return_string;

}

function config_number_format($price) {

   return number_format((float)$price, 2, '.', '');

}

//This adds the information as meta data so that it can be seen as part of the order (to hide any meta data from the customer just start it with an underscore)



add_action('woocommerce_add_order_item_meta', 'wdm_add_values_to_order_item_meta', 1, 2);

function wdm_add_values_to_order_item_meta($item_id, $values)

{

 

	global $woocommerce, $wpdb;

	

	$html = '';

	$html .= '<div>';

	foreach ($values['config_component_object'] as $comp_detail) {

		// multiple component addons checkout details

		foreach($comp_detail as $comp_obj) {

			$html .= '<div class="component-block-s">';

			$html .= '<span><strong>Component</strong> : ' . $comp_obj['component_name'] . ' </span>';

			$html .= '<span><strong>Product:</strong> : ' . $comp_obj['addon_name'] . '</span>';

			$html .= '<span><strong>Price: </strong> ' . $comp_obj['qty'] . ' X ' . $comp_obj['addon_price']. get_woocommerce_currency_symbol() . ' = ' . $comp_obj['total_price']. get_woocommerce_currency_symbol() . ' </span>';

			$html .= '</div>';

		}

	}

	$html .= '</div>';

	wc_add_order_item_meta($item_id, 'order_item_details', $html);

}







//if you want to override the price you can use information saved against the product to do so

add_action('woocommerce_before_calculate_totals', 'update_custom_price', 1, 1);

function update_custom_price($cart_object)

{



	foreach ($cart_object->cart_contents as $cart_item_key => $value) {

  

		if (isset($value['config_component_object'])) {



			$custom_price = 0;

			foreach ($value['config_component_object'] as $cal_price) {

				// addons total price sum

				foreach($cal_price as $price) {

					$custom_price += (float)$price['total_price'];

				}

			}

			if ($custom_price > 0) {

				$value['data']->set_price($custom_price);

			}

		}

	}

	wc_cart_custom_discount_html();

}



//front-end

//get the defined product for configurators

function get_config_default_product()

{

	global $wpdb;

	$conditions = $wpdb->get_results("SELECT * FROM wp_configurator_settings");



	return $conditions;

}



function surveillan_cart_redirect_url() {

	return esc_url('/');

}

add_filter('woocommerce_return_to_shop_redirect', 'surveillan_cart_redirect_url' );



add_action('restrict_manage_posts','addons_category_fitler');

// Show Category filter at configurator post type page

function addons_category_fitler() {

	$screen = get_current_screen();

	if ( $screen->post_type == 'configurator'  ) { 

		$selected_id = @$_GET['configurator_category'];

	  $args = [

			'show_option_all'    => 'Configurator Category',

			'orderby'            => 'ID', 

			'order'              => 'ASC',

			'hide_empty'         => 0, 

			'hierarchical'       => 1, 

			'selected'           => $selected_id,

			'taxonomy'           => 'configurator-category',

			'hide_if_empty'      => false,

			'class'              => 'configurator-category-class',

			'name'               => 'configurator_category'





		];

		wp_dropdown_categories( $args );

	}

}

add_action('restrict_manage_posts','addons_price_fitler');

// Show Price filter at configurator post type page

function addons_price_fitler() {

	$screen = get_current_screen();

	if ($screen->post_type == 'configurator') { ?>

	   <label for="start_date">Price:</label>

		<input type="text"  name="configurator_price" value="<?php echo @$_GET['configurator_price']; ?>">

		<?php

	}

}

// Show Custom Columns add post type configurator



function add_configurator_columns ( $columns ) {

	unset($columns['date']);

	return array_merge ( $columns, [

	   'configurator_category' => __ ('Category'),

	   'configurator_price' => __ ('Price'),

	   'date' => __('Date')

	]);



 }



add_filter ( 'manage_configurator_posts_columns', 'add_configurator_columns' );



// Add the data to the custom columns for the configurator post type:

add_action( 'manage_configurator_posts_custom_column' , 'custom_configurator_column', 10, 2 );

function custom_configurator_column( $column, $post_id ) {

	switch ( $column ) {

		case 'configurator_category' :

		global $wpdb;

		// Show Category child level

		$component = get_post_meta($post_id);

		if(!empty($component['component_id'][0])) {

			$id = $component['component_id'][0];

		}

		if(isset($component['component_child'][0]) && !empty($component['component_child'][0])) {

			$id = $component['component_child'][0];

		}

		if(!empty($id)) {

			$term = $wpdb->get_row("SELECT name FROM $wpdb->terms where term_id = $id");

			echo $term->name;

		}

		break;

		case 'configurator_price' :

			$addon_price = get_post_meta($post_id, 'addon_price', true);

			echo $addon_price;

			break;

	}

}



add_filter( 'parse_query' , 'configurator_price_filter' );

// price filter for configurator post type

function configurator_price_filter($query) {

	global $pagenow;

	$post_type = 'configurator'; 

	$q_vars    = &$query->query_vars;

	$q_vars['meta_query'] = array();

	if ($pagenow == 'edit.php' && 

		isset($q_vars['post_type']) && 

		$q_vars['post_type'] == $post_type && 

		@$_GET['configurator_price']) {

		$q_vars['meta_query'][] = array(

			'key' => 'addon_price',

			'value' => @$_GET['configurator_price'],

			'compare' => '='

		);

	}

}

add_filter( 'parse_query' , 'configurator_category_filter' );



// category filter for configurator post type

function configurator_category_filter($query) {



	global $pagenow, $wpdb;

	$post_type = 'configurator'; 

	$configurator_category = @$_GET['configurator_category'];

	$tmeta_key = $wpdb->get_row("SELECT meta_key FROM $wpdb->postmeta where meta_value = $configurator_category");



   

		  

	$q_vars    = &$query->query_vars;



	$q_vars['meta_query'] = array();



	$key = @$tmeta_key->meta_key;

	if($key != 'component_id') {

		$key = 'component_child';

	} 

	

   



	if ($pagenow == 'edit.php' && 

		isset($q_vars['post_type']) && 

		$q_vars['post_type'] == $post_type && 

		@$_GET['configurator_category']) {

		$q_vars['meta_query'][] = array(

			'key' => $key,

			'value' => @$_GET['configurator_category'],

			'compare' => '='

		);

	}



}

// Update cart counter request ajax

add_action('wp_ajax_cart_count_retriever', 'cart_count_retriever');

add_action('wp_ajax_nopriv_cart_count_retriever', 'cart_count_retriever');

function cart_count_retriever() {

	global $wpdb;

	$product_cart_key = $_POST['edit_product'];



 

	if(empty($product_cart_key)) {

	   

		echo WC()->cart->get_cart_contents_count()+1;

		wp_die();

	} else {

		echo WC()->cart->get_cart_contents_count();

		wp_die();

	}

}



// get print data sheet and save in session

function print_data_sheet_ajax() {

	global $wpdb;

 

	$post_id = $_POST['current_comp_id'];

  

   

	

	$index = 0;

	$url = '';



	$query = new WP_Query(

	[

		'post_type' => 'configurator',

	]);  

	if($query->have_posts()) {

		while ($query->have_posts()) : $query->the_post(); 



		$get_attach = get_post_meta($post_id, 'wp_custom_attachment', true);

		if(isset($get_attach['url']) && !empty($get_attach['url'])) {

			 $url = $get_attach['url'];

		}

		endwhile;

	}

	

	if(!empty($url) && isset($url)) {

		$_SESSION['datasheet_session'] = $post_id;

		$redirect = get_site_url().'/datasheet-overview/';

		$response = array('type' => 'success', 'redirect' => $redirect);

	} else {

		 $response = array('type' => 'error');

	}

	echo json_encode($response);

	exit();

}



add_action('wp_ajax_print_data_sheet_ajax', 'print_data_sheet_ajax');

add_action('wp_ajax_nopriv_print_data_sheet_ajax', 'print_data_sheet_ajax');









// data sheet shortcode

function data_sheet_shortcode(){ ?>

<?php 

	ob_start();  

	



	$post_id = @$_SESSION['datasheet_session'];



 

	?>

	<div class="content-wrapper">

		<div class="container-fluid">

		  

			<div class="row">

				<div class="col-sm-4 col-12">

					<div class="img-col mb-2">

						<a class="datasheet-brand" href="">

							<img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/data-sheet/images/logo-1.png" alt="Logo" >

						</a>

					</div>

				</div>

				<div class="col-sm-8 col-12">

					<div class="row">

						<div class="col-sm-6  pb-2">

						

							<div class="m-list-item ">

								<div class="icon"><img class="scale-with-grid" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/data-sheet/images/experience.svg"></div>

								<div class="content">20 Years <span>Experience</span></div>

							</div>

						

						</div>

						<div class="col-sm-6  pb-2">

						

							<div class="m-list-item ">

								<div class="icon"><img class="scale-with-grid" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/data-sheet/images/positive-vote.svg"></div>

								<div class="content">Made In<span>Germany</span></div>

							</div>

						

						</div>

						<div class="col-sm-6 pb-2">

						

							<div class="m-list-item ">  

								<div class="icon"><img class="scale-with-grid" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/data-sheet/images/delivery.svg"></div>

								<div class="content"> Express <span>Assembly</span></div>

							</div>

						

						</div>

						<div class="col-sm-6 pb-2">

						

							<div class="m-list-item ">  

								<div class="icon"><img class="scale-with-grid" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/data-sheet/images/medal.svg"></div>

								<div class="content"> 4 Years <span>guarantee</span></div>

							</div>

						

						</div>

					</div>

				</div>

			</div>



			<section class="facebook-section">

				<div class="row mt-5">

					<div class="col-sm-6">

						<a class="facebook-link" href="#">

							<div class="facebook">

								<img class="scale-with-grid" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/data-sheet/images/facebook.svg">

								<span>facebook.com/example</span>

							</div>

						</a>

					</div>

					<div class="col-sm-6">

						<a class="facebook-link" href="#">

							<div class="email float-right">

								<img class="scale-with-grid" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/data-sheet/images/ekomi.png">

								<!-- <span>Email</span> -->

							</div>

						</a>

					</div>

				</div>

			</section>



			<section class="custom-pc-section">

				<div class="row">

					<div class="col-sm-12">

						<div class="dark-bg-heading">

						  <h3>Custom PC configuration</h3>

						</div>

					</div>

					<div class="col-sm-6">

						<div class="crox-img">

							<img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/data-sheet/images/no-sign-bg.png">

						</div>

					</div>

					<div class="col-sm-6">

						<div class="progress-section">

							<h3 class="progress-bar-title">ARTICLE NUMBER: indipc</h3>

							<div class="progress">

							  <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>

							</div>

							<span class="label-progressbar">Office</span>

							<div class="progress">

							  <div class="progress-bar" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>

							</div>

							<span class="label-progressbar">Multimedia</span>

							<div class="progress">

							  <div class="progress-bar" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>

							</div>

							<span class="label-progressbar">Gaming</span>



							<div class="progress">

							  <div class="progress-bar" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>

							</div>

							<span class="label-progressbar">Workstation/CAD</span>



							<div class="progress">

							  <div class="progress-bar" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>

							 </div>

						</div>

					</div>

				</div>

			</section>



			<section class="component-overview mt-5">

				<div class="row">

					<div class="col-sm-12">

						<div class="dark-bg-heading">

						  <h3>Component Overview</h3>

						</div>

					</div>

					<?php if(!empty($post_id)) { ?>

					<div class="col-sm-12">

						<div class="components-block">



							<?php 



							

							global $wpdb;



						  

							$get_attach = get_post_meta($post_id, 'wp_custom_attachment', true);



							$component_id = get_post_meta($post_id, 'component_id', true);

							$post_data = $wpdb->get_row("SELECT * FROM $wpdb->posts where ID = $post_id ");



							if(isset($get_attach['url']) && !empty($get_attach['url'])) { ?>

								 <?php 



									$term = $wpdb->get_row("SELECT name FROM $wpdb->terms where term_id = $component_id ");

									

								 ?>

										<div class="component clearfix">

											<div class="image">

												<?php 

											$url = wp_get_attachment_url(get_post_thumbnail_id($post_id));

											if (!empty($url)) {

											$image = $url;

											} else {

											$image = get_stylesheet_directory_uri()."/assets/images/no-image.png";

											} ?>



												<img src="<?php echo $image; ?>" alt="Cardreader intern">

											</div>

										   

											<div class="title">

												<h6><?php echo $term->name; ?></h6>

												<h4>

													 <a href="javascript:void(0)">             

														<span class="optionName"><?php echo $post_data->post_title; ?></span>

													</a>

													<a data-toggle="collapse" data-target="#component_content_1">▼</a>

													

														<a class="button-style view-button-s" href="<?php echo $get_attach['url']; ?>" target='_blank'>             

															<span class="optionName">View</span>

														</a>

														<a class="button-style download-button-s" href="<?php echo $get_attach['url']; ?>" download>             

															<span class="optionName">Download</span>

														</a>

											   

												

											   

													

												</h4>  

											   <p id="component_content_1" class="componentName collapse"><?php echo $post_data->post_content; ?></p>

												

											</div>  

											 

											  

											  

										</div>  

							 <?php } ?>                

							 

						</div>

						   

					</div>



				<?php } ?>

					</div>

		   

			</section>

		</div>

	</div>

<?php

return ob_get_clean();

}

add_shortcode( 'data_sheet_shortcode', 'data_sheet_shortcode' );



// share link data insert and update if id exit

function share_link_data() {

	global $wpdb;

	$share_link_id = $_POST['share_link_id'] ? $_POST['share_link_id']: uniqid() ;

	$_SESSION['share_link_id'] = $share_link_id;

	$component_json = $_POST['selected_addons'];

	$table_share = $wpdb->prefix . 'components_share_link';

	   $component_json_clean = str_replace("\\", "", $component_json);

		$table_check_row = $wpdb->get_row("SELECT * FROM $table_share WHERE uuid = '$share_link_id'");

		if(empty($table_check_row)){

			$share_link_data =

			[

				'uuid' => $share_link_id,

				'share_link_data' => $component_json_clean,

			];

			$wpdb->insert($table_share,$share_link_data);

		} else {

			$wpdb->update($table_share, ['share_link_data' => $component_json_clean], ['uuid' => $table_check_row->uuid]);

		}

	$response = array('type' => 'success', 'share_link_id' => $share_link_id, 'site_url' => get_site_url());

	echo json_encode($response);

	exit;

}

add_action('wp_ajax_share_link_data', 'share_link_data');

add_action('wp_ajax_nopriv_share_link_data', 'share_link_data');



add_action('wp_head', 'show_share_link_data');

// get data from db and show link for sharing in modal popup

function show_share_link_data() {

	global $wpdb;

	$table_share = $wpdb->prefix . 'components_share_link';

	$components_object = 'var components_object = {};';

	$share_link_id = @$_GET['share_link_id'];
	$kit_share_link_id = @$_GET['kit_share_link_id'];

	if (isset($share_link_id) && !empty($share_link_id)) {

		$addons_share_data = $wpdb->get_row("SELECT * FROM $table_share WHERE uuid = '$share_link_id'");

		// get first component id

		if(!empty($addons_share_data)) {

			foreach(json_decode($addons_share_data->share_link_data) as $selected_component) {

				$store_component_id = array_keys((array)$selected_component)[0]; 

			}

			$components_object = 'var components_object = {"components_data":' . $addons_share_data->share_link_data . ', "share_link_id":"' . $addons_share_data->uuid . '", "selected_component":' . $store_component_id . '};';

		}

	}
	if (isset($kit_share_link_id) && !empty($kit_share_link_id)) {

		$addons_share_data = $wpdb->get_row("SELECT * FROM $table_share WHERE uuid = '$kit_share_link_id'");

		// get first component id

		if(!empty($addons_share_data)) {

			foreach(json_decode($addons_share_data->product_share_link) as $selected_component) {

				$store_component_id = array_keys((array)$selected_component)[0]; 

			}

			$components_object = 'var components_object = {"components_data":' . $addons_share_data->product_share_link . ', "share_link_id":"' . $addons_share_data->uuid . '", "selected_component":' . $store_component_id . '};';

		}

	}

	echo "<script type='text/javascript'>\n";

	echo "/* <![SDATA[ */\n";

	echo $components_object;

	echo "\n/* ]]> */\n";

	echo "</script>\n";

}



// Custom Admin Menu 

add_action( 'admin_menu', 'config_dashboard_setting', 1);

function config_dashboard_setting ()  {

	add_submenu_page('edit.php?post_type=configurator',

		'AI Messages', 'AI Messages',

		'manage_options', 'config_ai_messages',

		'config_ai_messages_function' 

	);

}



// AI message layout

function config_ai_messages_function() { ?>

	<div class="wrap">

		<h1>Configurator AI Message</h1>

		<form method="post" action="options.php">

			<?php settings_fields( 'config-ai-settings-group' ); ?>

			<?php do_settings_sections( 'config-ai-settings-group' ); ?>

			<table class="form-table">

				<tr valign="top">

					<th scope="row">Home Page</th>

					<td>

						<textarea rows="5" cols="80" name="home_page_msg"><?php echo esc_attr( get_option('home_page_msg') ); ?></textarea>

					</td>

				</tr>



				<tr valign="top">

					<th scope="row">Configurator Page</th>

					<td>

						<textarea rows="5" cols="80" name="configurator_page_msg"><?php echo esc_attr( get_option('configurator_page_msg') ); ?></textarea>

					</td>

				</tr>

				<tr valign="top">

					<th scope="row">Kits Page</th>

					<td>

						<textarea rows="5" cols="80" name="kits_page_msg"><?php echo esc_attr( get_option('kits_page_msg') ); ?></textarea>

					</td>

				</tr>

				<tr valign="top">

					<th scope="row">Product Menu Page</th>

					<td>

						<textarea rows="5" cols="80" name="product_menu_page_msg"><?php echo esc_attr( get_option('product_menu_page_msg') ); ?></textarea>

					</td>

				</tr>



				<tr valign="top">

					<th scope="row">Add-ons Pop-Up</th>

					<td>

						<textarea rows="5" cols="80" name="addons_popup_page_msg"><?php echo esc_attr( get_option('addons_popup_page_msg') ); ?></textarea>

					</td>

				</tr>

				<tr valign="top">

					<th scope="row">Add to Cart</th>

					<td>

						<textarea rows="5" cols="80" name="add_to_cart_page_msg"><?php echo esc_attr( get_option('add_to_cart_page_msg') ); ?></textarea>

					</td>

				</tr>

				<tr valign="top">

					<th scope="row">CheckOut Page</th>

					<td>

						<textarea rows="5" cols="80" name="checkout_page_msg"><?php echo esc_attr( get_option('checkout_page_msg') ); ?></textarea>

					</td>

				</tr>

				<tr valign="top">

					<th scope="row">Print Datasheet</th>

					<td>

						<textarea rows="5" cols="80" name="print_datasheet_page_msg"><?php echo esc_attr( get_option('print_datasheet_page_msg') ); ?></textarea>

					</td>

				</tr>

				<tr valign="top">

					<th scope="row">Share Link</th>

					<td>

						<textarea rows="5" cols="80" name="share_link_datasheet_page_msg"><?php echo esc_attr( get_option('share_link_datasheet_page_msg') ); ?></textarea>

					</td>

				</tr>

				<tr valign="top">

					<th scope="row">Disable Messages</th>

					<td>

						<?php $disable_ai_messages =  esc_attr(get_option('disable_ai_messages')); ?>

						<input type="hidden" name="disable_ai_messages"  value="no">

						<input type="checkbox" name="disable_ai_messages"  value="yes" <?php echo $disable_ai_messages == 'yes' ? 'checked' : '' ?>>

					</td>

				</tr>

			</table>



			<?php submit_button(); ?>



		</form>

	</div>

	<?php

}

if ( is_admin() ){ // admin actions

  add_action( 'admin_init', 'register_ai_settings' );

} 

// save in options table

function register_ai_settings() {

	register_setting('config-ai-settings-group', 'home_page_msg');

	register_setting('config-ai-settings-group', 'configurator_page_msg');

	register_setting('config-ai-settings-group', 'kits_page_msg');

	register_setting('config-ai-settings-group', 'product_menu_page_msg');

	register_setting('config-ai-settings-group', 'addons_popup_page_msg');

	register_setting('config-ai-settings-group', 'add_to_cart_page_msg');

	register_setting('config-ai-settings-group', 'checkout_page_msg');

	register_setting('config-ai-settings-group', 'print_datasheet_page_msg');

	register_setting('config-ai-settings-group', 'share_link_datasheet_page_msg');

	register_setting('config-ai-settings-group', 'disable_ai_messages');

 

}



// show ai messages location wise 

function config_ai_messages_show() {

	$show_message = '';

	$current_page = parse_url($_POST['current_page']);

	$home_page = parse_url(get_site_url());

	$configurator_page = parse_url(get_site_url().'/custom-configurator');

	$datasheet_page = parse_url(get_site_url().'/datasheet-overview');

	$checkout_page = parse_url(get_site_url().'/checkout');

	$shop_page = parse_url(get_site_url().'/shop');

	if(!empty($_POST['current_page'])) {

		if($current_page['path'] == $home_page['path']) {

			$show_message = get_option( 'home_page_msg');

		} elseif ($current_page['path'] == $configurator_page['path']) {

			$show_message = get_option( 'configurator_page_msg');

		} elseif ($current_page['path'] == $shop_page['path']) {

			$show_message = get_option( 'kits_page_msg');

		} elseif ($current_page['path'] == $datasheet_page['path']) {

			$show_message = get_option( 'print_datasheet_page_msg');

		} elseif ($current_page['path'] == $checkout_page['path']) {

			$show_message = get_option( 'checkout_page_msg');

		}

	} else {

		$show_message = get_option($_POST['popup_msg']);

	}

  

   echo $show_message;  

   exit();

	

}

add_action('wp_ajax_config_ai_messages_show', 'config_ai_messages_show');

add_action('wp_ajax_nopriv_config_ai_messages_show', 'config_ai_messages_show');





//Get popup markup

function get_popup_markup(){ ?>

	<div class="wrapper popup-wrapper"  style="display: none;">



	  <div class="anim-popwrap">

		  <div class="popup-sec">

			  <a href="javascript:void(0)" class="popup-close"><img src="<?php echo get_stylesheet_directory_uri() . '/assets/images/popup/cross.png' ?>" /></a>

			  <div class="left-wrap">

				  <div class="pop-img">

					  <img src="<?php echo get_stylesheet_directory_uri() . '/assets/images/popup/anim.gif' ?>" class="img-responsive">

				  </div> 

			  </div>

			  <div class="right-wrap">

				 <div class="pop-content">

					 <img src="<?php echo get_stylesheet_directory_uri() . '/assets/images/popup/pointer.png' ?>" class="img-absol">

					 <div class="lg-title">Hi</div>

					 <p class="dynamic_content"> </p>

				 </div> 

			 </div>

			 <div class="pop-check">

				<label><input type="checkbox"> Don't show this again.</label>

			</div>

		</div>

	</div>

</div>

<?php }

add_action('wp_footer','get_popup_markup');





function short_alert_markup(){ ?>

	<div class="alert-box-bg" style="display: none;">

		<div class="popup-alert">

		  <span class="popup-closebtn">&times;</span> 

		  <div class="popup-alert-left-wrap">

			  <div class="popup-alert-img">

				  <img src="<?php echo get_stylesheet_directory_uri() . '/assets/images/popup/anim.gif' ?>" class="img-responsive">

			  </div> 

		  </div>

		  <div class="popup-alert-right-wrap">

			 <div class="popup-alert-content">

				<img src="<?php echo get_stylesheet_directory_uri() . '/assets/images/popup/pointer.png' ?>"> 

				 <div class="popup-alert-lg-title">Hi</div>

				  <p class="popup-dynamic-content"></p>



			 </div> 

		 </div>

	  </div>

  </div>

<?php }

add_action('wp_footer','short_alert_markup');



// Show discount per product

function discount_price_product($product, $qty) {



	global $wpdb;

	$product_id = $product->get_id();

	$price = $product->get_price();



	$row_price  = $price * $qty;



	$user_counter = 'wp_discount_user_counter';

	$configurator_settings = 'wp_configurator_settings';

	$config_sett = $wpdb->get_row("SELECT configurator_id FROM $configurator_settings where product_id = $product_id");



	$config_id = @$config_sett->configurator_id;

	if(isset($config_id) && !empty($config_id)) {

		$config_data = $wpdb->get_row("SELECT post_id FROM $wpdb->postmeta where meta_key = 'config_ids' and meta_value = $config_id");

	

		$user_count_record = $wpdb->get_row("SELECT user_count FROM $user_counter where config_id = $config_id");

		$user_count = @$user_count_record->user_count;

	}  if(!isset($config_id) && empty($config_id)) { 

		$config_id = get_post_meta($product_id, 'prefix_kit_configurator', true);

		$config_data = $wpdb->get_row("SELECT post_id FROM $wpdb->postmeta where meta_key = 'config_ids' and meta_value = $config_id");



		$user_count_record = $wpdb->get_row("SELECT user_count FROM $user_counter where config_id = $config_id");

	}

   

	$comp_post_id = @$config_data->post_id;

	if(isset($comp_post_id) && !empty($comp_post_id)) {

		$discount_expiry = get_post_meta($comp_post_id, 'config_expiry_date', true);

		$coupon_amount = get_post_meta($comp_post_id, 'coupon_amount', true);

		$usage_limit_per_user = get_post_meta($comp_post_id, 'config_usage_limit_per_user', true);

		$discount_type = get_post_meta($comp_post_id, 'config_discount_type', true);

	}



	$cur_date = strtotime(date("Y/m/d"));

	$ex_date = strtotime(@$discount_expiry);

	$discount_price = 0;

   





	

   

	if(isset($usage_limit_per_user) && !empty($usage_limit_per_user)) {

		if($user_count < $usage_limit_per_user) {

			if($cur_date < $ex_date) {

				if($discount_type  == 'percent_discount') {

					$discount_price =  ($coupon_amount/100)*$row_price;

				} elseif($discount_type  == 'fixed_discount') {

				   $discount_price =  $coupon_amount;

			   }

		   }

	   }

	} else {

		if($cur_date < $ex_date) {

			if($discount_type  == 'percent_discount') {

				$discount_price =  ($coupon_amount/100)*$row_price;

			} elseif($discount_type  == 'fixed_discount') {

			 $discount_price =  $coupon_amount;

			}

		}

	}

	

	return number_format((float)floor($discount_price), 2, '.', '');

	

}

// calculate discount configurator

function wc_cart_custom_discount_html(){



	global $wpdb, $woocommerce;

	$product_id = [];

	$config_data = [];

	$discount_expiry = [];

	$coupon_amount = [];

	$usage_limit_per_user = [];

	$discount_type = [];

	$quantity = [];

	$product = [];

	$price = [];

	$clear_price = [];

	// $row_price = $woocommerce->cart->get_cart_contents_total();

   

	$user_counter = 'wp_discount_user_counter';

	foreach(WC()->cart->get_cart() as $cart_item_key => $cart_item ){

		$product[]   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

		$product_id[] = $cart_item['product_id'];

		$quantity[] = $cart_item['quantity'];

	}

   

	$configurator_settings = 'wp_configurator_settings';

	foreach($product_id as $i => $id) {

	$config_sett[] = $wpdb->get_row("SELECT product_id, configurator_id FROM $configurator_settings where product_id = $id");

	  

	}

  

	 

   



	if(!empty(array_filter($config_sett))) {

		 $target_pid = ['2043','2044','2042','2045','2046'];

		 foreach ($config_sett as $key => $config_id) {

			  

			 

			if(isset($config_id) && !empty($config_id)) {

				if(!in_array($config_id->product_id, $product_id)) {

					foreach($product_id as $i => $kit_id) {

						$config_sett[$i] = new StdClass;

						$config_sett[$i]->product_id = $kit_id;

						$config_sett[$i]->configurator_id = get_post_meta($kit_id, 'prefix_kit_configurator', true);

					}

				} else {

					   

					foreach($product_id as $i => $kit_id) {

						

					if(!in_array($kit_id, $target_pid)) {

					 

						$config_sett[$i] = new StdClass;

						$config_sett[$i]->product_id = $kit_id;

						$config_sett[$i]->configurator_id = get_post_meta($kit_id, 'prefix_kit_configurator', true);

						}

					}

				}

			}

		}

	

  

	} elseif(empty(array_filter($config_sett))) {

		

		if(!isset($config_id) && empty($config_id)) {

	   

			foreach($product_id as $i => $kit_id) {

				$config_sett[$i] = new StdClass;

				$config_sett[$i]->product_id = $kit_id;

				$config_sett[$i]->configurator_id = get_post_meta($kit_id, 'prefix_kit_configurator', true);

			

			}

		}

	}



	 

   



	 

	

	foreach ($config_sett as $key => $config_id) {

		if(isset($config_id) && !empty($config_id)) {

			

 

			$price[$config_id->configurator_id][] = $product[$key]->get_price() * $quantity[$key];

			  $config_data[] = $wpdb->get_row("SELECT post_id FROM $wpdb->postmeta where meta_key = 'config_ids' and meta_value = $config_id->configurator_id ");

			  $user_count_record[] = $wpdb->get_row("SELECT user_count FROM $user_counter where config_id = $config_id->configurator_id");

		}

		  

	}





	if(!empty($config_data)) {

	foreach($config_data as $key => $comp_post_id) {

	

			if(isset($comp_post_id->post_id) && !empty($comp_post_id->post_id)) {

				$discount_expiry[] = get_post_meta($comp_post_id->post_id, 'config_expiry_date', true);

				$coupon_amount[] = get_post_meta($comp_post_id->post_id, 'coupon_amount', true);

				$usage_limit_per_user[] = get_post_meta($comp_post_id->post_id, 'config_usage_limit_per_user', true);

				$discount_type[] = get_post_meta($comp_post_id->post_id, 'config_discount_type', true);

				$post_meta_value = check_comp_exit($comp_post_id->post_id);



				$clear_price[] = array_unique($price[$post_meta_value->meta_value]);

			}

		}

	}



	$remove_repeating_value = array_map("unserialize", array_unique(array_map("serialize", $clear_price)));

	 

	

	$cur_date = strtotime(date("Y/m/d"));

 

	$discount_price = 0;

	$clear_priceg = 0;

	if(isset($coupon_amount) && !empty($coupon_amount)) {

		

		foreach($coupon_amount as $key => $amount) { 

		   

			if(!empty($user_count_record[$key]->user_count) && isset($user_count_record[$key]->user_count)) {

				if($user_count_record[$key]->user_count < $usage_limit_per_user[$key]) {

					if($cur_date < strtotime($discount_expiry[$key])) {

						if($discount_type[$key]  == 'percent_discount') {

							if(isset($remove_repeating_value[$key])) {

								foreach($remove_repeating_value[$key] as $priceg) {

									$discount_price +=  ($coupon_amount[$key]/100)*$priceg;

								}

							}

						} elseif($discount_type[$key]  == 'fixed_discount') {

							$discount_price +=  $coupon_amount[$key];

						}

					}

				}

			} else {

				if($cur_date < strtotime($discount_expiry[$key])) {

					if($discount_type[$key]  == 'percent_discount') {

						if(isset($remove_repeating_value[$key])) {

							foreach($remove_repeating_value[$key] as $priceg) {

								 $discount_price +=  ($coupon_amount[$key]/100)*$priceg;

							}

						}

					} elseif($discount_type[$key]  == 'fixed_discount') {

						

						$discount_price +=  $coupon_amount[$key];

					}

				}

			}

		}

	}





	$coupon_code = 'UNIQUECODE'; 

	$amount = floor($discount_price);

	$discount_type = 'fixed_cart'; 

	$coupon_id = 1472;

	$coupon = [

		'ID' => $coupon_id,

		'post_title' => $coupon_code,

		'post_content' => '',

		'post_status' => 'publish',

		'post_author' => 1,

		'post_type'     => 'shop_coupon'

	];    



	$update_coupon = wp_update_post( $coupon );

	// update meta

	update_post_meta( $coupon_id, 'discount_type', $discount_type );

	update_post_meta( $coupon_id, 'coupon_amount', $amount );

	update_post_meta( $coupon_id, 'individual_use', 'no' );

	update_post_meta( $coupon_id, 'product_ids', '' );

	update_post_meta( $coupon_id, 'exclude_product_ids', '' );

	update_post_meta( $coupon_id, 'usage_limit', '' );

	update_post_meta( $coupon_id, 'expiry_date', '' );

	update_post_meta( $coupon_id, 'apply_before_tax', 'yes' );

	update_post_meta( $coupon_id, 'free_shipping', '' );

   

	return number_format((float)floor($discount_price), 2, '.', '');

}

function  check_comp_exit($post_id) {

	global $wpdb;

	$config_data = [];

	return $config_data[] = $wpdb->get_row("SELECT meta_value FROM $wpdb->postmeta where meta_key = 'config_ids' and post_id = $post_id");

   

}



// apply coupon code

add_action('woocommerce_before_cart','configurator_apply_coupon_cart_values');

function configurator_apply_coupon_cart_values(){

	   // static  coupon

	$target_pid = ['2043','2044','2042','2045','2046'];

	if ( !WC()->cart->is_empty() ){

		foreach ( WC()->cart->get_cart() as $cart_item ){



			if(in_array($cart_item['product_id'], $target_pid) ){

				$coupon_code = 'UNIQUECODE';      

				WC()->cart->apply_coupon( $coupon_code );

				wc_clear_notices();

			} if(!in_array($cart_item['product_id'], $target_pid) ){

					$coupon_code = 'UNIQUECODE';      

					WC()->cart->apply_coupon( $coupon_code );

					wc_clear_notices();



			}

		} 

	}     

}



function config_coupon_counter() {

	global $wpdb;

	$user_counter = 'wp_discount_user_counter';

	$configurator_settings = 'wp_configurator_settings';

	foreach(WC()->cart->get_cart() as $cart_item_key => $cart_item ){

		$product_id[] = $cart_item['product_id'];

	}

	foreach($product_id as $i => $id) {

		$config_sett[] = $wpdb->get_row("SELECT configurator_id FROM $configurator_settings where product_id = $id");

	}

	$only_one_counter = array_map("unserialize", array_unique(array_map("serialize", $config_sett)));

	foreach ($only_one_counter as $key => $config_id) {

		$user_count_record[] = $wpdb->get_row("SELECT user_count FROM $user_counter where config_id = $config_id->configurator_id");

		$user_count = @$user_count_record[$key]->user_count;



		if(empty($user_count)){

			$count_user = [

				'config_id' => $config_id->configurator_id,

				'user_count' => 1,

			];

			$wpdb->insert($user_counter,$count_user);

		} else {

			$user_count += 1;

			$wpdb->update($user_counter, 

				['user_count' => $user_count], 

				['config_id' => $config_id->configurator_id]

			);

		}



	}

	wp_die();

}



add_action('wp_ajax_config_coupon_counter', 'config_coupon_counter');

add_action('wp_ajax_nopriv_config_coupon_counter', 'config_coupon_counter');









function cal_config_discount() {

	global $wpdb, $woocommerce;

	$discount_expiry = '';

	$coupon_amount = 0;

	$usage_limit_per_user = 0;

	$discount_type = '';

	$user_counter = 'wp_discount_user_counter';

	$configurator_settings = 'wp_configurator_settings';

	$dis_price_format = 0;

	$sub_total_format = 0;

	$config_id = $_POST['config_id'];

	$total_price = $_POST['total_price'];

	$config_data = $wpdb->get_row("SELECT post_id FROM $wpdb->postmeta where meta_key = 'config_ids' and meta_value = $config_id");

	$user_count_record = $wpdb->get_row("SELECT user_count FROM $user_counter where config_id = $config_id");



	if(!empty($config_data)) {

		if(isset($config_data->post_id) && !empty($config_data->post_id)) {

			$discount_expiry = get_post_meta($config_data->post_id, 'config_expiry_date', true);

			$coupon_amount = get_post_meta($config_data->post_id, 'coupon_amount', true);

			$usage_limit_per_user = get_post_meta($config_data->post_id, 'config_usage_limit_per_user', true);

			$discount_type = get_post_meta($config_data->post_id, 'config_discount_type', true);

		}

		

	}



	$cur_date = strtotime(date("Y/m/d"));

	$discount_price = 0;

	$sub_total = 0;

	$clear_priceg = 0;

	if(isset($coupon_amount) && !empty($coupon_amount)) {

			if(!empty($user_count_record->user_count) && isset($user_count_record->user_count)) {

				if($user_count_record->user_count < $usage_limit_per_user) {

					if($cur_date < strtotime($discount_expiry)) {

						if($discount_type  == 'percent_discount') {

							

							$discount_price +=  ($coupon_amount/100)*$total_price;

							 

					 } elseif($discount_type  == 'fixed_discount') {

						$discount_price +=  $coupon_amount;

					}

				}

			}

		} else {

			if($cur_date < strtotime($discount_expiry)) {

				if($discount_type  == 'percent_discount') {

					$discount_price +=  ($coupon_amount/100)*$total_price;

				} elseif($discount_type  == 'fixed_discount') {

					$discount_price +=  $coupon_amount;

				}

			}

		}

	

	}

	$discount_price = number_format((float)floor($discount_price), 2, '.', '');



	$sub_total = $total_price - $discount_price;

	$sub_total_format = number_format($sub_total,2);

	$dis_price_format = number_format($discount_price,2);

	if($discount_price > 0) {

		$response = array('type' => 'success', 'discount_price' => $dis_price_format, 'sub_total' => $sub_total_format);

	} else {

		 $response = array('type' => 'error', 'discount_price' => $dis_price_format, 'sub_total' => $sub_total_format);

	}

	

	echo json_encode($response);

	wp_die();

  

}



add_action('wp_ajax_cal_config_discount', 'cal_config_discount');

add_action('wp_ajax_nopriv_cal_config_discount', 'cal_config_discount');









function get_config_id_post($product_id) {

	global $wpdb;

	$configurator_settings = 'wp_configurator_settings';

	$config_setting = $wpdb->get_row("SELECT configurator_id FROM $configurator_settings where product_id = $product_id");

	if(isset($config_setting) && !empty($config_setting)) {

		return $config_setting->configurator_id;

	}

	

}



function kit_discount_price($product_id,$total_price) {

	global $wpdb, $woocommerce;

	$discount_expiry = '';

	$coupon_amount = 0;

	$usage_limit_per_user = 0;

	$discount_type = '';

	$user_counter = 'wp_discount_user_counter';

	$configurator_settings = 'wp_configurator_settings';

	$dis_price_format = 0;

	$sub_total_format = 0;

	$config_id = get_post_meta($product_id, 'prefix_kit_configurator', true);

	$config_data = $wpdb->get_row("SELECT post_id FROM $wpdb->postmeta where meta_key = 'config_ids' and meta_value = $config_id");

	$user_count_record = $wpdb->get_row("SELECT user_count FROM $user_counter where config_id = $config_id");



	if(!empty($config_data)) {

		if(isset($config_data->post_id) && !empty($config_data->post_id)) {

			$discount_expiry = get_post_meta($config_data->post_id, 'config_expiry_date', true);

			$coupon_amount = get_post_meta($config_data->post_id, 'coupon_amount', true);

			$usage_limit_per_user = get_post_meta($config_data->post_id, 'config_usage_limit_per_user', true);

			$discount_type = get_post_meta($config_data->post_id, 'config_discount_type', true);

		}

		

	}



	$cur_date = strtotime(date("Y/m/d"));

	$discount_price = 0;

	$sub_total = 0;

	$clear_priceg = 0;

	if(isset($coupon_amount) && !empty($coupon_amount)) {

			if(!empty($user_count_record->user_count) && isset($user_count_record->user_count)) {

				if($user_count_record->user_count < $usage_limit_per_user) {

					if($cur_date < strtotime($discount_expiry)) {

						if($discount_type  == 'percent_discount') {

							

							$discount_price +=  ($coupon_amount/100)*$total_price;

							 

					 } elseif($discount_type  == 'fixed_discount') {

						$discount_price +=  $coupon_amount;

					}

				}

			}

		} else {

			if($cur_date < strtotime($discount_expiry)) {

				if($discount_type  == 'percent_discount') {

					$discount_price +=  ($coupon_amount/100)*$total_price;

				} elseif($discount_type  == 'fixed_discount') {

					$discount_price +=  $coupon_amount;

				}

			}

		}

	

	}

	$discount_price = number_format((float)floor($discount_price), 2, '.', '');

	$sub_total = $total_price - $discount_price;

	$sub_total_format = number_format($sub_total, 2);

	return $sub_total_format;

  

}

function download_configurator_pdf() {
	global $wpdb;
	$term_id = $_POST['current_configurator_id'];
	$get_attach = get_term_meta( $term_id, 'pdf-guide', true );
	if(!empty($get_attach['url']) && isset($get_attach['url'])) {
		$redirect = $get_attach['url'];
		$response = array('type' => 'success', 'attachment' => $get_attach['url']);
	} else {
		 $response = array('type' => 'error');
	}
	echo json_encode($response);
	exit();
}
add_action('wp_ajax_download_configurator_pdf', 'download_configurator_pdf');
add_action('wp_ajax_nopriv_download_configurator_pdf', 'download_configurator_pdf');


add_filter( 'views_edit-configurator', 'configurator_add_button_to_views' );
function configurator_add_button_to_views( $views )
{
	$views['csv-file'] = '<input type="file" name="configurator_csv" class="configurator_csv " id="config_csv_file" />';
	$views['csv-button'] = '<button class="button button-primary configurator_csv" id="update-from-provider" type="button"   style="margin:10px">Import Csv</button>';
	$views['csv-button2'] = '<button class="button button-primary configurator_csv" id="export-provider" type="button"   style="margin:10px">Export Csv</button>';

	return $views;
}



function upload_configurator_csv() {
	global $wpdb;



	if(isset($_FILES['file']['name'])){
		$handle = fopen($_FILES['file']['tmp_name'],"r");
		$row = 0;
		$skip_row_number = array("1");
		while (($data = fgetcsv($handle)) !== FALSE) {
		   $row++;
		   $num = count($data);  
			if(in_array($row, $skip_row_number)) {
				continue;
			} else {
			$title = $data[0];
			$content = $data[1];
			$price = $data[2];
			$image_url = $data[3];
			$cateogry_term = $data[4];
			$manufacturer_term = $data[5];
			$term = explode('>',$cateogry_term);
		   
		   
			$tparent_name = get_term_by('slug', $term[0], 'configurator-category');
			$tchild_name = get_term_by('slug', $term[1], 'configurator-category');



			
	 
			
		  

			$filename = basename($image_url);
			$wp_filetype = wp_check_filetype($filename, null);
			$arr = [];
			$post_id = wp_insert_post( array(
				'post_mime_type' => $wp_filetype['type'],
				'post_title' => $title,
				'post_content' => $content,
				'post_type' => 'configurator',
				'post_date' => date('Y-m-d H:i:s'),
				'post_status' => 'publish'
			));
				update_post_meta( $post_id, 'addon_price', $price );
				$image = media_sideload_image( $image_url, $post_id, '','id');

				set_post_thumbnail( $post_id, $image );
				
				if(isset($term[2]) && !empty($term[2])) {
					$stchild_name = get_term_by('slug', $term[2], 'configurator-category');
					update_post_meta( $post_id, 'configurator_component', $stchild_name->term_id );
					update_post_meta( $post_id, 'component_id', $stchild_name->term_id );
					$arr = [$tparent_name->term_id,$tchild_name->term_id,$stchild_name->term_id];
				} else {
				   update_post_meta( $post_id, 'configurator_component', $tchild_name->term_id );
				   update_post_meta( $post_id, 'component_id', $tchild_name->term_id );
				   $arr = [$tparent_name->term_id,$tchild_name->term_id];
				}

				wp_set_post_terms( $post_id, $arr, 'configurator-category'  );
				update_post_meta( $post_id, 'configurator_id', $tparent_name->term_id );
				if(isset($manufacturer_term) && !empty($manufacturer_term)) {
					$manufacturer_slug = get_term_by('slug', $manufacturer_term, 'configurator-manufacturer');
					update_post_meta( $post_id, 'configurator-manufacturer', $manufacturer_slug->term_id );
				}
				
				
			}
		}
		fclose($handle);
	}
	$response = array('type' => 'success');
	echo json_encode($response);
	exit();
}
add_action('wp_ajax_upload_configurator_csv', 'upload_configurator_csv');
add_action('wp_ajax_nopriv_upload_configurator_csv', 'upload_configurator_csv');
add_action( 'admin_head-edit.php', 'configurator_move_custom_button' );


function config_t_top_mparent( $term, $taxonomy ) {
	// Start from the current term
	$parent  = get_term( $term, $taxonomy );
	// Climb up the hierarchy until we reach a term with parent = '0'
	while ( $parent->parent != '0' ) {
		$term_id = $parent->parent;
		$parent  = get_term( $term_id, $taxonomy);
	}
	return $parent;
}


function configurator_move_custom_button(  )
{
	global $current_screen;
	// Not our post type, exit earlier
	if( 'configurator' != $current_screen->post_type )
		return;
	?>
	<style>
		#config_csv_file {
			margin-top: 10px;
			cursor: pointer;
			width: 16%;
		}
	</style>
	<script type="text/javascript">
		jQuery(document).ready( function($) 
		{
			$('.configurator_csv').prependTo('#posts-filter'); 
			$('#posts-filter').attr('enctype',"multipart/form-data");  
		});     
	</script>
	<?php 
}

function export_csv_config() {
	$delimiter = ","; 
	$f = fopen('php://memory', 'w'); 
	$fields = array('Title', 'Content', 'Price', 'Feature Image'); 
	fputcsv($f, $fields);
	$args = array(  
		'post_type' => 'configurator',
		'post_status' => 'publish',
		'posts_per_page' => -1, 
		'orderby' => 'ID', 
		'order' => 'ASC',
	);
	$post_type_arr = [];
	$loop = new WP_Query( $args ); 
	while ( $loop->have_posts() ) : $loop->the_post(); 
		$featured_img = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID  ));
		$line_data = [
			get_the_title($post->ID), 
			get_the_content($post->ID), 
			get_post_meta($config_data->post_id, 'addon_price', true), 
			$featured_img[0], 
		]; 
	fputcsv($f, $line_data, $delimiter); 
	 endwhile;
	 wp_reset_postdata();
	fseek($f, 0); 
	 
	//output all remaining data on a file pointer 
	fpassthru($f); 

	echo stream_get_contents($f);
	wp_die();
 
   
}
add_action( 'wp_ajax_export_csv_config', 'export_csv_config' );
add_action("wp_ajax_nopriv_export_csv_config", "export_csv_config");



// Send PreOrder mail to admin
function share_link_admin_data() {
	global $wpdb;
	$curr_user = $_SESSION['curr_user'];
	$customer_email = $_POST['customer_email'];
	$table_share = $wpdb->prefix . 'components_share_link';
	$table_data = $wpdb->get_results("SELECT * FROM $table_share WHERE curr_user = '$curr_user'");
	$config_msg = '';
	foreach($table_data as $data) {
			$decode_comp = json_decode($data->share_link_data,true);
			$component_keys = array_keys($decode_comp);
			$component_name = [];
			foreach($component_keys as $com) {
				$component_name[] = get_term($com)->name;
			}
			$im_comp_name = implode(", ",$component_name);
			$link = get_site_url().'/custom-configurator/?share_link_id='.$data->uuid;
			$config_msg .= '<p><a href="'.$link.'">'.$link.'</a></p>';
			$config_msg .= '<p>Configurators or Kits that are made: '.$im_comp_name.'</p>';
	}
	
	


		$headers = "From: Configurator Cart Help<noreply@configurator.com>\r\n";
		$headers .= "Cc: ".$customer_email."\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=UTF-8\r\n";  
		

		$email_message = '<html><body>';
		$email_message .= '<p>Hello,</p>';
		$email_message .= '<p>The customer would like to check if his/her configurator/s are ok.</p>';
		$email_message .= $config_msg;
		$email_message .= '<p>Email: '.$customer_email.'</p>';
		$email_message .= '</body></html>';
		
		$admin_email = get_option('pre_order_admin_email');


		$mail_alert = mail($admin_email, 'Configurator Set Help', $email_message, $headers);

		if ($mail_alert) {
			$alert = 'Email sent Successfully!';
		} else {
			$alert = 'some error';
		}
		$response = array('type' => 'success', 'msg' => $alert, 'share_link_id' => $share_link_id);
		echo json_encode($response);
		exit;	
	
}
add_action('wp_ajax_share_link_admin_data', 'share_link_admin_data');
add_action('wp_ajax_nopriv_share_link_admin_data', 'share_link_admin_data');


add_action( 'admin_menu', 'config_email_templates_setting', 1);
function config_email_templates_setting ()  {
	add_submenu_page('edit.php?post_type=configurator', 
		'Email Template', 'Pre Order Email Message', 
		'manage_options', 'config_order_email_template', 
		'config_email_template_function'
	);
}
function config_email_template_function() {
	if(isset($_POST['submit_and_save'])) {
	
		$body_message = $_POST['message_body'];
		$body_message_option_key = 'pre_order_message';
		$pre_order_admin_email = $_POST['pre_order_admin_email'];
		$pre_order_admin_email_key = 'pre_order_admin_email';
		save_email_option_function($body_message, $body_message_option_key,$pre_order_admin_email, $pre_order_admin_email_key);
	}
	?>

	<form method="post" class="summernote">

		<div class="wrap">
			<h1>Pre Order Email Message</h1>
			<form method="post" action="options.php" novalidate="novalidate">

				<table class="form-table" role="presentation">

					<tbody>
						<tr>
							<th scope="row"><label for="blogname">Admin Email</label></th>
							<td><input type="text" class="form-control" name="pre_order_admin_email" value="<?php echo get_option('pre_order_admin_email'); ?>"></td>
						</tr>
						<tr>
							<th scope="row"><label for="blogname">Message</label></th>
							<td><textarea class="form-control "  name="message_body"  rows="15" cols="100"><?php echo get_option('pre_order_message'); ?></textarea></td>
						</tr>
						<tr>
							<th scope="row"><label for="blogname"></label></th>
							<td><input name="submit_and_save" type="submit" class="button button-primary" value="Save" /></td>
						</tr>
				</tbody>
			</table>
			</form>

		</div>
	
<?php }

function save_email_option_function($body_message, $body_message_option_key, $email_value,$email_key){
	//Saving email subject in wp option table
	$body_message = preg_replace('/\\\\/', '', $body_message);
		//Saving email body message in wp option table
	if( ! empty($body_message) ){
		$option_name = $body_message_option_key;
		$new_value = $body_message;
		if ( get_option( $option_name ) !== false ) {
			update_option( $option_name, $new_value );
		} else {
			$deprecated = null;
			$autoload = 'no';
			add_option( $option_name, $new_value, $deprecated, $autoload );
		}
	} 
	if( ! empty($email_value) ){
		$option_email_name = $email_key;
		$email_values = $email_value;
		if ( get_option( $option_email_name ) !== false ) {
			update_option( $option_email_name, $email_values );
		} else {
			$deprecated = null;
			$autoload = 'no';
			add_option( $option_email_name, $email_values, $deprecated, $autoload );
		}
	} 
}

add_action('wp_ajax_cart_share_link', 'cart_share_link');

add_action('wp_ajax_nopriv_cart_share_link', 'cart_share_link');

function cart_share_link() {

	global $wpdb;
	$cart_link_id = $_POST['cart_link_id'] ? $_POST['cart_link_id']: uniqid() ;

	$curr_user = isset($_SESSION['curr_user']) ? $_SESSION['curr_user']: uniqid() ;
	$_SESSION['curr_user'] = $curr_user;
	$component_json = $_POST['selected_addons'];
	$table_share = $wpdb->prefix . 'components_share_link';
	$component_json_clean = str_replace("\\", "", $component_json);
	$table_check_row = $wpdb->get_row("SELECT * FROM $table_share WHERE uuid = '$cart_link_id' AND curr_user = '$curr_user'");

	if(empty($table_check_row)){
		$share_link_data =
		[
			'uuid' => $cart_link_id,
			'share_link_data' => $component_json_clean,
			'curr_user' => $curr_user,
			// 'selected_configurator' => $selected_configurator

		];

		$wpdb->insert($table_share,$share_link_data);

	} else {

		$wpdb->update($table_share, 
			[
				'share_link_data' => $component_json_clean
			], 
			[
				'uuid' => $table_check_row->uuid
			]);

	}
	$response = array('type' => 'success', 'cart_link_id' => $cart_link_id, 'curr_user' => $curr_user);

	echo json_encode($response);

	exit;

}