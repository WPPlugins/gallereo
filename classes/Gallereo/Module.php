<?php

class Gallereo_Module {

	/**
	 * The instance of wpdb class.
	 *
	 * @access protected
	 * @var wpdb
	 */
	protected $_wpdb = null;

	/**
	 * Constructor.
	 *
	 * @access public
	 * @global wpdb $wpdb The instance of the wpdb class.
	 */
	public function __construct() {
		global $wpdb;

		$this->_wpdb = $wpdb;
	}

	/**
	 * Register action hook.
	 *
	 * @access protected
	 * @param string $tag The name of the action to which the $method is hooked.
	 * @param string $method The name of the method to be called.
	 * @param int $priority optional. Used to specify the order in which the functions associated with a particular action are executed (default: 10). Lower numbers correspond with earlier execution, and functions with the same priority are executed in the order in which they were added to the action.
	 * @param int $accepted_args optional. The number of arguments the function accept (default 1).
	 * @return Gallereo_Module
	 */
	protected function _addAction( $tag, $method, $priority = 10, $accepted_args = 1 ) {
		add_action( $tag, array( $this, $method ), $priority, $accepted_args );
		return $this;
	}

	/**
	 * Register filter hook.
	 *
	 * @access protected
	 * @param string $tag The name of the filter to hook the $method to.
	 * @param type $method The name of the method to be called when the filter is applied.
	 * @param int $priority optional. Used to specify the order in which the functions associated with a particular action are executed (default: 10). Lower numbers correspond with earlier execution, and functions with the same priority are executed in the order in which they were added to the action.
	 * @param int $accepted_args optional. The number of arguments the function accept (default 1).
	 * @return Gallereo_Module
	 */
	protected function _addFilter( $tag, $method, $priority = 10, $accepted_args = 1 ) {
		add_filter( $tag, array( $this, $method ), $priority, $accepted_args );
		return $this;
	}

	/**
	 * Add hook for shortcode tag.
	 *
	 * @access protected
	 * @param string $tag Shortcode tag to be searched in post content.
	 * @param string $method Hook to run when shortcode is found.
	 * @return Gallereo_Module
	 */
	protected function _addShortcode( $tag, $method ) {
		add_shortcode( $tag, array( $this, $method ) );
		return $this;
	}

}