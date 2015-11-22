<?php 


// bringing CMB2 form to frontend 
############################################
add_action('init', 'love_porinite');
function love_porinite(){
	$args = array(
		'public'		=> true,
		'label'			=> 'Porinite',
		'taxonomies'	=> array('category', 'post_tag')
	);
	register_post_type('porinite', $args);
}


add_action('cmb2_init', 'front_end_metabox');
function front_end_metabox(){
	$prefix = '_frontend_';
	$cmb = new_cmb2_box(array(
		'id'		=> $prefix.'form',
		'title'		=> __('Post Info', 'sexy'),
		'object_types'	=> array('porinite'),
		'context'		=> 'normal',
		'save_fields'  => false,
		'priority'		=> 'default'
	));

	$cmb->add_field(array(
		'name'			=> __('Name', 'sexy'),
		'id'			=> $prefix.'name',
		'type'			=> 'text',
		// 'repeatable'	=> true
	));

	$cmb->add_field(array(
		'name'			=> __('Email', 'sexy'),
		'id'			=> $prefix.'email',
		'type'			=> 'text_email',
		// 'repeatable'	=> true
	));
	
	$cmb->add_field(array(
		'name'			=> __('Phone Number', 'sexy'),
		'id'			=> $prefix.'phone',
		'type'			=> 'text',
		// 'repeatable'	=> true
	));
	
}
// end of the function front_end_metabox

/*
*Handle the cmb-frontend form shortcode
*/
add_shortcode('cmb-frontend-form', 'frontend_form_shortcode');
function frontend_form_shortcode($atts = array()){
	// current user
	$user_id = get_current_user_id();

	// user ID of metabox in front_end_metabox
	$metabox_id = '_frontend_form';

	// since post ID will not exist yet, just need to pass it
	$object_id = 'fake_object_id';

	// get cmb2 metabox object
	$cmb = cmb2_get_metabox($metabox_id, $object_id);

	// get $cmb object_type
	$post_types = $cmb->prop('object_types');

	$atts = shortcode_atts(array(
		'post_author'		=> $user_id ? $user_id : 1,
		'post_status'		=> 'pending',
		'post_type'			=> reset($post_types),
	),$atts, 'cmb-frontend-form');
	foreach ( $atts as $key => $value ) {
		$cmb->add_hidden_field( array(
			'field_args'  => array(
				'id'    => "atts[$key]",
				'type'  => 'hidden',
				'default' => $value,
			),
		) );
	}
	// Initiate our output variable
	$output = '';

	// Get any submission errors
	if ( ( $error = $cmb->prop( 'submission_error' ) ) && is_wp_error( $error ) ) {
		// If there was an error with the submission, add it to our ouput.
		$output .= '<h3>' . sprintf( __( 'There was an error in the submission: %s', 'sexy' ), '<strong>'. $error->get_error_message() .'</strong>' ) . '</h3>';
	}
	// If the post was submitted successfully, notify the user.
	if ( isset( $_GET['post_submitted'] ) && ( $post = get_post( absint( $_GET['post_submitted'] ) ) ) ) {
		// Get submitter's name
		$name = get_post_meta( $post->ID, '_frontend_name', 1 );
		$name = $name ? ' '. $name : '';
		// Add notice of submission to our output
		$output .= '<h3>' . sprintf( __( 'Thank you%s, your new post has been submitted and is pending review by a site administrator.', 'sexy' ), esc_html( $name ) ) . '</h3>';
	}
	// Get our form
	$output .= cmb2_get_metabox_form( $cmb, 'fake_object_id', array( 'save_button' => __( 'Submit Post', 'sexy' ) ) );
	return $output;
}

// Handles form submission on save

function frontend_form_handle($cmb, $post_data = array()){
	
	// If no form submission, bail
	if ( empty( $_POST ) || ! isset( $_POST['submit-cmb'], $_POST['object_id'] ) ) {
		return false;
	}

	// check required $_POST variable and security nonce
	if(
		!isset($_POST['submit-cmb'], $_POST['object_id'], $_POST[$cmb->nonce()]) 
		|| ! wp_verify_nonce($_POST[$cmb->nonce()], $cmb->nonce)
	){
		return new WP_Error( 'security_fail', __('Security check failed.') );
	}

	if( empty( $_POST['_frontend_name'] ) ){
		return new WP_Error('post_data_missing', __('New post requires a title'));
	}
	// Do wordpress insert_post stuff

	// fetch sanitized values
	$sanitized_values = $cmb->get_sanitized_values($_POST);

	// set our post data arguments
	$post_data['post_title']	= $sanitized_values['_frontend_name'];
	unset($sanitized_values['_frontend_name']);

	$new_submission_id = wp_insert_post($post_data, true);

	// if we hit a snag, update the user
	if( is_wp_error($new_submission_id)){
		return $new_submission_id;
	}

	unset( $post_data['post_type'] );
    unset( $post_data['post_status'] );

    foreach ($sanitized_values as $key => $value) {
    	update_post_meta($new_submission_id, $key, $value);
    }

	return $new_submission_id;
}
