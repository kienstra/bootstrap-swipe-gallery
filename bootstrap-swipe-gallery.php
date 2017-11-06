<?php
/**
 * Instantiates the Bootstrap Swipe Gallery Plugin
 *
 * @package BootstrapSwipeGallery
 */

namespace BootstrapSwipeGallery;

/*
Plugin Name: Bootstrap Swipe Gallery
Plugin URI: www.ryankienstra.com/bootstrap-swipe-gallery
Description: Swipe through gallery images on touch devices. Image sizes adjust to screen size. Requires a theme with Bootstrap 3 or higher.

Version: 1.0.5
Author: Ryan Kienstra
Author URI: www.ryankienstra.com
License: GPL2
*/

require_once dirname( __FILE__ ) . '/php/class-plugin.php';
$plugin = Plugin::get_instance();
$plugin->init();
