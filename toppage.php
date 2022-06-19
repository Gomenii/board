<?php
session_start();
require_once('db_board.php');
require_once('fanctions.php');
unset($_SESSION['title'], $_SESSION['content'], $_SESSION['csrfToken']);
var_dump($_SESSION);

//　セッションタイムアウト判定
if (isset($_SESSION['loginName']) && time() - $_SESSION['start'] > 600) {
    $_SESSION = array();
    $display = '時間が経過したため、ログイン状態が解除されました。<a href="login.php">再ログイン</a>';
}

// ログイン判定
if (isset($_SESSION['loginName'])) {
    $loginJudge = 'ログイン中';
    $_SESSION['start'] = time();
} else {
    $loginJudge = '未ログイン';
    $display = '※投稿したりスレッドを作成するには、<a href="account_reg.php">新規登録</a>または<a href="login.php">ログイン</a>が必要です。';
}

// 最新のスレッド50件のデータをDBから取得
$stmt = $dbh->prepare('SELECT * FROM threads ORDER BY id DESC LIMIT 50');
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="sample text">
    <link rel="stylesheet" type="text/css" href="board.css">
    <title>掲示板</title>
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
            <h2> <?php
                    if (isset($_SESSION['loginName'])) {
                        echo 'こんにちは！' . $_SESSION['loginName'] . 'さん！';
                    } else {
                        echo 'この掲示板はポートフォリオ用となっています';
                    }
                    ?> </h2>
            <?php
            if (isset($display)) {
                echo $display;
            } ?>
            <p><a href="new_thread.php">
                    <button class="btn btn_blue" type="button">スレッド作成</button>
                </a></p>
        </div>

        <div class="content">
            <?php
            for ($a = 0; $a < $data[0]['id']; $a++) {
                if (isset($data[$a])) {
                    echo 'タイトル：' . '<a href="thread' . $data[$a]['id'] . '.php">' . mb_strimwidth($data[$a]['title'], 0, 64, '...', 'UTF-8')  . '</a>' . '<br>';
                    echo '作成者：' . $data[$a]['name'] . '　作成日時：' . $data[$a]['created'] . '<br>' . '<br>' . '<br>';
                }
            }
            ?>
        </div>

        <div class="bottom">
            <!-- 前のページが存在している & 前のページのアドレスにサイトのホスト名が含まれていれば、前のページに戻るボタンを表示する -->
            <?php $hostName = $_SERVER['HTTP_HOST'];
            if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], $hostName) !== false) : ?>
                <a href="<?php echo $_SERVER['HTTP_REFERER']; ?>">
                    <button class="btn" type="button">前の画面に戻る</button>
                </a>
            <?php endif; ?>
        </div>

    </div>

</body>

</html>