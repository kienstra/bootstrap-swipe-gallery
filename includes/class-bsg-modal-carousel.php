<?php

// Builds and echoes a modal carousel for each gallery 
class BSG_Modal_Carousel {

  private $gallery_id ;
  private $carousel_inner_items ;
  private $image_indicators ;
  private $slide_to_index ;
  private $image_src_full_size ;
  private $number_of_images = 0 ;
  private static $instance_id = 1 ;  
  
  function __construct( $id = '' ) {
    $this->gallery_id = $id ? $id : 'gallery-' . self::$instance_id ;
    $this->carousel_inner_items  = '' ;
    $this->image_indicators = '' ;
    $this->slide_to_index = 0 ;
    self::$instance_id++;       
  }
    
  public function add_image( $image_src_full_size ) {
    $this->append_image_to_inner_items( $image_src_full_size ) ; 
    $this->append_to_carousel_indicators( $image_src_full_size ) ;
    $this->number_of_images++ ; // $this->number_of_images ; 
  }
  
  private function append_image_to_inner_items( $image_src_full_size ) {
    $is_active = (0 == $this->slide_to_index ) ? 'active' : '' ;

    $this->carousel_inner_items .= 
    "<div class='item {$is_active}'>
      <img src='{$image_src_full_size}'>
    </div> \n" ;
  }

  private function append_to_carousel_indicators( $image_src_full_size ){
    $is_active = (0 == $this->slide_to_index ) ? 'active' : '' ;
    
    $this->image_indicators .= 
    "<li class='{$is_active}' data-target='#{$this->gallery_id}' data-slide-to='{$this->slide_to_index}' data-src='{$image_src_full_size}'></li>" ;
    $this->slide_to_index++ ;
  }

  private function maybe_get_indicators_and_controls() {
    if ( $this->number_of_images > 1 ) {
      return
        "<ol class='carousel-indicators'> 
          {$this->image_indicators}
         </ol>	       
         <a class='left carousel-control' href='#{$this->gallery_id}' data-slide='prev'><span class='glyphicon glyphicon-chevron-left'></span></a>
	 <a class='right carousel-control' href='#{$this->gallery_id}' data-slide='next'><span class='glyphicon glyphicon-chevron-right'></span></a>\n" ;
    }
  } 
  
  public function get() {
    $maybe_indicators_and_controls = $this->maybe_get_indicators_and_controls() ;
    
    return 
    "<div id='{$this->gallery_id}' class='gallery-modal bsg modal fade'>
       <div class='modal-dialog modal-lg'>
	 <div class='modal-content modal-content-gallery'>
	   <div class='modal-header'>  
	     <a data-dismiss='modal' aria-hidden='true' href='#'>
	       <span class='glyphicon glyphicon-remove-circle'></span>
	     </a>
	   </div>\n
	   <div class='modal-body'>

	     <!-- carousel -->
	     <div id='carousel-{$this->gallery_id}' class='carousel bsg carousel-gallery'>   
	       <div class='carousel-inner'> 
		 {$this->carousel_inner_items}
	       </div>
               {$maybe_indicators_and_controls}
	       
	     </div>  <!-- .carousel --> 
           </div> <!-- .modal-body -->
         </div> <!-- .modal-content -->
       </div> <!-- .modal-dialog -->
     </div> <!-- .gallery-modal --> \n"  ;
  }
}