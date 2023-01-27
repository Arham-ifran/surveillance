jQuery(document).ready(function ($) {
    "use strict";

    async function get_condition_components(config_val) {
        return new Promise((resolve, reject) => {
            var data = {
                'action': 'component_conditions',
                'configurator_id': config_val,
                //'current_post_id': $('#curr_post_id').val(),
                //'source_selected_id' : $("#configurator_component").val()
            };
            jQuery.post(config_admin_ajax_req.ajaxurl, data)
                .then(function (res) {
                    resolve(res);
                })
                .catch(function (xhr, status, error) {
                    reject(true);
                });
        });
    }

    async function condition_component_tags(comp_value) {
        return new Promise((resolve, reject) => {
            var data = {
                'action': 'get_condition_component_tags',
                'component': comp_value,
            };

            jQuery.post(config_admin_ajax_req.ajaxurl, data)
                .then(function (res) {
                    resolve(res);
                })
                .catch(function (xhr, status, error) {
                    reject(true);
                });
        });
    }

    function add_component_condition() {

        let html_ = '';
        let group_length = $('.condition-block').length;

        html_ += '<div class="condition-block group-' + group_length + '"  group-id="' + group_length + '">';
        html_ += '<p class="form-field">';
        html_ += '<label class="custom-post-label-block-s">Configurator Types</label>';
        html_ += '<select  class="condition_configurator_id" name="configurator_id[' + group_length + '][]">' + $('.condition_configurator_id').eq(0).html() + '</select>';
        html_ += '</p>';
        html_ += '<p class="form-field">';
        html_ += '<label class="custom-post-label-block-s">Components</label>';
        html_ += '<select  class="condition_configurator_component" name="component[' + group_length + '][]">';
        html_ += '</select>';
        html_ += '</p>';

        html_ += '<p class="form-field child-comp-block-d" style="display:none;" >';
        html_ += '<label class="custom-post-label-block-s">Child Components</label>';
        html_ += '<select  class="condition_configurator_child_component" name="child_component[' + group_length + '][]">';
        html_ += '</select>';
        html_ += '</p>';

        html_ += '<p class="form-field">';
        html_ += '<label class="custom-post-label-block-s">Filters</label>';
        html_ += '<select multiple="multiple"  class="cond_tags cond_tags-s" name="filter[' + group_length + '][]">';
        html_ += '<option value=""></option>';
        html_ += '</select>';
        html_ += '</p>';
        html_ += '<p class="form-field">';
        html_ += '<label class="custom-post-label-block-s">Compulsory:</label>';
        html_ += '<select  class="condition_compulsory_component" name="compulsory_component[' + group_length + '][]">';
        html_ += '<option value=""></option>';
        html_ += '</select>';
        html_ += '</p>';
        html_ += '<p class="form-field">';
        html_ += '<label class="custom-post-label-block-s">Optional:</label>';
        html_ += '<select  class="condition_optional_component" name="optional_component[' + group_length + '][]">';
        html_ += '<option value=""></option>';
        html_ += '</select>';
        html_ += '</p>';

        html_ += '</div>';

        $('.append-block-div-d').append(html_);
    }


    function SimpleSelect2(element) {
        element.select2({
            multiple: true,
            //minimumInputLength: 1,
            //placeholder: "Select option",
            //allowClear: true,
        });
    }

    let comp_resp = '';
    $('body').on('change', '.condition_configurator_id', async function () {

        let group_id = $(this).parents('.condition-block').attr('group-id');
        $(".group-" + group_id + ' ' + ".condition_configurator_component").html('').select2();

        //remove the optional and compulsory on change configurator if already selected
        $(".group-" + group_id + ' ' + '.condition_compulsory_component').html('');
        $(".group-" + group_id + ' ' + '.condition_optional_component').html('');
        SimpleSelect2($(".group-" + group_id + ' ' + '.condition_compulsory_component'));
        SimpleSelect2($(".group-" + group_id + ' ' + '.condition_optional_component'));


        comp_resp = await get_condition_components($('option:selected', this).val());
        let comp_json_resp = JSON.parse(comp_resp);
        if (comp_json_resp.source_components.length > 0) {

            $(".group-" + group_id + ' ' + ".condition_configurator_component").html(comp_json_resp.source_components).select2();
        } else {
            $(".group-" + group_id + ' ' + ".condition_configurator_component").html('').select2();
        }

        $(".group-" + group_id + ' ' + ".condition_configurator_component").val($(".group-" + group_id + ' ' + "#condition_configurator_component option:selected").val()).change();
    });
    $(".group-0 .condition_configurator_id").val($(".group-0 .condition_configurator_id option:selected").val()).change();

    $('body').on('change', '.condition_configurator_component', async function () {

        if ($('option:selected', this).val()) {
            let group_id = $(this).parents('.condition-block').attr('group-id');

            let comp_tags = await condition_component_tags($('option:selected', this).val());
            comp_tags = JSON.parse(comp_tags);

            if (comp_tags.child_component) {

                $('.child-comp-block-d').show();
                //filter tag empty before loading child component
                $(".group-" + group_id + ' ' + '.cond_tags').html('');
                let tags_html = '';
                let selected_ = '';
                tags_html += '<option value="" >Please select </option>';
                $.each(comp_tags.child_component, function (i, item) {
                    tags_html += '<option value="' + item.id + '" ' + selected_ + ' ' + item.disabled + '>' + item.text + '</option>';
                });
                $(".group-" + group_id + ' ' + '.condition_configurator_child_component').html(tags_html);

            } else if (comp_tags.component_tags) {
                let tags_html = '';
                let selected_ = '';
                $.each(comp_tags.component_tags, function (i, item) {
                    tags_html += '<option value="' + item.id + '" ' + selected_ + ' >' + item.text + '</option>';
                });

                $(".group-" + group_id + ' ' + '.cond_tags').html(tags_html);
                SimpleSelect2($(".group-" + group_id + ' ' + '.cond_tags'));
            }
            else {
                $('.child-comp-block-d').hide();
                $(".group-" + group_id + ' ' + '.cond_tags').html('');
                $(".group-" + group_id + ' ' + '.condition_configurator_child_component').html('');
            }

            let comp_json_resp = JSON.parse(comp_resp);
            if (comp_tags.child_component) {
                let comp_app = '';
                comp_app += '<option value="">Please select</option>';
                $.each(comp_tags.echild_component, function (i, item) {
                    comp_app += '<option value="' + item.id + '" >' + item.text + '</option>';
                });

                $(".group-" + group_id + ' ' + '.condition_compulsory_component').html(comp_app);
                $(".group-" + group_id + ' ' + '.condition_optional_component').html(comp_app);
                SimpleSelect2($(".group-" + group_id + ' ' + '.condition_compulsory_component'));
                SimpleSelect2($(".group-" + group_id + ' ' + '.condition_optional_component'));
            }
            else if (comp_json_resp.source_components.length > 0) {
                let comp_app = '';
                comp_app += '<option value="">Please select</option>';
                comp_app += comp_json_resp.esource_components;
                console.log(comp_json_resp.source_components);
                $(".group-" + group_id + ' ' + '.condition_compulsory_component').html(comp_app);
                $(".group-" + group_id + ' ' + '.condition_optional_component').html(comp_app);
                SimpleSelect2($(".group-" + group_id + ' ' + '.condition_compulsory_component'));
                SimpleSelect2($(".group-" + group_id + ' ' + '.condition_optional_component'));
            }
            else {
                $(".group-" + group_id + ' ' + '.condition_compulsory_component').html('');
                $(".group-" + group_id + ' ' + '.condition_optional_component').html('');
                SimpleSelect2($(".group-" + group_id + ' ' + '.condition_compulsory_component'));
                SimpleSelect2($(".group-" + group_id + ' ' + '.condition_optional_component'));
            }
        }
    });


    //on change child component update filter tags    
    $('body').on('change', '.condition_configurator_child_component', async function () {
        let group_id = $(this).parents('.condition-block').attr('group-id');
        if ($('option:selected', this).val()) {

            let comp_tags = await condition_component_tags($('option:selected', this).val());
            comp_tags = JSON.parse(comp_tags);

            if (comp_tags.component_tags) {
                let tags_html = '';
                let selected_ = '';
                $.each(comp_tags.component_tags, function (i, item) {
                    tags_html += '<option value="' + item.id + '" ' + selected_ + ' >' + item.text + '</option>';
                });

                $(".group-" + group_id + ' ' + '.cond_tags').html(tags_html);
                SimpleSelect2($(".group-" + group_id + ' ' + '.cond_tags'));
            }
            else {
                $(".group-" + group_id + ' ' + '.cond_tags').html('');
            }
        }
        else {
            $(".group-" + group_id + ' ' + '.cond_tags').html('');
        }
    });




    $('body').on('click', '.del-validate-cond-d', function () {
        var data = {
            'action': 'delete_validate_cond',
            'id': $(this).attr("id"),
        };
        jQuery.post(config_admin_ajax_req.ajaxurl, data, function (response) {
            window.location.reload();
        });
    });

    $('.append-block-d').click(function () {
        add_component_condition();
    });

}); 