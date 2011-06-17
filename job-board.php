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
	
		if(!class_exists('NewPostType'))
			require_once('classes/newposttype/new_post_type.php');
		
		if(!class_exists('cmb_Meta_Box')){
			global $meta_boxes; // stupid thing you have to do
			$meta_boxes = array();
			
			require_once('classes/cmb/init.php');
		}
			
		NewPostType::instance()->add(array(
			'post_type' => 'twd_job_post',
			'post_type_name' => 'Job Post',
			'args' => array(
				'rewrite' => array( 'slug' => 'job' ),
				'supports' => array( 'title', 'editor', 'excerpt' ),
				'menu_icon' => TWD_JB_URL.'/img/luggage--plus.png'
			)
		))->add_taxonomy( 'jb_type', array(
			'taxonomy_single' => 'Type'
		));
		
	}
	
}

new TWD_Job_Board;