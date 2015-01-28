<?php

require_once('../inc/common.php');

$token = getAccessToken();
echo "Token: $token";

/**
 * Generates a random alphanumeric string.
 * @param int $len Length of string to generate.
 * @return string
 */
function random($len) {
  if (function_exists('openssl_random_pseudo_bytes')) {
    $byteLen = intval(($len / 2) + 1);
    return substr(bin2hex(openssl_random_pseudo_bytes($byteLen)), 0, $len);
  } elseif (@is_readable('/dev/urandom')) {
    if (($fh = @fopen('/dev/urandom', 'r')) !== false) {
      $urandom = @fread($fh, $len);
      @fclose($fh);
    }
  }

  $ret = '';
  $chars = array_merge(range('a', 'z'), range('A', 'Z'), range('0', '9'));

  for ($i = 0; $i < $len; ++$i) {
    if (isset($urandom)) {
      $ret .= $chars[ord($urandom[$i]) % count($chars)];
    } else {
      $ret .= $chars[mt_rand(0, count($chars) - 1)];
    }
  }
  return $ret;
}

/**
 * Passes the user off to Venmo to authorize the app.
 */
function requestAuthorization() {
  $state = random(32);
  $data = array(
    'client_id' => CLIENT_ID,
    'scope' => 'access_profile',
    'response_type' => 'code',
    'redirect_uri' => REDIRECT_URL,
    'state' => $state
  );

  saveValue('state', $state);

  $url = 'https://api.venmo.com/v1/oauth/authorize?' . http_build_query($data);
  header('Location: ' . $url, true, 307);
  die();
}

/**
 * Gets a new access token, redirecting the user to Venmo if necessary.
 * @throws Exception if no response is received.
 * @return string|void
 */
function getNewAccessToken() {
  // Some protection against CRSF.
  if (!(isset($_GET['code']) && !empty($_GET['code'] &&
        isset($_GET['state']) && $_GET['state'] == getSavedValue('state')))) {
    requestAuthorization();
    return;
  }

  $data = array(
    'client_id' => CLIENT_ID,
    'client_secret' => CLIENT_SECRET,
    'code' => $_GET['code'],
  );
  $url = 'https://api.venmo.com/v1/oauth/access_token';

  $response = postRequest($url, $data);
  if (empty($response))
    throw new Exception('No response');

  $result = json_decode($response);
  if (isset($result->error)) {
    requestAuthorization();
    return;
  }

  return $result->access_token;
}

/**
 * Gets a cached access , or requests and caches a new one.
 * @throws Exception if the token handling fails.
 * @return string
 */
function getAccessToken() {
  if ($token = getSavedValue('token'))
    return $token;

  if (!$token = getNewAccessToken())
    throw new Exception('No token received.');

  saveValue('token', $token);

  return $token;
}

?>
