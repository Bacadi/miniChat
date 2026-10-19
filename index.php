<?php
session_start();
require __DIR__ . '/tools/db.php';
require __DIR__ . '/tools/usercheck.php';

date_default_timezone_set('Europe/Paris');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($username === '' || $message === '') {
        $error = 'Please enter a username and a message.';
    } elseif (mb_strlen($username) > 50) {
        $error = 'Username is too long (50 characters max).';
    } else {
        $userId = getOrCreateUser($pdo, $username);

        $_SESSION['user_id'] = $userId;
        $_SESSION['username'] = $username;

        $stmt = $pdo->prepare(
            'INSERT INTO messages (user_id, content, created_at, ip_address)
             VALUES (:user_id, :content, :created_at, :ip_address)'
        );
        $stmt->execute([
            ':user_id' => $userId,
            ':content' => $message,
            ':created_at' => date('Y-m-d H:i:s'),
            ':ip_address' => $_SERVER['REMOTE_ADDR'],
        ]);

        header('Location: index.php');
        exit;
    }
}

// Display data
$messages = $pdo->query(
    'SELECT messages.user_id, messages.created_at, messages.content, users.username
     FROM messages
     JOIN users ON users.id = messages.user_id
     ORDER BY messages.created_at ASC, messages.id ASC'
)->fetchAll();

$currentUserId = $_SESSION['user_id'] ?? 0;

$users = $pdo->query('SELECT username FROM users ORDER BY username ASC')->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Minichat</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<script>
    const chat = document.querySelector('.chat');
    chat.scrollTop = chat.scrollHeight;
</script>

<body>
    <header>
        <div class="baseblock">
            <h1>Minichat</h1>
        </div>
    </header>

    <main>
        <div class="chat baseblock">
            <?php foreach ($messages as $message): ?>
                <article class="message<?= (int) $message['user_id'] === $currentUserId ? ' mine' : '' ?>">
                    <p class="info">
                        <?= htmlspecialchars($message['created_at']) ?> - <?= htmlspecialchars($message['username']) ?>
                    </p>
                    <p class="smlblock"><?= htmlspecialchars($message['content']) ?></p>
                </article>
            <?php endforeach; ?>
        </div>

        <div class="userslist baseblock">
            <?php foreach ($users as $user): ?>
                <p class="smlblock"><?= htmlspecialchars($user['username']) ?></p>
            <?php endforeach; ?>
        </div>
    </main>

    <form class="smlblock" method="post" action="index.php">
        <input class="info" type="text" name="username" placeholder="Username" maxlength="50" required
            value="<?= htmlspecialchars($_SESSION['username'] ?? '') ?>">
        <input class="info" type="text" name="message" placeholder="Your message" required>
        <button type="submit">➤</button>
    </form>

    <?php if ($error !== ''): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
</body>

</html>