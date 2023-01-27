jQuery(document).ready(function ($) {

	let show_kit_section = '';

	// add optgroup at category filter at configurator post type
	
	let s_kits = $('#s_kits').val();
	let selected_kits = '';
	if(s_kits == 'yes') {
		selected_kits = 'checked';
		show_kit_section = '';
	} else {
		selected_kits = '';
		show_kit_section = 'show_kits_section';
	}
	$('.prefix_kit_tab').addClass(show_kit_section);
	$('#_downloadable').parent().after(`<label for="_show_in_kits" style="">
	Show In Kits:
	<input type="hidden" name="_show_in_kits" value="no" />
	<input type="checkbox" name="_show_in_kits" id="_show_in_kits" value="yes" ${selected_kits}/>
	
	</label>`);
	// add optgroup at category filter at configurator post type
	$('.configurator-category-class option')
	.each(function() {
		
	   var optionText = $(this).html();
	  
		
	   //if there are 3 spaces (&nbsp;), then it is our product. 
	   //otherwise replace option tag with optgroup
	   var m = optionText.match(/(&nbsp;){3}(.*)/g);
	   if (!m)
		   $(this).replaceWith('<optgroup label="'+optionText+'"></optgroup>');
  
	});
	$(document).on('change','#_show_in_kits', function(){
		$('.prefix_kit_tab').toggleClass('show_kits_section');
	});

});

