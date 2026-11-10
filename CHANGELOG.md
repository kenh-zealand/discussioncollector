# Ændringslog

Alle væsentlige ændringer i Discussion Collector.

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
