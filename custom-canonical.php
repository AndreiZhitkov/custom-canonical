<?php
/*
Plugin Name: Custom canonical URL for Yoast SEO
Plugin URI:  https://github.com/AndreiZhitkov/custom-canonical
Description: Set default custom canonical URL for Yoast SEO. The setting is in Settings -> General.
Version:     1.0
Author:      Andrei Zhitkov
Author URI:  https://github.com/AndreiZhitkov
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: customcanonical
Domain Path: /languages
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// check if Yoast SEO is installed and active
function customcanonical_discover_yoast() {
	$customcanonical_yoast = FALSE;
		if ( is_plugin_active( 'wordpress-seo/wp-seo.php' ) ) {
			$customcanonical_yoast = TRUE;
		}
	return $customcanonical_yoast;
}

// prepare admin notification if Yoast SEO is not active
function customcanonical_admin_notice_noyoast() { ?>
    <div class="notice notice-error">
    <p><?php _e( '<strong>Custom canonical URL:</strong> Yoast SEO is not active', 'customcanonical' ); ?></p>
    </div>
<?php }

// deactivate if Yoast SEO is inactive, show admin notice, else set up all the params
function customcanonical_init () {
	if ( customcanonical_discover_yoast() == FALSE ) {
		add_action( 'admin_notices', 'customcanonical_admin_notice_noyoast');
		deactivate_plugins( custom-canonical( custom-canonical.php ) );
	} else {
		add_settings_section(
		'customcanonical_setting_section',
		'Default canonical URL for Yoast SEO',
		'customcanonical_section_callback',
		'general'
		);

		add_settings_field(
		'customcanonical_url',
		'Canonical URL',
		'customcanonical_setting_callback',
		'general',
		'customcanonical_setting_section'
		);

		register_setting(
		'general',
		'customcanonical_url'
		);
	}
}
add_action( 'admin_init', 'customcanonical_init' );

/* load text domain for this plugin | it will not load properly without add_action() */
function customcanonical_load_textdomain () {
		load_plugin_textdomain('custom-canonical', false, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'customcanonical_load_textdomain' );

/* all is good to go */
/* adminirtative interface */
 function customcanonical_section_callback () {
 	echo '<p>'. _e( 'Enter your canonical URL. Do not include the transfer protocol or trailing slash.', 'customcanonical' ) .'</p>';
}

function customcanonical_setting_callback () {
 	//echo '<form method="post" action="options.php">';
	//settings_fields( 'permalink' );
 	echo "<input name='customcanonical_url' id='customcanonical_url' type='text' value='". get_option('customcanonical_url') ."' class='regular-text code' placeholder='yourcanonical.url' /><code>/your-pretty-permalink</code>";
}

apply_filters( 'sanitize_option_customcanonical_url', 'customcanonical_url', 'customcanonical_url' );

/* Attribution:
* Plugin Name: WPSE WPSEO Canonical
* Plugin URI: http://wordpress.stackexchange.com
* Description: Changes canonical url domain.
* Author: Sisir
* Version: 1.0
* Author URI: http://developerpage.net
**/

// check if all is configured - the URL is set and in WP options database
$customcanonical_domain = get_option('customcanonical_url', '');

// substitute URL for the custom canonical here
function customcanonical_domain_replace ($url){
	global $customcanonical_domain;
	$parsed = parse_url(home_url());
	$current_site_domain = $parsed['host'];
	return str_replace($current_site_domain, $customcanonical_domain, $url);
}

// do noting if the cusom canonical URL is not actually sonfigured yet
function customcanonical_domain_set () {
	global $customcanonical_domain;
		if ( empty( $customcanonical_domain )) {
			return;
		} else {
			add_filter('wpseo_canonical', 'customcanonical_domain_replace');
			add_filter('wpseo_prev_rel_link', 'customcanonical_domain_replace');
			add_filter('wpseo_next_rel_link', 'customcanonical_domain_replace');
		}
}
add_action( 'plugins_loaded', 'customcanonical_domain_set' );
?>
