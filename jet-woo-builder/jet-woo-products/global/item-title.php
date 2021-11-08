<?php
/**
 * Loop item title
 */

$full_title    = jet_woo_builder_template_functions()->get_product_title();
$title         = jet_woo_builder_tools()->trim_text(
	$full_title,
	$this->get_attr( 'title_length' ),
	$this->get_attr( 'title_trim_type' ),
	'...'
);
$title_link    = jet_woo_builder_template_functions()->get_product_permalink( $product );
$title_tag     = ! empty( $this->get_attr( 'title_html_tag' ) ) ? jet_woo_builder_tools()->sanitize_html_tag( $this->get_attr( 'title_html_tag' ) ) : 'h5';
$title_tooltip = '';

if ( -1 !== $this->get_attr( 'title_length' ) && 'yes' === $this->get_attr( 'title_tooltip' ) ) {
	$title_tooltip = 'title="' . $full_title . '"';
}

$open_wrap  = '<' . $title_tag . ' class="jet-woo-product-title" ' . $title_tooltip . '>';
$close_wrap = '</' . $title_tag . '>';

if ( 'yes' === $this->get_attr( 'add_title_link' ) ) {
	$open_wrap  = $open_wrap . '<a href="' . $title_link . '" ' . $target_attr . '>';
	$close_wrap = '</a>' . $close_wrap;
}

if ( 'yes' !== $this->get_attr( 'show_title' ) || '' === $title ) {
	do_action( 'jet-woo-builder/products-grid/title-related/custom-fields-render', 'title_related', 'jet-title-fields', [ 'before','after' ], $this );
	return;
}

do_action( 'jet-woo-builder/products-grid/title-related/custom-fields-render', 'title_related', 'jet-title-fields', [ 'before' ], $this );

echo $open_wrap . $title . $close_wrap;

do_action( 'jet-woo-builder/products-grid/title-related/custom-fields-render', 'title_related', 'jet-title-fields', [ 'after' ], $this );
