<?php /* Template Name: Course */ ?>

<?php get_header(); ?>

	<div id="page">
	<?php if (have_posts()) : ?>
		<section class="content">
		<?php while (have_posts()) : the_post(); ?>

			<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
				<h2><?php the_title(); ?></h2>

				<div class="entry">
					<?php the_content('Read the rest of this entry &raquo;'); ?>
				</div>
			</div>

		<?php endwhile; 
		
		$assns= prof_get_assignments($post->ID); 
		
		if($assns){
		
		?>
		<div class="widget">
			<h3>Assignments</h3>
			<ul>
			
			<?php
					foreach ($assns as $a) :
						$a_info= get_post_custom($a->ID);
						
			?>
						<li><a href="<?php print get_permalink($a->ID); ?>"><?php print $a->post_title; ?></a> <strong>Due:</strong> <?php print $a_info['duedate'][0]; ?></li>
			
			<?php
						
					endforeach;
			?>
			</ul>
		</div>
		<?php } ?>
		
		</section>
	<?php endif; ?>
	
	<?php get_sidebar('course'); ?>
	</div>

<?php get_footer(); ?>
