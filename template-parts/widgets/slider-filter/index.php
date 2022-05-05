<?php if ( is_admin() ) return;

if ( $attribute = get_field( 'attribute' ) ) {
    $terms = get_terms( [
        'taxonomy'   => $attribute,
        'hide_empty' => false
    ] );

    $values = [];
    $min = 0;
    $max = 9999;

    if ( ! is_wp_error( $terms ) && $terms ) {
        foreach ( $terms as $term ) {
            if ( strpos( $term->name, '-' ) !== false ) {
                $parts = explode( '-', $term->name );

                $values[] = (int) $parts[0];
                $values[] = (int) $parts[1];
            } else {
                $values[] = (int) $term->name;
            }
        }

        $min = min( $values );
        $max = max( $values );

        $val_1 = $min;
        $val_2 = $max;

        if ( isset( $_GET['rng_' . $attribute] ) ) {
            $parts = explode( ',', $_GET['rng_' . $attribute] );

            $val_1 = $parts[0];
            $val_2 = $parts[1];
        }

        get_template_part( 'template-parts/widgets/slider-filter/template', null, [
            'attribute' => $attribute,
            'min'       => $min,
            'max'       => $max,
            'val_1'     => $val_1,
            'val_2'     => $val_2,
            'symbol'    => get_field( 'symbol' )
        ] );
    }
}