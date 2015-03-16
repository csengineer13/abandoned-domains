<?php
/* About
 *
 * This file is Cron'd to run every hour
 * It iterates through the e-mails in our inbox, looking for new Abandoned Domains
 *
 * It adds new domains to our DB if:
 * - Requesting e-mail is Admin_Contact in WHOIS for website
 * - abandoned-domain.txt exists in the root directory of the website and is formatted? (may remove this requirement)
 *
 * Some checks exist to reduce/block spammy bastards
 * Please note: limited to system wide 500 WHOIS requests a month
 */


/* Sources
 *
 * 1 - http://davidwalsh.name/gmail-php-imap
 * 2 - http://php.net/manual/en/function.imap-mail-move.php
 * 3 - http://unirest.io/php.html
 */

require_once __DIR__ . "/credentials.php";
require_once __DIR__ . "/resources/Unirest.php";	/* How we make REST Calls */
require_once __DIR__ . "/classes.php";				/* Models/objects */
require_once __DIR__ . "/functions.php";			/* Helps keep this file readable/modular */


/* try to connect */
$inbox = imap_open($hostname,$username,$password) or die('Cannot connect to inbox: ' . imap_last_error());

/* grab emails */
$emails = imap_search($inbox,'ALL');

/* if emails are returned, cycle through each... */
if($emails) {

	/* init our list(s) */
	$requestList = [];
	
	/* put the newest emails on top */
	rsort($emails);
	
	/* for every email... */
	foreach($emails as $email_number) {

		/* New instance of our request */
		$request = new AbandonedDomainRequest();
		
		/* get information specific to this email */
		$overview = imap_fetch_overview($inbox,$email_number,0);
		$message = imap_fetchbody($inbox,$email_number,2);
		/* Used to bypass "known" email addresses returning user's name */
		$header = imap_headerinfo($inbox, $email_number);
		$fromaddr = $header->from[0]->mailbox . "@" . $header->from[0]->host;
		
		/* output the email header information */
		$request->IsRead 	= $overview[0]->seen;
		$request->From 		= $fromaddr;//= $overview[0]->from;
		$request->Date   	= $overview[0]->date;
		$request->Subject 	= $overview[0]->subject;
		$request->Message 	= $message;

		/* Add to list of abandoned domain requests */
		$requestList[] = $request;

		/* Move to our "processed" folder */
		//imap_mail_move($inbox,$email_number,"INBOX.old-messages");
	}

	/* Flag inc. e-mails that...
	 * Have a blocked user
	 * Have too many failed attempts today
	 * Have too many failed attempts this month
	 * && UPDATE isBannable FLAG 
	 */

	/* Flag inc. emails if...
	 * valid, not valid, and/or already active in system
	 *
	 * Criteria: 
	 * - Check for abandoned-domains.txt
	 * - Check WHOIS for admin_contact email match
	 */

	$domain = "lug.io";
	//if( isAdminContact($ApiKey, $domain, $From) ){
	//
	//}

	/* Ban users that have isBannable flag set */

	/* Add valid websites to DB */

	/* Log all attempts based on flags... */

	/* Dispatch e-mails based on flags... */


	var_dump($requestList);

} 

/* delete all messages marked for deletion */
//imap_expunge($inbox);

/* close the connection */
imap_close($inbox);

?>