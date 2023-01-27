<?php

add_action('wp_ajax_component_conditions', 'component_conditions');
add_action('wp_ajax_nopriv_component_conditions', 'component_conditions');
function component_conditions()
{
	global $wpdb;
    $id = $_POST['configurator_id'];
    $selected_comp = $wpdb->get_results("SELECT * FROM wp_configurator_validate_condition WHERE configurator_id = $id AND child_component = 0");
    
    $components = get_configutor_child_texonomys($id);
    $source_options = '';
    $sele_option_dis = '';
    $esource_options = '';
    $options = [];
    if (!empty($components)) {
        foreach ($components as $key => $component) {
			if($selected_comp[$key]->component == $component->term_id) {
				$sele_option_dis = 'disabled';
			} else {
				$sele_option_dis = '';
			}
            $source_options .= '<option value="' . $component->term_id . '" ' . $selected . ' '.$sele_option_dis.' >' . $component->name . '</option>';
            $esource_options .= '<option value="' . $component->term_id . '" ' . $selected . '  >' . $component->name . '</option>';
        }
    }
   
    $options['source_components'] = $source_options;
    $options['esource_components'] = $esource_options;
    echo json_encode($options);
    exit();
}

//get componenets tags for admin side [configurator custom post type in post]
add_action('wp_ajax_get_condition_component_tags', 'get_condition_component_tags');
add_action('wp_ajax_nopriv_get_condition_component_tags', 'get_condition_component_tags');
function get_condition_component_tags()
{
	global $wpdb;
    $options = [];
    $parent_com = $_POST['component'];
    $selected_ccomp = $wpdb->get_results("SELECT * FROM wp_configurator_validate_condition WHERE component = $parent_com AND child_component <> 0");
    $get_child_component = get_configutor_child_texonomys($_POST['component']);
    if (!empty($get_child_component)) {
        foreach ($get_child_component as $key => $c_comp) {
        	// disabled child comp if already in list
        	if($selected_ccomp[$key]->child_component == $c_comp->term_id) {
				$sele_child_opt_dis = 'disabled';
			} else {
				$sele_child_opt_dis = '';
			}
            $store['id'] = $c_comp->term_id;
            $store['text'] = $c_comp->name;
            $store['selected'] = false;
            $store['disabled'] = $sele_child_opt_dis;
            $options['child_component'][] = $store;
            
            $estore['id'] = $c_comp->term_id;
            $estore['text'] = $c_comp->name;
            $estore['selected'] = false;
            $estore['disabled'] = $sele_child_opt_dis;
            $options['echild_component'][] = $estore;
        }
    }
	else
	{
		$results = get_tags_by_comp_id($_POST['component']);
		if (!empty($results)) {
			foreach ($results as $res) {
				$store['id'] = $res->id;
				$store['text'] = $res->text;
				$store['selected'] = false;
				$options['component_tags'][] = $store;
				$estore['id'] = $res->id;
				$estore['text'] = $res->text;
				$estore['selected'] = false;
				$options['ecomponent_tags'][] = $estore;
			}
		}
    }
    echo json_encode($options);
    exit();
}

/**
 * Adds a submenu page under a custom post type parent.
 */
add_action('admin_menu', 'configurator_register_ref_page');
function configurator_register_ref_page()
{
    add_submenu_page(
        'edit.php?post_type=configurator',
        __('Add Component Conditions', 'textdomain'),
        __('Validation Conditions', 'textdomain'),
        'manage_options',
        'configurator-shortcode-ref',
        'configurator_ref_page_callback'
    );
}
/**
 * Display callback for the submenu page.
 */
