<?php 
/* 
Plugin Name: WooCommerce Retina Picturefill
Description: Adds WP Retina 2x picturefill compatibility for WooCommerce variable product images.
Version: 1.0.0
Author: Jeremy Moseley
License: GPLv2 or later
Text Domain: wcrp
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

Copyright 2015 Jeremy Moseley
*/

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );

if ( in_array( 'woocommerce/woocommerce.php', $active_plugins ) && in_array( 'wp-retina-2x/wp-retina-2x.php', $active_plugins ) )  : 

function wcrp_get_srcset_callback() {
	if ( ! wp_verify_nonce( $_POST['nonce'], 'wcrp-nonce') ) {
		wp_die();
	}
	$method = wr2x_getoption( 'method', 'wr2x_advanced', 'Picturefill' );
	if ( $method == 'Picturefill' ) {
		$retina_url = wr2x_get_retina_from_url( $_POST['src'] );
		$retina_url = apply_filters( 'wr2x_img_retina_url', $retina_url );
		if ( $retina_url != null ) {
			$retina_url = wr2x_cdn_this( $retina_url );
			$img_url = wr2x_cdn_this( $_POST['src'] );
			$img_url  = apply_filters( 'wr2x_img_url', $img_url  );
			echo  "$img_url, $retina_url 2x";	
		}
		else {
			echo $_POST['src'];
		}
	}
	wp_die();
}

add_action( 'wp_ajax_get_srcset', 'wcrp_get_srcset_callback' );
add_action( 'wp_ajax_nopriv_get_srcset', 'wcrp_get_srcset_callback' );

function wcvp_enqueue_scripts() { 
	
	wp_enqueue_script(
		'wcrp', 
		plugins_url( '/js/wcrp.min.js', __FILE__ ), 
		array( 'jquery' ), 
		false, 
		true
	);
	wp_localize_script(
		'wcrp',
		'wcrp',
		array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'wcrp-nonce' )
		)
	);
   
} 
add_action( 'wp_enqueue_scripts', 'wcrp_enqueue_scripts' ); 

endif;