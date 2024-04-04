<?php
include_once "base.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>文字圖形處理練習</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1 class="header">文字圖形處理練習</h1>
    <!-- 建立檔案上傳機制 -->
    <form id="uploadForm" action="?" method="post" enctype="multipart/form-data">
        <div>
            檔案上傳 : <input type="text" name="string" value="ABC">
        </div>
        <input type="submit" value="上傳">
        <div>
            文字尺寸 : <input type="number" name="size" step="1" value="9">
        </div>
        <div>
            <select name="color" id="">
                <option value="red">紅</option>
                <option value="green">綠</option>
                <option value="blue">藍</option>
            </select>
        </div>
    </form>
 

<!-- ===============================================================產生圖形驗證碼=============================================================== -->
<?php
if(isset($_POST['string'])){
    $color=$_POST['color'];

    $dst_width=300; 
    $dst_height=200;
    $dst_img=imagecreatetruecolor($dst_width,$dst_height); // 產生圖片

    

    // imagecolorallocate()==宣告圖片顏色
    $white=imagecolorallocate($dst_img,255,255,255);
    $black=imagecolorallocate($dst_img,0,0,0);
    $red=imagecolorallocate($dst_img,255,0,0); 
    $green=imagecolorallocate($dst_img,0,255,0); 
    $blue=imagecolorallocate($dst_img,0,0,255); 

    imagefill($dst_img,0,0,$white); // 渲染圖片, 參數1:圖片、參數2&3:位置、參數4:顏色

    // imagestring()==將文字填入圖片, 參數1:使用的圖片、參數2:填入文字的尺寸、參數3:起點x軸、參數4:起點y軸、參數5:填入的文字、參數6:顏色
    imagestring($dst_img,$_POST['size'],20,20,$_POST['string'],$$color); // 用$$獲取上方顏色
    
    imagejpeg($dst_img,"./upload/text.jpg",100); // 輸出圖片
    imagedestroy($dst_img); // 從記憶體移除圖片相關的操作

}

?>
    <div style="width:500px; margin:auto;">
        <h2>加入文字後的圖形</h2>
        <img src="./upload/text.jpg" alt="">
    </div>
</body>
</html>