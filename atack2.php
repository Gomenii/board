<?php
session_start(); ?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="sample text">
    <link rel="stylesheet" type="text/css" href="board.css">
    <title>スレッド作成</title>
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
        <p><?php echo $loginJudge; ?></p>
        <button class="menu_btn">Menu</button>
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
            <h2 class="head_title">スレッド作成</h2>
            <!-- <h4>セキュリティ対策のため、右記5種の半角記号を使用すると文字化けが起きる仕様となっています。 < > & " '</h4> -->
            <?php if (isset($display)) {
                echo $display;
            } ?>
        </div>

        <div class="post">       
            <form action="http://localhost/board/new_thread_cfm.php" method="post">
                <input type="hidden" name="token" value="<?php echo $_SESSION['csrfToken'] ?>">
                <div class="content_threads">
                    <p>【タイトル】</p>
                    <p>csrf</p>
                    <p><br>【内容】</p>
                    <p>csrf</p>
                </div>
                <p><input class="btn blue_btn" type="submit" name="cfm" value="スレッドを作成する"></p>
            </form>        
        </div>




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