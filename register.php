<?php
include 'db.php';
session_start();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['field_register_username']);
    $email = trim($_POST['field_register_email']);
    $password = password_hash($_POST['field_register_password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    try {
        $stmt->execute([$username, $email, $password]);
        $success = 'Account created! <a href="login.php">Login here</a>';
    } catch (PDOException $e) {
        $error = 'Username already taken.';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Register - PowTube</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>
<?php include 'header.php'; ?>
<td width="130" rowspan="2"><a href="index.php"><img src="img/logo.gif" width="120" height="48" alt="YouTube" border="0" hspace="5" vspace="8"></a></td>
<table align="center" width="100%" bgcolor="#D5E5F5" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td><img src="/web/20050630014256im_/http://www.youtube.com/img/box_login_tl.gif" width="5" height="5"></td>
        <td><img src="/web/20050630014256im_/http://www.youtube.com/img/pixel.gif" width="1" height="5"></td>
        <td><img src="/web/20050630014256im_/http://www.youtube.com/img/box_login_tr.gif" width="5" height="5"></td>
    </tr>
</table>

<div class="pageTitle">Create Your Account</div>

<table width="80%" align="center" cellpadding="5" cellspacing="0" border="0">
<tr valign="top">
    <td>
        <span class="highlight">What is Powtube?</span>
        <br><br>
        YouTube is a way to get your videos to the people who matter to you. With PowTube you can:
        <ul>
            <li> Show off your favorite videos to the world
            <li> Blog the videos you take with your digital camera or cell phone
            <li> Securely and privately show videos to your friends and family around the world
            <li> ... and much, much more!
        </ul>
        <br><span class="highlight"><a href="signup.php">Sign up now</a> and open a free account.</span>
        <br><br><br>
        To learn more about our service, please see our <a href="help.php">Help</a> section.<br><br><br>
    </td>
    <td width="20"><img src="/web/20050630014256im_/http://www.youtube.com/img/pixel.gif" width="20" height="1"></td>
    <td width="300">
        <div style="background-color: #D5E5F5; padding: 10px; padding-top: 5px; border: 6px double #FFFFFF;">
        <table width="100%" bgcolor="#D5E5F5" cellpadding="5" cellspacing="0" border="0">
            <form method="post" action="register.php">
                <input type="hidden" name="field_command" value="register_submit">
                <tr>
                    <td align="center" colspan="2">
                        <div style="font-size: 14px; font-weight: bold; color:#003366; margin-bottom: 5px;">PowTube Registration</div>
                    </td>
                </tr>
                <?php if ($success): ?>
                <tr>
                    <td colspan="2" align="center" style="color:green; font-weight:bold;"><?= $success ?></td>
                </tr>
                <?php endif; ?>
                <?php if ($error): ?>
                <tr>
                    <td colspan="2" align="center" style="color:red; font-weight:bold;"><?= $error ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td align="right"><span class="label">Username:</span></td>
                    <td><input type="text" size="20" name="field_register_username" value="<?= htmlspecialchars($_POST['field_register_username'] ?? '') ?>"></td>
                </tr>
                <tr>
                    <td align="right"><span class="label">Email:</span></td>
                    <td><input type="text" size="20" name="field_register_email" value="<?= htmlspecialchars($_POST['field_register_email'] ?? '') ?>"></td>
                </tr>
                <tr>
                    <td align="right"><span class="label">Password:</span></td>
                    <td><input type="password" size="20" name="field_register_password"></td>
                </tr>
                <tr>
                    <td align="right"><span class="label">&nbsp;</span></td>
                    <td><input type="submit" value="Register"></td>
                </tr>
            </form>
        </table>
        </div>
    </td>
</tr>
</table>

<?php include 'footer.php'; ?>
</body>
</html>
