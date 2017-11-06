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
	 * Count of the carousels on the page.
	 *
	 * Based on a 1-index.
	 * Increments for each carousel (instance of this class).
	 *
	 * @var object
	 */
	public $carousel_count = 1;

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
		add_action( 'loop_end', array( $this, 'echo_galleries' ) );
		add_action( 'loop_end', array( $this, 'create_carousel' ) );
	}

	/**
	 * For each image gallery on the page, output a carousel in a modal.
	 *
	 * Though the logic is similar to create_carousel, this is only for galleries.
	 * This will only display on clicking a gallery image.
	 *
	 * @return void
	 */
	function echo_galleries() {
		$galleries = get_post_galleries( get_the_ID(), false );
		foreach ( $galleries as $gallery ) {
			$this->echo_modal_carousel( $this->get_image_ids_from_gallery( $gallery ) );
		}
	}

	/**
	 * Output the modal carousel.
	 *
	 * @param array  $image_ids The image IDs in the carousel, or none if there aren't any.
	 * @param string $carousel_id ID of the carousel, optional.
	 * @return void
	 */
	function echo_modal_carousel( $image_ids, $carousel_id = '' ) {
		$id = ! empty( $carousel_id ) ? $carousel_id : $this->carousel_count;
		$modal = new Modal_Carousel( 'gallery-' . $id );
		$this->carousel_count++;
		foreach ( $image_ids as $image_id ) {
			$attachment = wp_get_attachment_image_src( $image_id, 'full', false );
			if ( false !== $attachment ) {
				$modal->add_image( reset( $attachment ) );
			}
		}
		echo wp_kses_post( $modal->get() );
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

	/**
	 * Conditionally output a carousel of the post images.
	 *
	 * If the conditions are met, output modaal with a carousel of images.
	 * Though the logic is similar to echo_galleries(), this creates a modal for non-gallery post images.
	 *
	 * @return void
	 */
	function create_carousel() {
		$image_ids = $this->get_image_ids();
		if ( $this->do_make_carousel() & ! empty( $image_ids ) ) {
			$this->echo_modal_carousel( $image_ids, 'non-gallery' );
		}
	}

	/**
	 * Whether to make a carousel of the post images.
	 *
	 * @return bool $make_carousel Whether to make a carousel.
	 */
	public function do_make_carousel() {
		return (
			( is_single() || is_page() )
			&&
			$this->plugin->components->options->allow_carousel_for_post_images()
			&&
			! empty( $this->get_image_ids() )
		);
	}

	/**
	 * Get the image IDs to display in the carousel.
	 *
	 * First, match the IDs that appear in the post content.
	 * If this doesn't fiend IDs, get the image IDs that are attached to the post.
	 *
	 * @return array|null
	 */
	function get_image_ids() {
		$regex = '/wp-image-([\d]{1,4})/';
		preg_match_all( $regex, get_the_content(), $matches );
		if ( ! empty( $matches[1] ) ) {
			return $matches[1];
		}
		return $this->find_image_ids_attached_to_post();
	}

	/**
	 * Get the image IDs that are attached to a post.
	 *
	 * @return array|null $image_ids The IDs of images attached to the post, or null if there aren't any.
	 */
	function find_image_ids_attached_to_post() {
		$attachments = $this->query_for_images_in_post();
		if ( empty( $attachments ) ) {
			return null;
		}
		$image_ids = array();
		foreach ( $attachments as $attachment ) {
			array_push( $image_ids, $attachment->ID );
		}
		return $image_ids;
	}

	/**
	 * Get a query for the images attached to the current post.
	 *
	 * @return array|null $query A query for images attachments.
	 */
	function query_for_images_in_post() {
		$query = new \WP_Query( array(
			'post_type'      => 'attachment',
			'posts_per_page' => 20,
			'order'          => 'ASC',
			'orderby'        => 'menu_order',
			'post_parent'    => get_the_ID(),
			'post_mime_type' => 'image',
		) );
		return $query->get_posts();
	}
}
