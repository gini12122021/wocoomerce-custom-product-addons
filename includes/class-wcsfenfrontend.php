<?php
	class wcsfenFrontend extends wcsfen {
		
		public function __construct(){
				add_action( 'wp_enqueue_scripts', array( $this, 'wcsfen_enqueue_frontend_script')); // add css and js
				/*remove img uplode custom*/
				add_action( 'wp_ajax_wcsfen_remove_imgcartid', array( $this, 'wcsfen_remove_imgcartid') );
				add_action( 'wp_ajax_nopriv_wcsfen_remove_imgcartid', array( $this, 'wcsfen_remove_imgcartid') );
				/*img uplode ajax */
				add_action( 'wp_ajax_wcsfen_update_cart_imguplode_custome', array( $this, 'wcsfen_update_cart_imguplode_custome' ));
				add_action( 'wp_ajax_nopriv_wcsfen_update_cart_imguplode_custome', array( $this, 'wcsfen_update_cart_imguplode_custome' ));
				/*single page uplode img add before cart button*/
				add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'wcsfen_display_additional_product_fields'), 9 );
				/*add custome img uplode in cart item hook*/
				add_filter( 'woocommerce_add_cart_item_data', array( $this, 'wcsfen_add_custom_fields_data_as_custom_cart_item_data'), 10, 2 );
				/*display custom uplode img cart page */
				add_filter( 'woocommerce_get_item_data', array( $this, 'wcsfen_display_custom_item_data'), 10, 2 );
				/*add in order  custome data*/
				//add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'wcsfen_custom_field_update_order_item_meta'), 20, 4 );

				add_action( 'woocommerce_after_order_itemmeta', array( $this, 'wcsfen_backend_image_link_after_order_itemmeta'), 10, 3 );
				add_action( 'woocommerce_add_order_item_meta', array( $this, 'wcsfen_add_values_to_order_item_meta'), 10, 2 );

				
				/*send email table img */
				add_action( 'woocommerce_email_after_order_table', array( $this,'wc_email_new_order_custom_meta_data'), 10, 4);
				add_action( 'woocommerce_after_cart_item_name', array( $this,'prefix_after_cart_item_name'), 10, 2 );
				add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'wcsfen_checkout_create_order_line_item'), 10, 4 );
		}
		
	
	
		
		/*js and css add */
		public function wcsfen_enqueue_frontend_script(){
			wp_enqueue_style( 'customproduct' );
			wp_enqueue_style( 'wcsfen-frontendstyle', plugin_dir_url( __FILE__ ) . '../assets/css/wcsfen-frontendstyle.css' ); 
			wp_enqueue_script( 'customproduct' );
			wp_enqueue_script( 'ajax-script', plugin_dir_url( __FILE__ ) . '../assets/js/wcsfen-frontendscript.js', array( 'jquery' ), false, true );
            wp_localize_script( 'ajax-script', 'my_ajax_object',array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );

		
		}


		/*display single page uplode img */
		public function wcsfen_display_additional_product_fields(){
			?>
			<p class="form-row validate-required dispps" id="image" >
				<label for="file_field"><?php echo __("Upload Image") . ': '; ?>
					<input type='file' class="fileuplode1" name='image' accept='image/*'>
				</label>
			</p>
			<?php

		}


	// Add custom fields data as the cart item custom data

	public function wcsfen_add_custom_fields_data_as_custom_cart_item_data( $cart_item, $product_id ){
    if( isset($_FILES['image']) && ! empty($_FILES['image']) ) {
        $upload       = wp_upload_bits( $_FILES['image']['name'], null, file_get_contents( $_FILES['image']['tmp_name'] ) );
        $filetype     = wp_check_filetype( basename( $upload['file'] ), null );
        $upload_dir   = wp_upload_dir();
        $upl_base_url = is_ssl() ? str_replace('http://', 'https://', $upload_dir['baseurl']) : $upload_dir['baseurl'];
        $base_name    = basename( $upload['file'] );

        $cart_item['file_upload'] = array(
            'guid'      => $upl_base_url .'/'. _wp_relative_upload_path( $upload['file'] ), // Url
            'file_type' => $filetype['type'], // File type
            'file_name' => $base_name, // File name
            'title'     => ucfirst( preg_replace('/\.[^.]+$/', '', $base_name ) ), // Title
        );
        $cart_item['unique_key'] = md5( microtime().rand() ); // Avoid merging items
    }
    return $cart_item;
	}

	// Display custom cart item data in cart (optional)
	public function wcsfen_display_custom_item_data( $cart_item_data, $cart_item ) {
   
		if ( isset( $cart_item['file_upload']['title'] ) ){
		
			$cart_item_data[] = array(
				'name' => __( 'Image uploaded', 'woocommerce' ),
				'value' =>  '<a id="removeid_'.$cart_item['key'].'" data-cartid="'.$cart_item['key'].'" class="removeimg">remove</a><br/><img src="'.$cart_item['file_upload']['guid'].'">',
			);
		}
		return $cart_item_data;
	}

	// Save Image data as order item meta data

	public function wcsfen_custom_field_update_order_item_meta( $item, $cart_item_key, $values, $order ) {
		if ( isset( $values['file_upload'] ) ){
			$item->update_meta_data( '_img_file',  $values['file_upload'] );
		}
	}

