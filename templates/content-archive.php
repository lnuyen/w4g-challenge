<?php
/**
 * The template for displaying challenge_app application archive.
 */

?>

<div class="w4g-applications-archive">

	<?php 
	// the query
	$the_query = new WP_Query( array( 'post_type' => 'challenge_app', 'posts_per_page' => -1 ) ); ?>

	<?php if ( $the_query->have_posts() ) : ?>

		<!-- the loop -->
		<?php while ( $the_query->have_posts() ) : $the_query->the_post();

			echo '<div class="w4g-application">';

			the_title( sprintf( '<h2 class="w4g-application-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' );

			echo '<p class="w4g-application-meta"> Submitted ' . get_the_date() . ' by ' . get_the_author() . '</p>';

			echo '</div>';
			
		endwhile; ?>
		<!-- end of the loop -->

		<?php wp_reset_postdata(); ?>

	<?php else : ?>
		<p><?php _e( 'Sorry, no applications have been submitted just yet.' ); ?></p>
	<?php endif; ?>

</div>