<?php
/**
 * Contains template tags specific to the Inkblot theme.
 * 
 * @package Inkblot
 */

if ( ! function_exists('webcomic')) :
/**
 * Is a compatible version of Webcomic installed and active?
 * 
 * This function is actually part of the Webcomic plugin. If it doesn't exist,
 * it's probably safe to assume that Webcomic is not installed and active.
 * 
 * @return boolean
 */
function webcomic() {
	return false;
}
endif;

if ( ! function_exists('inkblot_page_description')) :
/**
 * Return appropriate `<meta>` description text.
 * 
 * @return string
 */
function inkblot_page_description() {
	$object = get_queried_object();
	$output = get_bloginfo('description', 'display');
	
	if (is_singular() and has_excerpt() and ! is_home()) {
		$output = get_the_excerpt();
	} else if ((is_category() or is_tag() or is_tax()) and $object->description) {
		$output = $object->description;
	} elseif (is_author() and $bio = get_user_meta($object->ID, 'description', true)) {
		$output = $bio;
	}
	
	if (140 < strlen($output = strip_tags($output))) {
		$output = substr($output, 0, 133) . '&#8230;';
	}
	
	return esc_attr($output);
}
endif;

if ( ! function_exists('inkblot_date_archive_title')) :
/**
 * Return the date archive title, formatted for Inkblot.
 * 
 * @return string
 */
function inkblot_date_archive_title($year = 'Y', $month = 'F Y', $date = '') {
	if (is_year()) {
		$date = $year;
	} else if (is_month()) {
		$date = $month;
	}
	
	return get_the_date($date);
}
endif;

if ( ! function_exists('inkblot_post_nav')) :
/**
 * Return post navigation.
 * 
 * Because there are no `get_` functions for these links we use
 * output buffering to store them in a varaiable.
 * 
 * @param mixed $class CSS classes or an array of classes to add to the <nav> element.
 * @param string $previous Previous post link text.
 * @param string $next Next post link text.
 * @param boolean $in_same_cat Whether the previous and next post must be within the same category as the current post.
 * @param string $excluded_categories List of categories to exclude for previous and next posts, like '1 and 2 and 3'.
 * @return string
 */
function inkblot_post_nav($class = '', $previous = '&laquo; %title', $next = '%title &raquo;', $in_same_cat = false, $excluded_categories = '') {
	if (get_adjacent_post(false, '', true) or get_adjacent_post(false, "", false)) {
		ob_start();
		
		previous_post_link('%link', $previous, $in_same_cat, $excluded_categories);
		next_post_link('%link', $next, $in_same_cat, $excluded_categories);
		
		$links = ob_get_contents();
		
		ob_end_clean();
		
		return sprintf('<nav class="%s">%s</nav>',
			implode(' ', array_merge(array('posts'), (array) $class)),
			$links
		);
	}
}
endif;

if ( ! function_exists('inkblot_posts_nav')) :
/**
 * Return posts paged navigation.
 * 
 * @param mixed $class CSS classes or an array of classes to add to the <nav> element.
 * @param array $args Arguments to pass to either `paginate_links` or `get_posts_nav_link`.
 * @param boolean $paged Whether to display paged navigation.
 * @return string
 */
function inkblot_posts_nav($class = '', $args = array(), $paged = false) {
	global $wp_query;

	if ($wp_query->max_num_pages > 1) {
		$class = array_merge(array('posts-paged'), (array) $class);
		
		return sprintf('<nav class="%s">%s</nav><!-- .posts-paged -->',
			implode(' ', (array) $class),
			$paged ? preg_replace('/>...</', '>&#8230;<', paginate_links(array_merge(array(
				'base' => str_replace(999999999, '%#%', get_pagenum_link(999999999)),
				'total' => $wp_query->max_num_pages,
				'format' => '?paged=%#%',
				'current' => max(1, get_query_var('paged'))
			), (array) $args)))
				   : get_posts_nav_link((array) $args)
		);
	}
}
endif;

if ( ! function_exists('inkblot_post_datetime')) :
/**
 * Return the post publish date and time, formatted for Inkblot.
 * 
 * @return string
 */
function inkblot_post_datetime() {
	return sprintf('<a href="%1$s" title="%2$s" rel="bookmark"><time datetime="%3$s">%4$s</time></a>',
		esc_url(get_permalink()),
		esc_attr(get_the_time()),
		esc_attr(get_the_date('c')),
		esc_html(get_the_date())
	);
}
endif;

