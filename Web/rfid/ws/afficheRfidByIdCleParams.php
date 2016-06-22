<?php
header("Content-Type:text/plain");
include_once('../conf/connection.php');

$cleParams= "";
if(isset($_GET['cleParams'])){
	$cleParams = $_GET['cleParams'];
}else{
?>
{
	"reponse" : {
		"code" : "KO",
		"libelle" : "Il manque le parametre cleParams"
	}
}
<?php
	exit;
}

if(strlen($cleParams)==0){
?>
{
	"reponse" : {
		"code" : "KO",
		"libelle" : "Le parametre cleParams est vide"
	}
}
<?php
	exit;
}

$valeur= "";
if(isset($_GET['valeur'])){
	$valeur = $_GET['valeur'];
}else{
?>
{
	"reponse" : {
		"code" : "KO",
		"libelle" : "Il manque le parametre valeur"
	}
}
<?php
	exit;
}

if(strlen($valeur)==0){
?>
{
	"reponse" : {
		"code" : "KO",
		"libelle" : "Le parametre valeur est vide"
	}
}
<?php
	exit;
}

$affiche_emetteur = -1;
if(isset($_GET['affiche_emetteur'])){
	$affiche_emetteur = $_GET['affiche_emetteur'];
}

$affiche_client = -1;
if(isset($_GET['affiche_client'])){
	$affiche_client = $_GET['affiche_client'];
}

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

$sql = "select distinct rfid.id as id,rfid.nom_interne as nom_interne,rfid.longitude as longitude,rfid.latitude as latitude,DATE_FORMAT(rfid.date_creation,'%d/%m/%Y %H:%i:%s') as date_creation,DATE_FORMAT(rfid.date_reception,'%d/%m/%Y %H:%i:%s') as date_reception,DATE_FORMAT(rfid.date_lecture_notification,'%d/%m/%Y %H:%i:%s') as date_lecture_notification,rfid.id_device AS id_device from rfid,rfid_infos where rfid.id=rfid_infos.id_rfid and cle_params='".$cleParams."' and valeur='".$valeur."';";

if (!$result_set = $connection->query($sql)) {
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
?>
{
	"reponse" : {
		"code" : "OK",
		"libelle" : "Retour ok"
	},
	"rfid" : [
<?php
$nb_rfid=0;
while ($ligneRfid = $result_set->fetch_assoc()) {
	if($nb_rfid>0){
		echo "		,\n";
	}
	$id = $ligneRfid['id'];

?>
		{
			"id" : "<?php echo $ligneRfid['id'];?>",
			"nom_interne" : "<?php echo $ligneRfid['nom_interne'];?>",
			"date_creation" : "<?php echo $ligneRfid['date_creation'];?>",
			"date_reception" : "<?php echo $ligneRfid['date_reception'];?>",
			"date_lecture_notification" : "<?php echo $ligneRfid['date_lecture_notification'];?>",
			"id_device" : "<?php echo $ligneRfid['id_device'];?>",
			"longitude" : "<?php echo $ligneRfid['longitude'];?>",
			"latitude" : "<?php echo $ligneRfid['latitude'];?>",
			"fields" : [
<?php
$sql = "select rfid_infos.cle_params as cle_params,params.libelle as libelle_params,rfid_infos.valeur as valeur,rfid_infos.affichage_emetteur AS affiche_emetteur,rfid_infos.affichage_recepteur AS affiche_client from rfid_infos,params where rfid_infos.id_rfid='".$ligneRfid['id']."' and params.cle = rfid_infos.cle_params";
if($affiche_emetteur>-1){
	$sql = $sql . " AND affichage_emetteur=".$affiche_emetteur;
}
if($affiche_client>-1){
	$sql = $sql . " AND affichage_recepteur=".$affiche_client;
}
$sql = $sql . ";";

$result_set_rfid = $connection->query($sql);

	$nb_lignes = 0;
	while ($ligne = $result_set_rfid->fetch_assoc()) {
		if($nb_lignes>0){
			echo "				,\n";
		}
		echo "				{\n";
		echo "					\"cle_params\": \"".$ligne['cle_params']."\",\n";
		echo "					\"libelle_params\": \"".$ligne['libelle_params']."\",\n";
		echo "					\"valeur\": \"".$ligne['valeur']."\",\n";
		echo "					\"affiche_emetteur\": ".$ligne['affiche_emetteur'].",\n";
		echo "					\"affiche_client\": ".$ligne['affiche_client']."\n";
		echo "				}\n";
		$nb_lignes = $nb_lignes + 1;
	}
?>
		
			]
		}
<?php
	$nb_rfid = $nb_rfid + 1;
}
?>
	]
}
<?php
$connection->close();
?>