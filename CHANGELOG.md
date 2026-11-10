# Ændringslog

Alle væsentlige ændringer i Discussion Collector.

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
