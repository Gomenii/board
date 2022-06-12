<?php
session_start();
require_once('db_board.php');
require_once('fanctions.php');

// ログイン判定
if (isset($_SESSION['loginName'])) {
    $loginJudge = 'ログイン中';
} else {
    $loginJudge = '未ログイン';
}

if (isset($_SESSION['loginName']) && time() - $_SESSION['start'] > 600) {
    unset($_SESSION['loginName'], $_SESSION['loginPass']);
    $display = '時間が経過したため、ログイン状態が解除されました。<a href="login.php">再ログイン</a>';
}
$_SESSION['start'] = time();

// 入力判定
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
        $_SESSION['loginName'] = $_POST['name'];
        $_SESSION['loginPass'] = $_POST['pass'];
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
    <script type="text/javascript">
        window.addEventListener('DOMContentLoaded', () => {
            const btn = document.querySelector('.menu_btn');
            const nav = document.querySelector('nav');
            btn.addEventListener('click', () => {
                nav.classList.toggle('open_menu')
                if (btn.innerHTML === 'Menu') {
                    btn.innerHTML = 'Close';
                } else {
                    btn.innerHTML = 'Menu';
                }
            });
        });
    </script>
</head>

<body>

    <div class="header">
        <h1 class="header_title"><a href="toppage.php">サンプル掲示板</a></h1>
        <button class="menu_btn">Menu</button>
        <p><?php echo $loginJudge; ?></p>
        <nav class="menu_list">
            <ul>
                <li><a href="toppage.php">トップページ</a></li>
                <li><a href="mypage.php">マイページ</a></li>
                <li><a href="logout_cfm.php">ログアウト</a></li>
                <li><a href="inquiry.php">お問い合わせ</a></li>
                <li><a href="admin.php">運営者情報</a></li>
            </ul>
        </nav>
    </div>

    <div class="main">

        <div class="head">
            <h2 class="head_title">ログインする</h2>
            <?php if (isset($display)) {
                echo $display;
            } ?>
        </div>

        <div class="post">
            <h4 class="post_error"><?php foreach ($errors as $error) {
                                        echo $error . '<br>' . '<br>';
                                    } ?></h4>
            <form class="post_form" action="" method="POST">
                <p>ユーザー名　<input type="text" name="name" value="<?php if (isset($_POST['name'])) {
                                                                    htmlsc($_POST['name']);
                                                                } ?>"></p>
                <p>パスワード　<input class="" type="text" name="pass" value="<?php if (isset($_POST['pass'])) {
                                                                            htmlsc($_POST['pass']);
                                                                        } ?>"></p>
                <p><input class="btn" type="submit" name="login" value="ログイン"></p>
            </form>
        </div>

        <p id="hazimete">はじめての方はこちら</p>
        <a href="account_reg.php">
            <button class="btn blue_btn new_account_btn" type="button">新規登録</button>
        </a>

        <div class="bottom">
            <!-- 前のページが存在している & 前のページのアドレスにサイトのホスト名が含まれていれば、前のページに戻るボタンを表示する -->
            <?php $hostName = $_SERVER['HTTP_HOST'];
            if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], $hostName) !== false) : ?>
                <a href="<?php echo $_SERVER['HTTP_REFERER']; ?>">
                    <button class="btn back_btn" type="button">前の画面に戻る</button>
                </a>
            <?php endif; ?>
        </div>

    </div>

</body>

</html>