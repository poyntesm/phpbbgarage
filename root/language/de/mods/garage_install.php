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

	'FILE_USED'			=> 'Information used from',			// Single file
	'FILES_CONFLICT'		=> 'Conflict files',
	'FILES_CONFLICT_EXPLAIN'	=> 'The following files are modified and do not represent the original files from the old version. phpBB determined that these files create conflicts if they are tried to be merged. Please investigate the conflicts and try to manually resolve them or continue the update choosing the preferred merging method. If you resolve the conflicts manually check the files again after you modified them. You are also able to choose between the preferred merge method for every file. The first one will result in a file where the conflicting lines from your old file will be lost, the other one will result in loosing the changes from the newer file.',
	'FILES_MODIFIED'		=> 'Modified files',
	'FILES_MODIFIED_EXPLAIN'	=> 'The following files are modified and do not represent the original files from the old version. The updated file will be a merge between your modifications and the new file.',
	'FILES_NEW'			=> 'New files',
	'FILES_NEW_EXPLAIN'		=> 'The following files currently do not exist within your installation.',
	'FILES_NEW_CONFLICT'		=> 'New conflicting files',
	'FILES_NEW_CONFLICT_EXPLAIN'	=> 'The following files are new within the latest version but it has been determined that there is already a file with the same name within the same position. This file will be overwritten by the new file.',
	'FILES_NOT_MODIFIED'		=> 'Not modified files',
	'FILES_NOT_MODIFIED_EXPLAIN'	=> 'The following files were not modified and represent the original phpBB files from the version you want to update from.',
	'FILES_UP_TO_DATE'		=> 'Already updated files',
	'FILES_UP_TO_DATE_EXPLAIN'	=> 'The following files are already up to date and do not need to be updated.',
	'FTP_SETTINGS'			=> 'FTP settings',
	'FTP_UPDATE_METHOD'		=> 'FTP upload',
	'INCOMPATIBLE_UPDATE_FILES'	=> 'The update files found are incompatible with your installed version. Your installed version is %1$s and the update file is for updating phpBB Garage %2$s to %3$s.',
	'INCOMPATIBLE_REMOVE_FILES'	=> 'The remove files found are incompatible with your installed version. Your installed version is %1$s and the remove file is for phpBB Garage %2$s.',
	'INCOMPLETE_UPDATE_FILES'	=> 'The update files are incomplete.',
	'INLINE_UPDATE_SUCCESSFUL'	=> 'The database update was successful. Now you need to continue the update process.',

	'KEEP_OLD_NAME'			=> 'Keep username',

	'NO_UPDATE_FILES_EXPLAIN'	=> 'The following files are new or modified but the directory they normally reside in could not be found on your installation. If this list contains files to other directories than language/ or styles/ than you may have modified your directory structure and the update may be incomplete.',
	'NO_UPDATE_FILES_OUTDATED'	=> 'No valid update directory was found, please make sure you uploaded the relevant files.<br /><br />Your installation does <strong>not</strong> seem to be up to date. Updates are available for your version of phpBB Garage %1$s, please visit <a href="http://www.phpbbgarage.com/downloads/" rel="external">http://www.phpbbgarage.com/downloads/</a> to obtain the correct package to update from Version %2$s to Version %3$s.',
	'NO_UPDATE_FILES_UP_TO_DATE'	=> 'Your version is up to date. There is no need to run the update tool. If you want to make an integrity check on your files make sure you uploaded the correct update files.',
	'NO_UPDATE_INFO'		=> 'Update file information could not be found.',
	'NO_UPDATES_REQUIRED'		=> 'No updates required',
	'NO_VISIBLE_CHANGES'		=> 'No visible changes',
	'NOTICE'			=> 'Notice',
	'NUM_CONFLICTS'			=> 'Number of conflicts',

	'OLD_UPDATE_FILES'		=> 'Update files are out of date. The update files found are for updating from phpBB %1$s to phpBB %2$s but the latest version of phpBB is %3$s.',

	'PACKAGE_UPDATES_TO'		=> 'Current package updates to version',
	'PERFORM_DATABASE_UPDATE'	=> 'Perform database update',
	'PERFORM_DATABASE_UPDATE_EXPLAIN'	=> 'Below you will find a button to the database update script. The database update can take a while, so please do not stop the execution if it seems to hang. After the database update has been performed just follow the instructions to continue the update process.',
	'PREVIOUS_VERSION'		=> 'Previous version',
	'PROGRESS'			=> 'Progress',

	'RESULT'			=> 'Result',
	'RUN_DATABASE_SCRIPT'		=> 'Update my database now',

	'SELECT_DIFF_MODE'		=> 'Select diff mode',
	'SELECT_DOWNLOAD_FORMAT'	=> 'Select download archive format',
	'SELECT_FTP_SETTINGS'		=> 'Select FTP settings',
	'SHOW_DIFF_CONFLICT'		=> 'Show differences/conflicts',
	'SHOW_DIFF_FINAL'		=> 'Show resulting file',
	'SHOW_DIFF_MODIFIED'		=> 'Show merged differences',
	'SHOW_DIFF_NEW'			=> 'Show file contents',
	'SHOW_DIFF_NEW_CONFLICT'	=> 'Show differences',
	'SHOW_DIFF_NOT_MODIFIED'	=> 'Show differences',
	'SOME_QUERIES_FAILED'		=> 'Some queries failed, the statements and errors are listing below.',
	'SQL'				=> 'SQL',
	'SQL_FAILURE_EXPLAIN'		=> 'This is probably nothing to worry about, update will continue. Should this fail to complete you may need to seek help at our support forums. See <a href="../docs/README.html">README</a> for details on how to obtain advice.',
	'STAGE_FILE_CHECK'		=> 'Check files',
	'STAGE_UPDATE_DB'		=> 'Update database',
	'STAGE_UPDATE_FILES'		=> 'Update files',
	'STAGE_VERSION_CHECK'		=> 'Version check',
	'STATUS_CONFLICT'		=> 'Modified file producing conflicts',
	'STATUS_MODIFIED'		=> 'Modified file',
	'STATUS_NEW'			=> 'New file',
	'STATUS_NEW_CONFLICT'		=> 'Conflicting new file',
	'STATUS_NOT_MODIFIED'		=> 'Not modified file',
	'STATUS_UP_TO_DATE'		=> 'Already updated file',

	'UPDATE_COMPLETED'		=> 'Update completed',
	'UPDATE_DATABASE'		=> 'Update database',
	'UPDATE_DATABASE_EXPLAIN'	=> 'Within the next step the database will be updated.',
	'UPDATE_DATABASE_SCHEMA'	=> 'Updating database schema',
	'UPDATE_FILES'			=> 'Update files',
	'UPDATE_FILES_NOTICE'		=> 'Please make sure you have updated your board files too, this file is only updating your database.',
	'UPDATE_INSTALLATION'		=> 'Update phpBB Garage installation',
	'UPDATE_INSTALLATION_EXPLAIN'	=> 'With this option, it is possible to update your phpBB Garage installation to the latest version.<br />During the process all of your files will be checked for their integrity. You are able to review all differences and files before the update.<br /><br />The file update itself can be done in two different ways.</p><h2>Manual Update</h2><p>With this update you only download your personal set of changed files to make sure you do not lose your file modifications you may have done. After you downloaded this package you need to manually upload the files to their correct position under your phpBB Garage root directory. Once done, you are able to do the file check stage again to see if you moved the files to their correct location.</p><h2>Automatic Update with FTP</h2><p>This method is similar to the first one but without the need to download the changed files and uploading them on your own. This will be done for you. In order to use this method you need to know your FTP login details since you will be asked for them. Once finished you will be redirected to the file check again to make sure everything got updated correctly.<br /><br />',
	'UPDATE_INSTRUCTIONS'			=> '

		<h1>Release announcement</h1>

		<p>Please read <a href="%1$s" title="%1$s"><strong>the release announcement for the latest version</strong></a> before you continue your update process, it may contain useful information. It also contains full download links as well as the change log.</p>

		<br />

		<h1>How to update your installation with the Automatic Update Package</h1>

		<p>The recommended way of updating your installation listed here is only valid for the automatic update package. You are also able to update your installation using the methods listed within the INSTALL.html document. The steps for updating phpBB Garage  automatically are:</p>

		<ul style="margin-left: 20px; font-size: 1.1em;">
			<li>Go to the <a href="http://www.phpbbgarage.com/downloads/" title="http://www.phpbbgarage.com/downloads/">phpBBGarage.com downloads page</a> and download the "Automatic Update Package" archive.<br /><br /></li>
			<li>Unpack the archive.<br /><br /></li>
			<li>Upload the complete uncompressed install folder to your phpBB Garage root directory (where your config.php file is).<br /><br /></li>
		</ul>

		<p>Once uploaded your board will be offline for normal users due to the install directory you uploaded now present.<br /><br />
		<strong><a href="%2$s" title="%2$s">Now start the update process by pointing your browser to the install folder</a>.</strong><br />
		<br />
		You will then be guided through the update process. You will be notified once the update is complete.
		</p>
	',
	'UPDATE_INSTRUCTIONS_INCOMPLETE'	=> '

		<h1>Unvollständige Aktualisierung erkannt</h1>

		<p>phpBB Garage hat eine unvollständige automatische Aktualisierung erkannt. Bitte stelle sicher, dass du jeden Schritt innerhalb des automatischen Aktualisierungs-Assistenten befolgt hast. Nachfolgend findest du den Link erneut or go directly to your install directory.</p>
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
