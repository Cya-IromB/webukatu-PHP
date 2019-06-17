<?php
  error_reporting(E_ALL); //E_STRICTレベル以外のエラーを報告
  ini_set('display_errors','On'); //エラー表示させるかどうか

  session_start();
?>

<!DOCTYPE html>
<html lang="ja">

  <head>
    <meta charset="UTF-8">
    <title>マイページ</title>
    <link rel="stylesheet" type="text/css" href="style.css">
  </head>

  <body>
    <?php if(!empty($_SESSION['login'])): ?>
      <section>
        <p>あなたのメールアドレスは <?php echo 'あっはん'; ?> です。 </p>
        <p>あなたのパスワードは <?php echo 'うっふん'; ?> です。</p>
        <a href="index.php">ユーザー登録へ戻る</a>
      </section>

    <?php else: ?>
      <p>ログインしていなければ見ることができません。</p>
    <?php endif; ?>
  </body>
</html>
