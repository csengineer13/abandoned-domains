<?php

function dbConnect($host, $dbname, $user, $pass){ 
	try{
		# MySQL with PDO_MYSQL
		$DBH = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
		$DBH->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		// $DBH->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
		// $DBH->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT );
	}
	catch(PDOException $e) {
		echo $e->getMessage();
	}

	return $DBH;
}



/* isAdminContact
 * Does a WHOIS lookup on $domain
 * Generates a list of Admin_Contacts for $domain
 * Returns if $email is in list of Admin_Contacts
 */
function isAdminContact($key, $domain, $email){

	// WHOIS Request
	$response = Unirest\Request::get("http://jsonwhois.com/api/v1/whois", 

	   array(
	    "Accept" => "application/json",
	    "Authorization" => "Token token=" . $key
	   ),

	   array(
	       "domain" => $email // lug.io
	   )

	);

	// Our neat response
	$StatusCode 	= $response->code;        // HTTP Status code
	$Headers 		= $response->headers;     // Headers
	$ParsedBody 	= $response->body;        // Parsed body
	$UnparsedBody 	= $response->raw_body;    // Unparsed body

	header('Content-Type: application/json');

	// What we're here for
	$AdminContacts = $ParsedBody->admin_contacts;
	foreach($AdminContacts as $contact) {
		echo json_encode($contact);
	}

	return true;
}

/* updateRequestFlags
 * $emails - a list of AbandonedDomainRequest objects
 * $todayLimit - Limit for how many failed requests they can have today
 * $monthLimit - ...
 *
 * Function takes list of AbandonedDomainRequests
 * It checks sender's email against view that shows outstanding requests for today, month, and all time
 * It also checks if the user is perma-blocked
 * It then updates flags and returns the list
 */
function updateRequestFlags($DBH, $requests, $todayLimit, $monthLimit)
{

	$emailAddresses = [];
	foreach($requests as $key => $request) {
		$emailAddresses[] = $request->From;
	}

	$inQuery = implode(',', array_fill(0, count($emailAddresses), '?'));

	$STH = $DBH->prepare(
	    'SELECT *
	     FROM abd_request_log_summary
	     WHERE Email IN(' . $inQuery . ')'
	);

	$STH->execute($emailAddresses);

	if ($STH->rowCount() > 0) {
		# Have results! (:
		while($row = $STH->fetch(PDO::FETCH_ASSOC)) {
			// Here is where we need to check values and set flags
			print_r($row);
		}
	}else{
		# No results :(
		// todo: we don't need to set any flags? May need to add new users (wait till log?)
		echo "NOTHING!";
	}

	return $requests;
}
?>