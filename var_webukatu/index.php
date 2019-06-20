<?php
  error_reporting(E_ALL); //E_STRICTレベル以外のエラーを報告
  ini_set('display_errors','On'); //エラー表示させるかどうか

  const ER_EMPTY = '必須入力です！ ';
  const ER_NOT_EMAIL = 'email形式ではありません！ ';
  const ER_PASS_BAD_RETYPE = 'パスワードの再入力が一致しません！ ';
  const ER_PASS_NOT_HANKAKU = 'パスワードで使える文字は半角英数字のみです！ ';
  const ER_PASS_SHORT = 'パスワードは最低でも6文字以上必要です！ ';


  $error = array(
    'email' => '',
    'password' => '',
    'retype' => '',
  );

  //POST送信されている
  if(!empty($_POST)){
    /*エラーチェック内容
    １POST送信されているかチェック

    ２フォーム入力漏れが無いかチェック
    ３emailフォームはemail形式かチェック
    ４パスワードと再入力フォームは一致しているか
    ５パスワードは規定通りか
        半角英数字限定
        6文字以上のみ
    */

    //入力漏れ
    $error['email'] = emptyForm($_POST['email'], $error['email'], ER_EMPTY);
    $error['password'] = emptyForm($_POST['password'], $error['password'], ER_EMPTY);
    $error['retype'] = emptyForm($_POST['password_retype'], $error['retype'], ER_EMPTY);

    //email形式か
    $error['email'] = valid_email($_POST['email'], $error['email'], ER_NOT_EMAIL);

    //パスワードと再入力フォームは一致しているか
    $error['retype'] = equals_pw_retype($_POST['password'], $_POST['password_retype'], $error['email'], ER_PASS_BAD_RETYPE);

    //パスワードは規定通りか
    $error['password'] = valid_password($_POST['password'], $error['password'], ER_PASS_NOT_HANKAKU, ER_PASS_SHORT);


    //ひとつでもエラーがあるかどうかチェック
    $isSuccess = true;
    foreach ($error as $key => $value) {
      if($isSuccess === false || !empty($value)){
        $isSuccess = false;
      }
    }

    //すべて大丈夫なら遷移
    if($isSuccess){
      //DB接続準備
      $dsn = 'mysql:dbname=oredb;host=localhost;charset=utf8mb4';
      $user = 'root';
      $adminPassword = 'yamazato27';
      $options = array(
        //ATTRのエラーモード:SQL実行時に例外をスローする
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        //ATTRのデフォルトのフェッチモード:連想配列形式に設定
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        //バッファードクエリ(一度に結果セットを一度に取得することでサーバー負荷を軽減する)
        //SELECTで得た結果に対してもrowCountメソッドを使えるようにする。
        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
      );

      //PDOオブジェクトの生成(DBへの接続)
      $dbh = new PDO($dsn, $user, $adminPassword, $options);

      //SQL文(クエリ作成)を格納
      $stmt = $dbh->prepare('INSERT INTO users (email, password, login_time) VALUES (:email,:password,:login_time)');

      //プレースホルダに値のセットとSQL文実行
      $stmt->execute(array(':email' => $_POST['email'], ':password' => $_POST['password'], ':login_time' => date('Y-m-d h:i:s')));

      header("Location:mypage.php");
    }
  }

  //functions
  function emptyForm($targetPost, $targetError, $ER_EMP){
    if(empty($targetPost)){
      $targetError = $targetError.$ER_EMP;
    }
    return $targetError;
  }
  function valid_email($targetPost, $targetError, $ER_EMAIL){
    if(!preg_match('|^[0-9a-z_./?-]+@([0-9a-z-]+\.)+[0-9a-z-]+$|', $targetPost)){
      $targetError = $targetError.$ER_EMAIL;
    }
    return $targetError;
  }
  function equals_pw_retype($postPw, $postRetype, $targetError, $ER_RETYPE){
    if($postPw !== $postRetype){
      $targetError = $targetError.$ER_RETYPE;
    }
    return $targetError;
  }
  function valid_password($postPw, $targetError, $ER_HANKAKU, $ER_SHORT){
    //半角
    if(!preg_match("/[a-zA-Z0-9]+$/", $postPw)){
      $targetError = $targetError.$ER_HANKAKU;
    }
    //n文字以上
    if(mb_strlen($postPw) < 6){
      $targetError = $targetError.$ER_SHORT;
    }

    return $targetError;
  }
?>

<!DOCTYPE html>
<html lang="ja">

  <head>
    <meta charset="UTF-8">
    <title>ユーザー登録</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
  </head>

  <body>
    <h1>ユーザー登録</h1>

    <form action="" method="POST">
      <label for="email">
        <p>email: <span class="description">例: xxxx@gmail.com </span><span class="js-error" id="er-email"><?php if(!empty($_POST)) echo $error['email']; ?></span>
        </p>
        <input type="text" name="email" id="email" value="<?php if(!empty($_POST)) echo $_POST['email']; ?>">
      </label>

      <label for="password">
        <p>password <span class="description">半角英数字かつ6文字以上 </span>
        <span class="js-error" id="er-password"><?php if(!empty($_POST)) echo $error['password']; ?></span></p>
        <input type="password" name="password" id="password" placeholder="パスワード" value="<?php if(!empty($_POST)) echo $_POST['password']; ?>">
      </label>

      <label for="password-retype">
        <p>password(再入力) <span class="description">*確認用です </span>
        <span class="js-error" id="er-password-retype"><?php if(!empty($_POST)) echo $error['retype']; ?></span></p>
        <input type="password" name="password_retype" id="password-retype" placeholder="パスワード再入力" value="<?php if(!empty($_POST)) echo $_POST['password_retype']; ?>">
      </label>

      <input type="submit" value="送信">
    </form>

    <a href="mypage.php">マイページへ</a>

  </body>
</html>
