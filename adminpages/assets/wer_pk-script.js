const {
  host, hostname, href, origin, pathname, port, protocol, search
} = window.location

let siteURL = origin;
let j = 0;
if(siteURL == "http://localhost"){
	siteURL = siteURL + "/wordpress652"
	console.log("ORIGIN::", siteURL);
}

// Count the number of options in the select element
var numberOfOptions = jQuery('#selectProductVariants option').length;

// Log the count
console.log('Number of options: ' + numberOfOptions);
jQuery("#mainSpinner").hide();
jQuery("#spinnerContent").hide();
jQuery('.selectedItem').hide();
jQuery('#notice').hide();

//jQuery('select[name=materialsName]').hide();
jQuery('select[name=materialsName]').attr('disabled', 'disabled');
jQuery('input[name=discount]').attr('disabled', 'disabled');


if (typeof ajax_object !== 'undefined' && ajax_object.nonce) {
	jQuery.ajaxPrefilter(function(options) {
		if (options.data instanceof FormData) {
			if (!options.data.has('nonce')) {
				options.data.append('nonce', ajax_object.nonce);
			}
		} else if (typeof options.data === 'string') {
			if (options.data.indexOf('nonce=') === -1) {
				options.data += (options.data ? '&' : '') + 'nonce=' + encodeURIComponent(ajax_object.nonce);
			}
		} else {
			options.data = options.data || {};
			if (typeof options.data.nonce === 'undefined') {
				options.data.nonce = ajax_object.nonce;
			}
		}
	});
}


if( jQuery('input[name=start_date]').val() != undefined ){

	var date = new Date();

	console.log("date::", date.toJSON().slice(0,10).split('-').reverse().join('/') );
	jQuery('input[name=start_date]').val(date.toJSON().slice(0,10));

} else if( jQuery('input[name=billdate]').val() != undefined ){

	var date = new Date();

	console.log("date::", date.toJSON().slice(0,10).split('-').reverse().join('/') );
	jQuery('input[name=billdate]').val(date.toJSON().slice(0,10));

}

if( jQuery('input[name=billNo]').val() != undefined && jQuery('input[name=billNo]').val() === "" ){
	jQuery('input[name=billNo]').attr('disabled', 'disabled');
	jQuery('input[name=billNo]').val( generateUUID() );

}

function generateUUID() {
	
	var date = new Date();

	console.log("date111111::", date.toJSON().slice(0,10).split('-').join('') );

	let addDate = date.toJSON().slice(0,10).split('-').join('');
    // Generate random values
    const randomValues = new Uint8Array(16);
    crypto.getRandomValues(randomValues);

    // Set the version to 4 and the variant to 2
    randomValues[6] = (randomValues[6] & 0x0f) | 0x40; // Version 4
    randomValues[8] = (randomValues[8] & 0x3f) | 0x80; // Variant 2

    // Convert bytes to hex and format into UUID string
    const hexArray = Array.from(randomValues).map(byte => byte.toString(16).padStart(2, '0'));
    return `INV-${addDate}-${hexArray[0]}${hexArray[1]}${hexArray[2]}${hexArray[3]}${hexArray[4]}${hexArray[5]}`;

}

// Example usage
console.log(generateUUID());


jQuery('input[name=start_date]').on("change", function () {
	console.log(jQuery('input[name=start_date]').val());
});

if(window.pagenow === "admin_page_wp_wer_pk_order_detail"){
	window.addEventListener( 'load', changeSupplier(true), true );
}

const currentQuantity = parseInt( jQuery('input[name=quantity]').val() );

console.log(window.pagenow);

function wer_pkDeleteFromTable(i, o, m){

		console.log(window.pagenow);
		console.log(i);
		console.log(o);
		
		if(window.pagenow === "admin_page_wp_wer_pk_order_detail"){
			var c = confirm(m);
			if (c) {
				window.location.href = `?page=wp_wer_pk_order_detail&order=${i}&orderDelete=${o}&action=trash`;
			}
		} else if(window.pagenow === "admin_page_wp_wer_pk_project_detail"){
			var c = confirm(m);
			if (c) {
				window.location.href = `?page=wp_wer_pk_project_detail&project=${i}&orderDelete=${o}&action=trash`;
			}
		} else if(window.pagenow === "manager-dashboard_page_wp_wer_pk_projects"){
			var c = confirm(m);
			if (c) {
				window.location.href = `?page=wp_wer_pk_projects&project=${i}&action=trash`;
			}
		}

}

function wer_pkEditFromTable(i, o){

	console.log(window.pagenow);
		console.log(i);
		console.log(o);
	
	if(window.pagenow === "admin_page_wp_wer_pk_order_detail"){
		
		window.location.href = `?page=wp_wer_pk_order_detail&order=${i}&orderItem=${o}&action=edit`;

	} else if(window.pagenow === "admin_page_wp_wer_pk_project_detail"){
	
		window.location.href = `?page=wp_wer_pk_project_detail&project=${o}&projectOrder=${i}&action=edit`;

	} else if(window.pagenow === "manager-dashboard_page_wp_wer_pk_projects"){
		
		window.location.href = `?page=wp_wer_pk_projects&project=${i}&action=edit`;

	}

}

jQuery( document ).on( 'heartbeat-tick', function ( event, data ) {
	console.log(data);
	// Check for our data, and use it.
	if ( ! data.myplugin_customfield_hashed ) {
		return;
	}

	console.log( 'This is ' + data.myplugin_customfield_hashed );
	//WebSocket#terminate();
});

/****************************************

Main Projects function implementation.

**************************************/

/**
 * Resets the form data.  Causes all form elements to be reset to their original value.
 */
