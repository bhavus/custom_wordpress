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
		<h4>Discuss <?php the_title(); ?></h4>
			<?php comments_template(); ?>

		<?php endwhile; ?>
		
		</section>
	<?php endif; ?>
	
	<?php get_sidebar('assn'); ?>
	</div>

<?php get_footer(); ?>
