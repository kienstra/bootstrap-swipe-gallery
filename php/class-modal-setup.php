<?php
/**
 * Modal_Setup class
 *
 * @package BootstrapSwipeGallery
 */

namespace BootstrapSwipeGallery;

/**
 * Set up a modal carousel for every gallery.
 */
class Modal_Setup {

	/**
	 * Instance of the plugin.
	 *
	 * @var object
	 */
	public $plugin;

	/**
	 * Options constructor.
	 *
	 * @param object $plugin Instance of the plugin.
	 */
	function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Add action and filter hooks.
	 *
	 * @return void
	 */
	function init() {
		add_action( 'loop_end', array( $this, 'create_galleries' ) );
		add_action( 'loop_end', array( $this, 'create_carousel' ) );
	}

	function create_galleries() {
		$galleries = get_post_galleries( get_the_ID(), false );
		foreach ( $galleries as $gallery ) {
			$this->echo_modal_carousel( $this->get_image_ids_from_gallery( $gallery ) );
		}
	}

	/**
	 * Output the modal carousel.
	 *
	 * @param array $image_ids The image IDs in the carousel, or none if there aren't any.
	 * @param string $carousel_id ID of the carousel, optional.
	 * @return string $markup The modal carousel markup.
	 */
	function echo_modal_carousel( $image_ids, $carousel_id = '' ) {
		if ( ( null === $image_ids ) || empty( $image_ids ) ) {
			return;
		}
		$modal_for_gallery = new Modal_Carousel( $carousel_id );
		foreach ( $image_ids as $image_id ) {
			$src_full_size = reset( wp_get_attachment_image_src( $image_id, 'full', false ) );
			$modal_for_gallery->add_image( $src_full_size );
		}
		echo wp_kses_post( $modal_for_gallery->get() );
	}

	/**
	 * Get the image IDs in a gallery.
	 *
	 * @param array $gallery The gallery, as produced by get_post_galleries().
	 * @return array|null $image_ids The IDs of images in tha gallery.
	 */
	function get_image_ids_from_gallery( $gallery ) {
		if ( ! isset( $gallery['ids'] ) ) {
			return null;
		}
		return explode( ',', $gallery['ids'] );
	}

	function create_carousel() {
		if ( $this->do_make_carousel_of_post_images() ) {
			$this->echo_modal_carousel( $this->get_image_ids(), 'non-gallery' );
		}
	}

	/**
	 * Whether to make a carousel of the post images.
	 *
	 * @return bool $make_carousel Whether to make a carousel.
	 */
	public function do_make_carousel_of_post_images() {
		return (
			( is_single() || is_page() )
			&&
			$this->plugin->components['options']->options_allow_carousel_for_all_post_images()
			&&
			! empty( $this->get_image_ids() )
		);
	}

	function get_image_ids() {
		$image_ids = $this->traverse_post_content_for_image_ids();
		if ( null === $image_ids ) {
			return $this->find_image_ids_attached_to_post();
		}
		return $image_ids;
	}

	function traverse_post_content_for_image_ids() {
		$regex = '/wp-image-([\d]{1,4})/';
		preg_match_all( $regex, get_the_content(), $matches );
		return isset( $matches[1] ) ? $matches[1] : null;
	}

	function find_image_ids_attached_to_post() {
		$attachments = $this->query_for_images_in_post();
		return $this->get_image_ids_from( $attachments );
	}

	function query_for_images_in_post() {
		$query = new WP_Query( array(
			'post_type'      => 'attachment',
			'posts_per_page' => 20,
			'order'	         => 'ASC',
			'orderby'        => 'menu_order',
			'post_parent'    => get_the_ID(),
			'post_mime_type' => 'image',
		) );
		return isset( $query->query ) ? $query->query : null;
	}

	function get_image_ids_from( $attachments ) {
		$image_ids = array();
		if ( empty( $attachments ) ) {
			return;
		}
		foreach ( $attachments as $attachment ) {
			array_push( $image_ids, $attachment->ID );
		}
		return $image_ids;
	}

}