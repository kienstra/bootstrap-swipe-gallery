<?php
/**
 * Tests for class Modal_Carousel.
 *
 * @package BootstrapSwipeGallery
 */

namespace BootstrapSwipeGallery;

/**
 * Tests for class Modal_Carousel.
 *
 * @package BootstrapSwipeGallery
 */
class Test_Class_Modal_Carousel extends \WP_UnitTestCase {
	/**
	 * Instance of tested class.
	 *
	 * @var object
	 */
	public $instance;

	/**
	 * ID of the modal carousel gallery.
	 *
	 * @const string
	 */
	const GALLERY_ID = 3;

	/**
	 * First mock URL for an image.
	 *
	 * @var string
	 */
	const IMAGE_1 = 'http://example.com/image-1.jpeg';

	/**
	 * Second mock URL for an image.
	 *
	 * @var string
	 */
	const IMAGE_2 = 'http://example.com/image-2.jpeg';

	/**
	 * Image ID for second image, created with a factory method.
	 *
	 * @var integer
	 */
	public $image_2;

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();
		$this->instance = new Modal_Carousel( self::GALLERY_ID );
	}

	/**
	 * Test construct().
	 *
	 * @see Modal_Carousel::__construct().
	 */
	public function test_construct() {
		$this->assertEquals( __NAMESPACE__ . '\Modal_Carousel', get_class( $this->instance ) );
		$this->assertEquals( self::GALLERY_ID, $this->instance->gallery_id );
		$this->assertEquals( '', $this->instance->indicators );
		$this->assertEquals( 0, $this->instance->slide_to_index );
		$this->assertEquals( 0, $this->instance->number_of_images );
	}

	/**
	 * Test add_image().
	 *
	 * @see Modal_Carousel::add_image().
	 */
	public function test_add_image() {
		$this->instance->add_image( self::IMAGE_1 );
		$this->assertEquals( 1, $this->instance->number_of_images );
	}

	/**
	 * Test append_to_inner_items().
	 *
	 * @see Modal_Carousel::append_to_inner_items().
	 */
	public function test_append_to_inner_items() {
		$this->instance->add_image( self::IMAGE_1 );
		$this->assertContains( '<div class="item active"', $this->instance->inner_items );
		$this->assertContains( self::IMAGE_1, $this->instance->inner_items );
	}

	/**
	 * Test append_to_indicators().
	 *
	 * @see Modal_Carousel::append_to_indicators().
	 */
	public function test_append_to_indicators() {
		$this->instance->add_image( self::IMAGE_1 );
		$this->assertContains( '<li class="active"', $this->instance->indicators );
		$this->assertContains( '<li class="active"', $this->instance->indicators );
	}

	/**
	 * Test controls().
	 *
	 * @see Modal_Carousel::controls().
	 */
	public function test_controls() {
		$this->instance->add_image( self::IMAGE_1 );
		$this->assertEmpty( $this->instance->controls() );
		$this->instance->add_image( self::IMAGE_2 );
		$this->assertContains( '<ol class="carousel-indicators">', $this->instance->controls() );
		$this->assertContains( $this->instance->indicators, $this->instance->controls() );
		$this->assertContains( strval( $this->instance->gallery_id ), $this->instance->controls() );
	}

	/**
	 * Test get().
	 *
	 * @see Modal_Carousel::get().
	 */
	public function test_get() {
		$this->instance->add_image( self::IMAGE_1 );
		$this->assertContains( strval( self::GALLERY_ID ), $this->instance->get() );
		$this->instance->add_image( self::IMAGE_2 );
		$this->assertContains( $this->instance->inner_items, $this->instance->get() );
		$this->assertContains( $this->instance->controls(), $this->instance->get() );
	}
}
