<?php
include("shared.php");


function checkIfLoggedIn()
{
    global $conn;
    if (isset($_SERVER['HTTP_TOKEN'])) {
        $token = $_SERVER['HTTP_TOKEN'];
        $result = $conn->prepare("SELECT * FROM users WHERE token=?");
        $result->bind_param("s", $token);
        $result->execute();
        $result->store_result();
        $num_rows = $result->num_rows;
        if ($num_rows > 0) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function login($email, $password)
{
    global $conn;
    $rarray = array();
    if (checkLogin($email, $password)) {
        $id = sha1(uniqid());
        $result2 = $conn->prepare("UPDATE users SET token=? WHERE email=?");
        $result2->bind_param("ss", $id, $email);
        $result2->execute();
        $rarray['token'] = $id;
        
    } else {
        header('HTTP/1.1 401 Unauthorized');
        $rarray['error'] = "Invalid username/password";
    }
    return json_encode($rarray);
}

function checkLogin($email, $password)
{
    global $conn;
    $password = md5($password);
    $result = $conn->prepare("SELECT * FROM users WHERE email=? AND password=?");
    $result->bind_param("ss", $email, $password);
    $result->execute();
    $result->store_result();
    $num_rows = $result->num_rows;
    if ($num_rows > 0) {
        return true;
    } else {
        return false;
    }
}

function checkLoginID($id)
{
    global $conn;
    $result = $conn->prepare("SELECT * FROM users WHERE id=? ");
    $result->bind_param("i", $id);
    $result->execute();
    $result->store_result();
    $num_rows = $result->num_rows;
    if ($num_rows > 0) {
        return true;
    } else {
        return false;
    }
}

function register($firstname, $lastname, $address, $email, $password)
{
    global $conn;
    $rarray = array();
    $errors = "";
    if (checkIfUserExists($email)) {
        $errors .= "Profile with that email already exists\r\n";
    }
    if (strlen($email) < 5) {
        $errors .= "Email must have at least 5 characters\r\n";
    }
    if (strlen($password) < 5) {
        $errors .= "Password must have at least 5 characters\r\n";
    }
    if (strlen($firstname) < 3) {
        $errors .= "First name must have at least 3 characters\r\n";
    }
    if (strlen($lastname) < 3) {
        $errors .= "Last name must have at least 3 characters\r\n";
    }
    if ($errors == "") {
        $stmt = $conn->prepare("INSERT INTO users (firstname, lastname, address, email, password) VALUES (?, ?, ?, ?,?)");
        $pass = md5($password);
        $stmt->bind_param("sssss", $firstname, $lastname, $address, $email, $pass);
        if ($stmt->execute()) {
            $id = sha1(uniqid());
            $result2 = $conn->prepare("UPDATE users SET token=? WHERE email=?");
            $result2->bind_param("ss", $id, $email);
            $result2->execute();
            $rarray['token'] = $id;

        } else {
            header('HTTP/1.1 400 Bad request');
            $rarray['error'] = "Database connection error";
        }
    } else {
        header('HTTP/1.1 400 Bad request');
        $rarray['error'] = json_encode($errors);
    }

    return json_encode($rarray);
}

function checkIfUserExists($email)
{
    global $conn;
    $result = $conn->prepare("SELECT * FROM users WHERE email=?");
    $result->bind_param("s", $email);
    $result->execute();
    $result->store_result();
    $num_rows = $result->num_rows;
    if ($num_rows > 0) {
        return true;
    } else {
        return false;
    }
}


function getUser($token)
{
    $token = str_replace('"', "", $token);
    global $conn;
    $query = 'SELECT id, firstname, lastname, address, email, token, role_id,
      (SELECT name FROM role WHERE role.id = users.role_id) AS role_name 
      FROM users
      WHERE users.token = ?';
    $user = array();
    $statement = $conn->prepare($query);
    $statement->bind_param('i', $token);
    if ($statement->execute()) {
        $result = $statement->get_result();
        while ($row = $result->fetch_assoc()) {
            $user['id'] = $row['id'];
            $user['firstname'] = $row['firstname'];
            $user['lastname'] = $row['lastname'];
            $user['address'] = $row['address'];
            $user['email'] = $row['email'];
            $user['role_id'] = $row['role_id'];
            $user['role_name'] = $row['role_name'];

        }
    }
    return json_encode($user);
}

function addProizvod($katID, $ime, $brand_id, $cena, $url)
{
    global $conn;
    $rarray = array();
    $errors = "";
    if (checkIfLoggedIn()) {

        if ($errors == "") {
            $stmt = $conn->prepare("INSERT INTO proizvod (katID, ime, brand_id, cena, url) 
        VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param('isiis', $katID, $ime, $brand_id, $cena, $url);

            if ($stmt->execute()) {
                $rarray['success'] = "ok";
            } else {
                $rarray['error'] = "Database connection error";
            }
            return json_encode($rarray);
        } else {
            header('HTTP/1.1 400 Bad request');
            $rarray['error'] = json_encode($errors);
            return json_encode($rarray);
        }

    } else {
        $rarray['error'] = "Please log in";
        header('HTTP/1.1 401 Unauthorized');
        return json_encode($rarray);
    }
}

function removeProduct($id)
{
    global $conn;
    $message = array();
    $query = 'DELETE
      FROM proizvod
      WHERE proizvod.id = ?';
    $statement = $conn->prepare($query);
    $statement->bind_param("i", $id);
    $statement->execute();
    if ($statement->execute()) {
        $message['success'] = "OK";
    } else {
        $message['error'] = "Error!";
    }
    return json_encode($message);
}

function getProdavnice()
{
    global $conn;
    $rarray = array();

    $result = $conn->query("SELECT * FROM prodavnica");
    $num_rows = $result->num_rows;
    $prodavnice = array();
    if ($num_rows > 0) {
        $result2 = $conn->query("SELECT * FROM prodavnica");
        while ($row = $result2->fetch_assoc()) {
            array_push($prodavnice, $row);
        }
    }
    $rarray['prodavnice'] = $prodavnice;
    return json_encode($rarray);

}

function getProizvodi()
{
    global $conn;
    $rarray = array();

    $result = $conn->query("SELECT * FROM PROIZVOD");
    $num_rows = $result->num_rows;
    $proizvodi = array();
    if ($num_rows > 0) {
        $result2 = $conn->query("SELECT * FROM PROIZVOD");
        while ($row = $result2->fetch_assoc()) {
            array_push($proizvodi, $row);
        }
    }
    $rarray['proizvodi'] = $proizvodi;
    return json_encode($rarray);

}
function getNarudzbine()
{
    global $conn;
    $rarray = array();

    $result = $conn->query("SELECT * FROM narudzbina");
    $num_rows = $result->num_rows;
    $narudzbine = array();
    if ($num_rows > 0) {
        $result2 = $conn->query("SELECT * FROM narudzbina");
        while ($row = $result2->fetch_assoc()) {
            array_push($narudzbine, $row);
        }
    }
    $rarray['narudzbine'] = $narudzbine;
    return json_encode($rarray);

}
function removeNarudzbina($id)
{
    global $conn;
    $message = array();
    $query = 'DELETE
      FROM narudzbina
      WHERE narudzbina.id = ?';
    $statement = $conn->prepare($query);
    $statement->bind_param("i", $id);
    $statement->execute();
    if ($statement->execute()) {
        $message['success'] = "OK";
    } else {
        $message['error'] = "Error!";
    }
    return json_encode($message);
}

function getBrand()
{
    global $conn;
    $rarray = array();

    $result = $conn->query("SELECT * FROM brand");
    $num_rows = $result->num_rows;
    $brand = array();
    if ($num_rows > 0) {
        $result2 = $conn->query("SELECT * FROM brand");
        while ($row = $result2->fetch_assoc()) {
            array_push($brand, $row);
        }
    }
    $rarray['brand'] = $brand;
    return json_encode($rarray);

}
function getProizvod($id){
    global $conn;
    $rarray = array();
    $proizvodi = array();
    if(checkIfLoggedIn()){
        $result = $conn->query("SELECT * FROM proizvod WHERE id=".$id);
        while($row = $result->fetch_assoc()) {

            $proizvodi = array();
            $proizvodi['id'] = $row['id'];
            $proizvodi['ime'] = $row['ime'];
            $proizvodi['cena'] = $row['cena'];
            $proizvodi['url'] = $row['url'];
            $proizvodi['brand_id'] = $row['brand_id'];
            $proizvodi = $proizvodi;

        }

        $rarray['proizvodi'] = $proizvodi;
        return json_encode($rarray);
    } else{
        $rarray['error'] = "Please log in";
        header('HTTP/1.1 401 Unauthorized');
        return json_encode($rarray);
    }
}
