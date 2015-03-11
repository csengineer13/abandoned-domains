<?php
/* Sources
 *
 * 1 - http://davidwalsh.name/gmail-php-imap
 * 2 - http://php.net/manual/en/function.imap-mail-move.php
 *
 */

require_once __DIR__ . "/resources/Unirest.php";
require_once __DIR__ . "credentials.config";


/* try to connect */
$inbox = imap_open($hostname,$username,$password) or die('Cannot connect to Gmail: ' . imap_last_error());

/* grab emails */
$emails = imap_search($inbox,'ALL');

/* if emails are returned, cycle through each... */
if($emails) {

	
	/* begin output var */
	$output = '';
	
	/* put the newest emails on top */
	rsort($emails);
	
	/* for every email... */
	foreach($emails as $email_number) {
		
		/* get information specific to this email */
		$overview = imap_fetch_overview($inbox,$email_number,0);
		$message = imap_fetchbody($inbox,$email_number,2);
		
		/* output the email header information */
		$output.= '<div class="toggler '.($overview[0]->seen ? 'read' : 'unread').'">';
		$output.= '<span class="subject">'.$overview[0]->subject.'</span> ';
		$output.= '<span class="from">'.$overview[0]->from.'</span>';
		$output.= '<span class="date">on '.$overview[0]->date.'</span>';
		$output.= '</div>';
		
		/* output the email body */
		$output.= '<div class="body">'.$message.'</div>';

		// WHOIS Request
		$response = Unirest\Request::get("http://jsonwhois.com/api/v1/whois", 

		   array(
		    "Accept" => "application/json",
		    "Authorization" => "Token token=" . $ApiKey
		   ),

		   array(
		       "domain" => "google.com"
		   )

		);

		$data = $response->body; // Parsed body

		print_r($data);

		// Move to our "processed" folder
		//imap_mail_move($inbox,$email_number,"INBOX.old-messages");

	}
	
	echo $output;
} 

/* delete all messages marked for deletion */
//imap_expunge($inbox);

/* close the connection */
imap_close($inbox);

?>