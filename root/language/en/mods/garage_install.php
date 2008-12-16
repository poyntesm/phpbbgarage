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

/**
* Language keys for phpBB Garage auto installer
*/
$lang = array_merge($lang, array(
	'FILES_REQUIRED_EXPLAIN'	=> '<strong>Required</strong> - In order to function correctly phpBB Garage needs to be able to access or write to certain files or directories. If you see âNot Foundâ you need to create the relevant file or directory. If you see âUnwritableâ you need to change the permissions on the file or directory to allow phpBB Garage to write to it.',

	'INSTALL_CONGRATS_EXPLAIN'	=> '
		<p>You have now successfully installed phpBB Garage %1$s. From here, you have two options as to what to do with your newly installed phpBB Garage:</p>
		<h2>Convert an existing garage board to phpBB Garage</h2>
		<p>The phpBB Garage Unified Convertor Framework supports the conversion of phpBB Garage 1.x.x and other garage board systems to phpBB Garage 2. If you have an existing garage board that you wish to convert, please <a href="%2$s">proceed on to the convertor</a>.</p>
		<h2>Go live with your phpBB Garage 2!</h2>
		<p>Clicking the button below will take you to your Administration Control Panel (ACP). Take some time to examine the options available to you. Remember that help is available online via the <a href="http://www.phpbbgarage.com/support/documentation/3.0/">Documentation</a> and the <a href="http://www.phpbbgarage.com/community/viewforum.php?f=4">beta support forums</a>, see the <a href="%3$s">README</a> for further information.</p>',
	'UPDATE_CONGRATS_EXPLAIN'	=> '
		<p>You have now successfully updated to phpBB Garage %1$s.',

	'INSTALL_INTRO_BODY'		=> 'With this option, it is possible to install phpBB Garage onto your server.</p>

	<p><strong>Note:</strong> This installer will help you through all the database related steps &amp; also the editting of core phpBB files. Please be sure you have read the template &amp; language MODX files to complete the installation.</p>

	<p>phpBB Garage supports the following databases:</p>
	<ul>
		<li>MySQL 3.23 or above (MySQLi supported)</li>
		<li>PostgreSQL 7.3+</li>
		<li>SQLite 2.8.2+</li>
		<li>Firebird 2.0+</li>
		<li>MS SQL Server 2000 or above (directly or via ODBC)</li>
		<li>Oracle</li>
	</ul>',

	'PHP_REQUIRED_MODULE'		=> 'Required modules',
	'PHP_REQUIRED_MODULE_EXPLAIN'	=> '<strong>Required</strong> - These modules or applications are required.',

	'OVERVIEW_BODY'			=> 'Welcome to our public beta of the next-generation of phpBB Garage after 1.x.x, phpBB Garage 2.0! This release is intended to help us identify bugs and problematic areas.</p><p>Please read <a href="../docs/INSTALL.html">our installation guide</a> for more information about installing phpBB Garage</p><p><strong style="text-transform: uppercase;">Note:</strong> This release is <strong style="text-transform: uppercase;">still not final</strong>. You may want to wait for the full final release before running it live.</p><p>This installation system will guide you through the process of installing phpBB Garage, converting from a different software package or updating to the latest version of phpBB Garage. For more information on each option, select it from the menu above.',

	'REQUIREMENTS_EXPLAIN'		=> 'Before proceeding with the full installation phpBB Garage will carry out some tests on your server configuration and files to ensure that you are able to install and run phpBB Garage. Please ensure you read through the results thoroughly and do not proceed until all the required tests are passed. If you wish to use any of the features depending on the optional tests, you should ensure that these tests are passed also.',

	'SOFTWARE'			=> 'Garage software',
	'STAGE_OPTIONAL'		=> 'Optional settings',
	'STAGE_OPTIONAL_EXPLAIN'	=> 'The options on this page allow you to have some default data created during the install. The options here are not required for install, however if you do not use the defaults you will need to setup items such as makes, models &amp; categories after the installation.',
	'STAGE_CREATE_TABLE_EXPLAIN'	=> 'The database tables used by phpBB Garage have been created and populated with required data and if selected some optional data. Proceed to the next screen to install new permissions required by phpBB Garage.',
	'STAGE_CREATE_PERMISSIONS'	=> 'Create permissions',
	'STAGE_CREATE_PERMISSIONS_EXPLAIN'	=> 'New permissions required by phpBB Garage have been created and assigned to default roles if they exist. You should after the install confirm you are happy with the permissions.',
	'STAGE_INSTALL_MODULES'		=> 'Install modules',
	'STAGE_INSTALL_MODULES_EXPLAIN'	=> 'The phpBB Garage modules have been installed.',
	'STAGE_DATA'			=> 'Data',
	'STAGE_DATA_EXPLAIN'		=> 'All phpBB Garage data is now removed. Proceeding will removing all files.',
	'STAGE_FILES'			=> 'Files',
	'STAGE_FILES_EXPLAIN'		=> 'All phpBB Garage files are now removed.',
	'SUPPORT_BODY'			=> 'During the beta phase minimal support will be given at <a href="http://forums.phpbbgarage.com/">the phpBB Garage support forums</a>. We will provide answers to general setup questions, configuration problems, conversion problems and support for determining common problems mostly related to bugs.',

	'WELCOME_INSTALL'		=> 'Welcome to phpBB Garage Installation',
	'INSERT_OPTIONS'		=> 'Optional data',
	'INSERT_MAKES'			=> 'Insert makes',
	'INSERT_MAKES_EXPLAIN'		=> 'Inserts a default set of makes and models.',
	'INSERT_CATEGORIES'		=> 'Insert categories',
	'INSERT_CATEGORIES_EXPLAIN'	=> 'Inserts a default set of modification categories.',
	'LOG_GARAGE_INSTALL'		=> 'phpBB Garage %1$s installed.'
));

