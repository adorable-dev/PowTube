<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM videos WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$videos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
<title>My Videos - PowTube</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>
<?php include 'header.php'; ?>
<div class="pageTitle">My Videos</div>

<?php foreach($videos as $v): ?>
<div class="moduleEntry">
    <img src="<?= htmlspecialchars($v['thumbnail']) ?>" width="120" height="90" class="moduleEntryThumb">
    <div class="moduleEntryTitle"><?= htmlspecialchars($v['title']) ?></div>
    <div class="moduleEntryDetails"><?= $v['added_date'] ?></div>
</div>
<?php endforeach; ?>

<?php include 'footer.php'; ?>
</body>
</html>
