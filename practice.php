<?php
require_once('fanctions.php');

// $c = 1 + 1;
// if (is_bool($c == 2)) {
//     echo is_bool($c == 2);
//     return true;
// }


// 使えるフォーマット
date_default_timezone_set('Asia/Tokyo');
date('Y/m/d G:i:s');

// 保管用
$stmt = $dbh->prepare('SELECT id FROM threads ORDER BY id DESC LIMIT 1');
$stmt->execute();
$newestID = $stmt->fetch(PDO::FETCH_COLUMN);
$pageCount = ceil($newestID / 10);

for ($count = 1; $count <= $pageCount; $count++) {
    $pages[] = $count;
}

foreach ($pages as $page) {
    echo '<a href=toppage.' . $page . 'php>' . $page . '</a>　';
}

?>


<!-- 以下htmlフォーマット -->

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="sample text">
    <link rel="stylesheet" type="text/css" href="xxxxx.css">
    <title>タイトル</title>
</head>

<body>
    <div class="header">

    </div>
    <div class="main">

    </div>
    <div class="footer">

    </div>
</body>

</html>