<?php



//calculate kits addons price and update product price.

function cw_change_product_price_display($price)

{



    global $product;



    $prod_id = $product->get_id();



    $kit_price = 0;

    if (!empty($prod_id)) {
       
        $pre_selected = pre_selected_kits($prod_id);
       

    

        if (isset($pre_selected['configurator']) && !empty($pre_selected['configurator'])

            && isset($pre_selected['component']) && !empty($pre_selected['component'])

            && isset($pre_selected['addon']) && !empty($pre_selected['addon'])) {
          
            if(isset($pre_selected['child_component']) && !empty($pre_selected['child_component'])) {
                
                foreach ($pre_selected['component'] as $comp) {

                    

                   

                    if (isset($pre_selected['addon'][$pre_selected['child_component'][$comp][0]]) && !empty($pre_selected['addon'][$pre_selected['child_component'][$comp][0]]) && in_array($comp, array_keys($pre_selected['child_component']))) {



                        // multiple child addons price display shop page

                        foreach($pre_selected['addon'] as $i => $pre_add) {

                       

                        foreach($pre_add as $in => $pre_comp) {

    

                           

                            $check_price = get_post_meta($pre_comp, 'addon_price', true);
                           

                           



                            $selected_quantity = get_post_meta($prod_id, 'prefix_kit_quantity_component', true);

                            $selected_quantity = json_decode($selected_quantity, true);

                          

                         

                            $cal_with_quantity = $check_price * $selected_quantity[$i][$in];

                            $total_quantity += $selected_quantity[$i][$in];

                  

                            if ($check_price) {

                                $kit_price += $cal_with_quantity;

                                  

      

                            }

                        }

                    }

                    }

                }

            } else {

                foreach ($pre_selected['component'] as $comp) {

                    if (isset($pre_selected['addon'][$comp]) && !empty($pre_selected['addon'][$comp])) {

                        // multiple addons price display shop page

                        foreach($pre_selected['addon'][$comp] as $in => $pre_comp) {

                            $check_price = get_post_meta($pre_comp, 'addon_price', true);
                            // print_r($check_price);

                            $selected_quantity = get_post_meta($prod_id, 'prefix_kit_quantity_component', true);

                            $selected_quantity = json_decode($selected_quantity, true);

                          

                            $cal_with_quantity = $check_price * $selected_quantity[$comp][$in];
                            

                             $total_quantity += $selected_quantity[$comp][$in];

                            if ($check_price) {

                                $kit_price += $cal_with_quantity;
                               

                            }

                        }

                    }

                }

            }

        }

    }

    // print_r($total_quantity);

    //  echo discount_price_product($product,1);

    // die(print_r($kit_price));

    $discounted_price =  kit_discount_price($product->get_id(),$kit_price);
 

    // die(print_r($kit_price));

    if ($discounted_price > 0) {

        return $discounted_price. get_woocommerce_currency_symbol().' '.get_option( 'woocommerce_price_display_suffix' );

    } else {

        return $price;

    }

}

add_filter('woocommerce_get_price_html', 'cw_change_product_price_display');

//add_filter('woocommerce_cart_item_price', 'cw_change_product_price_display');





//add configurator button product detail page

add_action('woocommerce_after_add_to_cart_button', 'config_add_button_after_add_to_cart');

function config_add_button_after_add_to_cart()

{

    global $product;

    $button = '';

    $host = array('127.0.0.1', "::1");

    if (in_array($_SERVER['REMOTE_ADDR'], $host)) {

        $cofigurator_url = get_site_url() . '/?page_id=142&config_link_id=' . $product->get_id();

    } else {

        $cofigurator_url = get_site_url() . '/?page_id=300&config_link_id=' . $product->get_id();

    }

    $configure = get_post_meta($product->get_id(), 'prefix_kit_configurator_enable', true);

    if ($configure) {

        echo '<a href="'.$cofigurator_url.'" class="button configurator-btn" rel="nofollow">Configure</a>';

    }

}



//add buuton *add to cart and configurtor* in shop and home page plus in loop products everywhere

add_action('woocommerce_after_shop_loop_item', 'config_template_loop_add_to_cart', 10);

function config_template_loop_add_to_cart()

{

    global $product;



    $button = '';

    $host = array('127.0.0.1', "::1");

    if (in_array($_SERVER['REMOTE_ADDR'], $host)) {

        $cofigurator_url = get_site_url() . '/?page_id=142&config_link_id=' . $product->get_id();

    } else {

        $cofigurator_url = get_site_url() . '/?page_id=300&config_link_id=' . $product->get_id();

    }



    $configure = get_post_meta($product->get_id(), 'prefix_kit_configurator_enable', true);

    $class = '';

    if ($configure) {

        $button .= '<a href="' . $cofigurator_url . '" class="button configurator-btn" rel="nofollow">Configure</a>';

    } else {

        $class = ' loop-single-product-s ';

    }



    echo apply_filters('woocommerce_loop_add_to_cart_link',

        sprintf('<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" data-quantity="%s" class="' . $class . ' button %s product_type_%s   %s" popup-attribute="add_to_cart_page_msg">%s</a>',

            esc_url($product->add_to_cart_url()),

            esc_attr($product->get_id()),

            esc_attr($product->get_sku()),

            esc_attr(isset($quantity) ? $quantity : 1),

            $product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button add-to-cart-btn' : '',

            esc_attr($product->get_type()),

            $product->get_type() == 'simple' ? 'ajax_add_to_cart' : '',

            esc_html($product->add_to_cart_text())

        ),

        $product);



    echo $button;

}

remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart');



//short description in shop and home page plus in loop products everywhere

add_action('woocommerce_after_shop_loop_item_title', 'config_short_des_product', 2);

function config_short_des_product()

{

    the_excerpt();

}



/*

 * add Kits section tab heading in woo-commerce

 */

add_filter('woocommerce_product_data_tabs', 'kit_settings_tabs');

function kit_settings_tabs($tabs)

{

    //unset( $tabs['inventory'] );

    $tabs['prefix_kit'] = array(

        'label' => 'Kits',

        'target' => 'kit_section_fields',

        //array( 'show_if_simple', 'show_if_variable','show_if_virtual'  ),

         'class' => array('show_if_simple', 'show_if_variable', 'show_if_virtual'),

        //'priority' => 21,

    );



    return $tabs;

}



/*

 * add Kits section tab content and fields in woo-commerce

 */

add_action('woocommerce_product_data_panels', 'prefix_kit_settings');

function prefix_kit_settings()

{

    global $post;



    echo '<div id="kit_section_fields" class="panel woocommerce_options_panel hidden">';



    //configurator types

    $config_val = get_post_meta($post->ID, 'prefix_kit_configurator', true);

    if (empty($config_val)) {

        $config_val = '';

    }



    $connfigurator = get_configutor_texonomys();

    $config[''] = __('Select value', 'woocommerce'); // default value



    foreach ($connfigurator as $key => $term) {

        $config[$term->term_id] = $term->name;

    }



    echo '<div class="options_group">';



    woocommerce_wp_checkbox(

        [

            'id' => 'prefix_kit_configurator_enable',

            'value' => get_post_meta(get_the_ID(), 'prefix_kit_configurator_enable', true),

            'label' => 'Configurator Enable',

            'desc_tip' => true,

            'description' => 'Enable configurator',

        ]

    );



    woocommerce_wp_select(array(

        'id' => 'prefix_kit_configurator',

        'label' => __('Configurator', 'woocommerce'),

        'options' => $config, //this is where I am having trouble

         'value' => $config_val,

        'class' => '',

        //'custom_attributes' => array('multiple' => 'multiple')

    ));



    echo '</div>';



    echo '<a href="javascript:void(0);"  class="add-kits-block-d add-filter-dest-s button-poisiting-s"><span>+</span></a><br/>';

    echo '<div class="component-addon-block-d"></div>';

    $s_in_kits = get_post_meta($post->ID, '_show_in_kits', true);

    echo '<input type="hidden" value="'.$s_in_kits.'" id="s_kits"/>';

    $component = get_post_meta($post->ID, 'prefix_kit_component', true);

    $child_component = get_post_meta($post->ID, 'prefix_kit_child_component', true);



    if (!empty($config_val) && !empty($component)) {

        $get_comp = get_configutor_child_texonomys($config_val);



        $selected_addon = get_post_meta($post->ID, 'prefix_kit_addon', true);



        $selected_addon = json_decode($selected_addon, true);

        



        $selected_lock = get_post_meta($post->ID, 'prefix_kit_lock_component', true);

        $selected_lock = json_decode($selected_lock, true);



        $selected_quantity = get_post_meta($post->ID, 'prefix_kit_quantity_component', true);

        $selected_quantity = json_decode($selected_quantity, true);



        $cprefix_kit_position = get_post_meta($post->ID, 'cprefix_kit_position', true);

        $cprefix_kit_position = json_decode($cprefix_kit_position, true);



        if (!empty($component) && empty($child_component)) {

            $selected_comp = json_decode($component, true);



            $html = '';

            $html .= '<div class="block-container-d">';

            foreach ($selected_comp as $key => $comp) {



                $component = [];

                $group_id = $key + 1;

                $html .= '<div class="option-group-d option-group-s clearfix group-container-' . $group_id . '" group-id="' . $group_id . '">';

                foreach ($get_comp as $c_key => $term) {

                    $selected = '';

                    if ($comp == $term->term_id) {

                        $selected = 'selected';

                    }



                    if ($c_key < 1) {

                        $html .= '<div class="options_group">';

                        $html .= '<p class="form-field ">';

                        $html .= '<label>Components</label>';

                        $html .= '<select class="prefix-kit-component-d" name="prefix_kit_component[]" group-id="' . $group_id . '">';

                        // $html .= '<option value="">Please select</option>';

                    }



                    $html .= '<option value="' . $term->term_id . '"  ' . $selected . ' >' . $term->name . '</option>';



                    end($get_comp);

                    if ($c_key === key($get_comp)) {

                        $html .= '</select>';

                        $html .= '</p>';

                        $html .= '</div>';

                    }

                }



                $get_child_comp = get_configutor_child_texonomys($comp);



                if (!empty($get_child_comp) && !empty($child_component)) {

                    $selected_c_comp = json_decode($child_component, true);



                    //print_r($selected_c_comp);

                    foreach ($get_child_comp as $cc_key => $c_term) {



                        if ($cc_key < 1) {

                            $html .= '<div class="options_group">';

                            $html .= '<p class="form-field prefix-child-comp-block-d">';

                            $html .= '<label>Child Components</label>';

                            $html .= '<select class="prefix_kit_child_component child-comp-group-' . $group_id . '" name="prefix_kit_child_component[' . $comp . '][]" group-id="' . $group_id . '">';

                            // $html .= '<option value="">Please select</option>';

                        }



                        $selected = '';

                        foreach($selected_c_comp as $select_child_com) {

                           

                            if (isset($select_child_com) && in_array($c_term->term_id, $select_child_com)) {

                                $selected = 'selected';

                                $comp = $c_term->term_id;

                            }

                        }

                        $html .= '<option value="' . $c_term->term_id . '"  ' . $selected . ' >' . $c_term->name . '</option>';



                        end($get_child_comp);

                        if ($cc_key === key($get_child_comp)) {

                            $html .= '</select>';

                            $html .= '</p>';

                            $html .= '</div>';

                        }

                    }

                } else {

                    $html .= '<div class="options_group">';

                    $html .= '<p class="form-field prefix-child-comp-block-d" style="display:none;" >';

                    $html .= '<label class="custom-post-label-block-s">Child Components</label>';

                    $html .= '<select class="prefix_kit_child_component child-comp-group-' . $group_id . '" name="prefix_kit_child_component[][]" group-id="' . $group_id . '">';

                    $html .= '</select>';

                    $html .= '</p>';

                    $html .= '</div>';

                }

                if(isset($cprefix_kit_position[$key]) && !empty($cprefix_kit_position[$key])) {

                    $ccprefix_kit_position = $cprefix_kit_position[$key];

                } else {

                    $ccprefix_kit_position = $group_id;

                }



                 $html .= '<input name="cprefix_kit_position[]" type="hidden" class="cprefix_kit_position" group-id="'.$group_id.'" value="'.$ccprefix_kit_position.'">';



                $get_addons = woo_kit_get_addon_by_id($comp);

                if (!empty($get_addons)) {

                    foreach ($get_addons as $a_key => $addon) {

                        $selctd = '';

                        if(!empty($child_component)) {

                            foreach($selected_addon as $selected_child_addon) {

                                if(in_array($addon->ID, $selected_child_addon)) {

                                    $selctd = 'selected';

                                } 

                            }

                        } else {

                        foreach($selected_addon[$comp] as $caddon) {

                            if (isset($caddon) && $caddon == $addon->ID) {

                                $selctd = 'selected';

                            }

                        }

                    }

                   

                        

                        $check_multi_select = get_term_meta($comp, 'multi-component-selection', true);

                        $multiple_select = 'multiple';

                        

                        

                        

                        if ($a_key < 1) {

                            $html .= '<div class="options_group" style="clear:none; display:inline-block;float:left;     width: 71%;">';

                            $html .= '<p class="form-field ">';

                            $html .= '<label>Addon</label>';

                            $html .= '<select '.$multiple_select.' class=" prefix-kit-addon-d addon-group-' . $group_id . '" name="prefix_kit_addon[' . $comp . '][]">';

                            // $html .= '<option value="">Please select</option>';

                        }



                        $html .= '<option value="' . $addon->ID . '" ' . $selctd . ' >' . $addon->post_title . '</option>';

                        end($get_addons);

                        if ($a_key === key($get_addons)) {

                            $html .= '</select>';

                            $html .= '</p>';

                            $html .= '</div>';

                        }

                    }

                } else {

                    $html .= '<div class="options_group" style="clear:none; display:inline-block;float:left ;    width: 71%;">';

                    $html .= '<p class="form-field ">';

                    $html .= '<label>Addon</label>';

                    $html .= '<select  '.$multiple_select.' class=" prefix-kit-addon-d addon-group-' . $group_id . '" name="prefix_kit_addon[' . $comp . '][]">';

                    // $html .= '<option value="">Please select</option>';

                    $html .= '</select>';

                    $html .= '</p>';

                    $html .= '</div>';

                }



                $lock_checked = '';

                if (isset($selected_lock[$comp][0])) {

                    $lock_checked = 'checked';

                }

                if (isset($selected_quantity[$comp])) {

                    $quantity_value = $selected_quantity[$comp];

                } 

                 $html .= '<div class="options_group" style="clear:none; display:inline-block;float:left;width:25%;"><p class="form-field custom_form_fields"><a style="top:-28px;" href="javascript:void(0);" class="add-quantity-block-d add-filter-dest-s button-poisiting-s"><span>+</span></a><label class="custom-label-checkbox-s">Quantity:</label> ';

                 if(isset($quantity_value)) {

                    foreach($quantity_value as $i => $qv) {

                    $html .= '<input name="prefix_kit_quantity_component[' . $comp . '][]" type="number" class="prefix-kit-quantity-comp-d custom_quantity_group quantity-group-' . $group_id . '" min="1" value="'.$qv.'" style="width:100%;">';

                    } 

                } else {

                     $html .= '<input name="prefix_kit_quantity_component[' . $comp . '][]" type="number" class="prefix-kit-quantity-comp-d custom_quantity_group quantity-group-' . $group_id . '" min="1" value="1" style="width:100%;">';

                }

                     $html .= '<a style="top:-28px; right:5px;" href="javascript:void(0);" class="add-filter-dest-s button-poisiting-s remove-button-s remove-quantity-d" data-id="' . $group_id . '"  ><span>-</span></a>';



                    $html .= '</p></div>';







                $html .= '<div class="options_group"><p class="form-field"><label class="custom-label-checkbox-s">Lock:</label> <input name="prefix_kit_lock_component[' . $comp . '][]" type="checkbox" class="prefix-kit-lock-comp-d lock-group-' . $group_id . '" ' . $lock_checked . ' ></p></div>';

                if ($key > 0) {

                    $html .= '<p><a href="javascript:void(0);" class="add-filter-dest-s button-poisiting-s remove-button-s remove-group-d" data-id="' . $group_id . '"  ><span>-</span></a></p>';

                }

                $html .= '</div>';

            }

            echo $html .= '</div>';

        }

        elseif (!empty($child_component)) {

            $selected_comp = json_decode($component, true);

            $selected_c_comp = json_decode($child_component, true);

           



            $html = '';

            $html .= '<div class="block-container-d">';

            foreach ($selected_comp as $key => $comp) {



                foreach ($selected_c_comp[$comp] as $ckey => $select_child_com) {



                    $component = [];

                    $group_id = $ckey + 1;

                    $html .= '<div class="option-group-d option-group-s clearfix group-container-' . $group_id . '" group-id="' . $group_id . '">';

                    foreach ($get_comp as $c_key => $term) {

                        $selected = '';

                        if ($comp == $term->term_id) {

                            $selected = 'selected';

                        }



                        if ($c_key < 1) {

                            $html .= '<div class="options_group">';

                            $html .= '<p class="form-field ">';

                            $html .= '<label>Components</label>';

                            $html .= '<select class="prefix-kit-component-d" name="prefix_kit_component[]" group-id="' . $group_id . '">';

                            // $html .= '<option value="">Please select</option>';

                        }



                        $html .= '<option value="' . $term->term_id . '"  ' . $selected . ' >' . $term->name . '</option>';



                        end($get_comp);

                        if ($c_key === key($get_comp)) {

                            $html .= '</select>';

                            $html .= '</p>';

                            $html .= '</div>';

                        }

                    }



                    $get_child_comp = get_configutor_child_texonomys($comp);





                    if (!empty($get_child_comp) && !empty($child_component)) {

                        // $selected_c_comp = json_decode($child_component, true);



                        //print_r($selected_c_comp);

                        foreach ($get_child_comp as $cc_key => $c_term) {

                            $selected = '';

                            if ($select_child_com == $c_term->term_id) {

                                $selected = 'selected';



                            }



                            if ($cc_key < 1) {

                                $html .= '<div class="options_group">';

                                $html .= '<p class="form-field prefix-child-comp-block-d">';

                                $html .= '<label>Child Components</label>';

                                $html .= '<select class="prefix_kit_child_component child-comp-group-' . $group_id . '" name="prefix_kit_child_component[' . $comp . '][]" group-id="' . $group_id . '">';

                                // $html .= '<option value="">Please select</option>';

                            }



                          

                            $html .= '<option value="' . $c_term->term_id . '"  ' . $selected . ' >' . $c_term->name . '</option>';



                            end($get_child_comp);

                            if ($cc_key === key($get_child_comp)) {

                                $html .= '</select>';

                                $html .= '</p>';

                                $html .= '</div>';

                            }

                        }

                    } else {

                        $html .= '<div class="options_group">';

                        $html .= '<p class="form-field prefix-child-comp-block-d" style="display:none;" >';

                        $html .= '<label class="custom-post-label-block-s">Child Components</label>';

                        $html .= '<select class="prefix_kit_child_component child-comp-group-' . $group_id . '" name="prefix_kit_child_component[][]" group-id="' . $group_id . '">';

                        $html .= '</select>';

                        $html .= '</p>';

                        $html .= '</div>';

                    }

                    if(isset($cprefix_kit_position[$key]) && !empty($cprefix_kit_position[$key])) {

                        $ccprefix_kit_position = $cprefix_kit_position[$key];

                    } else {

                        $ccprefix_kit_position = $group_id;

                    }



                     $html .= '<input name="cprefix_kit_position[]" type="hidden" class="cprefix_kit_position" group-id="'.$group_id.'" value="'.$ccprefix_kit_position.'">';



                    $get_addons = woo_kit_get_addon_by_id($select_child_com);



                    if (!empty($get_addons)) {

                        foreach ($get_addons as $a_key => $addon) {

                            $selctd = '';

                            if(!empty($child_component)) {

                                foreach($selected_addon as $selected_child_addon) {

                                    if(in_array($addon->ID, $selected_child_addon)) {

                                        $selctd = 'selected';

                                    } 

                                }

                            } else {

                            foreach($selected_addon[$comp] as $caddon) {

                                if (isset($caddon) && $caddon == $addon->ID) {

                                    $selctd = 'selected';

                                }

                            }

                        }

                       

                            

                            $check_multi_select = get_term_meta($comp, 'multi-component-selection', true);

                            

                            

                           

                            

                            if ($a_key < 1) {

                                $html .= '<div class="options_group" style="clear:none; display:inline-block;float:left;     width: 71%;">';

                                $html .= '<p class="form-field ">';

                                $html .= '<label>Addon</label>';

                                $html .= '<select multiple class=" prefix-kit-addon-d addon-group-' . $group_id . '" name="prefix_kit_addon[' . $select_child_com . '][]">';

                                // $html .= '<option value="">Please select</option>';

                            }



                            $html .= '<option value="' . $addon->ID . '" ' . $selctd . ' >' . $addon->post_title . '</option>';

                            end($get_addons);

                            if ($a_key === key($get_addons)) {

                                $html .= '</select>';

                                $html .= '</p>';

                                $html .= '</div>';

                            }

                        }

                    } else {

                        $html .= '<div class="options_group" style="clear:none; display:inline-block;float:left ;    width: 71%;">';

                        $html .= '<p class="form-field ">';

                        $html .= '<label>Addon</label>';

                        $html .= '<select  '.$multiple_select.' class=" prefix-kit-addon-d addon-group-' . $group_id . '" name="prefix_kit_addon[' . $select_child_com . '][]">';

                        // $html .= '<option value="">Please select</option>';

                        $html .= '</select>';

                        $html .= '</p>';

                        $html .= '</div>';

                    }



                    $lock_checked = '';

                    if (isset($selected_lock[$select_child_com])) {

                        $lock_checked = 'checked';

                    }

                    if (isset($selected_quantity[$select_child_com])) {

                        $quantity_value = $selected_quantity[$select_child_com];

                    } 

                     $html .= '<div class="options_group" style="clear:none; display:inline-block;float:left;width:25%;"><p class="form-field custom_form_fields"><a style="top:-28px;" href="javascript:void(0);" class="add-quantity-block-d add-filter-dest-s button-poisiting-s"><span>+</span></a><label class="custom-label-checkbox-s">Quantity:</label> ';

                     if(isset($quantity_value)) {

                        foreach($quantity_value as $i => $qv) {

                        $html .= '<input name="prefix_kit_quantity_component[' . $select_child_com . '][]" type="number" class="prefix-kit-quantity-comp-d custom_quantity_group quantity-group-' . $group_id . '" min="1" value="'.$qv.'" style="width:100%;">';

                        } 

                    } else {

                         $html .= '<input name="prefix_kit_quantity_component[' . $select_child_com . '][]" type="number" class="prefix-kit-quantity-comp-d custom_quantity_group quantity-group-' . $group_id . '" min="1" value="1" style="width:100%;">';

                    }

                         $html .= '<a style="top:-28px; right:5px;" href="javascript:void(0);" class="add-filter-dest-s button-poisiting-s remove-button-s remove-quantity-d" data-id="' . $group_id . '"  ><span>-</span></a>';



                        $html .= '</p></div>';







                    $html .= '<div class="options_group"><p class="form-field"><label class="custom-label-checkbox-s">Lock:</label> <input name="prefix_kit_lock_component[' . $select_child_com . '][]" type="checkbox" class="prefix-kit-lock-comp-d lock-group-' . $group_id . '" ' . $lock_checked . ' ></p></div>';

                    if ($ckey > 0) {

                        $html .= '<p><a href="javascript:void(0);" class="add-filter-dest-s button-poisiting-s remove-button-s remove-group-d" data-id="' . $group_id . '"  ><span>-</span></a></p>';

                    }

                    $html .= '</div>';

                }

            }

            echo $html .= '</div>';

        }

    } else {



        $html = '';

        $html .= '<div class="block-container-d">';

        $html .= '<div class="option-group-d option-group-s clearfix group-container-1" group-id="1">';

        $html .= '<div class="options_group">';

        $html .= '<p class="form-field ">';

        $html .= '<label>Components</label>';

        $html .= '<select class="prefix-kit-component-d" name="prefix_kit_component[]" group-id="1">';

        // $html .= '<option value="" >Please select</option>';

        $html .= '</select>';

        $html .= '<input type="hidden" name="cprefix_kit_position[]" value="1"  class="cprefix_kit_position" group-id="1"/></p>';

        $html .= '</div>';



        $html .= '<div class="options_group">';

        $html .= '<p class="form-field prefix-child-comp-block-d" style="display:none;" >';

        $html .= '<label class="custom-post-label-block-s">Child Components</label>';

        $html .= '<select class="prefix_kit_child_component child-comp-group-1" name="prefix_kit_child_component[][]" group-id="1">';

        $html .= '</select>';

        $html .= '</p>';

        $html .= '</div>';



        $html .= '<div class="options_group" style="clear:none; display:inline-block;float:left ;    width: 71%;">';

        $html .= '<p class="form-field ">';

        $html .= '<label>Addon</label>';

        $html .= '<select class="prefix-kit-addon-d addon-group-1  " name="prefix_kit_addon[][]">';

        // $html .= '<option value="">Please select</option>';

        $html .= '</select>';

        $html .= '</p>';

        $html .= '</div>';



        $html .= '<div class="options_group" style="clear:none; display:inline-block;float:left;width: 25%;"><p class="form-field custom_form_fields"><a style="top:-28px;" href="javascript:void(0);" class="add-quantity-block-d add-filter-dest-s button-poisiting-s"><span>+</span></a><label class="custom-label-checkbox-s">Quantity:</label> <input name="prefix_kit_quantity_component[][]" type="number" class="prefix-kit-quantity-comp-d custom_quantity_group quantity-group-1 " min="1" value="1" style="width:100%;"></p></div>';



        $html .= '<div class="options_group"><p class="form-field"><label class="custom-label-checkbox-s">Lock:</label> <input name="prefix_kit_lock_component[][]" type="checkbox" class="prefix-kit-lock-comp-d lock-group-1" ></p></div>';

        $html .= '</div>';

        $html .= '</div>';

        echo $html;

    }

    echo '</div>';

}



