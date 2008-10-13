<?php
/**
*
* install [German]
*
* @package language
* @version $Id$
* @copyright (c) 2005 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* DO NOT CHANGE
*/
if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine

$lang = array_merge($lang, array(
	'CONFIG_PHPBB_EMPTY'		=> 'Die phpBB Garage Konfigurationsvariable für "%s" ist leer.',
	'CONVERT_COMPLETE_EXPLAIN'	=> 'Du hast deine Garage erfolgreich zu phpbb Garage 2.0 konvertiert. Du kannst dich jetzt einloggen und <a href="../../garage.php">auf deine Garage zugreifen</a>. Bitte vergewissere dich, dass die Einstellungen korrekt übernommen wurden, bevor du dein Board durch das Löschen des Installationsverzeichnisses aktivierst. Denke daran, dass Hilfe zum Gebrauch der phpBB Garage online verfügbar ist via <a href="http://docs.phpbbgarage.com/index.php">Dokumentation</a> und dem <a href="http://forums.phpbbgarage.com/viewforum.php?f=2">Support Forum</a>.',
	'CONVERT_INTRO'			=> 'Willkommen beim phpBB Garage Konvertierungs-System',
	'CONVERT_INTRO_BODY'		=> 'Hier kannst du Garage-Daten aus anderen (installierten) Datenbanksystemen importieren. Die nachfolgende Übersicht gibt Auskunft über alle derzeit verfügbaren Konvertierungsmodule. Wenn dort kein Konverter für die Forensoftware angezeigt wird, von der du konvertieren möchtest, prüfe bitte auf unserer Webseite, ob weitere Konvertierungsmodule zum Download bereitstehen.',

	'FILES_REQUIRED_EXPLAIN'	=> '<strong>Voraussetzung</strong> - phpBB Garage muss auf diverse Dateien und Verzeichnisse zugreifen oder diese beschreiben können, um reibungslos zu funktionieren. Wenn „Nicht gefunden“ angezeigt wird, musst du die entsprechende Datei oder das Verzeichnis erstellen. Wenn „Nicht beschreibbar“ angezeigt wird, musst du die Befugnisse für die Datei oder das Verzeichnis so ändern, dass phpBB Garage darauf schreiben kann.',

	'INSTALL_CONGRATS_EXPLAIN'	=> '
		<p>Du hast phpBB Garage %1$s nun erfolgreich installiert. Du hast nun zwei Optionen, was du mit deiner frisch installierten phpBB Garage machen kannst:</p>
		<h2>Ein bestehendes Garage-Board auf phpBB Garage konvertieren</h2>
		<p>Das phpBB Garage Konvertierungs-System unterstützt die Konvertierung von phpBB Garage 1.x.x und anderen Garage-Forensystemen zu phpBB Garage 2. Wenn du ein bestehendes Garage-Board konvertieren willst, fahre bitte <a href="%2$s">mit dem Konverter fort</a>.</p>
		<h2>Starte mit phpBB Garage 2 durch!</h2>
		<p>Wenn du unten auf die Schaltfläche klickst, wirst du zum Administrations-Bereich weitergeleitet. Nimm dir etwas Zeit, um die verfügbaren Optionen kennen zu lernen. Hilfe zum Gebrauch von phpBB Garage erhältst du online über die <a href="http://docs.phpbbgarage.com/">Dokumentation</a> und das <a href="http://forums.phpbbgarage.com/viewforum.php?f=2">Beta Support-Forum</a>. Für weitere Informationen lies die <a href="%3$s">README</a>-Datei.</p>',
	'UPDATE_CONGRATS_EXPLAIN'	=> '
		<p>Du hast phpBB Garage %1$s nun erfolgreich aktualisiert.',
	'REMOVE_INTRO'			=> 'Willkommen bei der Deinstallation',
	'REMOVE_INTRO_BODY'		=> 'Mit dieser Option kannst du phpBB Garage von deinem Server entfernen.</p>

	<p><strong>Note:</strong>Die Deinstallation wird die komplette phpBB Garage-Software entfernen. Einmal gelöscht lassen sich die Daten nicht wiederherstellen. Nur eine Wiederherstellung der Datenbank sowie der Dateien zu einem vorigen Zeitpunkt ermöglichen die Rückkehr zum Ausgangsstatus der phpBB Garage. Wenn du mit dem nächsten Schritt fortfährst, wird der Vorgang gestartet. FAHRE NICHT FORT, ES SEI DENN DU WEISST WAS DU TUST.</p>

	<p>phpBB Garage entfernt</p>
	<ul>
		<li>Alle erstellen Datenbankeinträge</li>
		<li>Alle Daten</li>
		<li>Alle phpBB Garage Dateien</li>
		<li>Alle phpBB Garage Module</li>
		<li>Alle phpBB Garage Berechtigungen</li>
		</ul>',

	'INSTALL_INTRO_BODY'		=> 'Dieser Assistent ermöglicht dir die Installation von phpBB Garage auf deinem Server.</p>

	<p><strong>Note:</strong>Dieser Assistent wird dich bei allen Schritten zur Datenbank und Bearbeitung der phpBB-Dateien begleiten. Bitte vergewissere dich, dass du die Anweisungen der Template- &amp; Sprach-MODX-Dateien zur Installation vollständig abgearbeitet hast.</p>

	<p>phpBB Garage unterstützt die folgenden Datenbanken:</p>
	<ul>
		<li>MySQL 3.23 oder höher (MySQLi-unterstützt)</li>
		<li>PostgreSQL 7.3+</li>
		<li>SQLite 2.8.2+</li>
		<li>Firebird 2.0+</li>
		<li>MS SQL Server 2000 oder höher (direkt oder via ODBC)</li>
		<li>Oracle</li>
	</ul>',
	'REMOVE_COMPLETE'		=> 'phpBB Garage gelöscht!!',
	'REMOVE_COMPLETE_EXPLAIN'	=> 'Die phpBB Garage und alle zugehörigen Komponenten wurden vollständig entfernt.',
	'PHP_REQUIRED_MODULE'		=> 'Erforderliche Module',
	'PHP_REQUIRED_MODULE_EXPLAIN'	=> '<strong>Voraussetzung</strong> - Diese Module oder Anwendungen sind erfoderlich.',

	'OVERVIEW_BODY'			=> 'Willkommen zu unserer veröffentlichten Beta-Version der nächsten Generation nach phpBB Garage 1.x.x, phpBB Garage 2.0! Dieses Release ist dazu gedacht, dass du uns helfen kannst, Bugs und Problemfelder zu entdecken.</p><p>Bitte lies <a href="../docs/INSTALL.html">unser Installations-Handbuch</a> für weitere Informationen zur Installation von phpBB Garage</p><p><strong style="text-transform: uppercase;">Beachte:</strong> Dieses Release ist <strong style="text-transform: uppercase;">noch keine Final-Version</strong>. Du kannst warten bis zum Final-Release, bevor du es auf einem Live-System betreibst.</p><p>Dieser Installations-Assistent führt dich durch den Installationsprozess der phpBB Garage, das Konvertieren von einer anderen Software oder die Aktualisierung auf die neueste Version von phpBB Garage. Für weitere Informationen zu den einzelnen Optionen wähle diese im obigen Menü.',

	'PRE_CONVERT_COMPLETE'		=> 'Alle vorbreitenden Schritte wurden erfolgreich abgeschlossen. Du kannst nun die Konvertierung starten. Bitte beachte, dass du einige Einstellungen manuell vornehmen musst. Nach der Konvertierung solltest du insbesondere die zugewiesenen Berechtigungen prüfen, falls erforderlich deinen Suchindex neu aufbauen und sicherstellen, dass die Dateien korrekt kopiert wurden, z.B. Avatar und Smilies.',
	'PROCESS_LAST'			=> 'Verarbeite abschließende Anweisungen',

	'REFRESH_PAGE'			=> 'Seite aktualisieren, um Konvertierung fortzusetzen',
	'REFRESH_PAGE_EXPLAIN'		=> 'Wenn auf Ja gesetzt, wird der Konverter die Seite aktualisieren, wenn er einen Schritt abgeschlossen hat. Wenn dies deine erste Konvertierung zu Testzwecken und um Fehler im Vorfeld festzustellen ist, empfehlen wir, dies auf Nein zu stellen.',
//	'REQUIRED'					=> 'Voraussetzung',
	'REQUIREMENTS_TITLE'		=> 'Installations-Kompatibilität',
	'REQUIREMENTS_EXPLAIN'		=> 'Bevor die Installation fortgesetzt werden kann, wird phpBB Garage einige Tests zu deiner Server-Konfiguration und deinen Dateien durchführen, um sicherzustellen, dass du phpBB Garage installieren und benutzen kannst. Bitte lies die Ergebnisse aufmerksam durch und fahre nicht weiter fort, bevor alle erforderlichen Tests bestanden sind. Falls du irgendeine der Funktionen, die unter den optionalen Modulen aufgeführt sind, nutzen möchtest, solltest du sicherstellen, dass die entsprechenden Tests auch bestanden werden.',
	'RETRY_WRITE'			=> 'Erneut versuchen, die Konfigurationsdatei zu schreiben',
	'RETRY_WRITE_EXPLAIN'		=> 'Wenn du möchtest, kannst du die Berechtigungen der config.php ändern, so dass sie phpBB schreiben kann. Mit „Erneut versuchen, die Konfigurationsdatei zu schreiben“ kannst du einen weiteren Versuch starten. Denke daran, die Berechtigungen der config.php nach der Installation wieder zurückzustellen.',

	'SCRIPT_PATH'			=> 'Scriptpfad',
	'SCRIPT_PATH_EXPLAIN'		=> 'er Pfad, in dem sich phpBB befindet, relativ zum Domainnamen. Z.&nbsp;B. <samp>/phpBB3</samp>.',
	'SELECT_LANG'			=> 'Sprache wählen',
	'SERVER_CONFIG'			=> 'Server-Konfiguration',
	'SEARCH_INDEX_UNCONVERTED'	=> 'Der Suchindex wurde nicht konvertiert',
	'SEARCH_INDEX_UNCONVERTED_EXPLAIN'	=> 'Dein alter Suchindex wurde nicht konvertiert. Eine Suche wird immer zu einem leeren Ergebnis führen. Um einen neuen Suchindex zu erstellen, gehe in den Administrations-Bereich, wähle dort das Register Wartung aus und rufe dann den Punkt Such-Indizes auf.',
	'SOFTWARE'			=> 'Garage-Software',
	'SPECIFY_OPTIONS'		=> 'Konvertierungs-Optionen festlegen',
	'STAGE_ADMINISTRATOR'		=> 'Administrator-Details',
	'STAGE_OPTIONAL'		=> 'Optionale Einstellungen',
	'STAGE_OPTIONAL_EXPLAIN'	=> 'Die Optionen auf dieser Seite ermöglichen dir, einige Standarddaten während der Installation zu erstellen. Diese Optionen sind keine Voraussetzung für die Installation. Falls du die Standards nicht verwenden möchtest, solltest du nach der Installation Komponenten wie Hersteller, Fabrikate &amp; Kategorien einrichten.',
	'STAGE_CONFIG_FILE'		=> 'Konfigurationsdatei',
	'STAGE_CREATE_TABLE'		=> 'Datenbank-Tabellen erstellen',
	'STAGE_CREATE_TABLE_EXPLAIN'	=> 'Die von phpBB Garage genutzten Datenbank-Tabellen wurden nun erstellt und mit einigen Ausgangswerten gefüllt. Geh weiter zum nächsten Schritt, um die Installation von phpBB Garage abzuschließen.',
	'STAGE_CREATE_PERMISSIONS'	=> 'Berechtigungen einrichten',
	'STAGE_CREATE_PERMISSIONS_EXPLAIN'	=> 'Neue Berechtigungen, die für phpBB Garage erforderlich sind, wurden erstellt und an Standardrollen vergeben, sofern diese existieren. Du solltest nach der Installation bestätigen, dass du mit den Berechtigungen zufrieden bist.',
	'STAGE_INSTALL_MODULES'		=> 'Module installieren',
	'STAGE_INSTALL_MODULES_EXPLAIN'	=> 'Die phpBB Garage Module wurden installiert.',
	'STAGE_DATABASE'		=> 'Datenbank-Einstellungen',
	'STAGE_FINAL'			=> 'Abschließender Schritt',
	'STAGE_INTRO'			=> 'Einführung',
	'STAGE_IN_PROGRESS'		=> 'Konvertierung wird durchgeführt',
	'STAGE_REQUIREMENTS'		=> 'Voraussetzungen',
	'STAGE_DATA'			=> 'Daten',
	'STAGE_DATA_EXPLAIN'		=> 'Alle phpBB Garage Daten sind nun gelöscht. Als nächstes werden alle Dateien gelöscht.',
	'STAGE_FILES'			=> 'Dateien',
	'STAGE_FILES_EXPLAIN'		=> 'Alle phpBB Garage Dateien sind nun gelöscht.',
	'STAGE_SETTINGS'		=> 'Einstellungen',
	'STARTING_CONVERT'		=> 'Starte Konvertierungsprozess',
	'STEP_PERCENT_COMPLETED'	=> 'Schritt <strong>%d</strong> von <strong>%d</strong>',
	'SUB_INTRO'			=> 'Einführung',
	'SUB_LICENSE'			=> 'Lizenz',
	'SUB_SUPPORT'			=> 'Support',
	'SUCCESSFUL_CONNECT'		=> 'Verbindung erfolgreich',
// TODO: Write some text on obtaining support
	'SUPPORT_BODY'			=> 'Während der Beta-Phase wird in den <a href="http://forums.phpbbgarage.com/">phpBB Garage Support Foren</a> nur eingeschränkter Support gewährt. Wir werden dort Antwort geben auf allgemeine Fragen zu Setup, Konfigurations- und Konvertierungsproblemen und leisten Support für häufig auftretende Probleme, die durch Bugs hervorgerufen wurden.',

	'WELCOME_INSTALL'		=> 'Willkommen zur phpBB Garage Installation',
	'WRITABLE'			=> 'Beschreibbar',
));

