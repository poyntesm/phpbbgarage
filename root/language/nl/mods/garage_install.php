<?php
/**
*
* install [Dutch]
*
* @package language translated by Roblom from www.deCRXgarage.nl
* @version $Id: install.php,v 1.119 2007/07/24 15:17:47 acydburn Exp $
* @copyright (c) 2005 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* NIET VERANDEREN
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
	'CONFIG_PHPBB_EMPTY'				=> 'De phpBB Garage configuratie variabele voor "%s" is leeg.',
	'CONVERT_COMPLETE_EXPLAIN'			=> 'De conversie van je garage naar phpBB Garage 2.0 is succesvol afgerond. Je kunt nu inloggen en <a href="../../garage.php">je garage bezoeken</a>. Wees er zeker van dat alle instellingen succesvol overgezet zijn voordat het forum word geactiveerd door de installatie folder te verwijderen. Onthoud dat hulp voor het gebruik van de phpBB Garage online beschikbaar is via de <a href="http://www.phpbbgarage.com/support/documentation/2.0/">Documentatie</a> en de <a href="http://www.phpbbgarage.com/community/viewforum.php?f=4">beta support fora</a>.',
	'CONVERT_INTRO'						=> 'Welkom op het phpBB Garage Unified Convertor Framework',
	'CONVERT_INTRO_BODY'				=> 'Vanaf hier kun je data importeren van andere (ge&iumlnstalleerde) garage forum systemen. De onderstaande lijst toont de coversie modules die momenteel beschikbaar zijn. Als het door jou gezochte conversie module niet in de lijst voorkomt, bezoek dan onze website waar mogelijk meer conversie modules beschikbaar zijn om te downloaden.',

	'FILES_REQUIRED_EXPLAIN'			=> '<strong>Verplicht</strong> - Voor het goed functioneren van de phpBB Garage is het noodzakelijk dat bepaalde files en mappen toegankelijk en beschrijfbaar zijn. Als er “Niet gevonden” word weergegeven dient het relevante bestand of map aangemaakt te worden. Als “Onbeschrijfbaar” word weergegeven dienen de toegangsrechten van het bestand of map gewijzigd te worden om de phpBB Garage schrijfmogelijkheden te geven.',

	'INSTALL_CONGRATS_EXPLAIN'			=> '
		<p>Je hebt nu phpBB Garage %1$s succesvol ge&iumlnstalleerd. Vanaf hier zijn er twee mogelijkheden wat te doen met de nieuw ge&iumlnstalleerde phpBB Garage:</p>
		<h2>Converteer een bestaand garage forum naar de nieuwe phpBB Garage</h2>
		<p>De phpBB Garage Unified Convertor Framework ondersteund de conversie van phpBB Garage 1.x.x en andere garage forum systemen naar phpBB Garage 2. Als je een bestaande garage hebt die je wil converteren <a href="%2$s">ga dan verder naar de conversie tool</a>.</p>
		<h2>Maak de phpBB Garage 2 online toegankelijk!</h2>
		<p>Klik op onderstaande button om naar het Administratie Controle Paneel (ACP) te gaan. Neem even de tijd om de beschikbare opties te verkennen. Onthoud dat er online hulp beschikbaar is op de <a href="http://www.phpbbgarage.com/support/documentation/3.0/">Documentatie</a> en de <a href="http://www.phpbbgarage.com/community/viewforum.php?f=4">beta support fora</a>, bekijk ook de <a href="%3$s">README</a> voor verdere informatie.</p>',
	'UPDATE_CONGRATS_EXPLAIN'			=> '
		<p>De update naar phpBB Garage %1$s is succesvol afgerond.',
	'REMOVE_INTRO'						=> 'Welkom op de de&iumlnstallatie',
	'REMOVE_INTRO_BODY'					=> 'Met deze optie is het mogelijk om phpBB Garage van de server te verwijderen.</p>

	<p><strong>Opmerking:</strong> deze de&iumlnstallatie verwijdert de complete phpBB Garage software. Als de data eenmaal is verwijdert is er geen herstel optie. Alleen een backup van de database en de bestanden van voor het uitvoeren van deze actie kunnen de phpBB Garage terug zetten naar zijn vorige staat. Door op de volgende stap te klikken zal het deinstallatie proces gestart worden. GAN NIET VERDER TENZIJ JE HET ZEKER WEET.</p>

	<p>phpBB Garage verwijdert</p>
	<ul>
		<li>Alle aangemaakte tabellen</li>
		<li>Alle data</li>
		<li>Alle phpBB Garage bestanden</li>
		<li>Alle phpBB Garage modules</li>
		<li>Alle phpBB Garage permissies</li>
		</ul>',
	'INSTALLMIN'						=> 'Installatie',
	'INSTALL_INTRO_BODY'				=> 'Met deze optie is het mogelijk om phpBB Garage op jouw server te installeren.</p>

	<p><strong>Opmerking:</strong> Deze installatie tool helpt je stap voor stap door alle database gerelateerde wijzigingen. Ook helpt hij bij het bewerken van de core phpBB bestanden. Lees om de installatie af te ronden de MODX bestanden van de template en taal bestanden.</p>

	<p>phpBB Garage ondersteund de volgende databases:</p>
	<ul>
		<li>MySQL 3.23 of hoger (MySQLi ondersteund)</li>
		<li>PostgreSQL 7.3+</li>
		<li>SQLite 2.8.2+</li>
		<li>Firebird 2.0+</li>
		<li>MS SQL Server 2000 of hoger (direct of via ODBC)</li>
		<li>Oracle</li>
	</ul>',
	'REMOVE_COMPLETE'					=> 'phpBB Garage verwijderd!!',
	'REMOVE_COMPLETE_EXPLAIN'			=> 'Some text about removal post checks here',
	'PHP_REQUIRED_MODULE'				=> 'Benodigde modules',
	'PHP_REQUIRED_MODULE_EXPLAIN'		=> '<strong>Benodigd</strong> - Deze modules of applicaties zijn benodigd.',

	'OVERVIEW_BODY'						=> 'Welkom op onze publieke beta versie van de volgende generatie phpBB Garage na 1.x.x, phpBB Garage 2.0! Deze uitgave is bedoeld om ons te helpen bugs en problematische gebieden te identificeren.</p><p>Lees <a href="../docs/INSTALL.html">onze installatie handleiding</a> voor meer informatie over het installeren van phpBB Garage</p><p><strong style="text-transform: uppercase;">Opmerking:</strong> Deze uitgave is nogsteeds<strong style="text-transform: uppercase;"> geen definitieve versie</strong>. Het is aan te raden te wachten op de definitieve versie voordat je het live laat draaien.</p><p>Dit installatie systeem leid je door de volgende stappen: het phpBB Garage installatie proces, converteren van een ander software pakket of updaten naar de laatste versie van phpBB Garage. Selecteer de optie in het bovenstaande menu voor meer informatie over deze optie.',

	'PRE_CONVERT_COMPLETE'				=> 'All pre-conversion steps have successfully been completed. You may now begin the actual conversion process. Please note that you may have to manually adjust several things. After conversion, especially check the permissions assigned, rebuild your search index if necessary and also make sure files got copied correctly, for example avatars and smilies.',
	'PROCESS_LAST'						=> 'Processing last statements',

	'REFRESH_PAGE'						=> 'Ververs de pagina om verder te gaan met de conversie.',
	'REFRESH_PAGE_EXPLAIN'				=> 'If set to yes, the convertor will refresh the page to continue the conversion after having finished a step. If this is your first conversion for testing purposes and to determine any errors in advance, we suggest to set this to No.',
//	'REQUIRED'							=> 'Benodigd',
	'REQUIREMENTS_TITLE'				=> 'Installation compatibility',
	'REQUIREMENTS_EXPLAIN'				=> 'Before proceeding with the full installation phpBB Garage will carry out some tests on your server configuration and files to ensure that you are able to install and run phpBB Garage. Please ensure you read through the results thoroughly and do not proceed until all the required tests are passed. If you wish to use any of the features depending on the optional tests, you should ensure that these tests are passed also.',
	'RETRY_WRITE'						=> 'Retry writing config',
	'RETRY_WRITE_EXPLAIN'				=> 'If you wish you can change the permissions on config.php to allow phpBB to write to it. Should you wish to do that you can click Retry below to try again. Remember to return the permissions on config.php after phpBB has finished installation.',

	'SCRIPT_PATH'						=> 'Script pad',
	'SCRIPT_PATH_EXPLAIN'				=> 'Het pad waar phpBB is ge&iumlnstalleerd relatief aan de domein naam, bijvoorbeeld <samp>/phpBB3</samp>.',
	'SELECT_LANG'						=> 'Selecteer taal',
	'SERVER_CONFIG'						=> 'Server configuratie',
	'SEARCH_INDEX_UNCONVERTED'			=> 'Zoek index is niet geconverteerd',
	'SEARCH_INDEX_UNCONVERTED_EXPLAIN'	=> 'De oude zoek index is niet geconverteerd. Elke zoekopdracht zal daarom geen resultaten opleveren. Om een zoek index aan te maken ga naar: Beheerderspaneel, selecteer onderhoud en kies dan in het submenu voor Zoek index.',
	'SOFTWARE'							=> 'Garage software',
	'SPECIFY_OPTIONS'					=> 'Specificeer conversie opties',
	'STAGE_ADMINISTRATOR'				=> 'Administrator details',
	'STAGE_OPTIONAL'					=> 'Optionele instellingen',
	'STAGE_OPTIONAL_EXPLAIN'			=> 'The options on this page allow you to have some default data created during the install. The options here are not required for install, however if you do not use the defaults you will need to setup items such as makes, models & categories after the installation.',
	'STAGE_CONFIG_FILE'					=> 'Configuratie bestand',
	'STAGE_CREATE_TABLE'				=> 'Database tabellen aanmaken',
	'STAGE_CREATE_TABLE_EXPLAIN'		=> 'The database tables used by phpBB Garage have been created and populated with required data and if selected some optional data. Proceed to the next screen to install new permissions required by phpBB Garage.',
	'STAGE_CREATE_PERMISSIONS'			=> 'Permissies aanmaken',
	'STAGE_CREATE_PERMISSIONS_EXPLAIN'	=> 'Nieuwe permissies die benodigd zijn voor phpBB Garage zijn aangemaakt en toegekend aan standaard rollen als deze aanwezig zijn. Na de installatie dien je de wijzigingen te bevestigen als je tevreden bent met deze permissies.',
	'STAGE_INSTALL_MODULES'				=> 'Modules installeren',
	'STAGE_INSTALL_MODULES_EXPLAIN'		=> 'De phpBB Garage modules zijn ge&iumlnstalleerd.',
	'STAGE_DATABASE'					=> 'Instellingen database',
	'STAGE_FINAL'						=> 'Laastste stap',
	'STAGE_INTRO'						=> 'Introductie',
	'STAGE_IN_PROGRESS'					=> 'Conversie in uitvoering',
	'STAGE_REQUIREMENTS'				=> 'Benodigdheden',
	'STAGE_DATA'						=> 'Data',
	'STAGE_DATA_EXPLAIN'				=> 'Alle phpBB Garage data is nu verwijdert. De volgende stap zal alle bestanden verwijderen.',
	'STAGE_FILES'						=> 'Bestanden',
	'STAGE_FILES_EXPLAIN'				=> 'Alle phpBB Garage bestanden zijn nu verwijdert.',
	'STAGE_SETTINGS'					=> 'Instellingen',
	'STARTING_CONVERT'					=> 'Start conversie proces',
	'STEP_PERCENT_COMPLETED'			=> 'Stap <strong>%d</strong> van <strong>%d</strong>',
	'SUB_INTRO'							=> 'Introductie',
	'SUB_LICENSE'						=> 'Licentie',
	'SUB_SUPPORT'						=> 'Ondersteuning',
	'SUCCESSFUL_CONNECT'				=> 'Successvolle verbinding',

// TODO: Write some text on obtaining support
	'SUPPORT_BODY'						=> 'Tijdens de beta fase zal er minimale ondersteuning te vinden zijn op <a href="http://forums.phpbbgarage.com/">het phpBB Garage support forum</a>. We zullen antwoorden geven op vragen over de algemene setup, configuratie problemen, conversie problemen en ondersteuning voor veel voorkomende problemen die meestal gerelateerd zijn aan bugs.',

	'WELCOME_INSTALL'					=> 'Welkom bij de phpBB Garage installatie',
	'WRITABLE'							=> 'Beschrijfbaar',
));

// Updater
$lang = array_merge($lang, array(
	'ALL_FILES_UP_TO_DATE'				=> 'All files are up to date with the latest phpBB Garage version. You should now check if everything is working fine.',

	'CHECK_FILES_UP_TO_DATE'			=> 'According to your database your version is up to date. You may want to proceed with the file check to make sure all files are really up to date with the latest phpBB Garage version.',
	'COLLECTED_INFORMATION_EXPLAIN'		=> 'The list below shows information about the files needing an update. Please read the information in front of every status block to see what they mean and what you may need to do to perform a successful update.',
	'COMPLETE_LOGIN_TO_BOARD'			=> 'You should now <a href="../ucp.php?mode=login">login to your board</a> and check if everything is working fine. Do not forget to delete, rename or move your install directory!',

	'FILE_USED'							=> 'Information used from',			// Single file
	'FILES_CONFLICT'					=> 'Conflict files',
	'FILES_CONFLICT_EXPLAIN'			=> 'The following files are modified and do not represent the original files from the old version. phpBB determined that these files create conflicts if they are tried to be merged. Please investigate the conflicts and try to manually resolve them or continue the update choosing the preferred merging method. If you resolve the conflicts manually check the files again after you modified them. You are also able to choose between the preferred merge method for every file. The first one will result in a file where the conflicting lines from your old file will be lost, the other one will result in loosing the changes from the newer file.',
	'FILES_MODIFIED'					=> 'Modified files',
	'FILES_MODIFIED_EXPLAIN'			=> 'The following files are modified and do not represent the original files from the old version. The updated file will be a merge between your modifications and the new file.',
	'FILES_NEW'							=> 'New files',
	'FILES_NEW_EXPLAIN'					=> 'The following files currently do not exist within your installation.',
	'FILES_NEW_CONFLICT'				=> 'New conflicting files',
	'FILES_NEW_CONFLICT_EXPLAIN'		=> 'The following files are new within the latest version but it has been determined that there is already a file with the same name within the same position. This file will be overwritten by the new file.',
	'FILES_NOT_MODIFIED'				=> 'Not modified files',
	'FILES_NOT_MODIFIED_EXPLAIN'		=> 'The following files were not modified and represent the original phpBB files from the version you want to update from.',
	'FILES_UP_TO_DATE'					=> 'Already updated files',
	'FILES_UP_TO_DATE_EXPLAIN'			=> 'The following files are already up to date and do not need to be updated.',
	'FTP_SETTINGS'						=> 'FTP settings',
	'FTP_UPDATE_METHOD'					=> 'FTP upload',
	'INCOMPATIBLE_UPDATE_FILES'			=> 'The update files found are incompatible with your installed version. Your installed version is %1$s and the update file is for updating phpBB Garage %2$s to %3$s.',
	'INCOMPATIBLE_REMOVE_FILES'			=> 'The remove files found are incompatible with your installed version. Your installed version is %1$s and the remove file is for phpBB Garage %2$s.',
	'INCOMPLETE_UPDATE_FILES'			=> 'The update files are incomplete.',
	'INLINE_UPDATE_SUCCESSFUL'			=> 'The database update was successful. Now you need to continue the update process.',

	'KEEP_OLD_NAME'						=> 'Keep username',

	'NO_UPDATE_FILES_EXPLAIN'			=> 'The following files are new or modified but the directory they normally reside in could not be found on your installation. If this list contains files to other directories than language/ or styles/ than you may have modified your directory structure and the update may be incomplete.',
	'NO_UPDATE_FILES_OUTDATED'			=> 'No valid update directory was found, please make sure you uploaded the relevant files.<br /><br />Your installation does <strong>not</strong> seem to be up to date. Updates are available for your version of phpBB Garage %1$s, please visit <a href="http://www.phpbbgarage.com/downloads/" rel="external">http://www.phpbbgarage.com/downloads/</a> to obtain the correct package to update from Version %2$s to Version %3$s.',
	'NO_UPDATE_FILES_UP_TO_DATE'		=> 'Your version is up to date. There is no need to run the update tool. If you want to make an integrity check on your files make sure you uploaded the correct update files.',
	'NO_UPDATE_INFO'					=> 'Update file information could not be found.',
	'NO_UPDATES_REQUIRED'				=> 'No updates required',
	'NO_VISIBLE_CHANGES'				=> 'No visible changes',
	'NOTICE'							=> 'Notice',
	'NUM_CONFLICTS'						=> 'Number of conflicts',

	'OLD_UPDATE_FILES'					=> 'Update files are out of date. The update files found are for updating from phpBB %1$s to phpBB %2$s but the latest version of phpBB is %3$s.',

	'PACKAGE_UPDATES_TO'				=> 'Current package updates to version',
	'PERFORM_DATABASE_UPDATE'			=> 'Perform database update',
	'PERFORM_DATABASE_UPDATE_EXPLAIN'	=> 'Below you will find a button to the database update script. The database update can take a while, so please do not stop the execution if it seems to hang. After the database update has been performed just follow the instructions to continue the update process.',
	'PREVIOUS_VERSION'					=> 'Previous version',
	'PROGRESS'							=> 'Progress',

	'RESULT'							=> 'Result',
	'RUN_DATABASE_SCRIPT'				=> 'Update my database now',

	'SELECT_DIFF_MODE'					=> 'Select diff mode',
	'SELECT_DOWNLOAD_FORMAT'			=> 'Select download archive format',
	'SELECT_FTP_SETTINGS'				=> 'Select FTP settings',
	'SHOW_DIFF_CONFLICT'				=> 'Show differences/conflicts',
	'SHOW_DIFF_FINAL'					=> 'Show resulting file',
	'SHOW_DIFF_MODIFIED'				=> 'Show merged differences',
	'SHOW_DIFF_NEW'						=> 'Show file contents',
	'SHOW_DIFF_NEW_CONFLICT'			=> 'Show differences',
	'SHOW_DIFF_NOT_MODIFIED'			=> 'Show differences',
	'SOME_QUERIES_FAILED'				=> 'Some queries failed, the statements and errors are listing below.',
	'SQL'								=> 'SQL',
	'SQL_FAILURE_EXPLAIN'				=> 'This is probably nothing to worry about, update will continue. Should this fail to complete you may need to seek help at our support forums. See <a href="../docs/README.html">README</a> for details on how to obtain advice.',
	'STAGE_FILE_CHECK'					=> 'Bestanden controleren',
	'STAGE_UPDATE_DB'					=> 'Database bijwerken',
	'STAGE_UPDATE_FILES'				=> 'Bestanden bijwerken',
	'STAGE_VERSION_CHECK'				=> 'Versie controle',
	'STATUS_CONFLICT'					=> 'Modified file producing conflicts',
	'STATUS_MODIFIED'					=> 'Modified file',
	'STATUS_NEW'						=> 'Nieuw bestand',
	'STATUS_NEW_CONFLICT'				=> 'Conflicting new file',
	'STATUS_NOT_MODIFIED'				=> 'Not modified file',
	'STATUS_UP_TO_DATE'					=> 'Already updated file',

	'UPDATE_COMPLETED'					=> 'Update completed',
	'UPDATE_DATABASE'					=> 'Update database',
	'UPDATE_DATABASE_EXPLAIN'			=> 'Within the next step the database will be updated.',
	'UPDATE_DATABASE_SCHEMA'			=> 'Updating database schema',
	'UPDATE_FILES'						=> 'Update files',
	'UPDATE_FILES_NOTICE'				=> 'Please make sure you have updated your board files too, this file is only updating your database.',
	'UPDATE_INSTALLATION'				=> 'Update phpBB Garage installation',
	'UPDATE_INSTALLATION_EXPLAIN'		=> 'With this option, it is possible to update your phpBB Garage installation to the latest version.<br />During the process all of your files will be checked for their integrity. You are able to review all differences and files before the update.<br /><br />The file update itself can be done in two different ways.</p><h2>Manual Update</h2><p>With this update you only download your personal set of changed files to make sure you do not lose your file modifications you may have done. After you downloaded this package you need to manually upload the files to their correct position under your phpBB Garage root directory. Once done, you are able to do the file check stage again to see if you moved the files to their correct location.</p><h2>Automatic Update with FTP</h2><p>This method is similar to the first one but without the need to download the changed files and uploading them on your own. This will be done for you. In order to use this method you need to know your FTP login details since you will be asked for them. Once finished you will be redirected to the file check again to make sure everything got updated correctly.<br /><br />',
	'UPDATE_INSTRUCTIONS'				=> '

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

		<h1>Incomplete update gedetecteerd.</h1>

		<p>phpBB Garage detecteert een incomplete automatische update. Wees er zeker van dat je alle stappen van de automatische update tool hebt gevolgd. Hieronder staat de link opnieuw, of ga direct naar de installatie map.</p>
		',
	'VERSION_CHECK'				=> 'Versie controle',
	'VERSION_CHECK_EXPLAIN'		=> 'Controleert of de huidige phpBB Garage installatie de meest recente versie is.',
	'VERSION_NOT_UP_TO_DATE'	=> 'De huidige versie van phpBB Garage is niet up to date. Ga verder met het update proces om de huidige versie bij te werken.',
	'VERSION_NOT_UP_TO_DATE_ACP'=> 'De huidige versie van de phpBB Garage is niet up to date.<br />Klik op de onderstaande link naar de vrijgegeven aankondigingen over de laatste versie en de instructies om de update uit te voeren.',
	'VERSION_UP_TO_DATE'		=> 'De huidige installatie is up to date, er zijn geen updates beschikbaar voor deze versie van de phpBB Garage. Je kunt evengoed verdergaan met de geldigheidscontrole van de bestanden.',
	'VERSION_UP_TO_DATE_ACP'	=> 'De huidige installatie is up to date, er zijn geen updates beschikbaar voor deze versie van de phpBB Garage. De installatie hoeft niet te worden bijgewerkt.',

	'INSERT_OPTIONS'			=> 'Optionele data',
	'INSERT_MAKES'				=> 'Merken toevoegen',
	'INSERT_MAKES_EXPLAIN'		=> 'Voegt een aantal standaard merken en modellen toe.',
	'INSERT_CATEGORIES'			=> 'Categorie&eumln toevoegen',
	'INSERT_CATEGORIES_EXPLAIN'	=> 'Voegt een aantal standaard modificatie categorie&eumln toe.',
	'CURRENT_VERSION'			=> 'Huidige versie',
	'LATEST_VERSION'			=> 'Laatste versie',


));

?>
