<?php 

$components = get_configutor_child_texonomys($_POST['comp_id']);
// check addon values exit in local storage or not
if(isset($_POST['relation']) && !empty($_POST['relation'])) {
    $reltion_arr = str_replace("\\", "", $_POST['relation']);
    $reltion_arr = json_decode($reltion_arr, true);
    $current_config_rel = $reltion_arr[$_POST['selected_component']];
    // check if it is kit config
    $selected_child_comp = array_keys($current_config_rel);
}

$pre_selected_kits = [];
if(isset($_POST['config_link_id']) && !empty($_POST['config_link_id']))
{
    $pre_selected_kits = pre_selected_kits($_POST['config_link_id']);
}



$list_component1 = '';
foreach($components as $key => $component): ?>
    <?php $image_id = get_term_meta( $component->term_id, 'configurator-taxonomy-image-id', true ); ?>
    
    <?php
        $class = '';
        if(isset($pre_selected_kits['child_component']) && in_array($component->term_id,$pre_selected_kits['child_component']) ) {
            $class = ' high-green ';
        }
        // if selected child comp not empty
        if(!empty($selected_child_comp)) {
            if(in_array($component->term_id, $selected_child_comp)) {
           
                $class = 'high-green';
            }
        }
    ?>

    <a href="javascript:void(0)" comp-id="<?php echo $component->term_id;?>" class="list-item component-filter-box comp-children-d <?php echo $class;?>" popup-attribute="addons_popup_page_msg">
       
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
    <?php $list_component1 .= $component->term_id.','; ?>
<?php endforeach; ?>
<?php $list_component1 = rtrim($list_component1,","); ?>

<input type="hidden" name="list_component1" value="<?php echo $list_component1;?>" />