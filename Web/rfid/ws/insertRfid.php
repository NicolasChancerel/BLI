<?php
header("Content-Type:text/plain");
include_once('../conf/connection.php');

if ($connection->connect_errno) {
?>
{
	"reponse" : {
		"code" : "KO",
		"libelle" : "<?php $connection->connect_errno;?>,<?php $connection->connect_error;?>"
	}
}
<?php
	exit;
}

$id= "";
if(isset($_POST['id'])){
	$id = $_POST['id'];
}else{
?>
{
	"reponse" : {
		"code" : "KO",
		"libelle" : "Il manque le parametre id"
	}
}
<?php
	exit;
}


if(strlen($id)==0){
?>
{
	"reponse" : {
		"code" : "KO",
		"libelle" : "Le parametre id est vide"
	}
}
<?php
	exit;
}

$nom_interne= "";
if(isset($_POST['nom_interne'])){
	$nom_interne = $_POST['nom_interne'];
}else{
?>
{
	"reponse" : {
		"code" : "KO",
		"libelle" : "Il manque le parametre nom_interne"
	}
}
<?php
	exit;
}

if(strlen($nom_interne)==0){
?>
{
	"reponse" : {
		"code" : "KO",
		"libelle" : "Le parametre nom_interne est vide"
	}
}
<?php
	exit;
}

/**/
/*
echo $id."\n";
echo $nom_interne."\n";
echo $_POST['cles_params']."\n";
echo $_POST['valeurs']."\n";
echo $_POST['affiche_emetteur']."\n";
echo $_POST['affiche_client']."\n";

exit;
*/
/**/

$sql = "select id,nom_interne from rfid where id='".$id."';";

if (!$resultat = $connection->query($sql)) {
?>
{
	"reponse" : {
		"code" : "KO",
		"libelle" : "<?php echo $sql;?>,<?php $connection->connect_errno;?>,<?php $connection->connect_error;?>"
	}
}
<?php
	exit;
}

if($result_set = $resultat->fetch_assoc()){
?>
{
	"reponse" : {
		"code" : "KO",
		"libelle" : "Le rfid <?php echo $id;?> existe deja..."
	}
}
<?php
	exit;
}

if(!isset($_POST['cles_params'])){
?>
{
	"reponse" : {
		"code" : "KO",
		"libelle" : "Il manque le parametre cles_params"
	}
}
<?php
	exit;
}

if(!isset($_POST['valeurs'])){
?>
{
	"reponse" : {
		"code" : "KO",
		"libelle" : "Il manque le parametre valeurs"
	}
}
<?php
	exit;
}

$cles_params = explode(";",trim($_POST['cles_params']));
$valeurs = explode(";",trim($_POST['valeurs']));
$affiche_emetteur = explode(";",trim($_POST['affiche_emetteur']));
$affiche_client = explode(";",trim($_POST['affiche_client']));

if(count($cles_params)!=count($valeurs) || count($cles_params)!=count($affiche_emetteur) || count($cles_params)!=count($affiche_client)){
?>
{
	"reponse" : {
		"code" : "KO",
		"libelle" : "Incoherence entre les streamId et les valeurs ou les affichage emetteur ou client (nombre different)"
	}
}
<?php
	exit;
}

for($i=0;$i<count($affiche_emetteur);$i=$i+1){
	if($affiche_emetteur[$i]!=0 && $affiche_emetteur[$i]!=1){
?>
{
	"reponse" : {
		"code" : "KO",
		"libelle" : "Les valeurs de affiche_emetteur doivent etre de 0 ou 1"
	}
}
<?php
		exit;
	}
}

for($i=0;$i<count($affiche_client);$i=$i+1){
	if($affiche_client[$i]!=0 && $affiche_client[$i]!=1){
?>
{
	"reponse" : {
		"code" : "KO",
		"libelle" : "Les valeurs de affiche_client doivent etre de 0 ou 1"
	}
}
<?php
		exit;
	}
}

$sql = "INSERT INTO rfid (id,nom_interne,date_creation) values('".$id."','".$nom_interne."',NOW());";
if (!$resultat = $connection->query($sql)) {
?>
{
	"reponse" : {
		"code" : "KO",
		"libelle" : "<?php echo $sql;?>,<?php $connection->connect_errno;?>,<?php $connection->connect_error;?>"
	}
}
<?php
	exit;
}

for($i=0;$i<count($cles_params);$i=$i+1){
	$sql = "INSERT INTO rfid_infos (id_rfid,cle_params,valeur,affichage_emetteur,affichage_recepteur) values('".$id."','".$cles_params[$i]."','".$valeurs[$i]."',".$affiche_emetteur[$i].",".$affiche_client[$i].");";
	if (!$resultat = $connection->query($sql)) {
?>
{
	"reponse" : {
		"code" : "KO",
		"libelle" : "<?php echo $sql;?>,<?php $connection->connect_errno;?>,<?php $connection->connect_error;?>"
	}
}
<?php
		exit;
	}
}

$connection->close();
?>
{
	"reponse" : {
		"code" : "OK",
		"libelle" : "Insertion Rfid <?php echo $id;?> ok"
	}
}
