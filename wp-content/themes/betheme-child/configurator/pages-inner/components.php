<?php
if(isset($_POST['configurator_id'])) {
    $cat_id = $_POST['configurator_id'];
}
$list_component = '';
?>
<?php $components = get_configutor_child_texonomys($cat_id); ?>

<?php
    $pre_selected_kits = [];
    $relation_bases = [];

    if(isset($_POST['config_link_id']) && !empty($_POST['config_link_id']))
    {
        $pre_selected_kits = pre_selected_kits($_POST['config_link_id']);
    }
?>
<?php 


    if (isset($_POST['relation']) && !empty($_POST['relation'])) {

        $reltion_arr = str_replace("\\", "", $_POST['relation']);

        $reltion_arr = json_decode($reltion_arr, true);
        $select_config = $reltion_arr[$_POST['configurator_id']];

        foreach($select_config as $skey => $sconfig) {
            if($skey == 52) {
                foreach($sconfig['addon'] as $s_camera) {
                    $relation_bases[] = get_post_meta($s_camera, 'relation_bases', true);
                }
            }
        }
    }


?>
<?php 
// check addon from local storage exit or not
if(isset($_POST['relation']) && !empty($_POST['relation'])) {
    $reltion_arr = str_replace("\\", "", $_POST['relation']);
    $reltion_arr = json_decode($reltion_arr, true);
    $current_config_rel = $reltion_arr;
    $first_sele_arr = array_keys($current_config_rel[$_POST['configurator_id']]);
    foreach($first_sele_arr as $selec_arr) {
        $term = get_term($selec_arr, 'configurator-category');
        $term_parent[] = ($term->parent == 0) ? $term : get_term($term->parent, 'configurator-category');
    }
    $term_parent_id = [];
    
    foreach($term_parent as $term_data) {
        $term_parent_id[] = $term_data->term_id;
    }
    

}
?>
<?php if(!empty($components)):?>
    <?php $popup = 'addons_popup_page_msg'; ?>
    <div class="sub-cat-list">
       
        <?php foreach($components as $key => $component): ?>
            <?php $image_id = get_term_meta( $component->term_id, 'configurator-taxonomy-image-id', true ); ?>
            <?php
                $check_child_comp = get_configutor_child_texonomys($component->term_id);
                $class = 'component-filter-box';
                $popup = 'addons_popup_page_msg';
                if($check_child_comp) {
                    $class = 'show-child-comp-d';
                    $popup = '';

                }
                if(isset($pre_selected_kits['component']) && in_array($component->term_id,$pre_selected_kits['component']) ) {
                    $class .= ' high-green ';
                }
                
                // if not empty
                if(!empty($term_parent_id)) {
                    // check child parent with component term for add class
                    if(in_array($component->term_id, $term_parent_id)) {
                      $class .= ' high-green ';
                    }
                }
            ?>
            <?php if($component->term_id != 56) { ?>
            <a href="javascript:void(0)" comp-id="<?php echo $component->term_id;?>" class="list-item <?php echo $class;?>" popup-attribute="<?php echo $popup; ?>">
                <div class="icon">
                    <?php
                        $url = wp_get_attachment_image_url($image_id);
                        if ($url && @getimagesize($url)) {
                            $image = wp_get_attachment_image_url($image_id);
                        } else {
                            $image = get_stylesheet_directory_uri()."/assets/images/no-image.png";
                        }
                    ?>
                    <img class="scale-with-grid" src="<?php echo $image;?>">
                </div>
                <h5><?php echo $component->name; ?></h5>
            </a>
        <?php } elseif($component->term_id == 56) { 
                 if (array_filter($relation_bases)) { ?>
                    <a href="javascript:void(0)" comp-id="<?php echo $component->term_id;?>" class="list-item <?php echo $class;?>" popup-attribute="<?php echo $popup; ?>">
                        <div class="icon">
                            <?php
                            $url = wp_get_attachment_image_url($image_id);
                            if ($url && @getimagesize($url)) {
                                $image = wp_get_attachment_image_url($image_id);
                            } else {
                                $image = get_stylesheet_directory_uri()."/assets/images/no-image.png";
                            }
                            ?>
                        <img class="scale-with-grid" src="<?php echo $image;?>">
                        </div>
                        <h5><?php echo $component->name; ?></h5>
                    </a><?php
                    }
                }
                $list_component .= $component->term_id.','; ?>
        <?php endforeach; ?>
        <?php $list_component = rtrim($list_component,","); ?>
    </div>


    <!-- add parernt attribute in child upper div -->
    <div class="sub-cat-list" id="component-children-div-d" parent-comp="" style="display:none;border-top:1px solid;"></div>

<?php else: ?>
    <p class="align-center">No record found!</p>
<?php endif;?>
<input type="hidden" name="list_component" value="<?php echo $list_component;?>" class="list_components_class<?php echo $cat_id; ?>" />