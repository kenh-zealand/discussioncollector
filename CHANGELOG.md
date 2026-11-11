# Ændringslog

Alle væsentlige ændringer i Discussion Collector.

## [1.4] – 2026-11-11

### Tilføjet
- Knappen "⤓ Eksportér PDF" ved alle indlæg: eksporterer det, man ser – listen med alle indlæg foldet helt ud eller ordskyen.

## [1.3] – 2026-11-10

### Tilføjet
- Modelnavnet gemmes ved hver opsamling og vises i "i"-boksen under prompten og i PDF-eksporten.

## [1.2] – 2026-11-10

### Ændret
- Alle interne navne bruger nu præfikset `dcol_` (Discussion Collector) i stedet for det oprindelige sitenavn: funktioner, indstillinger, indholdstyperne `dcol_indlaeg` og `dcol_opsamling`, REST-adressen `discussion-collector/v1` og indstillingssiden.
- Konstanterne til `wp-config.php` hedder nu `DCOL_KODE` og `DCOL_ANTHROPIC_KEY`.
- Sidens fanetitel viser hjemmesidens eget navn.

## [1.1] – 2026-11-10

### Ændret
- Pakket som et rigtigt WordPress-plugin: upload ZIP-filen under Plugins og aktivér. Det er ikke længere nødvendigt at kopiere filer ind i temaet.
- Sideskabelonen ligger nu i `templates/page-opsamling.php` og bruges uanset tema.

### Tilføjet
- Siden `/opsamling/` oprettes automatisk ved aktivering, hvis den ikke findes.
- Link til "Indstillinger" direkte i plugin-listen.
- Advarsel, hvis opsamlingen allerede er indlæst fra et tema, så koden ikke kører dobbelt.
- MIT-licens (LICENSE), så alle frit må bruge, ændre og dele pluginet.

## [1.0] – 2026-11-10

Første offentlige version.

### Tilføjet
- Kodebeskyttet opsamlingsside (`/opsamling/`), hvor deltagere skriver indlæg.
- Frivilligt navn ved indgangen og ved hvert indlæg; tomt navn vises som "Anonym".
- Liste over alle indlæg med automatisk opdatering; lange indlæg forkortes til 3 linjer med "Vis mere".
- Ordsky med dansk ordstamme, så bøjninger (fx interview/interviews/interviewene) tælles som ét ord.
- Knappen "Vis tal i ordsky", der viser det samlede antal for hvert ord.
- AI-opsamling via Anthropic API ("↻ Opdater") ud fra en redigerbar prompt.
- Valg af model og API-nøgle under Indstillinger → Opsamling.
- Deltagerkoden kan sættes under Indstillinger → Opsamling eller i `wp-config.php`.
- Eksport af opsamlingen til PDF.
- Versionsnummer nederst på siden.

### Sikkerhed
- Kun indloggede WordPress-administratorer kan opdatere opsamlingen, ændre prompten og nulstille.
- Mærket med nye indlæg siden sidste opsamling vises kun for administratorer.
- Deltagernavne sendes ikke med til Claude.
- Ingen API-nøgle eller adgangskode i koden.
