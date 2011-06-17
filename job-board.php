<?php
/*
Plugin Name: TWD job board
Plugin URI: https://github.com/tulsawebdevs/wp-job-board
Description: Tulsa Web Webs job board plugin
Version: 0.0.1
Author: Patrick Forringer
Author URI: http://patrick.forringer.com
*/

define( 'TWD_JB_VER', '0.0.1' );

/* Set constant path to the Content Accordion directory. */
define( 'TWD_JB_DIR', plugin_dir_path( __FILE__ ) );

/* Set constant path to the Content Accordion URL. */
define( 'TWD_JB_URL', plugin_dir_url( __FILE__ ) );

class TWD_Job_Board{
	
	function __construct(){
		
	}
	
}

new TWD_Job_Board;