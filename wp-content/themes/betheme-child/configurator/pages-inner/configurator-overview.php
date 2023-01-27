<?php

if(isset($_POST['relation']) && !empty($_POST['relation'])) {
    $reltion_arr = str_replace("\\", "", $_POST['relation']);
    $reltion_arr = json_decode($reltion_arr, true);

    $current_config_rel = $reltion_arr[$_POST['configurator_id']];

    // get keys of all selected components in relation array
    $seleced_comp = array_keys($current_config_rel);
    $component_addons_title = [];
    $component_addons_qty = [];
    $component_addons_addon = [];

    $relation_bases = [];
 
 
    $title = '';
    $qty = '';
    $addon = '';

    // get selected product related to their component
   
    foreach($current_config_rel as $key => $arr_rel) {
            $component_addons_title[$key] = $arr_rel['title'];
            $component_addons_qty[$key] = $arr_rel['qty'];
            $component_addons_addon[$key] = $arr_rel['addon'];
    }
 
}


if (isset($current_config_rel) && !empty($current_config_rel)) {

   
    foreach($current_config_rel as $skey => $sconfig) {
        if($skey == 52) {
            foreach($sconfig['addon'] as $s_camera) {
                $relation_bases[] = get_post_meta($s_camera, 'relation_bases', true);
            }
        }
    }
}



if(isset($_POST['configurator_id'])) {
    $cat_id = $_POST['configurator_id'];
}
?>
<?php $components = get_configutor_child_texonomys($cat_id); ?>
<?php if(!empty($components)):?>

<?php

    $lock = [];
    if(isset($_GET['config_link_id']) && !empty($_GET['config_link_id']))
    {
        $check_lock_filter = pre_selected_kits($_GET['config_link_id'],'lock');
        if(isset($check_lock_filter['lock']))
        {
            $lock = $check_lock_filter['lock'];
        }
    }
    else if(!empty($_POST))
    {
        $resp = validate_component($_POST);
    }

 
    $pre_selected_kits = [];
    if(isset($_POST['config_link_id']) && !empty($_POST['config_link_id']))
    {
        $pre_selected_kits = pre_selected_kits($_POST['config_link_id']);
      
    }

 ?>

    <div class="config-list">
        <?php foreach($components as $key=> $component): ?>
            
            <?php

          
                $title = $component_addons_title[$component->term_id];
                $qty = $component_addons_qty[$component->term_id];
                $addon = $component_addons_addon[$component->term_id];
                
                if(isset($pre_selected_kits['component']) && in_array($component->term_id,$pre_selected_kits['component']) ) {
                    $configClass = 'green-highlight';
                } else if(isset($resp[0][0]->id)) {
                    if(!empty($resp[0][0]->compulsory_component))
                    {
                        $compulsory = explode(",",$resp[0][0]->compulsory_component);
                        if(in_array($component->term_id, $compulsory)) {
                            $configClass = 'red-highlight';
                        } else {
                            $configClass = 'gray-highlight';
                        }
                       
                    } else if(!empty($resp[0][0]->optional_component))
                    {
                        $optional = explode(",",$resp[0][0]->optional_component);
                        if(in_array($component->term_id, $optional)){
                            $configClass = 'gray-highlight';
                        } else {
                            $configClass = 'red-highlight';
                        }
                       
                    } else {
                        $configClass = 'gray-highlight';
                    }
                   
                } else {
                    $validationType = get_term_meta( $component->term_id, 'config-taxonomy-validation', true );
                   
                    if( $validationType == 'yes' ) {
                        $configClass = 'red-highlight';
                    } else if( $validationType == 'no' || $validationType == '') {
                        $configClass = 'gray-highlight';
                    }

                }
                // this condition not for kits only for components 
                if(!isset($pre_selected_kits['component'])) {
                    if(in_array($component->term_id, $seleced_comp)) {
                        $configClass = 'green-highlight';
                    }
                }
            ?>
            <?php if($component->term_id != 56) { ?>
            <a href="javascript:void(0);" data-comp="<?php echo $component->term_id; ?>" class="config-overview-d <?php echo $configClass;?> ">
                <div class="text">
                    <h5 ><?php echo $component->name; ?> :</h5>
                    <small><?php foreach($title as $i =>$t) {
                        $get_attach = get_post_meta($addon[$i], 'wp_custom_attachment', true);
                        if(isset($get_attach['url']) && !empty($get_attach['url'])) {
                            $url = $get_attach['url'];
                            $target = '_blank';
                             $class = 'pdf_get';
                        } else {
                            $url = 'javasript:void(0);';
                            $target = '';
                             $class = '';
                        }
                         echo '<span class="'.$class.'" data-url="'.@$url.'" target="'.$target.'">'.$t.'</span> (Qty: '.$qty[$i].')<br>';
                    }; ?></small>
                </div>
            </a>
        <?php } elseif($component->term_id == 56) { 
            if (array_filter($relation_bases)) { ?>
                    <a href="javascript:void(0);" data-comp="<?php echo $component->term_id; ?>" class="config-overview-d <?php echo $configClass;?> ">
                    <div class="text">
                    <h5 ><?php echo $component->name; ?> :</h5>
                    <small><?php foreach($title as $i =>$t) {
                        $get_attach = get_post_meta($addon[$i], 'wp_custom_attachment', true);
                        if(isset($get_attach['url']) && !empty($get_attach['url'])) {
                            $url = $get_attach['url'];
                            $target = '_blank';
                             $class = 'pdf_get';
                        } else {
                            $url = 'javasript:void(0);';
                            $target = '';
                            $class = '';
                        }
                       echo '<span  class="'.$class.'" data-url="'.@$url.'" target="'.$target.'">'.$t.'</span> (Qty: '.$qty[$i].')<br>';
                    }; ?></small>
                </div>
                </a><?php
                    }
                } ?>
        <?php endforeach; ?>
    </div>
<?php else:?>
    <p class="align-center">No record found!</p> 
<?php endif;?>