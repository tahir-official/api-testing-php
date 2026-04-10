<!DOCTYPE html>
<html>
<head>
    <title>Knowledge Hub</title>
    <style>
    .carousel-wrapper {
        position: relative;
        max-width: 100%;
        margin: 20px auto;
        display: flex;
        align-items: center;
    }

    .carousel-container {
        overflow: hidden;
        width: 100%;
    }

    .carousel-track {
        display: flex;
        transition: transform 0.4s ease-in-out;
    }

    .carousel-item {
        min-width: 33.33%;
        box-sizing: border-box;
        padding: 15px;
    }

    .carousel-item img {
        width: 100%;
        height: 180px;
        object-fit: cover;
    }

    .carousel-item h3 {
        font-size: 16px;
        margin: 10px 0;
    }

    .carousel-item p {
        font-size: 14px;
    }

    .carousel-btn {
        background: #333;
        color: #fff;
        border: none;
        padding: 12px;
        cursor: pointer;
        font-size: 18px;
    }

    .carousel-btn:hover {
        background: #000;
    }
    </style>



</head>
<body>
<?php
error_reporting(E_ALL);
include 'encryptdecrypt.php';
include 'header.php';
$langcode = $_GET['langcode'] ?? 'en';
$category = $_GET['category'] ?? '';
$keyword = $_GET['keyword'] ?? '';


/**
 * ============================
 * 1. FETCH KNOWLEDGE CATEGORIES (Encrypted)
 * ============================
 */
$catApiUrl = API_URL."/api/knowledge-hub-categories";

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
 * FETCH KNOWLEDGE HUB (Encrypted)
 * ============================
 */

$knowledgeApiUrl = API_URL."/api/knowledge-hub";

$payload = [
    "langcode" => $langcode
];

if (!empty($category)) {
    $payload["category"] = $category;
}

if (!empty($keyword)) {
    $payload["keyword"] = $keyword;
}

$payload["show_on_home"] = 'no';
$payload["offset"] = 0;
$payload["limit"] = 6;

//$payload["offset"] = 6;
//$payload["limit"] = 3;

$encryptedPayload = encrypt($payload);

$requestJson = json_encode([
    "data" => $encryptedPayload['body'],
    "userName" => "saral33"
]);

$ch = curl_init($knowledgeApiUrl);
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

$knowledgeHubData = [];

if ($response) {
    $json = json_decode($response, true);
    if (!empty($json['data'])) {
        $knowledgeHubDataDecrypt = decrypt($json['data']);
        $knowledgeHubData = $knowledgeHubDataDecrypt['body'];
    }
}

?>
<h2>Knowledge Hub</h2>
<div>
<form method="get" style="margin-bottom:20px;">
    <label for="keyword">Keyword:</label>
    <input type="text" name="keyword" id="keyword"
           value="<?= htmlspecialchars($keyword) ?>"
           placeholder="Search knowledge hub...">

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


<div class="carousel-wrapper">
    <button class="carousel-btn prev">&#10094;</button>

    <div class="carousel-container">
        <div class="carousel-track">
            <?php foreach ($knowledgeHubData as $item): ?>
                <div class="carousel-item">
                    <img src="<?= htmlspecialchars($item['thumbnail_image']) ?>"
                         alt="<?= htmlspecialchars($item['thumbnail_image_alt']) ?>">

                    <h3><?= htmlspecialchars($item['title']) ?></h3>  
                    <p>Published By: <?= htmlspecialchars($item['published_by']) ?> | Published on: <?= htmlspecialchars($item['published_on']) ?></p>
                    <p><?= $item['view_count'] ?></p>
                    <?php if (!empty($item['knowledge_hub_categories'])): ?>

                        <div class="category-wrapper" style="display:flex; gap:10px; flex-wrap:wrap;">
                          
                          <?php foreach ($item['knowledge_hub_categories'] as $cat): ?>
                            
                            <div class="category-item" style="padding:6px 12px; background:#f2f2f2; border-radius:6px;">
                              <?= htmlspecialchars($cat['title']); ?>
                            </div>
                          
                          <?php endforeach; ?>
                          
                        </div>
                    
                    <?php endif; ?>

                    <?php if (!empty($item['knowledge_hub_tags'])): ?>

                        <div class="category-wrapper" style="display:flex; gap:10px; flex-wrap:wrap;">
                          
                          <?php foreach ($item['knowledge_hub_tags'] as $tag): ?>
                            
                            <div class="category-item" style="padding:6px 12px; background:#f2f2f1; border-radius:6px;">
                              <?= htmlspecialchars($tag['title']); ?>
                            </div>
                          
                          <?php endforeach; ?>
                          
                        </div>
                    
                    <?php endif; ?>
                    <p><?= htmlspecialchars(substr($item['short_description'], 0, 200)) ?>...</p>
                    <!-- <a href="<?php echo BASE_URL;?>/knowledge-hub-details.php?id=<?= $item['id'] ?>">Read More</a>
 -->                    <a href="javascript:void(0);" onclick="openKnowledgeDetails(<?= $item['id'] ?>)">Read More</a>

                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <button class="carousel-btn next">&#10095;</button>
</div>

</body>
</html>
<script>
const track = document.querySelector('.carousel-track');
const items = document.querySelectorAll('.carousel-item');
const prevBtn = document.querySelector('.prev');
const nextBtn = document.querySelector('.next');

let index = 0;
const itemsToShow = 3;

function updateCarousel() {
    const itemWidth = items[0].offsetWidth;
    track.style.transform = `translateX(-${index * itemWidth}px)`;
}

nextBtn.addEventListener('click', () => {
    if (index < items.length - itemsToShow) {
        index++;
        updateCarousel();
    }
});

prevBtn.addEventListener('click', () => {
    if (index > 0) {
        index--;
        updateCarousel();
    }
});

window.addEventListener('resize', updateCarousel);


function openKnowledgeDetails(id) {

    fetch("increment-view.php?id=" + id)
        .then(() => {
            window.location.href = "<?php echo BASE_URL;?>/knowledge-hub-details.php?id=" + id;
        })
        .catch(() => {
            window.location.href = "<?php echo BASE_URL;?>/knowledge-hub-details.php?id=" + id;
        });
}

</script>