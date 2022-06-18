<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="sample text">
    <link rel="stylesheet" type="text/css" href="board.css">
    <title>アカウント新規登録に使える文字</title>
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
            <h2 class="head_title">アカウント新規登録に使える文字</h2>
        </div>

        <div class="content">
            <h4>ユーザー名に使える文字</h4>
            <ul>
                <li>半角英大文字（A ~ Z）</li>
                <li>半角英小文字（a ~ z）</li>
                <li>半角数字（0 ~ 9）</li>
                <li>半角アンダーバー（ _ ）</li>
            </ul>
            <p>※他のユーザーと同じ名前は使用できません。</p>
            <p>※先頭に _（アンダーバー）は使用できません。</p>
            <p>※空白（スペース）は使用できません。</p><br>

            <h4>パスワードに使える文字</h4>
            <ul>
                <li>半角英大文字（A ~ Z）</li>
                <li>半角英小文字（a ~ z）</li>
                <li>半角数字（0 ~ 9）</li>
                <li>半角記号 右記32種類 ! " # $ % & ' ( ) * + , - . / : ; <=> ? @ [ ￥ ] ^ _ ` { | } ~</li>
            </ul>
            <p>※先頭に記号は使用できません。</p>
            <p>※空白（スペース）は使用できません。</p>
        </div>

        <div class="bottom">
            <a href="account_reg.php">
                <button class="btn" type="button">前の画面に戻る</button>
            </a>
        </div>

    </div>


</body>

</html>