<?php
	include_once('/entrada.pl.php');
	include_once('/tweet.php');

ini_set('display_errors',0); //para no mostrar errores
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="es">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link rel="icon" type="image/png" href="favicon.ico">
        <title>Tweets</title>
        <link  href="css/stylesheet.css" rel="stylesheet" type="text/css" />
        <link  href="css/tweet_style.css" rel="stylesheet" type="text/css" />
       <script src="jquery.js"></script> 
<script>                                                          
function loadXMLDoc(query){
var xmlhttp;
if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
}else{// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
}

xmlhttp.onreadystatechange=function(query){
    if (xmlhttp.readyState==4 && xmlhttp.status==200){
        document.getElementById("myDiv").innerHTML=xmlhttp.responseText;
    }
}
xmlhttp.open("GET","ajax_info.php/?q="+query,true);
xmlhttp.send();
}

$(document).ready(function() {
    $('ul').bind('click', function() {
        $('ul div').addClass('tweet');
    });
});           
        
</script>        
    </head>
    <body>
        <div id="searchPanel"> 
		<img id="logo" src="imagen/Aum.png" /><br/>
        <div id="search">
            <input id="entrada"></input>
            <input type="button" value="Buscador Twitter" onclick="loadXMLDoc(document.getElementById('entrada').value)"></input>
        </div>
		</div>

			<?php
               /* if($_GET['q']){                   
					// Hace la conexion con el API de twitter y crea un archivo con la info de los tweets
				    recuperar_tweets($_GET['q']);
                } */
            ?>

        <div id="myDiv"><em>Ingrese una palabra para poder comenzar la busqueda</em></div>
        
    </body>
   
</html>
