<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Send message
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $to = $_POST['to'];
    $content = $_POST['content'];

    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$to]);
    $receiver = $stmt->fetch();

    if ($receiver) {
        $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, content) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $receiver['id'], $content]);
    }
}

// Messages reçus
$stmt = $pdo->prepare("
SELECT m.*, u.username AS sender_name 
FROM messages m 
JOIN users u ON m.sender_id = u.id 
WHERE m.receiver_id = ? 
ORDER BY sent_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$msgs = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
<title>My Messages - PowTube</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>
<?php include 'header.php'; ?>

<div class="pageTitle">My Messages</div>

<form method="POST" class="formTable">
<table align="center">
    <tr><td class="label">To (username):</td><td><input type="text" name="to"></td></tr>
    <tr><td class="label">Message:</td><td><textarea name="content" cols="40" rows="4"></textarea></td></tr>
    <tr><td colspan="2" align="center"><input type="submit" value="Send Message"></td></tr>
</table>
</form>

<hr>

<?php foreach($msgs as $m): ?>
<div class="commentsEntry">
    <b><?= htmlspecialchars($m['sender_name']) ?>:</b><br>
    <?= nl2br(htmlspecialchars($m['content'])) ?><br>
    <span class="small"><?= $m['sent_at'] ?></span>
</div>
<?php endforeach; ?>

<?php include 'footer.php'; ?>
</body>
</html>