jQuery.fn.resetForm = function() {
    return this.each(function() {
        // guard against an input with the name of 'reset'
        // note that IE reports the reset function as an 'object'
        if (typeof this.reset == 'function' || (typeof this.reset == 'object' && !this.reset.nodeType)) {
            this.reset();
        }
    });
};


function wer_pkSaveForm(){

	if(parseInt( jQuery('input[name=size]').val()) > parseInt( jQuery('input[name=size]').attr('max') ) ){
		return;
	} else {

		var postData = {};

		if( jQuery('input[name=project_name]').val().length > 0 
		&& jQuery('input[name=size]').val().length > 0 
		&& jQuery('input[name=location]').val().length > 0 
		&& jQuery('input[name=start_date]').val().length > 0 ) {
		
			jQuery("#mainSpinner").show();

			postData = {
				action: 'saveProject',
				project_name: jQuery('input[name=project_name]').val(),
				size: jQuery('input[name=size]').val(), 
				location: jQuery('input[name=location]').val(),
				start_date: jQuery('input[name=start_date]').val(),
				status: jQuery('input[name=status]').prop('checked')
			}
		
		} else {
			return;
		}

		jQuery.ajax({
				type : 'post',
				dataType: 'json',
				url : decodeURI(siteURL)+'/wp-admin/admin-ajax.php',
				data : postData,
				success: function (response) {
						console.log(response);
						//jQuery("#project_Add_Edit").resetForm();
						jQuery("#mainSpinner").hide();

						/*
						jQuery( document ).on( 'heartbeat-send', function ( event, data ) {
							// Add additional data to Heartbeat data.
							data.myplugin_customfield = postData.project_name;
						});
						*/

						window.location.href = "admin.php?page=wp_wer_pk_projects";
				},
                error: function(xhr, status, error) {
					jQuery("#mainSpinner").hide();
                    console.error('AJAX Error: ' + status + ' - ' + error);
                }
		});

	}

}

function wer_pkUpdateForm(){

	
	if(parseInt( jQuery('input[name=size]').val()) > parseInt( jQuery('input[name=size]').attr('max') ) ){
		
		console.log(jQuery('input[name=size]').val()+">"+jQuery('input[name=size]').attr('max'));
		
		console.log(jQuery('input[name=size]').val() > jQuery('input[name=size]').attr('max'));
		
		return;

	} else {

		var postData = {};

		if( jQuery('input[name=project_name]').val().length > 0 
		&& jQuery('input[name=size]').val().length > 0 
		&& jQuery('input[name=location]').val().length > 0 
		&& jQuery('input[name=start_date]').val().length > 0 ) {
		
			jQuery("#mainSpinner").show();

			postData = {
				action: 'getProjectById',
				project: jQuery('input[name=project_id]').val(),
				project_name: jQuery('input[name=project_name]').val(),
				size: jQuery('input[name=size]').val(), 
				location: jQuery('input[name=location]').val(),
				start_date: jQuery('input[name=start_date]').val(),
				status: jQuery('input[name=status]').prop('checked')
			}
		
		} else {
			return;
		}

			jQuery.ajax({
				type : 'post',
				dataType: 'json',
				url : decodeURI(siteURL)+'/wp-admin/admin-ajax.php',
				data : postData,
				success: function (response) {
						//console.log(response);
						jQuery("#project_Add_Edit").resetForm();
						jQuery("#mainSpinner").hide();
						window.location.href = "admin.php?page=wp_wer_pk_projects"
				},
                error: function(xhr, status, error) {
					jQuery("#mainSpinner").hide();
                    console.error('AJAX Error: ' + status + ' - ' + error);
                }
			});

	}

}

/****************************************

Main Orders function implementation.

**************************************/



function wer_pkOrderSaveForm(){

		var postData = {};

		if( jQuery('input[name=billNo]').val().length > 0 
		&& jQuery('input[name=expenseType]').val().length > 0 
		&& jQuery('input[name=billdate]').val().length > 0 
		&& jQuery('textarea[name=description]').val().length > 0 ) {
		
			jQuery("#mainSpinner").show();

			postData = {
				action: 'saveProjectOrder',
				projectid: jQuery('input[name=projectid]').val(),
				billNo: jQuery('input[name=billNo]').val(),
				expenseType: jQuery('input[name=expenseType]').val(),
				billdate: jQuery('input[name=billdate]').val(),
				description: jQuery('textarea[name=description]').val()
			}

		
		} else {
			return;
		}

		jQuery.ajax({
				type : 'post',
				dataType: 'json',
				url : decodeURI(siteURL)+'/wp-admin/admin-ajax.php',
				data : postData,
				success: function (response) {
						console.log(response);
						jQuery("#order_Add_Edit").resetForm();
						jQuery("#mainSpinner").hide();
						window.location.href = "admin.php?page=wp_wer_pk_project_detail&project="+jQuery('input[name=projectid]').val();
				},
                error: function(xhr, status, error) {
					jQuery("#mainSpinner").hide();
                    console.error('AJAX Error: ' + status + ' - ' + error);
                }
		});
	

}

