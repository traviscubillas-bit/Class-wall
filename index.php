<?php
session_start();
if (!isset($_SESSION['visits'])) {
    $_SESSION['visits'] = 1;
} else {
    $_SESSION['visits']++;
}

include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $author = $_POST['author'] ?? '';
    $body = $_POST['body'] ?? '';
    $remember = $_POST['remember'] ?? '';

    if (!empty($author) && !empty($body)) {
        $stmt = $pdo->prepare("INSERT INTO messages (author, body) VALUES (?, ?)");
        $stmt->execute([$author, $body]);

        if ($remember === 'yes') {
            setcookie('author', $author, time() + (86400 * 30), "/");
        } else {
            setcookie('author', '', time() - 3600, "/");
        }

        header("Location: index.php");
        exit;
    }
}

$cookie_name = $_COOKIE['author'] ?? '';

$search = $_GET['search'] ?? '';

if (!empty($search)) {
    $stmt = $pdo->prepare("SELECT author, body, created_at FROM messages WHERE body LIKE ? OR author LIKE ? ORDER BY created_at DESC");
    $stmt->execute(["%$search%", "%$search%"]);
} else {
    $stmt = $pdo->query("SELECT author, body, created_at FROM messages ORDER BY created_at DESC");
}
$messages = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ClassWall Message Board</title>
</head>
<body>
    <h1>ClassWall</h1>
    
    <p><em>You've visited this page <?php echo $_SESSION['visits']; ?> times during this browser session.</em></p>

    <form method="GET" action="index.php" style="margin-bottom: 20px;">
        <input type="text" name="search" placeholder="Search messages..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Search</button>
        <?php if (!empty($search)): ?>
            <a href="index.php"><button type="button">Clear</button></a>
        <?php endif; ?>
    </form>

    <hr>

    <form method="POST" action="index.php">
        <div>
            <label for="author">Your Name:</label><br>
            <input type="text" id="author" name="author" value="<?php echo htmlspecialchars($cookie_name); ?>" required>
        </div>
        <br>
        <div>
            <label for="body">Your Message:</label><br>
            <textarea id="body" name="body" rows="4" required></textarea>
        </div>
        <br>
        <div>
            <input type="checkbox" id="remember" name="remember" value="yes" <?php echo !empty($cookie_name) ? 'checked' : ''; ?>>
            <label for="remember">Remember my name</label>
        </div>
        <br>
        <button type="submit">Post Message</button>
    </form>

    <hr>

    <h2>Message Wall</h2>
    
    <?php if (empty($messages)): ?>
        <p>No messages found.</p>
    <?php else: ?>
        <?php foreach ($messages as $msg): ?>
            <div style="border: 1px solid #ccc; padding: 10px; margin-bottom: 10px; border-radius: 5px;">
                <strong><?php echo htmlspecialchars($msg['author']); ?></strong> 
                <small style="color: gray;">(<?php echo $msg['created_at']; ?>)</small>
                <p><?php echo nl2br(htmlspecialchars($msg['body'])); ?></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</body>
</html>