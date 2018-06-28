<?php


header('Access-Control-Allow-Methods: GET, POST');
include("functions.php");

if (isset($_POST['katID']) && isset($_POST['ime']) && isset($_POST['brand_id']) && isset($_POST['cena']) && isset($_POST['url'])) {


    $katID = $_POST['katID'];
    $ime = $_POST['ime'];
    $brand_id = $_POST['brand_id'];
    $cena = $_POST['cena'];
    $url = $_POST['url'];
    


    echo addProizvod($katID, $ime, $brand_id, $cena, $url);
}


?>