/**
* Language keys for phpBB Garage converter
*/
$lang = array_merge($lang, array(
	'CONFIG_PHPBB_EMPTY'		=> 'The phpBB Garage config variable for "%s" is empty.',
	'CONVERT_COMPLETE_EXPLAIN'	=> 'You have now successfully converted your garage to phpBB Garage 2.0. You can now login and <a href="../../garage.php">access your garage</a>. Please ensure that the settings were transferred correctly before enabling your board by deleting the install directory. Remember that help on using phpBB Garage is available online via the <a href="http://www.phpbbgarage.com/support/documentation/2.0/">Documentation</a> and the <a href="http://www.phpbbgarage.com/community/viewforum.php?f=4">beta support forums</a>.',
	'CONVERT_INTRO'			=> 'Welcome to the phpBB Garage Unified Convertor Framework',
	'CONVERT_INTRO_BODY'		=> 'From here, you are able to import data from other (installed) garage board systems. The list below shows all the conversion modules currently available. If there is no convertor shown in this list for the board software you wish to convert from, please check our website where further conversion modules may be available for download.',

	'PRE_CONVERT_COMPLETE'		=> 'All pre-conversion steps have successfully been completed. You may now begin the actual conversion process. Please note that you may have to manually adjust several things. After conversion, especially check the permissions assigned, rebuild your search index if necessary and also make sure files got copied correctly, for example avatars and smilies.',
));

/**
* Language keys for phpBB Garage auto updater & converter
*/
$lang = array_merge($lang, array(
	'ALL_FILES_UP_TO_DATE'		=> 'All files are up to date with the latest phpBB Garage version. You should now check if everything is working fine.',

	'CHECK_FILES_UP_TO_DATE'	=> 'According to your database your version is up to date. You may want to proceed with the file check to make sure all files are really up to date with the latest phpBB Garage version.',

	'INCOMPATIBLE_UPDATE_FILES'	=> 'The update files found are incompatible with your installed version. Your installed version is %1$s and the update file is for updating phpBB Garage %2$s to %3$s.',

	'NO_UPDATE_FILES_OUTDATED'	=> 'No valid update directory was found, please make sure you uploaded the relevant files.<br /><br />Your installation does <strong>not</strong> seem to be up to date. Updates are available for your version of phpBB Garage %1$s, please visit <a href="http://www.phpbbgarage.com/downloads/" rel="external">http://www.phpbbgarage.com/downloads/</a> to obtain the correct package to update from Version %2$s to Version %3$s.',

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
	'VERSION_CHECK_EXPLAIN'		=> 'Checks to see if the version of phpBB Garage you are currently running is up to date.',
	'VERSION_NOT_UP_TO_DATE'	=> 'Your version of phpBB Garage is not up to date. Please continue the update process.',
	'VERSION_NOT_UP_TO_DATE_ACP'=> 'Your version of phpBB Garage is not up to date.<br />Below you will find a link to the release announcement for the latest version as well as instructions on how to perform the update.',
	'VERSION_UP_TO_DATE'		=> 'Your installation is up to date, no updates are available for your version of phpBB Garage. You may want to continue anyway to perform a file validity check.',
	'VERSION_UP_TO_DATE_ACP'	=> 'Your installation is up to date, no updates are available for your version of phpBB Garage. You do not need to update your installation.',
));

/**
* Language keys for phpBB Garage auto remover
*/
$lang = array_merge($lang, array(
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
	'REMOVE_COMPLETE'		=> 'phpBB Garage removed!!',
	'REMOVE_COMPLETE_EXPLAIN'	=> 'Some text about removal post checks here',
	'INCOMPATIBLE_REMOVE_FILES'	=> 'The remove files found are incompatible with your installed version. Your installed version is %1$s and the remove file is for phpBB Garage %2$s.',
));

?>