if ( ! function_exists('inkblot_post_parent')) :
/**
 * Return a post parent link, formatted for Inkblot.
 * 
 * @return string
 */
function inkblot_post_parent() {
	if ($ancestors = get_post_ancestors(get_the_ID())) {
		$parent = current($ancestors);
		
		return sprintf('<a href="%1$s" title="%2$s" rel="gallery">%3$s</a>',
			esc_url(get_permalink($parent)),
			sprintf(__('Return to %1$s', 'inkblot'), esc_attr(strip_tags(get_the_title($parent)))),
			get_the_title($parent)
		);
	}
}
endif;

if ( ! function_exists('inkblot_image_details')) :
/**
 * Return image information for attachments, formatted for Inkblot.
 * 
 * @return string
 */
function inkblot_image_details() {
	if ($data = wp_get_attachment_metadata() and isset($data['width'], $data['height'])) {
		return sprintf('<a href="%1$s" title="%2$s" class="image">%3$s &#215; %4$s</a>',
			esc_url(wp_get_attachment_url()),
			__('View ful-size image', 'inkblot'),
			$data['width'],
			$data['height']
		);
	}
}
endif;

if ( ! function_exists('inkblot_start_comment')) :
/**
 * Render a comment.
 * 
 * @param object $comment Comment data object.
 * @param array $args Arguments passed to `wp_list_comments`.
 * @param integer $depth Depth of comment in reference to parents.
 * @return void
 */
function inkblot_start_comment($comment, $args, $depth) {
	$GLOBALS['comment'] = $comment; ?>
	
	<article id="comment-<?php comment_ID(); ?>" <?php comment_class(); ?>>
		<footer class="comment-footer">
			
			<?php
				print empty($args['avatar_size']) ? '' : get_avatar($comment, $args['avatar_size']);
				
				comment_author_link();
				
				printf(__('<a href="%1$s" class="time"><time datetime="%2$s">%3$s @ %4$s</time></a>', 'inkblot'),
					esc_url(get_comment_link($comment->comment_ID)),
					get_comment_time('c'),
					get_comment_date(),
					get_comment_time()
				);
				
				comment_reply_link(array_merge($args, array(
					'depth' => $depth,
					'max_depth' => $args['max_depth']
				)));
				
				edit_comment_link();
			?>
			
		</footer><!-- .comment-footer -->
		
		<?php if ( ! $comment->comment_approved) : ?>
			<div class="moderating-comment"><?php _e('Your comment is awaiting moderation.', 'inkblot'); ?></div><!-- .moderating-comment -->
		<?php endif; ?>
		
		<div class="comment-content"><?php comment_text(); ?></div><!-- .comment-content -->
		
	<?php
}
endif;

if ( ! function_exists('inkblot_end_comment')) :
/**
 * Render a comment closing tag.
 * 
 * @param object $comment Comment data object.
 * @param array $args Arguments passed to `wp_list_comments`.
 * @param integer $depth Depth of comment in reference to parents.
 * @return void
 */
function inkblot_end_comment($comment, $args, $depth) { ?>
	
	</article><!-- #comment-<?php comment_ID(); ?> -->
	
	<?php
}
endif;

if ( ! function_exists('inkblot_comments_nav')) :
/**
 * Return comments paged navigation.
 * 
 * @param mixed $class CSS classes or an array of classes to add to the <nav> element.
 * @param mixed $paged Arguments to pass to `paginate_comments_link`, or true to enable pagination with default arguments.
 * @param string $previous Label to use for the previous comments page link when not using paged navigation.
 * @param string $next Label to use for the next comments page link when not using paged navigation.
 * @return string
 */
function inkblot_comments_nav($class = '', $paged = array(), $previous = '', $next = '') {
	if (1 < get_comment_pages_count() and get_option('page_comments')) {
		$class = array_merge(array('comments-paged'), (array) $class);
		
		return sprintf('<nav class="%s">%s</nav><!-- .comments-paged -->',
			implode(' ', (array) $class),
			$paged ? paginate_comments_links(array_merge(array('echo' => false), (array) $paged))
				   : get_previous_comments_link($previous) . get_next_comments_link($next)
		);
	}
}
endif;

