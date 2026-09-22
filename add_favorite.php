<?php
session_start();
require 'db.php';

// check if user is logged and verify the id
if (!isset($_SESSION['user_id']) || !isset($_GET['video_id'])) {
    die("Invalid request.");
}

$user_id = (int)$_SESSION['user_id'];
$video_id = (int)$_GET['video_id'];

// check if the video exists
$videoCheck = $pdo->prepare("SELECT id FROM videos WHERE id = ?");
$videoCheck->execute([$video_id]);
$video = $videoCheck->fetch();

if (!$video) {
    die("Error: The video does not exist.");
}

// check if already in favorites
$favCheck = $pdo->prepare("SELECT id FROM favorites WHERE user_id = ? AND video_id = ?");
$favCheck->execute([$user_id, $video_id]);

if ($favCheck->fetch()) {
    echo "This video is already in your favorites!";
} else {
    // insert into favorites
    $insert = $pdo->prepare("INSERT INTO favorites (user_id, video_id) VALUES (?, ?)");
    if ($insert->execute([$user_id, $video_id])) {
        echo "Added to favorites!";
    } else {
        echo "Error: Could not add to favorites.";
    }
}
?>
