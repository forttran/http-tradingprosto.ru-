<?php
/**
 * Admin Functions
 *
 * Admin area specific function like registering scripts.
 *
 * @package      responsive_mobile
 * @license      license.txt
 * @copyright    2014 CyberChimps Inc
 * @since        0.0.1
 *
 * Please do not edit this file. This file is part of the responsive_mobile Framework and all modifications
 * should be made in a child theme.
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Add stylesheet and JS for upsell page.
function cyberchimps_admin_style() {
	$suffix  = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	$rtl     = ( is_rtl() ) ? '-rtl' : '';
	$template_directory_uri = get_template_directory_uri();

	wp_register_style( 'responsive-bootstrap', $template_directory_uri . '/core/bootstrap/stylesheets/bootstrap' . $rtl . $suffix . '.css', false, '3.1.1' );
	// @TODO Check that it works for RTL too
	wp_register_style( 'upsell-style', $template_directory_uri . '/core/css/upsell' . $suffix . '.css', array(), '2.0.0' );
	wp_register_style( 'responsive-theme-options', $template_directory_uri . '/libraries/css/theme-options' . $suffix . '.css', array(), '2.0.0' );
	wp_register_script( 'responsive-theme-options', $template_directory_uri . '/libraries/js/theme-options' . $suffix . '.js', array( 'jquery' ), '2.0.0' );
}
add_action( 'admin_enqueue_scripts', 'cyberchimps_admin_style' );
