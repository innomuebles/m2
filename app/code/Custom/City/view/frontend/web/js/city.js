var inp = '';
function addressPageCall(){
    console.log('inside addressPageCall--->');
    var region_id = jQuery('#region_id');
    if (typeof(region_id) != 'undefined' && region_id != null) {
        inp = document.getElementById('#city');
        var value = jQuery('#region_id').val();
        if (value != '' && typeof(value) != 'undefined') {
            getRegionCitiesAddress(value,'edit');
        }
        jQuery('#region_id').change(function(event) {
            var value = jQuery('#region_id').val();
            if (value != '') {
                getRegionCitiesAddress(value,'edit');
            }
        });
        jQuery('#country_id').change(function(event) {
            var value = jQuery('#region_id').val();
            if (value != '') {
                getRegionCitiesAddress(value,'edit');
            } else {
                jQuery('#city').html(inp);
                jQuery('.billing_notinlist').remove();
            }
        });
    }
}
function shippingmainCityCart(){
    console.log('inside shippingmainCityCart--->');
    if(jQuery('#shipping-zip-form').length==0){
        setTimeout(function(){ shippingmainCityCart();}, 2000);
    }
    var region_id = jQuery('#shipping-zip-form [name="region_id"]');
    if (typeof(region_id) != 'undefined' && region_id != null
        && jQuery('#shipping-zip-form [name="city"]') != 'undefined'
        && jQuery('#shipping-zip-form [name="city"]') !=null) {
        var city_id =  jQuery('#shipping-zip-form [name="city"]').attr('id');
        inp = document.getElementById(city_id);
        var value = jQuery('#shipping-zip-form [name="region_id"]').val();
        if (value != '' && typeof(value) != 'undefined') {
            getRegionCitiesCart(value,'shipping-zip-form');
        } else { 
			setTimeout(function(){
				getRegionCitiesCart(jQuery('#shipping-zip-form [name="region_id"]').val(),'shipping-zip-form');
			}, 2000); 
		}
        jQuery('#shipping-zip-form [name="region_id"]').change(function(event) {
            var value = jQuery('#shipping-zip-form [name="region_id"]').val();
            if (value != '') {
                getRegionCitiesCart(value,'shipping-zip-form');
            }
        });
        jQuery('#shipping-zip-form [name="country_id"]').change(function(event) {
            var value = jQuery('#shipping-zip-form [name="region_id"]').val();
            if (value != '') {
                getRegionCitiesCart(value,'shipping-zip-form');
            } else {
                jQuery('#shipping-zip-form [name="city"]').html(inp);
                jQuery('#shipping-zip-form .billing_notinlist').remove();
            }
        });
    }
}
function bilingmainCityCall(){
    console.log('inside bilingmainCityCall--->');
    if(jQuery('#billing-new-address-form [name="region_id"]').length == 0){
        setTimeout(function(){ bilingmainCityCall();}, 1000);
    }
    var region_id = jQuery('#billing-new-address-form [name="region_id"]');
    if (typeof(region_id) != 'undefined' && region_id != null) {
        var city_id =  jQuery('#billing-new-address-form [name="city"]').attr('id');
        inp = document.getElementById(city_id);
        var value = jQuery('#billing-new-address-form [name="region_id"]').val();
        if (value != '' && typeof(value) != 'undefined') {
            getRegionCitiesBilling(value,'billing-new-address-form');
        }
        jQuery('#billing-new-address-form [name="region_id"]').change(function(event) {
            var value = jQuery('#billing-new-address-form [name="region_id"]').val();
            if (value != '') {
                getRegionCitiesBilling(value,'billing-new-address-form');
            }
        });
        jQuery('#billing-new-address-form [name="country_id"]').change(function(event) {
            var value = jQuery('#billing-new-address-form [name="region_id"]').val();
            if (value != '') {
                getRegionCitiesBilling(value,'billing-new-address-form');
            } else {
                jQuery('#billing-new-address-form [name="city"]').html(inp);
                jQuery('#billing-new-address-form .billing_notinlist').remove();
            }
        });
    }
}

