<?php
/**
 * Helper functions.
 *
 * Quick and easy-to-use functions for enqueueing font stylesheets, particularly
 * from Google Web Fonts. Mostly, these are just wrappers around the style
 * functions in core WP.
 *
 * @package   HybridFont
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2018, Justin Tadlock
 * @link      https://github.com/justintadlock/hybrid-font
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\Font;

/**
 * Registers a font.
 *
 * @uses   wp_register_style()
 * @since  5.0.0
 * @access public
 * @param  string  $handle
 * @param  array   $args
 * @return bool
 */
function register( $handle, array $args = [] ) {

	$args = wp_parse_args( $args, [
		// Arguments for https://developers.google.com/fonts/docs/getting_started
		'family'  => [],
		'subset'  => [],
		'text'    => '',
		'effect'  => [],

		// Arguments for `wp_register_style()`.
		'depends' => [],
		'version' => false,
		'media'   => 'all',
		'src'     => ''     // Will overwrite Google Fonts arguments.
	] );

	$url = url( $handle, $args );

	// If there's no src and we have a family, we're loading from Google Fonts.
	if ( ! $args['src'] && $args['family'] ) {

		// Automatically filter `wp_resource_hints` to preload fonts.
		add_filter( 'wp_resource_hints', function( $urls, $relation_type ) use ( $handle ) {

			if ( 'preconnect' === $relation_type && is( $handle, 'queue' ) ) {

				$urls[] = [
					'href' => 'https://fonts.gstatic.com',
					'crossorigin'
				];
			}

			return $urls;

		}, 10, 2 );
	}

	return wp_register_style(
		"{$handle}-font",
		$url,
		$args['depends'],
		$args['version'],
		$args['media']
	);
}

/**
 * Deregisters a registered font.
 *
 * @uses   wp_deregister_style()
 * @since  5.0.0
 * @access public
 * @param  string  $handle
 * @return void
 */
function deregister( $handle ) {

	wp_deregister_style( "{$handle}-font" );
}

/**
 * Enqueue a registered font.  If the font is not registered, pass the `$args` to
 * register it.  See `register_font()`.
 *
 * @uses   wp_enqueue_style()
 * @since  5.0.0
 * @access public
 * @param  string  $handle
 * @param  array   $args
 * @return void
 */
function enqueue( $handle, array $args = [] ) {

	if ( ! is_registered( $handle ) ) {
		register( $handle, $args );
	}

	wp_enqueue_style( "{$handle}-font" );
}

/**
 * Dequeues a font.
 *
 * @uses   wp_dequeue_style()
 * @since  5.0.0
 * @access public
 * @param  string  $handle
 * @return void
 */
function dequeue( $handle ) {

	wp_dequeue_style( "{$handle}-font" );
}

/**
 * Checks a font's status.
 *
 * @uses   wp_style_is()
 * @since  5.0.0
 * @access public
 * @param  string  $handle
 * @param  string  $list
 * @return bool
 */
function is( $handle, $list = 'enqueued' ) {

	return wp_style_is( "{$handle}-font", $list );
}

/**
 * Checks if a font is registered.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $handle
 * @return bool
 */
function is_registered( $handle ) {

	return is( $handle, 'registered' );
}

/**
 * Checks if a font is enqueued.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $handle
 * @return bool
 */
function is_enqueued( $handle ) {

	return is( $handle, 'enqueued' );
}

/**
 * Helper function for creating the Google Fonts URL.  Note that `add_query_arg()`
 * will call `urlencode_deep()`, so we're going to leaving the encoding to
 * that function.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $handle
 * @param  array   $args
 * @return void
 */
function url( $handle, array $args = [] ) {

	$font_url   = $args['src'] ?: '';
	$query_args = [];

	if ( ! $font_url ) {

		$family = apply_filters( "hybrid/font/{$handle}/family", $args['family'] );
		$subset = apply_filters( "hybrid/font/{$handle}/subset", $args['subset'] );
		$text   = apply_filters( "hybrid/font/{$handle}/text",   $args['text']   );
		$effect = apply_filters( "hybrid/font/{$handle}/effect", $args['effect'] );

		if ( $family ) {

			$query_args['family'] = implode( '|', (array) $family );

			if ( $subset ) {
				$query_args['subset'] = implode( ',', (array) $subset );
			}

			if ( $text ) {
				$query_args['text'] = $text;
			}

			if ( $effect ) {
				$query_args['effect'] = implode( '|', (array) $effect );
			}

			$font_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
		}
	}

	return esc_url(
		apply_filters( "hybrid/font/{$handle}/url", $font_url, $args, $query_args )
	);
}
