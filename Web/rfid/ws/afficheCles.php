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

$sql = "select cle,libelle from params;";

if (!$result_set = $connection->query($sql)) {
?>
{
	"reponse" : {
		"code" : "KO",
		"libelle" : "<?php $connection->connect_errno;?>,<?php $connection->connect_error;?>"
	}
}
<?php
	
}

?>
{
	"reponse" : {
		"code" : "OK",
		"libelle" : "Voici les parametres utilisables"
	},
	"values" : [
<?php
$nb_lignes = 0;
while ($ligne = $result_set->fetch_assoc()) {
	if($nb_lignes>0){
		echo "	,\n";
	}
	echo "	{\n";
    echo "		\"cle\": \"".$ligne['cle']."\",\n";
	echo "		\"libelle\": \"".$ligne['libelle']."\"\n";
	echo "	}\n";
	$nb_lignes = $nb_lignes + 1;
}
?>
	]
}
<?php

$connection->close();
?>