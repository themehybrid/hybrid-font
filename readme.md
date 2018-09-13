# Hybrid\\Font

Hybrid Font is a drop-in package that theme authors can use for loading fonts in their WordPress themes.

This package is a set of helper functions primarily for working with Google Web Fonts. The functions are simply
wrappers around existing WordPress style-loading functions. The helpers just present a standard API for handling font styles.

The idea for this package came from a [tutorial on adding Google Fonts](https://blog.josemcastaneda.com/2016/02/29/adding-removing-fonts-from-a-theme/) by Jose Castaneda.

## Requirements

* WordPress 4.9+.
* PHP 5.6+ (preferably 7+)
* [Composer](https://getcomposer.org/) for managing PHP dependencies.

Technically, you could make this work without Composer by directly downloading and dropping the package into your theme.  However, using Composer is ideal and the supported method for using this project.

## Documentation

The following docs are written with theme authors in mind because that'll be the most common use case.  If including in a plugin, it shouldn't be much different.

### Installation

**Composer:**

First, you'll need to open your command line tool and change directories to your theme folder.

```bash
cd path/to/wp-content/themes/<your-theme-name>
```

Then, use Composer to install the package.

```bash
composer require justintadlock/hybrid-font
```

Assuming you're not already including the Composer autoload file for your theme and are shipping this as part of your theme package, you'll want something like the following bit of code in your theme's `functions.php` to autoload this package (and any others).

The Composer autoload file will automatically load up Hybrid Font for you and make its code available for you to use.

```php
if ( file_exists( get_parent_theme_file_path( 'vendor/autoload.php' ) ) ) {
	require_once( get_parent_theme_file_path( 'vendor/autoload.php' ) );
}
```

**Manual:**

If manually installing the package, you simply need to put it in the desired location in your theme and include the bootstrap file like so:

```php
require_once( get_parent_theme_file_path( 'path/to/hybrid-font/src/bootstrap-font.php' ) );
```

### Usage

The primary function that you'll want to use is the `enqueue()` function.  You'd load a font like in the following example.

```php
add_action( 'wp_enqueue_scripts', function() {

	Hybrid\Font\enqueue( 'themeslug', [
		'family' => [
			'roboto'      => 'Roboto:400,400i,700,700i',
			'roboto-slab' => 'Roboto+Slab:400,700'
		],
		'subset' => [
			'latin',
			'latin-ext'
		]
	] );

} );
```

_Note that the plugin's namespace is `Hybrid\Font`.  If you're working within another namespace, you'll want to add a `use` statement after your own namespace call or call `\Hybrid\Font\enqueue()` directly.  I'll assume you know what you're doing if you're working with namespaces.  Otherwise, stick to the above._

### Parameters

The following parameters are available.

**$handle**

The first parameter is the handle/ID for the font. This should be a unique string.

**$args**

The `$args` parameter is an array of options that you may set.  The arguments

* `family` - Array of Google-style font families that you wish to load.
* `subset` - Array of Google script subsets.
* `text` - String of specific text you want Google to load the font for.
* `effect` - Array of Google font effects.
* `depends` - Array of stylesheet handles this style depends on.
* `version` - Version of the stylesheet.
* `media` - What type of screen to load this all.
* `src` - A URL to a specific stylesheet to load. Note that this will overwrite any Google-specific arguments and load this stylesheet instead.

See the [Google Fonts documentation](https://developers.google.com/fonts/docs/getting_started) for more detailed docs on using the Google-specific arguments.

### Functions

```php
// Register and load a font stylesheet.
enqueue( $handle, $args = [] );

// Register a font stylesheet.
register( $handle, $args = [] );

// Build a font stylesheet URL.
url( $handle, $args = [] );
```

### Preloading

This package will automatically filter `wp_resource_hints` and preload Google Fonts. There's no need to do this yourself.

## Copyright and License

This project is licensed under the [GNU GPL](http://www.gnu.org/licenses/old-licenses/gpl-2.0.html), version 2 or later.

2018 &copy; [Justin Tadlock](http://justintadlock.com).
