<?php
/*
l'utilisation de la librairie m2x necessite l'utilisation du gestionnaire de dependances php composer : https://getcomposer.org/
il faut ensuite creer le fichier composer.json avec
{
  "require": {
    "attm2x/m2x-php": "~4.0"
  }
}

puis lancer le composer.phar pour generer les differents fichiers de conf de composer et telecharger la librairie
une page de doc : https://github.com/attm2x/m2x-php/blob/master/README.md
*/

//chargement de la librairie via l'autoload de composer
require_once "./vendor/autoload.php";

//declaration des objets
use Att\M2X\M2X;
use Att\M2X\Error\M2XException;

//clé api
$apiKey = "587f7006234b4059d15de39c98dd7a4a";

//id du device
$deviceId = '434b72300bad423cbaa295f7db30404c';

//instanciation de l'objet
$m2x = new M2X($apiKey);

//Get the device
$device = $m2x->device($deviceId);

//Create the streams if they don't exist yet
$device->updateStream('load_1m');
$device->updateStream('load_5m');
$device->updateStream('load_15m');

//liste des load du cpu
list($load_1m, $load_5m, $load_15m) = sys_getloadavg();
$now = date('c');

$values = array(
'load_1m'  => array(array('value' => $load_1m,  'timestamp' => $now)),
'load_5m'  => array(array('value' => $load_5m,  'timestamp' => $now)),
'load_15m' => array(array('value' => $load_15m, 'timestamp' => $now))
);

//affichage pour debug
echo "load_1m.value=".$values['load_1m'][0]['value']." load_1m.timestamp=".$values['load_1m'][0]['timestamp']."\n<br/>";  
echo "load_5m.value=".$values['load_5m'][0]['value']." load_5m.timestamp=".$values['load_5m'][0]['timestamp']."\n<br/>";
echo "load_15m.value=".$values['load_15m'][0]['value']." load_15m.timestamp=".$values['load_15m'][0]['timestamp']."\n<br/>";

//on ecrit le resultat sur le stream du device
//attention, il y a un probleme quand on essaie de charger plusiauers flux en meme temps (timeout heroku ?)
//$device->stream('load_1m')->postValues(array(array('value' => $load_1m,  'timestamp' => $now)));
$device->stream('load_5m')->postValues(array(array('value' => $load_5m,  'timestamp' => $now)));
//$device->stream('load_15m')->postValues(array(array('value' => $load_15m,  'timestamp' => $now)));

?>