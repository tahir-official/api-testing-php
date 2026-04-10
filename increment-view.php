<?php
include 'encryptdecrypt.php';

$id = $_GET['id'] ?? '';

if (empty($id)) {
    exit;
}

$payload = [
    "id" => $id
];

$encryptedPayload = encrypt($payload);

$requestJson = json_encode([
    "data" => $encryptedPayload['body'],
    "userName" => "saral33"
]);

$apiUrl = API_URL."/api/increment-knowledge-hub-views";

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $requestJson);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Accept: application/json",
    "Content-Type: application/json"
]);

$response = curl_exec($ch);
curl_close($ch);
