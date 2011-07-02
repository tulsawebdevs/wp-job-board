<?php

/**
 * Outputs the current type of job
 *
 * @return void
 **/
function twd_jobs_type()
{
  
}


/**
 * Template Functions
 *
 * @package twd-jobs
 * @author Patrick Forringer 
 **/
class twd_jobs_tmpl
{
  
  static function get_job_type()
  {
    return get_the_terms( get_the_ID(), 'jb_type' );
  }
  
  static function get_jobs_template( $context = null ) {
		
		$type = false;
		
		// It would be cool to switch on post formay, but submitting a new job via gravity forms would be harder.
    // if( function_exists('get_post_format') )
    //  $type = (string) get_post_format( get_the_ID() );
		
		$type = self::get_job_type();
		
		print_r($type);
		
		do_action( "get_job_template", $type, $context );
	
		$templates = array();
		
		if ( !empty($type) ){
		
			if(is_array($context)){
				foreach($context as $con)
				$templates[] = "type-job-{$type}-{$con}.php";
			}else{
				$templates[] = "type-job-{$type}-{$context}.php";
			}
		
			$templates[] = "type-job-{$type}.php";
		}
		
		if(is_array($context)){
			foreach($context as $con)
			$templates[] = "type-job-{$con}.php";
		}else{
			$templates[] = "type-job-{$context}.php";
		}
		
		$templates[] = "type-job.php";
		print_r($templates);
		$located = locate_template( $templates, false, false );
		
		if( empty($located) && file_exists ( TWD_JB_DIR .'/theme/type-job.php' )){
		  $located =  TWD_JB_DIR .'/theme/type-job.php';
		}
		
		if ($located)
  		load_template( $located, false );
		
	}
} // END tmpl class 