function wer_pkOrderUpdateForm(){

		var postData = {};

		if( jQuery('input[name=billNo]').val().length > 0 
		&& jQuery('input[name=expenseType]').val().length > 0 
		&& jQuery('input[name=billdate]').val().length > 0 
		&& jQuery('textarea[name=description]').val().length > 0 ) {
		
			jQuery("#mainSpinner").show();

			postData = {
				action: 'getProjectOrderById',
				orderid: jQuery('input[name=orderid]').val(),
				projectid: jQuery('input[name=projectid]').val(),
				billNo: jQuery('input[name=billNo]').val(),
				expenseType: jQuery('input[name=expenseType]').val(),
				billdate: jQuery('input[name=billdate]').val(),
				description: jQuery('textarea[name=description]').val()
			}

		
		} else {
			return;
		}

		jQuery.ajax({
			type : 'post',
			dataType: 'json',
			url : decodeURI(siteURL)+'/wp-admin/admin-ajax.php',
			data : postData,
			success: function (response) {
					console.log(response);
					jQuery("#order_Add_Edit").resetForm();
					jQuery("#mainSpinner").hide();
					window.location.href = "admin.php?page=wp_wer_pk_project_detail&project="+jQuery('input[name=projectid]').val();
			},
            error: function(xhr, status, error) {
				jQuery("#mainSpinner").hide();
                console.error('AJAX Error: ' + status + ' - ' + error);
            }
		});

}


/****************************************

Order Items function implementation.

**************************************/

function changeSupplier(editing=false){

	if( jQuery('select[name=supplierName]') != undefined ){
		
		let index = jQuery('select[name=supplierName]')[0].selectedIndex;

		console.log(jQuery('select[name=supplierName]')[0][index].innerText);

		console.log(jQuery('select[name=supplierName]').val());

	}
	
	
		var postData = {};

		postData = {
			action: 'getSuppliersItems',
			supplierName: jQuery('select[name=supplierName]').val(),
		}
		
		jQuery.ajax({
				type : 'get',
				dataType: 'json',
				url : decodeURI(siteURL)+'/wp-admin/admin-ajax.php',
				data : postData,
				global: true,
				processData: true,
				async: true,
				contentType: "application/json; charset=UTF-8",
				success: function (response) {

						let itemSelect = jQuery('select[name=materialsName] option:first').text();

						jQuery('.selectedItem').hide();

						let myOptions = `<option>${itemSelect}</option>`;

						for(var i = 0; i < response.length; i++ ) {
							
							let selectedEditItem = response[i]["variantSKU"] +" - "+ response[i]["materialsName"] +" "+ response[i]["attributes"]
							
							let selectedOption = selectedEditItem === jQuery('.selectedItem').html() ? "selected" : "";
							
							let selectedItem = selectedEditItem === jQuery('.selectedItem').html() ? response[i] : "";
							
							jQuery('.itemPrice').html(selectedItem["variantPrice"]);
							jQuery('.itemGST').html(selectedItem["variantGST"]);
							
							if(selectedItem != ""){

								let inhand = parseInt(selectedItem["variantStock"]) > 0 ? "Stock available: " + selectedItem["variantStock"] : "Please update stock.";
							
								console.log("Stock",inhand);
								parseInt(selectedItem["variantStock"]) > 0 ? jQuery('.itemsinHand').css("color", "green") : jQuery('.itemsinHand').css("color", "red")
								console.log("In hand:", inhand);
							
								jQuery('.itemsinHand').html(inhand);								
							}

							myOptions += `<option value=${response[i]["variant_id"]} ${selectedOption}>${response[i]["variantSKU"]} - ${response[i]["materialsName"]} ${response[i]["attributes"]}</option>`
						}

					
						jQuery('select[name=materialsName]').html(myOptions);

						console.log( "test::", jQuery('.selectedItem').html() );
						if(jQuery('.selectedItem').html() === ""){
							jQuery('.selectedItem').html() === "" ? jQuery('.itemsinHand').html("Please select an Item.") : "";
							jQuery('.selectedItem').html() === "" ? jQuery('.itemsinHand').css("color", "green") : jQuery('.itemsinHand').css("color", "red")
						}
						

						if(editing && jQuery('.selectedItem').html() != ""){
							jQuery('select[name=supplierName]').attr('disabled', 'disabled');
						} else {
							jQuery('select[name=materialsName]').removeAttr('disabled');
						}

						
				},
                error: function(xhr, status, error) {
					jQuery("#mainSpinner").hide();
                    console.error('AJAX Error: ' + status + ' - ' + error);
                }
		});
}

function changeItemPrice(){

		jQuery('input[name=quantity]').val(1);
	
		var postData = {};

		postData = {
			action: 'getSuppliersItemsPrice',
			materialsName: jQuery('select[name=materialsName]').val(),
		}
		
		jQuery.ajax({
				type : 'get',
				dataType: 'json',
				url : decodeURI(siteURL)+'/wp-admin/admin-ajax.php',
				data : postData,
				global: true,
				processData: true,
				async: true,
				contentType: "application/json; charset=UTF-8",
				success: function (response) {
						console.log(response);

						jQuery('.itemPrice').html(response.variantPrice);
						jQuery('.itemGST').html(response.variantGST);

						console.log(response.variantStock);
						let inhand = parseInt(response.variantStock) > 0 ? "Stock available: " + response.variantStock : "Please update stock.";
						jQuery('.itemsinHand').html(inhand);
						parseInt(response.variantStock) > 0 ? jQuery('.itemsinHand').css("color", "green") : jQuery('.itemsinHand').css("color", "red");

						let gstPrice = parseFloat(response.variantPrice) + parseFloat(response.variantGST);

						jQuery('input[name=totalPrice]').val(gstPrice);
						jQuery('input[name=GST]').val(response.variantGST);
						jQuery('input[name=discount]').val(response.variantDiscount);
						jQuery('input[name=productid]').val(response.variant_id);
						manageDiscount();

				},
                error: function(xhr, status, error) {
					jQuery("#mainSpinner").hide();
                    console.error('AJAX Error: ' + status + ' - ' + error);
                }
		});
	

}

