<?php
/**
 * JetWooBuilder Products Grid widget loop item content template.
 *
 * This template can be overridden by copying it to yourtheme/jet-woo-builder/widgets/global/products-grid/item-content.php.
 */

if ( 'yes' !== $this->get_attr( 'show_excerpt' ) ) {
	do_action( 'jet-woo-builder/products-grid/content-related/custom-fields-render', 'content_related', 'jet-content-fields', [ 'before','after' ], $this );
	return;
}

$excerpt = jet_woo_builder_tools()->trim_text(
	jet_woo_builder_template_functions()->get_product_excerpt(),
	$this->get_attr( 'excerpt_length' ),
	$this->get_attr( 'excerpt_trim_type' ),
	'...'
);

if ( empty( $excerpt ) ) {
	do_action( 'jet-woo-builder/products-grid/content-related/custom-fields-render', 'content_related', 'jet-content-fields', [ 'before','after' ], $this );
	return;
}

do_action( 'jet-woo-builder/products-grid/content-related/custom-fields-render', 'content_related', 'jet-content-fields', [ 'before' ], $this );
?>

<div class="jet-woo-product-excerpt">
	<?php echo wp_kses_post( $excerpt ); ?>
</div>

<?php do_action( 'jet-woo-builder/products-grid/content-related/custom-fields-render', 'content_related', 'jet-content-fields', [ 'after' ], $this ); ?>