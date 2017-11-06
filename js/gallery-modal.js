/* exported bsgGalleryModal */
var bsgGalleryModal = ( function( $ ) {
	'use strict';

	var component = {
		/**
		 * Module data.
		 */
		data: {},

		modalSelector: '.gallery-modal',

		resetCarousel: function( $carousel ) {
			var $carouselInner = $carousel.find( '.carousel-inner' );

			$carousel.carousel( 'pause' );
			$carousel.find( '.carousel-indicators .active' ).removeClass( 'active' );
			$carouselInner.find( '.item.active' ).removeClass( 'active' );
			$carouselInner.find( '.item.next' ).removeClass( 'next' );
			$carouselInner.find( '.item.left' ).removeClass( 'left' );
		},

		openModal: function( $modalCarousel, imageIndex ) {
			var $carousel = $modalCarousel.find( '.carousel-gallery' );
			component.resetCarousel( $carousel );

			// Set the image in the modal carousel to "active" so it appears when it opens
			$carousel.find( '.carousel-inner .item' ).eq( imageIndex ).addClass( 'active' );
			$carousel.find( '.carousel-indicators li' ).eq( imageIndex ).addClass( 'active' );
			$carousel.carousel( { interval: false } );
			$modalCarousel.modal();
		},

		sizeContainer: function() {
			$( '.gallery-modal .carousel.carousel-gallery .carousel-inner .item' ).css( 'height', function() {
				var heightMultiple = 0.8;
				return heightMultiple * $( window ).height();
			} );
		},

		addHandlers: function() {
			/**
			 * On clicking a gallery image, open the modal carousel by
			 *
			 * @see gallery-modal-setup.php for the markup of this carousel.
			 */
			$( '.gallery-item' ).on( 'click', function() {
				var $parentGallery = $( this ).parents( '.gallery' ),
					ordinal = $parentGallery.parents( '.post' ).find( '.gallery' ).index( $parentGallery ),
					index = $( this ).parents( '.gallery' ).find( '.gallery-item' ).index( this ),
					$modal = $( '.bsg.gallery-modal' ).eq( ordinal );

				component.openModal( $modal, index );
				return false;
			} );

			/**
			 * Shortcut the gallery entirely.
			 */
			if ( 'undefined' !== typeof bsgDoAllow && '1' === component.data.postImageCarousels ) {
				component.postSelector = '.post';
				component.postCarouselSelector = '#non-gallery';
				component.imageSelector = 'img:not(.thumbnail):not(.attachment-thumbnail):not(.attachment-post-thumbnail)';

				$( component.postSelector ).find( component.imageSelector ).on( 'click', function() {
					var $modalCarousel = $( component.postCarouselSelector ),
						postImageIndex = $( this ).parents( component.postSelector ).find( component.imageSelector ).index( this );
					// If this is a gallery item, return early.
					if ( 0 < $( this ).parents( '.gallery-item' ).length ) {
						return $( this );
					}
					component.openModal( $modalCarousel, postImageIndex );
					return false;
				} );
			}

			$( '.carousel .left' ).on( 'click', function() {
				$( this ).parents( '.carousel' ).carousel( 'prev' );
				return false;
			} );

			$( '.carousel .right' ).on( 'click', function() {
				$( this ).parents( '.carousel' ).carousel( 'next' );
				return false;
			} );

			$( '.carousel-indicators li' ).on( 'click', function() {
				var slideTo = $( this ).data( 'slide-to' );
				$( this ).parents( '.carousel' ).carousel( slideTo );
				return false;
			} );

			// On swiping right, go to the previous image.
			$( component.modalSelector ).swiperight( function() {
				$( this ).carousel( 'prev' );
			} );

			// On swiping left, go to the next image.
			$( component.modalSelector ).swipeleft(function() {
				$( this ).carousel( 'next' );
			} );
		}
	};

	return {
		 /**
		  * Init module.
		 *
		 * @param {Object} data Object data.
		 * @return {void}
		 */
		init: function( data ) {
			component.data = data;
			$( document ).ready( function() {
				component.addHandlers();
				component.sizeContainer();
			} );
		}
	};

} )( jQuery );