jQuery('input[name=quantity]').on("change", function () {

	let gstPrice = parseFloat(jQuery(".itemPrice").html()) + parseFloat(jQuery(".itemGST").html());
	
	let total = gstPrice * jQuery(this).val();

	let gstTotal = parseFloat(jQuery(".itemGST").html()) * jQuery(this).val();

	jQuery('input[name=GST]').val(gstTotal);
	
	jQuery('input[name=totalPrice]').val(total);

	manageDiscount();
	console.log(jQuery(this).val());
	console.log(jQuery(".itemPrice").html());

})

jQuery('#notice').on("click", function () {

	console.log("test");
	jQuery('#notice').hide();

})

function manageDiscount(){

	let gstPrice = parseFloat(jQuery(".itemPrice").html()) + parseFloat(jQuery(".itemGST").html());
	
	let total = gstPrice * jQuery('input[name=quantity]').val();

	let discountPer = parseInt(jQuery('input[name=discount]').val()) / parseInt(100);

	let totalPrice = total * discountPer;

	let sellingPrice = total - totalPrice;

	console.log("Total without any discount %: ", sellingPrice);

	jQuery('input[name=totalPrice]').val(sellingPrice);

}

jQuery('input[name=discount]').on("change", function () {

	manageDiscount();

})

function wer_pkSaveBillItem(){

		
		let supIndex = jQuery('select[name=supplierName]')[0].selectedIndex;
		let matIndex = jQuery('select[name=materialsName]')[0].selectedIndex;

		console.log("Selected Store: " + jQuery('select[name=supplierName]').val());

		console.log(jQuery('select[name=supplierName]')[0][supIndex].innerText);
		console.log(jQuery('select[name=materialsName]')[0][matIndex].innerText);
		
	
		var postData = {};

		if( jQuery('select[name=supplierName]').val().length > 0 
		&& jQuery('select[name=materialsName]').val().length > 0 
		&& jQuery('input[name=quantity]').val().length > 0 
		&& jQuery('input[name=totalPrice]').val().length > 0 ) {
		
			jQuery("#mainSpinner").show();

			let discountPer = parseInt(jQuery('input[name=discount]').val()) / parseInt(100);

			let totalPrice = discountPer * parseInt(jQuery('input[name=totalPrice]').val());

			let sellingPrice = jQuery('input[name=totalPrice]').val() - totalPrice;

			console.log("discount %: ", discountPer);
			console.log("order Price: ", totalPrice);
			console.log("Selling Price: ", sellingPrice);

			postData = {
				action: 'saveorder',
				billid: jQuery('input[name=billid]').val(),
				supplierName: jQuery('select[name=supplierName]').val(), //jQuery('select[name=supplierName]')[0][supIndex].innerText,
				materialsName: jQuery('select[name=materialsName]')[0][matIndex].innerText, 
				quantity: jQuery('input[name=quantity]').val(),
				product: jQuery('select[name=materialsName]').val(),
				GST: jQuery('input[name=GST]').val(),
				discount: jQuery('input[name=discount]').val(),
				productid: jQuery('input[name=productid]').val(),
				totalPrice: jQuery('input[name=totalPrice]').val()
			}
		
		} else {
			return;
		}

		jQuery.ajax({
				type : 'post',
				dataType: 'json',
				url : decodeURI(siteURL)+'/wp-admin/admin-ajax.php',
				data : postData,
				success: function (response) {
						console.log("test", response);
						//jQuery("#project_Add_Edit").resetForm();
						jQuery("#mainSpinner").hide();

						if(response != "Please fill stock of this product."){
							window.location.href = "admin.php?page=wp_wer_pk_order_detail&order="+jQuery('input[name=billid]').val();
						} else {
							jQuery('#notice').show();
							console.log(jQuery('#notice'));
							
							jQuery('#notice').html(`<span class="dashicons-before dashicons-no" id="closeMe"></span>`);
							jQuery('#notice').append("<p>" + response + "</p>");
							return response;
						}

				},
                error: function(xhr, status, error) {
					jQuery("#mainSpinner").hide();
                    console.error('AJAX Error: ' + status + ' - ' + error);
                }
		});
}

