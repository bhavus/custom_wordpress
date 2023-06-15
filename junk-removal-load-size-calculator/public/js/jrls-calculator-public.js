jQuery(function(){
	jQuery(".valhideshow").hide();
	jQuery('[data-toggle="tooltip"]').tooltip();  

	jQuery(".btn-calc").click(function(e){
		var baserate = 124;
		var calcxl= jQuery("#calcxl").val();
		var calcl= jQuery("#calcl").val();
		var calcm= jQuery("#calcm").val();
		var calcrbb= jQuery("#calcrbb").val();
		
		var xl = calcxl*42;
		var l = calcl*28;
		var m = calcm*14;
		var rbb = calcrbb*12;
		
		var TotalAmount= xl + l + m + rbb + baserate;
		
		jQuery("#txtTotalAmount").val(TotalAmount);
		if ( TotalAmount >= 124 && TotalAmount <= 224 ) {
			var valrate = '1/4 truck';
			jQuery("#rs_truck").html(valrate);
		}else if (TotalAmount >= 225 && TotalAmount <= 324 ) {
			var valrate = '1/2 truck';
			jQuery("#rs_truck").html(valrate);
		}else if (TotalAmount >= 325 && TotalAmount <= 424 ) {
			var valrate = '3/4 truck';
			jQuery("#rs_truck").html(valrate);
		}else if (TotalAmount >= 425 && TotalAmount <= 504 ) {
			var valrate = 'Full';
			jQuery("#rs_truck").html(valrate);
		}else if (TotalAmount >= 505 && TotalAmount <= 694 ) {
			var valrate = 'XL';
			jQuery("#rs_truck").html(valrate);
		}else {
			var valrate = 'Get a Custo Quote';
			jQuery("#rs_truck").html(valrate);
			}
		jQuery("#truck").val(valrate);	
		});

		var ajaxurl = public_jrlsco.ajaxurl;

	jQuery(document).on("click", "#btn-front-end-ajax", function(event){
		event.preventDefault();
		var postdata = jQuery("#frm-add-estimate").serialize();
		const isValidUrl = urlString=> {
			var urlPattern = new RegExp('^(https?:\\/\\/)?'+ 
		  '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+
		  '((\\d{1,3}\\.){3}\\d{1,3}))'+ 
		  '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ 
		  '(\\?[;&a-z\\d%_.~+=-]*)?'+ 
		  '(\\#[-a-z\\d_]*)?$','i'); 
		return !!urlPattern.test(urlString);
	    }
		var link = jQuery(this).attr('href');
	
		if(!isValidUrl(link)){
			// alert("Enter Valid Url");
			jQuery('#frm-add-estimate')[0].reset();
		}
		postdata += "&action=public_ajax_request&param=first_ajax_request";
     	jQuery.post(ajaxurl, postdata, function(response){
            
			if(response.status == 1){
			  jQuery('#frm-add-estimate')[0].reset();
				
				const formredirection = setInterval(function () {
					clearInterval(formredirection);
					window.location.href = link;
				}, 1000);
			}
		});
	});

});
jQuery(document).ready(function($){
	
	$(".xl_calc").keyup(function(){
		//alert('hi');
		jQuery(".valhideshow").show();
		var baserate = 124;
		let calcxl = jQuery("#calcxl").val();
		let calcl = jQuery("#calcl").val();
		let calcm = jQuery("#calcm").val();
		let calcrbb = jQuery("#calcrbb").val();
	
		var xl = calcxl*42;
		var l = calcl*28;
		var m = calcm*14;
		var rbb = calcrbb*12;
		if(xl == 0 && l == 0 && m == 0 && rbb == 0){
			jQuery(".valhideshow").hide();
		}
		var TotalAmount= xl + l + m + rbb + baserate;
		if ( TotalAmount >= 124 && TotalAmount <= 224 ) {
			var valrate = '1/4 truck';
			jQuery("#rs_truck").html(valrate);
		}else if (TotalAmount >= 225 && TotalAmount <= 324 ) {
			var valrate = '1/2 truck';
			jQuery("#rs_truck").html(valrate);
		}else if (TotalAmount >= 325 && TotalAmount <= 424 ) {
			var valrate = '3/4 truck';
			jQuery("#rs_truck").html(valrate);
		}else if (TotalAmount >= 425 && TotalAmount <= 504 ) {
			var valrate = 'Full';
			jQuery("#rs_truck").html(valrate);
		}else if (TotalAmount >= 505 && TotalAmount <= 694 ) {
			var valrate = 'XL';
			jQuery("#rs_truck").html(valrate);
		}else {
			var valrate = 'Get a Custo Quote';
			jQuery("#rs_truck").html(valrate);
		}
		jQuery("#truck").html(valrate);
	});
  });
 