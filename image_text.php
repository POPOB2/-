<?php
include_once "base.php";
/*
1. 驗證碼
2. bbox()取字串寬高
3. 計算底圖邊框的寬高
4. 產生畫布
5. 畫上文字
*/
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

    // imagestring()==將文字填入圖片, 參數1:使用的圖片、參數2:填入文字的尺寸、參數3:起點x軸、參數4:起點y軸、參數5:填入的文字、參數6:顏色
    // imagestring($dst_img,$_POST['size'],20,20,$_POST['string'],$$color); // 用$$獲取上方顏色

    // 評估文字資訊
    $text_info=imagettfbbox($_POST['size'],0,realpath('./font/arial.ttf'),$gstr); // imagettfbbox()==以陣列提供產生的圖片文字, 其大小、xy位置等資訊
    dd($text_info);

    // 用產出的文字圖片, 其左上xy軸作為判斷, 供文字產出時以該xy座標使文字出現在靠齊位置
    $dst_x=0-$text_info[6];
    $dst_y=0-$text_info[7];

    // 從文字圖片尺寸中取最大減最小的寬高=文字圖片尺寸, 在該文字圖片兩側加上30px的邊框
    $arrayW=[$text_info[0],$text_info[2],$text_info[4],$text_info[6]];
    $arrayH=[$text_info[1],$text_info[3],$text_info[5],$text_info[7]];
    $dst_w=max($arrayW)-min($arrayW);
    $dst_h=max($arrayH)-min($arrayH);
$border=30; // 邊框距離
    $base_w=$dst_w+($border*2);
    $base_h=$dst_h+($border*2);
    $dst_img=imagecreatetruecolor($base_w,$base_h); // 依上述計算完圖片後加上邊框的值產生總圖片底圖(文字+邊框)

    // imagecolorallocate()==宣告圖片顏色定義
    $white=imagecolorallocate($dst_img,255,255,255);
    $black=imagecolorallocate($dst_img,0,0,0);
    $red=imagecolorallocate($dst_img,255,0,0); 
    $green=imagecolorallocate($dst_img,0,255,0); 
    $blue=imagecolorallocate($dst_img,0,0,255); 
    imagefill($dst_img,0,0,$white); // 渲染圖片, 參數1:圖片、參數2&3:位置、參數4:顏色

    // imagettftext($dst_img,24,0,10,10,$blue,'./font/arial.ttf','ABCDE');
    // realpath()==自動從根目錄找到目前該檔案位置(看似相對路徑但實為絕對路徑)
    imagettftext($dst_img,$_POST['size'],0,($border+$dst_x),($border+$dst_y),$$color,realpath('./font/arial.ttf'),$gstr); // imagettftext()==於圖像上繪製文字, 參數1:使用圖像、參數2345:

    // 圖形驗證干擾線
    $lines=rand(4,6);
    for($i=0; $i<$lines; $i++){
        $left_x=rand(5,$border-5); // 隨機5~25作為x軸起點
        $left_y=rand(5,$base_h-5); 
        $right_x=rand($base_w-$border+5,$base_w-5); // 參數1:總寬(圖片本體+兩側邊框長度)-邊框長度(30)+5=終點線最低的x值 、 參數2:總寬(圖片本體+兩側邊框長度)-5=終點線最高的x值
        $right_y=rand(5,$base_h-5); // 參數1:終點線y最低為5 、 參數2:總高(圖片本體+上下邊框長度)=終點線最高的y值
        imageline($dst_img,$left_x,$left_y,$right_x,$right_y,$red); // imageline()==畫線, 參數1:畫在哪、參數23:畫的起點xy、參數45:畫的終點xy、參數6:顏色
    }
    
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