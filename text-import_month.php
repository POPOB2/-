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
<div style="width:50%; margin:1rem auto;">
    <!-- 將option所選的年份, 用get從網址帶值給當前頁 -->
    <form action="?" method="get"> <!-- action="?" 問號代表待表單送到當下目前的該檔案  -->
        選擇年份 : <select name="year">
            <?php
            // GROUP BY == 從資料庫"群組"相同的值, 
                $years=$pdo->query("SELECT `year` FROM `temperature_month` GROUP BY `year`")->fetchAll(PDO::FETCH_ASSOC); // 使用fetch只會撈一筆, 因有多個年份, 所以使用fetchAll
                foreach($years as $year){
                    echo "<option value='{$year['year']}'>{$year['year']}年</option>"; // 每一次的$year雖然只有一個值, 但其是陣列內容, [key]為year, 所以寫成$year['year']印出每次的foreach()撈出的年份

                }

            ?>
        </select>
        <input type="submit" value="送出">
    </form>
</div>



    <table id="list">
        <tr>
            <th>年</th>
            <th>月</th>
            <th>平均氣溫[0C]</th>
            <th>平均相對溼度[%]</th>
            <th>日照時數[小時]</th>
            <th>降水量[毫米]</th>
            <th>降水日數[日]</th>
            <th>操作</th>
        </tr>
        <?php
        if(isset($_GET['year'])){ // 於select選擇年份, 就會有值
            $sql="SELECT * FROM temperature_month WHERE `year`='{$_GET['year']}'"; // 顯示get傳來的所選年份
        }else{
            $sql="SELECT * FROM temperature_month"; // 未收到get傳來的年份, 就顯示所有資料
        }
        $rows=$pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

$counts=count($rows); // 取資料筆數作為總數, 供計算平均值

        // 產生畫面時, 就寫入一份該畫面的內容至csv供下載
        if(isset($_GET['year'])){
            $file=fopen('paper.csv','w'); // $file=寫入檔案到paper.csv , **補充:w==寫入模式** , 此時$file只是被設為寫入模式的檔案, 還未有內容

            fwrite($file,"\xEF\xBB\xBF"); // 在第一次開始寫入內容之前(下一行fwrite()), 需在最前面(第一次執行fwrite(),也就是這次), 寫入存檔類型, 才可以使其存為BOM-UTF8, 避免以UTF-8存檔產生亂碼
// 先歸0, 待會用於計算
$tempe=0;
$humidity=0;
$daylight=0;
$preci=0;
$preci_days=0;
            // fwrite()==執行寫入檔案, 參數1:欲寫入的檔案(該檔案需如上設為寫入模式)、參數2:欲寫入之內容
            fwrite($file , '"年份","月份","平均氣溫[0C]","平均相對溼度[%]","日照時數[小時]","降水量[毫米]","降水日數[日]"'."\r\n"); // 先執行一次. 將檔案第一行的標題寫入
                                                      // 注意"\r\n"一定要用雙引號框起來, 如果碰到前面的內容已經用單引號框起來, 就用.進行連接, 另外寫上"\r\n" 如上

            foreach($rows as $row){ // 將上方pdo從庫撈到的資料, 依序..
                unset($row['id']); // unset()==去除陣列的指定內容, 因不須使用id, 所以去除陣列$row的['id']

// 使用迴圈進行計算, 每次都將各個值進行加總
$tempe+=$row['tempe'];
$humidity+=$row['humidity'];
$daylight+=$row['daylight'];
$preci+=$row['preci'];
$preci_days+=$row['preci_days'];

                fwrite($file, join(',',$row)."\r\n"); // 依序使用fwrite()將內容寫入$file , 其內容使用join()以逗號作為區隔將$row從陣列改為字串供寫入
                                                      // 每寫入一行就需斷行, 否則會全部寫在同一行, 可使用"\r\n"執行換行(微軟可解析的換行符號)
            }

// 將迴圈獲取的數, 除以總數取平均值, 並使用round()設置四捨五入後小數點顯示幾位, 參數1:進行計算的值、參數2:顯示小數點後幾位數
$tempe=round($tempe/$counts,2);
$humidity=round($humidity/$counts,2);
$daylight=round($daylight/$counts,2);
$preci=round($preci/$counts,2);
$preci_days=round($preci_days/$counts,2);

// 寫入平均值資料
fwrite($file,"'','平均',$tempe,$humidity,$daylight,$preci,$preci_days");

            fclose($file); // fclose()==停止開啟檔案, 建議每次使用f系列函式, 如fopen()等操作, 在結束時使用fclose()關閉file開啟狀態, 避免發生不可預期之錯誤
        }

        foreach($rows as $row){
        ?>
        <tr>
            <td><?=$row['year']?></td>
            <td><?=$row['month']?></td>
            <td><?=$row['tempe']?></td>
            <td><?=$row['humidity']?></td>
            <td><?=$row['daylight']?></td>
            <td><?=$row['preci']?></td>
            <td><?=$row['preci_days']?></td>
            <td>
                <button onclick="location.href='update_row.php?id=<?=$row['id']?>'">編輯</button>
                <button onclick="location.href='del_row.php?id=<?=$row['id']?>'">刪除</button>
            </td>
        </tr>
        <?php  
        }
        ?>
    </table>
<div style="width:500px; margin:1rem auto; text-align:right;">
<!-- 依不同瀏覽器對a標籤放上的檔案類型去定義怎麼開啟, 可能不會是下載, 例如若a標籤放圖片會以瀏覽器開啟圖片, 而非下載圖片 -->
    <!-- <a href="paper.csv">下載</a>  -->

<?php
if(isset($_GET['year'])){ // 有選擇年份才顯示下載, 全顯示時==未選擇年份==沒有GET['year']==不顯示下載
?>
<!-- 多帶一條download屬性, 可以確保該a標籤為下載功能 -->
    <a href="paper.csv" download>下載</a> 
<?php } ?>    
</div>


</body>
</html>