// Admin orders: Display a linked button + the link of the image file
public function wcsfen_backend_image_link_after_order_itemmeta( $item_id, $item, $product ) {
    // Only in backend for order line items (avoiding errors)
    if( is_admin() && $item->is_type('line_item') && $file_data = $item->get_meta( '_img_file' ) ){
        echo '<p><b>Custom img:</b><a href="'.$file_data['guid'].'" target="_blank" class="button"><img style="width:50px;height:50px;" src="'.$file_data['guid'].'" ></a></p>'; // Optional
        
    }
}

// Admin new order email: Display a linked button + the link of the image file
public function wc_email_new_order_custom_meta_data( $order, $sent_to_admin, $plain_text, $email ){
    // On "new order" email notifications
    if ( 'new_order' === $email->id ) {
        foreach ($order->get_items() as $item ) {
            if ( $file_data = $item->get_meta( '_img_file' ) ) {
                echo '<p>
                    <a href="'.$file_data['guid'].'" target="_blank" class="button">'.__("Download Image") . '</a><br>
                    <img style="width:80px;height:80px;" src="'.$file_data['guid'].'" ></pre>
                </p><br>';
            }
        }
    }
}


public function prefix_after_cart_item_name( $cart_item, $cart_item_key ) {
    if ( !isset( $cart_item['file_upload']['title'] ) ){  
    $notes = isset( $cart_item['file_upload']['title'] ) ? $cart_item['file_upload']['title'] : '';
  
    printf(
    '<div id="cartid"> <label for="file_field">Upload Image<input type="file" name="image"  value="%s" accept="image" class="prefix-cart-image" id="cart_notes_%s" data-cart-id="%s" ></label></div>',
    'prefix-cart-notes',
    $cart_item_key,
    $cart_item_key,
    $notes
     );
    }
  }
   

  public function wcsfen_remove_imgcartid(){
        
	$cart = WC()->cart->cart_contents;
	$cart_id = $_POST['cart_id'];
	$cart_item = $cart[$cart_id];
	$cart_item['file_upload']=array();
	WC()->cart->cart_contents[$cart_id] = $cart_item;
	WC()->cart->set_session();
	wp_send_json( array( 'success' => 1));
	exit;
	
}
public function wcsfen_update_cart_imguplode_custome() {
	// Do a nonce check
	if( ! isset( $_POST['security'] ) || ! wp_verify_nonce( $_POST['security'], 'woocommerce-cart' ) ) {
	wp_send_json( array( 'nonce_fail' => 1 ) );
	exit;
	}
	
	// Save the notes to the cart meta
	$cart = WC()->cart->cart_contents;
	$cart_id = $_POST['cart_id'];
	$cart_item = $cart[$cart_id];
	if( isset($_FILES['image']) && ! empty($_FILES['image']) ) {
		$upload       = wp_upload_bits( $_FILES['image']['name'], null, file_get_contents( $_FILES['image']['tmp_name'] ) );
		$filetype     = wp_check_filetype( basename( $upload['file'] ), null );
		$upload_dir   = wp_upload_dir();
		$upl_base_url = is_ssl() ? str_replace('http://', 'https://', $upload_dir['baseurl']) : $upload_dir['baseurl'];
		$base_name    = basename( $upload['file'] );

		$cart_item['file_upload'] = array(
			'guid'      => $upl_base_url .'/'. _wp_relative_upload_path( $upload['file'] ), // Url
			'file_type' => $filetype['type'], // File type
			'file_name' => $base_name, // File name
			'title'     => ucfirst( preg_replace('/\.[^.]+$/', '', $base_name ) ), // Title
		);
		$cart_item['unique_key'] = md5( microtime().rand() ); // Avoid merging items
	}
   
	WC()->cart->cart_contents[$cart_id] = $cart_item;
	WC()->cart->set_session();
	wp_send_json( array( 'success' => 1,'imgparth'=> $cart_item['file_upload']['guid'] ) );
	exit;
   }
   
   public function wcsfen_checkout_create_order_line_item( $item, $cart_item_key, $values, $order ) {
	
			foreach( $item as $cart_item_key=>$cart_item ) {
			
					if( isset( $cart_item['file_upload'] ) ) {
					$item->add_meta_data( '_img_file', $cart_item['file_upload'], true );
					}
			}
   }

   public function wcsfen_add_values_to_order_item_meta($item_id, $values)
   {
		 global $woocommerce,$wpdb;
		 $user_custom_values = $values['file_upload'];
		 if(!empty($user_custom_values))
		 {
			 wc_add_order_item_meta($item_id,'_img_file',$user_custom_values);  
		 }
   }
		
}
	