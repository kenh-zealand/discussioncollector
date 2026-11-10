<?php
/**
 * Selvstændig opsamlingsside (/opsamling/) – uden sidens almindelige tema.
 * Logikken ligger i inc/opsamling.php. Vises af pluginet for siden med adressen /opsamling/.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
nocache_headers();
if ( ! defined( 'DONOTCACHEPAGE' ) ) {
	define( 'DONOTCACHEPAGE', true ); // Siden ser forskellig ud for admin og deltagere.
}
$keha_admin      = function_exists( 'keha_ops_er_admin' ) && keha_ops_er_admin();
$keha_side       = get_queried_object();
$keha_logget_ind = function_exists( 'keha_ops_er_logget_ind' ) && keha_ops_er_logget_ind();
?><!doctype html>
<html lang="da">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title><?php echo esc_html( get_the_title( $keha_side ) ); ?> · keha.dk</title>
<style>
:root{--lilla:#6a4fd6;--lilla-lys:#b9a8f0;--lilla-bg:#f4f1ff;--tekst:#1b1b1f;--graa:#5d5d68;--kant:#e4e2ec;--bg:#fafafc}
*{box-sizing:border-box}
[hidden]{display:none!important}
body{margin:0;font-family:system-ui,-apple-system,"Segoe UI",Roboto,sans-serif;color:var(--tekst);background:var(--bg);line-height:1.5}
.wrap{max-width:900px;margin:0 auto;padding:2rem 1.25rem 4rem}
h1{font-size:1.9rem;margin:0 0 .5rem}
h2{font-size:1.15rem;margin:0 0 .75rem}
.intro{color:var(--graa);margin-bottom:1.5rem}
.kort{background:#fff;border:1px solid var(--kant);border-radius:14px;padding:1.25rem 1.4rem;margin-bottom:1.25rem}
label{display:block;font-weight:600;font-size:.9rem;margin-bottom:.3rem}
textarea,input[type=text],input[type=password]{width:100%;font:inherit;padding:.65rem .8rem;border:1px solid #cfccd9;border-radius:10px;background:#fff}
textarea{min-height:110px;resize:vertical}
textarea:focus,input:focus,button:focus-visible{outline:3px solid var(--lilla-lys);outline-offset:1px}
.raekke{display:flex;gap:.75rem;align-items:flex-end;flex-wrap:wrap;margin-top:.75rem}
.raekke .navn{flex:1 1 220px}
.knap{font:inherit;font-weight:600;border:1px solid var(--lilla);background:var(--lilla);color:#fff;border-radius:999px;padding:.6rem 1.3rem;cursor:pointer}
.knap.sekundaer{background:#fff;color:var(--lilla)}
.knap:disabled{opacity:.6;cursor:default}
.status{min-height:1.4em;font-size:.9rem;color:var(--graa);margin-top:.5rem}
.hp{position:absolute;left:-9999px}
.analyse-hoved{display:flex;justify-content:space-between;align-items:center;gap:1rem;margin-bottom:.5rem}
.nye{display:inline-flex;align-items:center;gap:.35rem;border:1px solid var(--lilla);color:var(--lilla);background:#fff;border-radius:999px;padding:.15rem .7rem;font-size:.85rem;font-weight:600;cursor:pointer}
.nye::before{content:"";width:.5rem;height:.5rem;border-radius:50%;background:#22b35e}
.analyse{border-left:4px solid var(--lilla-lys);padding:.25rem 0 .25rem 1.1rem}
.analyse h3{font-size:1rem;margin:1rem 0 .3rem}
.analyse h3:first-child{margin-top:0}
.analyse ul{margin:.2rem 0 .5rem;padding-left:1.3rem}
.tom{color:var(--graa);font-style:italic}
.analyse-fod{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.5rem;margin-top:1rem;font-size:.85rem;color:var(--graa)}
.info{position:relative;display:inline-flex;align-items:center;gap:.3rem}
.info button{border:1px solid var(--graa);color:var(--graa);background:none;border-radius:50%;width:1.15rem;height:1.15rem;font-size:.7rem;line-height:1;padding:0;cursor:help}
.tip{display:none;position:absolute;bottom:1.7rem;left:0;width:min(380px,80vw);background:#1b1b1f;color:#fff;font-style:italic;font-size:.82rem;padding:.7rem .85rem;border-radius:8px;z-index:5;white-space:pre-wrap}
.info:hover .tip,.info:focus-within .tip{display:block}
.fanebar{display:flex;gap:.5rem;margin-bottom:1rem}
.fane{font:inherit;border:1px solid var(--kant);background:#fff;border-radius:999px;padding:.35rem 1rem;cursor:pointer}
.fane[aria-selected=true]{background:var(--lilla-bg);border-color:var(--lilla);color:var(--lilla);font-weight:600}
.liste{list-style:none;margin:0;padding:0}
.liste li{border-top:1px solid var(--kant);padding:.8rem 0}
.liste li:first-child{border-top:0}
.meta{font-size:.8rem;color:var(--graa);margin-bottom:.15rem}
.meta b{color:var(--tekst)}
.tekst{white-space:pre-wrap;overflow-wrap:anywhere}
.tekst.klip{display:-webkit-box;-webkit-line-clamp:3;line-clamp:3;-webkit-box-orient:vertical;overflow:hidden}
.vis-mere{display:block;margin:0 auto;background:none;border:0;color:var(--lilla);font:inherit;font-size:.85rem;font-weight:700;padding:.25rem 0 0;cursor:pointer}
.vis-mere:hover{text-decoration:underline}
.ny-markering{animation:blink 2.5s ease-out}
@keyframes blink{from{background:var(--lilla-bg)}to{background:transparent}}
.sky{display:flex;flex-wrap:wrap;justify-content:center;align-items:center;gap:.35rem 1rem;padding:1rem .5rem;min-height:160px}
.sky span{line-height:1.1;transition:font-size .4s;display:inline-flex;flex-direction:column;align-items:center}
.sky small{display:none;font-size:.7rem;font-weight:500;color:var(--graa);line-height:1;margin-top:.1rem}
.sky.med-tal small{display:block}
.toggle{font:inherit;border:1px solid var(--kant);background:#fff;border-radius:999px;padding:.35rem 1rem;cursor:pointer}
.toggle[aria-pressed=true]{background:var(--lilla-bg);border-color:var(--lilla);color:var(--lilla);font-weight:600}
.indstillinger summary{cursor:pointer;font-weight:600;color:var(--lilla)}
.indstillinger[open] summary{margin-bottom:1rem}
.indstillinger h3{font-size:1rem;margin:0 0 .3rem}
.indstillinger hr{border:0;border-top:1px solid var(--kant);margin:1.2rem 0}
.hjaelp{font-size:.88rem;color:var(--graa);margin:.4rem 0}
a.knap{text-decoration:none;display:inline-block}
.hoejre{display:inline-flex;align-items:center;gap:.75rem}
#opdater{padding:.4rem 1rem;font-size:.9rem}
.analyse.arbejder{opacity:.45;transition:opacity .3s}
.knap.fare{background:#b3261e;border-color:#b3261e}
.login{max-width:380px;margin:12vh auto;padding:0 1.25rem}
.kun-print{display:none}
.version{text-align:center;font-size:.75rem;color:var(--graa);margin:1.5rem 0 0}
@media print{
	body{background:#fff}
	.wrap{padding:0;max-width:none}
	.wrap>*:not(#opsamling-sektion){display:none!important}
	#opsamling-sektion{border:0;padding:0;margin:0}
	.analyse-fod,.nye,#opdater-status{display:none!important}
	.analyse{border-left:0;padding-left:0;opacity:1!important}
	.kun-print{display:block;color:#444;font-size:.85rem;margin:0 0 1.2rem}
	.kun-print h1{font-size:1.5rem;color:#000;margin:0 0 .3rem}
}
.fejl{color:#b3261e;font-size:.9rem;margin-top:.5rem}
@media (prefers-reduced-motion:reduce){.ny-markering{animation:none}.sky span{transition:none}}
</style>
</head>
<body>
<?php if ( ! $keha_logget_ind ) : ?>
<main class="login">
	<div class="kort">
		<h1><?php echo esc_html( get_the_title( $keha_side ) ); ?></h1>
		<p class="intro">Skriv koden, du har fået af underviseren.</p>
		<form method="post">
			<label for="kode">Kode</label>
			<input type="password" id="kode" name="keha_ops_kode" autocomplete="off" required autofocus>
			<?php if ( ! empty( $GLOBALS['keha_ops_fejl'] ) ) : ?>
				<p class="fejl" role="alert">Forkert kode – prøv igen.</p>
			<?php endif; ?>
			<div style="margin-top:1rem">
				<label for="login-navn">Navn (valgfrit)</label>
				<input type="text" id="login-navn" maxlength="60" autocomplete="name" placeholder="Lad stå tomt for at skrive anonymt">
				<p class="hjaelp">Navnet vises ved dine indlæg. Du kan altid ændre det eller fjerne det inde på siden.</p>
			</div>
			<div class="raekke"><button class="knap" type="submit">Gå ind</button></div>
		</form>
	</div>
</main>
<script>
(() => {
	const f = document.getElementById('login-navn');
	try { const n = localStorage.getItem('keha_ops_navn'); if (n) f.value = n; } catch (e) {}
	f.closest('form').addEventListener('submit', () => {
		try { localStorage.setItem('keha_ops_navn', f.value.trim()); } catch (e) {}
	});
})();
</script>
<?php else : ?>
<main class="wrap">
	<h1><?php echo esc_html( get_the_title( $keha_side ) ); ?></h1>
	<div class="intro"><?php echo wpautop( wp_kses_post( $keha_side->post_content ) ); ?></div>

	<section class="kort" aria-labelledby="skriv-h">
		<h2 id="skriv-h">Skriv dit bidrag</h2>
		<form id="form">
			<label for="tekst" class="hp">Dit bidrag</label>
			<textarea id="tekst" maxlength="2000" required placeholder="Skriv dit indlæg her …"></textarea>
			<input type="text" class="hp" id="hjemmeside" tabindex="-1" autocomplete="off" aria-hidden="true">
			<div class="raekke">
				<div class="navn">
					<label for="navn">Navn (valgfrit)</label>
					<input type="text" id="navn" maxlength="60" autocomplete="name" placeholder="Lad stå tomt for at skrive anonymt">
				</div>
				<button class="knap" id="send" type="submit">Send</button>
			</div>
			<div class="status" id="status" role="status" aria-live="polite"></div>
		</form>
	</section>

	<?php ob_start(); // Underviserboksen vises kun for WordPress-administratorer. ?>
	<details class="kort indstillinger">
		<summary>Underviser: prompt og nulstilling</summary>
		<div class="ind-indhold">
			<label for="prompt">Prompt – sådan skal indlæggene behandles i opsamlingen</label>
			<textarea id="prompt" maxlength="3000" placeholder="Fx: Organisér indlæggene og udled temaer …"></textarea>
			<div class="raekke">
				<button class="knap" id="gem-prompt" type="button">Gem prompt</button>
			</div>
			<div class="status" id="prompt-status" role="status" aria-live="polite"></div>
			<p class="hjaelp">Prompten bruges på alle indlæg, når opsamlingen opdateres.</p>

			<hr>
			<h3>Nulstil</h3>
			<p class="hjaelp">Fjerner alle indlæg og analysen, så siden er klar til en ny runde. Prompten bevares. Indholdet lægges i papirkurven i WordPress og kan gendannes derfra.</p>
			<div class="raekke" id="nulstil-start">
				<button class="knap fare" id="nulstil" type="button">Nulstil opsamlingen</button>
			</div>
			<div class="raekke" id="nulstil-bekraeft" hidden>
				<strong>Er du sikker? Alle indlæg fjernes for alle deltagere.</strong>
				<button class="knap fare" id="nulstil-ja" type="button">Ja, nulstil</button>
				<button class="knap sekundaer" id="nulstil-nej" type="button">Annullér</button>
			</div>
			<div class="status" id="nulstil-status" role="status" aria-live="polite"></div>
		</div>
	</details>
	<?php $keha_underviser_html = ob_get_clean(); if ( $keha_admin ) { echo $keha_underviser_html; } ?>

	<section class="kort" id="opsamling-sektion" aria-labelledby="analyse-h">
		<div class="kun-print">
			<h1><?php echo esc_html( get_the_title( $keha_side ) ); ?> – opsamling</h1>
			<div id="print-meta"></div>
		</div>
		<div class="analyse-hoved">
			<h2 id="analyse-h">Opsamling</h2>
			<button type="button" class="nye" id="nye" hidden></button>
		</div>
		<div class="analyse" id="analyse"><p class="tom"><?php echo $keha_admin ? 'Der er endnu ingen opsamling. Tryk "Opdater" for at lave den.' : 'Der er endnu ingen opsamling.'; ?></p></div>
		<div class="analyse-fod">
			<span class="info">AI-genereret analyse
				<button type="button" aria-label="Vis prompten bag analysen">i</button>
				<span class="tip" id="tip" role="tooltip">Endnu ingen prompt.</span>
			</span>
			<span class="hoejre">
				<span id="opdateret"></span>
				<button type="button" class="knap sekundaer" id="pdf" title="Åbner udskriftsvinduet – vælg 'Gem som PDF'">⤓ Eksportér PDF</button>
				<?php if ( $keha_admin ) : ?><button type="button" class="knap sekundaer" id="opdater">↻ Opdater</button><?php endif; ?>
			</span>
		</div>
		<div class="status" id="opdater-status" role="status" aria-live="polite"></div>
	</section>

	<section class="kort" id="indlaeg-sektion" aria-labelledby="indlaeg-h">
		<div class="analyse-hoved">
			<h2 id="indlaeg-h">Alle indlæg (<span id="antal">0</span>)</h2>
		</div>
		<div class="fanebar" role="tablist">
			<button type="button" class="fane" role="tab" aria-selected="true" data-vis="liste">Liste</button>
			<button type="button" class="fane" role="tab" aria-selected="false" data-vis="sky">Ordsky</button>
			<button type="button" class="toggle" id="vis-tal" aria-pressed="false">Vis tal i ordsky</button>
		</div>
		<ul class="liste" id="liste"></ul>
		<div class="sky" id="sky" hidden aria-label="Ordsky over alle indlæg"></div>
	</section>
	<p class="version">Discussion Collector v<?php echo esc_html( defined( 'KEHA_OPS_VERSION' ) ? KEHA_OPS_VERSION : '' ); ?></p>
</main>

<script>
(() => {
	const API = <?php echo wp_json_encode( esc_url_raw( rest_url( 'keha-opsamling/v1/' ) ) ); ?>;
	const $ = (id) => document.getElementById(id);
	const ADMIN = <?php echo $keha_admin ? 'true' : 'false'; ?>;
	// WordPress kræver en nonce, før REST-kald kører som den indloggede admin.
	const HOVED = ADMIN ? { 'X-WP-Nonce': <?php echo wp_json_encode( $keha_admin ? wp_create_nonce( 'wp_rest' ) : '' ); ?> } : {};
	const STOP = new Set(('og i jeg det at en den til er som på de med han af for ikke der var mig sig men et har om vi min havde ham hun nu over da fra du ud sin dem os op man hans hvor eller hvad skal selv her alle vil blev kunne ind når være dog noget ville jo deres efter ned skulle denne end dette mit også under have dig anden hende mine alt meget sit sine vor mod disse hvis din nogle hos blive mange ad bliver hendes været thi jer sådan kan få så ser bare godt mere mest hvordan hvorfor fordi nok kun ved via samt lige vores sig mm fx bl.a evt osv lidt helt rigtig hele andre anden andet ingen nogen noget hvilke hvilket hvem hvornår gør gøre giver give kommer komme kunne måske altid aldrig nye ny godt gode god får fået vores jeres man mere mindre samme også både enten ellers sammen igen allerede stadig blot bare være været er var').split(' '));
	let sidsteIds = new Set();
	let forste = true;
	let data = null;

	const tid = (iso) => new Date(iso).toLocaleString('da-DK', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' });

	function visListe(indlaeg) {
		const ul = $('liste');
		ul.textContent = '';
		if (!indlaeg.length) {
			const li = document.createElement('li');
			li.className = 'tom';
			li.textContent = 'Ingen indlæg endnu – vær den første.';
			ul.append(li);
			return;
		}
		[...indlaeg].reverse().forEach((p) => {
			const li = document.createElement('li');
			if (!forste && !sidsteIds.has(p.id)) li.className = 'ny-markering';
			const meta = document.createElement('div');
			meta.className = 'meta';
			const b = document.createElement('b');
			b.textContent = p.navn;
			meta.append(b, ' · ' + tid(p.tid));
			const t = document.createElement('div');
			t.className = 'tekst';
			t.textContent = p.tekst;
			li.append(meta, t);
			ul.append(li);
			// Højst 3 linjer; "Vis mere" kun hvis teksten faktisk er længere.
			const aaben = udfoldet.has(p.id);
			t.classList.add('klip');
			const forLang = t.scrollHeight > t.clientHeight + 1;
			if (!forLang) return;
			if (aaben) t.classList.remove('klip');
			const k = document.createElement('button');
			k.type = 'button';
			k.className = 'vis-mere';
			k.setAttribute('aria-expanded', aaben);
			k.textContent = aaben ? 'Vis mindre ▴' : 'Vis mere ▾';
			k.addEventListener('click', () => {
				const nu = !udfoldet.has(p.id);
				if (nu) udfoldet.add(p.id); else udfoldet.delete(p.id);
				t.classList.toggle('klip', !nu);
				k.setAttribute('aria-expanded', nu);
				k.textContent = nu ? 'Vis mindre ▴' : 'Vis mere ▾';
			});
			li.append(k);
		});
	}
	const udfoldet = new Set();

	/* Dansk ordstamme (forenklet Snowball-algoritme): interview/interviews/interviewene → "interview",
	   anvende/anvendelse/anvendelsen → "anvend". Bruges kun til at gruppere ord i ordskyen. */
	const VOK = 'aeiouyæøå';
	const ENDELSER = ['erendes','erende','hedens','ethed','erede','heden','heder','endes','ernes','erens','erets','ered','ende','erne','eren','erer','heds','enes','eres','eret','hed','ene','ere','ens','ers','ets','en','er','es','et','e'];
	function stamme(o) {
		let r1 = o.length;
		for (let i = 1; i < o.length; i++) {
			if (!VOK.includes(o[i]) && VOK.includes(o[i - 1])) { r1 = i + 1; break; }
		}
		r1 = Math.max(r1, 3);
		const iR1 = (suf) => o.endsWith(suf) && o.length - suf.length >= Math.max(r1, 4);
		const trin2 = () => { if (['gd','dt','gt','kt'].some(iR1)) o = o.slice(0, -1); };
		const suf = ENDELSER.find(iR1);
		if (suf) o = o.slice(0, -suf.length);
		else if (iR1('s') && /[abcdfghjklmnoprtvwyzå]s$/.test(o)) o = o.slice(0, -1);
		else { const k = ['ene','en','et'].find((x) => o.endsWith(x) && o.length - x.length >= 3); if (k) o = o.slice(0, -k.length); } // tiden → tid, målet → mål
		trin2();
		if (o.endsWith('igst')) o = o.slice(0, -2);
		const suf3 = ['elig','løst','lig','els','ig'].find(iR1);
		if (suf3 === 'løst') o = o.slice(0, -1);
		else if (suf3) { o = o.slice(0, -suf3.length); trin2(); }
		if (o.length - 2 >= r1 && !VOK.includes(o.slice(-1)) && o.slice(-1) === o.slice(-2, -1)) o = o.slice(0, -1);
		return o;
	}

	function visSky(indlaeg) {
		const grupper = new Map(); // stamme → { n, former: Map(form → antal) }
		indlaeg.forEach((p) => {
			(p.tekst.toLowerCase().match(/[a-zæøåéü0-9-]+/g) || []).forEach((o) => {
				o = o.replace(/^-+|-+$/g, '');
				if (o.length < 3 || STOP.has(o) || /^\d+$/.test(o)) return;
				const s = stamme(o);
				if (!grupper.has(s)) grupper.set(s, { n: 0, former: new Map() });
				const g = grupper.get(s);
				g.n++;
				g.former.set(o, (g.former.get(o) || 0) + 1);
			});
		});
		// Vis den form, der er brugt flest gange; ved lighed den korteste.
		const taelling = [...grupper.values()].map((g) => {
			const former = [...g.former.entries()].sort((a, b) => b[1] - a[1] || a[0].length - b[0].length || a[0].localeCompare(b[0], 'da'));
			return [former[0][0], g.n, former.map((f) => f[0])];
		});
		const top = taelling.sort((a, b) => b[1] - a[1]).slice(0, 60);
		const sky = $('sky');
		sky.textContent = '';
		if (!top.length) { sky.innerHTML = '<p class="tom">Ordskyen vises, når der er indlæg.</p>'; return; }
		const max = top[0][1], min = top[top.length - 1][1];
		const farver = ['#6a4fd6', '#3d2a99', '#8e6ff0', '#1b1b1f', '#5b47b8'];
		top.sort((a, b) => a[0].localeCompare(b[0], 'da')).forEach(([ord, n, former], i) => {
			const s = document.createElement('span');
			const v = max === min ? 0.5 : (n - min) / (max - min);
			s.style.fontSize = (0.95 + v * 2.1).toFixed(2) + 'rem';
			s.style.fontWeight = v > 0.5 ? 700 : 500;
			s.style.color = farver[i % farver.length];
			s.title = ord + ': ' + n + (former.length > 1 ? ' (' + former.join(', ') + ')' : '');
			const tal = document.createElement('small');
			tal.textContent = n;
			s.append(ord, tal);
			sky.append(s);
		});
	}

	function visAnalyse(d) {
		const a = d.analyse;
		if (a) {
			$('analyse').innerHTML = a.html;
			$('tip').textContent = a.prompt || 'Ingen prompt angivet.';
			$('opdateret').textContent = 'Opdateret ' + tid(a.opdateret);
		}
		const nye = $('nye');
		if (ADMIN && a && d.nye > 0) { // Kun admin ser, at opsamlingen mangler nye indlæg.
			nye.hidden = false;
			nye.textContent = d.nye + (d.nye === 1 ? ' nyt' : ' nye') + ' ↓';
			nye.title = 'Indlæg kommet til siden analysen blev lavet';
		} else {
			nye.hidden = true;
		}
	}

	async function hent() {
		try {
			const r = await fetch(API + 'feed', { credentials: 'same-origin', cache: 'no-store', headers: HOVED });
			if (r.status === 401 || r.status === 403) { location.reload(); return; }
			if (!r.ok) return;
			data = await r.json();
			$('antal').textContent = data.indlaeg.length;
			visListe(data.indlaeg);
			visSky(data.indlaeg);
			visAnalyse(data);
			const pf = $('prompt');
			if (pf && !promptRedigeret && document.activeElement !== pf) pf.value = data.prompt || '';
			sidsteIds = new Set(data.indlaeg.map((p) => p.id));
			forste = false;
		} catch (e) { /* prøver igen ved næste runde */ }
	}

	$('form').addEventListener('submit', async (e) => {
		e.preventDefault();
		const tekst = $('tekst').value.trim();
		if (!tekst) return;
		$('send').disabled = true;
		$('status').textContent = 'Sender …';
		try {
			const r = await fetch(API + 'indlaeg', {
				method: 'POST', credentials: 'same-origin',
				headers: { ...HOVED, 'Content-Type': 'application/json' },
				body: JSON.stringify({ tekst, navn: $('navn').value.trim(), hjemmeside: $('hjemmeside').value })
			});
			const svar = await r.json().catch(() => ({}));
			if (!r.ok) throw new Error(svar.message || 'Noget gik galt.');
			$('tekst').value = '';
			$('status').textContent = 'Tak – dit indlæg er sendt.';
			hent();
		} catch (err) {
			$('status').textContent = err.message;
		} finally {
			$('send').disabled = false;
		}
	});

	document.querySelectorAll('.fane').forEach((f) => f.addEventListener('click', () => {
		document.querySelectorAll('.fane').forEach((x) => x.setAttribute('aria-selected', x === f));
		$('liste').hidden = f.dataset.vis !== 'liste';
		$('sky').hidden = f.dataset.vis !== 'sky';
		if (f.dataset.vis === 'liste' && data) visListe(data.indlaeg);
	}));
	$('vis-tal').addEventListener('click', () => {
		const til = $('vis-tal').getAttribute('aria-pressed') !== 'true';
		$('vis-tal').setAttribute('aria-pressed', til);
		$('vis-tal').textContent = til ? 'Skjul tal i ordsky' : 'Vis tal i ordsky';
		$('sky').classList.toggle('med-tal', til);
		if (til) document.querySelector('.fane[data-vis="sky"]').click();
	});
	let promptRedigeret = false;
	// Underviserknapperne findes kun for admin – ?. springer dem over for deltagere.
	$('prompt')?.addEventListener('input', () => { promptRedigeret = true; $('prompt-status').textContent = 'Ikke gemt endnu.'; });
	const post = async (sti, krop) => {
		const r = await fetch(API + sti, { method: 'POST', credentials: 'same-origin', headers: { ...HOVED, 'Content-Type': 'application/json' }, body: JSON.stringify(krop || {}) });
		const svar = await r.json().catch(() => ({}));
		if (!r.ok) throw new Error(svar.message || 'Noget gik galt.');
		return svar;
	};
	$('gem-prompt')?.addEventListener('click', async () => {
		$('gem-prompt').disabled = true;
		try {
			const s = await post('prompt', { prompt: $('prompt').value });
			$('prompt').value = s.prompt;
			promptRedigeret = false;
			$('prompt-status').textContent = 'Prompten er gemt.';
		} catch (e) { $('prompt-status').textContent = e.message; }
		finally { $('gem-prompt').disabled = false; }
	});
	$('nulstil')?.addEventListener('click', () => { $('nulstil-start').hidden = true; $('nulstil-bekraeft').hidden = false; $('nulstil-nej').focus(); });
	$('nulstil-nej')?.addEventListener('click', () => { $('nulstil-bekraeft').hidden = true; $('nulstil-start').hidden = false; });
	$('nulstil-ja')?.addEventListener('click', async () => {
		$('nulstil-ja').disabled = true;
		try {
			const s = await post('nulstil');
			$('nulstil-status').textContent = 'Opsamlingen er nulstillet (' + s.slettet + ' elementer fjernet).';
			$('analyse').innerHTML = '<p class="tom">Der er endnu ingen opsamling. Tryk "Opdater" for at lave den.</p>';
			$('tip').textContent = 'Endnu ingen prompt.';
			$('opdateret').textContent = '';
			hent();
		} catch (e) { $('nulstil-status').textContent = e.message; }
		finally { $('nulstil-ja').disabled = false; $('nulstil-bekraeft').hidden = true; $('nulstil-start').hidden = false; }
	});

	let analyserer = false;
	$('opdater')?.addEventListener('click', async () => {
		analyserer = true;
		$('opdater').disabled = true;
		$('opdater').textContent = '↻ Analyserer …';
		$('analyse').classList.add('arbejder');
		$('opdateret').textContent = '';
		$('opdater-status').textContent = 'Analyserer indlæggene – det kan tage op til et minut …';
		try { await post('anmod'); $('opdater-status').textContent = ''; }
		catch (e) { $('opdater-status').textContent = e.message; }
		analyserer = false;
		$('opdater').disabled = false;
		$('opdater').textContent = '↻ Opdater';
		$('analyse').classList.remove('arbejder');
		await hent();
	});

	$('pdf').addEventListener('click', () => {
		const a = data && data.analyse;
		if (!a) { $('opdater-status').textContent = 'Der er ingen opsamling at eksportere endnu.'; return; }
		$('print-meta').textContent = 'Opdateret ' + tid(a.opdateret) + ' · ' + data.indlaeg.length + ' indlæg · AI-genereret med prompten: ' + (a.prompt || '–');
		const titel = document.title;
		document.title = 'Opsamling ' + new Date(a.opdateret).toLocaleDateString('da-DK'); // bliver filnavnet
		window.print();
		document.title = titel;
	});

	$('nye').addEventListener('click', () => $('indlaeg-sektion').scrollIntoView({ behavior: 'smooth' }));

	try { const n = localStorage.getItem('keha_ops_navn'); if (n) $('navn').value = n; } catch (e) {}
	$('navn').addEventListener('change', () => { try { localStorage.setItem('keha_ops_navn', $('navn').value); } catch (e) {} });

	hent();
	setInterval(() => { if (!document.hidden) hent(); }, 5000);
	document.addEventListener('visibilitychange', () => { if (!document.hidden) hent(); });
})();
</script>
<?php endif; ?>
</body>
</html>
