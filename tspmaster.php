<?php

/**
 * @package Spider Man 
 * @version 1.0.0
 */
/* asasd
Plugin Name: Master of tinsinhphuc
Plugin URI: http://wordpress.org/plugins/hello-dolly/
Description: thực hiện các thủ tục của công ty như in đơn hàng, in báo giá, in ...
Author: tienlavan
Version: 1.0.0 
Author URI: hackintosh.vn
*/ 

if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}
 
if (! class_exists('tspMaster')) {
	class tspMaster {

		protected static $instance;

		public static function get_instance(){
			if( is_null( self::$instance ) ){
				self::$instance = new self();
			}
			return self::$instance;
		}

		public function __construct() {
			add_action( 'plugins_loaded',array($this,'KiemtraPlugin'),0);
		}

		public function KiemtraPlugin() {			
			
			// nếu plugin cha có thì nạp plugin
			$this->TudongTaiFile();	
		
		}

		public function ThongbaoLoi() { 
			// Thông báo lỗi load plugin
			?>
				<div class='error'>
					<p> Plugin Simple Options is not loaded </p>
				</div>

			<?php
		}

		// kiểm tra plugin để 
		public function TudongTaiFile() {

			$filedir = plugin_dir_path(__FILE__);
	
			$filedir =  $filedir . 'includes'  ;             
			
			if (! file_exists($filedir.'/classes.json')) return;			
			$_classes = json_decode(file_get_contents($filedir.'/classes.json'),true);      
		
			foreach ($_classes as $key => $isfile) {
			if ( file_exists($filedir . '/' . $isfile) ) {                    
				require_once($filedir . '/' . $isfile);                      
				$initclass = str_replace('-','',str_replace('.php','',$isfile));                  
				if (function_exists($initclass)) $initclass();
			}
						
			}    		 
		}
	}
}
return tspMaster::get_instance(); 

//fswatch -0 tinsinhphuc | xargs -0 -n 1 -I {} 
//rsync -avzhe ssh --progress --delete --exclude '.*' --chmod=0755 --chown=www-data:www-data tinsinhphuc/  
//root@45.32.123.235:/home/nginx/sites/indepgiasi/blog/wp-content/plugins/tinsinhphuc/