<?php

/**
 * Register widget areas.
 */
function pdgc_register_widget_areas() {

	/**
	 * Register widget areas for each end level
	 * product category.
	 */
	$cats = get_terms( array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => false
	) );

	if ( ! is_wp_error( $cats ) && $cats ) {
		foreach ( $cats as $cat ) {
            register_sidebar( array(
                'name'          => $cat->name,
                'id'            => $cat->term_id . '_sidebar',
                'before_widget' => '<div class="shop-sidebar-widget">',
                'after_widget'  => '</div>',
                'before_title'  => '<h3>',
                'after_title'   => '</h3>',
            ) );
		}
	}

}
add_action( 'init', 'pdgc_register_widget_areas' );

/**
 * Register widget ( using ACF blocks ).
 */
function pdgc_register_widgets() {

    if ( function_exists( 'acf_register_block_type' ) ) {
        acf_register_block_type( [
            'name'            => 'slider-filter',
            'title'           => 'Slider Filter',
            'mode'            => 'edit',
            'render_template' => 'template-parts/widgets/slider-filter/index.php',
            'enqueue_assets'  => function() {
                wp_enqueue_script( 'range-slider', PDGC_URL . '/template-parts/widgets/slider-filter/assets/vendor/range-slider/jquery-ui.min.js', ['jquery'], null, true );
                wp_enqueue_script( 'range-slider-init', PDGC_URL . '/template-parts/widgets/slider-filter/assets/js/init.js', ['jquery'], null, true );

                wp_enqueue_style( 'range-slider', PDGC_URL . '/template-parts/widgets/slider-filter/assets/vendor/range-slider/jquery-ui.min.css' );
                wp_enqueue_style( 'range-slider-structure', PDGC_URL . '/template-parts/widgets/slider-filter/assets/vendor/range-slider/jquery-ui.structure.min.css' );
                wp_enqueue_style( 'range-slider-style', PDGC_URL . '/template-parts/widgets/slider-filter/assets/css/style.css' );
            }
        ] );
    }

}
add_action( 'acf/init', 'pdgc_register_widgets' );

/**
 * Populate attribute picker field choices.
 */
function pdgc_attribute_picker_field( $field ) {

    $field['choices'] = [];

    if ( ! function_exists( 'wc_get_attribute_taxonomies' ) ) {
        return $field;
    }

    $attributes = wc_get_attribute_taxonomies();

    if ( $attributes ) {
        foreach ( $attributes as $attribute ) {
            $field['choices']['pa_' . $attribute->attribute_name] = $attribute->attribute_label;
        }
    }

    return $field;

}
add_filter( 'acf/load_field/name=attribute', 'pdgc_attribute_picker_field' );

/**
 * Filter products based on range slider values.
 */
function pdgc_range_slider_filter( $query ) {

    if ( ! is_admin() && $query->is_main_query() ) {
        if ( ! empty( $_GET ) ) {
            foreach ( $_GET as $key => $value ) {
                if ( strpos( $key, 'rng_' ) === 0 ) {
                    $range = explode( ',', $value );
                    $tax   = str_replace( 'rng_', '', $key );
                    $min   = floatval( htmlspecialchars( $range[0] ) );
                    $max   = floatval( htmlspecialchars( $range[1] ) );

                    $range = range( $min, $max, 0.1 );

                    $terms = get_terms( [
                        'taxonomy'   => $tax,
                        'hide_empty' => false
                    ] );

                    if ( ! is_wp_error( $terms ) && $terms ) {
                        $ids = [];

                        foreach ( $terms as $term ) {
                            if ( strpos( $term->name, '-' ) !== false ) {
                                $parts = explode( '-', $term->name );

                                $value_1 = (int) $parts[0];
                                $value_2 = (int) $parts[1];

                                $value_range = range( $value_1, $value_2, 0.1 );

                                if ( in_array( $value_1, $range ) || in_array( $value_2, $range ) || array_intersect( $value_range, $range ) ) {
                                    $ids[] = $term->term_id;
                                }
                            } else {
                                $value = (int) $term->name;

                                if ( in_array( $value, $range ) ) {
                                    $ids[] = $term->term_id;
                                }
                            }
                        }

                        if ( $ids ) {
                            $query->tax_query->queries[] = array(
                                'taxonomy' => $tax,
                                'terms'    => $ids
                            );
                            $query->query_vars['tax_query'] = $query->tax_query->queries;
                        }
                    }
                }
            }
        }
    }

}
add_action( 'pre_get_posts', 'pdgc_range_slider_filter' );