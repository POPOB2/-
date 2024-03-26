<?php
include_once "base.php";
dd($_FILES); // 顯示當下上傳檔案的資訊
if(isset($_FILES['img']) && $_FILES['img']['error']==0){
    echo $_FILES['img']['name'].'<br>'; // 若正確, 顯示其資訊
    echo $_FILES['img']['type'].'<br>';
    echo $_FILES['img']['tmp_name'].'<br>';
    // 若直接顯示圖片, 因網頁無法解析".tmp"檔, 而無法顯示圖片, 但其確實為圖片
        // echo "<img src='{$_FILES['img']['tmp_name']}'>"; 

    // 使用該fun移動檔案使其為非tmp檔, 參數1:檔案原位置, 參數2:移至何處, 檔名為何(可自行調整)
    move_uploaded_file($_FILES['img']['tmp_name'],"./upload/".$_FILES['img']['name']);  // 移至同層並命名為原檔名
    echo "<img src='./{$_FILES['img']['name']}' style='width:100px';>"; // 利用轉移位置後的路徑與檔名顯示圖片
}else{
    echo "<hr><h3>上傳錯誤, 無法顯示圖片</h3><hr>";
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>檔案上傳</title>
</head>
<body>
    <h1>檔案上傳練習</h1>
    <!-- 建立表單及設定編碼 -->
    <!-- action == 要將該form表單提供的資料送至何處, 使用?代表送至當前頁, 此例就是upload.php -->
    <form action="?" method="post" enctype="multipart/form-data">  <!-- 上傳的固定格式 -->
        <div>
            選擇圖片<input type="file" name="img">
        </div>
        <input type="submit" value="上傳">
    </form>


    <!-- 建立一個連結來查看上船後的圖檔 -->


</body>
</html>