add_action('woocommerce_process_product_meta', 'prefix_kit_fields_save', 10, 2);

function prefix_kit_fields_save($id, $post)

{

    if(!empty($_POST['_show_in_kits'])  && isset($_POST['_show_in_kits'])) {



        update_post_meta($id, '_show_in_kits', $_POST['_show_in_kits']);

    } 

 

    if (isset($_POST['prefix_kit_configurator']) && !empty($_POST['prefix_kit_configurator'])) {

        if (!empty($_POST['prefix_kit_configurator_enable'])) {

            update_post_meta($id, 'prefix_kit_configurator_enable', $_POST['prefix_kit_configurator_enable']);

        } else {

            delete_post_meta($id, 'prefix_kit_configurator_enable');

        }



        update_post_meta($id, 'prefix_kit_configurator', $_POST['prefix_kit_configurator']);

      

        $component = array_unique($_POST['prefix_kit_component']);

        $component = array_filter($component);

        if (isset($_POST['prefix_kit_component']) && !empty($_POST['prefix_kit_component'])) {

            $s_comp = [];

            $cprefix_kit_position = [];

            foreach ($component as $ki => $comp) {



                if (isset($_POST['prefix_kit_child_component']) && !empty($_POST['prefix_kit_child_component'])) {

                    

                    $cs_comp = [];





                    if (isset($_POST['prefix_kit_addon'][$_POST['prefix_kit_child_component'][$comp][0]]) && !empty($_POST['prefix_kit_addon'][$_POST['prefix_kit_child_component'][$comp][0]])) {

                        foreach($_POST['prefix_kit_addon'][$_POST['prefix_kit_child_component'][$comp][0]] as $child_com) {



                            $cs_comp[] = $child_com;

                        }

                    }



                    if (!empty($cs_comp)) {

                        $s_comp[] = $comp;

                        $cprefix_kit_position[] = $_POST['cprefix_kit_position'][$ki];

                    }

                } else {

                    

                    if (isset($_POST['prefix_kit_addon'][$comp][0]) && !empty($_POST['prefix_kit_addon'][$comp][0])) {

                     

                      

                         $s_comp[] = $comp;

                         $cprefix_kit_position[] = $_POST['cprefix_kit_position'][$ki];

                        

                    }

                }

            }

          



            if (!empty($s_comp)) {

                update_post_meta($id, 'prefix_kit_component', wp_slash(json_encode($s_comp)));

                update_post_meta($id, 'cprefix_kit_position', wp_slash(json_encode($cprefix_kit_position)));

            } else {

                delete_post_meta($id, 'prefix_kit_component');

                delete_post_meta($id, 'cprefix_kit_position');

            }

        } else {

            delete_post_meta($id, 'prefix_kit_component');

            delete_post_meta($id, 'cprefix_kit_position');

        }

       

        if (isset($_POST['prefix_kit_child_component']) && !empty($_POST['prefix_kit_child_component'])) {

           

            $c_comp = [];

            foreach ($_POST['prefix_kit_child_component'] as $k => $_comp) {



                if (isset($_POST['prefix_kit_addon'][$_comp[0]]) && !empty($_POST['prefix_kit_addon'][$_comp[0]])) {

                    $c_comp[$k] = $_comp;



                }

            }



            if (!empty($c_comp)) {

                update_post_meta($id, 'prefix_kit_child_component', wp_slash(json_encode($c_comp)));

            } else {

                delete_post_meta($id, 'prefix_kit_child_component');

            }

        } else {

            delete_post_meta($id, 'prefix_kit_child_component');

        }

        

        // check addons 

        if (isset($_POST['prefix_kit_addon']) && !empty($_POST['prefix_kit_addon'])) {

            $addons = [];



            foreach ($_POST['prefix_kit_addon'] as $key => $addon) {





                if (isset($_POST['prefix_kit_child_component']) && !empty($_POST['prefix_kit_child_component'])) {

                    $flag = 0;

                    foreach ($_POST['prefix_kit_child_component'] as $k => $_comp) {

                            foreach($_comp as $ckey => $_child_comp) {

                            // set child addons according to their component

                            if (isset($_POST['prefix_kit_addon'][$_comp[$ckey]]) && !empty($_POST['prefix_kit_addon'][$_comp[$ckey]]) && $_child_comp ==  $key) {

                             



                                foreach($_POST['prefix_kit_addon'][$_comp[$ckey]] as $i => $child_comp) {

                                    $addons[$key][] = $child_comp;

                                    $flag = 1;

                                    // break;

                                }

                            }

                        }

                    } 



                    if ($flag < 1) {

                    

                        if (isset($addon[0]) && !empty($addon[0]) && in_array($key, $component)) {

                            $addons[$key] = [$addon[0]];

                        }

                    }

                } else {



                    if (isset($addon) && !empty($addon) && in_array($key, $component)) {

                        // add multiple addons against a component

                        foreach ($addon as  $c_addon) {

                            $addons[$key][] = $c_addon;

                        }

                    }

                }

            }



            if (!empty($addons)) {

                update_post_meta($id, 'prefix_kit_addon', wp_slash(json_encode($addons)));

            } else {

                delete_post_meta($id, 'prefix_kit_addon');

            }

        } else {

            delete_post_meta($id, 'prefix_kit_addon');

        }







        if (isset($_POST['prefix_kit_lock_component']) && !empty($_POST['prefix_kit_lock_component'])) {

            $locks = [];



            foreach ($_POST['prefix_kit_lock_component'] as $key => $lock) {



                if (isset($_POST['prefix_kit_child_component']) && !empty($_POST['prefix_kit_child_component'])) {

                    $flag = 0;

                    // add lock against component if checked

                    foreach ($_POST['prefix_kit_child_component'] as $k => $_comp) {

                        if(in_array($key, $_comp)) {

                            $locks[$key] = [$lock[0]];

                        }

                    }

                } else {

                    if (isset($lock[0]) && !empty($lock[0]) && in_array($key, $component)) {

                        $locks[$key] = [$lock[0]];

                    }

                }

            }

          

            if (!empty($locks)) {

                update_post_meta($id, 'prefix_kit_lock_component', wp_slash(json_encode($locks)));

            } else {

                delete_post_meta($id, 'prefix_kit_lock_component');

            }

        } else {

            delete_post_meta($id, 'prefix_kit_lock_component');

        }

      

        if (isset($_POST['prefix_kit_quantity_component']) && !empty($_POST['prefix_kit_quantity_component'])) {

            $quantity = [];





            foreach ($_POST['prefix_kit_quantity_component'] as $key => $lock) {



                if (isset($_POST['prefix_kit_child_component']) && !empty($_POST['prefix_kit_child_component'])) {

                    $flag = 0;

                    // add lock against component if checked

                    foreach ($_POST['prefix_kit_child_component'] as $k => $_comp) {

                        if(in_array($key, $_comp)) {

                            $quantity[$key] = $lock;

                        }

                    }

                } else {

                    if (isset($lock[0]) && !empty($lock[0]) && in_array($key, $component)) {

                        $quantity[$key] = $lock;

                    }

                }

            }

           

            if (!empty($quantity)) {

                update_post_meta($id, 'prefix_kit_quantity_component', wp_slash(json_encode($quantity)));

            } else {

                delete_post_meta($id, 'prefix_kit_quantity_component');

            }

        } else {

            delete_post_meta($id, 'prefix_kit_quantity_component');

        }

    } else {

        delete_post_meta($id, 'prefix_kit_configurator');

        delete_post_meta($id, 'prefix_kit_component');

        delete_post_meta($id, 'prefix_kit_addon');

        delete_post_meta($id, 'prefix_kit_lock_component');

           delete_post_meta($id, 'prefix_kit_quantity_component');

    }



}



