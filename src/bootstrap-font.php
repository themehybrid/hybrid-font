<?php
/**
 * Boostraps the project.
 *
 * @package   HybridFont
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2018, Justin Tadlock
 * @link      https://github.com/justintadlock/hybrid-font
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

# Check if the package has been bootstrapped. If not, load the bootstrap files
# and get the package set up.
if ( ! defined( 'HYBRID_FONT_BOOTSTRAPPED' ) ) {

	// Autoload functions.
	require_once( __DIR__ . '/functions-helpers.php' );

	define( 'HYBRID_FONT_BOOTSTRAPPED', true );
}
