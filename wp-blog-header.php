<?php
/**
 * Loads the WordPress environment and template.
 *
 * @package WordPress
 */

//wp-blog-header.php根据博客参数定义博客页面显示内容

if ( !isset($wp_did_header) ) {
//	如果wp_did_header为空
	$wp_did_header = true;

	// Load the WordPress library.what is WordPress library?
//	引入一次wp-load.php
	require_once( dirname(__FILE__) . '/wp-load.php' );

	// Set up the WordPress query.
	wp();

	// Load the theme template.
	require_once( ABSPATH . WPINC . '/template-loader.php' );

}
