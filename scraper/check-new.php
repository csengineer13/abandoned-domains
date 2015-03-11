<?php
/* Sources
 *
 * 1 - http://davidwalsh.name/gmail-php-imap
 * 2 - http://php.net/manual/en/function.imap-mail-move.php
 * 3 - http://unirest.io/php.html
 */

require_once __DIR__ . "/resources/Unirest.php";
require_once __DIR__ . "/credentials.php";
require_once __DIR__ . "/functions.php";

/* try to connect */
$inbox = imap_open($hostname,$username,$password) or die('Cannot connect to inbox: ' . imap_last_error());

/* grab emails */
$emails = imap_search($inbox,'ALL');

/* if emails are returned, cycle through each... */
if($emails) {

	/* init our lists */

	
	/* put the newest emails on top */
	rsort($emails);
	
	/* for every email... */
	foreach($emails as $email_number) {
		
		/* get information specific to this email */
		$overview = imap_fetch_overview($inbox,$email_number,0);
		$message = imap_fetchbody($inbox,$email_number,2);
		
		/* output the email header information */
		$IsRead 	= $overview[0]->seen;
		$Subject 	= $overview[0]->subject;
		$From 		= $overview[0]->from;
		$date   	= $overview[0]->date;

		// add to list of abandoned domain requests

		// Move to our "processed" folder
		imap_mail_move($inbox,$email_number,"INBOX.old-messages");
	}

	// better db calls

	// check number of failed attempts by this e-mail
	//// check if email is perm banned

	$domain = "lug.io";
	if( isAdminContact($ApiKey, $domain, $From) ){

	}

} 

/* delete all messages marked for deletion */
imap_expunge($inbox);

/* close the connection */
imap_close($inbox);

?>