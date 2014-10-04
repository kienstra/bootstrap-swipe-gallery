<?php

/*
Plugin Name: Bootstrap Swipe Gallery
Plugin URI: www.ryankienstra.com/swipe-gallery
Description: Swipe through gallery images on touch devices. Image sizes adjust to screen size. Must have Twitter Bootstrap 3. 

Version: 1.0.2
Author: Ryan Kienstra
Author URI: www.ryankienstra.com
License: GPL2
*/

if ( ! defined( 'WPINC' )  ) {
 die ;
}

define( 'BSG_PLUGIN_SLUG' , 'bootstrap-swipe-gallery' ) ;
define( 'BSG_PLUGIN_VERSION' , '1.0.2' ) ; 

register_activation_hook( __FILE__ , 'bsg_deactivate_if_early_wordpress_version' ) ;
function bsg_deactivate_if_early_wordpress_version() {
  if ( version_compare( get_bloginfo( 'version' ) , '3.8' , '<' ) ) {
    deactivate_plugins( basename( __FILE__ ) ) ;
  }
}

register_activation_hook( __FILE__ , 'bsg_activate_with_default_options' ) ;
function bsg_activate_with_default_options() {
  add_option( 'bsg_plugin_options' , array( 'bsg_allow_carousel_for_all_post_images' , '0' ) ) ;
}

add_action( 'plugins_loaded' , 'bsg_text_domain' ) ;
function bsg_text_domain() {
  load_plugin_textdomain( 'bootstrap-swipe-gallery' ) ; 
}

add_action( 'plugins_loaded' , 'bsg_get_required_files' ) ;
function bsg_get_required_files() {
  require_once( plugin_dir_path( __FILE__ ) . 'includes/class-bsg-modal-carousel.php' ) ;
  require_once( plugin_dir_path( __FILE__ ) . 'includes/gallery-modal-setup.php' ) ;
  require_once( plugin_dir_path( __FILE__ ) . 'includes/bsg-options.php' ) ;
}

add_action( 'wp_enqueue_scripts' , 'bsg_enqueue_scripts_and_styles_if_page_has_gallery' ) ;
function bsg_enqueue_scripts_and_styles_if_page_has_gallery() {
  global $post ;
  if ( isset( $post ) && bsg_post_should_have_a_swipe_gallery( $post ) ) {
    wp_enqueue_style( BSG_PLUGIN_SLUG . '-carousel' , plugins_url( '/css/bsg-carousel.css' , __FILE__ ) , BSG_PLUGIN_VERSION );
    wp_enqueue_script( 'jquery' ) ; 
    // MIT license: https://jquery.org/license/
    wp_enqueue_script( BSG_PLUGIN_SLUG . '-jquery-mobile-swipe', plugins_url( '/js/jquery.mobile.custom.min.js' , __FILE__ ) , array( 'jquery' ) , BSG_PLUGIN_VERSION , true ) ;
    wp_enqueue_script( BSG_PLUGIN_SLUG . '-modal-setup', plugins_url( '/js/gallery-modal.js' , __FILE__ ) , array( 'jquery' , BSG_PLUGIN_SLUG . '-jquery-mobile-swipe' ) , BSG_PLUGIN_VERSION , true ) ;
    bsg_localize_script() ; 
  }
}

function bsg_localize_script() {
  $do_allow = ( bsg_do_make_carousel_of_post_images() ) ? true : false ; 
  wp_localize_script( BSG_PLUGIN_SLUG . '-modal-setup' , 'bsg_do_allow' , array( 'post_image_carousels' => $do_allow ) ) ;
}

function bsg_post_should_have_a_swipe_gallery( $post ) {
  return ( bsg_post_has_a_gallery( $post ) || bsg_do_make_carousel_of_post_images() ) ; 
}

function bsg_post_has_a_gallery( $post ) {
  $galleries = get_post_galleries( $post->id , false ) ;
  if ( $galleries ) {
    return true ;
  }
}

function bsg_do_make_carousel_of_post_images() {
  return ( bsg_is_single_post_or_page() && bsg_options_allow_carousel_for_all_post_images() && bsg_post_has_attached_images() ) ;
}

function bsg_is_single_post_or_page() {
  global $post ;
  if ( isset( $post ) ) {
    return ( is_single( $post->ID ) || is_page( $post->ID ) ) ;
  }
}

function bsg_options_allow_carousel_for_all_post_images() {
  $plugin_options = get_option( 'bsg_plugin_options' ) ;
  $all_posts_option = ( isset( $plugin_options[ 'bsg_allow_carousel_for_all_post_images' ] ) ) ?
    $plugin_options[ 'bsg_allow_carousel_for_all_post_images' ] : false ;
  return $all_posts_option ;
}

function bsg_post_has_attached_images() {
  $images = bsg_get_image_ids() ;
  if ( $images ) {
    return true ;
  }
}