<?php
/**
 * Webcomic content template.
 * 
 * This is the content template used for Webcomic posts. It's almost identical
 * to the standard content template used for other posts, except for the
 * inclusion of a webcomic preview for search and archive pages. To change the
 * actual full webcomic display see the `/webcomic/image.php` template file.
 * 
 * @package Inkblot
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	
	<?php if ((is_search() or is_archive()) and get_theme_mod('webcomic_archive_size', 'large')) : ?>
		
		<div class="post-webcomic">
			<div class="webcomic-image">
				
				<?php the_webcomic(get_theme_mod('webcomic_archive_size', 'large'), 'self'); ?>
				
			</div><!-- .webcomic-image -->
		</div><!-- .post-webcomic -->
		
	<?php endif; ?>
	
	<?php if (has_post_thumbnail()) : ?>
		<div class="post-image"><?php the_post_thumbnail(); ?></div><!-- .post-image -->
	<?php endif; ?>
	
	<header class="post-header">
		
		<?php if (is_single()) : ?>
			<h1><?php the_title(); ?></h1>
		<?php else : ?>
			<h1><a href="<?php the_permalink(); ?>" title="<?php print esc_attr(sprintf(__('Permalink to %s', 'inkblot'), the_title_attribute('echo=0'))); ?>" rel="bookmark"><?php the_title(); ?></a></h1>
		<?php endif; ?>
		
		<div class="post-details">
			<?php
				print inkblot_post_datetime();
				
				the_author_posts_link();
				
				if ( ! post_password_required() and (comments_open() or get_comments_number())) :
					comments_popup_link();
				endif;
				
				if (webcomic_prints_available()) :
					printf('<a href="%1$s">%2$s</a>',
						add_query_arg(array('prints' => ''), get_permalink()),
						__('Purchase', 'inkblot')
					);
				endif;
				
				edit_post_link();
			?>
		</div>
	</header><!-- .post-header -->
	
	<?php if (is_search()) : ?>
		<div class="post-excerpt"><?php the_excerpt(); ?></div><!-- .post-excerpt -->
	<?php else : ?>
		
		<div class="post-content">
			<?php
				the_content();
				
				wp_link_pages(array(
					'before' => '<nav class="post-pages">',
					'after' => '</nav>'
				));
			?>
		</div>
		
	<?php endif; ?>
	
	<footer class="post-footer">
		
		<?php
			the_terms(get_the_ID(), 'category', '<span class="post-categories">', __(', ', 'inkblot'), '</span>');
			
			the_tags('<span class="post-tags">', __(', ', 'inkblot'), '</span>');
			
			print WebcomicTag::get_the_webcomic_collection_list(0, '<span class="webcomic-collections">', __(', ', 'inkblot'), '</span>');
			
			print WebcomicTag::get_the_webcomic_term_list(0, 'storyline', '<span class="webcomic-storylines">', __(', ', 'inkblot'), '</span>');
			
			print WebcomicTag::get_the_webcomic_term_list(0, 'character', '<span class="webcomic-characters">', __(', ', 'inkblot'), '</span>');
		?>
		
	</footer><!-- .post-footer -->
	
</article><!-- #post-<?php the_ID(); ?> -->