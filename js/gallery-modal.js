( function( $ ) {
  $( function() {
    // When a gallery image is clicked, open the modal carousel that was built by gallery-modal-setup.php
    $( '.gallery-item' ).on('click' , function( event ) {
      var $parent_gallery = $( this ).parents( '.gallery' ) ;
      var gallery_ordinal = $parent_gallery.parents( '.post' ).find( '.gallery' ).index( $parent_gallery ) ;
      var image_index = $( this ).parents( '.gallery' ).find( '.gallery-item' ).index( this ) ;
      var $bsg_modal_carousel = $( '.bsg.gallery-modal' ).eq( gallery_ordinal ) ;
      open_modal_carousel_with_image( $bsg_modal_carousel , image_index ) ;
      return false ;
    } ) ;

    // shortcut the galllery entirely 
    if ( bsg_do_allow && bsg_do_allow.post_image_carousels ) { // inserted through wp_localize_script
      var post_selector = '.post' ;
      var post_carousel_selector = '#non-gallery' ;
      var image_selector = 'img:not(.thumbnail):not(.attachment-thumbnail):not(.attachment-post-thumbnail)' ;

      var post_image_regex = /wp-image-[\d]{1,4}/ ; // should insert with wp_localize_script ?
      var $old_non_gallery_images_in_post = $( post_selector ).filter( function() {
	return this.className.match( post_image_regex ) ;
      } ) ;

      $( post_selector ).find( image_selector ).on( 'click' , function() {
	if ( $( this ).parents( '.gallery-item' ).length > 0 ) {
	  return $( this ) ; // this is actually a gallery item , so return
	}
	var $modal_carousel = $( post_carousel_selector ) ;
	var post_image_index = $( this ).parents( post_selector ).find( image_selector ).index( this ) ;
	open_modal_carousel_with_image( $modal_carousel , post_image_index ) ;
	return false ;
      } ) ;
    }

    function open_modal_carousel_with_image( $modal_carousel , image_index ) {
      var $carousel = $modal_carousel.find( '.carousel-gallery' ) ;
      reset_carousel( $carousel ) ;

      // Set the image in the modal carousel to "active" so it appears when it opens
      $carousel.find( '.carousel-inner .item' ).eq( image_index ).addClass( 'active' ) ;
      $carousel.find( '.carousel-indicators li' ).eq( image_index ).addClass( 'active' ) ;
      $carousel.carousel( { interval : false } ) ;
      $modal_carousel.modal() ;
    }

    $( '.carousel .left' ).on( 'click' , function() {
      $( this ).parents( '.carousel' ).carousel( 'prev' ) ;
      return false ;
    } ) ;

    $( '.carousel .right' ).on( 'click' , function() {
      $( this ).parents( '.carousel' ).carousel( 'next' ) ;
      return false ;
    } ) ;

    $( '.carousel-indicators li' ).on( 'click' , function() {
      var slide_to = $( this ).data( 'slide-to' ) ;
      $( this ).parents( '.carousel' ).carousel( slide_to ) ;
      return false ;
    } ) ;

    function reset_carousel( $carousel ) {
      $carousel.carousel( 'pause' ) ;
      $carousel.find( '.carousel-indicators .active' ).removeClass( 'active' ) ;
      var $carousel_inner = $carousel.find( '.carousel-inner' ) ;
      $carousel_inner.find( '.item.active' ).removeClass( 'active' ) ;
      $carousel_inner.find( '.item.next' ).removeClass( 'next' ) ;
      $carousel_inner.find( '.item.left' ).removeClass( 'left' ) ;
    }

    // Swipe support
    $( '.gallery-modal' ).swiperight( function() {
      $( this ).carousel( 'prev' );
    } ) ;
    $( '.gallery-modal' ).swipeleft(function() {
	$( this ).carousel( 'next' );
    } ) ;

    size_containing_div_of_image() ;
    $( window ).resize( size_containing_div_of_image ) ;

    function size_containing_div_of_image() {
      $( '.gallery-modal .carousel.carousel-gallery .carousel-inner .item' ).css( 'height' , function() {
	return ( 0.8 * $( window ).height() ) ;
      } ) ;
    }

  } ) ;
} )( jQuery ) ;
