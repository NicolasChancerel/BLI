<?php
header("Content-Type:text/plain");
include_once('../conf/connection.php');

$idDevice= "";
if(isset($_GET['idDevice'])){
	$idDevice = $_GET['idDevice'];
}else{
?>
{
	"reponse" : {
		"code" : "KO",
		"libelle" : "Il manque le parametre idDevice"
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

if(strlen($idDevice)==0){
?>
{
	"reponse" : {
		"code" : "KO",
		"libelle" : "Le parametre idDevice est vide"
	}
}
<?php
	exit;
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

$sql = "select id,nom_interne,longitude,latitude,DATE_FORMAT(date_creation,'%d/%m/%Y %H:%i:%s') as date_creation,DATE_FORMAT(date_reception,'%d/%m/%Y %H:%i:%s') as date_reception,DATE_FORMAT(date_lecture_notification,'%d/%m/%Y %H:%i:%s') as date_lecture_notification,id_device AS id_device from rfid where id_device='".$idDevice."';";

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