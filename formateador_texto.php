<?php
// funciones usadas para dar formato a la informacion del tweet antes de generar los archivos de datos entrada a prolog 
$stopwords= file("stopwords.txt",FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$emoticones= file("emoticones.txt",FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$palabras_poco_serias= file("palabras_poco_serias.txt",FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$enie= utf8_decode("ñ");
$eNie= utf8_decode("Ñ");
$hash= utf8_decode("#");  
$arroba= utf8_decode("@");
$guion_bajo= utf8_decode("_");
$guion= utf8_decode("-");

/*  FUNCIONES PARA TIPO DE TWEET  */

function es_risa($palabra){
    $pattern1='#^[j|a][j|a]+$#';
    $pattern2='#^j[j|e]+$|^ee[j|e]+$|^ejj[j|e]*$|^eje[j|e]+$#';
    $pattern3='#^[j|i][j|i]+$#';
    $pattern4='#^j[j|o]+$|^oo[j|o]+$|^ojj[j|o]*$|^ojo[j|o]+$#';
    $pattern5='#^[j|u][j|u]+$#';
    $pattern6='#^aa[a|h]*$|^ahh[a|h]*$|^aha[a|h]+$|^hh[a|h]*$|^ha[a|h]+$#';
    $pattern7='#^ee[e|h]*$|^eh[e|h]+$|^he[e|h]+$|^hh[e|h]*$#';
    $pattern8='#^[i|h][i|h]+$#';
    $pattern9='#^h[o|h]+$|^oo[o|h]*$|^oh[o|h]+$#';
    $pattern10='#^h[u|h]+$|^uu[u|h]*$|^uh[u|h]+$#';
            
    $i=preg_match($pattern1, $palabra) + preg_match($pattern2, $palabra) + preg_match($pattern3, $palabra) + preg_match($pattern4, $palabra) + preg_match($pattern5, $palabra) + preg_match($pattern6, $palabra) + preg_match($pattern7, $palabra) + preg_match($pattern8, $palabra) + preg_match($pattern9, $palabra) + preg_match($pattern10, $palabra);
    return ($i > 0);
}

function no_es_serio($texto){
    global $emoticones;
    global $palabras_poco_serias;
    
    //revisar si existen emoticones
    $texto= limpiar_entities($texto);
    foreach($emoticones as $key => $emoticon){
        $contiene= (stripos($texto,$emoticon)!== false);
        if($contiene){break;}
    }
    
    //si no contiene emoticones, me fijo si tiene risas o palabras poco serias
    if(!$contiene){         
        $palabras = explode(" ",$texto);
    
        foreach($palabras as $key => $palabra){
            $palabra= strtolower($palabra);
            $serio= in_array($palabra,$palabras_poco_serias);
            $risa=  es_risa($palabra);
            $contiene=  $serio || $risa;
            if($contiene){break;}
        }
        
    }      
    return $contiene;
}

function tipoEstructTweet($texto, $palabra_query){
    if(stripos($texto, $palabra_query)=== false || no_es_serio($texto)){
        return "irrelevante";
    }else if($texto[0] == '@'){
        return "conversacion";
    }else if(stripos($texto,"https://")!== false || stripos($texto,"http://")!== false){
        return "difusion";   
    }else{ 
        return "general";
    }
    
}  

/*------------- FUNCIONES PARA OBTENER PALABRAS ------------*/
function caracter_valido($char){
    global $enie;
    global $eNie;
    global $hash;
    global $arroba;
    global $guion;
    global $guion_bajo;
    return ctype_alpha($char) || is_numeric($char) || $char==$eNie || $char==$enie || $char==$guion_bajo ||/* $char==$hash ||*/ $char==$guion;
}

function limpiar_acentos($texto){  
    
   //buscar mas caracteres extraños en http://www.i18nqa.com/debug/utf8-debug.html 
   $caracteres_raros = array(    'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
                                'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
                                'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
                                'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 
                                'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ü'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 
                                '¿'=>'.', '&lt'=>'.', '&gt'=>'.', 'ƒ'=>'.', '…'=>'.', '†'=>'.', '‡'=>'.', '‰'=>'.', '‘'=>'.', '’'=>'.',
                                '“'=>'.', '”'=>'.', '•'=>'.', '–'=>'.', '—'=>'.', '˜ '=>'.', '™'=>'.', '›'=>'.', '¡'=>'.', '©'=>'.' ,
                                '«'=>'.', '¬'=>'.', '®'=>'.', '¯'=>'.', '°'=>'.', '±'=>'.', '²'=>'.', '³'=>'.', '´'=>'.', '»'=>'.', 
                                '¸'=>'.', '¹'=>'.', 'º'=>'.', '¼'=>'.', '½'=>'.', '¾'=>'.', '×'=>'.', 'ç'=>'c' );
    return strtr( $texto, $caracteres_raros );
}



function es_stopword($palabra){
    global $stopwords;    
    return in_array(strtolower($palabra),$stopwords);
}

function es_usuario($palabra){
    return substr( $palabra, 0, 1 ) == "@";
}
  
function limpiar_simbolos($texto){ 
     
    $texto= limpiar_acentos($texto);

    $texto=utf8_decode($texto);
     
    for($i=0; $i<strlen($texto); $i++){
        if(!caracter_valido($texto[$i])) $texto[$i]=" ";
    }
   
    $texto=utf8_encode($texto);
    
    return $texto;
}

function limpiar_dobles($texto){
    $texto= trim($texto);
    $texto= preg_replace('{(.)\1+}','$1$1',$texto);
    return $texto;
}

function separar_palabras($texto){
    $palabras = explode(" ",$texto);

    //limpiar palabras y sacar stopwords
    $cjto = array();
    foreach($palabras as $pal){
        $pal= trim($pal);
        if(strlen($pal)!=0 && !es_stopword($pal) && !es_usuario($pal) && !is_numeric($pal) && !es_risa($pal) && !isset($cjto[$pal])){
            $cjto[$pal]=true;
        }
    }
    return $cjto;
}

function caracter_valido_hashtag($char){
    global $enie;
    global $eNie;
    global $guion_bajo;
    return ctype_alpha($char) || is_numeric($char) || $char==$eNie || $char==$enie || $char==$guion_bajo ;
}

function caracter_valido_username($char){
    global $guion_bajo;
    return ctype_alpha($char) || is_numeric($char) || $char==$guion_bajo ;
}


function limpiar_entities($texto){
      
    $texto= limpiar_acentos($texto);  
    
    //LIMPIAR HASHTAGS    
   /* foreach($tweet->hashtags as $hashtag):
        $long= $hashtag["indices"][1]-$hashtag["indices"][0]+1; 
        $reemplazo= str_repeat(".",$long);
        $texto= substr_replace($texto, $reemplazo, $hashtag["indices"][0],$long);  
    endforeach;*/
    
    
    $hash= $actual= 0;
    while($actual < strlen($texto) && ($actual=stripos($texto,"#"))!==false){
        $hash= $actual;
        $long=1;
        $actual++;
        if($actual < strlen($texto)){
             while($actual < strlen($texto) && caracter_valido_hashtag($texto[$actual])){
                 $actual++;
                 $long++;
             }
             $reemplazo= str_repeat(".",$long); 
             $texto= substr_replace($texto,$reemplazo,$hash,$long);
        }
    }
    
    
    //LIMPIAR URLS
  /*  foreach($tweet->urls as $urls):
        $long= $urls["indices"][1]-$urls["indices"][0]+1;
        $reemplazo= str_repeat(".",$long); 
        $texto= substr_replace($texto,$reemplazo,$urls["indices"][0],$long);      
    endforeach;   */
    
    while(($i=stripos($texto,"http://"))!== false){        
        while($i<strlen($texto) && $texto[$i]!=" "){
            $texto[$i]=".";
            $i++;
        }
    }                             
    
    while(($i=stripos($texto,"https://"))!==false){        
        while($i<strlen($texto) && $texto[$i]!=" "){
            $texto[$i]="";
            $i++;
        }
    }
    
    //LIMPIAR USERS
/*    foreach($tweet->userMentions as $key => $mention):
        $long= $mention["indices"][1]-$mention["indices"][0]+1;
        $reemplazo= str_repeat(".",$long); 
        $texto= substr_replace($texto,$reemplazo,$mention["indices"][0],$long);  
    endforeach;  */
        
    $arr= $actual= 0;
    while($actual < strlen($texto) && ($actual=stripos($texto,"@"))!==false){
        $arr= $actual;
        $long=1;
        $actual++;
        if($actual < strlen($texto)){
             while($actual < strlen($texto) && caracter_valido_username($texto[$actual])){
                 $actual++;
                 $long++;
             }
             $reemplazo= str_repeat(".",$long); 
             $texto= substr_replace($texto,$reemplazo,$arr,$long);
        }
    }
    
    
   // $texto=utf8_encode($texto);
    
   /* for($i=0; $i<strlen($texto); $i++){
        if(substr( $texto, $i,7 )=="http://" || substr( $texto, $i,8 )=="https://"){
            $j=$i;
            while($j<strlen($texto) && $texto[$j]!=" "){
                $texto[$j]=" ";    
                $j++;
            }                  
        }
    } */
    return $texto;
}

function texto_a_palabras($tweet){

    $texto= $tweet->text;
    
    $texto= limpiar_entities($texto); 

    $texto= limpiar_simbolos($texto);
    
    $texto= limpiar_dobles($texto);   

    $palabras= separar_palabras($texto); 

    return $palabras;

}

?>