function shippingmainCityCall(){
    console.log('inside shippingmainCityCall--->');
    if(jQuery('#co-shipping-form [name="region_id"]').length == 0){
        setTimeout(function(){ shippingmainCityCall();}, 1000);
    }else if(jQuery('#shipping').css('display') == 'none' || jQuery('#co-shipping-form').css('display')== 'none'){
        setTimeout(function(){ bilingmainCityCall();}, 1000);
    }
    var region_id = jQuery('#co-shipping-form [name="region_id"]');
    if (typeof(region_id) != 'undefined' && region_id != null) {
        var city_id =  jQuery('#co-shipping-form [name="city"]').attr('id');
        inp = document.getElementById(city_id);
        var value = jQuery('#co-shipping-form [name="region_id"]').val();
        if (value != '' && typeof(value) != 'undefined') {
            getRegionCitiesShipping(value,'co-shipping-form');
        }
        jQuery('#co-shipping-form [name="region_id"]').change(function(event) {
            var value = jQuery('#co-shipping-form [name="region_id"]').val();
            if (value != '') {
                getRegionCitiesShipping(value,'co-shipping-form');
            }
        });
        jQuery('#co-shipping-form [name="country_id"]').change(function(event) {
            var value = jQuery('#co-shipping-form [name="region_id"]').val();
            if (value != '') {
                getRegionCitiesShipping(value,'co-shipping-form');
            } else {
                jQuery('#co-shipping-form [name="city"]').html(inp);
                jQuery('#co-shipping-form .billing_notinlist').remove();
            }
        });
    }
}

window.realodShippingRate = function() {
	try { 
		var quote = require('Magento_Checkout/js/model/quote'); 
		var rateRegistry = require('Magento_Checkout/js/model/shipping-rate-registry'); 
		var address = quote.shippingAddress(); 
		// address.trigger_reload = new Date().getTime(); 
		rateRegistry.set(address.getKey(), null);
		rateRegistry.set(address.getCacheKey(), null); 
		quote.shippingAddress(address);  
	} catch (e){}
}; 
window.getCustomerCity = function() {
	
	try {
		
		var quote = require('Magento_Checkout/js/model/quote');
		
		return quote.shippingAddress().city ? quote.shippingAddress().city : '';
		
	} catch (e) { }
	
	return '';
};

/* This is for checkout shipping Step */
var ajaxLoading = false;
function getRegionCitiesShipping(value,main_id) {
    if(!ajaxLoading) {
        ajaxLoading = true;
        var city_id =  jQuery('#'+main_id+' [name="city"]').attr('id');
        var url = window.data_url;
        var loader = '<div data-role="loader" class="loading-mask city_loading_mask" style="position: relative;text-align:right;"><div class="loader"><img src="'+window.loading_url+'" alt="Loading..." style="position: absolute;text-align:center;"></div>Please wait loading cities...</div>';
        if(jQuery('#'+main_id+' .city_loading_mask').length==0){
            jQuery('#'+main_id+' [name="city"]').after(loader);
        }
        emptyInput('',main_id);
        jQuery('#error-'+city_id).hide();
        jQuery('.mage-error').hide();
        jQuery('#'+main_id+' [name="city"]').hide();
        jQuery('#'+city_id+'-select').remove();
        jQuery('#'+main_id+' .billing_notinlist').remove();
        jQuery.ajax({
            url : url,
            type: "get",
            data:"state="+value,
            dataType: 'json',
        }).done(function (transport) {
            ajaxLoading = false;
            jQuery('#error-'+city_id).show();
            jQuery('.mage-error').show();
            jQuery('#'+main_id+' .city_loading_mask').remove();
            jQuery('#'+main_id+' [name="city"]').show();
            var response = transport; 
            var options = '<select onchange="getCityStateShipping(this.value,\''+main_id+'\')" id="'+city_id+'-select" class="select" title="City" name="city-select" ><option value="">Please select city</option>';
            if (response.length > 0) {
				
				var customerCity = window.getCustomerCity();
				
                for (var i = 0; i < response.length; i++) {
					
					if(customerCity && customerCity.toLowerCase() == response[i].toLowerCase()) {
						
						options += '<option selected="selected" value="' + response[i] + '">' + response[i] + '</option>';
						
					} else {
						
						options += '<option value="' + response[i] + '">' + response[i] + '</option>';
					}
                }
                options += "</select>";
                if(window.data_city_link!=""){
                    var title = window.data_city_title;
                    options+= "<br class='br_billing_notinlist' /><a onclick='notInList(\"billing\",\""+main_id+"\")' class='billing_notinlist' href='javascript:void(0)' class='notinlist'>"+title+"</a>";
                }
                jQuery('#'+main_id+' [name="city"]').hide();
                if(jQuery('#'+city_id+'-select').length==0){
                    jQuery('#'+main_id+' [name="city"]').after(options);
                    jQuery(options).change(); 
                    window.realodShippingRate();
                }
            } else {
                jQuery('#'+main_id+' [name="city"]').html(inp);
                jQuery('#'+main_id+' .billing_notinlist').remove();
            }
        }).fail( function ( error )
        {
            ajaxLoading = false;
            jQuery('#error-'+city_id).show();
            jQuery('#'+main_id+' .city_loading_mask').remove();
            jQuery('#'+main_id+' [name="city"]').show();
            console.log(error);
        });
    }
}

