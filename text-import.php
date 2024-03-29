<?php
include_once "base.php";

if(isset($_FILES['file']) && $_FILES['file']['error']==0){
    echo $_FILES['file']['tmp_name']; // 檔案上傳後的暫存位置

    // fopen()==開啟檔案使其可讀取內容, 參數1:欲開啟的檔案、參數2:開啟的模式(詳見官方文件)
    $file=fopen($_FILES['file']['tmp_name'],'r'); // 'r'==read模式==僅讀取出內容
    // fgets()==獲取資料, 參數1:欲獲取的資料、參數2:獲取的行數
        // $str=fgets($file); 
        // echo $str;

    
    fgets($file); // 若碰到第一行是標題, 欲略過第一行不存入資料庫, 可在迴圈外空執行一次fgets();
    while(!feof($file)){ // feof()==執行到最後一行, 沒東西時停止, 此用!代表未到最後一行就一直執行迴圈
        $str=fgets($file); // 獲取資料
        $col=explode(",",$str); // 用explode()將獲取的資料內容, 拆開微陣列
        // dd($col);
        // exit();
        
        /* 為避免上傳檔案有空字串, 使空字串也被存入資料庫, 需多一個判斷, 依需求使用.. */
        // if(!empty($col)){ // $col不為空(有值)才執行, 任一key有值就會執行, 可能僅0有值就執行, 空白時0可能會有值
        // if(count($col)>0){ // 總數大於0就執行, 可以避過[0]有值就執行, 但除了0任一key有值就執行, 存入資料可能不完整
        if(count($col)==6){ // 確定[key]有多少的情況下, 直接設定值須為key總數, 可確保所有欄位有值的情況下才執行
            $data=['duration'=>$col[0], // 已從dd()得知$col得出陣列, 依序將陣列key改為字串(用於之後存入資料庫)
            'tempe'=>$col[1],
            'humidity'=>$col[2],
            'daylight'=>$col[3],
            'preci'=>$col[4],
            'preci_days'=>$col[5],
        ];
        save('temperature',$data); // 參數1:存入的table、參數2:存入的資料 
        }
    }
    
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