function configurator_ref_page_callback()
{
    global $wpdb;
    if (isset($_POST['save_condition'])) {
        foreach ($_POST['configurator_id'] as $key => $value) {
            $save =
                [
                'configurator_id' => $value[0], //$_POST['post_id'],
                'component' => $_POST['component'][$key][0],
                'child_component' => isset($_POST['child_component'][$key][0]) && !empty(isset($_POST['child_component'][$key][0])) ? $_POST['child_component'][$key][0] : '',
                'filter' => implode(",", $_POST['filter'][$key]),
                'compulsory_component' => implode(",", $_POST['compulsory_component'][$key]),
                'optional_component' => implode(",", $_POST['optional_component'][$key]),
                'created_at' => date('Y-m-d H:i:s'),
            ];
            $wpdb->insert('wp_configurator_validate_condition', $save);
        }
    }
    $conditions = $wpdb->get_results("SELECT * FROM wp_configurator_validate_condition");

    ?>
    <div class="wrap">

		<form method="POST" action="">
			<h1><?php _e('Add Component Conditions', 'textdomain'); ?></h1>
			<!-- Components table start -->
			<div class="components-condition-table mt-2">
				<table class="table wp-list-table widefat fixed striped table-view-list component-conditions">
				<thead>
					<tr>
						<th style="width: 7%;">ID</th>
						<th>Configurator</th>
						<th>Components</th>
						<th>Child Components</th>
						<th>Filters</th>
						<th>Compulsory Component</th>
						<th>Optional Component</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php if (!empty($conditions)): ?>
						<?php foreach ($conditions as $key => $cond): ?>
							<tr>
								<td><?php echo $key + 1; ?></td>
								<td><?php echo get_term_by_id($cond->configurator_id)->name; ?></td>
								<td><?php echo get_term_by_id($cond->component)->name; ?></td>
								<td><?php $c_comp = get_term_by_id($cond->child_component);echo isset($c_comp->name) ? $c_comp->name : ''; ?></td>
								<td>
									<?php
										$filters = get_tags_by_in_ids(explode(",", $cond->filter));
										if (!empty($filters)) {
											foreach ($filters as $key => $fltr) {
												echo '<span>' . $fltr->text . ',</span>';
											}
										}
									?>
								</td>
								<td>
								<?php
									$compulsory_component = explode(",", $cond->compulsory_component);
									if (!empty($compulsory_component)) {
										foreach ($compulsory_component as $key => $value) {
											echo '<span>' . get_term_by_id($value)->name . ',</span>';
										}
									}
								?>
								</td>
								<td>
									<?php
										$optional_component = explode(",", $cond->optional_component);
										if (!empty($optional_component)) {
											foreach ($optional_component as $key => $value) {
												echo '<span>' . get_term_by_id($value)->name . ',</span>';
											}
										}
									?>
								</td>
								<td><a href="javascript:void(0);" class="del-validate-cond-d" id="<?php echo $cond->id; ?>">Delete</a></td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
				</table>
			</div>
			<!--Components table Endhere -->
			<div class="condition-block group-0 condition-block-s" group-id="0">
				<p class="form-field">
					<label class="custom-post-label-block-s"><?php _e('Configurator Types'); ?></label>
					<select class="condition_configurator_id" name="configurator_id[0][]">
						<?php $configurators = get_configutor_texonomys(); ?>
						<?php if (!empty($configurators)): ?>
							<?php foreach ($configurators as $key => $configrator): ?>
								<option value="<?php echo $configrator->term_id; ?>"><?php echo $configrator->name; ?></option>
							<?php endforeach; ?>
						<?php endif; ?>
					</select>
				</p>
				<p class="form-field">
					<label class="custom-post-label-block-s"><?php _e('Components'); ?></label>
					<select  class="condition_configurator_component" name="component[0][]">
						<option value=""></option>
					</select>
				</p>
				<p class="form-field child-comp-block-d" style="display:none;" >
					<label class="custom-post-label-block-s"><?php _e('Child Components'); ?></label>
					<select  class="condition_configurator_child_component" name="child_component[0][]">
						<option value=""></option>
					</select>
				</p>
				<p class="form-field">
					<label class="custom-post-label-block-s">Filters</label>
					<select multiple="multiple"  class="cond_tags cond_tags-s" name="filter[0][]">
						<option value=""></option>
					</select>
				</p>
				<p class="add-new-button-s">
					<a href="javascript:void(0)" class="append-block-d append-block-d-s" >+ Add New</a>
				</p>
				<p class="form-field">
					<label class="custom-post-label-block-s">Compulsory:</label>
					<select  class="condition_compulsory_component" name="compulsory_component[0][]">
						<option value=""></option>
					</select>
				</p>
				<p class="form-field">
					<label class="custom-post-label-block-s">Optional:</label>
					<select  class="condition_optional_component" name="optional_component[0][]">
						<option value=""></option>
					</select>
				</p>
			</div>

			<div class="height:5px:clear:both"></div>
			<!-- <a href="javascript:void(0)" class="append-block-d" >+ Add New</a> -->
			<div class="height:5px;clear:both;"></div>
			<div class="append-block-div-d condition-block-s"></div>
			<input type="submit" name="save_condition" value="Add" />
		</form>
    </div>
<?php
}