jQuery(document).ready(function ($) {
	"use strict";

	var generat_filter = 1;

	$('#configurator_id').select2();
	$('#configurator_component').select2();
	$('#config_filters').select2();
	 $('.configurator-category-class .level-2').prev('.level-1').prop('disabled',true);
	$( "#mytabs" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
	$( "#mytabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );

	$("#post").validate({
	errorElement: 'span',
	});

	 
	//initialize filer option select2 
	function post_tag_select2(selectElementObj) {

		selectElementObj.select2({
			placeholder: 'Search filter',
			tags: true,
			multiple: true,
			minimumInputLength: 1,
			//minimumResultsForSearch: 10,
			tokenSeparators: [','],
			createTag: function (params) {
				if (params.term.trim() === '') {
					return null;
				}
				return {
					id: params.term.toLowerCase() + "(New)", //How to get id here???
					text: params.term.toLowerCase(),
					newTag: true // add additional parameters
				}
			},

		});

		selectElementObj.on('select2:select', function (e) {

			if (e.params.data.newTag) {
				$.ajax({
					//delay: 250,
					url: config_admin_ajax_req.ajaxurl,
					dataType: 'json',
					async: true,
					type: "POST",
					// data: function(params) {
					//     //this.data('term', params.term);
					//     return {
					//         q: params.term,
					//         action:'config_filter_tags'
					//     };
					// },
					data: {
						tag: e.params.data.text.toLowerCase(),
						//post_id: $('#curr_post_id').val(),
						//configurator_id: $('#configurator_id option:selected').val(),
						//component:$('#configurator_component option:selected').val(),
						configurator_id: $('#parent option:selected').val(),
						component: $("input[name=tag_ID]").val(),

						action: 'config_filter_tags',
						selected_post_tags: selectElementObj.val(),
					},
					success: function (response) {
						let comp_tags = (response);
						let tags_html = '';
						if (comp_tags.component_tags) {
							$.each(comp_tags.component_tags, function (i, item) {
								let selected = '';
								if (item.selected == true) {
									selected = 'selected';
									tags_html += '<option value="' + item.id + '" ' + selected + '>' + item.text + '</option>';
								}
								

							});
							//$(".post_tags").html('');
							selectElementObj.html(tags_html);
						}
					},
				})
			}
		});
	}

	async function get_components() {
		return new Promise((resolve, reject) => {
			var data = {
				'action': 'get_admin_components',
				'configurator_id': $('#configurator_id').val(),
				'current_post_id': $('#curr_post_id').val(),
				'source_selected_id': $("#configurator_component").val()
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



	async function get_post_components() {
		return new Promise((resolve, reject) => {
			var data = {
				'action': 'get_post_admin_components',
				'configurator_id': $('#configurator_id').val(),
				'current_post_id': $('#curr_post_id').val(),
				'source_selected_id': $("#configurator_component").val()
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


	async function component_tags() {
		return new Promise((resolve, reject) => {
			var data = {
				//'action': 'get_component_tags',
				'action': 'get_post_component_tags',
				//'configurator_id': $('#configurator_id').val(),
				'component': $("#configurator_component").val(),
				'child_component': $("#component_child option:selected").val(),
				'post_id': $('#curr_post_id').val()
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

	async function component_and_tags_with_relation() {
		return new Promise((resolve, reject) => {
			var data = {
				'action': 'get_component_and_tags_with_relation',
				'configurator_id': $('#configurator_id').val(),
				'component': $("#configurator_component").val(),
				'post_id': $('#curr_post_id').val()
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


	function get_source_comp_and_filter() {
		let cntt = $('.relation-block').length;
		let div_c = 0;
		if (cntt > 0) {
			div_c = cntt;
		} else {
			div_c = 0;
		}

		let source_comp_clone = '';
		let select_source_counter = $('.source_selected_tags').length;

		source_comp_clone += '<div class="custom-relation-head-s clearfix source-compo-div-d relation-container-block-' + div_c + '">';
		source_comp_clone += '<p class="config_filter_field_s">';
		source_comp_clone += '<label>Component:</label>';
		source_comp_clone += '<select style="width: 90%;"  class="source_selected_component" name="" disabled></select>';
		source_comp_clone += '</p>';
		source_comp_clone += '<p class="config_filter_field_select_s">';
		source_comp_clone += '<label>Filters:</label>';
		source_comp_clone += '<select style="width: 100%;" multiple="multiple" class="source_selected_tags" name="source_selected_tags[' + select_source_counter + '][]"></select>';
		source_comp_clone += '</p>';

		if (select_source_counter < 1) {
			source_comp_clone += '<a class="create-relation-block add-filter-dest-s" href="javascript:void(0)" style="background:green;"><span>+</span></a>';
		}
		else {
			source_comp_clone += '<a class="add-filter-dest-s remove-container" data-id="' + div_c + '" href="javascript:void(0)" style="background:red;"><span>-</span></a>';
		}
		source_comp_clone += '</div>';

		source_comp_clone += '<div class="clearfix relation-block relation-container-' + div_c + '"></div>';

		$('.filter-dest-relation-s').append(source_comp_clone);


	}


	function component_tag_html(count, add_new = '') {
		let filter_html = '';
		filter_html += '<div style="clear:both; height:5px;"></div>';
		filter_html += '<p class="config_filter_field_s filter-container-block-' + count + '">';
		filter_html += '<label for="config_filter_heading">Title:</label>';
		filter_html += '<input type="text" class="post_title" name="post_title_tags[' + count + '][title][]" value="" />';
		filter_html += '</p>';

		filter_html += '<p class="config_filter_field_select_s filter-container-block-' + count + '">';
		filter_html += '<label>Filters:</label>';
		filter_html += '<select multiple="multiple" class="post_tags post_tags_custom" name="post_title_tags[' + count + '][tags][]">';
		filter_html += '<option value=""></option>';
		filter_html += '</select>';
		// add checkbox for hide and show filters admin side
		filter_html += '<input type="hidden" name="post_title_tags[' + count + '][filter_show]" value="0">';
		filter_html += '<label><b>Show at Front Side</b> <input type="checkbox" name="post_title_tags[' + count + '][filter_show]" value="1" class="show_checked_tags"></label>';
		filter_html += '</p>';
		// add remove button with all except first one
		if (count > 0) {
			filter_html += '<a class="add-filter-dest-s remove-filter_container" data-id="' + count + '" href="javascript:void(0)" style="background:red;"><span>-</span></a>';
		}

		if (add_new) {
			filter_html += '<p class="add-new-filter add-new-filter-s"><a href="javascript:void(0)"><span>+</span></a></p>';
		}

		filter_html += '<div style="clear:both; height:5px;"></div>';
		$(".select-container").append(filter_html);
		if ($(".component-tags-block").length > 0) {
			$(".component-tags-block td").append(filter_html);
		}
	}


	function component_relation_tag_html(count, add_new = '') {
		let filter_dest_html = '';
		let select_source_counter = $.trim($('.source_selected_tags').length - 1);
		let component_relation_tags_count = $('.component_relation_tags').length;
		let component_filter_count = $('.add-filter-dest').length;

		filter_dest_html += '<div style="clear:both; height:5px;"></div>';
			filter_dest_html += '<p class="config_filter_field_s filter-sub-container-block-' + count + ' filter-parent-container-block-' + component_relation_tags_count + '" >';
		filter_dest_html += '<label>Components:</label>';
		filter_dest_html += '<select style="width:90%;" class="component_relation_filter" name="post_relation[' + select_source_counter + '][' + count + '][component][]" id="' + component_relation_tags_count + '">';
		//filter_dest_html += $(".component_relation_filter").html();
		filter_dest_html += '</select>';
		filter_dest_html += '</p>';
		filter_dest_html += '<p class="config_filter_field_select_s filter-sub-container-block-' + count + ' filter-parent-container-block-' + component_relation_tags_count + '">';
		filter_dest_html += '<label>Filters:</label>';
		//filter_dest_html += '<select style="width: 100%;" multiple="multiple" class="component_relation_tags" name="post_relation['+select_source_counter+']['+count+'][tag][]" id="post-relation-'+count+'">';

		filter_dest_html += '<select style="width: 100%;" multiple="multiple" class="component_relation_tags" name="post_relation[' + select_source_counter + '][' + count + '][tag][]" id="post-relation-' + component_relation_tags_count + '">';

		filter_dest_html += '</select>';
		filter_dest_html += '</p>';

		// check every new button parent counter
		if(component_filter_count - select_source_counter > 0) {
			 filter_dest_html += '<a class="add-filter-dest-s remove-sub-container" data-id="' + count + '" parent-data-id="' + component_relation_tags_count + '" href="javascript:void(0)" style="background:red;"><span>-</span></a>';
		}

		let cnt = $('.relation-block').length - 1;
		if (add_new) {
			let rel_cont = $(".relation-container-" + add_new + " .component_relation_tags").length;
			if (rel_cont < 1) {
				filter_dest_html += '<a class="add-filter-dest add-filter-dest-s" href="javascript:void(0)" data-comp="' + rel_cont - 1 + '" ><span>+</span></a>';
			}
			$(".relation-container-" + add_new).append(filter_dest_html);
		} else {
			console.log(cnt);
			console.log($(".relation-container-" + cnt + " .component_relation_tags").length, "b");
			if ($(".relation-container-" + cnt + " .component_relation_tags").length < 1) {

				filter_dest_html += '<a class="add-filter-dest add-filter-dest-s" href="javascript:void(0)" data-comp="' + cnt + '" ><span>+</span></a>';
			}
			$(".relation-container-" + cnt).append(filter_dest_html);
		}
		//$(".relation-container-"+cnt).append(filter_dest_html);
		//$(".filter-dest-relation:last").append(filter_dest_html);
	}


	//onload: call the above function 
	$(".post_tags").each(function () {
		post_tag_select2($(this));
	});
	// if sub child exits
	setTimeout(function() {
		if($('#component_child').children('option').length != 0) {
		   $('#component_child').trigger('change');
		} 

	},2000);

	let current_post_tags_flag = 0;


	$('body').on('change', '#configurator_id', async function () {
		//let comp_resp = await get_components();

		$("#configurator_component").html('').select2();
		$(".post_tags").html('').select2();

		let comp_resp = await get_post_components();

		comp_resp = JSON.parse(comp_resp);
		if (comp_resp.source_components.length > 0) {

			$("#configurator_component").html(comp_resp.source_components).select2();
			$("#configurator_component").val($("#configurator_component option:selected").val()).change();

		} else {

			$("#configurator_component").html('').select2();
		}
	});


	$('body').on('change', '#configurator_component', async function () {
		$('#component_child').html('');
		$('.filter-relation-block-d').hide();
		$('.show-relation-block').show();

		//$('.select-container').html('');
		$('#component_id').val($(this).val());

		let comp_tags = await component_tags();
		comp_tags = JSON.parse(comp_tags);

		let count = parseInt($('#current_post_filter_tags').val())
		let tags_html;

		tags_html = '';
		let selected_ = '';
console.log(comp_tags);
		if (comp_tags.component_tags) {
			if (!comp_tags.component_tags[0].child_comp) {
				$.each(comp_tags.component_tags, function (i, item) {
					console.log(item);
					if (item.selected == true) {
						selected_ = 'selected';
					} else {
						selected_ = '';
					}
					if(item.title != null) {
						tags_html += '<option value="' + item.id + '" ' + selected_ + ' >' + item.text + ' '+ '('+ item.title +')'  + '</option>';
					}
				});
				$('.post_tags').html(tags_html);
				initializeSelect2WithoutTag($('.post_tags'));
			}
		}

		if (comp_tags.component_tags) {
			if (comp_tags.component_tags[0].child_comp) {
				let child_comp_html = '';
				let child_select = '';
				$.each(comp_tags.component_tags[0].child_comp, function (i, itm) {
					if (itm.selected == true) {

						child_select = 'selected';
					} else {
						child_select = '';
					}
					child_comp_html += '<option value="' + itm.id + '" ' + child_select + ' >' + itm.text + '</option>';
				});
				$('#component_child').html(child_comp_html);
				$('#component_child').select2();
				$('.component-child-d').show();
			}
			else {
				$('#component_child').html('');
				$('.component-child-d').hide();
			}
		}
	});


	$('body').on('change', '#component_child', async function () {


		let comp_tags = await component_tags();
		comp_tags = JSON.parse(comp_tags);

		let tags_html;

		tags_html = '';
		let selected_ = '';

		if (comp_tags.component_tags) {
			$.each(comp_tags.component_tags, function (i, item) {
				if (item.selected == true) {
					selected_ = 'selected';
				} else {
					selected_ = '';
				}
				if(item.title != null) {
					tags_html += '<option value="' + item.id + '" ' + selected_ + ' >' + item.text + ' '+ '('+ item.title +')'  + '</option>';
				}
			});
			$('.post_tags').html(tags_html);
			initializeSelect2WithoutTag($('.post_tags'));
		}
	});

	//Select value without triggering change event
	$('#configurator_id').val($('#configurator_id').val());
	//Select value and trigger change event
	$('#configurator_id').val($('#configurator_id').val()).change();

	function initializeSelect2WithoutTag(element) {

		element.select2({
			multiple: true,
			//minimumInputLength: 1,
			// allowClear: true,
		});
	}


	$('body').on('click', '.hide-relation-block', function () {

		$('.filter-relation-block-d').hide();
		$('.hide-relation-block').hide();
		$('.show-relation-block').show();
	});


	$('body').on('change', '.component_relation_filter', function () {
		if ($(this).val() != '') {
			let comp_counter_id = $(this).attr("id");
			$('#post-relation-' + comp_counter_id).html('');
			var data = {
				'action': 'get_tags_by_components',
				'component': $(this).val(),
				'post_id': $('#curr_post_id').val()
			};
			jQuery.post(config_admin_ajax_req.ajaxurl, data, function (response) {
				let component_and_tags = JSON.parse(response);
				if (component_and_tags.relation_component_tags) {
					let tags_html = '';
					$.each(component_and_tags.relation_component_tags, function (i, item) {
						tags_html += '<option value="' + item.id + '" >' + item.text + '</option>';
					});
					$('#post-relation-' + comp_counter_id).html(tags_html);
					//$('.component_relation_filter:last').html(tags_html);
					initializeSelect2WithoutTag($('#post-relation-' + comp_counter_id));
				}
			});
		}
	});

	/*******************************************************************************/
	async function component_tags_new() {
		return new Promise((resolve, reject) => {
			var data = {
				'action': 'get_component_tags',
				'component': $("input[name=tag_ID]").val(),
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

	async function get_components_new() {
		return new Promise((resolve, reject) => {
			var data = {
				'action': 'get_admin_components',
				'configurator_id': $('#parent option:selected').val(),
				'component': $("input[name=tag_ID]").val(),
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

	async function component_and_tags_with_relation_new() {
		return new Promise((resolve, reject) => {
			var data = {
				'action': 'get_component_and_tags_with_relation',
				'configurator_id': $('#parent option:selected').val(),
				'component': $("input[name=tag_ID]").val(),
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


	if ($('.component-tags-block').length > 0) {
		async function component_and_filters() {
			//component_tag_html(0)
			//$('.select-container').html('');
			//$('#component_id').val($(this).val());
			let comp_tags = await component_tags_new();
			comp_tags = JSON.parse(comp_tags);

			let count = parseInt($('#current_post_filter_tags').val())
			let tags_html;
			if (comp_tags.term_component_tags) {
				$.each(comp_tags.term_component_tags, function (t, tag) {
					if (t < 1) {
						component_tag_html(t, true);
					} else {
						component_tag_html(t);
					}
					tags_html = '';
					if (comp_tags.component_tags) {
						$.each(comp_tags.component_tags, function (i, item) {
							// check if comp_tags id empty return false
						  if (!comp_tags.term_component_tags[t].tags_id) {
								return;
							}
							let selected_ = '';
							if (comp_tags.term_component_tags[t].tags_id.indexOf(item.id) !== -1) {
								selected_ = 'selected';
								tags_html += '<option value="' + item.id + '" ' + selected_ + '>' + item.text + '</option>';
							}
							
						});
						$('.post_tags:last').html(tags_html);
					}
					post_tag_select2($('.post_tags:last'));
					$('.post_title:last').val(tag.title);
					$(".show_checked_tags:last").prop("checked", false);
					// if filter show 1 then attr checked true
					if(tag.filter_show == 1) {
						$(".show_checked_tags:last").prop("checked", true);
					}
				});
			}
			else {
				// component_tag_html(count - 1,true);
				component_tag_html(0, true);
				tags_html = '';
				$.each(comp_tags.component_tags, function (i, item) {
					tags_html += '<option value="' + item.id + '" >' + item.text + '</option>';
				});
				$('.post_tags').html(tags_html);
				 post_tag_select2($('.post_tags:last'));
			}
		}
		component_and_filters();

		let current_post_tags_flag = 0;
		$("body").on("click", ".add-new-filter", async function () {
			let filter_html = '';
			current_post_tags_flag++;
			let count = parseInt($('#current_term_filter_tags').val()) + (current_post_tags_flag - 1);

			component_tag_html(count);
			$('#current_term_filter_tags').val(count);

			let comp_tags = await component_tags_new();
			comp_tags = JSON.parse(comp_tags);
			let tags_html = '';
			if (comp_tags.component_tags) {
				$.each(comp_tags.component_tags, function (i, item) {
					tags_html += '<option value="' + item.id + '">' + item.text + '</option>';
				});
				// $('.post_tags:last').html(tags_html);
			}
			post_tag_select2($('.post_tags:last'));
		});

		$('body').on('click', '.show-relation-block', async function () {

			$('.filter-dest-relation').html('');
			//get_source_comp_and_filter();
			taxonomy_component_filter_relation();
		});

		$('body').on('click', '.create-relation-block', async function () {

			get_source_comp_and_filter();
			taxonomy_component_filter_relation(1);
		});

		$('body').on('click', '.remove-container', function () {

			let block_id = $(this).attr("data-id");
			$('.relation-container-block-' + block_id).remove();
			$('.relation-container-' + block_id).remove();

		});
		// remove filter and title
		$('body').on('click', '.remove-filter_container', function () {

			let block_id = $(this).attr("data-id");
			$('.filter-container-block-' + block_id).remove();
			$(this).remove();

		});
		$('body').on('click', '.remove-sub-container', function () {

			let block_id = $(this).attr("data-id");
			let parent_data_id = $(this).attr("parent-data-id");
			console.log('.filter-sub-container-block-' + block_id+'.filter-parent-container-block-' + parent_data_id);
			$('.filter-sub-container-block-' + block_id+'.filter-parent-container-block-' + parent_data_id).remove();
			$(this).remove();

		});
		



		function destination_component(comp_resp, obj_id = '', t = '') {
			if (comp_resp.dest_components) {
				let filter_html = '';
				if (obj_id == '') {
					console.log("WRONG");
					filter_html += '<option value="">Select Option</option>';
				}
				$.each(comp_resp.dest_components, function (i, item) {
					let selected_ = '';
					if ((obj_id || obj_id == '0') && comp_resp.relation_component_tags[obj_id][t].component == item.id) {
						selected_ = 'selected';
					}
					filter_html += '<option value="' + item.id + '" ' + selected_ + ' >' + item.text + '</option>';
				});
				$('.component_relation_filter:last').html(filter_html);
			}
		}
		async function taxonomy_component_filter_relation(new_block = '') {
			let comp_resp = await get_components_new();
			comp_resp = JSON.parse(comp_resp);
			if (new_block == '') {
				console.log(comp_resp, "RELATION RESPONSE");
			   
				if (comp_resp.source_selected_tags !== null ) {
					$.each(comp_resp.source_selected_tags, function (obj_id, item) {

						get_source_comp_and_filter();
						let source_tags_html = '';
						$.each(comp_resp.source_selected_tags[obj_id], function (s_opt, s_tag) {
							let select_comp_tag = '';
							if (s_tag.selected == true && new_block == '') {
								select_comp_tag = 'selected';
							}
							source_tags_html += '<option value="' + s_tag.id + '"  ' + select_comp_tag + ' >' + s_tag.text + '</option>';
						});
						$('.source_selected_tags:last').html(source_tags_html);
						initializeSelect2WithoutTag($('.source_selected_tags:last'));



						if (comp_resp.relation_component_tags[obj_id] !== null && comp_resp.relation_component_tags[obj_id] !== undefined && Object.keys(comp_resp.relation_component_tags[obj_id]).length > 0) {
							console.log("IFFFFFFFFFFFFFFFF", "IFFFFFFFFFF");
							$.each(comp_resp.relation_component_tags[obj_id], function (t, item) {

								component_relation_tag_html(t);
								destination_component(comp_resp, obj_id, t);
								if (Object.keys(comp_resp.relation_component_tags[obj_id][t].tags).length > 0) {
									let tags_html = '';
									$.each(comp_resp.relation_component_tags[obj_id][t].tags, function (tt, tag) {
										let selected_tag = '';
										if (Object.keys(comp_resp.relation_component_tags[obj_id][t].selected_tags).length && comp_resp.relation_component_tags[obj_id][t].selected_tags.indexOf(tag.id.toString()) !== -1) {
											selected_tag = 'selected';
										}
										tags_html += '<option value="' + tag.id + '" ' + selected_tag + ' >' + tag.text + '</option>';
									});

									$('.component_relation_tags:last').html(tags_html);
									//initializeSelect2WithoutTag($('.component_relation_tags:last'));
								}
							});
						}
						else {
							console.log("ELSESSSSSSSSSSSSSS", "ELSEEEEEEEEEE");
							if ($('.relation-block').length > 0) {
								component_relation_tag_html($('.relation-block').length);
							} else {
								component_relation_tag_html(0);
							}
							destination_component(comp_resp)

						}
					});
				}
				else {
					get_source_comp_and_filter();
					if ($('.relation-block').length > 0) {
						component_relation_tag_html($('.relation-block').length);
					} else {
						component_relation_tag_html(0);
					}

					if (comp_resp.dest_components) {
						let tags_html = '';
						$.each(comp_resp.dest_components, function (i, item) {
							if (i < 1) {
								tags_html += '<option value="" >Select Option</option>';
							}
							tags_html += '<option value="' + item.id + '" >' + item.text + '</option>';
						});
						$('.component_relation_filter:last').html(tags_html);
					}


					let tags_html = '';
					if (comp_resp.source_selected_tags) {
						console.log(comp_resp.source_selected_tags[0]);
						$.each(comp_resp.source_selected_tags[0], function (i, item) {

							tags_html += '<option value="' + item.id + '" >' + item.text + '</option>';
						});
						$('.source_selected_tags:last').html(tags_html);
					}
					initializeSelect2WithoutTag($('.source_selected_tags:last'));
					console.log("HERRRRRRRRRRRRRRRRRRRRRR");
				}
			}
			else {
				if ($('.relation-block').length > 0) {
					component_relation_tag_html($('.relation-block').length);
				} else {
					component_relation_tag_html(0);
				}

				if (comp_resp.dest_components) {
					let tags_html = '';
					$.each(comp_resp.dest_components, function (i, item) {
						if (i < 1) {
							tags_html += '<option value="" >Select Option</option>';
						}
						tags_html += '<option value="' + item.id + '" >' + item.text + '</option>';
					});
					$('.component_relation_filter:last').html(tags_html);
				}
				// initializeSelect2WithoutTag($('.component_relation_tags:last'));

				let tags_html = '';
				if (comp_resp.source_selected_tags) {
					console.log(comp_resp.source_selected_tags[0]);
					$.each(comp_resp.source_selected_tags[0], function (i, item) {

						tags_html += '<option value="' + item.id + '" >' + item.text + '</option>';
					});
					$('.source_selected_tags:last').html(tags_html);
				}
				initializeSelect2WithoutTag($('.source_selected_tags:last'));
			}

			// $(".source_selected_component:last").html(comp_resp.source_selected_option);
			$(".source_selected_component").html(comp_resp.source_selected_option);

			$('.show-relation-block').hide();
			$('.hide-relation-block').show();

			$(".component_relation_tags").each(function () {
				initializeSelect2WithoutTag($(this));
			});


			$('.filter-relation-block-d').show();
		}

		$('body').on('click', '.add-filter-dest', async function () {
			let component_and_tags = await component_and_tags_with_relation_new();

			// component_relation_tag_html($('.component_relation_filter').length);
			let comp_container = $(this).attr('data-comp');

			component_relation_tag_html($('.relation-container-' + comp_container + ' ' + '.component_relation_filter').length, comp_container);

			component_and_tags = JSON.parse(component_and_tags);
			if (component_and_tags.relation_component) {
				let tags_html = '';
				$.each(component_and_tags.relation_component, function (i, item) {
					if (i < 1) {
						tags_html += '<option value="" >Select Option</option>';
					}
					tags_html += '<option value="' + item.id + '" >' + item.text + '</option>';
				});
				let comp_container = $(this).attr('data-comp');
				$('.relation-container-' + comp_container + ' .component_relation_filter:last').html(tags_html);
				//$('.component_relation_filter:last').html(tags_html);
			}
			//initializeSelect2WithoutTag($('.component_relation_tags:last'));
			initializeSelect2WithoutTag($('.relation-container-' + comp_container + ' ' + '.component_relation_tags:last'));
		});

	}
	$(document).on('click', '#update-from-provider', function() {

		var form_data = new FormData();
		var files = $('#config_csv_file')[0].files
		form_data.append("action", 'upload_configurator_csv');
		form_data.append("file", files[0]);
		$.ajax({
			url: config_admin_ajax_req.ajaxurl,
			type: 'post',
			data: form_data,
			contentType: false,
			processData: false,
			success:function(response) {
				alert('File Imported successfully!');
				location.reload();
				
			},
			error: function(response){
		
			}
		});
		
	});

	$(document).on('click', '#export-provider', function() {
		$.ajax({
			url: config_admin_ajax_req.ajaxurl,
			type: 'post',
			data: {
				action: 'export_csv_config'
			},
			success:function(response) {
				console.log(response);
				var downloadLink = document.createElement("a");
				var fileData = [response];

				var blobObject = new Blob(fileData,{
					type: "text/csv;charset=utf-8;"
				});

				var url = URL.createObjectURL(blobObject);
				downloadLink.href = url;
				downloadLink.download = "configurator-file.csv";
               document.body.appendChild(downloadLink);
               downloadLink.click();
               document.body.removeChild(downloadLink);
				
			},
			error: function(response){
		
			}
		});
		
	});

});