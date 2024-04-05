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
            <!-- 4~8字 -->
            長度 : <input type="number" name="length" value="<?=rand(4,8);?>">
        </div>
        <div>
            文字尺寸 : <input type="number" name="size" step="1" value="24">
        </div>
        <div>
            <select name="color" id="">
                <option value="red">紅</option>
                <option value="green">綠</option>
                <option value="blue">藍</option>
            </select>
        </div>
        <input type="submit" value="產生驗證碼">
    </form>
 

<!-- ===============================================================產生圖形驗證碼=============================================================== -->
<?php
if(isset($_POST)){
    // $gstr=rand(10000,99999); // rand==設定範圍, 產生範圍內的隨機數字, 此例區間10000~99999
    // $gstr=chr(rand(65,90)); // chr()==依數字產生ascii的字母, 65==A、90==Z, 搭配rand(65,90)==A~Z
    // $gstr=chr(rand(97,122)); // 97~122=小寫a~z

    $gstr="";
    for($i=0; $i<$_POST['length']; $i++){
        $type=rand(1,3); // 隨機選1~3, 並用switch產生對應的內容
        switch($type){
            case 1: // 數字
                $gstr.=rand(1,3);
            break;
            case 2: // 大寫
                $gstr.=chr(rand(65,90));
            break;
            case 3: // 小寫
                $gstr.=chr(rand(97,122));
            break;
        }
        
    }
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
    // imagestring($dst_img,$_POST['size'],20,20,$_POST['string'],$$color); // 用$$獲取上方顏色

    // 評估文字資訊
    $text_info=imagettfbbox($_POST['size'],0,realpath('./font/arial.ttf'),$gstr); // imagettfbbox()==以陣列提供產生的圖片文字, 其大小、xy位置等資訊
    dd($text_info);
    $dst_x=0-$text_info[6];// 用產出的文字圖片, 其左上xy軸作為判斷, 供文字產出時以該xy座標使文字出現在靠齊位置
    $dst_y=0-$text_info[7];

    // imagettftext($dst_img,24,0,10,10,$blue,'./font/arial.ttf','ABCDE');
    // realpath()==自動從根目錄找到目前該檔案位置(看似相對路徑但實為絕對路徑)
    imagettftext($dst_img,$_POST['size'],0,$dst_x,$dst_y,$$color,realpath('./font/arial.ttf'),$gstr); // imagettftext()==於圖像上繪製文字, 參數1:使用圖像、參數2345:
    
    imagejpeg($dst_img,"./upload/text.jpg",100); // 輸出圖片
    imagedestroy($dst_img); // 從記憶體移除圖片相關的操作

}

?>
    <div style="width:500px; margin:auto;">
        <h2>加入文字後的圖形</h2>
        <img src="./upload/text.jpg" alt="" style="border:2px solid black">
    </div>
</body>
</html>