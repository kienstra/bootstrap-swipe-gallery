<?php
/**
 * Template for the carousel.
 *
 * @package BootstrapSwipeGallery
 */

namespace BootstrapSwipeGallery;

?>
<div id="<?php echo esc_attr( $this->gallery_id ); ?>" class="gallery-modal bsg modal fade">
	<div class="modal-dialog modal-lg">
		<div class="modal-content modal-content-gallery">
			<div class="modal-header">
				<a data-dismiss="modal" aria-hidden="true" href="#">
					<span class="glyphicon glyphicon-remove-circle"></span>
				</a>
			</div>
			<div class="modal-body">
				<div id="carousel-<?php echo esc_attr( $this->gallery_id ); ?>" class="carousel bsg carousel-gallery">
					<div class="carousel-inner">
						<?php echo wp_kses_post( $this->carousel_inner_items ); ?>
					</div>
					<?php echo wp_kses_post( $this->controls() ); ?>
				</div>
			</div>
		</div>
	</div>
</div>
