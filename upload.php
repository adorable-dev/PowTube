<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Generate a 11 caracters long video id
function generateVideoID($length = 11) {
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $id = '';
    for ($i = 0; $i < $length; $i++) {
        $id .= $characters[random_int(0, strlen($characters) - 1)];
    }
    return $id;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);

    if (!empty($_FILES['video_file']['name'])) {
        $targetDir = "uploads/videos/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        $videoExt = strtolower(pathinfo($_FILES["video_file"]["name"], PATHINFO_EXTENSION));
        $videoID = generateVideoID();
        $videoFileName = $videoID . '.' . $videoExt;
        $videoPath = $targetDir . $videoFileName;

        $allowed = ['mp4', 'mov', 'avi', 'mkv'];
        if (in_array($videoExt, $allowed)) {
            if (move_uploaded_file($_FILES["video_file"]["tmp_name"], $videoPath)) {

                // Save the video into the database
                $stmt = $pdo->prepare("INSERT INTO videos (video_id, user_id, title, video_url, added_date) VALUES (?, ?, ?, ?, NOW())");
                $stmt->execute([$videoID, $_SESSION['user_id'], $title, $videoPath]);

                $message = '<div class="success" style="text-align:center;">Video uploaded successfully! 
                    <a href="watch.php?v=' . htmlspecialchars($videoID) . '">Watch it</a></div>';
            } else {
                $message = '<div class="error" style="text-align:center;">Error while uploading file.</div>';
            }
        } else {
            $message = '<div class="error" style="text-align:center;">Invalid file type. Only MP4, MOV, AVI, MKV allowed.</div>';
        }
    } else {
        $message = '<div class="error" style="text-align:center;">Please select a video file.</div>';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Upload Video - PowTube</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .video-preview {
            width: 320px;
            height: 240px;
            background: #000;
            border: 1px solid #ccc;
        }
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
<div class="pageTitle">Upload Your Video</div>
<?= $message ?>

<form method="POST" enctype="multipart/form-data" class="formTable">
    <table align="center">
        <tr>
            <td class="label">Title:</td>
            <td><input type="text" name="title" required></td>
        </tr>
        <tr>
            <td class="label">Video File:</td>
            <td><input type="file" name="video_file" accept="video/*" required></td>
        </tr>
        <tr>
            <td colspan="2" align="center">
                <input type="submit" value="Upload Video">
            </td>
        </tr>
    </table>
</form>

<?php include 'footer.php'; ?>
</body>
</html>
