<?php
/**
 * The template for displaying challenge_app application
 */

?>

<div class="w4g-application-form">

		<?php // Challenge Application Options

		// Application ACF ID
		$acfID = 'acf_application';
		$acfExtraFields = get_option( 'custom_fields_id' );

		// Challenge Status
		$status = get_option( 'challenge_status'); // options: 'active' or 'inactive'
		$challengeStatus = $status[0];

		// Messages
		$messageInactive 	= get_option( 'challenge_information');
		$messageActive 		= get_option( 'challenge_instructions');
		$messageLoggedOut 	= get_option( 'challenge_logged_out_message');
		$messageEnded		= get_option( 'challenge_ended_message');

		?>

		<p class="w4g-application-alert" style="display:none">
			<?php // Challenge Status

				switch( $challengeStatus ){
					case 'active':
						echo 'Challenge is active';
						break;
					case 'inactive':
						echo 'Challenge is inactive';
						break;
					case 'ended':
						echo 'Challenge is closed';
						break;
				}

			?>
		</p>

		<?php if ( $challengeStatus == 'inactive' ): ?>

			<p><?php echo $messageInactive; ?></p>

		<?php elseif ( $challengeStatus == 'active' ): ?>

			<?php if ( is_user_logged_in() ): ?>

				<p><?php echo $messageActive; ?></p>

				<?php

					$new_post = array(
						'post_id'			 => 'new',
						'post_type'    		 => 'challenge_app',
						'field_groups'  	 => array($acfID,$acfExtraFields), // Create post field group ID(s)
						'return'        	 => '%post_url%', // Redirect to new post url
						'submit_value'       => 'Submit',
						'updated_message'    => 'Saved!',
						'form_attributes' 	 => array('autocomplete' => "off"),
					);

					acf_form( $new_post );
				
				?>

			<?php else: ?>

				<p><?php echo $messageLoggedOut; ?></p>
				<p>Please <a href="<?php echo wp_registration_url(); ?>" title="Register">register</a> or <a href="<?php echo wp_login_url( get_permalink() ); ?> " title="Login">login</a> in order to submit an application.</p>

			<?php endif; ?>

		<?php elseif ( $challengeStatus == 'ended' ): ?>

			<p><?php echo $messageEnded; ?></p>			

		<?php endif; ?>

</div>

<script type="text/javascript">

	jQuery('.acf-form').trigger("reset");

</script>


