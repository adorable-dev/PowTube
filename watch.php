<?php
session_start();
require 'db.php';

// Récup l'ID
if (!isset($_GET['v'])) {
    die("<div style='text-align:center;color:red;'>Invalid video ID.</div>");
}
$video_id = $_GET['v'];

// Récupère la vidéo et son auteur
$stmt = $pdo->prepare("
    SELECT v.*, u.username AS author
    FROM videos v
    LEFT JOIN users u ON v.user_id = u.id
    WHERE v.video_id = ?
");
$stmt->execute([$video_id]);
$video = $stmt->fetch();

if (!$video) {
    die("<div style='text-align:center;color:red;'>Video not found.</div>");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($video['title'] ?? 'Video') ?> - PowTube</title>
<link rel="stylesheet" href="styles.css">
<style>
.playerScreen video {
  width: 100%;
  height: auto;
  background: #000;
}
.pageTitle { font-size: 18px; text-align:center; font-weight:bold; padding:10px; }
.watchDescription { font-size:13px; color:#333; padding:5px 0; }
.watchTags a { text-decoration:none; color:#3366cc; }
.watchTags a:hover { text-decoration:underline; }
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
<?php include "header.php"?>

<?php if ($video['removed'] == 1): ?>
    <div style="text-align:center; color:red; font-weight:bold; font-size:18px; margin:50px 0;">
        This video has been removed because it break our rules.
    </div>
<?php else: ?>
                

<div class="pageTitle"><?= htmlspecialchars($video['title'] ?? '') ?></div>


<table width="795" align="center" cellpadding="0" cellspacing="0" border="0">
<tr valign="top">
<td width="515" style="padding-right: 15px;">
  <div style="text-align:center;font-weight:bold;">
    <a href="#">Share</a> // 
    <a href="#comment">Comment</a> // 
    <a href="add_favorite.php?video_id=<?= urlencode($video['video_id']) ?>" target="_blank">Add to Favorites</a> //
    <a href="profile.php?user=<?= urlencode($video['author'] ?? 'Anonymous') ?>">Contact Me</a>
  </div>

<iframe
  id="vid-player"
  style="border: 0px; overflow: hidden;"
  src="player/lolplayer.php?id=<?= urlencode($_GET['v']) ?>"
  height="360"
  width="480">
</iframe>
<br><br>

<script>
function hmsToSecondsOnly(str) {
    var p = str.split(':'),
        s = 0, m = 1;
    while (p.length > 0) {
        s += m * parseInt(p.pop(), 10);
        m *= 60;
    }
    return s;
}

function setTimePlayer(seconds) {
    var parsedSec = hmsToSecondsOnly(seconds);
    var player = document.getElementById('vid-player');
    if (player && player.contentWindow && player.contentWindow.document) {
        var video = player.contentWindow.document.getElementById('video-stream');
        if (video) {
            video.currentTime = parsedSec;
        }
    }
}
</script>


  <div class="watchDescription">
    <?= nl2br(htmlspecialchars($video['description'] ?? 'No description available.')) ?>
  </div>

  <div class="watchTags">
    Tags // 
    <?php
    if (!empty($video['tags'])) {
        $tags = explode(',', $video['tags']);
        foreach ($tags as $tag) {
            $tag = trim($tag);
            echo '<a href="results.php?q=' . urlencode($tag) . '">' . htmlspecialchars($tag) . '</a> ';
        }
    } else {
        echo 'No tags.';
    }
    ?>
  </div>

  <div class="watchAdded">
    Added: <?= htmlspecialchars($video['added_date'] ?? '') ?> by 
    <a href="profile.php?user=<?= urlencode($video['author'] ?? 'Anonymous') ?>">
      <?= htmlspecialchars($video['author'] ?? 'Anonymous') ?>
    </a>
  </div>

<div class="watchDetails">
    Views: <?= (int)($video['views'] ?? 0) ?> |
    <a href="#comment">Comments</a>: <?= (int)($video['comments'] ?? 0) ?>
</div>

<br>


<a name="comment"></a>
<div style="padding-bottom:5px;font-weight:bold;color:#444;">Comment on this video:</div>
<form name="comment_form" method="post" action="add_comment.php" target="invisible">
    <input type="hidden" name="video_id" value="<?= (int)$video['id'] ?>">
    <textarea name="comment" cols="55" rows="3"></textarea><br>
    <input type="submit" name="comment_button" value="Add Comment">
</form>

<br>
<div class="commentsTitle">Comments (<?= (int)($video['comments'] ?? 0) ?>):</div>

<?php
// Récup les comments
$comments_stmt = $pdo->prepare("
    SELECT c.comment, u.username, c.added_date
    FROM comments c
    JOIN users u ON c.user_id = u.id
    WHERE c.video_id = ?
    ORDER BY c.added_date ASC
");
$comments_stmt->execute([$video['id']]);
$comments = $comments_stmt->fetchAll();

if (!empty($comments)) {
    echo '<div class="commentsList">';
    foreach ($comments as $c) {
        echo '<div class="commentItem" style="border-bottom:1px solid #ddd; padding:5px 0;">';
        echo '<strong>' . htmlspecialchars($c['username']) . '</strong> ';
        echo '<span style="color:#888; font-size:12px;">(' . $c['added_date'] . ')</span><br>';
        echo nl2br(htmlspecialchars($c['comment']));
        echo '</div>';
    }
    echo '</div>';
} else {
    echo '<p>No comments yet. Be the first to comment!</p>';
}
?>



<td width="300" valign="top">
  <table width="300" align="center" cellpadding="0" cellspacing="0" border="0" bgcolor="#CCCCCC">
    <tr>
      <td><div class="moduleTitleBar"><div class="moduleFrameBarTitle">Related Videos</div></div></td>
    </tr>
    <tr>
      <td style="padding:5px;">
        <?php
        $related = $pdo->prepare("
          SELECT video_id, title, video_url 
          FROM videos 
          WHERE video_id != ? 
          ORDER BY RAND() 
          LIMIT 5
        ");
        $related->execute([$video_id]);
        foreach ($related as $rel) {
            echo '<div class="video-thumbnail" style="margin-bottom:10px;text-align:center;">';
            echo '<a href="watch.php?v=' . htmlspecialchars($rel['video_id']) . '">';
            echo '<video width="120" height="90" muted preload="metadata">';
            echo '<source src="' . htmlspecialchars($rel['video_url']) . '" type="video/mp4">';
            echo '</video></a><br>';
            echo '<a href="watch.php?v=' . htmlspecialchars($rel['video_id']) . '">' . htmlspecialchars($rel['title']) . '</a>';
            echo '</div>';
        }
        ?>
      </td>
    </tr>
  </table>
</td>
</tr>
</table>

<br>

<?php include "footer.php"; ?>
</body>
</html>
<?php endif; ?>

<?php include "footer.php"; ?>
</body>
</html>
