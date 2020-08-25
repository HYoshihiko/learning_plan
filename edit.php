<?php

require_once('config.php');
require_once('functions.php');

// 受け取ったレコードのID
$id = $_GET['id'];

// データベースへの接続
$dbh = connectDb();

// SQLの準備と実行
$sql = 'SELECT * FROM plans WHERE id = :id';
$stmt = $dbh->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();

// 結果の取得
$plan = $stmt->fetch(PDO::FETCH_ASSOC);

// タスクの編集
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 受け取ったデータ
    $title = $_POST['title'];
    $due_date = $_POST['due_date'];

    // エラーチェック用の配列
    $errors = [];

    // バリデーション
    if ($title == '') {
        $errors['title'] = '・学習内容を入力してください';
    }
    if ($due_date == '') {
        $errors['due_date'] = '・期限日を入力してください';
    }

    if (($title == $plan['title']) && ($due_date == $plan['due_date'])) {
        $errors['title'] = '・変更内容がありません';
    }

    // エラーが1つもなければレコードを更新
    if (!$errors) {

        $sql = 'UPDATE plans SET title = :title WHERE id = :id';
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        header('Location: index.php');
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>編集画面</title>
</head>

<body>
    <h2>編集</h2>
    <div>
        <form action="" method="post">
            <label for="content">学習内容:</label>
            <input type="text" name="title" value="<?= h($plan['title']) ?>"><br>
            <label for="due">期限日:</label>
            <input type="date" name="due_date" value="<?= h($plan['due_date']) ?>">
            <input type="submit" value="編集">
        </form>
        <ul>
            <li style="color:red; list-style:none;"><?= h($errors['title']) ?></li>
            <li style="color:red; list-style:none;"><?= h($errors['due_date']) ?></li>
        </ul>
    </div>
    <div>
        <a href="index.php">戻る</a>
    </div>
</body>

</html>