add_action('wp_ajax_woocommerce_get_component_addons', 'woocommerce_get_component_addons');

add_action('wp_ajax_nopriv_woocommerce_get_component_addons', 'woocommerce_get_component_addons');

function woocommerce_get_component_addons()

{

    $id = $_POST['configurator_id'];

    $options = [];

    if (isset($_POST['type']) && $_POST['type'] == 'addon') {



        $get_child_component = get_configutor_child_texonomys($_POST['comp_id']);

        if (!empty($get_child_component)) {

            foreach ($get_child_component as $c_comp) {

                $store['id'] = $c_comp->term_id;

                $store['text'] = $c_comp->name;

                $store['selected'] = false;

                $options['child_component'][] = $store;

            }

        } else {

            $addons = woo_kit_get_addon_by_id($_POST['comp_id']);

            foreach ($addons as $addon) {

                $store['id'] = $addon->ID;

                $store['text'] = $addon->post_title;



                $options['addons'][] = $store;

            }

        }

        $check_multi_select = get_term_meta($_POST['comp_id'], 'multi-component-selection', true);

        $store_multi['multiple_select'] = $check_multi_select;

        $options['multi_select'][] = $store_multi;

    } else {

        if ($id) {



            $components = get_configutor_child_texonomys($id);

            if (!empty($components)) {

                foreach ($components as $comp) {

                    $store['id'] = $comp->term_id;

                    $store['text'] = $comp->name;

                    $options['components'][] = $store;

                }

            }

            $prefix_kit_component = get_post_meta($_POST['post_id'], 'prefix_kit_component', true);

            if (!empty($prefix_kit_component)) {

                $options['action'] = 'update';

            } else {

                $options['action'] = 'create';

            }

        }

    }



    echo json_encode($options);

    exit();

}



