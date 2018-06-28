<?php
header('Access-Control-Allow-Methods: GET, POST');
include("shared.php");


function getOrder($token)
{
    $idKorisnika = tokenToId($token);

    global $conn;
    $query = 'SELECT narudzbina.id, narudzbina.idProizvoda, 
       proizvod.ime, proizvod.cena, narudzbina.kolicina,
       proizvod.katID, proizvod.brand_id, proizvod.url,narudzbina.korpa_id,
      (narudzbina.kolicina * proizvod.cena) AS total,
      (SELECT naziv FROM kategorija WHERE kategorija.id = proizvod.katID) AS type
      FROM narudzbina
      JOIN korpa ON narudzbina.korpa_id = korpa.id
      JOIN users ON korpa.user_id = users.id
      JOIN proizvod ON narudzbina.idProizvoda = proizvod.id
      WHERE korpa.flag = 1 AND korpa.user_id = ?';
    $korpa = array();
    if ($statement = $conn->prepare($query)) {
        $statement->bind_param('i', $idKorisnika);
        $statement->execute();
        $result = $statement->get_result();
        while ($row = $result->fetch_assoc()) {
            $order = array();
            $order['id'] = $row['id'];
            $order['idProizvoda'] = $row['idProizvoda'];
            $order['ime'] = $row['ime'];
            $order['type'] = $row['type'];
            $order['cena'] = $row['cena'];
            $order['kolicina'] = $row['kolicina'];
            $order['katID'] = $row['katID'];
            $order['brand_id'] = $row['brand_id'];
            $order['url'] = $row['url'];
            $order['korpa_id'] = $row['korpa_id'];
            $order['total'] = $row['total'];
            array_push($korpa, $order);
        }
    }
    $message['korpa'] = $korpa;
    return json_encode($korpa);
}

function removeOrder($token, $idProizvoda)
{
    $idKorisnika = tokenToId($token);
    global $conn;
    $message = array();
    $query = 'DELETE narudzbina
      FROM narudzbina
      JOIN proizvod ON narudzbina.idProizvoda = proizvod.id
      JOIN korpa ON narudzbina.korpa_id = korpa.id
      WHERE narudzbina.idProizvoda = ? AND korpa.user_id = ?';
    $statement = $conn->prepare($query);
    $statement->bind_param("ii", $idProizvoda, $idKorisnika);
    $statement->execute();
    if ($statement->execute()) {
        $message['success'] = "OK";
    } else {
        $message['error'] = "Error!";
    }
    return json_encode($message);
}

function addOrder($token, $idProizvoda, $kolicina)
{
    $cart_id = tokenToCart($token);
    global $conn;
    $message = array();
    $query = 'INSERT INTO narudzbina (korpa_id, idProizvoda, kolicina) VALUES  (?, ?, ?)';
    $statement = $conn->prepare($query);
    $statement->bind_param("iii", $cart_id, $idProizvoda, $kolicina);
    if ($statement->execute()) {
        $message['success'] = "OK";
    } else {
        $message['error'] = "Error!";
    }
    return json_encode($message);
}

function updateOrder($token, $idProizvoda, $kolicina)
{
    $idKorisnika = tokenToId($token);
    global $conn;
    $message = array();
    $query = 'UPDATE narudzbina
      JOIN korpa	
      ON narudzbina.korpa_id = korpa.id
      JOIN users 
      ON korpa.user_id = users.id
      SET narudzbina.kolicina = ?
      WHERE narudzbina.idProizvoda = ?
      AND users.id = ?';
    $statement = $conn->prepare($query);
    $statement->bind_param("iii", $kolicina, $idProizvoda, $idKorisnika);
    if ($statement->execute()) {
        $message['success'] = "OK";
    } else {
        $message['error'] = "Error!";
    }
    if (!checkIfCartExists($token)) {
        createCart($token);
    }
    return json_encode($message);
}

function checkout($token)
{
    $token = str_replace('"', "", $token);
//    sendMail($token);
    $idKorisnika = tokenToId($token);
    global $conn;
    $message = array();
    $query = 'UPDATE korpa 
              SET flag = 2
              WHERE korpa.flag = 1 AND korpa.user_id = ?';
    $statement = $conn->prepare($query);
    $statement->bind_param("i", $idKorisnika);
    if ($statement->execute()) {
        $message['success'] = "OK";
    } else {
        $message['error'] = "Error!";
    }
    if (!checkIfCartExists($token)) {
        createCart($token);
    }
    return json_encode($message);
}


?>