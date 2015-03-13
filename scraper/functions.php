<?php

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

?>