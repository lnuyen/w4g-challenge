<?php
/**
 * The template for displaying challenge_app single application
 */

// Challenge Options

// Application ACF ID
$acfID = 'acf_application';
$acfExtraFields = get_option( 'custom_fields_id' );

// Get Author & Visitor IDs
global $post;
$author  = $post->post_author;
$visitor = get_current_user_id();

// Challenge Status
$status = get_option( 'challenge_status' ); // options: 'active', 'inactive', 'ended'
$challengeStatus = $status[0];

// Editing Capabilities
$editCaps = get_option( 'challenge_edit' ); // options: 'allow', 'deny'
$editCap  = $editCaps[0];

?>

<div class="w4g-application-single">

	<?php if (isset($_GET['status'])) { // ADD ALERT HERE THAT POST IS SUCCESSFULLY CREATED !
		    if ( $_GET['status'] == 'created' ) {
		    	echo '<p class="w4g-application-alert">Your application has been submitted!</p>';
		    }
		} 

	if ( $challengeStatus == 'active' && $editCap == 'allow' && $author == $visitor ) { // Show Editable Fields to Author

		if (isset($_GET['updated'])) { // ADD ALERT HERE THAT POST IS SUCCESSFULLY UPDATED !
		    if ( $_GET['updated'] == 'true' ) {
		    	echo '<p class="w4g-application-alert">Your application has been updated!</p>';
		    }
		}

		$edit_post = array(
			'post_id'            => get_the_ID(), // Get the post ID
			'field_groups'  	 => array($acfID,$acfExtraFields), // Create post field group ID(s)
			'html_before_fields' => '',
			'html_after_fields'  => '',
			'submit_value'       => 'Save Changes',
			'updated_message'    => __("Post updated", 'acf'),
			'form_attributes' 	 => array('autocomplete' => "off"),
		);
		acf_form( $edit_post );

		// Delete Post
		$nonce = wp_create_nonce('my_delete_post_nonce'); ?>

		<p class="w4g-application-meta">
			<a href="<?php echo admin_url( 'admin-ajax.php?action=my_delete_post&id=' . get_the_ID() . '&nonce=' . $nonce ) ?>" data-id="<?php the_ID() ?>" data-nonce="<?php echo $nonce ?>" class="delete-post" onClick="return confirm('Are you sure you want to delete this application?')">Delete</a>
		</p>

		<?php

	} else { // Show Field Values only to Admin

		// Print All Fields

		/*
		*  get all custom fields and dump for testing
		*/

		//$fields = get_field_objects();
		//var_dump( $fields );

		//echo '<pre>';
			//var_dump( $field );
		//echo '</pre>';

		/*
		*  get all custom fields, loop through them and load the field object to create a label => value markup
		*/

		$fields = get_field_objects();

		if( $fields )
		{
			foreach( $fields as $field_name => $field )
			{
				if( $field['value'] ) {

					echo '<div style="margin-bottom:1em;">';
						echo '<h3>' . $field['label'] . '</h3>';

						if( $field['type'] == 'file' ) {

							$title  = $field['value']['title'];
							$url	= $field['value']['url']; 
							$type	= $field['value']['mime_type'];
							$needle = 'image';
							$pos 	= strpos($type,$needle);

							if( !current_user_can( 'manage_options' ) ) { // visitor is NOT admin (include file preview/title only)
								
								if($pos === false) { 
									echo $title;
								}
								else {
									echo '<img src="'. $url .'" style="height:auto; width:250px;" />';
								}

							} else { // visitor is admin (include link to file)
								
								if($pos === false) {
									echo '<a href="'. $url .'" target="_blank">'. $title .'</a>';
								}
								else {
									echo '<a href="'. $url .'" target="_blank"><img src="'. $url .'" style="height:auto; width:250px;" /></a>';
								}
							
							}

						} elseif( $field['type'] == 'select' ) {

							$options = $field['value'];

							if ( is_array($options) ) {

								echo implode(', ', $options);

							} else {

								echo '<p>' . $field['value'] . '</p>';

							}

						} elseif( $field['type'] == 'checkbox' ) {

							$options = $field['value'];

							echo implode(', ', $options);

						} elseif( $field['type'] == 'true_false' ) {

							$bool    = $field['value'];
							$answer  = boolval($bool) ? 'true' : 'false';
							$message = $field['message'];

							echo '<p>' . $message . '&nbsp; (' . $answer . ')</p>';

						} else {
							
							echo '<p>' . $field['value'] . '</p>';
							
						}

					echo '</div>';

				}
			}
		}

		//echo '<br><pre>Author ID: ' . $author . ' Visitor ID: ' . $visitor . '</pre>';

	}

	?><!-- end Print All Fields ! -->

</div>