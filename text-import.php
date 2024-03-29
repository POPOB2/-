<?php
include_once "base.php";

if(isset($_FILES['file']) && $_FILES['file']['error']==0){
    echo $_FILES['file']['tmp_name']; // 檔案上傳後的暫存位置

    // fopen()==開啟檔案, 參數1:欲開啟的檔案、參數2:開啟的模式(詳見官方文件)
    $file=fopen($_FILES['file']['tmp_name'],'r'); // 'r'==read模式==僅讀取出內容
    // fgets()==獲取資料, 參數1:欲獲取的資料、參數2:獲取的行數
        // $str=fgets($file); 
        // echo $str;

    
    fgets($file); // 若碰到第一行是標題, 欲略過第一行不存入資料庫, 可在迴圈外空執行一次fgets();
    while(!feof($file)){ // feof()==執行到最後一行, 沒東西時停止, 此用!代表未到最後一行就一直執行迴圈
        $str=fgets($file); // 獲取資料
        $col=explode(",",$str);
        dd($col);
        exit();
    }
    

    // 




    // $sql="INSERT INTO `upload`(`name`,``,``,``)VALUES('')";
    // $pdo->exec($sql);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>文字檔案匯入</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1 class="header">文字檔案匯入練習</h1>
    <!-- 建立檔案上傳機制 -->
    <form id="uploadForm" action="?" method="post" enctype="multipart/form-data">
        <div>
            選擇檔案:<input type="file" name="file">
        </div>

        <input type="submit" value="上傳">
    </form>
    <!-- 讀出匯入完成的資料 -->
</body>
</html>