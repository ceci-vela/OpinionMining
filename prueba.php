<?php
    $texto="hoooola   hoy nos vamos               junnntos a las 12 ";
     
    $emoticones= file("emoticones.txt",FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach($emoticones as $key => $emoticon){
        $esta= (stripos($texto,$emoticon)!== false);
        if($esta){break;}
    }
    
    
  //  $texto= preg_filter($emoticones, '*', $texto);
    
     var_dump($esta);
   
?>
