<?php
/**
 * Opsamling – selvstændig prototype af en Howspace-lignende opsamlingsblok.
 *
 * - Siden /opsamling/ er beskyttet med en fælles kode (DCOL_KODE).
 * - Deltagernes indlæg gemmes som private indlæg af typen dcol_indlaeg.
 * - Et autogenereret, privat dokument ("Samlet opsamling") indeholder alle
 *   indlæg i ét, så Claude kan læse dem med ét kald.
 * - AI-analysen skrives af Claude som privat indlæg af typen dcol_opsamling
 *   (indhold = analysen i HTML, uddrag = prompten). Den senest ændrede vises.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Deltagerkoden sættes under Indstillinger → Opsamling,
// eller i wp-config.php med: define( 'DCOL_KODE', 'din-kode' );
const DCOL_COOKIE  = 'dcol_adgang';
const DCOL_VERSION = '1.4';

/* Deltagerkoden: DCOL_KODE (fx i wp-config.php) går forud; ellers Indstillinger → Opsamling. */
function dcol_kode() {
	if ( defined( 'DCOL_KODE' ) && '' !== (string) DCOL_KODE ) {
		return strtolower( trim( (string) DCOL_KODE ) );
	}
	return strtolower( trim( (string) get_option( 'dcol_kode', '' ) ) );
}

/* ── Indholdstyper ─────────────────────────────────────────────────────── */

function dcol_register_types() {
	$common = array(
		'public'              => true,
		'publicly_queryable'  => false,
		'exclude_from_search' => true,
		'show_in_nav_menus'   => false,
		'show_ui'             => true,
		'show_in_rest'        => false,
		'has_archive'         => false,
		'rewrite'             => false,
		'query_var'           => false,
		'supports'            => array( 'title', 'editor', 'excerpt' ),
	);
	register_post_type( 'dcol_indlaeg', array_merge( $common, array(
		'labels'    => array( 'name' => 'Opsamling: indlæg', 'singular_name' => 'Indlæg' ),
		'menu_icon' => 'dashicons-format-chat',
	) ) );
	register_post_type( 'dcol_opsamling', array_merge( $common, array(
		'labels'    => array( 'name' => 'Opsamling: analyse', 'singular_name' => 'Analyse' ),
		'menu_icon' => 'dashicons-lightbulb',
	) ) );
}
add_action( 'init', 'dcol_register_types' );

/* ── Adgangskode ───────────────────────────────────────────────────────── */

function dcol_token() {
	return hash_hmac( 'sha256', 'opsamling|' . dcol_kode(), wp_salt( 'auth' ) );
}

/* Kun WordPress-administratorer må opdatere, nulstille og ændre prompten. */
function dcol_er_admin() {
	return current_user_can( 'manage_options' );
}

function dcol_er_logget_ind() {
	return dcol_er_admin() || isset( $_COOKIE[ DCOL_COOKIE ] )
		&& hash_equals( dcol_token(), (string) $_COOKIE[ DCOL_COOKIE ] );
}

function dcol_haandter_login() {
	if ( ! is_page( 'opsamling' ) || 'POST' !== ( $_SERVER['REQUEST_METHOD'] ?? '' ) || ! isset( $_POST['dcol_kode'] ) ) {
		return;
	}
	$kode = strtolower( trim( wp_unslash( (string) $_POST['dcol_kode'] ) ) );
	if ( '' !== dcol_kode() && hash_equals( dcol_kode(), $kode ) ) {
		setcookie( DCOL_COOKIE, dcol_token(), array(
			'expires'  => time() + 30 * DAY_IN_SECONDS,
			'path'     => '/',
			'secure'   => is_ssl(),
			'httponly' => true,
			'samesite' => 'Lax',
		) );
		wp_safe_redirect( get_permalink( get_queried_object_id() ) );
		exit;
	}
	$GLOBALS['dcol_fejl'] = true;
}
add_action( 'template_redirect', 'dcol_haandter_login' );

/* ── Data ──────────────────────────────────────────────────────────────── */

function dcol_hent_indlaeg() {
	return get_posts( array(
		'post_type'        => 'dcol_indlaeg',
		'post_status'      => 'private',
		'numberposts'      => 1000,
		'orderby'          => 'date',
		'order'            => 'ASC',
		'suppress_filters' => true,
	) );
}

