<html>
	<head>
		<link rel="stylesheet" type="text/css" media="screen" href="./css/styleGeneral.css" />
		<title>Surveillance commandes</title>
		<script>


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
				
				
				
				retour = retour + "<h1 style=\"text-align:center;margin:0 auto;\">Suivi de commande</h1>";
				retour = retour + "<div style=\"display:table;margin:0 auto;width:100%;\">";
				retour = retour + "<div style=\"display:table-row;\">";
				retour = retour + "<div style=\"display:table-cell;border-style:solid;border-width:thin;\">Code</div>";
				retour = retour + "<div style=\"display:table-cell;border-style:solid;border-width:thin;\">Nom interne</div>";
				retour = retour + "<div style=\"display:table-cell;border-style:solid;border-width:thin;\">Infos</div>";
				retour = retour + "<div style=\"display:table-cell;border-style:solid;border-width:thin;\">Dates du suivi</div>";
				retour = retour + "</div>";
				for(var i=0;i<jsonResponse.rfid.length;i=i+1){
					var bgcolor = "#00AAFF";//turquoise
					var evenements = "Date d'envoi="+jsonResponse.rfid[i].date_creation.substring(0,10);
					if(jsonResponse.rfid[i].date_reception!="00/00/0000 00:00:00"){
						bgcolor = "#FFAA00";//orange
						evenements = evenements + "<br/>Date de reception="+jsonResponse.rfid[i].date_reception.substring(0,10)
					}
					
					if(jsonResponse.rfid[i].date_lecture_notification!="00/00/0000 00:00:00"){
						bgcolor = "#00FF00";//vert
						evenements = evenements + "<br/>Date de notification="+jsonResponse.rfid[i].date_lecture_notification.substring(0,10)
					}
					retour = retour + "<div style=\"display:table-row;background-color:"+bgcolor+";\">";
					retour = retour + "<div style=\"display:table-cell;border-style:solid;border-width:thin;\">"+jsonResponse.rfid[i].id+"</div>";
					retour = retour + "<div style=\"display:table-cell;border-style:solid;border-width:thin;\">"+jsonResponse.rfid[i].nom_interne+"</div>";
					
					retour = retour + "<div style=\"display:table-cell;border-style:solid;border-width:thin;\">";
					for(var j=0;j<jsonResponse.rfid[i].fields.length;j=j+1){
						retour = retour + jsonResponse.rfid[i].fields[j].libelle_params+":"+jsonResponse.rfid[i].fields[j].valeur+"<br/>";
					}
					retour = retour + "</div>";
					
					
					retour = retour + "<div style=\"display:table-cell;border-style:solid;border-width:thin;\">"+evenements+"</div>";
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

function refreshListeTimeOut(){
	window.setTimeout(refreshListeTimeOut,10000);
	refreshListe();
}

		</script>
	</head>
	<body onload="refreshListeTimeOut();">
		<div id="listeRfid">
		</div>
	</body>
</html>
