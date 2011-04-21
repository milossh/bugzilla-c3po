<?php

include_once "controller.inc.php";

// Populate $locales var with a set of locales
// we want to file bugs for
$locales = clean_explode($_POST['locales'], ',');

// Summary, or title of the bug;
// An locale tag in form of "[ab-CD]" will preceed it
$bugsummary = $_POST['summary'];


// Text area content that's sent using POST methos
// is stored in this war. Later on we'll run it
// against a regex which enables us to use 
// variables when filing bugs
$bugdesc = <<<DESCRIPTION
{$_POST['description']}
DESCRIPTION;

// Login info that we'll get via POST
$xml_data_login = array(
    'login' => $_POST['username'],
    'password' => $_POST['pwd'],
    'remember' => 1,
);

// Data we use for bug creation
// All data is provided on front page
$xml_data_create = array (
    'product' => $_POST['product'],
    'component' => $_POST['component'],
    'version' => $_POST['version'],
    'op_sys' => 'All', // Operating system
    'rep_platform' => 'All', // Platform
    'status_whiteboard' => $_POST['whiteboard'],
    'blocked' => $_POST['blocked'],
    'assigned_to' => $_POST['assign_to'],
);

// Set the target for our requests
$curl_target = $bugzilla_url . 'xmlrpc.cgi';

// Create a cookie
$cookie = tempnam('', 'bugzilla-filer');

// Set cURL options
$curlopts = array(
    CURLOPT_URL     => $curl_target,
    CURLOPT_POST    => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER  => array( 'Content-Type: text/xml', 'charset=utf-8' )
);

// Initialize cURL
$curl_start = curl_init();
curl_setopt_array($curl_start, $curlopts);

// Create a request based on data we got from index.php
$request = xmlrpc_encode_request("User.login", $xml_data_login);
curl_setopt($curl_start, CURLOPT_POSTFIELDS, $request);
curl_setopt($curl_start, CURLOPT_COOKIEJAR, $cookie); // Get the cookie from Bugzilla

$response = curl_exec($curl_start); // execute
$response = xmlrpc_decode($response); // Decoded response is logged-in user ID


// Check if response is all ok, and proceed. If not, throw an error
if (empty($response['id'])) {
    trigger_error("xmlrpc: $response[faultString] ($response[faultCode])");
    die('failed to log in. login details below:<br>'. print_r($xml_data_login));
}

// If no errors were thrown at user, that means we're connected, cookie is saved
// which means we're logged in and session has started

// Now we loop through all locales from a $locales var and file a bug for each one
foreach($locales as $key => $shortcode)
{
    // This set of vars needs to be in the loop as it depends on locale code
    $xml_data_create['bug_file_loc'] = preg_replace('{{{{locale}}}}', $shortcode, $_POST['url']);
    $xml_data_create['summary'] = preg_replace('{{{{locale}}}}', $shortcode, $_POST['summary']);
    $xml_data_create['description'] = preg_replace('{{{{locale}}}}', $shortcode, $_POST['description']);
    // Make the request to file a bug
    $request = xmlrpc_encode_request("Bug.create", $xml_data_create); // create a request for filing bugs
    curl_setopt($curl_start, CURLOPT_POSTFIELDS, $request);
    curl_setopt($curl_start, CURLOPT_COOKIEFILE, $cookie);
    $buglist_array_item = xmlrpc_decode(curl_exec($curl_start)); // Get the ID of the filed bug
    $buglist[] = $buglist_array_item['id']; // Add the ID of the filed bug to array
}

curl_close($curl_start);

foreach($buglist as $item) {
    echo '<br><a href="'. $bugzilla_url . 'show_bug.cgi?id=' .$item . '">Bug ID=' . $item . '</a>';
}

unlink($cookie);
?>