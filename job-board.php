<?php
/*
Plugin Name: TWD job board
Plugin URI: https://github.com/tulsawebdevs/wp-job-board
Description: Tulsa Web Webs job board plugin
Version: 0.0.1
Author: Patrick Forringer
Author URI: http://patrick.forringer.com
*/

/*
Goals:
Title, Company, Date, Description, Contact
Type (Contract, Full-time, Part-time)
any contributor can create jobs, and a moderator will have to approve them before they show up
*/

define( 'TWD_JB_VER', '0.0.1' );

/* Set constant path to the Content Accordion directory. */
define( 'TWD_JB_DIR', plugin_dir_path( __FILE__ ) );

/* Set constant path to the Content Accordion URL. */
define( 'TWD_JB_URL', plugin_dir_url( __FILE__ ) );

class TWD_Job_Board{
	
	function __construct(){
		
		if(!class_exists('GFCPTAddon')){
			return new WP_Error( 'required', 'job board plugin needs "Gravity Forms + Custom Post Types" plugin installed' );	
		}
		
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
				'supports' => array( 'title', 'editor' ), //, 'excerpt' 
				'menu_icon' => TWD_JB_URL.'/img/luggage--plus.png'
			)
		))->add_taxonomy( 'jb_type', array(
			'taxonomy_single' => 'Type'
		))->add_taxonomy( 'jb_company', array(
			'taxonomy_single' => 'Company'
		))->add_metabox(array(
			'title' => 'Job Details',
			'fields' => array(
		    array(
            'name' => 'Company Name',
            //'desc' => 'field description (optional)',
            'id' => 'job_company',
            'type' => 'text_medium'
        ),
				array(
	        'name' => 'Job Start Date',
	        //'desc' => 'field description (optional)',
	        'id' => 'job_start_date',
	        'type' => 'text_date'
		    ),
		    array(
            'name' => 'Contact Name',
            //'desc' => 'field description (optional)',
            'id' => 'job_contact',
            'type' => 'text_medium'
        ),
		    array(
            'name' => 'Contact Email',
            //'desc' => 'field description (optional)',
            'id' => 'job_contact_email',
            'type' => 'text_medium'
        )
			)
		));
		
	}
	
}

new TWD_Job_Board;