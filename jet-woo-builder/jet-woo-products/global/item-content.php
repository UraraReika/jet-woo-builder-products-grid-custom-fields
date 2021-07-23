<?php
/**
 * Loop item content
 */

$excerpt = jet_woo_builder_template_functions()->get_product_excerpt();
$excerpt = jet_woo_builder_tools()->trim_text(
	$excerpt,
	$this->get_attr( 'excerpt_length' ),
	$this->get_attr( 'excerpt_trim_type' ),
	'...'
);

if ( 'yes' !== $this->get_attr( 'show_excerpt' ) || null === $excerpt ) {
	do_action( 'jet-woo-builder/products-grid/content-related/custom-fields-render', 'content_related', 'jet-content-fields', [ 'before','after' ], $this );
	return;
}

do_action( 'jet-woo-builder/products-grid/content-related/custom-fields-render', 'content_related', 'jet-content-fields', [ 'before' ], $this );
?>

	<div class="jet-woo-product-excerpt"><?php echo $excerpt; ?></div>

<?php do_action( 'jet-woo-builder/products-grid/content-related/custom-fields-render', 'content_related', 'jet-content-fields', [ 'after' ], $this ); ?>