function woocommerce_wp_select_multiple($field)

{

    global $thepostid, $post;



    $thepostid = empty($thepostid) ? $post->ID : $thepostid;

    $field['class'] = isset($field['class']) ? $field['class'] : 'select short';

    $field['wrapper_class'] = isset($field['wrapper_class']) ? $field['wrapper_class'] : '';

    $field['name'] = isset($field['name']) ? $field['name'] : $field['id'];

    $field['value'] = isset($field['value']) ? $field['value'] : (get_post_meta($thepostid, $field['id'], true) ? get_post_meta($thepostid, $field['id'], true) : array());



    echo '<p class="form-field ' . esc_attr($field['id']) . '_field ' . esc_attr($field['wrapper_class']) . '"><label for="' . esc_attr($field['id']) . '">' . wp_kses_post($field['label']) . '</label><select id="' . esc_attr($field['id']) . '" name="' . esc_attr($field['name']) . '" class="' . esc_attr($field['class']) . '" multiple="multiple">';



    foreach ($field['options'] as $key => $value) {

        echo '<option value="' . esc_attr($key) . '" ' . (in_array($key, $field['value']) ? 'selected="selected"' : '') . '>' . esc_html($value) . '</option>';

    }



    echo '</select> ';



    if (!empty($field['description'])) {

        if (isset($field['desc_tip']) && false !== $field['desc_tip']) {

            echo '<img class="help_tip" data-tip="' . esc_attr($field['description']) . '" src="' . esc_url(WC()->plugin_url()) . '/assets/images/help.png" height="16" width="16" />';

        } else {

            echo '<span class="description">' . wp_kses_post($field['description']) . '</span>';

        }

    }

    echo '</p>';

}



