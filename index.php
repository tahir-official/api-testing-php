<!DOCTYPE html>
<html>
<head>
    <title>Testimonial</title>
</head>
<body>
<?php
error_reporting(E_ALL);
include 'encryptdecrypt.php';
include 'header.php';
/**
 * ============================
 * SETTINGS
 * ============================
 */


$langcode = $_GET['langcode'] ?? 'en';
$category = $_GET['category'] ?? '';



/**
 * ============================
 * 1. FETCH CATEGORIES (Encrypted)
 * ============================
 */
$catApiUrl = API_URL."/api/testimonial-categories";

$catPayload = [
    "langcode" => $langcode
];

$main_error = '';
$encryptedCatPayload = encrypt($catPayload);
if($encryptedCatPayload['status']!==200){
    $main_error = $encryptedCatPayload['body'];
}

$catRequestJson = json_encode([
    "data" => $encryptedCatPayload['body'],
    "userName" => "saral33"
]);

$ch = curl_init($catApiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $catRequestJson);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Accept: application/json",
    "Content-Type: application/json"
]);

$catResponse = curl_exec($ch);
curl_close($ch);

$categoriesList = [];

if ($catResponse) {
    $res = json_decode($catResponse, true);

    if (!empty($res['data'])) {
        $categoriesListDecrypt = decrypt($res['data']);
        if($categoriesListDecrypt['status']!==200){
            $categoriesList = $categoriesListDecrypt['body'];
        }else{
            $categoriesList = $categoriesListDecrypt['body'];
        }

    }
}



/**
 * ============================
 * 2. FETCH TESTIMONIALS (Encrypted)
 * ============================
 */
$apiUrl = API_URL."/api/testimonials";

$payload = [
    "langcode" => $langcode
];

if (!empty($category)) {
    $payload["category"] = $category;
}

$payload["show_on_home"] = 'no';
$payload["offset"] = 0;
$payload["limit"] = 6;

//$payload["offset"] = 6;
//$payload["limit"] = 3;

$encryptedPayload = encrypt($payload);
if($encryptedPayload['status']!==200){
    $main_error .= $encryptedPayload['body'];
}

$testRequestJson = json_encode([
    "data" => $encryptedPayload['body'],
    "userName" => "saral33"
]);

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $testRequestJson);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Accept: application/json",
    "Content-Type: application/json"
]);

$response = curl_exec($ch);
curl_close($ch);

$decryptedData = [];

if ($response) {
    $json = json_decode($response, true);

    if (!empty($json['data'])) {
        $decryptedDataDecrypt = decrypt($json['data']);
        if($decryptedDataDecrypt['status']!==200){
            $decryptedData = $decryptedDataDecrypt['body'];
        }else{
            $decryptedData = $decryptedDataDecrypt['body'];
        }
    }
}

?>

<!-- ============================
     FILTER FORM
     ============================ -->
<h2>Testimonial</h2> 
<div>
<form method="get" style="margin-bottom:20px;">
    <label for="lang">Language:</label>
    <select name="langcode" id="lang">
        <option value="en" <?= ($langcode === 'en') ? 'selected' : '' ?>>English</option>
        <option value="hi" <?= ($langcode === 'hi') ? 'selected' : '' ?>>Hindi</option>
        <option value="mr" <?= ($langcode === 'mr') ? 'selected' : '' ?>>Marathi</option>
    </select>

    <label for="cat">Category:</label>
    <select name="category" id="cat">
        <option value="">-- All Categories --</option>
        <?php foreach ($categoriesList as $cat): ?>
            <option value="<?= $cat['id'] ?>" <?= ($category == $cat['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['title']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Filter</button>
</form>
</div>
<div>
<?php
/**
 * ============================
 * DISPLAY TESTIMONIALS
 * ============================
 */
if (!empty($decryptedData)) {
    foreach ($decryptedData as $testimonial) {
        echo "<h3>" . htmlspecialchars($testimonial['name']) . "</h3>";
        echo "<p><strong>Role:</strong> " . htmlspecialchars($testimonial['designation']) . "</p>";
        echo "<p><strong>Department:</strong> " . htmlspecialchars($testimonial['department']) . "</p>";
        echo "<p><strong>Company:</strong> " . htmlspecialchars($testimonial['company']) . "</p>";
        echo "<p>" . nl2br(htmlspecialchars($testimonial['testimonial_description'])) . "</p>";
        
        echo "<p><strong>Gender:</strong> " . htmlspecialchars($testimonial['gender']) . "</p>";
        echo "<img src='" . htmlspecialchars($testimonial['profile_image']) . "' alt='" . htmlspecialchars($testimonial['profile_image_alt']) . "' width='100'>";
        echo "<p><strong>Thumbnail Type:</strong> " . htmlspecialchars($testimonial['thumbnail_type']) . "</p>";
        echo "<img src='" . htmlspecialchars($testimonial['thumbnail']) . "'  width='100'><hr>";
    }
} else {
    echo $main_error;
}
?>
</div>
</body>
</html>
