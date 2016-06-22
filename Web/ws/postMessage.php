<?php
//chargement de la librairie via l'autoload de composer
require_once "../vendor/autoload.php";

//declaration des objets
use Att\M2X\M2X;
use Att\M2X\Error\M2XException;
	
$error=0;
$msgError = "";
if(isset($_POST['apiKey']) && !empty($_POST['apiKey'])){
	$apiKey = $_POST['apiKey'];
}else{
	$error = 1;
	if(!isset($_POST['apiKey'])){
		$msgError = $msgError . "Il manque le parametre apiKey\n";
	}else if(empty($_POST['apiKey'])){
		$msgError = $msgError . "Le parametre apiKey est vide\n";
	}
}

if(isset($_POST['deviceId']) && !empty($_POST['deviceId'])){
	$deviceId = $_POST['deviceId'];
}else{
	$error = 1;
	if(!isset($_POST['deviceId'])){
		$msgError = $msgError . "Il manque le parametre deviceId\n";
	}else if(empty($_POST['deviceId'])){
		$msgError = $msgError . "Le parametre deviceId est vide\n";
	}
}

$streamId = "";
if(isset($_POST['streamId']) && !empty($_POST['streamId'])){
	$streamId = $_POST['streamId'];
}else{
	$error = 1;
	if(!isset($_POST['streamId'])){
		$msgError = $msgError . "Il manque le parametre streamId\n";
	}else if(empty($_POST['streamId'])){
		$msgError = $msgError . "Le parametre streamId est vide\n";
	}
}

$valeur = "";
if(isset($_POST['valeur']) && !empty($_POST['valeur'])){
	$valeur = $_POST['valeur'];
}else{
	$error = 1;
	if(!isset($_POST['valeur'])){
		$msgError = $msgError . "Il manque le parametre valeur\n";
	}else if(empty($_POST['valeur'])){
		$msgError = $msgError . "Le parametre valeur est vide\n";
	}
}

$streams=explode(";",$streamId);
$valeurs=explode(";",$valeur);

//test si le nombre de stream = le nombre de valeurs
if(count($streams)!=count($valeurs)){
	$error = 1;
	$msgError = $msgError . "Incoherence entre les streamId et les valeurs (nombre different)\n";
}

$timestamps  = array();

if($error==0){
	//instanciation de l'objet
	$m2x = new M2X($apiKey);

	//Get the device
	$device = $m2x->device($deviceId);

	//Create the streams if they don't exist yet
	for($i=0;$i<count($streams);$i=$i+1){
		$device->updateStream($streams[$i]);

		$now = date('c');
		$timestamps[$i] = $now;
		$device->stream($streams[$i])->postValues(array(array('value' => $valeurs[$i],  'timestamp' => $now)));
	}
?>
{
	"reponse" : {
		"code" : "OK",
		"libelle" : "Tout s'est bien passe"
	},
	"valeurs": [
<?php
for($i=0;$i<count($streams);$i=$i+1){
	if($i>0){
		echo ",";
	}
?>
		{
			"apiKey" : "<?php echo $apiKey;?>",
			"deviceId" : "<?php echo $deviceId;?>",
			"streamId" : "<?php echo $streams[$i];?>",
			"valeur" : "<?php echo $valeurs[$i];?>",
			"timestamp" : "<?php echo $timestamps[$i];?>"
		}
<?php
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