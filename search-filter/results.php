<?php
/**
 * Search & Filter Pro
 *
 * Sample Results Template
 *
 * @package   Search_Filter
 * @author    Ross Morsali
 * @link      https://searchandfilter.com
 * @copyright 2018 Search & Filter
 *
 * Note: these templates are not full page templates, rather
 * just an encaspulation of the your results loop which should
 * be inserted in to other pages by using a shortcode - think
 * of it as a template part
 *
 * This template is an absolute base example showing you what
 * you can do, for more customisation see the WordPress docs
 * and using template tags -
 *
 * http://codex.wordpress.org/Template_Tags
 *
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( $query->have_posts() )
{
	?>

	Found <?php echo $query->found_posts; ?> Results<br />
	Page <?php echo $query->query['paged']; ?> of <?php echo $query->max_num_pages; ?><br />
	<?php
	while ($query->have_posts())
	{
		$query->the_post();
		$resources = pods( 'resource', get_the_ID() );
		$resource_link = get_the_permalink();
		$viewable = 'Viewable Content';
		if( $resources->display( 'file' ) && $resources->display('download_only') == true) {
			$resource_link = $resources->display( 'file' );
			$viewable = 'Downloadable File';
		}

		$terms = get_the_terms( $query->ID, 'resource_category' );
		$term_names = join(', ', wp_list_pluck($terms, 'name'));

		?>
		<div <?php post_class( 'search-filter-item' ); ?>>
			<h2 class="search-filter-item__title"><a href="<?= $resource_link; ?>"><?php the_title(); ?></a></h2>
			<p class="search-filter-item__date"><small>Published Date: <?= get_the_date(); ?></small></p>
			<div class="search-filter-item__content">
				<p><br /><?php the_excerpt(); ?></p>
				<?php
				if ( has_post_thumbnail() ) {
					echo '<p>';
					the_post_thumbnail("small");
					echo '</p>';
				}
			?>
			</div>


			<p class="search-filter-item__categories">Topics: <?= $term_names; ?></p>
			<p class="search-filter-item__download"><a href="<?= $resource_link; ?>"><?= $viewable; ?></a></p>

		</div>
		<?php
	}
	?>
	<div class="pagination pagination--search-filter">
		<?php
			/* example code for using the wp_pagenavi plugin */
			if (function_exists('wp_pagenavi'))
			{
				echo "<br />";
				wp_pagenavi( array( 'query' => $query ) );
			}
		?>
	</div>
	<?php
}
else
{
	echo "No Results Found";
}
?>