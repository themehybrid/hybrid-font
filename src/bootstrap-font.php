<?php
/**
 * Bootsraps the project.
 *
 * @package   HybridFont
 * @link      https://github.com/themehybrid/hybrid-font
 *
 * @author    Theme Hybrid
 * @copyright Copyright (c) 2008 - 2023, Theme Hybrid
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// Check if the package has been bootstrapped. If not, load the bootstrap files
// and get the package set up.
if ( ! defined( 'HYBRID_FONT_BOOTSTRAPPED' ) ) {

    // Autoload functions.
    require_once __DIR__ . '/functions-helpers.php';

    define( 'HYBRID_FONT_BOOTSTRAPPED', true );
}
