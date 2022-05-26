<?php
session_start();
require_once('db_board.php');
require_once('fanctions.php');
// echo var_dump($_);
// echo var_dump($_POST);
// echo var_dump($_SERVER);
// echo var_dump($_SESSION);
// echo var_dump($_COOKIE);

// echo var_dump($_POST['name']);
// echo var_dump($_POST['pass']);
// echo var_dump($_SESSION['name']);
// echo var_dump($_SESSION['pass']);
// echo var_dump($_SERVER['HTTP_HOST']);
// echo var_dump($_SERVER['HTTP_REFERER']);
// echo var_dump(strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']));
?>

<?php

// $_POSTが空（' '空文字, 0, false, NULL）ではなければ実行
if (isset($_POST['name']) && isset($_POST['pass'])) {
    $_SESSION['name'] = $_POST['name'];
    $_SESSION['pass'] = $_POST['pass'];
    header('Location: account_reg_cfm.php');
    exit();
}


// // $_POSTが空（' '空文字, 0, false, NULL）ではなければ実行
//     if (!empty($_POST['name']) && !empty($_POST['pass'])) {
//         $name = $_POST['name'];
//         $pass = $_POST['pass'];
//         $hash = password_hash($pass, PASSWORD_BCRYPT);
//         $limit = 4;
//         $passLength = strlen($pass);

//         // $_POSTが空（' '空文字, 0, false, NULL）ではない & $_POST[password]が4文字以上か確認
//         if ($passLength >= $limit) {
//             $stmt = $dbh->prepare('INSERT INTO users (name, password) VALUES (:name, :password)');
//             $stmt->bindValue(':name', $name, PDO::PARAM_STR);
//             $stmt->bindValue(':password', $hash, PDO::PARAM_STR);
//             $stmt->execute();
//             header ('Location: account_reg_cfm.php');
//             exit();
//         }

//     }
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="sample text">
    <link rel="stylesheet" type="text/css" href="board.css">
    <title>アカウント新規登録</title>
</head>

<body>
    <div class="header">
        <h1><a href="toppage.php">サンプル掲示板</a></h1>
    </div>

    <div class="main">
        <h2>アカウント新規登録</h2>
        <p>ユーザー名とパスワード（4文字以上）を入力してください。</p>
    </div>

    <div class="main">
        <form action="" method="POST" class="account_reg">
            <p>ユーザー名　<input type="text" name="name"></p>
            <p>パスワード　<input type="text" name="pass"></p>
            <p><input type="submit" name="account_reg_cfm_btn" value="確認画面にすすむ"></p>
        </form>
    </div>

    <div class="main">
        <!-- 前のページが存在している & 前のページのアドレスにサイトのホスト名が含まれていれば、前のページに戻るボタンを表示する -->
        <?php $host_name = $_SERVER['HTTP_HOST'];
        if (!empty($_SERVER['HTTP_REFERER']) && (strpos($_SERVER['HTTP_REFERER'], $host_name) !== false)) : ?>
            <a href="<?php echo $_SERVER['HTTP_REFERER']; ?>">
                <button class="back_btn" type="button">前の画面に戻る</button>
            </a>
        <?php endif; ?>
    </div>

</body>

</html>