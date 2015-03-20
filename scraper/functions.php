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


function updateValidEmailFlags($requests) {
	// todo: implement this...
	return $requests;
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
		while($row = $STH->fetch(PDO::FETCH_ASSOC)) 
		{
			# Compare EA request agains EA result
			foreach($requests as $key => $request){
				// If we have an e-mail match...
				if( $request->From == $row["Email"] ){
					// Check properties and set flags
					if( $row["FailedToday"] > $todayLimit ){	
						$request->F_IsTodayBlocked = True;
					}
					if( $row["FailedMonth"] > $monthLimit ){ 	
						$request->F_IsMonthBlocked = True; 
					}
					if( $row["FailedAllTime"] > ($monthLimit * 2) ){ 	
						$request->F_IsBannable = True; 
					}
					if( $row["IsBanned"] > $todayLimit ){		
						$request->F_IsBanned = True;
					}
				}
			}
		}
	}else{
		# No results :(
		// todo: we don't need to set any flags? May need to add new users (wait till log?)
	}

	return $requests;
}


/* Simple function for testing if string is JSON
 * Returns TRUE or FALSE
 */
function isJson($string) {
	json_decode($string);
 	return (json_last_error() == JSON_ERROR_NONE);
}


/* updateJSONFlags
 *
 * Scrapes /abd-domain.txt of each domain in batch of requests
 * If we find JSON, we try to update the properties for each request
 * No JSON? Set a failed flag
 * Bad JSON? Set a failed flag
 *
 * Returns updated batch of requests
 */
function updateJSONFlags($requests){

	foreach($requests as $key => $request)
	{	
		if(	$request->F_IsBanned || $request->F_IsTodayBlocked ||
		 	$request->F_IsMonthBlocked || $request->F_IsBannable){
			continue;
		}

		$URL = "http://" . $request->Domain . "/abd-domains.txt";

        $CH = curl_init();
        $headers = array(
    		'Accept: application/json',
    		'Content-type: application/json'
		);
		curl_setopt($CH, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($CH, CURLOPT_HEADER, false);
        curl_setopt($CH, CURLOPT_URL, $URL);
        /* Setting cURL's option to return the webpage data */
        curl_setopt($CH, CURLOPT_RETURNTRANSFER, TRUE);
        /* Grabbing what Curl returned */
        $data = curl_exec($CH); 				
        curl_close($CH);    							

        /* Did we get JSON back? */
        if( isJson($data) )
        {
 			/* Decode */
        	$JSONdata = json_decode($data);

        	/* Update request properties */
        	// todo: add properties to request...
        	// todo: check to make sure required properties are filled out?

        	/* Update JSON flag */    
        	$request->F_HasJSON 		= True;
    		$request->F_HasValidJSON 	= True;
        }
        /* Flag is set to "False" by default */
    }

	return $requests; 
}
function updateWHOISFlags($requests){ 

	foreach($requests as $key => $request)
	{	
		/* Make sure it is worth our time to check */
		if(	$request->F_IsBanned || $request->F_IsTodayBlocked ||
		 	$request->F_IsMonthBlocked || $request->F_IsBannable ||
		 	!$request->F_HasJSON || !$request->F_HasValidJSON){
			continue;
		}

	}

	return $requests; 
}

?>