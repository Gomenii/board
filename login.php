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


if (empty($_POST['name'])) {
    $errors[] = '※ユーザー名を入力してください';
}

if (empty($_POST['pass'])) {
    $errors[] = '※パスワードを入力してください';
}

// 入力されたユーザー名でDBの検索を実行。データ（id, name, password, created, modified）を取得
if (!empty($_POST['name']) && !empty($_POST['pass'])) {
    $name = $_POST['name'];
    $stmt = $dbh->prepare('SELECT * FROM users WHERE name = :name');
    $stmt->bindValue(':name', $name, PDO::PARAM_STR);
    $res = $stmt->execute();
}

// DBの検索に成功した場合、データを変数に格納。（key:カラム名　value:データ）　失敗した場合、案内を表示
if (isset($res)) {
    if ($res) {
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $errors[] = '※サーバーエラーのためしばらくお待ちいただいてから、再度ログインしてください。';
    }
}

// DBの検索結果があった場合は、入力されたパスワードがDBのハッシュ化されたパスワードと一致するか確認
if (isset($data)) {
    if ($data !== false) {
        $passMatch = password_verify($_POST['pass'], $data['password']);
    } else {
        $errors[] = '※ユーザー名が違うか、登録されていません。';
    }
}

// パスワードが一致していればトップページに遷移
if (isset($passMatch)) {
    if ($passMatch) {
        header('Location: toppage.php');
        exit();
    } else {
        $errors[] = '※パスワードが違います。';
    }
}
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
            <h4><?php foreach ($errors as $error) {
                    echo $error . '<br>' . '<br>';
                } ?></h4>
            <p>ユーザー名　<input type="text" name="name" value="<?php if (isset($_POST['name'])) {
                                                                htmlsc($_POST['name']);
                                                            } ?>"></p>
            <p>パスワード　<input type="text" name="pass" value="<?php if (isset($_POST['pass'])) {
                                                                htmlsc($_POST['pass']);
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
        <?php $hostName = $_SERVER['HTTP_HOST'];
        if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], $hostName) !== false) : ?>
            <a href="<?php echo $_SERVER['HTTP_REFERER']; ?>">
                <button class="back_btn" type="button">前の画面に戻る</button>
            </a>
        <?php endif; ?>
    </div>

</body>

</html>