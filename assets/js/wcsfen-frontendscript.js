jQuery('document').ready(function(){
    jQuery(document).on('change','.prefix-cart-image',function(e){
		e.preventDefault();
        var fd = new FormData();
        jQuery('.cart_totals').block({
            message: null,
            overlayCSS: {
            background: '#fff',
            opacity: 0.6
            }
        });
        var cart_id = jQuery(this).data('cart-id');
        var files = jQuery('#cart_notes_'+cart_id)[0].files;
        if(files.length > 0 ){
            fd.append('image',files[0]);
            fd.append('cart_id',cart_id);
            fd.append('security',jQuery('#woocommerce-cart-nonce').val());
            fd.append('action','wcsfen_update_cart_imguplode_custome');
            jQuery.ajax({
                type: 'POST',
                url: my_ajax_object.ajax_url,
                data: fd,
                contentType: false,
                cache: false,
                processData: false,
                success: function( response ) {
                    if(response.success == 1){
                        location.reload(); 
                        jQuery('.cart_totals').unblock();
                     }
                }
                
            })
         
        }
        else{

            alert('Please Uplode correct formate.');
        }

        
    });    
     jQuery(document).on('click','.removeimg',function(e){
		e.preventDefault();
        var cart_id = jQuery(this).data('cartid');
        var str =  '&cart_id='+cart_id+ '&action=wcsfen_remove_imgcartid';
        jQuery.ajax({
            type: 'POST',
            url: my_ajax_object.ajax_url,
            data: str,
            success: function( response ) {
               if(response.success == 1){
                    location.reload(); 
                    jQuery('.cart_totals').unblock();
                    }
            }
            
            })
      
    });
    jQuery(document).on('change','.fileuplode1',function(e){
    	e.preventDefault();


		
		
		if (this.files && this.files[0] && this.files[0].name.match(/\.(jpg|jpeg|png|pdf|doc|docx)$/) ) {

			
			if(jQuery(".pip").length==0){
				var files = e.target.files;
				filesLength = files.length;
				var data = files[0].name;
				var arr = data.split('.');
				var extentionmane=arr[1];

				

				if( (extentionmane == 'png') ){
				for (var i = 0; i < filesLength; i++) {
					var f = files[i];
					var fileReader = new FileReader();
					fileReader.onload = (function(e) {
					  var file = e.target;
					  jQuery("<span class=\"pip\">" +
						"<img class=\"imageThumb\" src=\"" + e.target.result + "\" title=\"" + file.name + "\"/>" +
						"<br/><span class=\"remove\">remove</span>" +
						"</span>").insertAfter(".fileuplode1");
                        jQuery(".remove").click(function(){
					
						jQuery(this).parent(".pip").remove();
					  });
					  
					 
					  
					});
					fileReader.readAsDataURL(f);
				}

			}
			else if( (extentionmane == 'jpg') ){
				for (var i = 0; i < filesLength; i++) {
					var f = files[i];
					var fileReader = new FileReader();
					fileReader.onload = (function(e) {
					  var file = e.target;
					  jQuery("<span class=\"pip\">" +
						"<img class=\"imageThumb\" src=\"" + e.target.result + "\" title=\"" + file.name + "\"/>" +
						"<br/><span class=\"remove\">remove</span>" +
						"</span>").insertAfter(".fileuplode1");
                        jQuery(".remove").click(function(){
						console.log($(this).parent(".pip").html());
						jQuery(this).parent(".pip").remove();
					  });
					  
					 
					  
					});
					fileReader.readAsDataURL(f);
				}

			}
			else if( (extentionmane == 'jpeg') ){
				for (var i = 0; i < filesLength; i++) {
					var f = files[i];
					var fileReader = new FileReader();
					fileReader.onload = (function(e) {
					  var file = e.target;
					  jQuery("<span class=\"pip\">" +
						"<img class=\"imageThumb\" src=\"" + e.target.result + "\" title=\"" + file.name + "\"/>" +
						"<br/><span class=\"remove\">remove</span>" +
						"</span>").insertAfter(".fileuplode1");
                        jQuery(".remove").click(function(){
						console.log($(this).parent(".pip").html());
						jQuery(this).parent(".pip").remove();
					  });
					  
					 
					  
					});
					fileReader.readAsDataURL(f);
				}

			}
			else if( (extentionmane == 'doc') ){
				var f = files[0];
					var fileReader = new FileReader();
					fileReader.onload = (function(e) {
					var file = e.target;

					
					jQuery("<span class=\"pip\">" +
						"<img class=\"imageThumb\" src=\"https://promos-bwt.com/assets/image/pdf.png\" title=\"" + files[0].name + "\"/>" +
						"<br/>" + files[0].name + "<span class=\"remove\">remove</span>" +
						"</span>").insertAfter(".fileuplode1");
                        jQuery(".remove").click(function(){
                            jQuery(this).parent(".pip").remove();
					});
					
					
					
					});
					fileReader.readAsDataURL(f);

				}
				else if( (extentionmane == 'docx') ){
				var f = files[0];
					var fileReader = new FileReader();
					fileReader.onload = (function(e) {
					var file = e.target;

					
					jQuery("<span class=\"pip\">" +
						"<img class=\"imageThumb\" src=\"https://promos-bwt.com/assets/image/pdf.png\" title=\"" + files[0].name + "\"/>" +
						"<br/>" + files[0].name + "<span class=\"remove\">remove</span>" +
						"</span>").insertAfter(".fileuplode1");
                        jQuery(".remove").click(function(){
                            jQuery(this).parent(".pip").remove();
					});
					
					
					
					});
					fileReader.readAsDataURL(f);

				}
			else{

				var f = files[0];
					var fileReader = new FileReader();
					fileReader.onload = (function(e) {
					  var file = e.target;

					  
                      jQuery("<span class=\"pip\">" +
						"<img class=\"imageThumb\" src=\"https://promos-bwt.com/assets/image/pdf.png\" title=\"" + files[0].name + "\"/>" +
						"<br/>" + files[0].name + "<span class=\"remove\">remove</span>" +
						"</span>").insertAfter(".fileuplode1");
                        jQuery(".remove").click(function(){
                            jQuery(this).parent(".pip").remove();
					  });
					  
					  
					  
					});
					fileReader.readAsDataURL(f);

			}

			}else{

				console.log('length ');
				//alert('please select only one image');
			}
		} else {
			alert('This is not an image file!');
		}
    });
})