function woo_kit_get_addon_by_id($id)

{

    $args = [

        'posts_per_page' => -1,

        'post_type' => 'configurator',

        'post_status' => 'publish',

        //'orderby'     => 'title',

         'order' => 'DESC',

        'tax_query' => [

            'relation' => 'AND',

            [

                'taxonomy' => 'configurator-category',

                'field' => 'term_id',

                'terms' => $id,

                'include_children' => false,

            ],

        ],

    ];



    $wp_query = new WP_Query($args);

    if (isset($wp_query->posts) && !empty($wp_query->posts)) {

        return $wp_query->posts;

    }

    return array();

}



//create custom tab

add_filter('woocommerce_product_tabs', 'woo_custom_product_tabs');

function woo_custom_product_tabs($tabs)

{

    // Adds the qty pricing  tab

    $tabs['qty_pricing_tab'] =

        [

        'title' => __('Kits', 'woocommerce'),

        'priority' => 110,

        'callback' => 'kits_pre_selected_configuration',

    ];



    return $tabs;

}



function kits_pre_selected_configuration()

{

    global $product;



    $prod_id = $product->get_id();

    $config_id = get_post_meta($prod_id, 'prefix_kit_configurator', true);

    if ($config_id) {

        $configurator = get_term_by_id($config_id);

        echo '<span><strong>Configurator: </strong> ' . $configurator->name . ' </span>';

        $config_comp = get_post_meta($prod_id, 'prefix_kit_component', true);

        $config_child_comp = get_post_meta($prod_id, 'prefix_kit_child_component', true);

        $addon = get_post_meta($prod_id, 'prefix_kit_addon', true);



        if (!empty($addon)) {

            $addon = json_decode($addon, true);

        }



        $html = '';

        if ($config_comp) {

            $config_comp = json_decode($config_comp, true);



            if (!empty($config_child_comp)) {

                $config_child_comp = json_decode($config_child_comp, true);

                if (!empty($config_comp)) {

                    foreach ($config_comp as $comp) {

                        if (isset( $config_child_comp[$comp]) && isset($addon[$config_child_comp[$comp]][0])) {

                            $component = get_term_by_id($comp);

                            $c_component = get_term_by_id($config_child_comp[$comp]);

                            $html .= '<div class="component-block-s">';

                         

                            $html .= '<span><strong>Component: </strong> ' . $component->name . ' </span>';

                            $html .= '<span><strong>Child Component: </strong> ' . $c_component->name . ' </span>';

                            $html .= '<span><strong>addon: </strong> ' . get_the_title($addon[$config_child_comp[$comp]][0]) . '</span>';

                            $html .= '<span><strong>Price: </strong> ' . get_post_meta($addon[$config_child_comp[$comp]][0], 'addon_price', true) . '</span>';

                            $html .= '</div>';

                        }

                    }

                }

            } else {

                if (!empty($config_comp)) {

                    foreach ($config_comp as $comp) {



                        if (isset($addon[$comp][0])) {

                            $component = get_term_by_id($comp);

                            $html .= '<div class="component-block-s">';

                            $html .= '<span><strong>Component: </strong> ' . $component->name . ' </span>';

                            $html .= '<span><strong>addon: </strong> ' . get_the_title($addon[$comp][0]) . '</span>';

                            $html .= '<span><strong>Price: </strong> ' . get_post_meta($addon[$comp][0], 'addon_price', true) . '</span>';

                            $html .= '</div>';

                        }

                    }

                }

            }

        }

        echo $html;

    }

}

add_filter( 'woocommerce_product_query_meta_query', 'shop_only_kits_products', 10, 2 );

function shop_only_kits_products( $meta_query, $query ) {

   

    // Only on shop archive pages

    if( is_admin() || is_search() || ! is_shop() ) return $meta_query;



    $meta_query[] = array(

        'key'     => '_show_in_kits',

        'value'   => 'yes',

        'compare' => '='

    );

    return $meta_query;

   

}