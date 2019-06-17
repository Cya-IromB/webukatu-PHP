<?php
  error_reporting(E_ALL); //E_STRICTレベル以外のエラーを報告
  ini_set('display_errors','On'); //エラー表示させるかどうか

  //POST送信されている
  if(!empty($_POST)){
    /*
    想定される送信内容:
    1.email
    2.password
    */

    $email = $_POST['email'];
    $password = $_POST['password'];

    //DB接続準備
    $dsn = 'mysql:dbname=oredb;host=localhost;charset=utf8';
    $user = 'root';
    $adminPassword = 'yamazato27';
    $options = array(
      //ATTRのエラーモード:SQL実行時に例外をスローする
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

      //ATTRのデフォルトのフェッチモード:連想配列形式に設定
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

      //バッファードクエリ(結果セットを一度に取得することでサーバー負荷を軽減する)
      //SELECTで得た結果に対してもrowCountメソッドを使えるようにする。
      PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
    );

    //PDOオブジェクトを生成し、データベースへ接続する。
    $dbh = new PDO($dsn, $user, $adminPassword, $options);

    //SQL
    $stmt = $dbh->prepare('SELECT * FROM users WHERE email = :email AND password = :password');

    //プレースホルダ
    $stmt->execute(array(':email' => $email, 'password' => $password));

    $result = 0;

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!empty($result)){
      session_start();
      $_SESSION['login'] = true;
      header('location:mypage.php');
    }
  }
?>

<!DOCTYPE html>
<html lang="ja">

  <head>
    <meta charset="UTF-8">
    <title>ログインするッ！！</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
  </head>

  <body>
    <h1>ログイン</h1>

    <form action="" method="POST">
      <label for="email">
        <p>E.Mail</p>
        <input type="text" name="email" id="email" value="<?php if(!empty($_POST)) echo $_POST['email']; ?>">
      </label>

      <label for="password">
        <p>Password</p>
        <input type="password" name="password" id="password" placeholder="パスワード" value="<?php if(!empty($_POST)) echo $_POST['password']; ?>">
      </label>

      <input type="submit" value="送信">
    </form>

    <a href="mypage.php">マイページへ</a>

  </body>
</html>