//get componenets and tags with relationship for admin side [configurator custom post type in post]
add_action('wp_ajax_delete_validate_cond', 'delete_validate_cond');
add_action('wp_ajax_nopriv_delete_validate_cond', 'delete_validate_cond');
function delete_validate_cond()
{
    global $wpdb;

    $table = 'wp_configurator_validate_condition';
    $wpdb->delete($table, array('id' => $_POST['id']));

    exit();
}

/**
 * Adds a submenu page under a custom post type parent.
 */
add_action('admin_menu', 'configurator_register_settings_page');
function configurator_register_settings_page()
{
    add_submenu_page(
        'edit.php?post_type=configurator',
        __('Add Component Conditions', 'textdomain'),
        __('Settings', 'textdomain'),
        'manage_options',
        'configurator-settings-ref',
        'configurator_settings_page_callback'
    );
}
/**
 * Display callback for the submenu page.
 */
function configurator_settings_page_callback()
{
    global $wpdb;
    $conditions = $wpdb->get_results("SELECT * FROM wp_configurator_settings");
    if (isset($_POST['save_config_settings'])) {
        if (!empty($conditions)) {
            $wpdb->query("TRUNCATE TABLE `wp_configurator_settings`");
        }
        foreach ($_POST['configurator_id'] as $key => $value) {
            $save =
                [
                'product_id' => $_POST['product_ids'][$key],
                'configurator_id' => $value,
            ];
            $wpdb->insert('wp_configurator_settings', $save);
        }
    }
    ?>
		<style>
		.width_50{
			width: 50% !important;
		}
		.condition-block-s .width_50 select{
			width: 50%;
		}
		</style>

    <div class="wrap">
		<form method="POST" action="">
			<h1><?php _e('Configurator product settings', 'textdomain'); ?></h1>
			<?php
$config_option = '';
    foreach (get_configutor_texonomys() as $key => $value) {
        $config_option .= '<option value="' . $value->term_id . '">' . $value->name . '</option>';
    }
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => -1,
    );
    $woo_prod = '';
    $loop = new WP_Query($args);
    while ($loop->have_posts()): $loop->the_post();
        $woo_prod .= '<option value="' . get_the_ID() . '">' . get_the_title() . '</option>';
    endwhile;
    wp_reset_query();
    ?>
			<!--Components table Endhere -->
			<div class="condition-block condition-block-s">
				<?php
				for ($i = 0; $i < 4; $i++) { ?>
					<p class="form-field width_50">
						<label class="custom-post-label-block-s"><?php _e('Configurator Type'); ?></label>
						<select class="config_id-sett" name="configurator_id[]">
							<?php echo $config_option; ?>
						</select>
					</p>
					<p class="form-field width_50">
						<label class="custom-post-label-block-s"><?php _e('Select product'); ?></label>
						<select class="prod_id-sett" name="product_ids[]">
							<?php echo $woo_prod; ?>
						</select>
					</p>
					<div class="height:5px:clear:both"></div>
					<?php if (!empty($conditions)): ?>
						<script>
							jQuery('.config_id-sett:last').val('<?php echo $conditions[$i]->configurator_id; ?>');
							jQuery('.prod_id-sett:last').val('<?php echo $conditions[$i]->product_id; ?>');
						</script>
					<?php endif; ?>
					<?php
				}
				?>
			</div>
			<div class="height:5px:clear:both"></div>
			<input type="submit" name="save_config_settings" value="Save" />
		</form>
    </div>
<?php
}