// Updater
$lang = array_merge($lang, array(
	'ALL_FILES_UP_TO_DATE'		=> 'Alle Dateien sind auf dem Stand der neuesten phpBB Garage-Version. Du solltest nun prüfen, ob alles einwandfrei funktioniert.',

	'CHECK_FILES_UP_TO_DATE'	=> 'Laut deiner Datenbank ist deine Version auf dem neuesten Stand. Du solltest mit der Dateiüberprüfung fortfahren, um sicher zu gehen, dass alle Dateien wirklich auf dem Stand der aktuellen phpBB Garage-Version sind.',
	'COLLECTED_INFORMATION_EXPLAIN'	=> 'Die folgende Liste zeigt dir die Dateien, die eine Aktualisierung benötigen. Bitte lies die Informationen vor jedem Abschnitt durch, um zu verstehen was passiert und was du möglicherweise tun musst, um ein erfolgreiches Update durchzuführen.',
	'COMPLETE_LOGIN_TO_BOARD'	=> 'Du solltest dich jetzt <a href="../ucp.php?mode=login">in deinem Forum anmelden</a> und prüfen, ob alles funktioniert. Vergiss nicht, das Installations-Verzeichnis „install“ zu löschen!',

	'FILE_USED'			=> 'Information benutzt von',			// Single file
	'FILES_CONFLICT'				=> 'Problematische Dateien',
	'FILES_CONFLICT_EXPLAIN'		=> 'Die folgenden Dateien wurden geändert und entsprechen nicht den Originaldateien der alten Version. phpBB hat festgestellt, dass die Dateien nicht zusammengeführt werden können, da beide Versionen verändert wurden. Bitte sieh dir diese Konflikte an und versuche, sie von Hand zu lösen oder fahre mit der Aktualisierung fort, indem du deine bevorzugte Methode des Zusammenführens auswählst. Wenn du die Konflikte von Hand löst, prüfe die Dateien nach der Bearbeitung erneut. Du kannst außerdem die Zusammenführungsmethode für jede Datei getrennt angeben. Die erste Methode erzeugt eine Datei, in der die problematischen Zeilen deiner alten Datei verworfen werden, die andere Methode verwirft die Änderungen in der neuen Datei.',
	'FILES_MODIFIED'				=> 'Geänderte Dateien',
	'FILES_MODIFIED_EXPLAIN'		=> 'Die folgenden Dateien wurden geändert und entsprechen nicht den Originaldateien der alten Version. In der aktualisierten Version werden deine Änderungen und die Neuerungen der phpBB-Datei zusammengeführt.',
	'FILES_NEW'						=> 'Neue Dateien',
	'FILES_NEW_EXPLAIN'				=> 'Gegenwärtig fehlen die folgenden Dateien in deiner Installation. Sie werden dieser hinzugefügt.',
	'FILES_NEW_CONFLICT'			=> 'Neue Dateien, die bereits existieren',
	'FILES_NEW_CONFLICT_EXPLAIN'	=> 'Die folgenden Dateien sind neu innerhalb der aktuellen Version, aber es wurde festgestellt, dass bereits eine Datei mit dem gleichen Namen im gleichen Verzeichnis existiert. Diese wird durch die neue Datei überschrieben.',
	'FILES_NOT_MODIFIED'			=> 'Nicht geänderte Dateien',
	'FILES_NOT_MODIFIED_EXPLAIN'	=> 'Die folgenden Dateien sind nicht geändert und entsprechen den originalen phpBB-Dateien der Version, von der aus du updaten willst.',
	'FILES_UP_TO_DATE'				=> 'Bereits aktualisierte Dateien',
	'FILES_UP_TO_DATE_EXPLAIN'		=> 'Die folgenden Dateien sind bereits auf dem neuesten Stand und müssen nicht aktualisiert werden.',
	'FTP_SETTINGS'					=> 'FTP-Einstellungen',
	'FTP_UPDATE_METHOD'				=> 'FTP-Upload',
	'INCOMPATIBLE_UPDATE_FILES'		=> 'Die gefundenen Update-Dateien sind inkompatibel zu deiner installierten Version. Deine phpBB Garage-Version ist %1$s. Das Update-Paket aktualisiert Version %2$s auf %3$s.',
	'INCOMPATIBLE_REMOVE_FILES'	=> 'Die gefundenen Deinstallations-Dateien sind inkompatibel zu deiner installierten Version. Deine phpBB Garage-Version ist is %1$s und die Deinstallations-Datei ist für phpBB Garage %2$s.',
	'INCOMPLETE_UPDATE_FILES'		=> 'Das Update-Paket ist unvollständig.',
	'INLINE_UPDATE_SUCCESSFUL'		=> 'Die Aktualisierung der Datenbank war erfolgreich. Du musst nun den Update-Prozess fortsetzen.',

	'KEEP_OLD_NAME'			=> 'Benutzernamen beibehalten',

	'NO_UPDATE_FILES_EXPLAIN'	=> 'Die folgenden Dateien sind neu oder wurden verändert. Das Verzeichnis, in dem sie sich normalerweise befinden, konnte jedoch in deiner Installation nicht gefunden werden. Wenn diese Liste Dateien in anderen Verzeichnissen als language/ oder styles/ enthält, so hast du möglicherweise deine Verzeichnissturktur geändert und das Update könnte unvollständig sein.',
	'NO_UPDATE_FILES_OUTDATED'		=> 'Es wurde kein gültiges Aktualisierungsverzeichnis gefunden. Bitte stelle sicher, dass du die entsprechenden Dateien hochgeladen hast.<br /><br />Deine Installation scheint <strong>nicht</strong> auf dem neuesten Stand zu sein. Für deine phpBB Garage-Version %1$s sind Updates verfügbar. Bitte besuche <a href="http://downloads.phpbbgarage.com/" rel="external">http://downloads.phpbbgarage.com/</a>, um das richtige Packet für das Update von Version %2$s auf Version %3$s herunterzuladen.',
	'NO_UPDATE_FILES_UP_TO_DATE'	=> 'Deine Version ist auf dem neuesten Stand. Es ist nicht nötig, das Update-Tool auszuführen. Wenn du eine Integritätsprüfung der Dateien ausführen möchtest, stelle sicher, dass du das richtige Update-Paket hochgeladen hast.',
	'NO_UPDATE_INFO'				=> 'Information zu den Update-Paketen konnte nicht gefunden werden.',
	'NO_UPDATES_REQUIRED'			=> 'Kein Update notwendig',
	'NO_VISIBLE_CHANGES'			=> 'Keine sichtbaren Änderungen',
	'NOTICE'						=> 'Hinweis',
	'NUM_CONFLICTS'					=> 'Anzahl der Konflikte',

	'OLD_UPDATE_FILES'		=> 'Die Update-Dateien sind nicht auf dem neuesten Stand. Die gefundenen Update-Dateien sind für ein Update von phpBB %1$s auf phpBB %2$s, aber die neueste Version von phpBB ist %3$s.',

	'PACKAGE_UPDATES_TO'				=> 'Dieses Paket aktualisiert auf Version',
	'PERFORM_DATABASE_UPDATE'			=> 'Datenbankaktualisierung durchführen',
	'PERFORM_DATABASE_UPDATE_EXPLAIN'	=> 'Weiter unten findest du eine Schaltfläche zum Skript für die Datenbank-Aktualisierung. Die Aktualisierung der Datenbank kann eine Weile dauern, also unterbreche bitte die Ausführung nicht, falls sie zu hängen scheint. Nachdem die Datenbank-Aktualisierung durchgeführt wurde, folge bitte den Hinweisen, um den Update-Prozess fortzusetzen.',
	'PREVIOUS_VERSION'					=> 'Vorherige Version',
	'PROGRESS'							=> 'Fortschritt',

	'RESULT'					=> 'Ergebnis',
	'RUN_DATABASE_SCRIPT'		=> 'Datenbank jetzt aktualisieren',

	'SELECT_DIFF_MODE'			=> 'Unterschiedmodus auswählen',
	'SELECT_DOWNLOAD_FORMAT'	=> 'Format des Download-Archivs wählen',
	'SELECT_FTP_SETTINGS'		=> 'FTP-Einstellungen auswählen',
	'SHOW_DIFF_CONFLICT'		=> 'Unterschiede/Konflikte zeigen',
	'SHOW_DIFF_FINAL'			=> 'Die sich ergebende Datei zeigen',
	'SHOW_DIFF_MODIFIED'		=> 'Zusammengefügte Unterschiede anzeigen',
	'SHOW_DIFF_NEW'				=> 'Dateiinhalte zeigen',
	'SHOW_DIFF_NEW_CONFLICT'	=> 'Unterschiede zeigen',
	'SHOW_DIFF_NOT_MODIFIED'	=> 'Unterschiede zeigen',
	'SOME_QUERIES_FAILED'		=> 'Einige Abfragen sind gescheitert. Die Abfragen und die zugehörigen Fehler sind weiter unten aufgeführt.',
	'SQL'						=> 'SQL',
	'SQL_FAILURE_EXPLAIN'		=> 'Dies ist in der Regel nicht kritisch, die Aktualisierung wird fortgeführt. Sollte deren Fertigstellung scheitern, musst du möglicherweise Hilfe in unserem Supportforum in Anspruch nehmen. Details, wie und wo du Hilfe bekommst, kannst du der <a href="../docs/README.html">README-Datei</a> entnehmen.',
	'STAGE_FILE_CHECK'			=> 'Dateien überprüfen',
	'STAGE_UPDATE_DB'			=> 'Datenbank aktualisieren',
	'STAGE_UPDATE_FILES'		=> 'Dateien aktualisieren',
	'STAGE_VERSION_CHECK'		=> 'Versionsprüfung	',
	'STATUS_CONFLICT'			=> 'Geänderte Datei, die Konflikte verursacht',
	'STATUS_MODIFIED'			=> 'Veränderte Datei',
	'STATUS_NEW'				=> 'Neue Datei',
	'STATUS_NEW_CONFLICT'		=> 'Problematische neue Datei',
	'STATUS_NOT_MODIFIED'		=> 'Unveränderte Datei',
	'STATUS_UP_TO_DATE'			=> 'Bereits aktualisierte Datei',

	'UPDATE_COMPLETED'				=> 'Update abgeschlossen',
	'UPDATE_DATABASE'				=> 'Datenbank jetzt aktualisieren',
	'UPDATE_DATABASE_EXPLAIN'		=> 'Im nächsten Schritt wird die Datenbank aktualisiert.',
	'UPDATE_DATABASE_SCHEMA'		=> 'Datenbankstruktur wird aktualisiert',
	'UPDATE_FILES'					=> 'Dateien werden aktualisiert',
	'UPDATE_FILES_NOTICE'			=> 'Bitte stelle sicher, dass du auch die Dateien des Boards aktualisiert hast. Diese Datei aktualisiert nur die Datenbank.',
	'UPDATE_INSTALLATION'			=> 'Update der phpBB Garage-Installation',
	'UPDATE_INSTALLATION_EXPLAIN'	=> 'Mit dieser Option kannst du deine phpBB Garage-Version auf den neuesten Stand bringen.<br />Während dieses Prozesses wird die Integrität aller deiner Dateien überprüft. Du hast die Möglichkeit, alle Dateiunterschiede vor dem Update zu überprüfen.<br /><br />Die Dateiaktualisierung an sich kann auf zwei Wegen erfolgen:</p><h2>Manuelle Aktualisierung</h2><p>Bei dieser Methode lädst du nur die von dir geänderten Dateien herunter, damit du sichergehen kannst, dass die erfolgten Dateiänderungen nicht verloren gehen. Nach dem Herunterladen dieses Archivs musst du die Dateien in die entsprechenden Verzeichnisse deiner phpBB Garage-Installation hochladen. Nachdem du das getan hast, kannst du die Dateiüberprüfung erneut ausführen, um zu sehen, ob du alle Dateien korrekt hochgeladen hast.</p><h2>Automatische Aktualisierung über FTP</h2><p>Diese Methode ist der ersten sehr ähnlich, mit dem Unterschied, dass du die veränderten Dateien nicht herunter- und anschließend von Hand wieder hochladen musst. Dies wird automatisch erledigt. Um diese Methode nutzen zu können, musst du deine FTP-Anmeldedaten kennen und eingeben. Nach der Fertigstellung wird auch hier eine Integritätsprüfung der Dateien ausgeführt.',
	'UPDATE_INSTRUCTIONS'			=> '

		<h1>Bekanntmachungen zur Veröffentlichung</h1>

		<p>Bitte lies <a href="%1$s" title="%1$s"><strong>die Bekanntmachung zur Veröffentlichung der neuesten Version</strong></a> bevor du den Update-Prozess beginnst, sie enthält wichtige Informationen. Außerdem enthält sie die Download-Links sowie ein Änderungsprotokoll (Changelog) der Versionen.</p>

		<br />

		<h1>Wie du ein Update deiner Installation mit dem „Automatisches-Update-Paket“ durchführst</h1>

		<p>Diese empfohlene Anleitung zum Update deiner Installation gilt nur für das „Automatisches-Update-Paket“ („automatic update package“). Du kannst deine Installation auch mit den in der INSTALL.html beschriebenen Methoden aktualisieren. Zum automatischen Update von phpBB Garage musst du folgende Schritte ausführen:</p>

		<ul style="margin-left: 20px; font-size: 1.1em;">
			<li>Gehe zur <a href="http://downloads.phpbbgarage.com/" title="http://downloads.phpbbgarage.com/">phpBB Garage-Downloadseite</a> und lade das entsprechende „Automatisches-Update-Paket“ herunter (<a href="http://www.phpbb.de/go/3/downloads">deutschsprachige Downloadseite</a>).<br /><br /></li>
			<li>Entpacke das Archiv.<br /><br /></li>
			<li>Lade das entpackte Installationsverzeichnis komplett in dein phpBB Garage-Hauptverzeichnis (dort, wo die config.php ist).<br /><br /></li>
		</ul>

		<p>Nach dem Upload wird das Forum vorübergehend für normale Benutzer nicht zugänglich sein, da das von dir hochgeladene Installations-Verzeichnis vorhanden ist.<br /><br />
		<strong><a href="%2$s" title="%2$s">Starte nun den Update-Prozess, indem du in deinem Webbrowser die Adresse zum Installationsverzeichnis angibst</a>.</strong><br />
		<br />
		Anschließend wirst du durch den Update-Prozess geführt. Du wirst benachrichtigt, sobald das Update abgeschlossen ist.
		</p>
	',
	'UPDATE_INSTRUCTIONS_INCOMPLETE'	=> '

		<h1>Unvollständiges Update gefunden</h1>

		<p>phpBB Garage hat ein unvollständiges automatisches Update gefunden. Bitte stell sicher, dass du jeden Schritt des automatischen Updates durchgeführt hast. Du findest unten nochmals den Link oder rufe das „install“-Verzeichnis direkt auf.</p>
		',
	'VERSION_CHECK'				=> 'Versionsprüfung',
	'VERSION_CHECK_EXPLAIN'		=> 'Prüft, ob die phpBB Garage-Version, die du einsetzt, auf dem neuesten Stand ist.',
	'VERSION_NOT_UP_TO_DATE'	=> 'Deine Version von phpBB Garage ist nicht auf dem neuesten Stand. Bitte fahre mit der Aktualisierung fort.',
	'VERSION_NOT_UP_TO_DATE_ACP'=> 'Deine Version von phpBB Garage ist nicht auf dem neuesten Stand.<br />Im Folgenden findest du einen Link zu der Release-Ankündigung der neuesten Version sowie Informationen, wie du deine Version aktualisieren kannst.',
	'VERSION_UP_TO_DATE'		=> 'Deine Installation ist auf dem neuesten Stand, für deine phpBB Garage-Version sind keine Updates verfügbar. Du kannst trotzdem fortfahren und die Dateien auf Gültigkeit überprüfen.',
	'VERSION_UP_TO_DATE_ACP'	=> 'Deine Installation ist auf dem neuesten Stand, für deine phpBB Garage-Version sind keine Updates verfügbar. Es ist nicht nötig, die Installation zu aktualisieren.',

	'INSERT_OPTIONS'		=> 'Optionale Daten',
	'INSERT_MAKES'			=> 'Hersteller einfügen',
	'INSERT_MAKES_EXPLAIN'		=> 'fügt einen Standardsatz von Herstellern ein',
	'INSERT_CATEGORIES'		=> 'Kategorien einfügen',
	'INSERT_CATEGORIES_EXPLAIN'	=> 'fügt einen Standardsatz von Modifikationskategorien ein',
	'CURRENT_VERSION'				=> 'Derzeit installierte Version',
	'LATEST_VERSION'		=> 'Neueste Version',

));

?>