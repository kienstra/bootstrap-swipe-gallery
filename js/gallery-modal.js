/* global bsgDoAllow */
( function( $ ) {
	$( function() {
		'use strict';

		var component = {};

		component.modalSelector = '.gallery-modal';

		component.resetCarousel = function($carousel ) {
			var $carousel_inner = $carousel.find( '.carousel-inner' );

			$carousel.carousel( 'pause' );
			$carousel.find( '.carousel-indicators .active' ).removeClass( 'active' );
			$carousel_inner.find( '.item.active' ).removeClass( 'active' );
			$carousel_inner.find( '.item.next' ).removeClass( 'next' );
			$carousel_inner.find( '.item.left' ).removeClass( 'left' );
		};

		component.openModalCarouselWithImage = function($modal_carousel , image_index ) {
			var $carousel = $modal_carousel.find( '.carousel-gallery' );

			component.resetCarousel( $carousel );
			// Set the image in the modal carousel to "active" so it appears when it opens
			$carousel.find( '.carousel-inner .item' ).eq( image_index ).addClass( 'active' );
			$carousel.find( '.carousel-indicators li' ).eq( image_index ).addClass( 'active' );
			$carousel.carousel( { interval : false } );
			$modal_carousel.modal();
		};

		component.sizeContainingDivOfImage = function() {
			$( '.gallery-modal .carousel.carousel-gallery .carousel-inner .item' ).css( 'height' , function() {
				var heightMultiple = 0.8;
				return heightMultiple * $( window ).height();
			} );
		};

		// When a gallery image is clicked, open the modal carousel that was built by gallery-modal-setup.php
		$( '.gallery-item' ).on('click' , function() {
			var $parent_gallery = $( this ).parents( '.gallery' ),
				gallery_ordinal = $parent_gallery.parents( '.post' ).find( '.gallery' ).index( $parent_gallery ),
				image_index = $( this ).parents( '.gallery' ).find( '.gallery-item' ).index( this ),
				$bsg_modal_carousel = $( '.bsg.gallery-modal' ).eq( gallery_ordinal );

			component.openModalCarouselWithImage( $bsg_modal_carousel, image_index );
			return false;
		} );

		// Shortcut the gallery entirely.
		if ( 'undefined' !== typeof bsgDoAllow && true === bsgDoAllow.postImageCarousels ) {
			component.postSelector = '.post';
			component.postCarouselSelector = '#non-gallery';
			component.imageSelector = 'img:not(.thumbnail):not(.attachment-thumbnail):not(.attachment-post-thumbnail)';

			$( component.postSelector ).find( component.imageSelector ).on( 'click' , function() {
				var $modal_carousel = $( component.postCarouselSelector ),
					post_image_index = $( this ).parents( component.postSelector ).find( component.imageSelector ).index( this );

				if ( 0 < $( this ).parents( '.gallery-item' ).length ) {
					// This is actually a gallery item, so return early.
					return $( this );
				}
				component.openModalCarouselWithImage( $modal_carousel, post_image_index );
				return false;
			} );
		}

		$( '.carousel .left' ).on( 'click' , function() {
			$( this ).parents( '.carousel' ).carousel( 'prev' );
			return false;
		} );

		$( '.carousel .right' ).on( 'click' , function() {
			$( this ).parents( '.carousel' ).carousel( 'next' );
			return false;
		} );

		$( '.carousel-indicators li' ).on( 'click' , function() {
			var slide_to = $( this ).data( 'slide-to' );
			$( this ).parents( '.carousel' ).carousel( slide_to );
			return false;
		} );

		// Swipe support.
		$( component.modalSelector ).swiperight( function() {
			$( this ).carousel( 'prev' );
		} );

		$( component.modalSelector ).swipeleft(function() {
			$( this ).carousel( 'next' );
		} );

		component.sizeContainingDivOfImage();

		// @todo: debounce or reconsider this.
		$( window ).resize( component.sizeContainingDivOfImage );


	} );
} )( jQuery );