const DCOL_STANDARDPROMPT = 'Organisér indlæggene og udled de gennemgående temaer. Indlæg, der siger det samme med forskellig ordlyd, hører til samme tema. Giv hvert tema en kort overskrift og 2-4 punkter.';

function dcol_prompt() {
	$p = (string) get_option( 'dcol_prompt', '' );
	return '' !== trim( $p ) ? $p : DCOL_STANDARDPROMPT;
}

function dcol_samlet_id() {
	return (int) get_option( 'dcol_samlet_id', 0 );
}

function dcol_analyse() {
	$p = get_posts( array(
		'post_type'        => 'dcol_opsamling',
		'post_status'      => array( 'private', 'publish' ),
		'numberposts'      => 1,
		'orderby'          => 'modified',
		'order'            => 'DESC',
		'post__not_in'     => array( dcol_samlet_id() ),
		'suppress_filters' => true,
	) );
	return $p ? $p[0] : null;
}

/* Det samlede dokument, som Claude læser. Genskabes, når indlæg ændres. */
function dcol_opdater_samlet() {
	$linjer = array();
	$i      = 0;
	foreach ( dcol_hent_indlaeg() as $p ) {
		$i++;
		$tid      = get_date_from_gmt( $p->post_date_gmt, 'd.m.Y H:i' );
		$linjer[] = $i . '. [' . $tid . '] ' . $p->post_title . ': ' . preg_replace( '/\s+/', ' ', $p->post_content );
	}
	$bestilt = dcol_anmodning_aaben();
	$indhold = ( $bestilt ? 'STATUS: Ny opsamling bestilt ' . get_date_from_gmt( gmdate( 'Y-m-d H:i:s', $bestilt ), 'd.m.Y H:i' ) . "\n\n" : "STATUS: Ingen bestilling\n\n" )
		. 'PROMPT (brug denne til opsamlingen): ' . dcol_prompt() . "\n\n"
		. 'Antal indlæg: ' . $i . "\n\n" . implode( "\n", $linjer );

	$id   = dcol_samlet_id();
	$data = array(
		'post_type'    => 'dcol_opsamling',
		'post_status'  => 'private',
		'post_title'   => wp_slash( 'Samlet opsamling (autogenereret – rediger ikke)' ),
		'post_name'    => 'samlet',
		'post_content' => wp_slash( $indhold ),
	);
	if ( $id && get_post( $id ) ) {
		$data['ID'] = $id;
		wp_update_post( $data );
	} else {
		$ny = wp_insert_post( $data );
		if ( $ny && ! is_wp_error( $ny ) ) {
			update_option( 'dcol_samlet_id', (int) $ny, false );
		}
	}
}

function dcol_marker_aendring( $post_id ) {
	$type = get_post_type( $post_id );
	$ok   = 'dcol_indlaeg' === $type || ( 'dcol_opsamling' === $type && (int) $post_id !== dcol_samlet_id() );
	if ( ! $ok ) {
		return;
	}
	if ( ! has_action( 'shutdown', 'dcol_opdater_samlet' ) ) {
		add_action( 'shutdown', 'dcol_opdater_samlet' );
	}
}
add_action( 'save_post', 'dcol_marker_aendring' );
add_action( 'trashed_post', 'dcol_marker_aendring' );
add_action( 'untrashed_post', 'dcol_marker_aendring' );
add_action( 'before_delete_post', 'dcol_marker_aendring' );

/* ── REST-endepunkter (kræver koden) ───────────────────────────────────── */

add_action( 'rest_api_init', function () {
	register_rest_route( 'discussion-collector/v1', '/feed', array(
		'methods'             => 'GET',
		'callback'            => 'dcol_feed',
		'permission_callback' => 'dcol_er_logget_ind',
	) );
	register_rest_route( 'discussion-collector/v1', '/indlaeg', array(
		'methods'             => 'POST',
		'callback'            => 'dcol_nyt',
		'permission_callback' => 'dcol_er_logget_ind',
	) );
	register_rest_route( 'discussion-collector/v1', '/prompt', array(
		'methods'             => 'POST',
		'callback'            => 'dcol_gem_prompt',
		'permission_callback' => 'dcol_er_admin',
	) );
	register_rest_route( 'discussion-collector/v1', '/anmod', array(
		'methods'             => 'POST',
		'callback'            => 'dcol_anmod',
		'permission_callback' => 'dcol_er_admin',
	) );
	register_rest_route( 'discussion-collector/v1', '/nulstil', array(
		'methods'             => 'POST',
		'callback'            => 'dcol_nulstil',
		'permission_callback' => 'dcol_er_admin',
	) );
} );

