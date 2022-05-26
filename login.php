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
// ログイン内容の入力があれば、入力されたユーザー名でDBを検索し、データ（id, name, password, created, modified）を取得。
if (isset($_POST)) {
    $name = $_POST['name'];
    $stmt = $dbh->prepare('SELECT * FROM users WHERE name = :name');
    $stmt->bindValue(':name', $name, PDO::PARAM_STR);
    $res = $stmt->execute();
}

// データの取得が成功した場合、データを変数に格納。（key:カラム名　value:データ）
if ($res = true) {
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
}

// パスワードが正しければ、トップページに遷移
if (password_verify($_POST['pass'], $data['password'])) {
    header('Location: toppage.php');
}

// if (strpos($_SERVER['HTTP_REFERER'], 'thread') !== false) {
//     $http_r = $_SERVER['HTTP_REFERER'];
//     header('location: $http_r');
// } else {
//     echo strpos($_SERVER['HTTP_REFERER'], 'thread');
//     header('Location: toppage.php');
// }

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="sample text">
    <link rel="stylesheet" type="text/css" href="board.css">
    <title>ログインする</title>
</head>

<body>
    <div class="header">
        <h1><a href="toppage.php">サンプル掲示板</a></h1>
    </div>

    <div class="main">
        <h2>ログインする</h2>
    </div>

    <div class="main">
        <form action="" method="POST" class="login">
            <p>ユーザー名　<input type="text" name="name" value="<?php if (isset($_POST['name'])) {
                                                                echo htmlspecialchars($_POST['name'], ENT_QUOTES);
                                                            } ?>"></p>
            <p>パスワード　<input type="text" name="pass" value="<?php if (isset($_POST['pass'])) {
                                                                echo htmlspecialchars($_POST['pass'], ENT_QUOTES);
                                                            } ?>"></p>
            <p><input type="submit" name="login" value="ログイン"></p>
        </form>
        <p id="hazimete">はじめての方はこちら</p>
        <a href="account_reg.php">
            <button class="account_reg_btn" type="button">新規登録</button>
        </a>
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