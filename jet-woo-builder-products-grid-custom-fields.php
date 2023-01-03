<?php
/**
 * Plugin Name: JetWooBuilder - Products Grid Custom Fields
 * Plugin URI: https://github.com/UraraReika/jet-woo-builder-products-grid-custom-fields
 * Version:     1.0.3
 * Author:      Crocoblock
 * Author URI:  https://crocoblock.com/
 * Text Domain: jet-woo-builder
 * License:     GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 * Domain Path: /languages
 */

use Elementor\Controls_Manager;
use Elementor\Core\Schemes\Typography as Scheme_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die();
}

class Jet_Woo_Builder_Products_Grid_Custom_Fields {

	public function __construct() {

		// Register controls for Products Grid widget.
		add_action( 'elementor/element/jet-woo-products/section_carousel/after_section_end', [ $this, 'register_custom_fields_controls' ], 10, 2 );
		add_action( 'elementor/element/jet-woo-products/section_not_found_message_style/after_section_end', [ $this, 'register_custom_fields_style_controls' ], 10, 2 );

		// Render meta for passed position.
		add_action( 'jet-woo-builder/products-grid/title-related/custom-fields-render', [ $this, 'render_custom_fields' ], 10, 4 );
		add_action( 'jet-woo-builder/products-grid/content-related/custom-fields-render', [ $this, 'render_custom_fields' ], 10, 4 );

		// Add custom fields settings to providers settings list.
		add_filter( 'jet-smart-filters/providers/jet-woo-products-grid/settings-list', [ $this, 'add_custom_fields_settings_to_list' ] );

	}

	/**
	 * Register custom fields controls.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param $obj
	 */
	public function register_custom_fields_controls( $obj ) {

		$obj->start_controls_section(
			'section_products_custom_fields',
			[
				'label' => __( 'Custom Fields', 'jet-woo-builder' ),
			]
		);

		$this->add_meta_controls(
			$obj,
			'title_related',
			__( 'Before/After Title', 'jet-woo-builder' )
		);

		$this->add_meta_controls(
			$obj,
			'content_related',
			__( 'Before/After Content', 'jet-woo-builder' )
		);

		$obj->end_controls_section();

	}

	/**
	 * Register custom fields style controls.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param $obj
	 */
	public function register_custom_fields_style_controls( $obj ) {

		$obj->start_controls_section(
			'section_custom_fields_style',
			[
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'Custom Fields', 'jet-woo-builder' ),
			]
		);

		$this->add_meta_style_controls(
			$obj,
			'title_related',
			__( 'Before/After Title', 'jet-woo-builder' ),
			'jet-title-fields'
		);

		$this->add_meta_style_controls(
			$obj,
			'content_related',
			__( 'Before/After Content', 'jet-woo-builder' ),
			'jet-content-fields'
		);

