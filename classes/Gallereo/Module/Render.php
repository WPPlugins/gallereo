<?php

class Gallereo_Module_Render extends Gallereo_Module {

	private static $_instance = 0;
	private $_images = array();

	public function __construct() {
		parent::__construct();

		$this->_addAction( 'wp_enqueue_scripts', 'enqueueScripts' );
		$this->_addAction( 'wp_footer', 'enqueueImages' );
		$this->_addAction( 'init', 'loadTextDomain' );

		$this->_addFilter( 'post_gallery', 'renderGallery', 99, 2 );

		if ( !has_filter( 'widget_text', 'do_shortcode' ) ) {
			add_filter( 'widget_text', 'do_shortcode' );
		}

		if ( !has_filter( 'term_description', 'do_shortcode' ) ) {
			add_filter( 'term_description', 'do_shortcode' );
		}
	}

	public function loadTextDomain() {
		load_plugin_textdomain( Gallereo_Plugin::NAME, false, dirname( GALLEREO_BASENAME ) . '/languages/' );
	}

	public function enqueueScripts() {
		wp_register_style( 'gallereo', GALLEREO_ABSURL . 'css/styles.css', array(), Gallereo_Plugin::VERSION );

		wp_register_script( 'gallereo-lazyload', GALLEREO_ABSURL . 'js/lazyload.js', array( 'jquery' ), '1.8.4', true );
		wp_register_script( 'gallereo', GALLEREO_ABSURL . 'js/scripts.js', array( 'gallereo-lazyload' ), Gallereo_Plugin::VERSION, true );
	}

	public function enqueueImages() {
		if ( !empty( $this->_images ) ) {
			wp_localize_script( 'gallereo', 'gallereo', array(
				'path' => array( 'images' => GALLEREO_ABSURL . 'images/' ),
				'data' => $this->_images,
				'l10n' => array(
					'close'    => esc_html__( 'Close', Gallereo_Plugin::NAME ),
					'next'     => esc_html__( 'Next', Gallereo_Plugin::NAME ),
					'previous' => esc_html__( 'Previous', Gallereo_Plugin::NAME ),
				),
			) );
		}
	}

	public function renderGallery( $output, $attr ) {
		if ( is_feed() ) {
			return '';
		}

		if ( isset( $attr['orderby'] ) ) {
			$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
			if ( !$attr['orderby'] ) {
				unset( $attr['orderby'] );
			}
		}

		$post = get_post();
		$attr = shortcode_atts( array(
			'order'   => 'ASC',
			'orderby' => 'menu_order ID',
			'id'      => !is_null( $post ) ? $post->ID : '',
			'size'    => 'medium',
			'include' => '',
			'exclude' => ''
		), $attr );

		if ( 'RAND' == $attr['order'] ) {
			$attr['orderby'] = 'none';
		}

		$attachments = $this->_getAttachments( intval( $attr['id'] ), $attr['include'], $attr['exclude'], $attr['orderby'], $attr['order'] );
		if ( empty( $attachments ) ) {
			return '';
		}

		wp_enqueue_style( 'gallereo' );
		wp_enqueue_script( 'gallereo' );

		$images = array();
		foreach ( $attachments as $id => $attachment ) {
			$title = trim( strip_tags( $attachment->post_excerpt ) );
			$image = wp_get_attachment_image_src( $id, $attr['size'] );
			if ( is_array( $image ) && count( $image ) >= 3 ) {
				$image = array_combine( array( 'url', 'width', 'height' ), array_slice( $image, 0, 3 ) );
				$image['title'] = !empty( $title ) ? $title : '';
				$image['description'] = trim( $attachment->post_content ) ? wptexturize( $attachment->post_content ) : '';

				$full = wp_get_attachment_image_src( $id, 'large' );
				$image['link'] = $full[0];

				$images[] = $image;
			}
		}

		$this->_images['gallereo-' . ++self::$_instance] = $images;

		return sprintf( '<div id="gallereo-%d" class=gallereo></div>', self::$_instance );
	}

	private function _getAttachments( $id, $include, $exclude, $orderby, $order ) {
		$attachments = array();

		if ( !empty( $include ) ) {
			$_attachments = get_posts( array(
				'include'        => $include,
				'post_status'    => 'inherit',
				'post_type'      => 'attachment',
				'post_mime_type' => 'image',
				'order'          => $order,
				'orderby'        => $orderby,
			) );

			foreach ( $_attachments as $val ) {
				$attachments[$val->ID] = $val;
			}
		} elseif ( !empty( $exclude ) ) {
			$attachments = get_children( array(
				'post_parent'    => $id,
				'exclude'        => $exclude,
				'post_status'    => 'inherit',
				'post_type'      => 'attachment',
				'post_mime_type' => 'image',
				'order'          => $order,
				'orderby'        => $orderby,
			) );
		} else {
			$attachments = get_children( array(
				'post_parent'    => $id,
				'post_status'    => 'inherit',
				'post_type'      => 'attachment',
				'post_mime_type' => 'image',
				'order'          => $order,
				'orderby'        => $orderby,
			) );
		}

		return $attachments;
	}

}