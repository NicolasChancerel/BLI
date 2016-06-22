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

$id_device= "";
if(isset($_POST['id_device'])){
	$id_device = $_POST['id_device'];
}else{
?>
{
	"reponse" : {
		"code" : "KO",
		"libelle" : "Il manque le parametre id_device"
	}
}
<?php
	exit;
}


if(strlen($id_device)==0){
?>
{
	"reponse" : {
		"code" : "KO",
		"libelle" : "Le parametre id_device est vide"
	}
}
<?php
	exit;
}

$sql = "UPDATE rfid SET date_reception=NOW(),id_device='".$id_device."' WHERE id='".$id."';";

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

$connection->close();
?>
{
	"reponse" : {
		"code" : "OK",
		"libelle" : "Update Rfid <?php echo $id;?> ok"
	}
}
