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

if ($loginJudge == '未ログイン') {
    $display = '※投稿したりスレッドを作成するには、<a href="account_reg.php">新規登録</a>または<a href="login.php">ログイン</a>が必要です。';
}




// スレッド表示用の格納
// 入力されたユーザー名でDBの検索を実行。データ（id, name, password, created, modified）を取得
$stmt = $dbh->prepare('SELECT id FROM threads ORDER BY id DESC LIMIT 1');
$stmt->execute();
$id = $stmt->fetch(PDO::FETCH_ASSOC);

var_dump($id);

for ($count = $id; $count > 0; $count--) {
    echo $count;
    $stmt = $dbh->prepare('SELECT * FROM threads WHERE id = :id');
    $stmt->bindValue(':id', $count, PDO::PARAM_STR);
    $stmt->execute();
    $data[] = $stmt->fetch(PDO::FETCH_ASSOC);
}
var_dump($data);

// for ($count = 10; $count > 0; $count--){
//     echo $count;
// }

// DBの検索に成功した場合、データを変数に格納。（key:カラム名　value:データ）　失敗した場合、案内を表示
if (isset($res)) {
    if ($res) {
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $errors[] = '※サーバーエラーのためしばらくお待ちいただいてから、再度ログインしてください。';
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
            <h2>この掲示板はポートフォリオ用となっています</h2>
            <?php if (isset($display)) {
                echo $display;
            } ?>
            <p><a href="new_thread.php">
                    <button class="btn blue_btn new_thread_btn" type="button">スレッド作成</button>
                </a></p>
        </div>

        <div class="content">
            <?php foreach ($errors as $error) {
                echo $error . '<br>' . '<br>';
            } ?>





            <a href="thread1.php">
                <button class="btn blue_btn thread_btn" type="button">スレッド1</button>
            </a>
        </div>

    </div>

</body>

</html>