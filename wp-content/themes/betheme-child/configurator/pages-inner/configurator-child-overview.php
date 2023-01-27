<?php
if (isset($_POST['comp_id'])) {
    $cat_id = $_POST['comp_id'];
}
?>
<?php $components = get_configutor_child_texonomys($cat_id); ?>
<?php
// check addon exit in local storage or not
if(isset($_POST['relation']) && !empty($_POST['relation'])) {
    $reltion_arr = str_replace("\\", "", $_POST['relation']);
    $reltion_arr = json_decode($reltion_arr, true);
    $current_config_rel = $reltion_arr[$_POST['selected_component']];
    $product_ids = [];
    $component_addons_qty = [];
    $component_addons_addon = [];
    $title = '';
    $qty = '';
    $addon = '';

   

    $selected_child_comp = array_keys($current_config_rel);
     foreach($current_config_rel as $key => $arr_rel) {
            $component_addons_title[$key] = $arr_rel['title'];
            $component_addons_qty[$key] = $arr_rel['qty'];
             $component_addons_addon[$key] = $arr_rel['addon'];
    }
}
?>
<?php if (!empty($components)): ?>
    <div class="config-list">
        <?php foreach ($components as $key => $component): ?>
            <!-- get the addons title related to their component term-->
           <?php 
        
                $title = $component_addons_title[$component->term_id];
                 $qty = $component_addons_qty[$component->term_id];
                 $addon = $component_addons_addon[$component->term_id];
                if (isset($pre_selected_kits['component']) && in_array($component->term_id, $pre_selected_kits['component'])) {
                    $configClass = 'green-highlight';
                } elseif (isset($resp[0][0]->id)) {
                    if (!empty($resp[0][0]->compulsory_component)) {
                        $compulsory = explode(",", $resp[0][0]->compulsory_component);
                        if (in_array($component->term_id, $compulsory)) {
                            $configClass = 'red-highlight';
                        } else {
                            $configClass = 'gray-highlight';
                        }
                    } elseif (!empty($resp[0][0]->optional_component)) {
                        $optional = explode(",", $resp[0][0]->optional_component);
                        if (in_array($component->term_id, $optional)) {
                            $configClass = 'gray-highlight';
                        } else {
                            $configClass = 'red-highlight';
                        }
                    } else {
                        $configClass = 'gray-highlight';
                    }
                } else {
                    $validationType = get_term_meta($component->term_id, 'config-taxonomy-validation', true);
                    if ($validationType == 'yes') {
                        $configClass = 'red-highlight';
                    } elseif ($validationType == 'no' || $validationType == '') {
                        $configClass = 'gray-highlight';
                    }
                }
                if(!empty($selected_child_comp)) {
                    if(in_array($component->term_id, $selected_child_comp)) {
                        $configClass = 'green-highlight';
                    }
                }
                ?>
                <a href="javascript:void(0);" data-comp="<?php echo $component->term_id; ?>" class="config-overview-d <?php echo $configClass; ?> ">
                    <div class="text">
                        <h5 class="print_data_sheet"><?php echo $component->name; ?> :</h5>
                        <small><?php foreach($title as $i => $t) {
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
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p class="align-center">No record found!</p>
<?php endif; ?>