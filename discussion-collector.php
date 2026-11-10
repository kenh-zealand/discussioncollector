<?php
/**
 * Plugin Name:       Discussion Collector
 * Plugin URI:        https://github.com/kenh-zealand/discussioncollector
 * Description:       Kodebeskyttet opsamlingsside (/opsamling/), hvor deltagere skriver indlæg, og Claude (Anthropic) samler dem i temaer. Med ordsky og PDF-eksport.
 * Version:           1.3
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Kenneth Hansen
 * Text Domain:       discussion-collector
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Hvis opsamlingen allerede er indlæst andetsteds (fx fra et tema), stopper pluginet for at undgå dobbelt kode.
if ( function_exists( 'dcol_kode' ) ) {
	add_action( 'admin_notices', function () {
		echo '<div class="notice notice-warning"><p><strong>Discussion Collector:</strong> Opsamlingen er allerede indlæst et andet sted (fx fra en anden kopi af pluginet eller fra temaet). Fjern den ene for at undgå dobbelt kode.</p></div>';
	} );
	return;
}

define( 'DCOL_PLUGIN_FIL', __FILE__ );

require_once __DIR__ . '/inc/opsamling.php';

/* Siden /opsamling/ vises med pluginets egen skabelon – uanset tema. */
add_filter( 'template_include', function ( $skabelon ) {
	if ( is_page( 'opsamling' ) ) {
		return __DIR__ . '/templates/page-opsamling.php';
	}
	return $skabelon;
}, 99 );

/* Ved aktivering: opret siden /opsamling/, hvis den ikke findes. */
register_activation_hook( __FILE__, function () {
	if ( get_page_by_path( 'opsamling' ) ) {
		return;
	}
	wp_insert_post( array(
		'post_type'    => 'page',
		'post_status'  => 'publish',
		'post_title'   => 'Opsamling',
		'post_name'    => 'opsamling',
		'post_content' => 'Skriv dit bidrag nedenfor. Underviseren samler indlæggene i en opsamling.',
	) );
} );

/* Genvej til indstillingerne fra plugin-listen. */
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), function ( $links ) {
	array_unshift( $links, '<a href="' . esc_url( admin_url( 'options-general.php?page=discussion-collector' ) ) . '">Indstillinger</a>' );
	return $links;
} );
