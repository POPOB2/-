<?php
include_once "base.php";

if(isset($_FILES['file']) && $_FILES['file']['error']==0){
    $type=$_FILES['file']['type'];
    $sub=explode('.',$_FILES['file']['name'])[1];
    $name=date('Ymdhis').".".$sub;

    /* 若僅是更新庫的資訊並上傳新檔案, 未刪舊檔案, 未導致主機內的檔案越來越多 */
    // 先撈取舊檔案存入$origin_file
    $origin_file=$pdo->query("SELECT * FROM `upload` WHERE `id`='{$_GET['id']}'")->fetch();

    // 新檔案新增至資料夾後
    move_uploaded_file($_FILES['file']['tmp_name'],'./upload/'.$name);

    // 將舊檔案移除
    unlink("./upload/".$origin_file['name']);

    // 更新庫的資料為新上傳的資料
    $sql="UPDATE `upload` SET `name`='{$name}',`type`='{$type}',`collections`='{$_POST['collections']}' WHERE `id`='{$_GET['id']}'";
    $pdo->exec($sql);
    header('location:manage.php');
}else{

}

?>






<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>更換檔案</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- action == 要將該form表單提供的資料送至何處, 使用?代表送至當前頁, 此例就是update_file.php -->
    <!-- <form id="uploadForm" action="?" method="post" enctype="multipart/form-data"> -->

    <!-- 注意:form表單的action=?,
        ?後面為跳轉頁面時帶入的$_GET值, 
        若只寫? 則再次執行該頁面動作紀錄的值(僅暫存,不會存入$_GET)
        當撰寫"更新頁面"等需帶入$_GET值進行應用的功能, 需寫上欲帶入的GET值供跳轉後傳遞作後續使用
    -->
    <form id="uploadForm" action="?id=<?=$_GET['id'];?>" method="post" enctype="multipart/form-data">
    
    <div>
        選擇檔案 : <input type="file" name="file">
    </div>
    <select name="collections">
        <option value="圖片">圖片</option>
        <option value="文件">文件</option>
        <option value="試算表">試算表</option>
    </select>
    <input type="submit" value="上傳">
    </form>
</body>
</html>