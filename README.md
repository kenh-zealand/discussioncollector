# Discussion Collector

Dette er en opsamler af indlæg med tematisering via AI (tilføj egen API).

En lille, selvstændig WordPress-side inspireret af Howspace' opsamlingsblok: deltagere skriver korte indlæg, og underviseren får Claude (Anthropic) til at samle dem i temaer ud fra en prompt. Siden har også en ordsky og kan eksportere opsamlingen som PDF.

**Version:** 1.4 – se [CHANGELOG.md](CHANGELOG.md).

## Funktioner

- Siden er beskyttet med en fælles deltagerkode. Indlogget WordPress-administrator kommer ind uden kode.
- Deltagere skriver indlæg med frivilligt navn (ellers "Anonym"). Navnet huskes i browseren.
- Liste over alle indlæg, der opdateres automatisk hvert 5. sekund. Lange indlæg vises med 3 linjer og "Vis mere".
- Ordsky, hvor bøjninger af samme ord lægges sammen (dansk ordstamme), med mulighed for at vise antal.
- AI-opsamling via Anthropic API ud fra en prompt, som underviseren skriver. Navne sendes ikke med til Claude.
- Eksport til PDF (via browserens "Gem som PDF") af både opsamlingen og af alle indlæg eller ordskyen.
- Kun administratorer kan opdatere opsamlingen, ændre prompten og nulstille siden.

## Installation

1. Klik på den grønne knap **Code → Download ZIP** her på GitHub.
2. Gå i WordPress til **Plugins → Tilføj nyt plugin → Upload plugin**, vælg ZIP-filen, og tryk **Installer nu** og derefter **Aktivér**.
3. Ved aktivering oprettes siden **/opsamling/** automatisk (hvis den ikke findes i forvejen). Sidens tekst vises som introduktion og kan redigeres som en almindelig side.
4. Gå til **Indstillinger → Opsamling** (eller klik "Indstillinger" ved pluginet) og udfyld:
   - **Anthropic API-nøgle** – oprettes på [console.anthropic.com](https://console.anthropic.com) (kræver betaling dér).
   - **Model** – fx `claude-haiku-5-5` (hurtig og billig) eller `claude-sonnet-5-5` (standard).
   - **Deltagerkode** – den kode, deltagerne skal skrive (små bogstaver).

Kode og nøgle kan i stedet sættes i `wp-config.php`, så de ikke ligger i databasen:

```php
define( 'DCOL_KODE', 'din-kode' );
define( 'DCOL_ANTHROPIC_KEY', 'sk-ant-...' );
```

Læg aldrig din API-nøgle eller deltagerkode i dette repository.

Administratorer logger ind med deres almindelige WordPress-konto – der er ingen særskilt admin-kode.

## Filer

- `discussion-collector.php` – pluginets hovedfil.
- `inc/opsamling.php` – logik: indholdstyper, adgang, REST-endepunkter, kald til Claude og indstillinger.
- `templates/page-opsamling.php` – selve siden /opsamling/ (HTML, CSS og JavaScript).

## Data

- Indlæg gemmes som private indlæg af typen `dcol_indlaeg`, analysen som `dcol_opsamling`. Begge kan ses i WordPress-admin.
- "Nulstil" lægger indlæg og analyse i papirkurven, så de kan gendannes.
- Siden caches ikke (`DONOTCACHEPAGE`), fordi den ser forskellig ud for administrator og deltagere.

## Licens

MIT – du må frit bruge, ændre og dele koden, også kommercielt, så længe copyright-linjen og licensteksten følger med. Se [LICENSE](LICENSE). Koden leveres som den er, uden garanti.
