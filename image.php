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
            縮放比例 : <input type="number" name="percent" step="any"> <!-- step="any" 為浮點數(float) -->
        </div>
        <div>
            <select name="color" id="">
                <option value="red">紅</option>
                <option value="green">綠</option>
                <option value="blue">藍</option>
            </select>
        </div>
    </form>


<?php

if(isset($_FILES['file']['tmp_name'])){
    move_uploaded_file($_FILES['file']['tmp_name'],'./upload/'.$_FILES['file']['name']);
    echo "<img src='./upload/{$_FILES['file']['name']}'>";
}


?>    
    <!-- =================================================================縮放圖形================================================================= -->
<?php
/* 確認有圖形上傳, 才去執行縮放圖形的動作 */
if(isset($_FILES['file']['tmp_name'])){
    $filename="./upload/{$_FILES['file']['name']}";
    // $percent=$_POST['percent']??0.5; // 狀況:若未填值傳的是null會被解析為"字串", 若type非int, 下方用乘法計算時會報錯
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
    <!-- =============================================================圖形加邊框============================================================= -->
    <?php
    /* 確認有圖形上傳, 才去執行縮放圖形的動作 */
if(isset($_FILES['file']['tmp_name'])){
    $filename="./upload/{$_FILES['file']['name']}";
    $percent=0.5; // 縮放比例固定用一半(0.5)
    $border=2; // 邊框距離圖片的間隔寬高
    $color=$_POST['color'];

    // dd(getimagesize($filename)); // getimagesize()==讀取並以陣列提供來源圖片的寬、高、類型等資訊, 參數1:圖片位置+檔名
    $src_width=getimagesize($filename)['0']; // 來源圖片的寬值
    $src_height=getimagesize($filename)['1']; // 來源圖片的高值

    // $寬高用於產生目的圖片資源
    $dst_width=$src_width*$percent; 
    $dst_height=$src_height*$percent; 
    // 畫出圖片
    $dst_img=imagecreatetruecolor($dst_width,$dst_height); 

    // imagecolorallocate()==可以將預設為黑的底色, 以RGB的方式更改定義的顏色(僅定義 未上色)
    $red=imagecolorallocate($dst_img,255,0,0); 
    $green=imagecolorallocate($dst_img,0,255,0); 
    $blue=imagecolorallocate($dst_img,0,0,255); 
    

// switch($color){
//     case 'red':
//         imagefill($dst_img,0,0,$red);
//     break;
//     case 'green':
//         imagefill($dst_img,0,0,$green);
//     break;
//     case 'blue':
//         imagefill($dst_img,0,0,$blue);
//     break;
//     default:
//         $col="0,0,0";
// }


    // 用兩個$$可使$color帶入option的結果選擇已設置的$顏色($red、$green、$blue)
    imagefill($dst_img,0,0,$$color); // imagefill()==上色, 參數1:欲上色的圖片底圖、參數2:上色位置x軸、參數3:上色位置y軸、參數4:已定義的顏色
    $src_img=imagecreatefromjpeg($filename);

    // 再次計算, 將邊框寬高($border)放入, 覆蓋掉原目的寬高, 用於下方渲染
    $dst_width=$src_width*$percent - ($border*2); // 扣掉間隔寬高*2(左右兩邊)
    $dst_height=$src_height*$percent - ($border*2); // 扣掉間隔寬高*2(上下兩邊)


    /* imagecopyresampled()==執行渲染 將原圖渲染至容器, 參數1:目的圖形資源、參數2:來源圖形資源、參數3:來源圖起始點x軸、參數4:來源圖起始點y軸
       參數5:目的圖起始點x軸、參數5:目的圖起始點y軸、參數7:目的圖形資源寬度、參數8:目的圖形資源高度、參數9:來源圖形資源寬度、參數10:來源圖形資源高度 */
    imagecopyresampled($dst_img, $src_img,$border,$border,0,0,$dst_width,$dst_height,$src_width,$src_height);

    // imagejpeg()==輸出圖形資源, 參數1:輸出的圖、參數2:品質數值
    imagejpeg($dst_img,"./upload/border.jpg",100);

    // 將上述圖片相關的操作從記憶體移除, 避免佔用資源
    imagedestroy($dst_img);
    imagedestroy($src_img);

}
?>
    <h2>加邊框後的圖形</h2>
    <img src="./upload/border.jpg" alt="">

    <!-- ===============================================================產生圖形驗證碼=============================================================== -->
</body>
</html>