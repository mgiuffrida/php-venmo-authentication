<?php

require_once('config.php');

/**
 * Sends a request with the given data and returns the response.
 * @param string $url Base URL.
 * @param mixed $data Data to add as query string.
 * @param string $hash Hash to postfix.
 * @param boolean $post True to use POST, false to use GET.
 * @return mixed Response, or false on failure.
 */
function postRequest($url, $data, $hash = '', $post = true) {
  $url .= '?' . http_build_query($data);
  if ($hash)
    $url .= '#' . $hash;

  $ch = curl_init();

  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_POST, $post);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  if (!curl_setopt($ch, CURLOPT_CAINFO, __DIR__ . '/certs.crt') ||
      !curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true) ||
      !curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2)) {
    curl_close($ch);
    die("Unable to set verification options");
  }

  $result = curl_exec($ch);
  curl_close($ch);
  return $result;
}

/**
 * Persists the value to disk.
 * @param string $key Key for value lookup.
 * @param string $value Value to save.
 * @throws Exception if the disk write fails.
 */
function saveValue($key, $value) {
  if (($fh = @fopen(__DIR__ . '/' . $key . '.txt', 'w')) === false)
    throw new Exception('Couldn\'t open ' . $key . ' file for writing.');
  $status = fwrite($fh, $value);
  if (!@fclose($fh) || !$status)
    throw new Exception('Couldn\'t write ' . $key . ' to file.');
}

/**
 * Attempts to retrieve a saved value.
 * @param string $key Key for value lookup.
 * @return string|false
 */
function getSavedValue($key) {
  if (($fh = @fopen(__DIR__ . '/' . $key . '.txt', 'r')) !== false) {
    $value = @fread($fh, 128);
    @fclose($fh);
    return $value;
  }
  return false;
}

?>
