<?php
require 'db.php';

if (!isset($_GET['video_id'])) {
    http_response_code(400);
    exit('Missing video_id parameter.');
}

$video_id = $_GET['video_id'];

// Récup le chemin de la vidéo /D
$stmt = $pdo->prepare("SELECT video_url FROM videos WHERE video_id = ?");
$stmt->execute([$video_id]);
$row = $stmt->fetch();

if (!$row || empty($row['video_url'])) {
    http_response_code(404);
    exit('Video not found.');
}

// Le chemin de la vidéo
$videoPath = __DIR__ . '/' . $row['video_url'];

if (!file_exists($videoPath)) {
    http_response_code(404);
    exit('Video file not found on server.');
}

// vidéo vers le navigateur
header('Content-Type: video/mp4');
header('Content-Length: ' . filesize($videoPath));
readfile($videoPath);
exit;