if ( ! function_exists('inkblot_count_widget')) :
/**
 * Return the number of widgets for the specified sidebar.
 * 
 * @param string $sidebar ID of the sidebar to count widgets for.
 * @param integer $default Default number of widgets for `$sidebar`.
 * @return integer
 */
function inkblot_count_widgets($sidebar, $default = 0) {
	$sidebar = "sidebar-{$sidebar}";
	$sidebars = get_option('sidebars_widgets');
	
	if (isset($sidebars[$sidebar]) and count($sidebars[$sidebar])) {
		return 10 < count($sidebars[$sidebar]) ? 10 : count($sidebars[$sidebar]);
	}
	
	return $default;
}
endif;

if ( ! function_exists('inkblot_widgetized')) :
/**
 * Render a generic widgetized area.
 * 
 * @param string $id ID of the widgetized area.
 * @return void
 * @uses inkblot_count_widgets()
 */
function inkblot_widgetized($id) {
	$widget = '';
	
	if ($count = inkblot_count_widgets($id) or is_customize_preview()) :
		ob_start(); ?>
			
			<div role="complementary" class="widgets <?php print $id; ?> columns-<?php print $count; ?>"><?php dynamic_sidebar($id); ?></div><!-- #<?php print $id; ?> -->
			
		<?php
		
		$widget = ob_get_contents();
		
		ob_end_clean();
	endif;
	
	return $widget;
}
endif;

if ( ! function_exists('inkblot_search_id')) :
/**
 * Return a unique search form ID.
 * 
 * @param boolean $add Increment the counter.
 * @return string
 */
function inkblot_search_id($add = true) {
	static $count = 0;
	
	if ($add) {
		$count++;
	}
	
	return "s{$count}";
}

endif;

if ( ! function_exists('inkblot_copyright')) :
/**
 * Return copyright notice.
 * 
 * @param integer $user User ID to use for the copyright attribution name.
 * @return string
 */
function inkblot_copyright($user = 0) {
	global $wpdb;
	
	$end = date('Y');
	$start = $wpdb->get_results("SELECT YEAR(min(post_date)) AS year FROM {$wpdb->posts} WHERE post_status = 'publish'");
	$author = get_userdata($user);
	
	return sprintf('&copy; %1$s %2$s',
		$start[0]->year === $end ? $end : $start[0]->year . '&ndash;' . $end,
		$author ? $author->display_name : get_bloginfo('name', 'display')
	);
}
endif;

if ( ! function_exists('inkblot_contributor')) :
/**
 * Return a contributor block for the contributor template.
 * 
 * @param integer $user User ID.
 * @param integer $avatar Size of the avatar to display.
 * @return string
 */
function inkblot_contributor($user, $avatar = 96) {
	ob_start(); ?>
	
	<div class="contributor">
		
		<?php if ($avatar) : ?>
			
			<div class="contributor-image">
				<?php echo get_avatar($user, (int) $avatar); ?>
			</div><!-- .contributor-image -->
			
		<?php endif; ?>
		
		<?php if ($post_count = count_user_posts($user)) : ?>
			<h2><a href="<?php print esc_url(get_author_posts_url($user)); ?>"><?php the_author_meta('display_name', $user); ?></a></h2>
		<?php else : ?>
			<h2><?php the_author_meta('display_name', $user); ?></h2>
		<?php endif; ?>
		
		<div class="contributor-description">
			<?php print wpautop(get_the_author_meta('description', $user)); ?>
		</div><!-- .contributor-description -->
		
	</div><!-- .contributor -->
	<?php
	
	$contributor = ob_get_contents();
	
	ob_end_clean();
	
	return $contributor;
}
endif;

if ( ! function_exists('inkblot_webcomic_transcript')) :
function inkblot_webcomic_transcript() { ?>
	<article id="webcomic_transcript-<?php the_ID(); ?>" <?php post_class(); ?>>
		<div class="post-content"><?php the_content(); ?></div><!-- .post-content -->
		<footer class="post-footer">
			
			<?php
				the_webcomic_transcript_authors(true, '<span class="webcomic-transcript-authors">', ', ', '</span>');
				
				the_webcomic_transcript_languages('<span class="webcomic-transcript-languages">', ', ', '</span>');
			?>
			
		</footer><!-- .post-footer -->
	</article><!-- #webcomic-transcript-<?php the_ID(); ?> -->
	<?php
}
endif;