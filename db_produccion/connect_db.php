<?php

try{
    $pdo = new PDO('mysql:host=sql103.epizy.com;dbname=epiz_30810303_minimarket','epiz_30810303','RldDuybYDmDN');
    //echo 'Connection Successfull';
}catch(PDOException $error){
    echo $error->getmessage();
}


?>