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
		
		add_filter( 'the_content', array( &$this, 'the_content' ) );
		
		add_action( 'wp_enqueue_scripts', array( &$this , 'enqueue_scripts' ) );
		
		if(!class_exists('NewPostType'))
			require_once('classes/newposttype/new_post_type.php');
		
		if(!class_exists('cmb_Meta_Box'))
			require_once('classes/cmb/init.php');
		
		// Register post types
		$job_post_type = NewPostType::instance()->add(array(
			'post_type' => 'twd_job_post',
			'post_type_name' => 'Job Post',
			'args' => array(
				'rewrite' => array( 'slug' => 'job' ),
				'supports' => array( 'title', 'editor' ), //, 'excerpt' 
				'menu_icon' => TWD_JB_URL.'/img/luggage--plus.png',
				'has_archive' => 'jobs'
			)
		))->add_taxonomy( 'jb_type', array(
			'taxonomy_single' => 'Job Type',
			'args' => array(
				'rewrite' => array( 'slug' => 'job-type' )
			)
		))->add_taxonomy( 'jb_company', array(
			'taxonomy_single' => 'Company',
			'args' => array(
				'rewrite' => array( 'slug' => 'company' )
			)
		))->add_metabox(array(
			'title' => 'Job Details',
			'context' => 'side',
			'priority' => 'default',
			'fields' => array(
				array(
					'name' => 'Job Type',
					//'desc' => 'field description (optional)',
					'id' => 'job_type',
					'taxonomy' => 'jb_type',
					'type' => 'taxonomy-select'
				),
				/*
				array(
					'name' => 'Company Name',
					//'desc' => 'field description (optional)',
					'id' => 'job_company',
					'taxonomy' => 'jb_company',
					'type' => 'taxonomy-radio'
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
		
		add_action( 'admin_menu' , array( &$this, 'remove_meta_boxes' ));
		
		// add basic styles
		wp_register_style( 'job-board-styles', TWD_JB_URL.'/css/job.css', null, 0.1, 'screen' );
		
	}
	
	public function enqueue_scripts()
	{
		//echo get_query_var('post_type');
		//if( !is_admin() && get_query_var('post_type') == 'twd_job_post' )
		wp_enqueue_style('job-board-styles');
	}
	
	public static function single_term_output( $taxonomy ){
		
		$term = array_pop( wp_get_post_terms( get_the_ID(), $taxonomy ));

		$link = get_term_link( $term->slug, $taxonomy );
		
		return ( !empty($link) && !( $link instanceof WP_Error ) )
			? "<a href=\"{$link}\">{$term->name}</a>"
			: $term->name ;
	}
	
	public static function the_content($content)
	{
		//$class = self;
		
		if( get_post_type( get_the_ID() ) == 'twd_job_post' ){
			
			$items = array(
				'Type' => function(){
					// get the job type
					return TWD_Job_Board::single_term_output('jb_type');
				},
				'Company' => function(){
					// get company
					return TWD_Job_Board::single_term_output('jb_company');
				},
				'Start Date' => 'job_start_date',
				'Contact' => function(){
					// return a nicely formatted contact
					$o = '';
					$contact = get_post_meta( get_the_ID(), 'job_contact', true );
					$email = get_post_meta( get_the_ID(), 'job_contact_email', true );
					
					if( empty($contact) )
						return false;
						
					$o .= ( !empty($email) )
						? "<a href=\"mailto:{$email}\">{$contact}</a> "
						: $contact ;
					
					if( $phone = get_post_meta( get_the_ID(), 'job_contact_phone', true ) )
						$o .= 'by phone '.$phone;
						
					return $o;
				}
			);
			
			$o ='';
			
			// loop through items
			foreach( $items as $label => $meta ){
				
				if( !is_string($meta) )
					$var = $meta();
				else
					$var = get_post_meta( get_the_ID(), $meta, true );
				
				if(!empty($var))
					$o .= "<li><strong>{$label}:</strong> {$var}</li>";
			}
			
			$content = '<ul class="twd-job-items">'.$o.'</ul>'.$content;
		}
		
		return $content;
	}
	
	public function remove_meta_boxes()
	{
		// removes the type meta box.
		remove_meta_box( 'tagsdiv-jb_type', 'twd_job_post', 'side' );
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
