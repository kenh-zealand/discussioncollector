# Discussion Collector

Dette er en opsamler af indlæg med tematisering via AI (tilføj egen API).

En lille, selvstændig WordPress-side inspireret af Howspace' opsamlingsblok: deltagere skriver korte indlæg, og underviseren får Claude (Anthropic) til at samle dem i temaer ud fra en prompt. Siden har også en ordsky og kan eksportere opsamlingen som PDF.

**Version:** 1.0 – se [CHANGELOG.md](CHANGELOG.md).

## Funktioner

- Siden er beskyttet med en fælles deltagerkode. Indlogget WordPress-administrator kommer ind uden kode.
- Deltagere skriver indlæg med frivilligt navn (ellers "Anonym"). Navnet huskes i browseren.
- Liste over alle indlæg, der opdateres automatisk hvert 5. sekund. Lange indlæg vises med 3 linjer og "Vis mere".
- Ordsky, hvor bøjninger af samme ord lægges sammen (dansk ordstamme), med mulighed for at vise antal.
- AI-opsamling via Anthropic API ud fra en prompt, som underviseren skriver. Navne sendes ikke med til Claude.
- Eksport af opsamlingen til PDF (via browserens "Gem som PDF").
- Kun administratorer kan opdatere opsamlingen, ændre prompten og nulstille siden.

## Installation

Filerne er lavet til at ligge i et (child-)tema.

1. Kopiér `page-opsamling.php` og mappen `inc/` ind i dit tema.
2. Tilføj denne linje i temaets `functions.php`:
   ```php
   require_once get_stylesheet_directory() . '/inc/opsamling.php';
   ```
3. Opret en WordPress-side med adressen (slug) `opsamling`. Sidens tekst vises som introduktion.
4. Gå til **Indstillinger → Opsamling** og udfyld:
   - **Anthropic API-nøgle** – oprettes på [console.anthropic.com](https://console.anthropic.com) (kræver betaling dér).
   - **Model** – fx `claude-haiku-5-5` (hurtig og billig) eller `claude-sonnet-5-5` (standard).
   - **Deltagerkode** – den kode, deltagerne skal skrive (små bogstaver).

Kode og nøgle kan i stedet sættes i `wp-config.php`, så de ikke ligger i databasen:

```php
define( 'KEHA_OPS_KODE', 'din-kode' );
define( 'KEHA_OPS_ANTHROPIC_KEY', 'sk-ant-...' );
```

Læg aldrig din API-nøgle eller deltagerkode i dette repository.

## Data

- Indlæg gemmes som private indlæg af typen `keha_indlaeg`, analysen som `keha_opsamling`. Begge kan ses i WordPress-admin.
- "Nulstil" lægger indlæg og analyse i papirkurven, så de kan gendannes.
- Siden caches ikke (`DONOTCACHEPAGE`), fordi den ser forskellig ud for administrator og deltagere.
