<?php
require('tools/db.php');
require('tools/usercheck.php');

date_default_timezone_set('Europe/Paris');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST["username"] ?? '');
    $message = trim($_POST['message'] ?? '');
    $datetime = date('Y-m-d H:i:s');

    

    $sql = "INSERT INTO messages (datehour, content, username) VALUES (:username, :content, :datehour)";

    if ($username === '' || $message === '') {
        $msg = 'Merci de choisir un pseudo';
    } else {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':username' => $username,
            ':content' => $message,
            ':datehour' =>$datehour
        ]);
        header('Location: index.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mini Chat</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <form action="" method="post">
        <input id="username" name="username" reuired placeholder="Pseudo" value="<?= htmlspecialchars($username ?? '') ?>">
        <input id="message" name="message" required placeholder="Ecrire..." value="<?= htmlspecialchars($message ?? '') ?>">
        <button type="submit">></button>
    </form>
</body>
</html>