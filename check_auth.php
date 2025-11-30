<?php
require_once("config.php");

$google_oauth_client_id = GOOGLE_CLIENT_ID;
$google_oauth_client_secret = GOOGLE_CLIENT_SECRET;
$google_oauth_redirect_uri = GOOGLE_REDIRECT_URI;
$google_oauth_version = 'v3';


if (isset($_GET['code']) && !empty($_GET['code'])) {
    require_once("db.php");
    require_once("functions.php");
    // Execute cURL request to retrieve the access token
    $params = [
        'code' => $_GET['code'],
        'client_id' => $google_oauth_client_id,
        'client_secret' => $google_oauth_client_secret,
        'redirect_uri' => $google_oauth_redirect_uri,
        'grant_type' => 'authorization_code'
    ];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://accounts.google.com/o/oauth2/token');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    $response = json_decode($response, true);
    // Make sure access token is valid
    if (isset($response['access_token']) && !empty($response['access_token'])) {
        // Execute cURL request to retrieve the user info associated with the Google account
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/oauth2/' . $google_oauth_version . '/userinfo');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $response['access_token']]);
        $response = curl_exec($ch);
        curl_close($ch);
        $profile = json_decode($response, true);
        // Make sure the profile data exists
        if (isset($profile['email'])) {
            
            $first_name = !empty($profile['given_name']) ? $profile['given_name'] : '';
            $last_name = !empty($profile['family_name']) ? $profile['family_name'] : '';
            $email = !empty($profile['email']) ? $profile['email'] : '';
            $picture = !empty($profile['picture']) ? $profile['picture'] : '';
            
            
            // Check if user exists
            $query = "SELECT * FROM users WHERE email = ?";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
            // If user exists, update information; otherwise, insert new user
            if ($user) {
                $query = "UPDATE users SET first_name = ?, last_name = ?, picture = ? WHERE email = ?";
                $stmt = $pdo->prepare($query);
                $stmt->execute([$first_name, $last_name, $picture, $email]);
            } else {
                $query = "INSERT INTO users (first_name, last_name, email, picture, role) VALUES (?, ?, ?, ?, 'user')";
                $stmt = $pdo->prepare($query);
                $stmt->execute([$first_name, $last_name, $email, $picture]);
                
                // $userId = $pdo->lastInsertId();
                
                // $pdo->query("INSERT INTO user_apis (user_id,server_id,api,enable) VALUES ($userId,1,'',1)");
                // $pdo->query("INSERT INTO user_apis (user_id,server_id,api,enable) VALUES ($userId,14,'',1)");
            }
            
            $encrypted_email = AES('encrypt', $email);
            setcookie('ddeml', $encrypted_email, time() + (86400 * 30), "/");

            header('Location: /dashboard');
            exit;
        } else {
            exit('Could not retrieve profile information! Please try again later!');
        }
    } else {
        exit('Invalid access token! Please try again later!');
    }
} else {
    if(!isset($_COOKIE['ddeml'])) {
        
        $params = [
            'response_type' => 'code',
            'client_id' => $google_oauth_client_id,
            'redirect_uri' => $google_oauth_redirect_uri,
            'scope' => 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile',
            'access_type' => 'offline',
            'prompt' => 'consent'
        ];
        header('Location: https://accounts.google.com/o/oauth2/auth?' . http_build_query($params));
        exit;
    }

    header('Location: /dashboard');
    exit;
}
?>