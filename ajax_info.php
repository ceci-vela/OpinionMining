<?php
    include_once('/stream.php');
	include_once('/entrada.pl.php');
	//	include_once('/lib/db/secret.php');
	include_once('/tweet.php');
	set_time_limit(0);
    
    $app_dir= 'C:/xampp/htdocs/miApp/';
    
//    $prolog_compiler_dir= 'C:/Program Files/swipl/bin/'; //compu grande
    $prolog_compiler_dir= 'C:/xampp/htdocs/miApp/SWI-PrologPortable/'; 
    $prolog_compiler='SWI-PrologPortable.exe';

	
if($_GET['q']){   

 recuperar_tweets($_GET['q']);                
					
   $cmd = "\"".$prolog_compiler_dir.$prolog_compiler."\" -f none -g load_files('".$app_dir."Prolog/guardar_archivo',[silent(true)]).";

	exec($cmd, $output);

	$archivo= $app_dir."argumentacion.xml";
	$doc = simplexml_load_file($archivo);
	
	$text=formatear_resultado($doc);

	echo $text;
}


function ordenar_por_importancia_tweet($xml){
    
    $data=  array();

    //pasar el xml a un arreglo de xml
    foreach($xml as $tweet){ $data[]= $tweet; }


    while(sizeof($data)!=0){
        $max_imp= -1;
        $max_key=0;
        $key=0;
        while($key < sizeof($data)){
            $tweet= $data[$key];
            if((float)$tweet->importancia_tweet > (float)$max_imp){
                $max_imp= $tweet->importancia_tweet;
                $max_key= $key;
                $max_tweet= $tweet;
            }   
            $key++;    
        }
        $data2[]=$max_tweet;
        array_splice($data, $max_key, 1);
        
    }  
    return $data2;
}

function crear_vista_datos($id,$importancia_tweet, $tipo){
    $text = '<div class="analisis">';
       // $text.= '<div class="id_original">'.$id.'</div>';
        $text .= '<div class="influencia">Importancia: ';
            $text .= round((double)$importancia_tweet,3);
        $text .= '</div>';
        $text .= '<div class="influencia">Tipo: ';
            $text .= $tipo;
        $text .= '</div>';
    $text .= '</div>';   
    return $text;    
}

function crear_vista_keywords($keywords){
    $text ='<div class="keywords">';
    $max_val=-1;
    $max_pal="??";
    foreach($keywords->keyword as $key => $keyword):
        $imp= (float)$keyword->importancia;
        if($imp!=1 && $imp > $max_val){$max_pal=$keyword->word;$max_val=$imp; }
        else{
           /* if($imp > 0.1)*/ $text .= '<div class="keyword">'.$keyword->word.': '.round($imp,2).'</div>';
        }                    
    endforeach;
        if($max_val!=-1){$text .= '<div class="keyword"><strong>'.$max_pal.': '.round((float)$max_val,2).'</strong></div>';}          
    $text .='</div>';
    
    return $text;   
} 

function crear_vista_hashtags($hashtags){
    $text ='<div class="hashtags">';
    $max_val=-1;
    $max_pal="??";
    foreach($hashtags->hashtag as $key => $hashtag):
        if((float)$hashtag->importancia!=1 && (float)$hashtag->importancia > $max_val){$max_pal=$hashtag->word;$max_val=(float)$hashtag->importancia; }
        else{$text .= '<div class="hashtag">'.$hashtag->word.': '.round((float)$hashtag->importancia,2).'</div>';}                    
    endforeach;
        if($max_val!=-1){$text .= '<div class="hashtag"><strong>'.$max_pal.': '.round((float)$max_val,2).'</strong></div>';}          
    $text .='</div>';
    
    return $text;   
} 

function crear_vista_top($id, $importancia_tweet, $tipo, $keywords, $hashtags){
    $text = '<span><div class="top">';
       $text .= crear_vista_datos($id, $importancia_tweet, $tipo);
       $text .= crear_vista_keywords($keywords);
       $text .= crear_vista_hashtags($hashtags);
    $text .= '</div></span>';   
    return $text; 
}

function crear_vista_texto($tweet){
     $text = '<div class="texto"><strong>';
        $text .= $tweet;
    $text .= '</strong></div>';
    return $text;
}

function crear_vista_usuarios($usuarios){
    $text ='<div class="usuarios">';
    foreach($usuarios->usuario as $key => $usuario):
        $text .='<div class="usuario">';
           // $text .='<div class="id_usuario">'.$usuario->id_usuario.'</div>';
            $text .='<div class="influencia">Influencia: '.round((double)$usuario->influencia_usuario,2).'</div>';
            $text .= '<a href="https://twitter.com/'.$usuario->userScreenName.'">';
                $text .= '<div class="username">';
                    $text .= $usuario->userScreenName;
                $text .= '</div>';
            $text .= '</a>'; 
            $text .= '<a href="https://twitter.com/'.$usuario->userScreenName.'">';
                $text .= '<img src="'.$usuario->userImage.'"/>';
            $text .= '</a>'; 
        $text .='</div>';          
    endforeach;  
    $text .='</div>';
    
    return $text;    
}

function crear_vista_tweet($id, $importancia_tweet, $keywords, $tipo, $tweet, $usuarios, $hashtags){
    $text = '<div class="tweet" id_tweet= '.$id.'>';
        $text .= crear_vista_top($id, $importancia_tweet, $tipo, $keywords, $hashtags);
        $text .= crear_vista_texto($tweet);
        $text .= crear_vista_usuarios($usuarios);
    $text .= '</div>';    
    return $text;    
            
          /*      $text .= '<span><div class="top">';
                   /* 
                    $text .= '<div class="info_user">';
                        $text .= '<div class="analisis">';/*
                            */
                            /*  
                            
                           
                        $text .= '</div>';
                                              
                        /*
                    $text .= '</div>';        
                $text .= '</div></span>';*/
}

function formatear_resultado($doc){
	$text="";
    $text="<div id='resultado'>";
	//$text.= "<ul>";
    $tweets= array();
	
    $doc_ordenado = ordenar_por_importancia_tweet($doc);
        
	foreach ($doc_ordenado as $result) :
        
		$id = $result -> id_original;
       
        $tweet =(string)$result -> text;
        if(!in_array($tweet,$tweets)){
            
            $tweets[]= $tweet;// se lleva la cuenta de los tweets que ya pasaron para no repetir ninguno
            
            //$influencia_usuario = $result -> influencia_usuario;
            $importancia_tweet= $result -> importancia_tweet;
		    //$scrname = $result -> userScreenName;
            //$image = $result -> userImage;
		    
            $keywords = $result->keywords;
            $hashtags = $result->hashtags;
            $usuarios = $result->usuarios;
            
            $tipo = $result->tipo;
		    
            $text.=crear_vista_tweet($id, $importancia_tweet, $keywords, $tipo, $tweet, $usuarios, $hashtags);
            
		    
	    }
	endforeach;
	
	$text.= "</div>";
    //$text.=  "</ul>";
	return $text;
}
?>