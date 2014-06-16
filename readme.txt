=== MP Isotopes ===
Contributors: johnstonphilip, mintplugins, morduak
Donate link: http://mintplugins.com/
Tags: tags, categories, isotope, js
Requires at least: 3.5
Tested up to: 3.9
Stable tag: 1.0.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin gives you a template tag that uses isotope js functionality by David DeSandro and Metafizzy.

== Description ==

This plugin gives you a template tag that you can put on any archive, category, or tag page so that you can sort the posts using the isotope js functionality built by [David DeSandro and Metafizzy](http://isotope.metafizzy.co "Isotope JS"). It works with tags, categories, normal posts, and also the [Easy Digital Downloads](https://easydigitaldownloads.com "Easy Digital Downloads") and [WooCommerce](http://woothemes.com "WooCommerce") plugins.

To use it, just put the following code above the loop on any archive page:

`if ( function_exists( 'mp_isotopes' ) ): 
	mp_isotopes(); 
endif; `

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload the 'isotopes' folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place `<?php mp_isotopes(); ?>` in your archive, category, or tag templates

== Frequently Asked Questions ==

= What do I do with this?  =

After you install, put the following tag just before 'the loop' on any archive page (ie: archive.php), category page (ie: category.php), or tag page (ie: tag.php)

`<?php mp_isotopes(); ?>`

= Why isn't this working?  =

The theme you are using must make use of the post_class for this to function correctly. If it isn't working, there's a good chance that hasn't been utilized in your theme.

= How do I disable the built in CSS style for the tag buttons? =

In the WordPress admin area, go to Settings > Isotopes Settings and turn of the built in CSS.

= Can I use a drop down menu instead of an unordered list of links?  =

Yes. In the WordPress admin area, go to Settings > Isotopes Settings and  change it to use a drop down menu

= I'm using a custom post type and it doesn't work. How come? =

This plugin is built to work only with normal posts, the Easy Digital Downloads plugin, and the WooCommerce plugin. If you have a custom post type you would like to see added, contact me by emailing support@mintthemes.com

== Screenshots ==

1. This is a screenshot of the isotope tags being used with the twenty ten theme. If a user click on one of the tags, the posts auto re-sort themselves on the fly with a cool little animation.

2. This is a screenshot of the isotope tags being used with the armonico theme by [Mint Themes] (http://mintthemes.com "Niche WordPress Themes"). If a user click on one of the tags, the shirts auto re-sort themselves on the fly with a cool little animation.

== Changelog ==

= 1.0.0.2 = June 15, 2014
* Added relayout instead of loading on page load

= 1.0.0.1 = June 15, 2014
* Fixed error if no custom taxonomies are available.

= 1.0.0.0 = June 15, 2014
* Original release
