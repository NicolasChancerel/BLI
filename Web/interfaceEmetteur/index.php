<html>
	<head>
		<link rel="stylesheet" type="text/css" media="screen" href="./css/styleGeneral.css" />
		<title>Interface Createur</title>
		<script>
function rempliTableau(){
	var xhr = new XMLHttpRequest();
	if(xhr && xhr.readystate != 4){
		xhr.abort();
	}
	xhr.onreadystatechange  = function(){
	   if(xhr.readyState  == 4){
			if(xhr.status  == 200){
				var jsonResponse = eval('('+xhr.responseText+')');
				var retour = "";

				retour = retour + "<div style=\"display:table-row;\">";
				retour = retour + "<div style=\"display:table-cell;text-align:left;\">Code RFID :</div>";
				retour = retour + "<div style=\"display:table-cell;\"><input type=\"text\" name=\"rfid\"></div>";
				retour = retour + "<div style=\"display:table-cell;\"></div>";
				retour = retour + "<div style=\"display:table-cell;\"></div>";
				retour = retour + "</div>";
				
				retour = retour + "<div style=\"display:table-row;\">";
				retour = retour + "<div style=\"display:table-cell;text-align:left;\">Nom interne :</div>";
				retour = retour + "<div style=\"display:table-cell;\"><input type=\"text\" name=\"nom_interne\"></div>";
				retour = retour + "<div style=\"display:table-cell;\"></div>";
				retour = retour + "<div style=\"display:table-cell;\"></div>";
				retour = retour + "</div>";
				
				retour = retour + "<div style=\"display:table-row;\">";
				retour = retour + "<div style=\"display:table-cell;\"></div>";
				retour = retour + "<div style=\"display:table-cell;\"></div>";
				retour = retour + "<div style=\"display:table-cell;\">Affichage cot&eacute; emetteur</div>";
				retour = retour + "<div style=\"display:table-cell;\">Affichage cot&eacute; client</div>";
				retour = retour + "</div>";
				
				for(var i=0;i<jsonResponse.values.length;i=i+1){
					retour = retour + "<div style=\"display:table-row;\">";
					retour = retour + "<div style=\"display:table-cell;text-align:left;\">"+jsonResponse.values[i].libelle+" :</div>";
					retour = retour + "<div style=\"display:table-cell;\"><input type=\"text\" name=\"valeur\"><input type=\"hidden\" name=\"cle\" value=\""+jsonResponse.values[i].cle+"\"></div>";
					retour = retour + "<div style=\"display:table-cell;\"><input type=\"checkbox\" name=\"affiche_emetteur_"+jsonResponse.values[i].cle+"\"></div>";
					retour = retour + "<div style=\"display:table-cell;\"><input type=\"checkbox\" name=\"affiche_client_"+jsonResponse.values[i].cle+"\"></div>";
					retour = retour + "</div>";
				}
				
				retour = retour + "<div style=\"display:table-row;\">";
				retour = retour + "<div style=\"display:table-cell;\"><input type=\"button\" value=\"Valider\" onclick=\"valider();\"></div>";
				retour = retour + "<div style=\"display:table-cell;\"><input type=\"button\" value=\"Annuler\" onclick=\"annuler();\"></div>";
				retour = retour + "</div>";

				window.document.getElementById("divFormulaire").innerHTML = retour;
			}
		}
	};
	xhr.open( "GET", "../rfid/ws/afficheCles.php",  false);
	xhr.send(null);
}

function annuler(){
	rempliTableau();
}

function deleteRfid(id){
	if(confirm("Etes vous sur de vouloir supprimer le rfid d'id "+id+" ?")){
		var xhr = new XMLHttpRequest();
		xhr.open( "POST", "../rfid/ws/deleteRfid.php",  false);
		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		xhr.send("id="+id);
		refreshListe();
	}
}

function valider(){
	var rfid = window.document.formulaire.rfid.value;
	var nom_interne = window.document.formulaire.nom_interne.value;
	
	if(rfid.length==0){
		alert("Il faut un code rfid ...");
		return false;
	}
	
	if(nom_interne.length==0){
		alert("Il faut un nom interne ...");
		return false;
	}
	
	var cles = window.document.getElementsByName("cle");
	var valeurs = window.document.getElementsByName("valeur");
	var cle_string = "";
	var valeur_string = "";
	var affiche_emetteur_string = "";
	var affiche_client_string = "";
	
	
	for(var i=0;i<cles.length;i=i+1){
		var affiche_emetteur = window.document.getElementsByName("affiche_emetteur_"+cles[i].value)[0].checked;
		var affiche_client = window.document.getElementsByName("affiche_client_"+cles[i].value)[0].checked;
		
		if(valeurs[i].value.length>0 && (affiche_emetteur==true || affiche_client==true)){
			if(cle_string.length>0){
				cle_string = cle_string + ";"
				valeur_string = valeur_string + ";"
				affiche_emetteur_string = affiche_emetteur_string + ";"
				affiche_client_string = affiche_client_string + ";"
			}
			cle_string  = cle_string + cles[i].value;
			valeur_string  = valeur_string + valeurs[i].value;
			if(affiche_emetteur==true){
				affiche_emetteur_string  = affiche_emetteur_string + "1";
			}else{
				affiche_emetteur_string  = affiche_emetteur_string + "0";
			}
			if(affiche_client==true){
				affiche_client_string  = affiche_client_string + "1";
			}else{
				affiche_client_string  = affiche_client_string + "0";
			}
		}
	}
	
	if(cle_string.length==0){
		alert("Il faut un champ rempli et qu'il soit en affichage cote client ou emetteur ...");
		return false;
	}

	var xhr = new XMLHttpRequest();
	xhr.open( "POST", "../rfid/ws/insertRfid.php",  false);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send("id="+rfid+"&nom_interne="+nom_interne+"&cles_params="+cle_string+"&valeurs="+valeur_string+"&affiche_emetteur="+affiche_emetteur_string+"&affiche_client="+affiche_client_string);
	//debug
	/*
	window.document.realFormulaire.id.value = rfid;
	window.document.realFormulaire.nom_interne.value = nom_interne;
	window.document.realFormulaire.cles_params.value = cle_string;
	window.document.realFormulaire.valeurs.value = valeur_string;
	window.document.realFormulaire.affiche_emetteur.value = affiche_emetteur_string;
	window.document.realFormulaire.affiche_client.value = affiche_client_string;
	window.document.realFormulaire.submit();
	*/
	refreshListe();
	
}

