<?php
/**
 * The template for displaying challenge_app dashboard
 */

 	// Challenge Application Options

	// Challenge Status
	$status = get_option( 'challenge_status'); // options: 'active', 'inactive', 'ended'
	$challengeStatus = $status[0];

	// Editing Capabilities
	$editCaps = get_option( 'challenge_edit' ); // options: 'allow', 'deny'
	$editCap  = $editCaps[0];

?>

<div class="w4g-dashboard">

	<?php 

	if (isset($_GET['delete'])) { // ADD ALERT HERE THAT POST IS SUCCESSFULLY CREATED !
	    if ( $_GET['delete'] == 'success' ) {
	    	echo '<p class="w4g-application-alert">Your application has been deleted.</p>';
	    }
	}

	global $user_ID;
	// the query
	$the_query = new WP_Query( array( 'post_type' => 'challenge_app', 'posts_per_page' => -1, 'author' => $user_ID ) ); ?>

	<?php if ( $the_query->have_posts() ) : ?>

		<!-- the loop -->
		<?php while ( $the_query->have_posts() ) : $the_query->the_post();

			echo '<div class="w4g-application">';

			the_title( sprintf( '<h2 class="w4g-application-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' );

			echo '<p class="w4g-application-meta"> Submitted ' . get_the_date() . '</p>';

			if ( $challengeStatus == 'active' ) {

				if ( $editCap == 'allow' ) {

					echo '<p class="w4g-application-meta"><a href="' . esc_url( get_permalink() ) . '">Edit</a>';

				} else {

					 echo '<p class="w4g-application-meta">';
				}

				$nonce = wp_create_nonce('my_delete_post_nonce'); ?>

				<a href="<?php echo admin_url( 'admin-ajax.php?action=my_delete_post&id=' . get_the_ID() . '&nonce=' . $nonce ) ?>" data-id="<?php the_ID() ?>" data-nonce="<?php echo $nonce ?>" class="delete-post" onClick="return confirm('Are you sure you want to delete this application?')">Delete</a></p>

			<?php }

			echo '</div>';
			
		endwhile; ?>
		<!-- end of the loop -->

		<?php wp_reset_postdata(); ?>

	<?php else : ?>

		<?php if ( $challengeStatus == 'active' ) {

			echo '<p>Hello there! Submit your first application <a href="' . esc_url( home_url( '/' ) ) . 'application">here</a>.</p>';

		} else {

			echo '<p>Hello there! The <a href="' . esc_url( home_url( '/' ) ) . 'application">application</a> is currently closed.</p>';

		} ?>
	
	<?php endif; ?>

</div>