<?php

require_once('../inc/common.php');

assert_options(ASSERT_BAIL, 1);

$token = getSavedValue('token');
if ($token === false)
  die('Cannot get token');
else if (empty($token))
  die('Token is empty');

$data = array('access_token' => $token);
$url = 'https://api.venmo.com/v1/payments';

$response = postRequest($url, $data, '', false);
if ($response === false)
  die('Failed to get response');
if (empty($response))
  die('Response empty');

$result = json_decode($response);

if (property_exists($result, 'error'))
  die('Error received: ' . $result->error->message);

echo 'Token works!';

?>
