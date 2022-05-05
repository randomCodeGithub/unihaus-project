<?php

/**
 * The Template for displaying products in a product category. Simply includes the archive template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/taxonomy-product-cat.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     4.7.0
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

// wc_get_template( 'archive-product.php' );

get_header();
?>
<?php
global $product;
$term = get_queried_object();

$featuredProduct = get_field('featured_product', $term);
// print_r($featuredProduct);
if ($featuredProduct) : ?>
	<div class="featured-product">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-lg-6">
					<h4><?php echo esc_html($featuredProduct->post_title); ?></h4>
					<?php
					if (!empty($featuredProduct->post_excerpt)) {
						echo '<p>' . $featuredProduct->post_excerpt . '</p>';
					}
					?>
					<a href="<?php echo get_permalink($featuredProduct->ID); ?>" class="uh-btn d-inline-block text-decor-none"><?php _e('Uzzināt vairāk', 'unihaus'); ?></a>
				</div>
				<?php if (has_post_thumbnail($featuredProduct)) : ?>
					<div class="col-lg-6">
						<?php $image = wp_get_attachment_image_src(get_post_thumbnail_id($featuredProduct->ID), 'single-post-thumbnail'); ?>

						<img src="<?php echo $image[0]; ?>">
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>

<?php endif; ?>
<?php
$category = get_queried_object();
get_template_part('template-parts/breadcrumb');

$ProductTagline = get_field('product', $term);
$args = array('title' => $category->name, 'tagline' => $ProductTagline['tagline'], 'is_first_heading' => true);
?>
<div class="container">
	<?php get_template_part('template-parts/title-and-tagline', null, $args); ?>

</div>

<?php global $wp_query; ?>

<div class="container product-category-list">
	<div class="product-count d-flex flex-column d-lg-block">
		<?php
		if ($wp_query->found_posts > 0) {
			$productCountName = ($wp_query->found_posts > 1) ? __('produkti', 'unihaus') : __('produkts', 'unihaus');
			echo $wp_query->found_posts . ' ' . $productCountName;
		} else {
			echo '0' . ' ' . __('produkti', 'unihaus');
		}
		echo woocommerce_catalog_ordering();
		?>
	</div>
	<div class="row">
		<div class="col-category">
			<!-- START -->

			<?php
			$categories = get_field('categories', 'option');
			if ($categories) :
			?>

				<div class="woof_sid woof_sid_auto_shortcode">
					<div class="woof_container woof_container_checkbox">
						<div class="woof_container_overlay_item"></div>
						<div class="woof_container_inner">
							<h4>
								Palīglīdzekļa veids <a href="javascript: void(0);" title="toggle" class="woof_front_toggle woof_front_toggle_opened" data-condition="opened">
									<img src="http://unihaus.sem.lv/wp-content/plugins/woocommerce-products-filter/img/minus3.png" alt="toggle">
								</a>
							</h4>
							<div class="woof_block_html_items">
								<ul class="woof_list woof_list_checkbox">

									<?php if (have_rows('custom_filter_by_attribute', 'option')) : ?>
										<?php while (have_rows('custom_filter_by_attribute', 'option')) : the_row();
											$categoryPage = get_page_by_path('paliglidzekli');
											$attribute = get_sub_field('attribute');
											$category_for_attribute = get_sub_field('category_for_attribute');
											$attrbute_relationships = get_sub_field('attrbute_relationships');

											$relationship_attribute_slugs = '';
											foreach ($attrbute_relationships as $attrbute_relationship) {
												$after_slug = (end($attrbute_relationships) === $attrbute_relationship) ? '' : ',';

												$relationship_attribute_slugs .= $attrbute_relationship->slug . $after_slug;
											}
											$attribute_link = get_permalink($categoryPage) . $category_for_attribute->slug . '/?' . $attribute->taxonomy . '=' . $attribute->slug . '&' . $attrbute_relationships[0]->taxonomy . '=' . $relationship_attribute_slugs;
										?>
											<li>
												<input type="checkbox" id="<?php echo $attribute->slug ?>" name="<?php echo  $attribute->taxonomy ?>[]" value="<?php echo $attribute->slug ?>" <?php if (strpos($_SERVER['REQUEST_URI'], $attribute->slug) !== false) echo "checked";
																																																if (strpos($_SERVER['REQUEST_URI'], $attribute->slug) === false) echo 'data-href="' . $attribute_link . '"'; ?> class="woof_checkbox_term_custom woof_checkbox_term_attribute" value="">
												<label class="woof_checkbox_label " for="<?php echo $attribute->slug ?>"><?php echo $attribute->name ?></label>

											</li>
										<?php endwhile; ?>
									<?php endif; ?>
									<?php foreach ($categories as $category) : ?>
										<li>
											<input type="checkbox" id="<?php echo $category->slug ?>" <?php if (strpos($_SERVER['REQUEST_URI'], $category->slug) !== false) echo "checked";
																										if (strpos($_SERVER['REQUEST_URI'], $category->slug) === false) echo 'data-href="' . get_term_link($category) . '"'; ?> class="woof_checkbox_term_custom woof_checkbox_term_category" value="">
											<label class="woof_checkbox_label " for="<?php echo $category->slug ?>"><?php echo $category->name ?></label>

										</li>
									<?php endforeach; ?>
								</ul>
							</div>

						</div>
					</div>
				</div>
			<?php endif;
			?>
			<!-- END -->

			<div>
				<?php echo do_shortcode('[woof sid="auto_shortcode" autohide=0]'); ?>
			</div>

			<?php if (is_active_sidebar($category->term_id . '_sidebar')) {
				dynamic_sidebar($category->term_id . '_sidebar');
			} ?>
		</div>
		<div class="col-products">
			<?php
			if (woocommerce_product_loop()) {

				woocommerce_product_loop_start();

				if (wc_get_loop_prop('total')) {
			?>
					<div class="row" data-lm-wrap="products">
						<?php
						while (have_posts()) {

							the_post();

							/**
							 * Hook: woocommerce_shop_loop.
							 */
							do_action('woocommerce_shop_loop');

							wc_get_template_part('content', 'product');
						} ?>
					</div>
					<?php
					$currentPage = (get_query_var('paged')) ? get_query_var('paged') : 1;
					if ($currentPage != $wp_query->max_num_pages) : ?>
						<div class="text-center">
							<button class="uh-btn js-pdg-load-more" data-lm-id="products">
								<?php _e('Ielādēt vēl', 'unihaus'); ?>
							</button>
						</div>
						<script>
							var pdg_load_more = {
								'products': {
									args: '<?php echo json_encode($wp_query->query_vars); ?>',
									page: <?php echo (get_query_var('paged')) ? get_query_var('paged') : 1; ?>,
									max: <?php echo $wp_query->max_num_pages; ?>,
									lang: '<?php echo ICL_LANGUAGE_CODE; ?>',
									tpl: 'template-parts/product'
								}
							};
						</script>
					<?php endif; ?>
			<?php
				}

				woocommerce_product_loop_end();

				/**
				 * Hook: woocommerce_after_shop_loop.
				 *
				 * @hooked woocommerce_pagination - 10
				 */
				do_action('woocommerce_after_shop_loop');
			} else {
				/**
				 * Hook: woocommerce_no_products_found.
				 *
				 * @hooked wc_no_products_found - 10
				 */
				do_action('woocommerce_no_products_found');
			}
			?>
		</div>
	</div>
</div>
<?php
do_action('woocommerce_after_main_content');

get_footer();