function wer_pkUpdateBillItem(){

		let supIndex = jQuery('select[name=supplierName]')[0].selectedIndex;
		let matIndex = jQuery('select[name=materialsName]')[0].selectedIndex;

		console.log( "Index:", matIndex );

		console.log("Selected Store: " + jQuery('select[name=supplierName]').val());
		console.log(jQuery('select[name=supplierName]')[0][supIndex].innerText);
		console.log(jQuery('select[name=materialsName]')[0][matIndex].innerText);
			
		var postData = {};

		if( jQuery('select[name=supplierName]').val().length > 0 
		&& jQuery('select[name=materialsName]').val().length > 0 
		&& jQuery('input[name=quantity]').val().length > 0 
		&& jQuery('input[name=totalPrice]').val().length > 0 ) {
		
			jQuery("#mainSpinner").show();


			let orderQuantity = parseInt( jQuery('input[name=quantity]').val() );

			console.log("Order Quantity: ",orderQuantity + "+" + currentQuantity);
			
			let finalQuantity;

			if( orderQuantity > currentQuantity ){

				finalQuantity = orderQuantity - currentQuantity;
				console.log("Order Increased: ", finalQuantity);

			} else if (orderQuantity < currentQuantity) {

				finalQuantity = currentQuantity - orderQuantity;
				console.log("Order Decreased: ", finalQuantity);

			} 

			let discountPer = parseInt(jQuery('input[name=discount]').val()) / parseInt(100);

			let totalPrice = discountPer * parseInt(jQuery('input[name=totalPrice]').val());

			let sellingPrice = (parseInt(jQuery('input[name=totalPrice]').val()) - totalPrice);

			sellingPrice = parseInt(sellingPrice) * orderQuantity;

			console.log("discount %: ", discountPer);
			console.log("order Price: ", totalPrice);
			console.log("Selling Price: ", sellingPrice);
			console.log("final Quantity: ", orderQuantity);

			postData = {
				action: 'updateorder',
				billid: jQuery('input[name=billid]').val(),
				supplierName: jQuery('select[name=supplierName]').val(), //jQuery('select[name=supplierName]')[0][supIndex].innerText,
				materialsName: jQuery('select[name=materialsName]')[0][matIndex].innerText, 
				quantity: orderQuantity > currentQuantity ? finalQuantity : 0, //jQuery('input[name=quantity]').val(),
				totalPrice: jQuery('input[name=totalPrice]').val(),
				product: jQuery('select[name=materialsName]').val(),
				GST: jQuery('input[name=GST]').val(),
				discount: jQuery('input[name=discount]').val(),
				productid: jQuery('input[name=productid]').val(),
				oldQuantity: currentQuantity,
				decreasedItem: orderQuantity < currentQuantity ? finalQuantity : 0
			}

			console.log("Data: ", postData);

		
		} else {
			return;
		}

		jQuery.ajax({
				type : 'post',
				dataType: 'json',
				url : decodeURI(siteURL)+'/wp-admin/admin-ajax.php',
				data : postData,
				success: function (response) {
						console.log("update test", response);
						//jQuery("#project_Add_Edit").resetForm();
						jQuery("#mainSpinner").hide();

						/*
						jQuery( document ).on( 'heartbeat-send', function ( event, data ) {
							// Add additional data to Heartbeat data.
							data.myplugin_customfield = postData.project_name;
						});
						*/

						if(response != "Please fill stock of this product."){
							window.location.href = "admin.php?page=wp_wer_pk_order_detail&order="+jQuery('input[name=billid]').val();
						} else {
							jQuery('#notice').show();
							console.log(jQuery('#notice'));
							
							jQuery('#notice').html(`<span class="dashicons-before dashicons-no" id="closeMe"></span>`);
							jQuery('#notice').append("<p>" + response + "</p>");
							return response;
						}						

				},
                error: function(xhr, status, error) {
					jQuery("#mainSpinner").hide();
                    console.error('AJAX Error: ' + status + ' - ' + error);
                }
		});
	

}


/****************************************

Product Items function implementation.

**************************************/
function extractVariants(){

			const obj = {};
			var attributesArray = [];
			var attributesIds = [];


			jQuery('#inputContainer').find('input').each(function(index, element) {

				var inputValue = jQuery(this).val().trim(); // Get the value of the input
				var dataType = jQuery(this).data('type'); // Get the data-type using .data()

					console.log("Data Type: ",dataType); // Log the data-type

				// Iterate through each option in the select element
				jQuery('#selectProductVariants option').each(function() {

					// Get the value and text of the current option
					var value = jQuery(this).val().trim();
					// Skip the option with value "null"
					if (value !== "null") {
						var text = jQuery(this).text().trim();
						if(dataType == text){
							attributesIds.push("value:" + value); // Format as "Key: Value"
						}
						
					}

				});

				if(inputValue ){
							
					console.log(jQuery(element).val()); // Logs the value of each input element

					attributesArray.push(dataType + ": " + inputValue); // Format as "Key: Value"
				}

			});

			console.log("Attributes: ",attributesArray);

			// Join the array into a single string with commas
			var attributesString = attributesArray.join(", ");
			obj["attributes"] = attributesString

			// Join the array into a single string with commas
			var attributesIdsString = attributesIds.join(", ");
			obj["attributesIds"] = attributesIdsString;

			return obj;

}

