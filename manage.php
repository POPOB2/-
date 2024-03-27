<?php
include_once ('./base.php');
// 使用MYSQL將資料存入

/**
 * 1.建立資料庫及資料表來儲存檔案資訊 
 * 2.建立上傳表單頁面
 * 3.取得檔案資訊並寫入資料表
 * 4.製作檔案管理功能頁面
 */

if(isset($_FILES['file']) && $_FILES['file']['error']==0){
    $type=$_FILES['file']['type']; // 可以用type去找副檔名, 但不建議 因有上千種,

    // 名字本身含有副檔名, 僅須從名字後方提取副檔名即可, 
    // 用explode()將檔名和附檔名之間的.用於拆開為array, 可得出陣列最後的值為副檔名
    $sub=explode('.', $_FILES['file']['name'])[1]; // 最後放的[數字]為第幾個陣列:0、1、2..
    // 此例以上傳時間作為檔名
    $name=date("Ymdhis").'.'.$sub;
    move_uploaded_file($_FILES['file']['tmp_name'], './upload/'.$name);
    $sql="INSERT INTO `upload`(`name`,`type`,`collections`) VALUES('{$name}','{$type}','{$_POST['collections']}')";
    $pdo->exec($sql);
}else{

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>檔案管理功能</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <form id="uploadForm" action="?" method="post" enctype="multipart/form-data">
        <div>
            選擇檔案:<input type="file" name="file">
        </div>
        <input type="submit" value="上傳">
        <select name="collections">
            <option value="圖片">圖片</option>
            <option value="文件">文件</option>
            <option value="試算表">試算表</option>
        </select>
    </form>

    <!-- 透過資料表來顯示檔案的資訊，並可對檔案執行更新或刪除的工作 -->
    <table id="list">
        <tr>
            <th>id</th>
            <th>thumb</th>
            <th>name</th>
            <th>type</th>
            <th>collections</th>
            <th>操作</th>
        </tr>
<?php
// $files=all('upload');
$sql="SELECT * FROM `upload`";
$files=$pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
foreach($files as $file){
?>
        <tr>
            <td><?=$file['id'];?></td>
            <!-- 顯示圖片不可直接用$name會變成取cookie紀錄最後上傳的圖, 使全部都同一張 -->
            <!-- 應使用每次迴圈帶進來的$file裡的['name'] -->
            <!-- <img src='./upload/<?=$file['name']?>'> -->
            <td>
                <?php
                switch($file['collections']){
                    case '圖片':
                        echo "<img src='./upload/{$file['name']}'>";
                    break;
                    case '文件':
                        echo "<img src='./icon/word_icon.jpg'>";
                    break;
                    case '試算表':
                        echo "<img src='./icon/excel_icon.png'>";
                    break;
                    
                }
                ?>
                
            </td> 
            <td><?=$file['name'];?></td>
            <td><?=$file['type'];?></td>
            <td><?=$file['collections'];?></td>
            <td>
                <button onclick="location.href='update_file.php?id=<?=$file['id']?>'">更換檔案</button>
                <button onclick="location.href='delete_file.php?id=<?=$file['id']?>'">刪除</button>
            </td>
        </tr>
<?php
}
?>

    </table>

</body>
</html>