/* This is for checkout billing Step */

function getRegionCitiesBilling(value,main_id) {
    if(!ajaxLoading) {
        ajaxLoading = true;
        var city_id =  jQuery('#'+main_id+' [name="city"]').attr('id');
        var url = window.data_url;
        var loader = '<div data-role="loader" class="loading-mask city_loading_mask" style="position: relative;text-align:right;"><div class="loader"><img src="'+window.loading_url+'" alt="Loading..." style="position: absolute;text-align:center;"></div>Please wait loading cities...</div>';
        if(jQuery('#'+main_id+' .city_loading_mask').length==0){
            jQuery('#'+main_id+' [name="city"]').after(loader);
        }
        emptyInput('',main_id);
        jQuery('#error-'+city_id).hide();
        jQuery('.mage-error').hide();
        jQuery('#'+main_id+' [name="city"]').hide();
        jQuery('#'+city_id+'-select').remove();
        jQuery('#'+main_id+' .billing_notinlist').remove();
        jQuery.ajax({
            url : url,
            type: "get",
            data:"state="+value,
            dataType: 'json',
        }).done(function (transport) {
            ajaxLoading = false;
            jQuery('#error-'+city_id).show();
            jQuery('.mage-error').show();
            jQuery('#'+main_id+' .city_loading_mask').remove();
            jQuery('#'+main_id+' [name="city"]').show();
            var response = transport;

            var options = '<select onchange="getCityState(this.value,\''+main_id+'\')" id="'+city_id+'-select" class="select" title="City" name="city-select" ><option value="">Please select city</option>';
            if (response.length > 0) {
				
                var customerCity = window.getCustomerCity();
				
                for (var i = 0; i < response.length; i++) {
					
					if(customerCity && customerCity.toLowerCase() == response[i].toLowerCase()) {
						
						options += '<option selected="selected" value="' + response[i] + '">' + response[i] + '</option>';
						
					} else {
						
						options += '<option value="' + response[i] + '">' + response[i] + '</option>';
					}
                }
                
                options += "</select>";
                if(window.data_city_link!=""){
                    var title = window.data_city_title;
                    options+= "<br class='br_billing_notinlist' /><a onclick='notInList(\"billing\",\""+main_id+"\")' class='billing_notinlist' href='javascript:void(0)' class='notinlist'>"+title+"</a>";
                }
                jQuery('#'+main_id+' [name="city"]').hide();
                if(jQuery('#'+city_id+'-select').length==0){
                    jQuery('#'+main_id+' [name="city"]').after(options);
                }
            } else {
                jQuery('#'+main_id+' [name="city"]').html(inp);
                jQuery('#'+main_id+' .billing_notinlist').remove();
            }
        }).fail( function ( error )
        {
            ajaxLoading = false;
            jQuery('#error-'+city_id).show();
            jQuery('#'+main_id+' .city_loading_mask').remove();
            jQuery('#'+main_id+' [name="city"]').show();
            console.log(error);
        });
    }
}

/* This is for cart Step */

