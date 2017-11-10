<?php
/**
 * Template for the carousel controls.
 *
 * @package BootstrapSwipeGallery
 */

namespace BootstrapSwipeGallery;

?>
<ol class="carousel-indicators">
	<?php echo wp_kses_post( $this->indicators ); ?>
</ol>
<a class="left carousel-control" href="#<?php echo esc_attr( $this->gallery_id ); ?>"><span class="glyphicon glyphicon-chevron-left"></span></a>
<a class="right carousel-control" href="<?php echo esc_attr( $this->gallery_id ); ?>"><span class="glyphicon glyphicon-chevron-right"></span></a>
