<?php
session_start();
require 'db.php';

if (!isset($_POST['video_id']) || !isset($_POST['comment']) || !isset($_SESSION['user_id'])) {
    die("Invalid request.");
}

$video_id = $_POST['video_id'];
$comment = trim($_POST['comment']);
$user_id = $_SESSION['user_id'];

if ($comment !== '') {
    $stmt = $pdo->prepare("INSERT INTO comments (video_id, user_id, comment, added_date) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$video_id, $user_id, $comment]);

    // update the views counter (need to add the views counter to watch.php
    $update = $pdo->prepare("UPDATE videos SET comments = comments + 1 WHERE video_id = ?");
    $update->execute([$video_id]);

    echo "Comment added!";
} else {
    echo "Comment cannot be empty.";
}
?>
