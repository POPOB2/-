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
    $text_info=[];
    $dst_w=0;
    $dst_h=0;
    for($i=0; $i<mb_strlen($gstr); $i++){ // mn_strlen()==計算字串字數
        $char=mb_substr($gstr,$i,1); // mb_substr()==擷取字串內容, 參數1:擷取的字串、參數2:從第幾個字開始、參數3:擷取幾個字
        // $char取得每一個字做為陣列, 供imagettfbbox()產生文字圖片
        $tmp=imagettfbbox($_POST['size'],0,realpath('./font/arial.ttf'),$char); // imagettfbbox()==以陣列提供產生的圖片文字, 其大小、xy位置等資訊
        // 取出每個字的寬高
        $text_info[$char]['width']=max($tmp[0],$tmp[2],$tmp[4],$tmp[6])-min($tmp[0],$tmp[2],$tmp[4],$tmp[6]);
        $text_info[$char]['height']=max($tmp[1],$tmp[3],$tmp[5],$tmp[7])-min($tmp[1],$tmp[3],$tmp[5],$tmp[7]);

        $dst_w+=$text_info[$char]['width'];
        $dst_h=($dst_h>=$text_info[$char]['height'])?$dst_h:$text_info[$char]['height']; // 3元:每次迴圈判斷$dst_h變為取得的最大值

        // 取出每個字的xy起始點
        $text_info[$char]['x']=0-min($tmp[0],$tmp[2],$tmp[4],$tmp[6]);
        $text_info[$char]['y']=0-min($tmp[1],$tmp[3],$tmp[5],$tmp[7]);
    }
    // dd($text_info);
    // exit();

$border=10; // 邊框距離
    $base_w=$dst_w+($border*2);
    $base_h=$dst_h+($border*2);
    $dst_img=imagecreatetruecolor($base_w,$base_h); // 依上述計算完圖片後加上邊框的值產生總圖片底圖(文字+邊框)

    // imagecolorallocate()==宣告圖片顏色定義
    $white=imagecolorallocate($dst_img,255,255,255);
    $black=imagecolorallocate($dst_img,0,0,0);
    $red=imagecolorallocate($dst_img,255,0,0); 
    $green=imagecolorallocate($dst_img,0,255,0); 
    $blue=imagecolorallocate($dst_img,0,0,255); 
    $colors=[imagecolorallocate($dst_img,255,0,0),
             imagecolorallocate($dst_img,0,255,0),
             imagecolorallocate($dst_img,0,0,255),
             imagecolorallocate($dst_img,0,255,255),
             imagecolorallocate($dst_img,255,0,255),
             imagecolorallocate($dst_img,255,120,0),
    ];
    imagefill($dst_img,0,0,$white); // 渲染圖片, 參數1:圖片、參數2&3:位置、參數4:顏色

    
    $x_pointer=$border; // 累積的x軸, 須從$border值開始算
    $y_pointer=$border; 
    foreach($text_info as $char => $info){
        // 參數3 : 從累計的字寬x繼續向下
        imagettftext($dst_img,$_POST['size'],0,$x_pointer,$y_pointer+$info['y'],$colors[rand(0,5)],realpath('./font/arial.ttf'),$char); // imagettftext()==於圖像上繪製文字, 參數1:使用圖像、參數2345:
        // 每次獲得新的字就會多一段x的寬, 累計後作為下一個字的寬度向後使用
        $x_pointer+=$info['width'];
    }

    // 圖形驗證干擾線
    $lines=rand(4,6);
    
    for($i=0; $i<$lines; $i++){
        $left_x=rand(5,$border-5); // 隨機5~25作為x軸起點
        $left_y=rand(5,$base_h-5); 
        $right_x=rand($base_w-$border+5,$base_w-5); // 參數1:總寬(圖片本體+兩側邊框長度)-邊框長度(30)+5=終點線最低的x值 、 參數2:總寬(圖片本體+兩側邊框長度)-5=終點線最高的x值
        $right_y=rand(5,$base_h-5); // 參數1:終點線y最低為5 、 參數2:總高(圖片本體+上下邊框長度)=終點線最高的y值
        imageline($dst_img,$left_x,$left_y,$right_x,$right_y,$colors[rand(0,5)]); // imageline()==畫線, 參數1:畫在哪、參數23:畫的起點xy、參數45:畫的終點xy、參數6:顏色
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