/* ── "Opdater": Claude analyserer indlæggene ud fra prompten (Anthropic API) ── */

function dcol_api_noegle() {
	if ( defined( 'DCOL_ANTHROPIC_KEY' ) && DCOL_ANTHROPIC_KEY ) {
		return DCOL_ANTHROPIC_KEY;
	}
	return trim( (string) get_option( 'dcol_api_key', '' ) );
}

function dcol_model() {
	$m = trim( (string) get_option( 'dcol_model', '' ) );
	return '' !== $m ? $m : 'claude-sonnet-5-5';
}

function dcol_anmod() {
	$noegle = dcol_api_noegle();
	if ( '' === $noegle ) {
		return new WP_Error( 'ingen_noegle', 'Opsamlingen er ikke sat op endnu (mangler API-nøgle).', array( 'status' => 503 ) );
	}
	$indlaeg = dcol_hent_indlaeg();
	if ( ! $indlaeg ) {
		return new WP_Error( 'ingen_indlaeg', 'Der er ingen indlæg at opsamle endnu.', array( 'status' => 400 ) );
	}
	if ( get_transient( 'dcol_koerer' ) ) {
		return new WP_Error( 'koerer', 'Opsamlingen er allerede ved at blive opdateret – vent et øjeblik.', array( 'status' => 409 ) );
	}
	set_transient( 'dcol_koerer', 1, 180 );
	if ( function_exists( 'set_time_limit' ) ) {
		@set_time_limit( 180 );
	}

	$prompt = dcol_prompt();
	$tekst  = '';
	$i      = 0;
	foreach ( $indlaeg as $p ) {
		$i++;
		$tekst .= $i . '. ' . html_entity_decode( preg_replace( '/\s+/', ' ', $p->post_content ), ENT_QUOTES, 'UTF-8' ) . "\n";
	}

	$system = 'Du laver opsamlinger af deltagerindlæg til en undervisnings- eller workshopside. '
		. 'Følg underviserens prompt nøje. Skriv på dansk. Gengiv ikke navne på deltagere. '
		. 'Svar KUN med HTML: brug <h3> til overskrifter og <ul><li> til punkter, evt. <p> og <strong>. '
		. 'Ingen indledning, ingen afslutning, ingen kodeblokke.';
	$besked = "PROMPT FRA UNDERVISEREN:\n" . $prompt . "\n\nINDLÆG (" . $i . " i alt):\n" . $tekst;

	$svar = wp_remote_post( 'https://api.anthropic.com/v1/messages', array(
		'timeout' => 150,
		'headers' => array(
			'x-api-key'         => $noegle,
			'anthropic-version' => '2023-06-01',
			'content-type'      => 'application/json',
		),
		'body'    => wp_json_encode( array(
			'model'      => dcol_model(),
			'max_tokens' => 4000,
			'system'     => $system,
			'messages'   => array( array( 'role' => 'user', 'content' => $besked ) ),
		) ),
	) );
	delete_transient( 'dcol_koerer' );

	if ( is_wp_error( $svar ) ) {
		return new WP_Error( 'api', 'Kunne ikke nå Claude: ' . $svar->get_error_message(), array( 'status' => 502 ) );
	}
	$kode = wp_remote_retrieve_response_code( $svar );
	$data = json_decode( wp_remote_retrieve_body( $svar ), true );
	if ( 200 !== $kode ) {
		$fejl = isset( $data['error']['message'] ) ? $data['error']['message'] : 'HTTP ' . $kode;
		return new WP_Error( 'api', 'Claude svarede med en fejl: ' . $fejl, array( 'status' => 502 ) );
	}
	$html = '';
	foreach ( (array) ( $data['content'] ?? array() ) as $blok ) {
		if ( 'text' === ( $blok['type'] ?? '' ) ) {
			$html .= $blok['text'];
		}
	}
	$html = trim( preg_replace( '/^```(?:html)?\s*|\s*```$/i', '', trim( $html ) ) );
	$html = wp_kses( $html, array( 'h3' => array(), 'h4' => array(), 'ul' => array(), 'ol' => array(), 'li' => array(), 'p' => array(), 'strong' => array(), 'em' => array(), 'br' => array() ) );
	if ( '' === $html ) {
		return new WP_Error( 'tom', 'Claude returnerede en tom opsamling – prøv igen.', array( 'status' => 502 ) );
	}

	$a    = dcol_analyse();
	$post = array(
		'post_type'    => 'dcol_opsamling',
		'post_status'  => 'private',
		'post_title'   => 'Analyse',
		'post_name'    => 'analyse',
		'post_excerpt' => wp_slash( $prompt ),
		'post_content' => wp_slash( $html ),
	);
	if ( $a ) {
		$post['ID'] = $a->ID;
		$analyse_id = wp_update_post( $post );
	} else {
		$analyse_id = wp_insert_post( $post );
	}
	if ( $analyse_id && ! is_wp_error( $analyse_id ) ) {
		update_post_meta( $analyse_id, 'dcol_model', dcol_model() ); // Hvilken model lavede opsamlingen.
	}
	update_option( 'dcol_anmodet', 0, false );
	return array( 'ok' => true );
}

