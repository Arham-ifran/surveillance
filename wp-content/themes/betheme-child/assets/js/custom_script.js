jQuery(document).ready(function ($) {

    "use strict";





    //get the object size

    Object.size = function (obj) {

        var size = 0,

            key;

        for (key in obj) {

            if (obj.hasOwnProperty(key)) size++;

        }

        return size;

    };



    //prevent to click disabled block

    jQuery('body').on('click', '.disabled-block', function (e) {

        e.stopPropagation();

    });



    async function prepare_ajax_request(data) {

        return new Promise((resolve, reject) => {

            jQuery.post(config_ajax_req.ajaxurl, data)

                .then(function (res) {

                    resolve(res);

                })

                .catch(function (xhr, status, error) {

                    reject(true);

                });

        });

    }



    function get_query_string(param) {



        const url_params = new URLSearchParams(window.location.search);

        if (url_params.has(param)) {



            //return url_params.get('config_link_id');

            return url_params.get(param);

        } else {



            return '';

        }

    }



    // trigger configurator link comes from homepage

    jQuery(window).load(function () {

        if (get_query_string('config_action')) {

            jQuery('.show-component-d[config-id="' + get_query_string('config_action') + '"]').click();

        }

    });



    var term_filter = [];

    jQuery('body').on('click', '.show-component-d', function () {

      

        $('.m-list-item').removeClass('active');

        $(this).find('.m-list-item').addClass('active');



        let config_id = jQuery(this).attr('config-id');

        // get configurator image and replace with side image

        var config_image = $('[config-id="' + config_id + '"] .icon img').attr('src');

        $('.component-image-change').attr('src',config_image);



        $('input[name=selected_configurator]').val(config_id)

        localStorage.setItem("selected_configurator", config_id);

        let conf_req_type = ['component', 'config_overview'];

        for (let i = 0; i < conf_req_type.length; i++) {

            get_config_component(conf_req_type[i], config_id);

        }

        // call function when click on configurator parent component

        calculate_price();

        

    });







    function get_config_component(config_type, config_id) {

    

        jQuery('.pre-loaderr').show();

        let relation = '';

        let selected_component = '';

        // check if query string config link id exit

        if (!get_query_string('config_link_id')) {

            if (localStorage.getItem("config_object") != undefined ) {

                relation = localStorage.getItem("config_object");

            }

        } else {

             relation = localStorage.getItem("product_config_object");

        }

        if(localStorage.getItem("selected_component") != undefined) {

            selected_component = localStorage.getItem("selected_component");

        }



        var data = {

            'action': 'get_configurat_components',

            'configurator_id': config_id,

            'configurator_type': config_type,

            'relation': relation,

            'component': selected_component,

            'config_link_id': get_query_string('config_link_id')

        };



        jQuery.post(config_ajax_req.ajaxurl, data, function (response) {

            setTimeout(

            function(){ 

                jQuery('.pre-loaderr').hide();

            },1000);

            if (config_type == 'component') {

                

      

                localStorage.setItem("current_configurator_comp", $("input[name=list_component]").val());



                jQuery('#component').html(response);



                pre_selected_comp_hightlight();

                update_configurator_prod_id();



            } else if (config_type == 'config_overview') {

                jQuery('#configurator-overview').html(response);

            }

            // if child as class then trigger

            if($('.show-child-comp-d').hasClass('high-green')) {

               $('.show-child-comp-d.high-green').trigger('click'); 

            }

            

        });

    }



    function update_configurator_prod_id() {



        for (const [key, value] of Object.entries(config_pre_selected.configurator)) {

            if ($("input[name=selected_configurator]").val() == value.configurator_id) {

                $("input[name=config_product_id]").val(value.product_id);

            }

        }

    }



    function pre_selected_comp_hightlight() {

        if (!get_query_string('config_link_id')) {

            let selected_configurator = localStorage.getItem("selected_configurator");

            let config_object = localStorage.getItem("config_object");

            config_object = JSON.parse(config_object);

            if (config_object) {

                if (config_object[selected_configurator]) {

                    for (const [key, value] of Object.entries(config_object[selected_configurator])) {

                        $('.component-filter-box[comp-id="' + key + '"]').addClass("high-green");

                        $('.config-overview-d[data-comp="' + key + '"]').addClass("green-highlight");

                    }

                }

            }

        }

        else {

            let selected_configurator = localStorage.getItem("selected_configurator");

            let prod_config_object = localStorage.getItem("product_config_object");

            prod_config_object = JSON.parse(prod_config_object);

            if (prod_config_object) {

                if (prod_config_object[selected_configurator]) {

                    for (const [key, value] of Object.entries(prod_config_object[selected_configurator])) {

                        $('.component-filter-box[comp-id="' + key + '"]').addClass("high-green");

                        $('.config-overview-d[data-comp="' + key + '"]').addClass("green-highlight");

                    }

                }

            }

        }

    }



    function calculate_price() {

        if (get_query_string('config_link_id')) {

            let get_kit_obj = localStorage.getItem("product_config_object");





            let shared_pro_obj = JSON.parse(get_kit_obj);

            let proconfig_first_key = Object.keys(shared_pro_obj)[0];

        

            let kits_price_calc = 0;

            if (get_kit_obj) {

                get_kit_obj = JSON.parse(get_kit_obj);

                for (const [key, value] of Object.entries(get_kit_obj)) {

                    for (const [kit_comp_key, val] of Object.entries(value)) {

                        if (value[kit_comp_key].price.length > 0) {

                            // multiple kits addons price with quantity



                            kits_price_calc += arrays_multiple(value[kit_comp_key].price,value[kit_comp_key].qty);

                        }

                        else {

                            for (const [kit_c_comp_key, val_ch] of Object.entries(val)) {

                                if (val[kit_c_comp_key].price > 0) {

                                    kits_price_calc += arrays_multiple(value[kit_comp_key].price,value[kit_comp_key].qty);

                                }

                            }

                        }

                    }

                }

            }

           

            cal_config_discount(proconfig_first_key, parseFloat(kits_price_calc).toFixed(2));

            jQuery('.config-price-d').text("€ " + parseFloat(kits_price_calc).toFixed(2));

        } else {

            let selected_configurator = localStorage.getItem("selected_configurator");

            let get_config_obj = localStorage.getItem("config_object");

            let curr_config_price_calc = 0;

            if (get_config_obj) {

                get_config_obj = JSON.parse(get_config_obj);

                for (const [key, value] of Object.entries(get_config_obj)) {



                    if (key == selected_configurator) {

                        for (const [kit_comp_key, val] of Object.entries(value)) {

                            

                            if (value[kit_comp_key].price.length > 0) {

                                // multiple component addon price related to their quantity and save 

                                curr_config_price_calc += arrays_multiple(value[kit_comp_key].price,value[kit_comp_key].qty);

                            }

                            else {

                                for (const [kit_c_comp_key, val_ch] of Object.entries(val)) {

                                    if (parseFloat(val[kit_c_comp_key].price) > 0) {

                                        curr_config_price_calc += (parseFloat(val[kit_c_comp_key].price) * val[kit_c_comp_key].qty);

                                    }

                                }

                            }

                        }

                    }

                }

               

                cal_config_discount(selected_configurator, curr_config_price_calc.toFixed(2));

                jQuery('.config-price-d').text("€ " + curr_config_price_calc.toFixed(2));

            }

        }

    }



    jQuery('body').on('click', '.component-filter-box', function () {

       

    

    

        // $('.m-list-item.active').parents('.show-component-d').click();

            

        

        // show preloader

        var popup_msg = $(this).attr("popup-attribute");

        jQuery('.pre-loaderTab').show();

        term_filter = [];

        

        if (!$(this).hasClass('comp-children-d')) {

            $('#component-children-div-d').html('');

        }



        let comp_id = $(this).attr("comp-id");

        localStorage.setItem("selected_component", comp_id);



        let relation = '';

        if (localStorage.getItem("config_object") !== undefined &&  !get_query_string('config_link_id')) {

            relation = localStorage.getItem("config_object");

        } else {

            relation = localStorage.getItem("product_config_object");

        }



        var data = {

            'action': 'component_block',

            'comp_id': comp_id,

            'relation': relation,

            'selected_configurator': localStorage.getItem("selected_configurator"),

            'config_link_id': get_query_string('config_link_id'),

            'componet_filters' :localStorage.getItem("comp_filter_selected"),

        };

      



        jQuery.post(config_ajax_req.ajaxurl, data, function (response) {

            // hide preloader



            jQuery('.pre-loaderTab').hide();

            jQuery('.component-filter-block').html(response);

            let disabled_ai_messages = localStorage.getItem("disabled_ai_messages");

            if(disabled_ai_messages == null) {

                $.post(config_ajax_req.ajaxurl, {action: 'config_ai_messages_show',  current_page: '', popup_msg, popup_msg}).done(response => {

                    if ($.trim(response)){ 

                        $('.dynamic_content').html(response);

                        $('.popup-wrapper').show();

                    }

                });

                // setTimeout(function() {

                //     $('.popup-wrapper').hide();

                // },7000);

            }

           



            $('.addon-d').each(function (i, v) {

                $(this).attr('data-config', $('input[name=selected_configurator]').val());

            });



            let current_configurator_comp = localStorage.getItem("current_configurator_comp");

           

            let current_confi_comp_arr = current_configurator_comp.split(',');

            let selected_comp = current_confi_comp_arr.indexOf(comp_id);

           



            if (selected_comp != '-1' && current_confi_comp_arr[selected_comp + 1] !== undefined) {

                $('.next-component-id').attr("comp-id", current_confi_comp_arr[selected_comp + 1]);

            } else {

                $('.next-component-id').attr("disabled", "disabled");

            }



            if (selected_comp != '-1' && current_confi_comp_arr[selected_comp - 1] !== undefined) {

                $('.back-component-id').attr("comp-id", current_confi_comp_arr[selected_comp - 1]);

            } else {

                $('.back-component-id').attr("disabled", "disabled");

            }



            if (get_query_string('config_link_id')) {

                let selected_configurator = localStorage.getItem("selected_configurator");

                let prod_config_object = localStorage.getItem("product_config_object");

                prod_config_object = JSON.parse(prod_config_object);

                if (prod_config_object) {

                    if (prod_config_object[selected_configurator]) {

                        if (prod_config_object[selected_configurator][comp_id]) {

                            let trig_addon = prod_config_object[selected_configurator][comp_id].addon;

                            $('.addon-d[data-addon="' + trig_addon + '"]').eq(0).attr("checked", "checked");

                            $('.addon-d[data-addon="' + trig_addon + '"]').parents('tr').find('.quantity-d').val(prod_config_object[selected_configurator][comp_id].qty);

                        }

                    }

                }

            } else {

                let selected_configurator = localStorage.getItem("selected_configurator");

                let config_object = localStorage.getItem("config_object");

                config_object = JSON.parse(config_object);

                if (config_object) {

                    if (config_object[selected_configurator]) {

                        if (config_object[selected_configurator][comp_id]) {

                            // multiple addons auto select if in local storage

                            let trig_addon = config_object[selected_configurator][comp_id].addon;

                        

                            for (let i = 0; i < trig_addon.length; i++) {

                                $('.addon-d[data-addon="' + trig_addon[i] + '"]').eq(0).attr("checked", "checked");

                                $('.addon-d[data-addon="' + trig_addon[i] + '"]').parents('tr').find('.quantity-d').val(config_object[selected_configurator][comp_id].qty[i]);

                            }



                            

                            

                        }

                    }

                }

            }



            jQuery('.component-filter-block').show();

        });

    });



    jQuery('body').on('click', '#description-tab , #filters-tab', function (event) {



        if (event.target.id == 'description-tab') {



            $('#filters').hide();

            $('#filters ,#filters-tab').removeClass('active show');

            $(this).addClass('active');

            $('#description').addClass('active show');

            $('#description').show();



        } else if (event.target.id == 'filters-tab') {



            $('#description').hide();

            $('#description ,#description-tab ').removeClass('active show');

            $(this).addClass('active');

            $('#filters').addClass('active show');

            $('#filters').show();

        }

    });



    var jqxhr = { abort: function () { } };

    function component_posts(s) {

        $('.popup-table .component-posts-d').addClass('d-none');

        $('.pre-loader').show();



        let relation = '';

        

        if (localStorage.getItem("config_object") !== undefined &&  !get_query_string('config_link_id')) {

            relation = localStorage.getItem("config_object");

        } else {

            relation = localStorage.getItem("product_config_object");

        }



        jqxhr.abort();

        jqxhr = jQuery.ajax({

            type: 'POST',

            data: {

                'action': 'search_component_posts',

                's': s,

                'manufactuerer': $('.manufacturer-d option:selected').val(),

                'term_filter': term_filter,

                'comp_id': localStorage.getItem("selected_component"),

                'relation': relation,

                'selected_configurator': localStorage.getItem("selected_configurator"),

                'config_link_id': get_query_string('config_link_id')

            },

            url: config_ajax_req.ajaxurl,

            success: function (data) {

                setTimeout(

                    function(){ 

                        jQuery('.pre-loader').hide();

                        $('.component-posts-d').html(data);

                        $('.popup-table .component-posts-d').removeClass('d-none');

                        

                    },

                    1000);

            },

            error: function (e) {

                // Error

            }

        });

    }



    $(document).on("input", ".search-posts", function (event) {

        component_posts(jQuery(this).val());

    });



    $('body').on("change", ".manufacturer-d", function (event) {

        component_posts(jQuery('.search-posts').val());

    });



    var filter_array = [];

    $('body').on("click", ".term-filter-d", function (event) {



        let term_val = $(this).attr("data-id");

        if (term_filter.indexOf(term_val) !== -1) {

            const index = term_filter.indexOf(term_val);

            if (index > -1) {

                term_filter.splice(index, 1);

            }

            $(this).removeClass('active');

        } else {

            $(this).addClass('active');

            term_filter.push(term_val);

        }



        let filter_object = {};

        let comp_filter_selected = localStorage.getItem("comp_filter_selected");

        

        if (comp_filter_selected) {



            comp_filter_selected = JSON.parse(comp_filter_selected);

            if(comp_filter_selected)

            {

                for (const [key, value] of Object.entries(comp_filter_selected)) {

                     let kit_comp_key1 = [];

                    for (const [kit_comp_key, val] of Object.entries(value)) {

                       

                        kit_comp_key1.push(val)

                        filter_object[key] = kit_comp_key1;

                    }

                }

            }



            const filter_index = comp_filter_selected[localStorage.getItem("selected_component")];

                if(!filter_index ){

                    filter_array = [];

                }

                if(!filter_index || filter_index.indexOf(term_val) == -1)

                {

                    filter_array.push(term_val);

                }

                else

                {

                    const index = filter_index.indexOf(term_val);

                    filter_index.splice(index, 1);

                    filter_array = [...filter_index];

                }

        }

        else

        {

            filter_array.push(term_val); 

        }

        

        filter_object[localStorage.getItem("selected_component")] = filter_array;

        

        

        //filter_object[localStorage.getItem("selected_component")][term_val] = term_val

         

        localStorage.setItem("comp_filter_selected", JSON.stringify(filter_object));



        component_posts(jQuery('.search-posts').val());

    });



    $('body').on('click', '.close-component', function () {

        // $('input[name=addon]:checked', '.component-posts-d').trigger("click");



        $('#overlay').hide();

        $('#overlay').html('');

    });



    // product confogurator object by default if criteria match

    if (kits_pre_selected.kits) {

        let get_kit_obj = localStorage.getItem("product_config_object");

        localStorage.setItem("product_config_object", JSON.stringify(kits_pre_selected.kits));

       

    }





    //calculate price on page load

    calculate_price();



    // set the default product id for configurator checkout

    if (config_pre_selected.configurator) {

        update_configurator_prod_id();

    }



    //trigger the delected configurator on page load to show the selected components

    if (config_pre_selected.configurator) {

        $('.show-component-d').each(function () {

            if ($(this).find('.m-list-item.active').length > 0) {

                $(this).click();

            }

        });

    }

    // select multiple or single component addons event

    $('body').on('click change', '.addon-d', function () {

      

        // find class name and then checked false

        $(this).parents('tr').prevAll('tr').find('.unselect-addon').prop('checked',false);

  

        let data_config = parseInt($(this).attr("data-config"));

        let data_comp = parseInt($(this).attr("data-comp"));

        let data_child_comp = parseInt($(this).attr("data-child-comp"));

        let data_addon = parseInt($(this).attr("data-addon"));

        let data_price = parseInt($(this).attr("data-price"));

        let data_addon_title = $(this).parents('tr').find('.pro-title').text();

        let data_qty = $(this).parents('tr').find('.quantity-d').val();

        let parent_comp = $('#component-children-div-d').attr('parent-comp');

        let selected_configurator = localStorage.getItem("selected_configurator");

        let selected_comp = localStorage.getItem("selected_component");

       

        let multi_data_addon = [];

        let multi_data_price = [];

        let multi_data_qty = [];

        let multi_com_title = [];

        // push data of addon array wise when check a checkbox

        $("input[name='addon']:checked").each(function () {

            multi_data_addon.push(parseInt($(this).attr("data-addon")));

            multi_data_qty.push($(this).parents('tr').find('.quantity-d').val());

            multi_data_price.push(parseInt($(this).attr("data-price")));

            multi_com_title.push($(this).parents('tr').find('.pro-title').text());

        });



        // check parent comp is not undefined or not empty

        if(typeof parent_comp != 'undefined' && parent_comp != '') {

            $('[comp-id="' + parent_comp + '"]').addClass("high-green");

        }

        if (typeof data_price == null && typeof data_price == undefined && parseFloat(data_qty) < 1) {

            data_qty = 0;

        }



        if (kits_pre_selected.config_link_id && get_query_string('config_link_id')) {



            let get_kit_obj = localStorage.getItem("product_config_object");

            if (get_kit_obj) {



                get_kit_obj = JSON.parse(get_kit_obj);

                let kit_obj = {};



                let kit_comp_array = {};



                for (const [key, value] of Object.entries(get_kit_obj)) {

                    kit_comp_array = {};

                    for (const [kit_comp_key, val] of Object.entries(value)) {

                        let kit_comp = {};

                        kit_comp.comp_id = value[kit_comp_key].comp_id;

                        kit_comp.addon = value[kit_comp_key].addon;

                        kit_comp.position = value[kit_comp_key].position;

                        kit_comp.price = value[kit_comp_key].price;

                        kit_comp.qty = value[kit_comp_key].qty;

                        kit_comp.title = value[kit_comp_key].title;

                        kit_comp_array[kit_comp_key] = kit_comp;

                        kit_obj[key] = kit_comp_array;

                    }

                }

                let comp_size = Object.size(get_kit_obj[data_config]);

                let pos_check = '';



                if (comp_size > 0) {

                    if (get_kit_obj[data_config]) {

                        if (get_kit_obj[data_config][data_comp]) {

                            pos_check = get_kit_obj[data_config][data_comp].position;

                        } else {

                            comp_size++;

                        }

                    } else {

                        comp_size++;

                    }

                } else {

                    comp_size++;

                }



                //update current selected component object

                let kit_current_comp = {};

                kit_current_comp.comp_id = data_comp;

                kit_current_comp.addon = multi_data_addon;

                kit_current_comp.position = pos_check > 0 ? pos_check : comp_size;

                kit_current_comp.price = multi_data_price;

                kit_current_comp.qty = multi_data_qty;

                kit_current_comp.title = multi_com_title;

                //kit_comp_array[data_comp] = kit_current_comp;

                //kit_obj[data_config] = kit_comp_array;



                if (!kit_obj[data_config]) {

                    kit_obj[data_config] = {};

                }

                kit_obj[data_config][data_comp] = kit_current_comp;



                kit_obj = JSON.stringify(kit_obj);

                localStorage.setItem("product_config_object", kit_obj);

                // if child component parent exit then enter

                if(typeof parent_comp != 'undefined' && parent_comp != '') {

                    get_config_component_child(parent_comp);

                }

                // if no child component exit then enter

                if (!data_child_comp) {

                    get_config_component_child(selected_configurator);

                }

            }



        } else {



            let config_obj = {};

            let get_config_obj = localStorage.getItem("config_object");

            get_config_obj = JSON.parse(get_config_obj);



            if ((get_config_obj !== null && get_config_obj !== undefined) && get_config_obj.hasOwnProperty(data_config)) {

                let comp_array = {};
                var relation_value = '';

                if (get_query_string('config_link_id')) {
                    relation_value =  localStorage.getItem("product_config_object");
                } else {
                    relation_value =  localStorage.getItem("config_object");

                }

                var selected_config = localStorage.getItem("selected_configurator")
                var list_com = $('.list_components_class'+selected_config).val();
                if(!isNaN(data_child_comp)) {
                    $('.show-child-comp-d').removeClass("high-green");
                    let current_configurator_comp = localStorage.getItem("current_configurator_comp");
                    let current_confi_comp_arr = current_configurator_comp.split(',');
                        for (const [key, value] of Object.entries(get_config_obj)) {
                        
                                comp_array = {};
                          
                                for (const [comp_key, val] of Object.entries(value)) {
                                     let comp = {};
                                    
                                    comp.comp_id = val.comp_id;

                                    comp.addon = val.addon;

                                    comp.position = val.position;

                                    comp.price = val.price;

                                    comp.qty = val.qty;

                                    comp.title = val.title;

                                    comp_array[comp_key] = comp;

                                    config_obj[key] = comp_array;

                                    if(jQuery.inArray(comp_key, current_confi_comp_arr) === -1) {
                                        delete config_obj[selected_config];
                                    }
                                   
                                    
                                }

                        
                    } 
                    $('.show-child-comp-d[comp-id="' + parent_comp + '"]').addClass("high-green");
                } else {
                    for (const [key, value] of Object.entries(get_config_obj)) {

                        comp_array = {};

                        for (const [comp_key, val] of Object.entries(value)) {

                            let comp = {};



                            comp.comp_id = val.comp_id;

                            comp.addon = val.addon;

                            comp.position = val.position;

                            comp.price = val.price;

                            comp.qty = val.qty;

                            comp.title = val.title;

                            comp_array[comp_key] = comp;

                            config_obj[key] = comp_array;

                        }
                    }

                }
                // console.log(config_obj);





                let comp_size = Object.size(get_config_obj[data_config]);

                let pos_check = '';



                if (comp_size > 0) {

                    if (config_obj[data_config]) {

                        if (config_obj[data_config][data_comp]) {

                            pos_check = config_obj[data_config][data_comp].position;

                        } else {

                            comp_size++;

                        }

                    } else {

                        comp_size++;

                    }

                } else {

                    comp_size++;

                }
                //update current selected component object

                let current_comp = {};

                current_comp.comp_id = data_comp;

                current_comp.addon = multi_data_addon;

                current_comp.position = pos_check > 0 ? pos_check : comp_size;

                current_comp.price = multi_data_price;

                current_comp.qty = multi_data_qty;

                current_comp.title = multi_com_title;





                if (!config_obj[data_config]) {

                    config_obj[data_config] = {};

                }

                config_obj[data_config][data_comp] = current_comp;



            } else {

                let comp_array = {};

                if ((get_config_obj !== null && get_config_obj !== undefined)) {

                    for (const [key, value] of Object.entries(get_config_obj)) {

                        comp_array = {};

                        for (const [comp_key, val] of Object.entries(value)) {

                            let comp = {};

                            comp.comp_id = val.comp_id;

                            comp.addon = val.addon;

                            comp.position = val.position;

                            comp.price = val.price;

                            comp.qty = val.qty;

                            comp.title = val.title;

                            comp_array[comp_key] = comp;

                            config_obj[key] = comp_array;

                        }

                    }

                }



                let current_comp = {};

                current_comp.comp_id = data_comp;

                current_comp.addon = multi_data_addon;

                current_comp.position = 1;

                current_comp.price = multi_data_price;

                current_comp.qty = multi_data_qty;

                current_comp.title = multi_com_title;

                if (!config_obj[data_config]) {

                    config_obj[data_config] = {};

                }

                config_obj[data_config][data_comp] = current_comp;

            }

            

            for (const [key, value] of Object.entries(config_obj)) {

                for (const [comp_key, val] of Object.entries(value)) {

                    if (key == selected_configurator) {

                        if(selected_comp == val.comp_id && val.addon.length == 0) {

                            $('.component-filter-box[comp-id="' + selected_comp + '"]').removeClass("high-green");

                            $('.config-overview-d[data-comp="' + selected_comp + '"]').removeClass("green-highlight");

                            delete value[comp_key];

                            if(typeof parent_comp != 'undefined' && parent_comp != '') {


                               $('.show-child-comp-d[comp-id="' + parent_comp + '"]').removeClass("high-green");

                            }

                        } 

                    }

                }

            }

            config_obj = JSON.stringify(config_obj);

            localStorage.setItem("config_object", config_obj);

            if (!data_child_comp) {

                get_config_component('component', selected_configurator);

                get_config_component('config_overview', selected_configurator);

            }

            if(typeof parent_comp != 'undefined' && parent_comp != '') {

                get_config_component_child(parent_comp);

            }

        }

        pre_selected_comp_hightlight();

        calculate_price();

    });



    if ($("input[name=list_component]").length > 0) {



        localStorage.setItem("current_configurator_comp", $("input[name=list_component]").val());

        localStorage.setItem("selected_configurator", $("input[name=selected_configurator]").val());

    }





    $('body').on('click', '.show-child-comp-d', async function () {

     
        

        $('.pre-loaderTab').show();

        $('#component-children-div-d').html('');

        $('#component-children-div-d').hide();

        // add relation value and send it to post

        let relation_value = '';

       

        if (get_query_string('config_link_id')) {

            // if config_link_id exit

            relation_value =  localStorage.getItem("product_config_object");

        } else {

             relation_value =  localStorage.getItem("config_object");

        }





        let list_to_update = ['child_component', 'config_child_overview'];

        for (let i = 0; i < list_to_update.length; i++) {

            var data = {

                'action': 'get_child_component',

                'comp_id': $(this).attr("comp-id"),

                'config_link_id': get_query_string('config_link_id'),

                'type': list_to_update[i],

                'relation': relation_value,

                'selected_component': localStorage.getItem("selected_configurator"),

            };

            let load_child_comp = await prepare_ajax_request(data);

            jQuery('.pre-loaderTab').hide();

            if (load_child_comp.length && list_to_update[i] == 'child_component') {

                // add current comp select into parent comp attribute

                $('#component-children-div-d').attr('parent-comp',$(this).attr("comp-id"));

                $('#component-children-div-d').html(load_child_comp);

                $('#component-children-div-d').show();

                if ($("input[name=list_component1]").length > 0) {



                    $("input[name=list_component]").val($("input[name=list_component1]").val())

                    localStorage.setItem("current_configurator_comp", $("input[name=list_component1]").val());

                    localStorage.setItem("selected_configurator", $("input[name=selected_configurator]").val());

                }

            }

            else {

                jQuery('#configurator-overview').html(load_child_comp);

            }

        }

        // jQuery.post(config_ajax_req.ajaxurl, data, function (response) {



        //     if (response.length) {

        //         $('#component-children-div-d').html(response);

        //         $('#component-children-div-d').show();

        //         if ($("input[name=list_component1]").length > 0) {



        //             $("input[name=list_component]").val($("input[name=list_component1]").val())

        //             localStorage.setItem("current_configurator_comp", $("input[name=list_component1]").val());

        //             localStorage.setItem("selected_configurator", $("input[name=selected_configurator]").val());

        //         }

        //     }

        // });

    });



    $('body').on('click', '.add-to-cart-d', async function () {

        var count_request_data = {

                'action': 'cart_count_retriever',

                'edit_product' : get_query_string('edit-product')

            };

        let get_count = await prepare_ajax_request(count_request_data);

        // update counter

        $('#header_cart span').html(get_count);




        if (get_query_string('config_link_id')) {
            var share_request_data = {
                'action': 'cart_share_link',
                'selected_addons': localStorage.getItem("product_config_object"),
                'cart_link_id' : $(this).attr("cart-link-id"),
                'curr_user' : $(this).attr("curr-user"),
            };
        } else {
             var share_request_data = {
                'action': 'cart_share_link',
                'selected_addons': localStorage.getItem("config_object"),
                'cart_link_id' : $(this).attr("cart-link-id"),
                'curr_user' : $(this).attr("curr-user"),
            };
        }
        let share_data = await prepare_ajax_request(share_request_data);
        let data_response = $.parseJSON(share_data);
        $(this).attr("curr-user", data_response.curr_user);
        $(this).attr("cart-link-id", data_response.cart_link_id);




        let data = {};

        if (kits_pre_selected.kits && get_query_string('config_link_id')) {

            data = {

                'action': 'configurator_cart',

                'config_link_id': kits_pre_selected.config_link_id,

                'selected_configurator': $('.m-list-item.active').parents('.show-component-d').attr('config-id'),

                'product_id': kits_pre_selected.config_link_id,

                'configurator_detail': localStorage.getItem("product_config_object"),

                'edit_product' : get_query_string('edit-product')

            };

        }

        else {

            data = {

                'action': 'configurator_cart',

                //'config_link_id': kits_pre_selected.config_link_id,

                // select current config id

                'selected_configurator': $('.m-list-item.active').parents('.show-component-d').attr('config-id'),

                'product_id': $("input[name=config_product_id]").val(),

                'configurator_detail': localStorage.getItem("config_object"),

                'edit_product' : get_query_string('edit-product')

            };

        }



        let missing = '';

        let new_missing = '';

        $('.config-overview-d.red-highlight').each(function () {

            $(this).find('h5').text();

            

            missing += $(this).find('h5').text() + " , ";

           

           

        })

         new_missing = missing.replace(/\:/g, '');

         

        alert("You are missing the required components " + new_missing + " but you can proceed to the payment process");



        let add_to_cart_kits = await prepare_ajax_request(data);





    });

    



    $('body').on('click', '.reset-config-d', async function () {



        localStorage.removeItem("comp_filter_selected");

        if (get_query_string('config_link_id')) {

            localStorage.removeItem("product_config_object");

            window.location.reload();

        } else {



            let config_obj = {};

            let selected_configurator = localStorage.getItem("selected_configurator");

            let get_config_obj = localStorage.getItem("config_object");

            if (get_config_obj) {

                let comp_array = {};

                get_config_obj = JSON.parse(get_config_obj);

                if ((get_config_obj !== null && get_config_obj !== undefined)) {

                    for (const [key, value] of Object.entries(get_config_obj)) {

                        comp_array = {};

                        for (const [comp_key, val] of Object.entries(value)) {

                            if (key !== selected_configurator) {

                                let comp = {};

                                comp.comp_id = val.comp_id;

                                comp.addon = val.addon;

                                comp.position = val.position;

                                comp.price = val.price;

                                comp.qty = val.qty;

                                comp.title = val.title;

                                comp_array[comp_key] = comp;

                                config_obj[key] = comp_array;

                            }

                        }

                    }

                }

                if (config_obj) {

                    config_obj = JSON.stringify(config_obj);

                    localStorage.setItem("config_object", config_obj);

                } else {

                    localStorage.removeItem("config_object");

                }

                $(".component-filter-box").removeClass("high-green");

                jQuery('.config-price-d').text("€ 0.00");

            }

        }

        // reset current config overview classes

        $('.show-component-d').each(function () {

            if ($(this).find('.m-list-item.active').length > 0) {

                $(this).click();

            }

        });

    });

    $(document).on('click','.print_data_sheet', async function () {

        let data = {};

        let current_comp_id = $(this).data('post');

   

        data = {

            'action': 'print_data_sheet_ajax',

            // 'selected_addons': localStorage.getItem("config_object"),

            'current_comp_id':current_comp_id,

        };

        let data_sheet_response = await prepare_ajax_request(data);

        data_sheet_response = $.parseJSON(data_sheet_response);

     

        if(data_sheet_response.type == 'success') {

            window.open(

                data_sheet_response.redirect,

                '_blank' // <- This is what makes it open in a new window.

            );



        } else if(data_sheet_response.type == 'error') {



        }

       

       

    });



    // dynamic bootstrap popup modal script

    function modal(header, body, footer, size, center, callback,classes) {

        header = header !== undefined ? header : 'Modal header';

        body = body !== undefined ? body : 'Modal body';

        footer = footer !== undefined ? footer : 'Modal footer';

        center = center !== undefined ? 'modal-dialog-centered' : '';

        size = size !== undefined ? size : '';

        classes = classes !== undefined ? classes : '';

        let closeBtn = `<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>`;

        let $modalId = new Date().getSeconds();

        let $modal = `<div class="modal fade ${classes}" tabindex="-1" role="dialog" id="modal-${$modalId}">

          <div class="modal-dialog ${center} ${size}" role="document">

            <div class="modal-content border-orange">

              <div class="modal-header">

                ${header}${closeBtn}

              </div>

              <div class="modal-body">

                ${body}

              </div>

            </div>

          </div>

        </div>`;



        jQuery(document.body).append($modal);

        jQuery('#modal-'+$modalId).modal('show');



        jQuery(document).on('hidden.bs.modal', '#modal-'+$modalId, function(e) {

          jQuery('#modal-'+$modalId).remove();

        });

        if (callback !== undefined && typeof callback == 'function') {

          return callback('modal-'+$modalId);

        }

    }

    $(document).on('click','.share_link_data', async function () {

        let data = {};

        let disabled_ai_messages = localStorage.getItem("disabled_ai_messages");

        data = {

            'action': 'share_link_data',

            'selected_addons': localStorage.getItem("config_object"),

            'share_link_id' : $(this).attr("share-id"),

        };

        let data_link_response = await prepare_ajax_request(data);

        let data_parse = JSON.parse(data_link_response);

        

        $('.share_link_data').attr("share-id", data_parse.share_link_id);

        let header = '<h5>Share or send configuration / link </h5>';

        var share_link_text = `<p>Copy this link / URL in your email or into a forum post to share this configuration.</p>

        <h5> Link to this configuration:</h5>`;

        let share_link_html = `<input type="text" onfocus="this.select()" style="width:100%;" value="${data_parse.site_url}/custom-configurator/?share_link_id=${data_parse.share_link_id}">`;

        if(disabled_ai_messages == 'yes') {

            modal(header,'loading ','','',false, function(modal_Id){

                $('#'+modal_Id).find('.modal-body').html(`${share_link_text} ${share_link_html}`);

            },'');  

        }

        let popup_msg = $(this).attr("popup-attribute");

        if(disabled_ai_messages == null) {

            $.post(ajax_object.ajax_url, {action: 'config_ai_messages_show',  current_page: '', popup_msg, popup_msg}).done(response => {

                if ($.trim(response)){ 

                    $('.dynamic_content').html(`${response} ${share_link_html}`);

                    $('.popup-wrapper').show();

                }

            });

            // setTimeout(function() {

            //     $('.popup-wrapper').hide();

            // },7000);

        }

    });

    // check if components_object has value or not, if yes then set config object (localstorage) with

    // share link id value



    if (components_object.components_data) {

        localStorage.setItem("config_object", JSON.stringify(components_object.components_data));

        // config object first time not set so call function calculate_price 

        calculate_price();

    }

    // set selected component in component overview section

    if(components_object.selected_component) {

        localStorage.setItem("selected_component", JSON.stringify(components_object.selected_component));

        let shared_config_obj = localStorage.getItem("config_object");

        shared_config_obj = JSON.parse(shared_config_obj);

        let config_first_key = Object.keys(shared_config_obj)[0];

        if(config_first_key != '') {

            setTimeout(function() {

                $('.show-component-d[config-id="' + config_first_key + '"]').click();

            },1000); 

        }

    }

    // unselect the addon

    $(document).on('click', '.unselect-addon', async function () {



        let config_obj = {};

        let selected_configurator = localStorage.getItem("selected_configurator");

        let selected_comp = $(this).val();

        let get_config_obj = localStorage.getItem("config_object");

        $(this).parents('tr').nextAll('tr').find('.addon-d').prop('checked',false);

        if (get_config_obj) {

            let comp_array = {};

            get_config_obj = JSON.parse(get_config_obj);

            if ((get_config_obj !== null && get_config_obj !== undefined)) {

                for (const [key, value] of Object.entries(get_config_obj)) {

                    comp_array = {};

                    for (const [comp_key, val] of Object.entries(value)) {

            

                        if (key == selected_configurator) {

                            if(selected_comp == val.comp_id) {

                            $('.component-filter-box[comp-id="' + selected_comp + '"]').removeClass("high-green");

                            $('.config-overview-d[data-comp="' + selected_comp + '"]').removeClass("green-highlight");

                                delete value[comp_key];

                            } else {

                                let comp = {};

                                comp.comp_id = val.comp_id;

                                comp.addon = val.addon;

                                comp.position = val.position;

                                comp.price = val.price;

                                comp.qty = val.qty;

                                comp.title = val.title;

                                comp_array[comp_key] = comp;

                                config_obj[key] = comp_array;



                            }

                        }

                    }

                }

            }

            if(config_obj) {

                calculate_price();

                config_obj = JSON.stringify(config_obj);

                localStorage.setItem("config_object", config_obj);

            }

        }

        // reset the config overview

        $('.show-component-d').each(function () {

            if ($(this).find('.m-list-item.active').length > 0) {

                $(this).click();

            }

        });

        

    });

    // refresh after add addon

    function get_config_component_child(comp_id) {

        let relation_value = '';

        if (get_query_string('config_link_id')) {

            relation_value =  localStorage.getItem("product_config_object");

        } else {

            relation_value =  localStorage.getItem("config_object");

        }

        var data = {

            'action': 'get_child_component',

            'comp_id': comp_id,

            'config_link_id': get_query_string('config_link_id'),

            'type': 'config_child_overview',

            'relation': relation_value,

            'selected_component': localStorage.getItem("selected_configurator"),

        };



        jQuery.post(config_ajax_req.ajaxurl, data, function (response) {

            $('#configurator-overview').html(response);

            

        });

    }

    function arrays_multiple(array1, array2) {

        const result = [];

        let ctr = 0;

        let x = 0;

        let sum = 0;

        while (ctr < array1.length && ctr < array2.length) {

            result.push(array1[ctr] * array2[ctr]);

            ctr++;

        }



        if (ctr === array1.length) 

        {

            for (x = ctr; x < array2.length; x++)   {

                result.push(array2[x]);

            }

        } else {

            for (x = ctr; x < array1.length; x++) {

                result.push(array1[x]);

            }

        }

       

        sum = result.reduce(function(result, b) { return result + b; }, 0);

        return sum;

    }

    function cal_config_discount(config_id, total_price) {

        let curr_config_discount_price = 0;

        let curr_config_subtotal_price = 0;

        let html_content = '';

        let subtotal_content = '';

        jQuery('.discount_content').empty();

        jQuery('.sub_total_content').empty();

        var data = {

            'action': 'cal_config_discount',

            'config_id': config_id,

            'total_price': total_price,

        };



        jQuery.post(config_ajax_req.ajaxurl, data, function (response) {

           let parse_data =  jQuery.parseJSON(response);



           curr_config_discount_price = parse_data.discount_price;

           curr_config_subtotal_price = parse_data.sub_total;

            

            if(parse_data.type == 'success') {

                html_content = `<span class="config-discount-d">Discount</span><span>€ ${curr_config_discount_price}</span>

                                `;

                subtotal_content = `<span class="config-discount-d">SubTotal</span><span>€ ${curr_config_subtotal_price}</span>

                                `;

                jQuery('.discount_content').html(html_content);

                jQuery('.sub_total_content').html(subtotal_content);

                



            }

          

            

        });

    }

    // change quantity based product

     $('body').on('change', '.quantity-d', function () {

   

        // find class name and then checked false

        $(this).parents('tr').prevAll('tr').find('.unselect-addon').prop('checked',false);

        $(this).parents('tr').find('.addon-d').prop("checked",true);

        let data_config = $(this).parents('tr').find('.addon-d').attr("data-config");

    

        let data_comp = $(this).parents('tr').find('.addon-d').attr("data-comp");

        let data_child_comp = $(this).parents('tr').find('.addon-d').attr("data-child-comp");

        let data_addon = $(this).parents('tr').find('.addon-d').attr("data-addon");

        let data_price = $(this).parents('tr').find('.addon-d').attr("data-price");

        let data_addon_title = $(this).parents('tr').find('.pro-title').text();

        let data_qty = $(this).val();

        let parent_comp = $('#component-children-div-d').attr('parent-comp');

        let selected_configurator = localStorage.getItem("selected_configurator");

        let selected_comp = localStorage.getItem("selected_component");

        let multi_data_addon = [];

        let multi_data_price = [];

        let multi_data_qty = [];

        let multi_com_title = [];

        // push data of addon array wise when check a checkbox

        $("input[name='addon']:checked").each(function () {

            multi_data_addon.push($(this).parents('tr').find('.addon-d').attr("data-addon"));

            multi_data_qty.push($(this).parents('tr').find('.quantity-d').val());

            multi_data_price.push($(this).parents('tr').find('.addon-d').attr("data-price"));

            multi_com_title.push($(this).parents('tr').find('.pro-title').text());

        });





        // check parent comp is not undefined or not empty

        if(typeof parent_comp != 'undefined' && parent_comp != '') {

            $('[comp-id="' + parent_comp + '"]').addClass("high-green");

        }

        if (typeof data_price == null && typeof data_price == undefined && parseFloat(data_qty) < 1) {

            data_qty = 0;

        }



        if (kits_pre_selected.config_link_id && get_query_string('config_link_id')) {



            let get_kit_obj = localStorage.getItem("product_config_object");

            if (get_kit_obj) {



                get_kit_obj = JSON.parse(get_kit_obj);

                let kit_obj = {};



                let kit_comp_array = {};



                for (const [key, value] of Object.entries(get_kit_obj)) {

                    kit_comp_array = {};

                    for (const [kit_comp_key, val] of Object.entries(value)) {

                        let kit_comp = {};

                        kit_comp.comp_id = value[kit_comp_key].comp_id;

                        kit_comp.addon = value[kit_comp_key].addon;

                        kit_comp.position = value[kit_comp_key].position;

                        kit_comp.price = value[kit_comp_key].price;

                        kit_comp.qty = value[kit_comp_key].qty;

                        kit_comp.title = value[kit_comp_key].title;

                        kit_comp_array[kit_comp_key] = kit_comp;

                        kit_obj[key] = kit_comp_array;

                    }

                }





                let comp_size = Object.size(get_kit_obj[data_config]);

                let pos_check = '';



                if (comp_size > 0) {

                    if (get_kit_obj[data_config]) {

                        if (get_kit_obj[data_config][data_comp]) {

                            pos_check = get_kit_obj[data_config][data_comp].position;

                        } else {

                            comp_size++;

                        }

                    } else {

                        comp_size++;

                    }

                } else {

                    comp_size++;

                }



                //update current selected component object

                let kit_current_comp = {};

                kit_current_comp.comp_id = data_comp;

                kit_current_comp.addon = multi_data_addon;

                kit_current_comp.position = pos_check > 0 ? pos_check : comp_size;;

                kit_current_comp.price = multi_data_price;

                kit_current_comp.qty = multi_data_qty;

                kit_current_comp.title = multi_com_title;

                //kit_comp_array[data_comp] = kit_current_comp;

                //kit_obj[data_config] = kit_comp_array;



                if (!kit_obj[data_config]) {

                    kit_obj[data_config] = {};

                }

                kit_obj[data_config][data_comp] = kit_current_comp;



                kit_obj = JSON.stringify(kit_obj);

                localStorage.setItem("product_config_object", kit_obj);

                // if child component parent exit then enter

                if(typeof parent_comp != 'undefined' && parent_comp != '') {

                    get_config_component_child(parent_comp);

                }

                // if no child component exit then enter

                if (!data_child_comp) {

                    get_config_component_child(selected_configurator);

                }

            }



        } else {



            let config_obj = {};

            let get_config_obj = localStorage.getItem("config_object");

            get_config_obj = JSON.parse(get_config_obj);



            if ((get_config_obj !== null && get_config_obj !== undefined) && get_config_obj.hasOwnProperty(data_config)) {

                let comp_array = {};



                for (const [key, value] of Object.entries(get_config_obj)) {

                    comp_array = {};

                    for (const [comp_key, val] of Object.entries(value)) {

                        let comp = {};



                        comp.comp_id = val.comp_id;

                        comp.addon = val.addon;

                        comp.position = val.position;

                        comp.price = val.price;

                        comp.qty = val.qty;

                        comp.title = val.title;

                        comp_array[comp_key] = comp;

                        config_obj[key] = comp_array;

                    }

                }



                let comp_size = Object.size(get_config_obj[data_config]);

                let pos_check = '';



                if (comp_size > 0) {

                    if (config_obj[data_config]) {

                        if (config_obj[data_config][data_comp]) {

                            pos_check = config_obj[data_config][data_comp].position;

                        } else {

                            comp_size++;

                        }

                    } else {

                        comp_size++;

                    }

                } else {

                    comp_size++;

                }



                //update current selected component object

                let current_comp = {};

                current_comp.comp_id = data_comp;

                current_comp.addon = multi_data_addon;

                current_comp.position = pos_check > 0 ? pos_check : comp_size;

                current_comp.price = multi_data_price;

                current_comp.qty = multi_data_qty;

                current_comp.title = multi_com_title;





                if (!config_obj[data_config]) {

                    config_obj[data_config] = {};

                }

                config_obj[data_config][data_comp] = current_comp;



            } else {

                let comp_array = {};

                if ((get_config_obj !== null && get_config_obj !== undefined)) {

                    for (const [key, value] of Object.entries(get_config_obj)) {

                        comp_array = {};

                        for (const [comp_key, val] of Object.entries(value)) {

                            let comp = {};

                            comp.comp_id = val.comp_id;

                            comp.addon = val.addon;

                            comp.position = val.position;

                            comp.price = val.price;

                            comp.qty = val.qty;

                            comp.title = val.title;

                            comp_array[comp_key] = comp;

                            config_obj[key] = comp_array;

                        }

                    }

                }



                let current_comp = {};

                current_comp.comp_id = data_comp;

                current_comp.addon = multi_data_addon;

                current_comp.position = 1;

                current_comp.price = multi_data_price;

                current_comp.qty = multi_data_qty;

                current_comp.title = multi_com_title;

                if (!config_obj[data_config]) {

                    config_obj[data_config] = {};

                }

                config_obj[data_config][data_comp] = current_comp;

            }

            for (const [key, value] of Object.entries(config_obj)) {

                for (const [comp_key, val] of Object.entries(value)) {

                    if (key == selected_configurator) {

                        if(selected_comp == val.comp_id && val.addon.length == 0) {

                            $('.component-filter-box[comp-id="' + selected_comp + '"]').removeClass("high-green");

                            $('.config-overview-d[data-comp="' + selected_comp + '"]').removeClass("green-highlight");

                            delete value[comp_key];

                            if(typeof parent_comp != 'undefined' && parent_comp != '') {

                               $('.show-child-comp-d[comp-id="' + parent_comp + '"]').removeClass("high-green");

                            }

                        } 

                    }

                }

            }

            config_obj = JSON.stringify(config_obj);

            localStorage.setItem("config_object", config_obj);

            if (!data_child_comp) {

                get_config_component('config_overview', selected_configurator);

            }

            if(typeof parent_comp != 'undefined' && parent_comp != '') {

                get_config_component_child(parent_comp);

            }

        }

        pre_selected_comp_hightlight();

        calculate_price();

    });

    let config_first_key = localStorage.getItem("selected_configurator_edit");

    if(config_first_key != '') {



        setTimeout(function() {

            $('.show-component-d[config-id="' + config_first_key + '"]').click();

            localStorage.setItem("selected_configurator_edit",'');

        },1000); 

    }

    $(document).on('click','.pdf_get',  function () {

        let pdf_get =  $(this).data('url');

        window.open(

            pdf_get,

            '_blank' // <- This is what makes it open in a new window.

        );

    });
     $('body').on('click', '.pdf-config', async function () {
        let data = {};
        let selected_configurator = localStorage.getItem("selected_configurator");
        data = {
            'action': 'download_configurator_pdf',
            'current_configurator_id':selected_configurator,
        };
        let response = await prepare_ajax_request(data);
        let res = JSON.parse(response);
        if(res.type == 'success') {
            window.open(res.attachment, '_blank')
        }
    });



});