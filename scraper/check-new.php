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

$todayLimit = 3;
$monthLimit = 30;


/* try to connect */
$inbox = imap_open($Email_Host, $Email_User, $Email_Pass) or die('Cannot connect to inbox: ' . imap_last_error());

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

	/* Create a basic connection */
	$DBH = dbConnect($MySQL_Host, $MySQL_DBname, $MySQL_User, $MySQL_Pass);

	/* Check for users/domain
	 * If we don't have these... 
	 * then we can't move forward at all.
	 * This call will remove requests with bad criteria.
	 */
	$requestList = updateValidEmailFlags($requestList);


	/* Flag inc. e-mails that...
	 * Have a blocked user
	 * Have too many failed attempts today
	 * Have too many failed attempts this month
	 * && UPDATE isBannable FLAG 
	 */
	$requestList = updateRequestFlags($DBH, $requestList, $todayLimit, $monthLimit);


	/* JSON Check for non-banned domains
	 *
	 * Criteria: 
	 * - Check for abandoned-domains.txt
	 * - Is it properly formatted?
	 */
	$requestList = updateJSONFlags($requestList);


	/* WHOIS Check for unflagged requests
	 *
	 * Criteria: 
	 * - Check submitting user's email against WHOIS admin_contact
	 */
	$requestList = updateWHOISFlags($requestList);
	//if( isAdminContact($ApiKey, $domain, $From) ){


	/* 1. Add any new users */

	/* 2. Log all attempts based on flags... */

	/* 3. Ban users that have isBannable flag set */

	/* 4. Add valid websites to DB  */

	/* 5. Dispatch e-mails based on flags... */


	//var_dump($requestList);

	# close the connection
	$DBH = null;

} 

/* delete all messages marked for deletion */
//imap_expunge($inbox);

/* close the connection */
imap_close($inbox);

?>