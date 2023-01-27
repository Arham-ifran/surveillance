<?php
$term_post_tags = get_term_meta($_POST['comp_id'], 'post_title_tags', true);
$check_multi_select = get_term_meta($_POST['comp_id'], 'multi-component-selection', true);
// by default input type checbox
$input_type = 'checkbox';
// if defined multiple addon selection 'NO' then make it radio input type
if(!empty($check_multi_select)) {
    if($check_multi_select == 'no') {
        $input_type = 'radio';
    }
}
$store_ids_filter_show = [];
if(!empty($term_post_tags)) {
    $term_post_arr = json_decode($term_post_tags);
    foreach($term_post_arr as $tag_post) {
        if (!empty($tag_post->tags)) {
            foreach($tag_post->tags as $tag) {
                
                $store_ids_filter_show[$tag] = $tag_post->filter_show;
            } 
        }
    }
}

// check store tag array empty then get title tag using comp_id when click at any filter
if(empty($store_tag_colors)) {
    if(!empty($term_post_tags)) {
        $term_post_arr = json_decode($term_post_tags);

        $i = 0;
        foreach($term_post_arr as $tag_post) {
       
            $color = filter_colors($i);
            $i++;

            if (!empty($tag_post->tags)) {
                foreach($tag_post->tags as $tag) {
                    $store_tag_colors[$tag]['background'] = $color['background'];
                    $store_tag_colors[$tag]['color'] = $color['color'];
                } 
            }
        }
    }
}
$add_ons_lists = get_config_posts_by_comp_id($_POST['comp_id'], $_POST);

