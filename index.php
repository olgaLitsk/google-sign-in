<?php
session_start();
require_once 'vendor/autoload.php';
/*
 * Configuration and setup Google API
 */
$clientId = '155611178753-32vknqtnbqbmjnst41d0aonba9u7hrhp.apps.googleusercontent.com'; //Google client ID
$clientSecret = 'xzvhDZDYhWOEC15Xmfiu6XH5'; //Google client secret
$redirectURL = 'http://localhost:63342/googleSignIn/index.php'; //Callback URL

//Call Google API
$gClient = new Google_Client();
$gClient->setApplicationName('Test app');
$gClient->setClientId($clientId);
$gClient->setClientSecret($clientSecret);
$gClient->setScopes('https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile');
$gClient->setRedirectUri($redirectURL);

if (isset($_GET['code'])) {
    $gClient->authenticate($_GET['code']);
    $_SESSION['token'] = $gClient->getAccessToken();
    header('Location: ' . filter_var($redirectURL, FILTER_SANITIZE_URL));
}

if (isset($_SESSION['token'])) {
    $gClient->setAccessToken($_SESSION['token']);
}

if ($gClient->getAccessToken()) {
    // Specify the CLIENT_ID of the app that accesses the backend
    $client = new Google_Client(['client_id' => $clientId]);
    $client->setAccessToken($_SESSION['token']);
    $payload = $client->verifyIdToken($_SESSION['token']['id_token']);
    if ($payload) {
        // receive user info
        $oauth2 = new Google_Service_Oauth2($client);
        $userInfo = $oauth2->userinfo->get();    //https://www.googleapis.com/oauth2/v1/tokeninfo
        $userid = $payload['sub'];
    } else {
        echo 'Invalid ID token';
    }
} else {
    $authUrl = $gClient->createAuthUrl();
    $output = '<a href="' . filter_var($authUrl, FILTER_SANITIZE_URL) . '">link to sign-in</a>';
}
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Login with Google API</title>
</head>
<body>
<div><?php echo $output; ?></div>
</body>
</html>