function getRegionCitiesCart(value,main_id) {
    if(!ajaxLoading) {
        ajaxLoading = true;
        var city_id =  jQuery('#'+main_id+' [name="city"]').attr('id');
        var url = window.data_url;
        var loader = '<div data-role="loader" class="loading-mask city_loading_mask" style="position: relative;text-align:right;"><div class="loader"><img src="'+window.loading_url+'" alt="Loading..." style="position: absolute;text-align:center;"></div>Please wait loading cities...</div>';
        if(jQuery('#'+main_id+' .city_loading_mask').length==0){
            jQuery('#'+main_id+' [name="city"]').after(loader);
        }
        emptyInput('',main_id);
        jQuery('#error-'+city_id).hide();
        jQuery('.mage-error').hide();
        jQuery('#'+main_id+' [name="city"]').hide();
        jQuery('#'+city_id+'-select').remove();
        jQuery('#'+main_id+' .billing_notinlist').remove();
        jQuery.ajax({
            url : url,
            type: "get",
            data:"state="+value,
            dataType: 'json',
        }).done(function (transport) {
            ajaxLoading = false;
            jQuery('#error-'+city_id).show();
            jQuery('.mage-error').show();
            jQuery('#'+main_id+' .city_loading_mask').remove();
            jQuery('#'+main_id+' [name="city"]').show();
            var response = transport;

            var options = '<select onchange="getCityState(this.value,\''+main_id+'\')" id="'+city_id+'-select" class="select" title="City" name="city-select" ><option value="">Please select city</option>';
            if (response.length > 0) {
                for (var i = 0; i < response.length; i++) {
                    options += '<option value="' + response[i] + '">' + response[i] + '</option>';
                }
                options += "</select>";
                if(window.data_city_link!=""){
                    var title = window.data_city_title;
                    options+= "<br class='br_billing_notinlist' /><a onclick='notInList(\"billing\",\""+main_id+"\")' class='billing_notinlist' href='javascript:void(0)' class='notinlist'>"+title+"</a>";
                }
                jQuery('#'+main_id+' [name="city"]').hide();
                if(jQuery('#'+city_id+'-select').length==0){
                    jQuery('#'+main_id+' [name="city"]').after(options);
                }
            } else {
                jQuery('#'+main_id+' [name="city"]').html(inp);
                jQuery('#'+main_id+' .billing_notinlist').remove();
            }
        }).fail( function ( error )
        {
            ajaxLoading = false;
            jQuery('#error-'+city_id).show();
            jQuery('#'+main_id+' .city_loading_mask').remove();
            jQuery('#'+main_id+' [name="city"]').show();
            console.log(error);
        });
    }
}

function getRegionCitiesAddress(value,main_id) {
    var main_id = 'edit';
    if(!ajaxLoading) {
        ajaxLoading = true;
        var city_id =  "city";
        var url = window.data_url;
        var loader = '<div data-role="loader" class="loading-mask city_loading_mask" style="position: relative;text-align:right;"><div class="loader"><img src="'+window.loading_url+'" alt="Loading..." style="position: absolute;text-align:center;"></div>Please wait loading cities...</div>';
        if(jQuery('.city_loading_mask').length==0){
            jQuery('#city').after(loader);
        }
        emptyInput('',main_id);
        jQuery('#error-'+city_id).hide();
        jQuery('#city-select-error').remove();
        jQuery('.mage-error').hide();
        jQuery('#city').hide();
        jQuery('#'+city_id+'-select').remove();
        jQuery('.billing_notinlist').remove();
        jQuery.ajax({
            url : url,
            type: "get",
            data:"state="+value,
            dataType: 'json',
        }).done(function (transport) {
            ajaxLoading = false;
            jQuery('#error-'+city_id).show();
            jQuery('.mage-error').show();
            jQuery('.city_loading_mask').remove();
            jQuery('#city').show();
            var response = transport;

            var options = '<select onchange="getCityState(this.value,\''+main_id+'\')" id="'+city_id+'-select" class="validate-select select" title="City" name="city-select" ><option value="">Please select city</option>';
            if (response.length > 0) {
                for (var i = 0; i < response.length; i++) {
                    options += '<option value="' + response[i] + '">' + response[i] + '</option>';
                }
                options += "</select>";
                if(window.data_city_link!=""){
                    var title = window.data_city_title;
                    options+= "<br class='br_billing_notinlist' /><a onclick='notInList(\"billing\",\""+main_id+"\")' class='billing_notinlist' href='javascript:void(0)' class='notinlist'>"+title+"</a>";
                }
                jQuery('#city').hide();
                if(jQuery('#'+city_id+'-select').length==0){
                    jQuery('#city').after(options);
                }
            } else {
                jQuery('#city').html(inp);
                jQuery('.billing_notinlist').remove();
            }
        }).fail( function ( error )
        {
            ajaxLoading = false;
            jQuery('#error-'+city_id).show();
            jQuery('.city_loading_mask').remove();
            jQuery('#city').show();
            console.log(error);
        });
    }
}
/* City not in list */
function notInList(type,main_id){
    if(main_id=='edit'){
        var city_id =  "city";
        jQuery('#'+city_id+'-select').remove();
        jQuery('.billing_notinlist').remove();
        jQuery('.br_billing_notinlist').remove();
        jQuery('#city').show();
    }else{
        var city_id =  jQuery('#'+main_id+' [name="city"]').attr('id');
        jQuery('#'+city_id+'-select').remove();
        jQuery('#'+main_id+' .billing_notinlist').remove();
        jQuery('#'+main_id+' .br_billing_notinlist').remove();
        jQuery('#'+main_id+' [name="city"]').show();
    }

}
function getCityState(val,main_id){
    emptyInput(val,main_id);
}

