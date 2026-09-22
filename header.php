<table align="center" width="100%" bgcolor="#D5E5F5" cellpadding="0" cellspacing="0" border="0">
<tr>
    <td width="100%">
        <table width="100%" cellpadding="4" cellspacing="0" border="0">
            <tr>
                <td class="nav"><a href="index.php" class="bold">Home</a></td>
                <td class="nav"><a href="upload.php">Upload</a></td>
                <td class="nav"><a href="my_videos.php">My Videos</a></td>
                <td class="nav"><a href="my_favorites.php">Favorites</a></td>
                <td class="nav"><a href="my_messages.php">Messages</a></td>
                <td class="nav"><a href="results.php">Search</a></td>
                <td align="right" class="nav_sub">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        Welcome, <b><?= htmlspecialchars($_SESSION['username']) ?></b> |
                        <a href="logout.php">Logout</a>
                    <?php else: ?>
                        <a href="login.php">Login</a> | <a href="register.php">Register</a>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
    </td>
</tr>
</table>
