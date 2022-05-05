<div class="range-slider" 
    data-attribute="<?php echo $args['attribute']; ?>" 
    data-min="<?php echo $args['min']; ?>" 
    data-max="<?php echo $args['max']; ?>" 
    data-val1="<?php echo $args['val_1']; ?>" 
    data-val2="<?php echo $args['val_2']; ?>" 
    data-symbol="<?php echo $args['symbol']; ?>">

    <?php if ( get_field( 'title' ) ): ?>
        <h4 class="range-slider__title relative">
            <?php the_field( 'title' ); ?>

            <a href="javascript: void(0);" title="toggle" class="woof_front_toggle woof_front_toggle_opened js-toggle-range-slider" data-condition="opened">
                <img src="<?php echo home_url(); ?>/wp-content/plugins/woocommerce-products-filter/img/minus3.png" alt="toggle">
            </a>
        </h4>
    <?php endif; ?>

    <div class="range-slider__inner">
        <div class="range-slider__inputs flex justify-content-between align-items-center">
            <div class="range-slider__input-wrap">
                <input class="range-slider__input js-rs-input" type="text" data-type="min" value="<?php echo $args['val_1']; ?> <?php if ( $args['symbol'] ): ?><?php echo $args['symbol']; ?><?php endif; ?>">
            </div>

            <div class="range-slider__input-label"><?php _e( 'līdz', 'unihaus' ); ?></div>

            <div class="range-slider__input-wrap">
                <input class="range-slider__input js-rs-input" type="text" data-type="max" value="<?php echo $args['val_2']; ?> <?php if ( $args['symbol'] ): ?><?php echo $args['symbol']; ?><?php endif; ?>">
            </div>
        </div>

        <div class="range-slider__slider-wrap">
            <div class="range-slider__slider"></div>
        </div>
    </div>
</div>