function getCityStateShipping(val,main_id){
    emptyInput(val,main_id);
    //tmp_163::loadNearestStore(val);

}

function emptyInput(val,main_id){
    if(main_id=='edit'){
        jQuery('#city').focus();
        jQuery('#city').val(val);
        e = jQuery.Event('keyup');
        e.keyCode= 13; // enter
        jQuery('#city').trigger(e);
    }else{
        jQuery('#'+main_id+' [name="city"]').focus();
        jQuery('#'+main_id+' [name="city"]').val(val);
        e = jQuery.Event('keyup');
        e.keyCode= 13; // enter
        jQuery('#'+main_id+' [name="city"]').trigger(e);
    }
}
function loadNearestStore(city_name){
    
            jQuery("#show-address").html('');
            
            if(city_name!=""){
                    var url = window.nearStoreUrl;
                    jQuery.ajax({
                                                        dataType: 'json',
                                                        showLoader: true,
                                                        url: url,
                                                        type: "POST",
                                                        data: {ajaxid: 2, city_name: city_name},
                                                        success: function (data) {



                                                            jQuery('#pickup-store').find('option').remove().end();

                                                            var length = data.storeinfo.length;

                                                            var newOption = ('<option value="">Select pickup store.</option>')
                                                            jQuery('#pickup-store').append(newOption);

                                                            var markers1 = [];
                                                            var messages1 = [];

															var slectedStorePickupId = null;
															
                                                            for (var j = 0; j < length; j++)
                                                            { 
																if(data.storeinfo[j].storelocator_id == data.storeinfo[j].selected) {
																	
																	slectedStorePickupId = data.storeinfo[j].storelocator_id;
																	var newOption = ('<option selected="selected" value="' + data.storeinfo[j].storelocator_id + '">' + data.storeinfo[j].storename + '</option>');
																	
																} else {
																	
																	var newOption = ('<option value="' + data.storeinfo[j].storelocator_id + '">' + data.storeinfo[j].storename + '</option>');
																}
                                                                
                                                                jQuery('#pickup-store').append(newOption);
                                                                markers1.push(data.storeinfo[j]);
                                                                messages1.push(JSON.stringify(data.messages[j]));

                                                            }

															if(slectedStorePickupId) {
																
																jQuery('#pickup-store').val(slectedStorePickupId);
															}				

                                                            initializeshipMap(markers1, messages1);
                                                            var el = jQuery("#pickup-store");
                                                            el.removeAttr("disabled");
                                                            jQuery(".store-container-inner").show();

                                                            //~ jQuery('#pickup-store').val("");
                                                            jQuery("#show-address").html('');
                                                            jQuery("#date-pickup").hide();
                                                        },
                                                        error: function (xhr) {
                                                            
                                                        }
                                        });

                                    }
}
