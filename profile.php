<?php
session_start();
require 'db.php';

if (!isset($_GET['user'])) {
    die("<div style='text-align:center;color:red;'>User not specified.</div>");
}

$username = $_GET['user'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();

if (!$user) {
    die("<div style='text-align:center;color:red;'>User not found.</div>");
}

if ($user['username'] === '666') {
    echo '<h1>I see you</h1>';
    echo '<p>:)</p>';
}



$videos_stmt = $pdo->prepare("SELECT * FROM videos WHERE user_id = ? ORDER BY added_date DESC");
$videos_stmt->execute([$user['id']]);
$videos = $videos_stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($user['username']) ?> - PowTube Profile</title>
<link rel="stylesheet" href="styles.css">
<style>
.video-thumbnail { margin-bottom: 10px; text-align:center; }
.video-thumbnail video { width: 120px; height: 90px; }
.profileHeader { text-align:center; font-size:18px; font-weight:bold; padding:10px; }
.profileInfo { text-align:center; font-size:14px; color:#333; margin-bottom:15px; }
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
				                            <td><a href="register.php" class="bold">Sign Up</a></td>
				<td>&nbsp;|&nbsp;</td>
				<td><a href="login.php">Log In</a></td>
                				<td>&nbsp;|&nbsp;</td>
				<td><a href="#">Help</a>&nbsp;</td>
						</tr>
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
					<div style="font-size: 13px; font-weight: bold; text-align: right; margin-right: 5px;" bis_skin_checked="1"><a href="browse.php">Browse Videos</a><img border="0" src="/img/new.gif"> &nbsp;//&nbsp; <a href="my_videos_upload.php">Upload Videos</a></div>
				</td>
				
			</tr>
		</tbody></table>
<div class="profileHeader"><?= htmlspecialchars($user['username']) ?>'s Profile</div>

<div class="profileInfo">
    Joined: <?= htmlspecialchars($user['created_at'] ?? '') ?><br>
    Total Videos: <?= count($videos) ?>
</div>

<div style="text-align:center;">
    <h3>Uploaded Videos</h3>
    <?php if (!empty($videos)): ?>
        <?php foreach ($videos as $video): ?>
            <div class="video-thumbnail">
                <a href="watch.php?v=<?= htmlspecialchars($video['video_id']) ?>">
                    <video muted preload="metadata">
                        <source src="<?= htmlspecialchars($video['video_url']) ?>" type="video/mp4">
                    </video>
                </a><br>
                <a href="watch.php?v=<?= htmlspecialchars($video['video_id']) ?>"><?= htmlspecialchars($video['title']) ?></a>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No videos uploaded yet.</p>
    <?php endif; ?>
</div>


<?php include "footer.php"; ?>
</body>
</html>
