<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$stmt = $pdo->prepare("
SELECT videos.* FROM favorites 
JOIN videos ON favorites.video_id = videos.id 
WHERE favorites.user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$favs = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
<title>My Favorites - PowTube</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>
<?php include 'header.php'; ?>
<div class="pageTitle">My Favorites</div>

<?php foreach($favs as $v): ?>
<div class="moduleEntry">
    <a href="watch.php?v=<?= htmlspecialchars($v['video_id']) ?>">
        <img src="<?= htmlspecialchars($v['thumbnail']) ?>" width="120" height="90" class="moduleEntryThumb">
    </a>
    <div class="moduleEntryTitle"><?= htmlspecialchars($v['title']) ?></div>
</div>
<?php endforeach; ?>

<?php include 'footer.php'; ?>
</body>
</html>
