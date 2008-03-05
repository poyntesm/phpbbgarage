<?php
/**
*
* install [English]
*
* @package language
* @version $Id: install.php,v 1.119 2007/07/24 15:17:47 acydburn Exp $
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
	'CONFIG_PHPBB_EMPTY'		=> 'The phpBB Garage config variable for "%s" is empty.',
	'CONVERT_COMPLETE_EXPLAIN'	=> 'You have now successfully converted your garage to phpBB Garage 2.0. You can now login and <a href="../../garage.php">access your garage</a>. Please ensure that the settings were transferred correctly before enabling your board by deleting the install directory. Remember that help on using phpBB Garage is available online via the <a href="http://www.phpbbgarage.com/support/documentation/2.0/">Documentation</a> and the <a href="http://www.phpbbgarage.com/community/viewforum.php?f=4">beta support forums</a>.',
	'CONVERT_INTRO'			=> 'Welcome to the phpBB Garage Unified Convertor Framework',
	'CONVERT_INTRO_BODY'		=> 'From here, you are able to import data from other (installed) garage board systems. The list below shows all the conversion modules currently available. If there is no convertor shown in this list for the board software you wish to convert from, please check our website where further conversion modules may be available for download.',

	'FILES_REQUIRED_EXPLAIN'	=> '<strong>Required</strong> - In order to function correctly phpBB Garage needs to be able to access or write to certain files or directories. If you see “Not Found” you need to create the relevant file or directory. If you see “Unwritable” you need to change the permissions on the file or directory to allow phpBB Garage to write to it.',

	'INSTALL_CONGRATS_EXPLAIN'	=> '
		<p>You have now successfully installed phpBB Garage %1$s. From here, you have two options as to what to do with your newly installed phpBB Garage:</p>
		<h2>Convert an existing garage board to phpBB Garage</h2>
		<p>The phpBB Garage Unified Convertor Framework supports the conversion of phpBB Garage 1.x.x and other garage board systems to phpBB Garage 2. If you have an existing garage board that you wish to convert, please <a href="%2$s">proceed on to the convertor</a>.</p>
		<h2>Go live with your phpBB Garage 2!</h2>
		<p>Clicking the button below will take you to your Administration Control Panel (ACP). Take some time to examine the options available to you. Remember that help is available online via the <a href="http://www.phpbbgarage.com/support/documentation/3.0/">Documentation</a> and the <a href="http://www.phpbbgarage.com/community/viewforum.php?f=4">beta support forums</a>, see the <a href="%3$s">README</a> for further information.</p>',
	'UPDATE_CONGRATS_EXPLAIN'	=> '
		<p>You have now successfully updated to phpBB Garage %1$s.',
	'REMOVE_INTRO'			=> 'Welcome to removal',
	'REMOVE_INTRO_BODY'		=> 'With this option, it is possible to remove phpBB Garage from your server.</p>

	<p><strong>Note:</strong> This remover will totally remove all of the phpBB Garage software. Once the data is removed there is no restore option. Only a DB restore and files from before this action can return phpBB Garage to its original state. By clicking next step this process will start. DO NOT PROCEED UNLESS SURE.</p>

	<p>phpBB Garage removes</p>
	<ul>
		<li>All created tables</li>
		<li>All data</li>
		<li>All phpBB Garage files</li>
		<li>All phpBB Garage modules</li>
		<li>All phpBB Garage permissions</li>
		</ul>',

	'INSTALL_INTRO_BODY'		=> 'With this option, it is possible to install phpBB Garage onto your server.</p>

	<p><strong>Note:</strong> This installer will help you through all the database related steps &amp; also the editting of core phpBB files. Please my sure you have read the template &amp; language MODX files to complete the installation.</p>

	<p>phpBB Garage supports the following databases:</p>
	<ul>
		<li>MySQL 3.23 or above (MySQLi supported)</li>
		<li>PostgreSQL 7.3+</li>
		<li>SQLite 2.8.2+</li>
		<li>Firebird 2.0+</li>
		<li>MS SQL Server 2000 or above (directly or via ODBC)</li>
		<li>Oracle</li>
	</ul>',
	'REMOVE_COMPLETE'		=> 'phpBB Garage removed!!',
	'REMOVE_COMPLETE_EXPLAIN'	=> 'Some text about removal post checks here',
	'PHP_REQUIRED_MODULE'		=> 'Required modules',
	'PHP_REQUIRED_MODULE_EXPLAIN'	=> '<strong>Required</strong> - These modules or applications are required.',

	'OVERVIEW_BODY'			=> 'Welcome to our public beta of the next-generation of phpBB Garage after 1.x.x, phpBB Garage 2.0! This release is intended to help us identify bugs and problematic areas.</p><p>Please read <a href="../docs/INSTALL.html">our installation guide</a> for more information about installing phpBB Garage</p><p><strong style="text-transform: uppercase;">Note:</strong> This release is <strong style="text-transform: uppercase;">still not final</strong>. You may want to wait for the full final release before running it live.</p><p>This installation system will guide you through the process of installing phpBB Garage, converting from a different software package or updating to the latest version of phpBB Garage. For more information on each option, select it from the menu above.',

	'PRE_CONVERT_COMPLETE'		=> 'All pre-conversion steps have successfully been completed. You may now begin the actual conversion process. Please note that you may have to manually adjust several things. After conversion, especially check the permissions assigned, rebuild your search index if necessary and also make sure files got copied correctly, for example avatars and smilies.',
	'PROCESS_LAST'			=> 'Processing last statements',

	'REFRESH_PAGE'			=> 'Refresh page to continue conversion',
	'REFRESH_PAGE_EXPLAIN'		=> 'If set to yes, the convertor will refresh the page to continue the conversion after having finished a step. If this is your first conversion for testing purposes and to determine any errors in advance, we suggest to set this to No.',
//	'REQUIRED'					=> 'Required',
	'REQUIREMENTS_TITLE'		=> 'Installation compatibility',
	'REQUIREMENTS_EXPLAIN'		=> 'Before proceeding with the full installation phpBB Garage will carry out some tests on your server configuration and files to ensure that you are able to install and run phpBB Garage. Please ensure you read through the results thoroughly and do not proceed until all the required tests are passed. If you wish to use any of the features depending on the optional tests, you should ensure that these tests are passed also.',
	'RETRY_WRITE'			=> 'Retry writing config',
	'RETRY_WRITE_EXPLAIN'		=> 'If you wish you can change the permissions on config.php to allow phpBB to write to it. Should you wish to do that you can click Retry below to try again. Remember to return the permissions on config.php after phpBB has finished installation.',

	'SCRIPT_PATH'			=> 'Script path',
	'SCRIPT_PATH_EXPLAIN'		=> 'The path where phpBB is located relative to the domain name, e.g. <samp>/phpBB3</samp>.',
	'SELECT_LANG'			=> 'Select language',
	'SERVER_CONFIG'			=> 'Server configuration',
	'SEARCH_INDEX_UNCONVERTED'	=> 'Search index was not converted',
	'SEARCH_INDEX_UNCONVERTED_EXPLAIN'	=> 'Your old search index was not converted. Searching will always yield an empty result. To create a new search index go to the Administration Control Panel, select Maintenance and then choose Search index from the submenu.',
	'SOFTWARE'			=> 'Garage software',
	'SPECIFY_OPTIONS'		=> 'Specify conversion options',
	'STAGE_ADMINISTRATOR'		=> 'Administrator details',
	'STAGE_OPTIONAL'		=> 'Optional settings',
	'STAGE_OPTIONAL_EXPLAIN'	=> 'The options on this page allow you to have some default data created during the install. The options here are not required for install, however if you do not use the defaults you will need to setup items such as makes, models &amp; categories after the installation.',
	'STAGE_CONFIG_FILE'		=> 'Configuration file',
	'STAGE_CREATE_TABLE'		=> 'Create database tables',
	'STAGE_CREATE_TABLE_EXPLAIN'	=> 'The database tables used by phpBB Garage have been created and populated with required data and if selected some optional data. Proceed to the next screen to install new permissions required by phpBB Garage.',
	'STAGE_CREATE_PERMISSIONS'	=> 'Create permissions',
	'STAGE_CREATE_PERMISSIONS_EXPLAIN'	=> 'New permissions required by phpBB Garage have been created and assigned to default roles if they exist. You should after the install confirm you are happy with the permissions.',
	'STAGE_INSTALL_MODULES'		=> 'Install modules',
	'STAGE_INSTALL_MODULES_EXPLAIN'	=> 'The phpBB Garage modules have been installed.',
	'STAGE_DATABASE'		=> 'Database settings',
	'STAGE_FINAL'			=> 'Final stage',
	'STAGE_INTRO'			=> 'Introduction',
	'STAGE_IN_PROGRESS'		=> 'Conversion in progress',
	'STAGE_REQUIREMENTS'		=> 'Requirements',
	'STAGE_DATA'			=> 'Data',
	'STAGE_DATA_EXPLAIN'		=> 'All phpBB Garage data is now removed. Proceeding will removing all files.',
	'STAGE_FILES'			=> 'Files',
	'STAGE_FILES_EXPLAIN'		=> 'All phpBB Garage files are now removed.',
	'STAGE_SETTINGS'		=> 'Settings',
	'STARTING_CONVERT'		=> 'Starting conversion process',
	'STEP_PERCENT_COMPLETED'	=> 'Step <strong>%d</strong> of <strong>%d</strong>',
	'SUB_INTRO'			=> 'Introduction',
	'SUB_LICENSE'			=> 'License',
	'SUB_SUPPORT'			=> 'Support',
	'SUCCESSFUL_CONNECT'		=> 'Successful connection',
// TODO: Write some text on obtaining support
	'SUPPORT_BODY'			=> 'During the beta phase minimal support will be given at <a href="http://forums.phpbbgarage.com/">the phpBB Garage support forums</a>. We will provide answers to general setup questions, configuration problems, conversion problems and support for determining common problems mostly related to bugs.',

	'WELCOME_INSTALL'		=> 'Welcome to phpBB Garage Installation',
	'WRITABLE'			=> 'Writable',
));

// Updater
$lang = array_merge($lang, array(
	'ALL_FILES_UP_TO_DATE'		=> 'All files are up to date with the latest phpBB Garage version. You should now check if everything is working fine.',

	'CHECK_FILES_UP_TO_DATE'	=> 'According to your database your version is up to date. You may want to proceed with the file check to make sure all files are really up to date with the latest phpBB Garage version.',
	'COLLECTED_INFORMATION_EXPLAIN'	=> 'The list below shows information about the files needing an update. Please read the information in front of every status block to see what they mean and what you may need to do to perform a successful update.',
	'COMPLETE_LOGIN_TO_BOARD'	=> 'You should now <a href="../ucp.php?mode=login">login to your board</a> and check if everything is working fine. Do not forget to delete, rename or move your install directory!',

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

		<h1>Incomplete update detected</h1>

		<p>phpBB Garage detected an incomplete automatic update. Please make sure you followed every step within the automatic update tool. Below you will find the link again, or go directly to your install directory.</p>
		',
	'VERSION_CHECK'				=> 'Version check',
	'VERSION_CHECK_EXPLAIN'		=> 'Checks to see if the version of phpBB Garage you are currently running is up to date.',
	'VERSION_NOT_UP_TO_DATE'	=> 'Your version of phpBB Garage is not up to date. Please continue the update process.',
	'VERSION_NOT_UP_TO_DATE_ACP'=> 'Your version of phpBB Garage is not up to date.<br />Below you will find a link to the release announcement for the latest version as well as instructions on how to perform the update.',
	'VERSION_UP_TO_DATE'		=> 'Your installation is up to date, no updates are available for your version of phpBB Garage. You may want to continue anyway to perform a file validity check.',
	'VERSION_UP_TO_DATE_ACP'	=> 'Your installation is up to date, no updates are available for your version of phpBB Garage. You do not need to update your installation.',

	'INSERT_OPTIONS'		=> 'Optional data',
	'INSERT_MAKES'			=> 'Insert makes',
	'INSERT_MAKES_EXPLAIN'		=> 'Inserts a default set of makes and models.',
	'INSERT_CATEGORIES'		=> 'Insert categories',
	'INSERT_CATEGORIES_EXPLAIN'	=> 'Inserts a default set of modification categories.',
	'CURRENT_VERSION'				=> 'Current version',
	'LATEST_VERSION'		=> 'Latest version',

));

?>
