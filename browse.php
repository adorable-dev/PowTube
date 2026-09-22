<?php
session_start();
require 'db.php'; 

// recover teh videos
$stmt = $pdo->query("SELECT v.*, u.username AS author 
                     FROM videos v 
                     JOIN users u ON v.user_id = u.id
                     ORDER BY v.added_date DESC
                     LIMIT 10");
$videos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>PowTube - Browse Videos</title>
<link rel="stylesheet" href="styles.css">
<style>
.moduleEntry { background:#DDD; padding:10px; margin:10px; display:inline-block; vertical-align:top; width:200px; text-align:center; }
.moduleEntryThumb { width:120px; height:90px; object-fit:cover; border:1px solid #999; }
.moduleEntryTitle { font-weight:bold; margin-top:5px; }
</style>
</head>
<body>
<div class="tableLinkBar" bis_skin_checked="1">
<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tbody><tr valign="top">
				<td width="130" rowspan="2"><a href="index.php"><img src="/img/logo.gif" width="120" height="48" alt="YuoToob" border="0" hspace="5" vspace="8"></a></td>
				<td width="100%" align="right">
		<table align="right" cellpadding="2" cellspacing="0" border="0">
			<tbody><tr>
		</tbody></table>
		</td>
	</tr>
            
		<tr>
		<td width="100%">
		<table cellpadding="2" cellspacing="0" border="0">
			<tbody><tr>
				<form method="GET" action="results.php"></form>
				<td>
					<input type="text" value="" name="search" size="30" maxlength="128" style="color:#ff3333; font-size: 16px; padding: 3px;">
				</td>
				<td>
					<input type="submit" value="Search Videos">
				</td>

				<td width="100%">
					<div style="font-size: 13px; font-weight: bold; text-align: right; margin-right: 5px;" bis_skin_checked="1"><a href="browse.php">Browse Videos</a><img border="0" src="/img/new.gif"> &nbsp;//&nbsp; <a href="upload.php">Upload Videos</a></div>
				</td>
				
			</tr>
		</tbody></table>
<?php include "header.php" ?>
<?php


// recover teh videos
$stmt = $pdo->query("SELECT v.*, u.username AS author 
                     FROM videos v 
                     JOIN users u ON v.user_id = u.id
                     ORDER BY v.added_date DESC
                     ");
$videos = $stmt->fetchAll();
?>


<h2 style="text-align:center;">Most Recent Videos</h2>
<div style="text-align:center;">
<?php foreach ($videos as $video): ?>
    <div class="moduleEntry">
        <a href="watch.php?v=<?= htmlspecialchars($video['video_id']) ?>">
            <video class="moduleEntryThumb" preload="metadata" muted>
                <source src="<?= htmlspecialchars($video['video_url']) ?>" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </a>
        <div class="moduleEntryTitle">
            <a href="watch.php?v=<?= htmlspecialchars($video['video_id']) ?>">
                <?= htmlspecialchars($video['title']) ?>
            </a>
        </div>
        <div class="moduleEntryDetails">
            Added: <?= htmlspecialchars(date("F j, Y", strtotime($video['added_date']))) ?><br>
            by <a href="profile.php?user=<?= urlencode($video['author']) ?>"><?= htmlspecialchars($video['author']) ?></a><br>
            Views: <?= (int)($video['views'] ?? 0) ?> | Comments: <?= (int)($video['comments'] ?? 0) ?>
        </div>
    </div>
<?php endforeach; ?>


<?php include "footer.php"; ?>
</body>
</html>