function refreshListe(){
	var xhr = new XMLHttpRequest();
	if(xhr && xhr.readystate != 4){
		xhr.abort();
	}
	xhr.onreadystatechange  = function(){
	   if(xhr.readyState  == 4){
			if(xhr.status  == 200){
				var jsonResponse = eval('('+xhr.responseText+')');
				var retour = "";
				
				retour = retour + "<h1 style=\"text-align:center;margin:0 auto;\">Liste des RFID</h1>";
				retour = retour + "<div style=\"display:table;margin:0 auto;width:100%;\">";
				retour = retour + "<div style=\"display:table-row;\">";
				retour = retour + "<div style=\"display:table-cell;border-style:solid;border-width:thin;\">Code</div>";
				retour = retour + "<div style=\"display:table-cell;border-style:solid;border-width:thin;\">Nom interne</div>";
				retour = retour + "<div style=\"display:table-cell;border-style:solid;border-width:thin;\">Textes</div>";
				retour = retour + "<div style=\"display:table-cell;border-style:solid;border-width:thin;\">Date Creation</div>";
				retour = retour + "<div style=\"display:table-cell;border-style:solid;border-width:thin;\">Date Reception</div>";
				retour = retour + "<div style=\"display:table-cell;border-style:solid;border-width:thin;\">Date Lecture Notification</div>";
				retour = retour + "<div style=\"display:table-cell;border-style:solid;border-width:thin;\">Id du device de reception</div>";
				retour = retour + "<div style=\"display:table-cell;border-style:solid;border-width:thin;\">Longitude/Latitude</div>";
				retour = retour + "<div style=\"display:table-cell;border-style:solid;border-width:thin;\">Delete</div>";
				retour = retour + "</div>";
				for(var i=0;i<jsonResponse.rfid.length;i=i+1){
					retour = retour + "<div style=\"display:table-row;\">";
					retour = retour + "<div style=\"display:table-cell;border-style:solid;border-width:thin;\">"+jsonResponse.rfid[i].id+"</div>";
					retour = retour + "<div style=\"display:table-cell;border-style:solid;border-width:thin;\">"+jsonResponse.rfid[i].nom_interne+"</div>";
					
					retour = retour + "<div style=\"display:table-cell;border-style:solid;border-width:thin;\">";
					for(var j=0;j<jsonResponse.rfid[i].fields.length;j=j+1){
						retour = retour + jsonResponse.rfid[i].fields[j].libelle_params+":"+jsonResponse.rfid[i].fields[j].valeur+"<br/>";
					}
					retour = retour + "</div>";
					
					retour = retour + "<div style=\"display:table-cell;border-style:solid;border-width:thin;\">"+jsonResponse.rfid[i].date_creation+"</div>";
					retour = retour + "<div style=\"display:table-cell;border-style:solid;border-width:thin;\">"+jsonResponse.rfid[i].date_reception+"</div>";
					retour = retour + "<div style=\"display:table-cell;border-style:solid;border-width:thin;\">"+jsonResponse.rfid[i].date_lecture_notification+"</div>";
					retour = retour + "<div style=\"display:table-cell;border-style:solid;border-width:thin;\">"+jsonResponse.rfid[i].id_device+"</div>";
					retour = retour + "<div style=\"display:table-cell;border-style:solid;border-width:thin;\">"+jsonResponse.rfid[i].longitude+"/"+jsonResponse.rfid[i].latitude+"</div>";
					retour = retour + "<div style=\"display:table-cell;border-style:solid;border-width:thin;\"><a href=\"#\" onclick=\"deleteRfid('"+jsonResponse.rfid[i].id+"');\">Suppression</a></div>";
					retour = retour + "</div>";
				}
				
				retour = retour + "</div>";
				
				
				window.document.getElementById("listeRfid").innerHTML = retour;
			}
		}
	};
	xhr.open( "GET", "../rfid/ws/afficheAllRfid.php",  false);
	xhr.send(null);
}
		</script>
	</head>
	<body onload="rempliTableau();refreshListe();">
		<div id="main-content" style="text-align:center;">
			<h1>Interface Createur</h1>
			<form name="formulaire">
				<div id="divFormulaire" style="display:table;margin:0 auto;">
				</div>
			</form>
		</div>
		<br/><br/><br/><br/><br/>
		<div id="listeRfid">
		</div>
		<form name="realFormulaire" action="../rfid/ws/insertRfid.php" method="POST">
			<input type="hidden" name="id">
			<input type="hidden" name="nom_interne">
			<input type="hidden" name="cles_params">
			<input type="hidden" name="valeurs">
			<input type="hidden" name="affiche_emetteur">
			<input type="hidden" name="affiche_client">
		</form>
	</body>
</html>