/* Indstillinger → Opsamling: API-nøgle og model. */
add_action( 'admin_menu', function () {
	add_options_page( 'Opsamling', 'Opsamling', 'manage_options', 'discussion-collector', 'dcol_indstillinger_side' );
} );
add_action( 'admin_init', function () {
	register_setting( 'discussion_collector', 'dcol_api_key', array(
		'type'              => 'string',
		'sanitize_callback' => function ( $v ) {
			$v = trim( (string) $v );
			return '' === $v ? (string) get_option( 'dcol_api_key', '' ) : $v; // Tomt felt = behold nuværende nøgle.
		},
	) );
	register_setting( 'discussion_collector', 'dcol_model', array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ) );
	register_setting( 'discussion_collector', 'dcol_kode', array(
		'type'              => 'string',
		'sanitize_callback' => function ( $v ) {
			return strtolower( trim( sanitize_text_field( (string) $v ) ) );
		},
	) );
} );
function dcol_indstillinger_side() {
	$har = '' !== dcol_api_noegle();
	?>
	<div class="wrap">
		<h1>Opsamling – Claude</h1>
		<p>Bruges af "Opdater"-knappen på <a href="<?php echo esc_url( home_url( '/opsamling/' ) ); ?>" target="_blank">/opsamling/</a>. Nøglen oprettes på console.anthropic.com og vises aldrig på siden.</p>
		<form method="post" action="options.php">
			<?php settings_fields( 'discussion_collector' ); ?>
			<table class="form-table">
				<tr><th scope="row"><label for="dcol_api_key">Anthropic API-nøgle</label></th>
					<td><input type="password" id="dcol_api_key" name="dcol_api_key" class="regular-text" autocomplete="off" placeholder="<?php echo $har ? 'Gemt – lad feltet stå tomt for at beholde den' : 'sk-ant-…'; ?>">
					<p class="description"><?php echo $har ? '✅ Der er gemt en nøgle.' : '❌ Ingen nøgle endnu.'; ?></p></td></tr>
				<tr><th scope="row"><label for="dcol_model">Model</label></th>
					<td><input type="text" id="dcol_model" name="dcol_model" class="regular-text" value="<?php echo esc_attr( get_option( 'dcol_model', '' ) ); ?>" placeholder="claude-sonnet-5-5">
					<p class="description">Standard: claude-sonnet-5-5.</p></td></tr>
				<tr><th scope="row"><label for="dcol_kode">Deltagerkode</label></th>
					<td><?php if ( defined( 'DCOL_KODE' ) && '' !== (string) DCOL_KODE ) : ?>
						<p class="description">Koden er sat i koden (DCOL_KODE) og ændres dér.</p>
					<?php else : ?>
						<input type="text" id="dcol_kode" name="dcol_kode" class="regular-text" autocomplete="off" value="<?php echo esc_attr( get_option( 'dcol_kode', '' ) ); ?>">
						<p class="description">Den kode, deltagerne skriver for at komme ind på siden (små bogstaver). Uden kode kan kun administratorer komme ind.</p>
					<?php endif; ?></td></tr>
			</table>
			<?php submit_button( 'Gem' ); ?>
		</form>
	</div>
	<?php
}

