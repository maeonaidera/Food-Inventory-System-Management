<?php
require_once('includes/load.php');

if (!isset($_GET['media_id'])) {
    http_response_code(400);
    echo "media_id parameter is required.";
    exit;
}

$media_id = (int)$_GET['media_id'];
$media = find_by_id('media', $media_id);

if (!$media) {
    http_response_code(404);
    echo "Image not found.";
    exit;
}

if (empty($media['file_data'])) {
    http_response_code(404);
    echo "Image data not found.";
    exit;
}

header("Content-Type: " . $media['file_type']);
// Remove Content-Length header because strlen may not be accurate for binary data
// header("Content-Length: " . strlen($media['file_data']));
echo $media['file_data'];
exit;
?>
