<?php
header("Content-Type:text/plain");
include_once('./conf/connection.php');

if ($connection->connect_errno) {
	echo "Erreur de connexion\n";
	echo "Errno: " . $connection->connect_errno . "\n";
    echo "Error: " . $connection->connect_error . "\n";
}

$sql = "DROP TABLE IF EXISTS rfid_infos;";
if (!$result = $connection->query($sql)) {
	echo "Error: Our query failed to execute and here is why: \n";
	echo "Query: " . $sql . "\n";
	echo "Errno: " . $connection->errno . "\n";
	echo "Error: " . $connection->error . "\n";
	exit;
}else{
	echo "Drop table rfid_infos ok\n";
}

$sql = "DROP TABLE IF EXISTS rfid;";
if (!$result = $connection->query($sql)) {
	echo "Error: Our query failed to execute and here is why: \n";
	echo "Query: " . $sql . "\n";
	echo "Errno: " . $connection->errno . "\n";
	echo "Error: " . $connection->error . "\n";
	exit;
}else{
	echo "Drop table rfid ok\n";
}

$sql = "DROP TABLE IF EXISTS params;";
if (!$result = $connection->query($sql)) {
	echo "Error: Our query failed to execute and here is why: \n";
	echo "Query: " . $sql . "\n";
	echo "Errno: " . $connection->errno . "\n";
	echo "Error: " . $connection->error . "\n";
	exit;
}else{
	echo "Drop table params ok\n";
}

$sql = "CREATE TABLE IF NOT EXISTS params (".
	"cle varchar(50) NOT NULL PRIMARY KEY,".
	"libelle varchar(50) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
if (!$result = $connection->query($sql)) {
	echo "Error: Our query failed to execute and here is why: \n";
	echo "Query: " . $sql . "\n";
	echo "Errno: " . $connection->errno . "\n";
	echo "Error: " . $connection->error . "\n";
	exit;
}else{
	echo "Creation table params ok\n";
}

$sql = "INSERT INTO params (cle, libelle) VALUES".
	"('NOM_CLIENT', 'Nom du client'),".
	"('NOM_PRODUIT', 'Nom du produit'),".
	"('REF_PRODUIT', 'Reference du produit'),".
	"('URL_MANUEL', 'Adresse du manuel');";
if (!$result = $connection->query($sql)) {
	echo "Error: Our query failed to execute and here is why: \n";
	echo "Query: " . $sql . "\n";
	echo "Errno: " . $connection->errno . "\n";
	echo "Error: " . $connection->error . "\n";
	exit;
}else{
	echo "Insert default table params ok\n";
}

$sql = "CREATE TABLE IF NOT EXISTS rfid (".
	"id varchar(50) NOT NULL PRIMARY KEY,".
  	"nom_interne varchar(255) NOT NULL,".
	"date_creation datetime NOT NULL,".
	"date_reception datetime NOT NULL,".
	"date_lecture_notification datetime NOT NULL,".
	"id_device varchar(255) NOT NULL,".
	"longitude FLOAT NOT NULL ,".
	"latitude FLOAT NOT NULL ".
	") ENGINE=InnoDB DEFAULT CHARSET=utf8;";
if (!$result = $connection->query($sql)) {
	echo "Error: Our query failed to execute and here is why: \n";
	echo "Query: " . $sql . "\n";
	echo "Errno: " . $connection->errno . "\n";
	echo "Error: " . $connection->error . "\n";
	exit;
}else{
	echo "Creation table rfid ok\n";
}

$sql = "CREATE TABLE IF NOT EXISTS rfid_infos (".
	"id_rfid varchar(50) NOT NULL,".
	"cle_params varchar(50) NOT NULL,".
	"valeur varchar(255) NOT NULL,".
	"affichage_emetteur int(11) DEFAULT NULL,".
	"affichage_recepteur int(11) DEFAULT NULL,".
	"PRIMARY KEY (id_rfid,cle_params)) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
if (!$result = $connection->query($sql)) {
	echo "Error: Our query failed to execute and here is why: \n";
	echo "Query: " . $sql . "\n";
	echo "Errno: " . $connection->errno . "\n";
	echo "Error: " . $connection->error . "\n";
	exit;
}else{
	echo "Creation table rfid_infos ok\n";
}
/*
$sql = "DELETE FROM params;";
if (!$result = $connection->query($sql)) {
	echo "Error: Our query failed to execute and here is why: \n";
	echo "Query: " . $sql . "\n";
	echo "Errno: " . $connection->errno . "\n";
	echo "Error: " . $connection->error . "\n";
	exit;
}else{
	echo "Delete table params ok\n";
}

$sql = "DELETE FROM rfid;";
if (!$result = $connection->query($sql)) {
	echo "Error: Our query failed to execute and here is why: \n";
	echo "Query: " . $sql . "\n";
	echo "Errno: " . $connection->errno . "\n";
	echo "Error: " . $connection->error . "\n";
	exit;
}else{
	echo "Delete table rfid ok\n";
}

$sql = "DELETE FROM rfid_infos;";
if (!$result = $connection->query($sql)) {
	echo "Error: Our query failed to execute and here is why: \n";
	echo "Query: " . $sql . "\n";
	echo "Errno: " . $connection->errno . "\n";
	echo "Error: " . $connection->error . "\n";
	exit;
}else{
	echo "Delete table rfid_infos ok\n";
}
*/
?>