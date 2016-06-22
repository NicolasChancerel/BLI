Trigger M2X<br/>

<?php
	//pour tester les entrées en GET et POST
	$values = "method=". $_SERVER['REQUEST_METHOD']."<br/>GET<br/>\n";
	if(count($_GET)>0){
		foreach($_GET as $key => $getValues){
			$values = $values . $key ."=" . $getValues . "<br/>\n";
		}
	}
	$values = $values . "POST<br/>\n";
	if(count($_POST)>0){
		foreach($_POST as $key => $getValues){
			$values = $values . $key ."=" . $getValues . "<br/>\n";
		}
	}
	//on voit que les triggers passent en POST mais le donnees ne passent pas par les variables 
	
	//par contre , on voit que les variables passent en raw et en json
	//pour le test, je les enregistre dans un fichier test.txt (en effet, on fonctionnement TRIGGER, je ne vois pas le resultat...
	$postdata = file_get_contents("php://input");
	$values = $values . $postdata . "<br/>\n";
	$retour = file_put_contents("test.txt",$values,FILE_APPEND);
	
	//pour afficher le resultat quand on fait des tests, sinon, il faut y acceder via son URI
	echo file_get_contents("test.txt");
?>
