<?php

// We define an array with locales we want to file bugs for
$locales = array(
3 => "ast", 4 => "be", 5 => "bg", 6 => "bn-BD", 7 => "bn-IN", 8 => "ca", 9 => "cs", 10 => "cy", 11 => "da", 12 => "de", 13 => "el", 14 => "en-GB", 15 => "eo", 16 => "es-AR", 17 => "es-CL", 18 => "es-ES", 19 => "es-MX", 20 => "et", 21 => "eu", 22 => "fa", 23 => "fi", 24 => "fr", 25 => "fy-NL", 26 => "ga-IE", 27 => "gd", 28 => "gl", 29 => "gu-IN", 30 => "he", 31 => "hi-IN", 32 => "hr", 33 => "hu", 34 => "hy-AM", 35 => "id", 36 => "is", 37 => "it", 38 => "ka", 39 => "kk", 40 => "kn", 41 => "ko", 42 => "ku", 43 => "lt", 44 => "lv", 45 => "mk", 46 => "ml", 47 => "mn", 48 => "mr", 49 => "nb-NO", 50 => "nl", 51 => "nn-NO", 52 => "oc", 53 => "or", 54 => "pa-IN", 55 => "pl", 56 => "pt-BR", 57 => "pt-PT", 58 => "rm", 59 => "ro", 60 => "ru", 61 => "si", 62 => "sk", 63 => "sl", 64 => "sq", 65 => "sr", 66 => "sv-SE", 67 => "ta", 68 => "ta-LK", 69 => "te", 70 => "th", 71 => "tr", 72 => "uk", 73 => "vi", 74 => "zh-CN", 75 => "zh-TW",
);

// Switch the bugzilla installation URL below
$bugzilla_url = 'https://bugzilla.mozilla.org/';

// Summary, or title of the bug;
// An locale tag in form of "[ab-CD]" will preceed it
$bugsummary = 'New about pages on mozilla.com';


// Data we use for bug creation
$xml_data_create = array (
    'product' => 'Websites', 
    'component' => 'www.mozilla.com',
    'version' => 'unspecified',
    'op_sys' => 'All', // Operating system
    'rep_platform' => 'All', // Platform
    'status_whiteboard' => '[l10n]',
);

$curl_target = $bugzilla_url . 'xmlrpc.cgi';
$xml_data_login = array(
    'login' => 'bugzilla email goes here',
    'password' => 'myl337sp34kp455w0rd',
    'remember' => 1,
);

$cookie = tempnam('', 'bugzilla-filer');
$curlopts = array(
    CURLOPT_URL     => $curl_target,
    CURLOPT_POST    => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER  => array( 'Content-Type: text/xml', 'charset=utf-8' )
);

// Script starts here
$curl_start = curl_init();
curl_setopt_array($curl_start, $curlopts);
$request = xmlrpc_encode_request("User.login", $xml_data_login);
curl_setopt($curl_start, CURLOPT_POSTFIELDS, $request);
curl_setopt($curl_start, CURLOPT_COOKIEJAR, $cookie); // Get the cookie from Bugzilla

$response = curl_exec($curl_start); // Outputs user id
$response = xmlrpc_decode($response);

if (empty($response['id'])) {
    trigger_error("xmlrpc: $response[faultString] ($response[faultCode])");
}

// If no errors were thrown at user, that means we're connected, cookie is saved
// which means we're logged in and session has started


// Now we loop through all locales from a $locales var and file a bug for each one
foreach($locales as $key => $code)
{   
$bugdesc = <<<DESCRIPTION

My text here. I am allowed to use vars like this {$myvar}
Make sure that heredoc closing tag (DESCRIPTION;) is on 
the beggining of the new line

DESCRIPTION;
    // This set of vars needs to be in the loop as it depends on locale code
    $xml_data_create['bug_file_loc'] = "http://www-trunk.stage.mozilla.com/" . $code . "/about/";
    $xml_data_create['summary'] = '[' . $code . '] '. $bugsummary;
    $xml_data_create['description'] = $bugdesc;
    
    // Make the request to file a bug
    $request = xmlrpc_encode_request("Bug.create", $xml_data_create);
    curl_setopt($curl_start, CURLOPT_POSTFIELDS, $request);
    curl_setopt($curl_start, CURLOPT_COOKIEFILE, $file_cookie);
    $buglist_array_item = xmlrpc_decode(curl_exec($curl_start)); // Get the ID of the filed bug
    $buglist[] = $buglist_array_item['id']; // Add the ID of the filed bug to array
}

curl_close($curl_start);

echo "List of bugs to add as dependices to the tracking bug:";

foreach($buglist as $item) {
    echo $item . ', ';
}

foreach($buglist as $item) {
    echo '<br><a href="'. $bugzilla_url . 'show_bug.cgi?id=' .$item . '">Bug ID=' . $item . '</a>';
}

unlink($file_cookie);
?>