function dcol_anmodning_aaben() {
	$t = (int) get_option( 'dcol_anmodet', 0 );
	$a = dcol_analyse();
	if ( ! $t || ( $a && strtotime( $a->post_modified_gmt . ' UTC' ) >= $t ) ) {
		return 0;
	}
	return $t;
}

function dcol_gem_prompt( WP_REST_Request $r ) {
	$p = trim( wp_strip_all_tags( (string) $r->get_param( 'prompt' ) ) );
	update_option( 'dcol_prompt', mb_substr( $p, 0, 3000 ), false );
	dcol_opdater_samlet();
	return array( 'ok' => true, 'prompt' => dcol_prompt() );
}

/* Nulstil: alle indlæg og analyser flyttes til papirkurven (kan gendannes i WP-admin). Prompten bevares. */
function dcol_nulstil() {
	$ids = get_posts( array(
		'post_type'        => array( 'dcol_indlaeg', 'dcol_opsamling' ),
		'post_status'      => array( 'private', 'publish', 'draft' ),
		'numberposts'      => -1,
		'fields'           => 'ids',
		'post__not_in'     => array( dcol_samlet_id() ),
		'suppress_filters' => true,
	) );
	foreach ( $ids as $id ) {
		wp_trash_post( $id );
	}
	dcol_opdater_samlet();
	return array( 'ok' => true, 'slettet' => count( $ids ) );
}

function dcol_feed() {
	$a     = dcol_analyse();
	$siden = $a ? strtotime( $a->post_modified_gmt . ' UTC' ) : 0;
	$ud    = array();
	$nye   = 0;
	foreach ( dcol_hent_indlaeg() as $p ) {
		$t = strtotime( $p->post_date_gmt . ' UTC' );
		if ( $a && $t > $siden ) {
			$nye++;
		}
		$ud[] = array(
			'id'    => $p->ID,
			'navn'  => html_entity_decode( $p->post_title, ENT_QUOTES, 'UTF-8' ),
			'tekst' => html_entity_decode( $p->post_content, ENT_QUOTES, 'UTF-8' ),
			'tid'   => gmdate( 'c', $t ),
		);
	}
	$svar = new WP_REST_Response( array(
		'indlaeg' => $ud,
		'nye'     => $nye,
		'prompt'  => dcol_prompt(),
		'bestilt' => ( $b = dcol_anmodning_aaben() ) ? gmdate( 'c', $b ) : null,
		'analyse' => $a ? array(
			'html'      => wp_kses_post( $a->post_content ),
			'prompt'    => $a->post_excerpt,
			'model'     => (string) get_post_meta( $a->ID, 'dcol_model', true ),
			'opdateret' => gmdate( 'c', $siden ),
		) : null,
	) );
	$svar->header( 'Cache-Control', 'no-store' );
	return $svar;
}

function dcol_nyt( WP_REST_Request $r ) {
	if ( '' !== (string) $r->get_param( 'hjemmeside' ) ) { // Honningkrukke mod spam-robotter.
		return array( 'ok' => true );
	}
	$tekst = trim( wp_strip_all_tags( (string) $r->get_param( 'tekst' ) ) );
	$navn  = trim( wp_strip_all_tags( (string) $r->get_param( 'navn' ) ) );
	if ( '' === $tekst ) {
		return new WP_Error( 'tom', 'Skriv noget først.', array( 'status' => 400 ) );
	}
	$tekst = mb_substr( $tekst, 0, 2000 );
	$navn  = mb_substr( $navn, 0, 60 );

	$noegle = 'dcol_rl_' . md5( (string) ( $_SERVER['REMOTE_ADDR'] ?? '' ) );
	if ( get_transient( $noegle ) ) {
		return new WP_Error( 'hurtig', 'Vent et par sekunder før næste indlæg.', array( 'status' => 429 ) );
	}
	set_transient( $noegle, 1, 3 );

	$id = wp_insert_post( array(
		'post_type'    => 'dcol_indlaeg',
		'post_status'  => 'private',
		'post_title'   => wp_slash( '' !== $navn ? $navn : 'Anonym' ),
		'post_content' => wp_slash( $tekst ),
	), true );
	if ( is_wp_error( $id ) ) {
		return $id;
	}
	return array( 'ok' => true, 'id' => $id );
}
