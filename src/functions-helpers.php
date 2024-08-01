<?php
/**
 * Helper functions.
 *
 * Quick and easy-to-use functions for enqueueing font stylesheets, particularly
 * from Google Web Fonts. Mostly, these are just wrappers around the style
 * functions in core WP.
 *
 * @package   HybridFont
 * @link      https://github.com/themehybrid/hybrid-font
 *
 * @author    Theme Hybrid
 * @copyright Copyright (c) 2008 - 2024, Theme Hybrid
 * @license   https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\Font;

/**
 * Registers a font.
 *
 * @param string $handle
 * @param array  $args
 * @return bool
 *
 * @uses   wp_register_style()
 */
function register( $handle, array $args = [] ) {

    $args = wp_parse_args( $args, [

        // Arguments for `wp_register_style()`.
        'depends' => [],
        'display' => '',
        'effect'  => [],
        // Arguments for https://developers.google.com/fonts/docs/getting_started
        'family'  => [],
        'media'   => 'all',
        'src'     => '', // Will overwrite Google Fonts arguments.
        'subset'  => [],
        'text'    => '',
        'version' => null,
    ] );

    $url = url( $handle, $args );

    // If there's no src and we have a family, we're loading from Google Fonts.
    if ( ! $args['src'] && $args['family'] ) {

        // Automatically filter `wp_resource_hints` to preload fonts.
        add_filter( 'wp_resource_hints', static function ( $urls, $relation_type ) use ( $handle ) {

            if ( 'preconnect' === $relation_type && is( $handle, 'queue' ) ) {

                $urls[] = [
                    'href' => 'https://fonts.gstatic.com',
                    'crossorigin',
                ];
            }

            return $urls;
        }, 10, 2 );
    }

    return wp_register_style( "{$handle}-font", $url, $args['depends'], $args['version'], $args['media'] );
}

/**
 * Deregisters a registered font.
 *
 * @param string $handle
 * @return void
 *
 * @uses   wp_deregister_style()
 */
function deregister( $handle ) {

    wp_deregister_style( "{$handle}-font" );
}

/**
 * Enqueue a registered font.  If the font is not registered, pass the `$args` to
 * register it.  See `register_font()`.
 *
 * @param string $handle
 * @param array  $args
 * @return void
 *
 * @uses   wp_enqueue_style()
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
 * @param string $handle
 * @return void
 *
 * @uses   wp_dequeue_style()
 */
function dequeue( $handle ) {

    wp_dequeue_style( "{$handle}-font" );
}

/**
 * Checks a font's status.
 *
 * @param string $handle
 * @param string $list
 * @return bool
 *
 * @uses   wp_style_is()
 */
function is( $handle, $list = 'enqueued' ) {

    return wp_style_is( "{$handle}-font", $list );
}

/**
 * Checks if a font is registered.
 *
 * @param string $handle
 * @return bool
 */
function is_registered( $handle ) {

    return is( $handle, 'registered' );
}

/**
 * Checks if a font is enqueued.
 *
 * @param string $handle
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
 * @param string $handle
 * @param array  $args
 * @return void
 */
function url( $handle, array $args = [] ) {

    $font_url   = $args['src'] ?: '';
    $query_args = [];

    if ( ! $font_url ) {

        $family  = apply_filters( "hybrid/font/{$handle}/family", $args['family'] );
        $subset  = apply_filters( "hybrid/font/{$handle}/subset", $args['subset'] );
        $text    = apply_filters( "hybrid/font/{$handle}/text", $args['text'] );
        $effect  = apply_filters( "hybrid/font/{$handle}/effect", $args['effect'] );
        $display = apply_filters( "hybrid/font/{$handle}/display", $args['display'] );

        if ( $family ) {

            $query_args['family'] = implode( '|', (array) $family );

            $allowed_display = [
                'auto',
                'block',
                'swap',
                'fallback',
                'optional',
            ];

            if ( $display && in_array( $display, $allowed_display ) ) {
                $query_args['display'] = $display;
            }

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
