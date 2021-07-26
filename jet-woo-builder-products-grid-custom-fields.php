<?php
/**
 * Plugin Name: JetWooBuilder - Products Grid Custom Fields
 * Plugin URI:
 * Description:
 * Version:     1.0.0
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

		// register controls for Products Grid widget
		add_action( 'elementor/element/jet-woo-products/section_carousel/after_section_end', [ $this, 'register_custom_fields_controls' ], 10, 2 );
		add_action( 'elementor/element/jet-woo-products/section_not_found_message_style/after_section_end', [ $this, 'register_custom_fields_style_controls' ], 10, 2 );

		// render meta for passed position
		add_action( 'jet-woo-builder/products-grid/title-related/custom-fields-render', [ $this, 'render_custom_fields' ], 10, 4 );
		add_action( 'jet-woo-builder/products-grid/content-related/custom-fields-render', [ $this, 'render_custom_fields' ], 10, 4 );

		// add custom add to cart icon settings to providers settings list
		add_filter( 'jet-smart-filters/providers/jet-woo-products-grid/settings-list', [ $this, 'add_custom_add_to_cart_icon_settings_to_list' ] );

	}

	/**
	 * Register custom fields controls
	 *
	 * @param $obj
	 */
	public function register_custom_fields_controls( $obj ) {

		$obj->start_controls_section(
			'section_products_custom_fields',
			[
				'label' => esc_html__( 'Custom Fields', 'jet-woo-builder' ),
			]
		);

		$this->add_meta_controls(
			$obj,
			'title_related',
			esc_html__( 'Before/After Title', 'jet-woo-builder' )
		);

		$this->add_meta_controls(
			$obj,
			'content_related',
			esc_html__( 'Before/After Content', 'jet-woo-builder' )
		);

		$obj->end_controls_section();

	}

	/**
	 * Register custom fields style controls
	 *
	 * @param $obj
	 */
	public function register_custom_fields_style_controls( $obj ) {

		$obj->start_controls_section(
			'section_custom_fields_style',
			[
				'label'      => esc_html__( 'Custom Fields', 'jet-woo-builder' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			]
		);

		$this->add_meta_style_controls(
			$obj,
			'title_related',
			esc_html__( 'Before/After Title', 'jet-woo-builder' ),
			'jet-title-fields'
		);

		$this->add_meta_style_controls(
			$obj,
			'content_related',
			esc_html__( 'Before/After Content', 'jet-woo-builder' ),
			'jet-content-fields'
		);

		$obj->end_controls_section();

	}

	/**
	 * Add meta controls for selected position
	 *
	 * @param        $obj
	 * @param string $position_slug
	 * @param string $position_name
	 *
	 * @return void
	 */
	public function add_meta_controls( $obj, $position_slug, $position_name ) {

		$obj->add_control(
			'show_' . $position_slug . '_meta',
			[
				'label' => sprintf( esc_html__( 'Show Meta %s', 'jet-woo-builder' ), $position_name ),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$obj->add_control(
			'meta_' . $position_slug . '_position',
			[
				'label'     => esc_html__( 'Meta Fields Position', 'jet-woo-builder' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'before',
				'options'   => [
					'before' => esc_html__( 'Before', 'jet-woo-builder' ),
					'after'  => esc_html__( 'After', 'jet-woo-builder' ),
				],
				'condition' => [
					'show_' . $position_slug . '_meta' => 'yes',
				],
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'meta_key',
			[
				'label'       => esc_html__( 'Key', 'jet-woo-builder' ),
				'description' => esc_html__( 'Meta key from postmeta table in database', 'jet-woo-builder' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
			]
		);

		$repeater->add_control(
			'meta_label',
			[
				'label'   => esc_html__( 'Label', 'jet-woo-builder' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
			]
		);

		$repeater->add_control(
			'meta_format',
			[
				'label'       => esc_html__( 'Value Format', 'jet-woo-builder' ),
				'description' => esc_html__( 'Value format string, accepts HTML markup. %s - is meta value', 'jet-woo-builder' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '%s',
			]
		);

		$repeater->add_control(
			'meta_callback',
			[
				'label'   => esc_html__( 'Prepare meta value with callback', 'jet-woo-builder' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => apply_filters( 'jet-woo-builder/products-grid/meta_callbacks', [
					''                        => esc_html__( 'Clean', 'jet-woo-builder' ),
					'get_permalink'           => esc_html__( 'Get Permalink', 'jet-woo-builder' ),
					'get_the_title'           => esc_html__( 'Get Title', 'jet-woo-builder' ),
					'wp_get_attachment_url'   => esc_html__( 'Get Attachment URL', 'jet-woo-builder' ),
					'wp_get_attachment_image' => esc_html__( 'Get Attachment Image', 'jet-woo-builder' ),
					'date'                    => esc_html__( 'Format date', 'jet-woo-builder' ),
					'date_i18n'               => esc_html__( 'Format date (localized)', 'jet-woo-builder' ),
				] ),
			]
		);

		$repeater->add_control(
			'date_format',
			[
				'label'       => esc_html__( 'Format', 'jet-woo-builder' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'F j, Y',
				'description' => sprintf( '<a href="https://codex.wordpress.org/Formatting_Date_and_Time" target="_blank">%s</a>', esc_html__( 'Documentation on date and time formatting', 'jet-woo-builder' ) ),
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
						'meta_label' => esc_html__( 'Label', 'jet-woo-builder' ),
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
	 * Add meta controls for selected position
	 *
	 * @param string $position_slug
	 * @param string $position_name
	 * @param string $base
	 *
	 * @return void
	 */
	public function add_meta_style_controls( $obj, $position_slug, $position_name, $base ) {

		$obj->add_control(
			$position_slug . '_meta_styles',
			[
				'label'     => sprintf( esc_html__( 'Meta Styles %s', 'jet-woo-builder' ), $position_name ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$obj->add_control(
			$position_slug . '_meta_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .' . $base => 'background-color: {{VALUE}}',
				],
			]
		);

		$obj->add_control(
			$position_slug . '_meta_label_heading',
			[
				'label'     => esc_html__( 'Meta Label', 'jet-woo-builder' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$obj->add_control(
			$position_slug . '_meta_label_color',
			[
				'label'     => esc_html__( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .' . $base . '__item-label' => 'color: {{VALUE}}',
				],
			]
		);

		$obj->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => $position_slug . '_meta_label_typography',
				'scheme'   => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .' . $base . '__item-label',
			]
		);

		$obj->add_control(
			$position_slug . '_meta_label_display',
			[
				'label'     => esc_html__( 'Display Meta Label and Value', 'jet-woo-builder' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'   => [
					'inline-block' => esc_html__( 'Inline', 'jet-woo-builder' ),
					'block'        => esc_html__( 'As Blocks', 'jet-woo-builder' ),
				],
				'selectors' => [
					'{{WRAPPER}} .' . $base . '__item-label' => 'display: {{VALUE}}',
					'{{WRAPPER}} .' . $base . '__item-value' => 'display: {{VALUE}}',
				],
			]
		);

		$obj->add_control(
			$position_slug . '_meta_label_gap',
			[
				'label'     => esc_html__( 'Horizontal Gap Between Label and Value', 'jet-woo-builder' ),
				'type'      => Controls_Manager::NUMBER,
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
				'label'     => esc_html__( 'Meta Value', 'jet-woo-builder' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$obj->add_control(
			$position_slug . '_meta_color',
			[
				'label'     => esc_html__( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .' . $base . '__item-value' => 'color: {{VALUE}}',
				],
			]
		);

		$obj->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => $position_slug . '_meta_typography',
				'selector' => '{{WRAPPER}} .' . $base . '__item-value',
			]
		);

		$obj->add_responsive_control(
			$position_slug . '_meta_margin',
			[
				'label'      => esc_html__( 'Margin', 'jet-woo-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
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
				'label'      => esc_html__( 'Padding', 'jet-woo-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .' . $base => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$obj->add_responsive_control(
			$position_slug . '_meta_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'jet-woo-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .' . $base => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$obj->add_responsive_control(
			$position_slug . '_meta_align',
			[
				'label'     => esc_html__( 'Alignment', 'jet-woo-builder' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => jet_woo_builder_tools()->get_available_h_align_types( true ),
				'selectors' => [
					'{{WRAPPER}} .' . $base => 'text-align: {{VALUE}};',
				],
				'classes'   => 'elementor-control-align',
			]
		);

	}

	/**
	 * Render meta for passed position
	 *
	 * @param string $position
	 * @param string $base
	 * @param array  $context
	 * @param null   $obj
	 *
	 * @return void
	 */
	public function render_custom_fields( $position = '', $base = '', $context = [ 'before' ], $obj = null ) {

		$settings = $obj->get_settings();

		$config_key    = $position . '_meta';
		$show_key      = 'show_' . $position . '_meta';
		$position_key  = 'meta_' . $position . '_position';
		$meta_show     = $settings[ $show_key ];
		$meta_position = $settings[ $position_key ];
		$meta_config   = $settings[ $config_key ];

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

		foreach ( $meta_config as $meta ) {

			if ( empty( $meta['meta_key'] ) ) {
				continue;
			}

			$key      = $meta['meta_key'];
			$callback = ! empty( $meta['meta_callback'] ) ? $meta['meta_callback'] : false;
			$value    = get_post_meta( get_the_ID(), $key, false );

			if ( ! $value ) {
				continue;
			}

			$callback_args = array( $value[0] );

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
						$callback_args = array( $format, $timestamp );

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

		}

		if ( empty( $result ) ) {
			return;
		}

		printf( '<div class="%1$s">%2$s</div>', $base, $result );

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
	 * Returns merged custom icon settings with JetSmartFilters providers settings list
	 *
	 * @param $list
	 *
	 * @return array
	 */
	public function add_custom_add_to_cart_icon_settings_to_list( $list ) {

		$custom_icon_settings = [
			'show_title_related_meta',
			'show_content_related_meta',
			'meta_title_related_position',
			'meta_content_related_position',
			'title_related_meta',
			'content_related_meta',
		];

		return array_merge( $list, $custom_icon_settings );

	}

}

new Jet_Woo_Builder_Products_Grid_Custom_Fields();