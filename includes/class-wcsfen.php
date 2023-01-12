<?php
	if ( ! class_exists( 'wcsfenBackend' ) ) {
		include_once dirname( __FILE__ ) . '/class-wcsfenbackend.php';
	}
	if ( ! class_exists( 'wcsfenFrontend' ) ) {
		include_once dirname( __FILE__ ) . '/class-wcsfenfrontend.php';
	}
	class wcsfen {
		
		public function __construct(){
			
		}
	}
	
	new wcsfenBackend;
	new wcsfenFrontend;