function wer_pkSaveProduct(){

	if(parseInt( jQuery('input[name=product_quantity]').val()) > parseInt( jQuery('input[name=product_quantity]').attr('max') ) ){
		return;
	}
	
		var postData = {};

		if( jQuery('input[name=product_name]').val().length > 0 ) {
		
			jQuery("#mainSpinner").show();
			
			let variantObject = extractVariants();

			postData = {
				action: 'saveProduct',
				product_name: jQuery('input[name=product_name]').val(),
				product_store: jQuery('input[name=product_store]').val(),
				product_id: jQuery('input[name=product_id]').val(),
				product_quantity: jQuery('input[name=product_quantity]').val(),
				product_GST: jQuery('input[name=product_GST]').val(), 
				product_price: jQuery('input[name=product_price]').val(),
				product_discount: !jQuery('input[name=product_discount]').val() ? "0.00" : jQuery('input[name=product_discount]').val(),
				product_SKU: "sku-0"+jQuery('input[name=product_id]').val(),
				...variantObject
			}


		} else {
			return;
		}

		jQuery.ajax({
				type : 'post',
				dataType: 'json',
				url : decodeURI(siteURL)+'/wp-admin/admin-ajax.php',
				data : postData,
				success: function (response) {
						console.log("test", response);
						//jQuery("#project_Add_Edit").resetForm();
						jQuery("#mainSpinner").hide();
						window.location.href = decodeURI(siteURL)+"/seller-account/";

				},
                error: function(xhr, status, error) {
					jQuery("#mainSpinner").hide();
                    console.error('AJAX Error: ' + status + ' - ' + error);
                }
		});
}

	function wer_pkUpdateSeller(productUpdate){

		console.log(productUpdate);
		if(parseInt( jQuery('input[name=product_quantity]').val()) < parseInt( jQuery('input[name=product_quantity]').attr('min') ) ){
			return;
		}

		jQuery.ajax({
				type : 'post',
				dataType: 'json',
				url : decodeURI(siteURL)+'/wp-admin/admin-ajax.php',
				data : productUpdate,
				success: function (response) {
						console.log("test", response);

						window.location.href = decodeURI(siteURL)+"/seller-account/";
						
				},
                error: function(xhr, status, error) {
					jQuery("#mainSpinner").hide();
                    console.error('AJAX Error: ' + status + ' - ' + error);
                }
		});

	}

	function wer_pkEditSeller(productId, variantId){

		console.log("test");
	
		var postData = {};
		const objv = {};

		jQuery("#variantProducts").remove();
		
		jQuery('#inputContainer').find('a').each(function(index, element) {
			this.remove();
		});

		let finalVariants = [];

		jQuery("#mainSpinner").show();

		postData = {
			action: 'getproductById',
			productId: productId,
			variantId: variantId
		}
		
		jQuery.ajax({
				type : 'post',
				dataType: 'json',
				url : decodeURI(siteURL)+'/wp-admin/admin-ajax.php',
				data : postData,
				success: function (response) {
						console.log("test asad", response);
						

						//const inputString = "Color:  Brown,  Green, Packaging:  10x10, 20x6, Size:  L, 32, M, XL";
						const inputString = response.attributes;

						// Split the string by commas
						const attributes = inputString.split(',').map(attr => attr.trim());

						// Initialize an objvect to group the attributes
						const groupedAttributes = {};
						let prevKey = "";

						// Loop through the attributes and group them
						attributes.forEach(attr => {
							const [key, value] = attr.split(': ').map(part => part.trim());

							if (value === undefined) {
								// This means we have a value without a key
								if (prevKey) {
									// Append this value to the previous key
									groupedAttributes[prevKey] = groupedAttributes[prevKey] || '';
									groupedAttributes[prevKey] += (groupedAttributes[prevKey] ? ', ' : '') + key;
								}
							} else {
								// Valid key-value pair
								groupedAttributes[key] = value;
								prevKey = key; // Update the previous key
							}
						});

						console.log(groupedAttributes);
						
						
						const attributesArray = [];
						// Format the output
						const formattedString = Object.entries(groupedAttributes)
							.map( ([key, values]) => {  
								
								//finalVariants.push(values);
								
								var input = jQuery('<a>', {
									class: 'btn smooth btn-a btn-sm',
									name: 'variantInput[]',
									//id: 'variantInput_'+key,
									id: key,
									text: key+": "+values,
									style: "margin-right: 10px;",
									value: values
								});

								finalVariants.push(input);

							});
							//.join(', ');

						finalVariants.forEach(attr => {
							
							var isEditing = jQuery('#inputContainer').find(attr);

							console.log(attr.attr("value"));

							objv[attr.attr("id")] = attr.attr("value");

							
							// Append the input and datalist to the container
							jQuery('#inputContainer').append(attr);
							
							jQuery(attr).on("dblclick", function () {
								
							/****************
								let variantData = {};

								//attr.attr("id").remove();
								delete objv[attr.attr("id")];

								jQuery(`#${attr.attr("id")}`).remove();
								
								//console.log(attr.attr("id"));
								console.log(attr.attr("value"));

								variantData = {
									action: 'deletePVariantById',
									variant_id: jQuery('input[name=variant_id]').val(),
									attributeValue: attr.attr("value").trim()
								}

								deletePVariantById(variantData);
							*****************/

							});


						});
						attributesArray.push(objv);

						
						//objv["attributes"] = attributesArray[0];


						jQuery('input[name=product_id]').val(response.product_id ? response.product_id : response.id);
						jQuery('input[name=product_name]').val(response.materialsName);
						jQuery('input[name=product_store]').val(response.storeId);
						jQuery('input[name=product_quantity]').val(response.variantStock);
						jQuery('input[name=product_GST]').val(response.variantGST);
						jQuery('input[name=product_price]').val(response.variantPrice);
						jQuery('input[name=product_discount]').val(response.variantDiscount);
						jQuery('input[name=variant_id]').val(response.variant_id);
						jQuery('input[name=status]').val(response.status);
						jQuery('#SaveMe').val("Update Product");
						jQuery('#SaveMe').html("Update Product");
						jQuery('#SaveMe').removeAttr("onclick");

						jQuery('#SaveMe').on("click", function () {

							console.log("Attributes: ", objv);
							console.log("Updating Form...");

							let variantObject = extractVariants();
							
							postData = {
								action: 'updateProduct',
								product_id: jQuery('input[name=product_id]').val(),
								product_name: jQuery('input[name=product_name]').val(),
								product_store: jQuery('input[name=product_store]').val(),
								product_quantity: jQuery('input[name=product_quantity]').val(),
								product_GST: jQuery('input[name=product_GST]').val(), 
								product_price: jQuery('input[name=product_price]').val(),
								product_discount: !jQuery('input[name=product_discount]').val() ? "0.00" : jQuery('input[name=product_discount]').val(),
								product_SKU: "sku-00"+jQuery('input[name=product_id]').val(),
								variant_id: jQuery('input[name=variant_id]').val()
							}

							console.log(postData);
							wer_pkUpdateSeller(postData);

						})



				},
                error: function(xhr, status, error) {
					jQuery("#mainSpinner").hide();
                    console.error('AJAX Error: ' + status + ' - ' + error);
                }
		});

	}

	function wer_pkDeleteSeller(productId=null, m){

		var c = confirm(m);
		if (c) {
		
			var postData = {};

			jQuery("#mainSpinner").show();

			postData = {
				action: 'deleteProduct',
				productId: productId
			}
		
			jQuery.ajax({
					type : 'post',
					dataType: 'json',
					url : decodeURI(siteURL)+'/wp-admin/admin-ajax.php',
					data : postData,
					success: function (response) {
							console.log("test", response);
							//jQuery("#project_Add_Edit").resetForm();
							jQuery("#mainSpinner").hide();
							window.location.href = decodeURI(siteURL)+"/seller-account/";
					},
					error: function(xhr, status, error) {
						jQuery("#mainSpinner").hide();
						console.error('AJAX Error: ' + status + ' - ' + error);
					}
			});	

		}
	
	}

	function wer_pkConfirmOrder (orderId) {

		jQuery(".spinnerEmail").show();

		let c = confirm("Are you sure?");

		if(c){
			var postData = {};

			postData = {
				action: 'wer_pkOrderEmail',
				order: orderId
			}
		
			jQuery.ajax({
					type : 'post',
					dataType: 'json',
					url : decodeURI(siteURL)+'/wp-admin/admin-ajax.php',
					data : postData,
					success: function (response) {
							//console.log("Response to Email:", response);
							//jQuery(".spinnerEmail").hide();
							//jQuery("#project_Add_Edit").resetForm();
							window.location.href = "wp-admin/admin.php?page=wp_wer_pk_order_detail&order="+orderId;
					},
					error: function(xhr, status, error) {
						jQuery("#mainSpinner").hide();
						console.error('AJAX Error: ' + status + ' - ' + error);
					}
			});	
		}
	
	}

	function wer_pkProcessOrder (orderId, process) {

		jQuery(".spinnerEmail").show();

		let c = confirm("Are you sure?");

		if(c){
			var postData = {};

			postData = {
				action: 'updateOrderConfirmation',
				order: orderId,
				process: process
			}
		
			jQuery.ajax({
					type : 'post',
					dataType: 'json',
					url : decodeURI(siteURL)+'/wp-admin/admin-ajax.php',
					data : postData,
					success: function (response) {
							console.log("Response to Email:", response);
							jQuery(".spinnerEmail").hide();
							//jQuery("#project_Add_Edit").resetForm();
							window.location.href = decodeURI(siteURL)+"/orders?orderStatus=3";
					},
					error: function(xhr, status, error) {
						jQuery("#mainSpinner").hide();
						console.error('AJAX Error: ' + status + ' - ' + error);
					}
			});	
		}
	
	}

	function savePVariant () {

		const dialog = jQuery('#my-dialog');

		if( !jQuery('input[name=product_variant]').val() ){
			console.log("Please add some text.");
		}

			var postData = {};

			postData = {
				action: 'savePVariant',
				product_variant: jQuery('input[name=product_variant]').val()
			}
		
			jQuery.ajax({
					type : 'post',
					dataType: 'json',
					url : decodeURI(siteURL)+'/wp-admin/admin-ajax.php',
					data : postData,
					success: function (response) {
							console.log("Response to Email:", response);
							//jQuery("#project_Add_Edit").resetForm();
							window.location.href = decodeURI(siteURL)+"/seller-account/";
					},
					error: function(xhr, status, error) {
						jQuery("#mainSpinner").hide();
						console.error('AJAX Error: ' + status + ' - ' + error);
					}
			});	

	
	}


	function deletePVariant (variant_id) {
	
		let c = confirm("Are you sure?");

		if(c){

			var postData = {};

			postData = {
				action: 'deletePVariant',
				variant_id: variant_id
			}
		
			jQuery.ajax({
					type : 'post',
					dataType: 'json',
					url : decodeURI(siteURL)+'/wp-admin/admin-ajax.php',
					data : postData,
					success: function (response) {
							console.log("Response to Email:", response);
							//jQuery("#project_Add_Edit").resetForm();
							window.location.href = decodeURI(siteURL)+"/seller-account/";
					},
					error: function(xhr, status, error) {
						jQuery("#mainSpinner").hide();
						console.error('AJAX Error: ' + status + ' - ' + error);
					}
			});	

		}
	
	}

	
	function getPVariantById (variant_id) {
	
		var postData = {};

		postData = {
			action: 'getPVariantById',
			variant_id: variant_id
		}
		
		jQuery.ajax({
				type : 'post',
				dataType: 'json',
				url : decodeURI(siteURL)+'/wp-admin/admin-ajax.php',
				data : postData,
				success: function (response) {
						console.log("get variant information:", response);
						jQuery('input[name=product_variant]').val(response.attributeName)
						
						jQuery('#variantSubmit').removeAttr("onclick");

						jQuery('#variantSubmit').on("click", function () {

							console.log("Updating Form...");
							postData = {
								action: 'editPVariant',
								variant_id: variant_id,
								attributeName: jQuery('input[name=product_variant]').val()
							}

							console.log(postData);
							editPVariant(postData);

						})

				},
                error: function(xhr, status, error) {
					jQuery("#mainSpinner").hide();
                    console.error('AJAX Error: ' + status + ' - ' + error);
                }
		});

	}


	function editPVariant (postData) {
	
		
		jQuery.ajax({
				type : 'post',
				dataType: 'json',
				url : decodeURI(siteURL)+'/wp-admin/admin-ajax.php',
				data : postData,
				success: function (response) {
						console.log("Response to Email:", response);
						jQuery('input[name=product_variant]').val(response)
						//jQuery("#project_Add_Edit").resetForm();
						window.location.href = decodeURI(siteURL)+"/seller-account/";
				},
                error: function(xhr, status, error) {
					jQuery("#mainSpinner").hide();
                    console.error('AJAX Error: ' + status + ' - ' + error);
                }
		});

	}

    jQuery('#selectProductVariants').change(function() {
		
        // Get the selected variant ID
        var selectedVariant = jQuery(this).val();

        // Only proceed if a valid option is selected
        if (selectedVariant !== 'null') {
            // Check if the input already exists
            //if (jQuery('#variantInput_' + selectedVariant).length === 0) {
                // Create a new input field
                var input = jQuery('<input>', {
                    type: 'text',
                    name: 'variantInput[]',
                    id: 'variantInput_' + selectedVariant+"_"+j,
                    'data-variant-id': selectedVariant,
                    placeholder: 'Enter ' + jQuery(this).find('option:selected').text(),
					'data-type': jQuery(this).find('option:selected').text(),
                    list: 'suggestions_' + selectedVariant // Link to the corresponding datalist
                });

                // Create a new datalist
                var datalist = jQuery('<datalist>', {
                    id: 'suggestions_' + selectedVariant // Unique ID for datalist
                });

				// AJAX call to get the variant options
                jQuery.ajax({
                    url : decodeURI(siteURL)+'/wp-admin/admin-ajax.php',
                    method: 'GET',
                    dataType: 'json',
                    data: {
                        action: 'getSelectedVariantData',
                        attribute_id: selectedVariant // Send the selected variant ID
                    },
                    success: function(data) {
                        // Populate the datalist
                        data.forEach(function(option) {
                            datalist.append(jQuery('<option>', { value: option }));
                        });

                        // Append the input and datalist to the container
                        jQuery('#inputContainer').append(input).append(datalist);
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error: ' + status + ' - ' + error);
                    }
                });
				j++;
            //}
        }
    });



	function deletePVariantById (postData) {
	
	
		let c = confirm('Are you sure? This action can\'t be undone.');

		if(c){

			jQuery.ajax({
				type : 'post',
				dataType: 'json',
				url : decodeURI(siteURL)+'/wp-admin/admin-ajax.php',
				data : postData,
				success: function (response) {
						console.log("Deleted? :", response);
						//jQuery("#project_Add_Edit").resetForm();
						//window.location.href = decodeURI(siteURL)+"/seller-account/";
				},
                error: function(xhr, status, error) {
					jQuery("#mainSpinner").hide();
                    console.error('AJAX Error: ' + status + ' - ' + error);
                }
			});

		}
		

	}

