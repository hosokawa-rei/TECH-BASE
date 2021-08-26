<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>投稿フォーム</title>
</head>
<body>
    <?php
        //データベースと接続
        $dsn ='DATABASE-NAME';
        $user = 'USER-NAME';
        $password = 'PASSWORD';
        $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_WARNING));
    ?>
    <?php
        //データベース内のテーブルを作成
        $sql = "CREATE TABLE IF NOT EXISTS myboard"
            ."("
            ."id INT(11) AUTO_INCREMENT PRIMARY KEY,"//11桁までの整数ID
            ."name char(32),"//32文字までの名前
            ."comment TEXT,"//20億字までのテキスト文
            ."postdate datetime,"//日付
            ."password char(32)"//パスワード
            .");";
        $stmt = $pdo->query($sql);
    ?>
    <?php
        //編集時に原文がフォーム画面に表示されるための
        //変数の定義
        $editid = "";
        $editname = "";
        $editcomment = "";
        $editpassword = "";
        //編集ボタンが押された時
        if(!empty($_POST["editid"])){
            //編集対象番号を変数に代入
            $id = $_POST['editid'];
            //編集する投稿の原文を抽出・表示
            //WHEREで絞り込み
            //idにプレースホルダーを用いる
            $sql = 'SELECT * FROM myboard WHERE id=:id';
            //prepare構文
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id',$id, PDO::PARAM_INT);
            $stmt->execute();
            $results = $stmt->fetchAll();
                foreach($results as $row){
                    if($_POST['editpass'] == $row['password']){
                        //パスワードが一致した場合
                        $editid = $row['id'];//$rowの中にはテーブルのカラム名が入る
                        $editname = $row['name'];
                        $editcomment = $row['comment'];
                        $editpassword = $row['password'];
                    }else{
                        //パスワードが不一致の場合
                        echo "パスワードが違います";
                    }
                }
        }
    ?>
    <?php
        //名前・コメント・パスワードの全てが入力されている時
        if(!empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["password"])){
            if(empty($_POST["edit_hidden"])){
                //新規投稿時
                //名前・コメント・パスワードをINSERT
                $sql = $pdo -> prepare("INSERT INTO myboard(name, comment, postdate, password) VALUES (:name, :comment, :postdate, :password)");
                $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                $sql -> bindParam(':postdate', $postdate, PDO::PARAM_STR);
                $sql -> bindParam(':password', $password, PDO::PARAM_STR);
                $name = $_POST["name"];
                $comment = $_POST["comment"];
                $postdate = date("Y/m/d H:i:s");
                $password = $_POST["password"];
                $sql -> execute();                
            }else{
                //編集時
                //編集対象番号の投稿のみを変更
                $id = $_POST["edit_hidden"];
                $name = $_POST["name"];
                $comment = $_POST["comment"];
                $postdate = date("Y/m/d H:i:s");
                $password = $_POST["password"];
                $sql = 'UPDATE myboard SET name=:name,comment=:comment,postdate=:postdate,password=:password WHERE id=:id';
                $stmt = $pdo->prepare($sql);
                //プレースホルダー
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                $stmt->bindParam(':postdate', $postdate, PDO::PARAM_STR);
                $stmt->bindParam(':password', $password, PDO::PARAM_STR);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
            }
        }
    ?>
    <?php
        //削除機能
        if(!empty($_POST['delid'])){
            //削除対象番号を定義
            $id = $_POST['delid'];
            //削除する投稿の原文を抽出・表示
            //WHEREで絞り込み
            //idにプレースホルダーを用いる
                        $sql = 'SELECT * FROM myboard WHERE id=:id';
            //prepare構文
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id',$id, PDO::PARAM_INT);
            $stmt->execute();
            $results = $stmt->fetchAll();
                foreach($results as $row){
                    if($_POST['delpass'] == $row['password']){
                        //パスワードが一致した場合
                        $sql = 'delete from myboard WHERE id=:id';
                        //prepare構文
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(':id',$id, PDO::PARAM_INT);
                        $stmt->execute();
                    }else{
                        //パスワードが不一致の場合
                        echo "パスワードが違います";
                    }
                }
        }
    ?>
    <form acton="#" method="post">
        <p>名前：<br>
            <input type="text" name="name" value="<?php echo $editname;?>"></p>
        <p>コメント：<br>
            <input type="text" name="comment" value="<?php echo $editcomment;?>"></p>
        <!-- 新規投稿か編集かを判別するhidden -->
        <input type="hidden" name="edit_hidden" value="<?php echo $editid;?>">
        <p>パスワード：<br>
            <input type="password" name="password" value="<?php echo $editpassword;?>"></p>
        <p>
            <input type="submit" value="送信"><input type="reset" value="リセット"></P>
        <!--削除ボタン-->
        <p>削除対象番号：<br>
            <input type="number" name="delid"></p>
        <p>パスワード：<br>
            <input type="password" name="delpass"></p>
        <p>
            <input type="submit" name="delete" value="削除"></p>
        <!--編集ボタン-->
        <p>編集対象番号：<br>
            <input type="number" name="editid"></p>
        <p>パスワード：<br>
            <input type="password" name="editpass"></p>
        <p>
            <input type="submit" name="edit" value="編集"></p>
    </form>
    <?php
        //ブラウザ上に表示する
        $dsn ='mysql:dbname=tb230273db;host=localhost';
        $user = 'tb-230273';
        $password = 'stRsTwbdxQ';
        $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_WARNING));
        //SELECT構文
        $sql = 'SELECT * FROM myboard';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
            foreach($results as$row){
                //$rowの中にはテーブルのカラム名が入る
                echo $row['id'].',';
                echo $row['name'].',';
                echo $row['comment'].',';
                echo $row['postdate'].'<br>';
                echo "<hr>";
            }        
    ?>
</body>
</html>