$component_name = get_term($_POST['comp_id']);
?>
<?php if (!empty($add_ons_lists)): ?>
    <!-- Html for unselected addon -->
    <tr>
        <td class="pro-img">
            <?php 
            $image = get_stylesheet_directory_uri() . "/assets/images/remove.png";
            ?><img class="scale-with-grid" src="<?php echo $image; ?>" />
        </td>
        <td class="pro-img"></td>
        <td class="pro-radio-button"><input  class="unselect-addon" type="radio"  value="<?php echo $_POST['comp_id']; ?>">
        </td>
            <td class="pro-radio-button" colspan="5"> &nbsp; No <?php echo $component_name->name; ?></td>
    </tr>
        <?php

            $i = 0;
            $pre_selected_addons = [];
            $relation_bases = [];
            $decode_relation_bases = [];
            $camera_rel_bases = [];
            if (isset($_POST['config_link_id']) && !empty($_POST['config_link_id'])) {
                $pre_addons = pre_selected_kits($_POST['config_link_id'], 'addon');

                if (isset($pre_addons['addon'][$_POST['comp_id']]) && !empty($pre_addons['addon'][$_POST['comp_id']])) {
                    // multiple addons pre selected
                    foreach($pre_addons['addon'][$_POST['comp_id']] as $caddons) {
                        $pre_selected_addons[$caddons] = $caddons;
                    }
                 
                }
            }
        
            if($_POST['comp_id'] == 56) {
                if (isset($_POST['relation']) && !empty($_POST['relation'])) {

                    $reltion_arr = str_replace("\\", "", $_POST['relation']);

                    $reltion_arr = json_decode($reltion_arr, true);
                    $select_config = $reltion_arr[$_POST['selected_configurator']];
                    foreach($select_config as $skey => $sconfig) {
                        if($skey == 52) {
                            foreach($sconfig['addon'] as $s_camera) {
                                $relation_bases[] = get_post_meta($s_camera, 'relation_bases', true);
                            }
                        }
                    }
                }
            }  
            foreach($relation_bases as $rbases) {
                $decode_relation_bases[] = json_decode($rbases, true);
            }
            foreach($decode_relation_bases as $rel_bases) {
                foreach($rel_bases as $rel_b) {
                    $camera_rel_bases = array_merge($camera_rel_bases, $rel_b); 
                }  
            }
           
                

            foreach ($add_ons_lists['posts'] as $ckey => $addon):
                if($_POST['comp_id'] != 56) {
                    $post_tag_cond = get_post_meta($addon->ID, 'post_title_tags', true);
                    if(!empty($post_tag_cond) && !empty($add_ons_lists['searchrelation'])) {
                        $flag = 0;
                        foreach (explode(",", $post_tag_cond) as $validate) {
                            if (in_array($validate, $add_ons_lists['searchrelation'])) {
                                //goto move_last;
                                $flag = 1;
                                break;
                            }
                        }
                        if(!$flag)
                        {
                            goto move_last;
                        }
                    } elseif (!empty($add_ons_lists['searchrelation'])) {
                        goto move_last;
                    }
                    $chil_comp = get_post_meta($addon->ID,"component_child",true); ?>
                    <tr>
                        <td class="pro-img">
                            <?php
                                $url = wp_get_attachment_url(get_post_thumbnail_id($addon->ID));
                                if ($url && @getimagesize($url)) {
                                    $image = wp_get_attachment_url(get_post_thumbnail_id($addon->ID), array(40, 12));
                                } else {
                                    $image = get_stylesheet_directory_uri() . "/assets/images/no-image.png";
                                }
                            ?>
                            <img class="scale-with-grid" src="<?php echo $image; ?>" />
                        </td>
                        <td class="pro-title-sku"><p><?php echo $addon->addon_sku; ?></p></td>
                        <td class="pro-radio-button">
                        <input data-price="<?php echo get_post_meta($addon->ID, 'addon_price', true); ?>" data-addon="<?php echo $addon->ID; ?>" data-comp="<?php echo $_POST['comp_id']; ?>" data-child-comp="<?php echo $chil_comp;?>" data-config="<?php echo $_POST['selected_configurator']; ?>" class="addon-d" type="<?php echo $input_type; ?>" name="addon" value="<?php echo $addon->ID; ?>" <?php echo $pre_selected_addons[$addon->ID] == $addon->ID ? 'checked' : ''; ?> ></td>
                        <td class="pro-title"><p><?php echo $addon->post_title; ?></p></td>
                        <td>
                            <?php
                                $post_filters = get_post_meta($addon->ID, 'post_title_tags', true);
                                if (!empty($post_filters)) {
                                    $post_filters = explode(',', $post_filters);
                                    foreach ($post_filters as $key_filter => $filters) {
                                        $color = filter_colors($i);
                                        $get_filter = get_tags_by_id($filters);
                                        $bg_color = $store_tag_colors[$get_filter[0]->id]['background'];
                                        $font_color = $store_tag_colors[$get_filter[0]->id]['color'];
                                        $font_text = $get_filter[0]->text;
                                        // check show filter from admin side
                                        if($store_ids_filter_show[$get_filter[0]->id] == 0) {
                                            $html = '';
                                        } else { 
                                            // get color according to their group defined in store tag color array 
                                           $html = '<div class="st-label" style="background: '.$bg_color.'; color: '.$font_color.'">'.$font_text.'</div>';
                                        }
                                        echo $html;
                                    }
                                    $i++;
                                }
                            ?>
                        </td>
                        <td>
                            <input type="number" min="1" class="quantity-d" name="" placeholder="Quantity e.g 1" value="1" />
                        </td>
                        <td>
                            <?php
                                $addon_sale_price = get_post_meta($addon->ID, 'addon_actual_price', true);
                                if($addon_sale_price) {
                                    $formatter = new NumberFormatter('en-US', NumberFormatter::CURRENCY);
                                    echo '<strike>'.$formatter->formatCurrency($addon_sale_price, $wcc), PHP_EOL.'</strike>';
                                }
                                $formatter = new NumberFormatter('en-US', NumberFormatter::CURRENCY);
                                $wcc =  get_option('woocommerce_currency');
                                echo $formatter->formatCurrency(get_post_meta($addon->ID, 'addon_price', true), $wcc), PHP_EOL;
                            ?>
                        </td>
                        <td>
                            <div class="popup-info-slide">
                                <div class="slide-icon">
                                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/info.svg" alt="-" class="scale-with-grid" width="25px">
                                </div>
                                <div class="popup-slide">
                                    <?php echo $addon->post_content; ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                   
     
            <?php } elseif($_POST['comp_id'] == 56) {?> 
                <?php  if(in_array($addon->ID, $camera_rel_bases)): ?>
                    <?php 
                     $post_tag_cond = get_post_meta($addon->ID, 'post_title_tags', true);
                     if(!empty($post_tag_cond) && !empty($add_ons_lists['searchrelation'])) {
                        $flag = 0;
                        foreach (explode(",", $post_tag_cond) as $validate) {
                            if (in_array($validate, $add_ons_lists['searchrelation'])) {

                                $flag = 1;
                                break;
                            }
                        }
                        if(!$flag)
                        {
                            goto move_last;
                        }
                    } elseif (!empty($add_ons_lists['searchrelation'])) {
                        goto move_last;
                    }
                     $chil_comp = get_post_meta($addon->ID,"component_child",true); ?>
                    <tr>
                        <td class="pro-img">
                            <?php
                                $url = wp_get_attachment_url(get_post_thumbnail_id($addon->ID));
                                if ($url && @getimagesize($url)) {
                                    $image = wp_get_attachment_url(get_post_thumbnail_id($addon->ID), array(40, 12));
                                } else {
                                    $image = get_stylesheet_directory_uri() . "/assets/images/no-image.png";
                                }
                            ?>
                            <img class="scale-with-grid" src="<?php echo $image; ?>" />
                        </td>
                        <td class="pro-title-sku"><p><?php echo $addon->addon_sku; ?></p></td>
                        <td class="pro-radio-button">
                        <input data-price="<?php echo get_post_meta($addon->ID, 'addon_price', true); ?>" data-addon="<?php echo $addon->ID; ?>" data-comp="<?php echo $_POST['comp_id']; ?>" data-child-comp="<?php echo $chil_comp;?>" data-config="<?php echo $_POST['selected_configurator']; ?>" class="addon-d" type="<?php echo $input_type; ?>" name="addon" value="<?php echo $addon->ID; ?>" <?php echo $pre_selected_addons[$addon->ID] == $addon->ID ? 'checked' : ''; ?> ></td>
                        <td class="pro-title"><p><?php echo $addon->post_title; ?></p></td>
                        <td>
                            <?php
                                $post_filters = get_post_meta($addon->ID, 'post_title_tags', true);
                                if (!empty($post_filters)) {
                                    $post_filters = explode(',', $post_filters);
                                    foreach ($post_filters as $key_filter => $filters) {
                                        $color = filter_colors($i);
                                        $get_filter = get_tags_by_id($filters);
                                        $bg_color = $store_tag_colors[$get_filter[0]->id]['background'];
                                        $font_color = $store_tag_colors[$get_filter[0]->id]['color'];
                                        $font_text = $get_filter[0]->text;
                                        // check show filter from admin side
                                        if($store_ids_filter_show[$get_filter[0]->id] == 0) {
                                            $html = '';
                                        } else { 
                                            // get color according to their group defined in store tag color array 
                                           $html = '<div class="st-label" style="background: '.$bg_color.'; color: '.$font_color.'">'.$font_text.'</div>';
                                        }
                                        echo $html;
                                    }
                                    $i++;
                                }
                            ?>
                        </td>
                        <td>
                            <input type="number" min="1" class="quantity-d" name="" placeholder="Quantity e.g 1" value="1" />
                        </td>
                        <td>
                            <?php
                                $addon_sale_price = get_post_meta($addon->ID, 'addon_actual_price', true);
                                $wcc =  get_option('woocommerce_currency');
                                if($addon_sale_price) {
                                    $formatter = new NumberFormatter('en-US', NumberFormatter::CURRENCY);
                                    echo '<strike>'.$formatter->formatCurrency($addon_sale_price, $wcc), PHP_EOL.'</strike>';
                                }
                                $formatter = new NumberFormatter('en-US', NumberFormatter::CURRENCY);
                                echo $formatter->formatCurrency(get_post_meta($addon->ID, 'addon_price', true), $wcc), PHP_EOL;
                            ?>
                        </td>
                        <td>
                            <div class="popup-info-slide">
                                <div class="slide-icon">
                                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/info.svg" alt="-" class="scale-with-grid" width="25px">
                                </div>
                                <div class="popup-slide">
                                    <?php echo $addon->post_content; ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                

                <?php  endif; ?>

            <?php } ?>
             <?php move_last:'' ?>     
    <?php endforeach; ?>
<?php else: ?>
    <tr>
        <td colspan="6" class="align-center">No records found!</td>
    </tr>
<?php endif; ?>