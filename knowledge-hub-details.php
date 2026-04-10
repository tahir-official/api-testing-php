<!DOCTYPE html>
<html>
<head>
    <title>Knowledge Hub Details</title>
    <style>
    .container {
        max-width: 1200px;
        margin: auto;
        padding: 20px;
        font-family: Arial, sans-serif;
    }

    .kh-title {
        font-size: 30px;
        font-weight: bold;
        margin-bottom: 15px;
    }

    .kh-short-desc {
        font-size: 16px;
        line-height: 1.7;
        margin-bottom: 30px;
    }

    /* Headings */
    .kh-heading h1 {
        font-size: 26px;
        margin: 40px 0 15px;
    }

    /* Full width blocks */
    .full-width {
        margin: 25px 0;
    }

    .full-width img {
        width: 100%;
        height: auto;
    }

    /* Two column layout */
    .two-col {
        display: flex;
        gap: 25px;
        margin: 30px 0;
        align-items: center;
    }

    .two-col .col {
        flex: 1;
    }

    .two-col img {
        width: 100%;
        height: auto;
    }

    /* Text content */
    .kh-text {
        font-size: 15px;
        line-height: 1.7;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .two-col {
            flex-direction: column;
        }
    }
    </style>

</head>
<body>
<?php
error_reporting(E_ALL);
include 'encryptdecrypt.php';
include 'header.php';
$langcode = $_GET['langcode'] ?? 'en';

$tag = $_GET['tag'] ?? '';



/**
 * ============================
 * 1. FETCH KNOWLEDGE CATEGORIES (Encrypted)
 * ============================
 */
$catApiUrl = API_URL."/api/knowledge-hub-tags";

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
////
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    die("Invalid Knowledge Hub ID");
}

$payload = [
    "id" => (int)$id,
    "langcode" => $langcode
];

$encryptedPayload = encrypt($payload);

$requestJson = json_encode([
    "data" => $encryptedPayload['body'],
    "userName" => "saral33"
]);



$apiUrl = API_URL."/api/knowledge-hub-details";

$ch = curl_init($apiUrl);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $requestJson,
    CURLOPT_HTTPHEADER => [
        "Accept: application/json",
        "Content-Type: application/json"
    ]
]);

$response = curl_exec($ch);
curl_close($ch);

$detail = [];

if ($response) {
    $json = json_decode($response, true);
    if (!empty($json['data'])) {
        $detailDecrypt = decrypt($json['data']);
        $detail = $detailDecrypt['body'];
    }
}

/*echo '<pre>';
print_r($detail);
echo '</pre>';*/
?>
<div class="container">

<!-- ======================
     TITLE
     ====================== -->
<div class="kh-title">
    <?= htmlspecialchars($detail['title']) ?>
</div>

<!-- ======================
     THUMBNAIL
     ====================== -->
<?php if (!empty($detail['thumbnail_image'])): ?>
    <div class="full-width">
        <img src="<?= htmlspecialchars($detail['thumbnail_image']) ?>"
             alt="<?= htmlspecialchars($detail['thumbnail_image_alt']) ?>">
    </div>
<?php endif; ?>

<!-- ======================
     SHORT DESCRIPTION
     ====================== -->
<div class="kh-short-desc">
    <?= nl2br(htmlspecialchars($detail['short_description'])) ?>
</div>

<!-- ======================
     SECTIONS
     ====================== -->
<?php foreach ($detail['sections'] as $section):  ?>


    <!-- TEXT HEADING -->
    <?php if ($section['type'] === 'text_heading'): ?>
        <div class="kh-heading">
            <h1><?= htmlspecialchars($section['heading']) ?></h1>
        </div>

    <!-- FULL WIDTH TEXT -->
    <?php elseif ($section['type'] === 'text'): ?>
        <div class="full-width kh-text">
            <?= $section['content']; ?>
        </div>
   
    <!-- TEXT LEFT | IMAGE RIGHT -->
    <?php elseif ($section['type'] === 'text_image'): ?>
        <div class="two-col">
            <div class="col kh-text">
                <?= $section['text']; ?>
            </div>
            <div class="col">
                <img src="<?= htmlspecialchars($section['image']['url']) ?>"
                     alt="<?= htmlspecialchars($section['image']['alt'] ?? '') ?>">
            </div>
        </div>

    <!-- IMAGE LEFT | TEXT RIGHT -->
    <?php elseif ($section['type'] === 'image_text'): ?>
        <div class="two-col">
            <div class="col">
                <img src="<?= htmlspecialchars($section['image']['url']) ?>"
                     alt="<?= htmlspecialchars($section['image']['alt'] ?? '') ?>">
            </div>
            <div class="col kh-text">
                <?= $section['text']; ?>
            </div>
        </div>


    <!-- SINGLE IMAGE -->
    <?php elseif ($section['type'] === 'image'): ?>
        <div class="full-width">
            <img src="<?= htmlspecialchars($section['image']['url']) ?>"
                 alt="<?= htmlspecialchars($section['image']['alt'] ?? '') ?>">
        </div>

    
    

    <!-- IMAGE + IMAGE -->
    <?php elseif ($section['type'] === 'image_image'): ?>
        <div class="two-col">
            <div class="col">
                <img src="<?= htmlspecialchars($section['image_left']['url']) ?>"
                     alt="<?= htmlspecialchars($section['image_left']['alt'] ?? '') ?>">
            </div>
            <div class="col">
                <img src="<?= htmlspecialchars($section['image_right']['url']) ?>"
                     alt="<?= htmlspecialchars($section['image_right']['alt'] ?? '') ?>">
            </div>
        </div>

    <!-- TEXT LEFT | TEXT Right -->
    <?php elseif ($section['type'] === 'text_text'): ?>
        <div class="two-col">
            <div class="col kh-text">
                <?= $section['text_left']; ?>
            </div>
            <div class="col kh-text">
                <?= $section['text_right']; ?>
            </div>

        </div>    

    <?php endif; ?>

<?php endforeach; ?>
<?php foreach ($categoriesList as $cat): ?>
            <a href="<?php echo BASE_URL;?>/knowledge-hub.php?tag=<?= $cat['id'] ?>" >
                <?= htmlspecialchars($cat['title']) ?>
            </a><br>
        <?php endforeach; ?>
</div>


</body>
</html>
