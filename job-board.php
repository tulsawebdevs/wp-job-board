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
	
	function __construct()
	{
		
		// TODO: require a certain PHP version?
		
		register_activation_hook( __FILE__, array( &$this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( &$this, 'deactivate' ) );
		
		add_action( 'admin_notices', array( &$this, 'admin_notices' ) );
		
		if(!class_exists('NewPostType'))
			require_once('classes/newposttype/new_post_type.php');
		
		if(!class_exists('cmb_Meta_Box'))
			require_once('classes/cmb/init.php');
		
		// Register post types
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
			'context' => 'side',
			'priority' => 'default',
			'fields' => array(
/*
				array(
					'name' => 'Company Name',
					//'desc' => 'field description (optional)',
					'id' => 'job_company',
					'type' => 'text_medium'
				),
*/
				array(
					'name' => 'Start Date',
					//'desc' => 'field description (optional)',
					'id' => 'job_start_date',
					'type' => 'text_date'
				),
				array(
					'name' => 'Contact Name',
					//'desc' => 'field description (optional)',
					'id' => 'job_contact',
					'type' => 'text'
				),
				array(
					'name' => 'Contact Email',
					//'desc' => 'field description (optional)',
					'id' => 'job_contact_email',
					'type' => 'text'
				),
				array(
					'name' => 'Contact Phone',
					//'desc' => 'field description (optional)',
					'id' => 'job_contact_phone',
					'type' => 'text'
				),
			)
		));
		
	}
	
	public function admin_notices()
	{
		if( !class_exists('GFCPTAddon') && !get_option( 'twd_jb_GFCPTAddon_notice_displayed' ) ){
			echo '<div class="updated fade"><p>TWD Job Board plugin can use use "Gravity Forms + Custom Post Types" plugin to make submitting new listings easier</p></div>';
			update_option( 'twd_jb_GFCPTAddon_notice_displayed', true );
		}
	}
	
	public function activate()
	{

	}

	public function deactivate()
	{
		// remove twd_jb_GFCPTAddon_notice option
		delete_option( 'twd_jb_GFCPTAddon_notice_displayed' );
	}
	
}

new TWD_Job_Board();

// TODO: check for errors and broadcast. don't do it like this \/
/*
if( is_wp_error( $job_board = new TWD_Job_Board ) )
	echo $job_board->get_error_message();
*/
