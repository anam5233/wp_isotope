<?php 
	
	add_filter( 'attachments_setting_screen', '__return_false');
	add_filter( 'attachments_default_instance', '__return_false' );
	

function structure_of_parinite($attachments){
		$parts = array(
			array(
				'name'	=> 'title', 
				'type'	=> 'text', 
				'label'	=> __('Title', 'attachments'),
				'default'	=> 'title'
			),
			array(
				'name'	=> 'tags', 
				'type'	=> 'text', 
				'label'	=> __('Tags', 'attachments'),
				
			),
			
		);

		$args = array(

		// title of the meta box (string)
			'label'         => 'Portfolio Images',

		// all post types to utilize (string|array)
			'post_type'     => array( 'page' ),

		// meta box position (string) (normal, side or advanced)
			'position'      => 'normal',

		// meta box priority (string) (high, default, low, core)
			'priority'      => 'high',

		// allowed file type(s) (array) (image|video|text|audio|application)
			'filetype'      => null,  // no filetype limit

		// include a note within the meta box (string)
			'note'          => 'Attach files here!',

		// by default new Attachments will be appended to the list
		// but you can have then prepend if you set this to false
			'append'        => true,

		// text for 'Attach' button in meta box (string)
			'button_text'   => __( 'Attach Image', 'attachments' ),

		// text for modal 'Attach' button (string)
			'modal_text'    => __( 'Attach', 'attachments' ),

		// which tab should be the default in the modal (string) (browse|upload)
			'router'        => 'browse',

		// whether Attachments should set 'Uploaded to' (if not already set)
			'post_parent'   => false,

		// fields array
			'fields'        => $parts,

	);

	$attachments->register('sexy_parinite', $args);
}
add_action( 'attachments_register', 'structure_of_parinite' );
