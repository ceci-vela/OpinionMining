<?php
    
    require_once('twitteroauth/twitteroauth.php');
    require_once('config.php');
   
    $entrada= null;

    function recuperar_tweets($search){
        global $entrada;        

        //creo el archivo argumento de entrada del razonador polog
        $entrada= new Entrada();
        $entrada->abrirArchivo();

       // recuperar tweets publicados anteriores a este moemento y los guarda en el archivo de entrada
        recuperar_tweets_viejos($search, $entrada);   
        
      // recuperar tweets publicados a partir de este momento     
      //  recuperar_tweets_nuevos($search, $entrada);

        $entrada->cerrarArchivo();
    
    }
    
    function objectToArray($d) {
        if (is_object($d)) {
            // Gets the properties of the given object
            // with get_object_vars function
            $d = get_object_vars($d);
        }
 
        if (is_array($d)) {
            /*
            * Return array converted to object
            * Using __FUNCTION__ (Magic constant)
            * for recursive call
            */
            return array_map(__FUNCTION__, $d);
        }
        else {
            // Return array
            return $d;
        }
    }
    
    function recuperar_tweets_viejos($search, $entrada){          

            /* Create a TwitterOauth object with consumer/user tokens. */
            $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, oauth_token, oauth_token_secret);
            
            /* If method is set change API call made. Test is called by default. */
            $content = $connection->get('account/verify_credentials');

            $aux =$connection->get('search/tweets', array('q' => $search, 'count' => 100, 'lang' => 'es')); //search api
          
            //guardo el termino de la query en la entrada    
            $entrada->guardarQuery($search);
            
            //por cada tweet recuperado, guardar su informacion en el archivo entrada    
            foreach ($aux->statuses as $key => $value) {
               // $entrada->guardarTweet(json_decode(json_encode($value),true)); 
                $tweet= objectToArray($value);
                $entrada->guardarTweet($tweet); 
            }

    }
   

	function recuperar_tweets_nuevos($search, $entrada) {
		
		global $user, $pass, $entrada;		
		set_time_limit(0); // Poner 0 para que cicle infinitamente...
		
		if ($search != "") {
			
			$query_data = array('track' => $search);
			
			$fp = fsockopen("ssl://stream.twitter.com", 443, $errno, $errstr, 30);
			if(!$fp){
				print "$errstr ($errno)\n";
			} else {
				$request = "GET /1/statuses/filter.json?" . http_build_query($query_data) . " HTTP/1.1\r\n";
				$request .= "Host: stream.twitter.com\r\n";
				$request .= "Authorization: Basic " . base64_encode($user . ':' . $pass) . "\r\n\r\n";
				fwrite($fp, $request);
			
				
				echo("<div>sali de la creacion de tweets.txt</div>");	
				
				
				$i=0;
				while($i<50){ //Para que no sea infinito, limitarlo a una cantidad por cada corrida...			
					stream_set_timeout($fp, 2);
					$json = fgets($fp);	
                    
   $msg = openssl_error_string();
    echo $msg . "<br />\n";
					$jsonTweet = json_decode(utf8_encode($json), true);				
					
					if($jsonTweet){	

						//$entrada->guardarTexto($data['text'], (string)$data['id_str']);	
						
						$entrada->guardarTweet($jsonTweet);				
						
				/*		$tmpHTML="<div class='rta'>"; 
						$tmpHTML.= "<a href=\"https://twitter.com/#!/".$jsonTweet['user']['screen_name']."\">"."-".$jsonTweet['user']['name']."</a>";
						$tmpHTML.= "<br />".$jsonTweet['text']."<br/>";
						$tmpHTML.="</div>";
						echo $tmpHTML;  */
						$i++;
					}
                     $i++;			
				}
				
				fclose($fp);
				
			}
		
		}
		else {
		
			echo "Indicar el termino con el cual consultar a twitter.";
		}
		
	}	
?>