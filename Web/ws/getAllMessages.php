<?php
//chargement de la librairie via l'autoload de composer
require_once "../vendor/autoload.php";

//declaration des objets
use Att\M2X\M2X;
use Att\M2X\Error\M2XException;

$error=0;
$msgError = "";
if(isset($_GET['apiKey']) && !empty($_GET['apiKey'])){
	$apiKey = $_GET['apiKey'];
}else{
	$error = 1;
	if(!isset($_GET['apiKey'])){
		$msgError = $msgError . "Il manque le parametre apiKey,";
	}else if(empty($_GET['apiKey'])){
		$msgError = $msgError . "Le parametre apiKey est vide,";
	}
}

if(isset($_GET['deviceId']) && !empty($_GET['deviceId'])){
	$deviceId = $_GET['deviceId'];
}else{
	$error = 1;
	if(!isset($_GET['deviceId'])){
		$msgError = $msgError . "Il manque le parametre deviceId,";
	}else if(empty($_GET['deviceId'])){
		$msgError = $msgError . "Le parametre deviceId est vide,";
	}
}

if(isset($_GET['streamId']) && !empty($_GET['streamId'])){
	$streamId = $_GET['streamId'];
}else{
	$error = 1;
	if(!isset($_GET['streamId'])){
		$msgError = $msgError . "Il manque le parametre streamId,";
	}else if(empty($_GET['streamId'])){
		$msgError = $msgError . "Le parametre streamId est vide,";
	}
}





	//instanciation de l'objet
	$m2x = new M2X($apiKey);

	//Get the device
	$device = $m2x->device($deviceId);

	//get the stream
	$stream = $device->stream($streamId);

	//get the values
	$values = $stream->values();
	
	if(!isset($values['values'])){
		$error = 1;
		$msgError = $msgError . "Le stream '".$streamId."' n'existe pas..";
	}
	
if($error==0){
	$valuesArray = $values['values'];

?>
{
	"reponse" : {
		"code" : "OK",
		"libelle" : "Tout s'est bien passe"
	},
	"values" : [
<?php
	$compteurLigne = 0;
	foreach($valuesArray as $value){
		if($compteurLigne>0){
			echo "		,";
		}else{
			echo "		";
		}
		echo "{\n";
		echo "			\"value\" : {\n";
		echo "				\"timestamp\":\"".$value['timestamp']."\",\n";
		echo "				\"valeur\":\"".$value['value']."\"\n";
		echo "			}\n";
		echo "		}\n";
		$compteurLigne = $compteurLigne  + 1;
	}
?>
	]
}
<?php
}else{
?>
{
	"reponse" : {
		"code" : "KO",
		"libelle" : "<?php echo $msgError;?>"
	}
}
<?php
}
?>
