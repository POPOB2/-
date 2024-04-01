<?php
include_once "base.php";
/*
1. 建立資料庫及資料表
2. 建立上傳圖案機制
3. 取得圖檔資源
4. 進行圖形處理
   ->圖形縮放
   ->圖形加邊框
   ->圖形驗證碼
5. 輸出檔案
*/
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>圖形處理練習</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1 class="header">圖形處理練習</h1>
    <!-- 建立檔案上傳機制 -->
    <form id="uploadForm" action="?" method="post" enctype="multipart/form-data">
        <div>
            檔案上傳 : <input type="file" name="file">
        </div>
        <input type="submit" value="上傳">
        <div>
            <!-- 提供表單, 填寫檔案縮放比例值 -->
            縮放比例 : <input type="number" name="percent" step="any" > <!-- step="any" 為浮點數(float) -->
        </div>
    </form>


<?php

if(isset($_FILES['file']['tmp_name'])){
    move_uploaded_file($_FILES['file']['tmp_name'],'./upload/'.$_FILES['file']['name']);
    echo "<img src='./upload/{$_FILES['file']['name']}'>";
}


?>    
    <!-- 縮放圖形 -->
<?php
/* 確認有圖形上傳, 才去執行縮放圖形的動作 */
if(isset($_FILES['file']['tmp_name'])){
    $filename="./upload/{$_FILES['file']['name']}";
    $percent=isset($_POST['percent'])?$_POST['percent']:0.5; // 若表單有提供值用該值, 否則縮放比例用一半(0.5)

    // dd(getimagesize($filename)); // getimagesize()==讀取並以陣列提供來源圖片的寬、高、類型等資訊, 參數1:圖片位置+檔名

    $src_width=getimagesize($filename)['0']; // 來源圖片的寬值
    $src_height=getimagesize($filename)['1']; // 來源圖片的高值

    // $目的圖形資源的寬高=$來源圖形資源的寬高*$設置的百分比
    $dst_width=$src_width*$percent; 
    $dst_height=$src_height*$percent;
    $dst_img=imagecreatetruecolor($dst_width,$dst_height); // imagecreatetruecolor()==設置 目的圖形資源, 參數1:寬、參數2:高

    // imagecreatefromjpeg()==來源的圖形資源, 參數1:原圖位置
    $src_img=imagecreatefromjpeg($filename);

    /* imagecopyresampled()==執行渲染 將原圖渲染至容器, 參數1:目的圖形資源、參數2:來源圖形資源、參數3:來源圖起始點x軸、參數4:來源圖起始點y軸
       參數5:目的圖起始點x軸、參數5:目的圖起始點y軸、參數7:目的圖形資源寬度、參數8:目的圖形資源高度、參數9:來源圖形資源寬度、參數10:來源圖形資源高度 */
    imagecopyresampled($dst_img, $src_img,0,0,0,0,$dst_width,$dst_height,$src_width,$src_height);

    // imagejpeg()==輸出圖形資源, 參數1:輸出的圖、參數2:品質數值
    imagejpeg($dst_img,"./upload/result.jpg",100);

    // 將上述圖片相關的操作從記憶體移除, 避免佔用資源
    imagedestroy($dst_img);
    imagedestroy($src_img);

}



?>
    <h2>縮放後的圖形</h2>
    <img src="./upload/result.jpg" alt="">
    <!-- 圖形加邊框 -->


    <!-- 產生圖形驗證碼 -->
</body>
</html>