<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
define('AES_KEY', 'mySuperSecretKey1234567890abcdef');
define('API_URL', 'https://dev-ybcms.pantheonsite.io');
define('BASE_URL', '/api-testing');

/**
   * LOCAL AES-256 Encryption (OLD)
*/
function encrypt($data) {
    $iv = random_bytes(16);
    $ciphertext = openssl_encrypt(json_encode($data), 'AES-256-CBC', AES_KEY, OPENSSL_RAW_DATA, $iv);
    //return base64_encode($iv . $ciphertext);
    return [
        'status' => 200,
        'body' => base64_encode($iv . $ciphertext),
    ];
}

/**
   * LOCAL AES-256 Decryption (OLD)
*/
function decrypt($encryptedData) {
    $raw = base64_decode($encryptedData);
    $iv = substr($raw, 0, 16);
    $cipherText = substr($raw, 16);

    $decryptedJson = openssl_decrypt($cipherText, 'AES-256-CBC', AES_KEY, OPENSSL_RAW_DATA, $iv);

    if ($decryptedJson === false) {
      throw new \Exception('Invalid encryption key or corrupted payload.');
    }
    
    return [
        'status' => 200,
        'body' => json_decode($decryptedJson, true),
    ];

}


/**
 * ============================
 * ENCRYPT USING EXTERNAL API
 * ============================
 */
function encrypt_replace($payload) {
    $url = "https://abc.com/aes/encryptdata";

    $postData = [
        "data" => $payload,
        "userName" => "saral33"
    ];

    $jsonBody = json_encode($postData);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonBody);
    curl_setopt($ch, CURLOPT_FAILONERROR, false);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    // API returns encrypted string directly
    return [
        'status' => $httpCode,
        'body' => trim($response),
    ];
}


/**
 * ============================
 * DECRYPT USING EXTERNAL API
 * ============================
 */
function decrypta_replace($encryptedString) {
    $url = "https://abc.com/aes/decryptdata";

    $postData = [
        "data" => $encryptedString,
        "userName" => "saral33"
    ];

    $jsonBody = json_encode($postData);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonBody);
    curl_setopt($ch, CURLOPT_FAILONERROR, false);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    // API returns encrypted string directly
    return [
        'status' => $httpCode,
        'body' => json_decode($response, true),
    ];
}
?>