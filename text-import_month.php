<?php
include_once "base.php";

if(isset($_FILES['file']) && $_FILES['file']['error']==0){
    // echo $_FILES['file']['tmp_name']; // 檔案上傳後的暫存位置


    $file=fopen($_FILES['file']['tmp_name'],'r'); 


    
    fgets($file); // 去除標題
    while(!feof($file)){ 
        $str=fgets($file); 
        $col=explode(",",$str); 

        if(count($col)==6){ // 判斷時, 未開始將$col拆為年和月, 所以總數為6
            // $year=explode(" ",$col[0])[0]; // $col的[0]為年月, explode用空白拆解後, 其[0]的第一個位置[0]為年
            // $month=explode(" ",$col[0])[1]; // $col的[0]為年月, explode用空白拆解後, 其[0]的第二個位置[1]為月

            /* 上傳的csv擋第一個值會帶入雙引號", 使應存入值為87變為"87發生錯誤, 
               str_replace()可以使用陣列依序替換內容, 將帶入的雙引號"也替換成空 */
            $year=str_replace(["年",'"'],"",explode(" ",$col[0])[0]); // 使用str_replace()將"年月"之字串去除(替換成空), 參數1:被替換的部分、參數2:替換成的部分、參數3:執行替換的目標
            $month=str_replace(["月",'"'],"",explode(" ",$col[0])[1]); 
// echo $year;
// echo "<hr>";
// echo $month;
// exit();
            $data=['year'=>$year, // 原為$col[0]的值還在, 所以後續的$col[1~5]依舊不會變
                   'month'=>$month,
                   'tempe'=>$col[1], 
                   'humidity'=>$col[2],
                   'daylight'=>$col[3],
                   'preci'=>$col[4],
                   'preci_days'=>$col[5],
        ];
        save('temperature_month',$data); // 參數1:存入的table、參數2:存入的資料 
        }
    }
    

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