<?php
/**
 * Modal_Carousel class
 *
 * @package BootstrapSwipeGallery
 */

namespace BootstrapSwipeGallery;

/**
 * Builds and returns the modal carousel markup for each gallery.
 */
class Modal_Carousel {
	/**
	 * ID of the gallery.
	 *
	 * @var string
	 */
	public $gallery_id;

	/**
	 * Markup of the carousel inner items.
	 *
	 * @var string
	 */
	public $inner_items;

	/**
	 * Markup of the image indicators.
	 *
	 * @var string
	 */
	public $indicators = '';

	/**
	 * Index of the image.
	 *
	 * @var integer
	 */
	public $slide_to_index = 0;

	/**
	 * Number of images in the carousel.
	 *
	 * @var integer
	 */
	public $number_of_images = 0;

	/**
	 * Modal_Carousel constructor.
	 *
	 * @param string $gallery_id The ID of the carousel (optional).
	 */
	public function __construct( $gallery_id ) {
		$this->gallery_id = $gallery_id;
	}

	/**
	 * Add an image to the carousel.
	 *
	 * Add to the markup in the inner items and the indicators (like breadcrumbs).
	 * And increment the number of images in the carousel.
	 *
	 * @param string $image_src URL of the full-size image.
	 * @return void
	 */
	public function add_image( $image_src ) {
		$this->append_to_inner_items( $image_src );
		$this->append_to_indicators( $image_src );
		$this->number_of_images++;
	}

	/**
	 * Add an image to the carousel inner items markup.
	 *
	 * This is the markup, as it'll appear in the carousel.
	 * Add an 'active' class if it's the first in the carousel.
	 *
	 * @param string $image_src URL of the image.
	 * @return void
	 */
	public function append_to_inner_items( $image_src ) {
		$active_class = ( 0 === $this->slide_to_index ) ? ' active' : '';

		$this->inner_items .=
		'<div class="item' . esc_attr( $active_class ) . '">
			<img src="' . esc_attr( $image_src ) . '">
		</div>';
	}

	/**
	 * Add an image to the carousel indicators markup.
	 *
	 * These indicators are like breadcrumbs.
	 * Clicking them will scroll the carousel to the image.
	 *
	 * @return void
	 */
	public function append_to_indicators() {
		$is_active = ( 0 === $this->slide_to_index );
		$this->indicators .= $is_active ? '<li class="active"></li>' : '<li></li>';
		$this->slide_to_index++;
	}

	/**
	 * Get the indicators and controls markup, if there's more than 1 image.
	 *
	 * The indicators are like breadcrumbs, and there is one for every image.
	 * And the controls are the same for every carousel. except for the gallery ID.
	 *
	 * @return string $markup Indicator and control markup.
	 */
	public function controls() {
		if ( $this->number_of_images > 1 ) {
			ob_start();
			require __DIR__ . '/templates/controls.php';
			return ob_get_clean();
		}
	}

	/**
	 * Get the full carousel markup.
	 *
	 * @return string $markup The full carousel markup.
	 */
	public function get() {
		ob_start();
		require __DIR__ . '/templates/carousel.php';
		return ob_get_clean();
	}
}
