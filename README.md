# PokerTH Best Brainies Cup (BBC)

Webanwendung des **Best Brainies Cup** auf <https://bbc.pokerth.net>: Anmeldung zu
Spielterminen, Ergebnis-Upload, Season-Ranking, Hall of Fame, Awards und Shoutbox.

Dieser Branch (`bbc`) enthält die BBC-Variante. Die anderen Branches im Repository
(`master`, `wec`, `kauberdi`, `laravel-base`) gehören zu Schwesterseiten bzw. zur
gemeinsamen Basis.

## Technik

- PHP 8.2, Laravel 12, MySQL/MariaDB (Datenbank `bbc`)
- Frontend: Vue 3 + Element Plus, gebaut mit Vite
- Das Build-Ergebnis in `public/build/` ist eingecheckt. Nach Änderungen an
  `resources/js` oder `resources/sass` muss neu gebaut und `public/build/` mit
  committet werden.

## Einrichtung

```bash
composer install
cp .env.example .env        # DB-Zugang und APP_URL eintragen
php artisan key:generate
php artisan migrate
npm ci
npm run build               # oder: npm run dev (Vite-Dev-Server)
```

Es gibt keine CI und keinen Deploy-Schritt: Auf dem Server gespeicherte PHP-Änderungen
sind sofort live. Eine Testinstanz liegt unter `/var/www/bbc_test`.

## Begriffe

| Begriff | Bedeutung |
|---|---|
| **Step 1–4** | Stufe eines Spiels. Step 1 ist offen für alle, Step 2–4 brauchen die jeweiligen Tickets (`s2_tickets` … `s4_tickets` am Spieler). Punkte pro Platz: 10 … 1, multipliziert mit dem Step. |
| **GameDate** | Ein Spieltermin mit Step. Spieler registrieren sich dafür (`registrations`). |
| **Tisch** | Je 10 Anmeldungen bilden einen Tisch. Auf jedem Tisch wird der erste registrierte Admin als „Admin" markiert. |
| **Season** | Wertungszeitraum. Das Ranking rechnet ab dem Start der laufenden Season. |
| **Rollen** | `users.role`: `a` = Admin, `s` = Superadmin. |
| **Monthlycup** | Am letzten Samstag im Monat gibt es um 19:30 und 21:30 keine BBC-Termine. |

## Artisan-Befehle

Alle Befehle werden im Projektverzeichnis ausgeführt: `php artisan <befehl>`.
Befehle, die Daten verändern, haben ein `--dry-run`. Im Zweifel damit zuerst
schauen, was passieren würde.

### `gamedates:create`

Legt die Spieltermine an.

```bash
php artisan gamedates:create
php artisan gamedates:create --dry-run
```

- **Step 1:** vier feste Slots pro Tag (19:30, 21:30, 23:15, 01:00), immer 21 Tage im
  Voraus. Es wird nur hinter dem letzten vorhandenen Termin angehängt, damit manuell
  gelöschte Slots nicht wiederkommen.
- **Step 2/3:** dynamisch. Pro 10 aktive Ticket-Inhaber (in den letzten 21 Tagen
  gespielt) gibt es einen Termin pro Woche. Er wird für den Tag in 3 Tagen angelegt
  und ersetzt dort einen Step-1-Termin ohne Anmeldungen. Die Uhrzeit richtet sich
  nach der historischen Spielquote des jeweiligen Slots.
- **Step 4:** ab 10 Ticket-Inhabern der erste Freitag 19:30, der mindestens 10 Tage
  entfernt ist. Kommen weniger als 10 Anmeldungen zustande, folgt 8 Tage später ein
  neuer Termin im nächsten Slot (19:30 → 21:30 → 23:15 → 01:00).
- Nach einem Season-Wechsel werden offene Step-2+-Termine der alten Season ohne
  Anmeldungen wieder zu Step 1 (oder entfernt, wenn der Slot schon ein Step 1 hat).

Aufgerufen wird der Befehl über `~/.local/bin/create_gamedates.sh`.

### `ranking:recalculate`

Baut die `points`-Tabelle aus der `games`-Tabelle neu auf und leert danach den Cache.
Nötig, wenn Spiele direkt in `games` korrigiert wurden (z. B. die Startzeit), denn
das Ranking rechnet ausschließlich über `points`.

```bash
php artisan ranking:recalculate                 # laufende Season (Default)
php artisan ranking:recalculate --game=1234     # nur Spiel #1234 (games.number)
php artisan ranking:recalculate --season=12     # ab Season 12
php artisan ranking:recalculate --dry-run       # nur anzeigen
```

Beim Bearbeiten eines Spiels im Web läuft der Befehl automatisch für dieses eine Spiel.

> **Achtung:** `--all` rechnet auch alle historischen Seasons neu. Die alten Seasons
> sind in `points` nicht mit `games` konsistent. Ein voller Lauf würde abgeschlossene
> Rankings nachträglich verändern. `--all` deshalb nur bewusst und nur nach einem
> `--dry-run` verwenden.

### `tickets:sync`

Schreibt die Tickets aller Spieler mit mindestens einem gewerteten Spiel nach
`public/exp3/bbcbot/minidb.txt` (für den bbcbot). Läuft automatisch, sobald im Web
Tickets geändert werden.

```bash
php artisan tickets:sync
```

### `admins:sync`

Schreibt die Namen aller Admins und Superadmins nach
`public/exp3/bbcbot/bbcadmins.txt` (für den bbcbot). Läuft automatisch, sobald im Web
ein User geändert oder gelöscht wird.

```bash
php artisan admins:sync
```

### `sitemap:generate`

Erzeugt `public/sitemap.xml` mit allen öffentlich erreichbaren Seiten (Startseite,
Ergebnisse, Ranking, Hall of Fame, Spielerliste, aktive CMS-Seiten, Spielerprofile).
Einzelne Spielseiten sind `noindex` und fehlen deshalb bewusst.

```bash
php artisan sitemap:generate
php artisan sitemap:generate --dry-run              # nur URLs zählen
php artisan sitemap:generate --output=/tmp/s.xml    # anderes Ziel
```

Der Scheduler der pthranking-App (`/var/www/pokerth/pthranking`) ruft den Befehl
täglich um 04:30 und 16:30 auf. Die BBC-App selbst hat keinen eigenen Scheduler.

### Nützliche Laravel-Standardbefehle

```bash
php artisan migrate            # Datenbank-Migrationen ausführen
php artisan cache:clear        # Cache leeren (z. B. nach DB-Änderungen von Hand)
php artisan route:list         # alle Routen anzeigen
php artisan tinker             # interaktive Konsole
php artisan list               # alle verfügbaren Befehle
```

## Lizenz

MIT
