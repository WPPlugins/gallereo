<?php

class Gallereo_Plugin {

	const NAME = 'gallereo';
	const VERSION = '1.0.0.29';

	private static $_instance = null;

	private $_modules = array();

	private function __construct() {
		$this->_modules['render'] = new Gallereo_Module_Render();
	}

	private function __clone() {

	}

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new Gallereo_Plugin();
		}

		return self::$_instance;
	}

}