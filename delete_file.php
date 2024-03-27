<?php
include_once 'base.php';
$id=$_GET['id'];
$file=$pdo->query("SELECT * FROM `upload` WHERE `id`='$id'"); // 搜尋該id之路徑供下一步unlink()執行刪除
/* 
有無使用fetch()之差異:
   1. 未使用fetch()取得一個PDO結果集, 而非陣列, 無法被程式使用
        PDOStatement Object ( [queryString] => SELECT * FROM `upload` WHERE `id`='30' )
   2. 使用fetch()取得一個陣列, 可被程式使用
        Array ( [id] => 30 [0] => 30 [name] => 20240323030909.txt [1] => 20240323030909.txt [type] => text/plain [2] => text/plain [collections] => 文件 [3] => 文件 )
*/
$file=$pdo->query("SELECT * FROM `upload` WHERE `id`='$id'")->fetch(); // 搜尋該id之路徑供下一步unlink()執行刪除

// 因需檔名獲取完整路徑, 需先從庫取該筆資料, 所以順序:1.先刪檔案 2.刪除資料庫
unlink("./upload/".$file['name']); // 1. 刪除檔案

// 僅刪除資料庫的資訊, 實際在主機裡的"檔案"由上列的unlink()刪除, 
del($id); // 刪除資料庫
header("location:manage.php");

?>