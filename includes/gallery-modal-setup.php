<?php

// Set up a modal carousel for every gallery

add_action( 'loop_end' , 'bsg_maybe_create_swipe_galleries' ) ;
function bsg_maybe_create_swipe_galleries() { 
  global $post ; 
  if ( isset( $post ) && bsg_post_has_a_gallery( $post ) ) { 
    bsg_echo_carousel_for_each_gallery_in_post() ;
  }
}

function bsg_echo_carousel_for_each_gallery_in_post() { 
  global $post ; 
  $galleries = get_post_galleries( $post->id , false ) ; 
  if ( isset( $galleries ) ) {
    foreach( $galleries as $gallery ) {
      $image_ids = bsg_get_image_ids_from_gallery( $gallery ) ;
      create_and_echo_modal_carousel( $image_ids ) ;
    }
  }
}

function bsg_get_image_ids_from_gallery( $gallery ) {
  $gallery_ids = $gallery[ 'ids' ] ;
  $image_ids = explode( ',' , $gallery_ids ) ;
  return $image_ids ; 
}
 
function create_and_echo_modal_carousel( $image_ids , $carousel_id = '' ) {
  $modal_for_gallery = new BSG_Modal_Carousel( $carousel_id ) ;
  foreach( $image_ids as $image_id ) {
    $src_full_size = bsg_get_full_size_image( $image_id ) ; 
    $modal_for_gallery->add_image( $src_full_size , '' ) ;
  }
  echo $modal_for_gallery->get() ;  
}

function bsg_get_full_size_image( $image_id ) {
  $raw_src_full_size = wp_get_attachment_image_src( $image_id , 'full', false ) ; 
  $src_full_size = $raw_src_full_size[ 0 ] ;
  return $src_full_size ;
}

add_action( 'loop_end' , 'bsg_maybe_make_carousel_of_post_images' ) ; 
function bsg_maybe_make_carousel_of_post_images() { 
  if ( bsg_do_make_carousel_of_post_images() ) { 
    bsg_echo_carousel_of_all_post_images() ;
  }
}

function bsg_echo_carousel_of_all_post_images() {
  global $post ; 
  if ( is_single( $post->ID ) ) {
    $image_ids = bsg_get_image_ids() ;
    create_and_echo_modal_carousel( $image_ids , 'non-gallery' ) ;    
  }
}

function bsg_get_image_ids() { 
  global $post ;
  $post_id = $post->ID ;  
  $image_ids = array() ;
  
  $args = array( 'post_type' => 'attachment' ,
  	  	 'posts_per_page' => -1 ,
		 'order'	  => 'ASC' ,
		 'orderby'	  => 'menu_order' ,
		 'post_status' => 'published' ,
		 'post_parent' => $post->ID ,
		 'post_mime_type' => 'image' ,
  ) ; 
  $attachments = get_posts( $args );
  if ( $attachments ) {
    foreach ( $attachments as $attachment ) {
      array_push( $image_ids , $attachment->ID ) ;
    }
  }
  return $image_ids ; 
}


