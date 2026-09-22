<?php
session_start();

$host = 'change that';
$db   = 'change that';
$user = 'change that';
$pass = 'change that';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die('<div class="error">Erreur de connexion : ' . htmlspecialchars($e->getMessage()) . '</div>');
}


$tags = $pdo->query("SELECT name, font_size FROM tags ORDER BY id DESC LIMIT 30")->fetchAll();

$videos = $pdo->query("
    SELECT v.*, u.username AS author
    FROM videos v
    LEFT JOIN users u ON v.user_id = u.id
    ORDER BY v.added_date DESC
    LIMIT 5
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>PowTube - Home Page</title>
<link rel="stylesheet" href="styles.css">
<style>
video.moduleFeaturedThumb { width: 120px; height: 90px; object-fit: cover; border:1px solid #999; background:#000; }
.video-cell { text-align:center; padding:10px; }
.moduleFeaturedTitle a { font-weight:bold; text-decoration:none; color:#000; }
.moduleFeaturedTitle a:hover { text-decoration:underline; }
</style>
</head>
<body>

<?php include "header.php"; ?>


<table width="80%" align="center" cellpadding="5" cellspacing="0" border="0">
<tr>
<td align="center">
<img src="img/logo.gif" width="180" height="71" alt="YouTube"><br>
<span class="highlight">Your Digital Video Repository</span><br><br>
</td>
</tr>
<form method="GET" action="results.php" name="searchForm">
<tr>
<td align="center">
<input type="text" name="q" size="35" maxlength="128" style="font-size:18px; padding:3px;">
</td>
</tr>
<tr>
<td align="center"><input type="submit" value="Search Videos"></td>
</tr>
</form>
</table>


<div style="text-align:center; margin:20px 0;">
<a href="upload.php" class="highlight">Upload Your Videos</a> &nbsp; // &nbsp; <a href="browse.php" class="highlight">Browse Videos</a><img border="0" src="/img/new.gif">
</div>


<div style="text-align:center; margin:20px auto; width:60%; font-size:13px; color:#333;">
<span class="table_top">Latest Tags //</span><br><br>
<?php foreach ($tags as $tag): ?>
<a href="results.php?q=<?= urlencode($tag['name'] ?? '') ?>"
   style="font-size:<?= (int)($tag['font_size'] ?? 12) ?>px; margin:3px;">
   <?= htmlspecialchars($tag['name'] ?? '') ?>
</a>
<?php endforeach; ?>
</div>


<table width="80%" align="center" cellpadding="0" cellspacing="0" border="0" bgcolor="#CCCCCC">
<tr>
<td>
<div class="moduleTitleBar">
<div class="moduleTitle">Featured Videos</div>
</div>

<div class="moduleFeatured">
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr valign="top">
<?php foreach ($videos as $video): ?>
<td width="20%" class="video-cell">
<a href="watch.php?v=<?= htmlspecialchars($video['video_id'] ?? '') ?>">
<video class="moduleFeaturedThumb" preload="metadata" muted>
<source src="<?= htmlspecialchars($video['video_url'] ?? '') ?>" type="video/mp4">
</video>
</a>
<div class="moduleFeaturedTitle">
<a href="watch.php?v=<?= htmlspecialchars($video['video_id'] ?? '') ?>">
<?= htmlspecialchars($video['title'] ?? '') ?>
</a>
</div>
<div class="moduleFeaturedDetails" style="font-size:11px; color:#444;">
Added: <?= htmlspecialchars($video['added_date'] ?? '') ?><br>
By: 
<?php if (!empty($video['author'])): ?>
<a href="profile.php?user=<?= urlencode($video['author']) ?>">
<?= htmlspecialchars($video['author']) ?>
</a>
<?php else: ?>
Anonymous
<?php endif; ?>
</div>
</td>
<?php endforeach; ?>
</tr>
</table>
</div>
</td>
</tr>
</table>

<script>
document.querySelectorAll('video.moduleFeaturedThumb').forEach(v => {
  v.addEventListener('mouseenter', () => v.play());
  v.addEventListener('mouseleave', () => { v.pause(); v.currentTime = 0; });
});
</script>

<?php include "footer.php"; ?>
</body>
</html>
