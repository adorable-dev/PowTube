<?php
include 'db.php';
session_start();

$q = $_GET['q'] ?? '';
$videos = [];

if ($q) {
    $stmt = $pdo->prepare("
        SELECT v.*, u.username AS author 
        FROM videos v
        LEFT JOIN users u ON v.user_id = u.id
        WHERE v.title LIKE ?
        ORDER BY v.added_date DESC
    ");
    $stmt->execute(['%' . $q . '%']);
    $videos = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Search Results - PowTube</title>
<link rel="stylesheet" href="styles.css">
<style>
.moduleEntry { background:#DDD; padding:10px; margin:10px; display:inline-block; vertical-align:top; width:200px; text-align:center; }
.moduleEntryThumb { width:120px; height:90px; object-fit:cover; border:1px solid #999; }
.moduleEntryTitle { font-weight:bold; margin-top:5px; }
</style>
</head>
<body>
<?php include 'header.php'; ?>

<div class="pageTitle">Search Videos</div>

<form method="GET" class="formTable" style="text-align:center;">
<input type="text" name="q" size="40" value="<?= htmlspecialchars($q ?? '') ?>">
<input type="submit" value="Search">
</form>

<div style="text-align:center;">
<?php if (empty($videos)): ?>
<p>No videos found.</p>
<?php else: ?>
<?php foreach($videos as $v): ?>
<div class="moduleEntry">
<a href="watch.php?v=<?= htmlspecialchars($v['video_id'] ?? '') ?>">
<video class="moduleEntryThumb" preload="metadata" muted>
<source src="<?= htmlspecialchars($v['video_url'] ?? '') ?>" type="video/mp4">
</video>
</a>
<div class="moduleEntryTitle"><?= htmlspecialchars($v['title'] ?? '') ?></div>
<div style="font-size:11px; color:#444;">
By: <?= htmlspecialchars($v['author'] ?? 'Anonymous') ?><br>
Added: <?= htmlspecialchars($v['added_date'] ?? '') ?>
</div>
</div>
<?php endforeach; ?>
<?php endif; ?>
</div>

<script>
document.querySelectorAll('video.moduleEntryThumb').forEach(v => {
  v.addEventListener('mouseenter', () => v.play());
  v.addEventListener('mouseleave', () => { v.pause(); v.currentTime = 0; });
});
</script>

<?php include 'footer.php'; ?>
</body>
</html>
