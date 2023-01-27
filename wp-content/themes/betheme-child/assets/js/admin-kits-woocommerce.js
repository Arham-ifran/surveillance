jQuery(document).ready(function ($) {
    "use strict";


    function wooCommerceMultipleSelect2(element) {
        element.select2({
            multiple:true,
            width: '100%',
            //minimumInputLength: 1,
            placeholder: "Please Select",
            allowClear: true,
        });
    }

    function wooCommerceSingleSelect2(element) {
        element.select2();
    }
    
    function woo_kit_components() {

        let comp_html = '';

        comp_html += '<div class="options_group">';
        comp_html += '<p class="form-field ">';
        comp_html += '<label>Main Components</label>';
        comp_html += '<select class="prefix-kit-component-d" name="prefix_kit_component[]" class="select short">';
        comp_html += '<option value="">Select value</option>';
        comp_html += '</select>';
        comp_html += '</p>';
        comp_html += '</div>';

        return comp_html;
    }

    function woo_kit_addon() {

        let comp_html = '';

        comp_html += '<div class="options_group">';
        comp_html += '<p class="form-field ">';
        comp_html += '<label>Addon</label>';
        comp_html += '<select class="prefix-kit-addon-d" name="prefix_kit_addon[]" class="select short">';
        comp_html += '<option value="">Select value</option>';
        comp_html += '</select>';
        comp_html += '</p>';
        comp_html += '</div>';

        return comp_html;
    }

    function woo_kit_build_compo_block()
    {
        let create_block = '';

        create_block += '<div class="block-container-d">';
        create_block += woo_kit_components();
        create_block += woo_kit_addon();
        create_block += '</div>';

        $('.component-addon-block-d').append(create_block);
    }

    async function create_admin_ajax_request(data_object)
    {
        return new Promise((resolve, reject) => {

            let data = data_object;
            jQuery.post(config_admin_ajax_req.ajaxurl, data)
            .then(function(res){
                resolve(res);
            })
            .catch(function(xhr, status, error) {
                reject(true);
            });
        });
    }

    $('body').on('change','#prefix_kit_configurator',async function() {

        $('select.prefix-kit-component-d option').prop("selected",false);
        $('select.prefix_kit_child_component').html("");
        $('select.prefix-kit-addon-d option').html("");
        $('select.prefix-kit-addon-d').html("");
        $('.prefix-child-comp-block-d').hide();

        let data = {
            'action': 'woocommerce_get_component_addons',
            'configurator_id' : $('option:selected',this).val(),
            'post_id': $('#post_ID').val(),
            'type':'component'
        };
        let get_comp = await create_admin_ajax_request(data);
        let res = JSON.parse(get_comp);

        if(res.components && res.components.length > 0) {

            let comp_html = '';
            comp_html += '<option value="" >Select value</option>';
            $.each(res.components, function(i, item) {
                comp_html += '<option value="'+item.id+'" >'+item.text+'</option>';
            });
            $('.prefix-kit-component-d').html(comp_html);
        }
        else
        {
            $('select.prefix-kit-component-d option').prop("selected",false);
            $('select.prefix_kit_child_component option').prop("selected",false);
            $('select.prefix-kit-addon-d option').prop("selected",false);
            
            $('.prefix-child-comp-block-d').hide();
        }
    });

    $('body').on('change','.prefix-kit-component-d',async function() {
        let comp_slect_val = $('option:selected',this).val();
        if(comp_slect_val) {
            let data = {
                'action': 'woocommerce_get_component_addons',
                'comp_id' : $('option:selected',this).val(),
                'post_id': $('#post_ID').val(),
                'type':'addon'
            };

            let group_id = $(this).attr("group-id");
    
            let get_comp = await create_admin_ajax_request(data);
            let res = JSON.parse(get_comp);
            let multi_select = res.multi_select[0].multiple_select;

            if (res.child_component) {

                $('.prefix-child-comp-block-d').show();
                //filter tag empty before loading child component
                $('.addon-group-'+group_id).html('');
                let comp_html = '';
              
               
                comp_html += '<option value="" >Please select </option>';
                $.each(res.child_component, function (i, item) {
                    comp_html += '<option value="' + item.id + '" >' + item.text + '</option>';
                });
                $(".child-comp-group-" + group_id).html(comp_html);
                $('.child-comp-group-'+group_id).attr("name","prefix_kit_child_component["+comp_slect_val+"][]")
            }
            else
            {
                $('.addon-group-'+group_id).html('');
                $('.addon-group-'+group_id).attr("name","prefix_kit_addon["+comp_slect_val+"][]")
                $('.lock-group-'+group_id).attr("name","prefix_kit_lock_component["+comp_slect_val+"][]"); 
                $('.quantity-group-'+group_id).attr("name","prefix_kit_quantity_component["+comp_slect_val+"][]");  

            
            
                // if(multi_select == 'no') {
                //     $('.addon-group-'+group_id).removeAttr('multiple'); 
                // } else {
                //     $('.addon-group-'+group_id).prop('multiple',true); 
                // }

                if(res.addons && res.addons.length > 0) {

                    let comp_html = ''
                    $.each(res.addons, function(i, item) {
                        comp_html += '<option value="'+item.id+'" >'+item.text+'</option>';
                    });
        
                    $('.addon-group-'+group_id).html(comp_html);
                } else {

                    $('.addon-group-'+group_id).html('');
                }
            }

        }  else {
            let group_id = $(this).attr("group-id");
            $('.addon-group-'+group_id).attr("name","prefix_kit_addon[][]")
            $('.lock-group-'+group_id).attr("name","prefix_kit_lock_component[][]");
            $('.quantity-group-'+group_id).attr("name","prefix_kit_quantity_component[][]");
            $('.addon-group-'+group_id).html('');
        }
        
    });


    //kits child component
    $('body').on('change','.prefix_kit_child_component',async function() {
        let comp_slect_val = $('option:selected',this).val();
        if(comp_slect_val) {
            let data = {
                'action': 'woocommerce_get_component_addons',
                'comp_id' : $('option:selected',this).val(),
                'post_id': $('#post_ID').val(),
                'type':'addon'
            };

            let group_id = $(this).attr("group-id");
    
            let get_comp = await create_admin_ajax_request(data);

            let res = JSON.parse(get_comp);  
            let multi_select = res.multi_select[0].multiple_select;
            // if(multi_select == 'no') {
            //     $('.addon-group-'+group_id).removeAttr('multiple'); 
            // } else {
            //     $('.addon-group-'+group_id).prop('multiple',true); 
            // }          

            $('.addon-group-'+group_id).attr("name","prefix_kit_addon["+comp_slect_val+"][]")
            $('.lock-group-'+group_id).attr("name","prefix_kit_lock_component["+comp_slect_val+"][]");   
            $('.quantity-group-'+group_id).attr("name","prefix_kit_quantity_component["+comp_slect_val+"][]");  
    
            if(res.addons && res.addons.length > 0) {

                let comp_html = ''
                $.each(res.addons, function(i, item) {
                    comp_html += '<option value="'+item.id+'" >'+item.text+'</option>';
                });
    
                $('.addon-group-'+group_id).html(comp_html);
            } else {

                $('.addon-group-'+group_id).html('');
            }
            

        }  else {
            let group_id = $(this).attr("group-id");
            $('.addon-group-'+group_id).attr("name","prefix_kit_addon[][]")
            $('.lock-group-'+group_id).attr("name","prefix_kit_lock_component[][]");
            $('.quantity-group-'+group_id).attr("name","prefix_kit_quantity_component[][]");
            $('.addon-group-'+group_id).html('');
        }
        
    });

    $('body').on('click','.add-kits-block-d',function() {
        
        if($('.prefix-kit-component-d:first option').length > 1)
        {


            let kits_block_length = $('.block-container-d .option-group-d').length+1;
            var $block = $('.option-group-d:last');

            // Grab the selected value
            var theValue = $block.find(':selected').val();
            $block.find('.prefix-kit-addon-d').select2('destroy');

            // Clone the block 
            var $clone = $block.clone();


            // if(theValue)
            // {
            //     // Find the selected value in the clone, and remove
            //     //$clone.find('option[value=' + theValue + ']').remove();
            // }
            //$block.find('select:option').removeAttr("selected");

            $clone.attr("group-id",kits_block_length);


            $clone.find('.prefix-kit-component-d').attr("group-id",kits_block_length);
            $clone.find('.cprefix_kit_position').val(kits_block_length);
            $clone.find('.cprefix_kit_position').attr("group-id",kits_block_length);
            $clone.find('.prefix_kit_child_component').attr("group-id",kits_block_length);
            $clone.find('.prefix-child-comp-block-d').hide();

            let rem_kits_length = kits_block_length-1;

            $clone.find('.prefix_kit_child_component').removeClass("child-comp-group-"+rem_kits_length).addClass("child-comp-group-"+kits_block_length);

            $clone.removeClass("group-container-"+rem_kits_length).addClass('group-container-'+kits_block_length);
            
            $clone.find('remove-group-d').attr("data-id",kits_block_length);

            $clone.find('.prefix-kit-addon-d').removeClass("addon-group-"+rem_kits_length).addClass("addon-group-"+kits_block_length);

            $clone.find('.prefix-kit-addon-d').html('');


            $clone.find('.prefix-kit-lock-comp-d').removeClass("lock-group-"+rem_kits_length).addClass("lock-group-"+kits_block_length);
            $clone.find('.prefix-kit-quantity-comp-d').removeClass("quantity-group-"+rem_kits_length).addClass("quantity-group-"+kits_block_length);
            // Append the clone
            $block.after($clone);
            wooCommerceMultipleSelect2($('.prefix-kit-addon-d'));

            if($clone.find('.remove-group-d').length < 1) {
                $('.option-group-d:last').append('<p><a href="javascript:void(0);" class="add-filter-dest-s button-poisiting-s remove-button-s remove-group-d" data-id="'+kits_block_length+'" ><span>-</span></a></p>');
            }

            $('.addon-group-'+kits_block_length).attr("name","prefix_kit_addon[][]")
            $('.lock-group-'+kits_block_length).attr("name","prefix_kit_lock_component[][]");
            $('.quantity-group-'+kits_block_length).attr("name","prefix_kit_quantity_component[][]");
            //.attr('selectedIndex', '-1');
            $('.prefix-kit-component-d:last option:selected').prop("selected", false);
            $('.prefix_kit_child_component:last option:selected').prop("selected", false);
            $('.prefix-kit-lock-comp-d:last option:selected').prop("checked", false);

            $('.prefix-kit-lock-comp-d:last').removeAttr("checked");
        }
        else
        {
            if($('.block-container-d .option-group-d').length == 1)
            {
                alert("Please select the configurator that have component");
            }
        }
    });
    $(document).on('click','.add-quantity-block-d',function() {
         
            var $block = $(this).siblings('.custom_quantity_group:last');
            var $clone = $block.clone();
            // if($clone.find('.remove-group-d').length < 1) {
            //     $('.option-group-d:last').append('<p><a href="javascript:void(0);" class="add-filter-dest-s button-poisiting-s remove-button-s remove-group-d" data-id="'+kits_block_length+'" ><span>-</span></a></p>');
            // }
            $block.after($clone);    
     
    });
    $('body').on('click','.remove-quantity-d',function() {
       let custom_quantity_group = $(this).prev(".custom_quantity_group");
       $(custom_quantity_group).remove();
    });

    $('body').on('click','.remove-group-d',function() {
       let group_id = $(this).attr("data-id");
       $('.group-container-'+group_id).remove();
    });
    //wooCommerce addons option select2 
    wooCommerceMultipleSelect2($('.prefix-kit-addon-d'));
    

});