<?php
/*
Plugin Name: Gallereo!
Plugin URI: 
Description: A simple and nice gallery plugin with fancy preview, which conversts your galleries design in a Google+ like style.
Version: 1.0.0
Author: Eugene Manuilov
Author URI: http://manuilov.org
License: Regular or Extended License
License URI: http://codecanyon.net/licenses
*/

define( 'GALLEREO_ABSURL', plugins_url( '/', __FILE__ ) );
define( 'GALLEREO_BASENAME', plugin_basename( __FILE__ ) );

spl_autoload_register( 'gallereo_autoloader' );
function gallereo_autoloader( $class ) {
	$filename =  dirname( __FILE__ ) . str_replace( '_', DIRECTORY_SEPARATOR, "_classes_{$class}.php" );
	if ( is_readable( $filename ) ) {
		require $filename;
		return true;
	}

	return false;
}

Gallereo_Plugin::instance();