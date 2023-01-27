jQuery(document).ready(function ($) {

	let current_url = $(location).attr('href');

	let disabled_ai_messages = localStorage.getItem("disabled_ai_messages");

	if(disabled_ai_messages == null) {

		$.post(ajax_object.ajax_url, {action: 'config_ai_messages_show',  current_page: current_url.replace(/\/$/, "")}).done(response => {

			if ($.trim(response)){ 

				$('.dynamic_content').html(response);

				$('.popup-wrapper').show();

			}

		});
	}
	$(document).on('click','.add-to-cart-btn', function() {

		let popup_msg = $(this).attr("popup-attribute");

		

		let disabled_ai_messages = localStorage.getItem("disabled_ai_messages");

		if(disabled_ai_messages == null) {

			$.post(ajax_object.ajax_url, {action: 'config_ai_messages_show',  current_page: '', popup_msg, popup_msg}).done(response => {

				if ($.trim(response)){ 

					

					$('.dynamic_content').html(response);
c
					$('.popup-wrapper').show();

				}

			});
		}

	});

	$(document).on('click','.popup-close', function() {

		$('.popup-wrapper').hide();

	});

	$(document).on('click','.pop-check', function() {

		localStorage.setItem("disabled_ai_messages", 'yes');

	})

	$(document).on('click','.popup-closebtn', function() {

		$('.alert-box-bg').hide();

	});

	let min_custom_price = Number($('#min_custom_price').val()) ? Number($('#min_custom_price').val()) : 0;

	let max_custom_price = Number($('#max_custom_price').val()) ? Number($('#max_custom_price').val()) : 5000;

	$( "#slider-range" ).slider({

		range: true,

		min: 0,

		max: 5000,

		values: [ min_custom_price, max_custom_price ],

		slide: function( event, ui ) {

			$( "#amount" ).val( "$" + ui.values[ 0 ] + " - $" + ui.values[ 1 ] );

		}

	});

	$( "#amount" ).val("$" + $( "#slider-range" ).slider( "values", 0 ) +

		" - $" + $( "#slider-range" ).slider( "values", 1 ) );



	$(document).on('click','#filter_product', function() {

		max_min_val();

		$('.custom-filter-ordering').submit();

		

	});

	$(document).on('click','.config-input-checkbox', function() {

		max_min_val();

		$('.custom-filter-ordering').submit();

		

	});

	function max_min_val() {

		$('#min_custom_price').val($("#slider-range").slider("option", "values")[0]);

		$('#max_custom_price').val($("#slider-range").slider("option", "values")[1]);

	}



	$('.cart-discount.coupon-uniquecode th').text('Discount');



	$(document).on('click','#place_order', function() {



		$.post(ajax_object.ajax_url, {action: 'config_coupon_counter'}).done(response => {

		});

	});



	$(document).on('click','.edit-page', function() {

	    let key = $(this).data('key');

		localStorage.setItem("selected_configurator_edit",$(this).data('value'));

		window.location.href = ajax_object.site_url+'/custom-configurator?edit-product='+key;

	});

	$(document).on('click','.edit-page-kits', function() {

	    let key = $(this).data('key');

	    let value = $(this).data('value');

		window.location.href = ajax_object.site_url+'/custom-configurator?config_link_id='+value+'&edit-product='+key;

	});

    var lhref = location.href;

    var last_segment = lhref.match(/([^\/]*)\/*$/)[1];
    if(last_segment == 'surveillance') {
        last_segment = 'video-surveillance';
    }
    if(last_segment == 'alarm') {
        last_segment = 'burglar-alarm';
    }


   

    $('.parent-menu-config > a > span').each(function(i, v){
        if(last_segment == $(v).text().toLowerCase().replace(' ','-')) {

          

            $(document.activeElement ).find('.parent-menu-config').parents('.menuo-sub-active').not('.woocommerce-shop').addClass('remove-filters');

            $('.remove-filters').find('.sections_group > .section').css({"width":"50%","margin":"0 auto"});

        }
		if(last_segment == 'kits') {
			$(document.activeElement ).find('.parent-menu-config').parents('.menuo-sub-active').not('.woocommerce-shop').addClass('remove-filters');

            $('.remove-filters').find('.sections_group > .section').css({"width":"50%","margin":"0 auto"});
		}

       

    });
     $(document).on('click','.send_email_pre_order', async function () {

        let data = {};

        data = {

            'action': 'share_link_admin_data',
            'customer_email' : $('#customer_email').val(),

        };

        let data_link_response = await prepare_ajax_request(data);
        let data_parse = JSON.parse(data_link_response);
        $('.send_email_pre_order').attr("share-id", data_parse.share_link_id);
       alert(data_parse.msg)
       setTimeout(function(){ 
       		location.reload();

        }, 3000);

        
    });

});


function enableBtn(){
	document.getElementById("send_email_pre_order").disabled = false;
} 

function disabledBtn(){
	document.getElementById("send_email_pre_order").disabled = true;
} 

 async function prepare_ajax_request(data) {

        return new Promise((resolve, reject) => {

            jQuery.post(ajax_object.ajax_url, data)

                .then(function (res) {

                    resolve(res);

                })

                .catch(function (xhr, status, error) {

                    reject(true);

                });

        });

    }