		$obj->end_controls_section();

	}

	/**
	 * Add meta controls.
	 *
	 * Add meta controls for selected position.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param object $obj           Widget instance.
	 * @param string $position_slug Position slug.
	 * @param string $position_name Position name.
	 *
	 * @return void
	 */
	public function add_meta_controls( $obj, $position_slug, $position_name ) {

		$obj->add_control(
			'show_' . $position_slug . '_meta',
			[
				'type'  => Controls_Manager::SWITCHER,
				'label' => sprintf( __( 'Show Meta %s', 'jet-woo-builder' ), $position_name ),
			]
		);

		$obj->add_control(
			'meta_' . $position_slug . '_position',
			[
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Meta Fields Position', 'jet-woo-builder' ),
				'options'   => [
					'before' => __( 'Before', 'jet-woo-builder' ),
					'after'  => __( 'After', 'jet-woo-builder' ),
				],
				'default'   => 'before',
				'condition' => [
					'show_' . $position_slug . '_meta' => 'yes',
				],
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'meta_key',
			[
				'type'        => Controls_Manager::TEXT,
				'label'       => __( 'Key', 'jet-woo-builder' ),
				'description' => __( 'Meta key from post meta table in database.', 'jet-woo-builder' ),
			]
		);

		$repeater->add_control(
			'meta_label',
			[
				'type'  => Controls_Manager::TEXT,
				'label' => __( 'Label', 'jet-woo-builder' ),
			]
		);

		$repeater->add_control(
			'meta_format',
			[
				'type'        => Controls_Manager::TEXT,
				'label'       => __( 'Value Format', 'jet-woo-builder' ),
				'description' => __( 'Value format string, accepts HTML markup. %s - is meta value.', 'jet-woo-builder' ),
				'default'     => '%s',
			]
		);

		$repeater->add_control(
			'meta_callback',
			[
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Prepare meta value with callback', 'jet-woo-builder' ),
				'options' => apply_filters( 'jet-woo-builder/products-grid/meta_callbacks', [
					''                        => __( 'Clean', 'jet-woo-builder' ),
					'get_permalink'           => __( 'Get Permalink', 'jet-woo-builder' ),
					'get_the_title'           => __( 'Get Title', 'jet-woo-builder' ),
					'wp_get_attachment_url'   => __( 'Get Attachment URL', 'jet-woo-builder' ),
					'wp_get_attachment_image' => __( 'Format date (localized)', 'jet-woo-builder' ),
				] ),
				'default' => '',
			]
		);

		$repeater->add_control(
			'date_format',
			[
				'type'        => Controls_Manager::TEXT,
				'label'       => __( 'Format', 'jet-woo-builder' ),
				'description' => sprintf( '<a href="https://codex.wordpress.org/Formatting_Date_and_Time" target="_blank">%s</a>', __( 'Documentation on date and time formatting.', 'jet-woo-builder' ) ),
				'default'     => 'F j, Y',
				'condition'   => [
					'meta_callback' => [ 'date', 'date_i18n' ],
				],
			]
		);

		$obj->add_control(
			$position_slug . '_meta',
			[
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[
						'meta_label' => __( 'Label', 'jet-woo-builder' ),
					],
				],
				'title_field' => '{{{ meta_key }}}',
				'condition'   => [
					'show_' . $position_slug . '_meta' => 'yes',
				],
			]
		);

	}

	/**
	 * Add meta style controls.
	 *
	 * Add meta controls for selected position.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param string $position_slug Position slug.
	 * @param string $position_name Position name.
	 * @param string $base          Position selector.
	 *
	 * @return void
	 */
	public function add_meta_style_controls( $obj, $position_slug, $position_name, $base ) {

		$obj->add_control(
			$position_slug . '_meta_styles',
			[
				'type'      => Controls_Manager::HEADING,
				'label'     => sprintf( __( 'Meta Styles %s', 'jet-woo-builder' ), $position_name ),
				'separator' => 'before',
			]
		);

		$obj->add_control(
			$position_slug . '_meta_bg_color',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Background Color', 'jet-woo-builder' ),
				'selectors' => [
					'{{WRAPPER}} .' . $base => 'background-color: {{VALUE}}',
				],
			]
		);

		$obj->add_control(
			$position_slug . '_meta_label_heading',
			[
				'type'      => Controls_Manager::HEADING,
				'label'     => __( 'Meta Label', 'jet-woo-builder' ),
				'separator' => 'before',
			]
		);

		$obj->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => $position_slug . '_meta_label_typography',
				'selector' => '{{WRAPPER}} .' . $base . '__item-label',
			]
		);

		$obj->add_control(
			$position_slug . '_meta_label_color',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'selectors' => [
					'{{WRAPPER}} .' . $base . '__item-label' => 'color: {{VALUE}}',
				],
			]
		);

		$obj->add_control(
			$position_slug . '_meta_label_display',
			[
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Display Meta Label and Value', 'jet-woo-builder' ),
				'options'   => [
					'inline-block' => __( 'Inline', 'jet-woo-builder' ),
					'block'        => __( 'As Blocks', 'jet-woo-builder' ),
				],
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .' . $base . '__item-label' => 'display: {{VALUE}}',
					'{{WRAPPER}} .' . $base . '__item-value' => 'display: {{VALUE}}',
				],
			]
		);

		$obj->add_control(
			$position_slug . '_meta_label_gap',
			[
				'type'      => Controls_Manager::NUMBER,
				'label'     => __( 'Horizontal Gap Between Label and Value', 'jet-woo-builder' ),
				'default'   => 5,
				'min'       => 0,
				'max'       => 20,
				'step'      => 1,
				'selectors' => [
					'{{WRAPPER}} .' . $base . '__item-label' => 'margin-right: {{VALUE}}px',
				],
			]
		);

		$obj->add_control(
			$position_slug . '_meta_value_heading',
			[
				'type'      => Controls_Manager::HEADING,
				'label'     => __( 'Meta Value', 'jet-woo-builder' ),
				'separator' => 'before',
			]
		);

		$obj->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => $position_slug . '_meta_typography',
				'selector' => '{{WRAPPER}} .' . $base . '__item-value',
			]
		);

		$obj->add_control(
			$position_slug . '_meta_color',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'selectors' => [
					'{{WRAPPER}} .' . $base . '__item-value' => 'color: {{VALUE}}',
				],
			]
		);

		$obj->add_responsive_control(
			$position_slug . '_meta_border_radius',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Border Radius', 'jet-woo-builder' ),
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .' . $base => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$obj->add_responsive_control(
			$position_slug . '_meta_margin',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Margin', 'jet-woo-builder' ),
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .' . $base => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator'  => 'before',
			]
		);

		$obj->add_responsive_control(
			$position_slug . '_meta_padding',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Padding', 'jet-woo-builder' ),
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .' . $base => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$obj->add_responsive_control(
			$position_slug . '_meta_align',
			[
				'type'      => Controls_Manager::CHOOSE,
				'label'     => __( 'Alignment', 'jet-woo-builder' ),
				'options'   => jet_woo_builder_tools()->get_available_h_align_types( true ),
				'selectors' => [
					'{{WRAPPER}} .' . $base => 'text-align: {{VALUE}};',
				],
				'classes'   => 'elementor-control-align',
			]
		);

	}

	/**
	 * Render custom field.
	 *
	 * Render meta for passed position.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param string $position Position slug.
	 * @param string $base     Position selector.
	 * @param array  $context  Usage context.
	 * @param null   $obj      Widget instance.
	 *
	 * @return void
	 */
	public function render_custom_fields( $position = '', $base = '', $context = [ 'before' ], $obj = null ) {

		global $product;

		$settings      = $obj->get_settings();
		$meta_show     = $settings[ 'show_' . $position . '_meta' ];
		$meta_position = $settings[ 'meta_' . $position . '_position' ];
		$meta_config   = $settings[ $position . '_meta' ];

		if ( 'yes' !== $meta_show ) {
			return;
		}

		if ( ! $meta_position || ! in_array( $meta_position, $context ) ) {
			return;
		}

		if ( empty( $meta_config ) ) {
			return;
		}

		$result = '';
		$order  = '';

		foreach ( $meta_config as $meta ) {
			if ( empty( $meta['meta_key'] ) ) {
				continue;
			}

			$key      = $meta['meta_key'];
			$callback = ! empty( $meta['meta_callback'] ) ? $meta['meta_callback'] : false;
			$value    = get_post_meta( $product->get_id(), $key, false );

			if ( ! $value ) {
				continue;
			}

			$callback_args = [ $value[0] ];

			if ( $callback ) {
				switch ( $callback ) {
					case 'wp_get_attachment_image':
						$callback_args[] = 'full';

						break;

					case 'date':
					case 'date_i18n':
						$timestamp       = $value[0];
						$valid_timestamp = $this->is_valid_timestamp( $timestamp );

						if ( ! $valid_timestamp ) {
							$timestamp = strtotime( $timestamp );
						}

						$format        = ! empty( $meta['date_format'] ) ? $meta['date_format'] : 'F j, Y';
						$callback_args = [ $format, $timestamp ];

						break;
				}
			}

			if ( ! empty( $callback ) && is_callable( $callback ) ) {
				$meta_val = call_user_func_array( $callback, $callback_args );
			} else {
				$meta_val = $value[0];
			}

			$meta_val = sprintf( $meta['meta_format'], $meta_val );

			$label = ! empty( $meta['meta_label'] )
				? sprintf( '<div class="%1$s__item-label">%2$s</div>', $base, $meta['meta_label'] )
				: '';

			$result .= sprintf(
				'<div class="%1$s__item %1$s__item-%4$s">%2$s<div class="%1$s__item-value">%3$s</div></div>',
				$base, $label, $meta_val, esc_attr( $key )
			);

			if ( 'preset-1' === $obj->get_attr( 'presets' ) ) {
				if ( 'title_related' === $position ) {
					$order = $settings['title_order'] ?? '';
				} elseif ( 'content_related' === $position ) {
					$order = $settings['excerpt_order'] ?? '';
				}
			}

		}

		if ( empty( $result ) ) {
			return;
		}

		printf( '<div class="%1$s" style="order: %3$s">%2$s</div>', $base, $result, $order );

	}

	/**
	 * Check if is valid timestamp
	 *
	 * @param int|string $timestamp
	 *
	 * @return boolean
	 */
	public function is_valid_timestamp( $timestamp ) {
		return ( ( string )( int )$timestamp === $timestamp ) && ( $timestamp <= PHP_INT_MAX ) && ( $timestamp >= ~PHP_INT_MAX );
	}

	/**
	 * Add custom fields settings to list.
	 *
	 * Returns merged custom fields settings with JetSmartFilters providers settings list.
	 *
	 * @since  1.0.0
	 * @since  1.0.3 Added additional settings.
	 * @access public
	 *
	 * @param array $list Stored settings list.
	 *
	 * @return array
	 */
	public function add_custom_fields_settings_to_list( $list ) {

		$custom_icon_settings = [
			'show_title_related_meta',
			'show_content_related_meta',
			'meta_title_related_position',
			'meta_content_related_position',
			'title_related_meta',
			'content_related_meta',
			'title_order',
			'excerpt_order',

		];

		return array_merge( $list, $custom_icon_settings );

	}

}

new Jet_Woo_Builder_Products_Grid_Custom_Fields();