/****************************************

Heartbeat Orders Notification function

**************************************/


  jQuery(document).ready(function() {

	jQuery(document).on('heartbeat-send', function(e, data) {
			console.log('Client: marco');
			data['client'] = 'marco';	//need some data to kick off AJAX call
		});
		
		//hook into heartbeat-tick: client looks for a 'confirmedOrders' var in the data array and logs it to console
		jQuery(document).on('heartbeat-tick', function(e, data) {			
			if(data['confirmedOrders'])
				console.log('confirmedOrders: ' + data['confirmedOrders']);
				if(data['confirmedOrders'] > 0){
					jQuery("#assignment-count").show();
					jQuery("#assignment-count").html( data['confirmedOrders'] );
				} else {
					//jQuery("#assignment-count").hide();
				}
		});
				
		//hook into heartbeat-error: in case of error, let's log some stuff
		jQuery(document).on('heartbeat-error', function(e, jqXHR, textStatus, error) {
			console.log('BEGIN ERROR');
			console.log(textStatus);
			console.log(error);			
			console.log('END ERROR');			
		});

	});	
	
	function get_expenses_aggrigation () {

		jQuery.ajax({
			type : 'get',
			dataType: 'json',
			url : decodeURI(siteURL)+'/wp-admin/admin-ajax.php',
			data: {
                    action: 'get_expenses_aggrigation'
            },
			success: function (response) {
						
				console.log("Data :", response);

			},
            error: function(xhr, status, error) {
				jQuery("#mainSpinner").hide();
                console.error('AJAX Error: ' + status + ' - ' + error);
            }
		});

	}

	function getOrdersData () {

		jQuery.ajax({
			type : 'get',
			dataType: 'json',
			url : decodeURI(siteURL)+'/wp-admin/admin-ajax.php',
			data: {
                    action: 'getOrdersData'
            },
			success: function (response) {
						
				console.log("Data :", response);

			},
            error: function(xhr, status, error) {
				jQuery("#mainSpinner").hide();
                console.error('AJAX Error: ' + status + ' - ' + error);
            }
		});

	}


	function getCSVData(m, fn) {

		var c = confirm(m);

		if(c){
			
			jQuery("#mainSpinner").show();

			jQuery.ajax({
				type : 'POST',
				dataType: 'json',
				url : decodeURI(siteURL)+'/wp-admin/admin-ajax.php',
				data: {
						action: 'empty_and_populate_tables',
						fileName: fn
				},
				success: function (response) {
					console.log("Data :", response);
					jQuery("#mainSpinner").hide();
				},
				error: function(xhr, status, error) {
					jQuery("#mainSpinner").hide();
					console.error('AJAX Error: ' + status + ' - ' + error);
				}
			});

		}

	}


	function wer_pkPayOrder (data) {

		jQuery.ajax({
			type : 'get',
			dataType: 'json',
			url : decodeURI(siteURL)+'/wp-admin/admin-ajax.php',
			data: {
                    action: 'wer_pk_processPayment',
					orderDeails: data
            },
			success: function (response) {
						
				console.log("Data :", response);

			},
            error: function(xhr, status, error) {
				jQuery("#mainSpinner").hide();
                console.error('AJAX Error: ' + status + ' - ' + error);
            }
		});

	}

	function wer_pk_resetData (message) {
		
		

		var c = confirm("Are you sure?");

		if(c){
			
			jQuery("#spinnerContent").show();

			jQuery.ajax({
				type : 'get',
				dataType: 'json',
				url : decodeURI(siteURL)+'/wp-admin/admin-ajax.php',
				data: {
						action: 'reset_wer_pk_tables',
						message: message
				},
				success: function (response) {
					
					console.log("Data :", response);
					window.location.href = window.location.href;

				},
				error: function(xhr, status, error) {
					jQuery("#spinnerContent").hide();
					console.error('AJAX Error: ' + status + ' - ' + error);
				}
			});

		}

	}