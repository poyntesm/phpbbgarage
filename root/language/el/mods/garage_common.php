<?php
/** 
*
* garage [Greek]
*
* @package language
* @version $Id: garage_common.php 587 2008-10-10 chrizathens $
* @copyright (c) 2005 phpBB Garage
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*/

/*
* DO NOT CHANGE 
*/
if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'GARAGE'				=> 'Γκαράζ',
	'QUARTERMILE'			=> 'Χρόνος 0-400μ.',
	'DYNORUN'				=> 'Δυναμομέτρηση',
	'PENDING_GARAGE_ITEMS'	=> 'Αντικείμενα του Γκαράζ σε αναμονή',
	'VIEWING_GARAGE'		=> 'Βλέπει το Γκαράζ',
	'USERS_GARAGE'			=> 'Προβολή του Γκαράζ του μέλους',
	'REMOVE_GARAGE_INSTALL'	=> 'Παρακαλώ διαγράψτε, μετακινήστε ή μετονομάστε τον φάκελο εγκατάστασης του Γκαράζ πριν χρησιμοποιήσετε το Γκαράζ. Αν αυτός ο φάκελος υπάρχει, το Γκαράζ δεν θα είναι διαθέσιμο.',
	'GARAGE_DISABLE'		=> 'Συγγνώμη αλλά το Γκαράζ δεν είναι διαθέσιμο καθώς η διαχείριση εκτελεί κάποιες εργασίες σε αυτό.',
));

?>
