<?php

/*  Clase para crear el archivo de entrada al razonador escrito en prolog   */
/*  crea una archivo diferente de entrada, de acuerdo al tipo de informacion de los tweets  */
/*   En cuanto a formato, solamente transforma string a numero cuandoe s necesario  */

include_once('/tweet.php');
include_once('/formateador_texto.php');

class Entrada{
	public $ourFileName;
	public $ourFileHandle;
    
    public $ourFileNames;
    public $ourFileHandles;
    
    public $tweet;
    public $palabra_query;
	
	public function abrirArchivo(){
        global $ourFileName,$ourFileHandle;
		global $ourFileNames,$ourFileHandles;
        
		$ourFileName = "Prolog/query.pl";
		$ourFileHandle  = fopen($ourFileName, 'w') or die("can't open file");	
		
        $ourFileNames = array(
            "tweet_text" => "Prolog/tweet_text.pl",
            "tweet_palabras" => "Prolog/tweet_palabras.pl",
            "tweet_userID" => "Prolog/tweet_userID.pl",
            "tweet_inReplyToTweetID" => "Prolog/tweet_inReplyToTweetID.pl",
            "tweet_inReplyToUserID" => "Prolog/tweet_inReplyToUserID.pl",
            "tweet_userMention" => "Prolog/tweet_userMention.pl",
            "tweet_idOriginal" => "Prolog/tweet_idOriginal.pl",
            "tweet_tipo" => "Prolog/tweet_tipo.pl",
            
            "user_image" => "Prolog/user_image.pl",
            "user_statusesCount" => "Prolog/user_statusesCount.pl",
            "user_favouritesCount" => "Prolog/user_favouritesCount.pl",
            "user_friendsCount" => "Prolog/user_friendsCount.pl",
            "user_followersCount" => "Prolog/user_followersCount.pl",
            "user_listedCount" => "Prolog/user_listedCount.pl",
            "user_location" => "Prolog/user_location.pl",
            "user_name" => "Prolog/user_name.pl",
            "user_screenName" => "Prolog/user_screenName.pl",
            "tweet_hashtag" => "Prolog/tweet_hashtag.pl",
            "tweet_placeCountry" => "Prolog/tweet_placeCountry.pl",
            "tweet_placeName" => "Prolog/tweet_placeName.pl",
            "tweet_retweetedCount" => "Prolog/tweet_retweetedCount.pl",
            "user_verified" => "Prolog/user_verified.pl",
        );
        $ourFileHandles = array(); 
        $ourFileHandles["tweet_text"] =fopen($ourFileNames["tweet_text"], 'w') or die("can't open file");
        $ourFileHandles["tweet_palabras"] =fopen($ourFileNames["tweet_palabras"], 'w') or die("can't open file");
        $ourFileHandles["tweet_userID"] =fopen($ourFileNames["tweet_userID"], 'w') or die("can't open file");
        $ourFileHandles["tweet_inReplyToTweetID"] =fopen($ourFileNames["tweet_inReplyToTweetID"], 'w') or die("can't open file");
        $ourFileHandles["tweet_inReplyToUserID"] =fopen($ourFileNames["tweet_inReplyToUserID"], 'w') or die("can't open file");
        $ourFileHandles["tweet_userMention"] =fopen($ourFileNames["tweet_userMention"], 'w') or die("can't open file");        
        $ourFileHandles["tweet_idOriginal"] =fopen($ourFileNames["tweet_idOriginal"], 'w') or die("can't open file");        
        $ourFileHandles["tweet_tipo"] =fopen($ourFileNames["tweet_tipo"], 'w') or die("can't open file");
        
        $ourFileHandles["user_image"] =fopen($ourFileNames["user_image"], 'w') or die("can't open file");
        $ourFileHandles["user_statusesCount"] =fopen($ourFileNames["user_statusesCount"], 'w') or die("can't open file");
        $ourFileHandles["user_favouritesCount"] =fopen($ourFileNames["user_favouritesCount"], 'w') or die("can't open file");
        $ourFileHandles["user_friendsCount"] =fopen($ourFileNames["user_friendsCount"], 'w') or die("can't open file");
        $ourFileHandles["user_followersCount"] =fopen($ourFileNames["user_followersCount"], 'w') or die("can't open file");
        $ourFileHandles["user_listedCount"] =fopen($ourFileNames["user_listedCount"], 'w') or die("can't open file");
        $ourFileHandles["user_location"] =fopen($ourFileNames["user_location"], 'w') or die("can't open file");
        $ourFileHandles["user_name"] =fopen($ourFileNames["user_name"], 'w') or die("can't open file");
        $ourFileHandles["user_screenName"] =fopen($ourFileNames["user_screenName"], 'w') or die("can't open file");
        $ourFileHandles["tweet_hashtag"] =fopen($ourFileNames["tweet_hashtag"], 'w') or die("can't open file");
        $ourFileHandles["tweet_placeCountry"] =fopen($ourFileNames["tweet_placeCountry"], 'w') or die("can't open file");
        $ourFileHandles["tweet_placeName"] =fopen($ourFileNames["tweet_placeName"], 'w') or die("can't open file");
        $ourFileHandles["tweet_retweetedCount"] =fopen($ourFileNames["tweet_retweetedCount"], 'w') or die("can't open file");
        $ourFileHandles["user_verified"] =fopen($ourFileNames["user_verified"], 'w') or die("can't open file");
        
	}
                                           
    public function guardarQuery($search){
        global $ourFileHandle;
        global $palabra_query;
        $palabra_query= $search;
        fwrite($ourFileHandle, 'busqueda("'.$search.'").');
    }
    
	public function guardarTweet($jsonTweet){
				
        $tweet= new Tweet($jsonTweet);    
        
		$this->factTweet($tweet);
        
        $this->factsUser($tweet);
        
    }
	
    private function factTweet($tweet){
       
        $this->factText($tweet->id, $tweet->text);
        
        $this->factInReplyToTweet($tweet->id, $tweet->inReplyToTweetID);
        
        $this->factInReplyToUser($tweet->id, $tweet->inReplyToUserID);
        
        $this->factPlaceCountry($tweet->id, $tweet->placeCountry);
        
        $this->factPlaceName($tweet->id, $tweet->placeName);
        
        $this->factRetweetedCount($tweet->id, $tweet->retweeted, $tweet->retweetedCount);
        
        $this->factHashtags($tweet->id, $tweet->hashtags);
        
        $this->factUserMentions($tweet->id, $tweet->userMentions);
          
        $this->factPalabras($tweet->id, $tweet);
        
        $this->factIdOriginal($tweet->id, $tweet->id_original);
        
        $this->factTipo($tweet->id, $tweet->text);
    }
    
    private function factPlaceCountry($tweetID,$placeCountry){
        global $ourFileHandles;                   
        fwrite($ourFileHandles["tweet_placeCountry"], $placeCountry != null ? "tweet_placeCountry(" .$tweetID. ",'" .$placeCountry. "').\n" : "");    
    }
    
    private function factPlaceName($tweetID,$placeName){
        global $ourFileHandles;                   
        fwrite($ourFileHandles["tweet_placeName"], $placeName != null ? "tweet_placeName(" .$tweetID. ",'" .$placeName. "').\n" : ""); 
    }
    
    private function factText($tweetID, $tweetText){
        global $ourFileHandles;                   
        fwrite($ourFileHandles["tweet_text"], "tweet_text(".$tweetID.",\"".$tweetText."\").\n");
    }
     
    private function factPalabras($tweetID, $tweet){
        global $ourFileHandles;
        
        $palabras= texto_a_palabras($tweet);
        
        foreach($palabras as $pal => $true)
            
            fwrite($ourFileHandles["tweet_palabras"], "tweet_palabra(".$tweetID.",\"".$pal."\").\n");
    }
    
    private function factIdOriginal($tweetID, $tweetIdOriginal){
        global $ourFileHandles;                   
        fwrite($ourFileHandles["tweet_idOriginal"], "tweet_idOriginal(".$tweetID.",".$tweetIdOriginal.").\n");
    } 
    
    private function factTipo($tweetID, $tweetText){
        global $ourFileHandles;
        global $palabra_query;
        fwrite($ourFileHandles["tweet_tipo"],"tweet_tipo(".$tweetID.",".tipoEstructTweet($tweetText,$palabra_query).").\n");
    }
      
	private function factInReplyToTweet($tweetID, $inReplyToTweetID){
        global $ourFileHandles;  
                          
        fwrite($ourFileHandles["tweet_inReplyToTweetID"], $inReplyToTweetID != null ? "tweet_inReplyToTweetID(".$tweetID.",'".$inReplyToTweetID."').\n" : ''); 
	}
	
	private function factInReplyToUser($tweetID, $inReplyToUserID){
        global $ourFileHandles;                    
        fwrite($ourFileHandles["tweet_inReplyToUserID"], $inReplyToUserID!=null ? "tweet_inReplyToUserID(".$tweetID.",".$inReplyToUserID.").\n" : ''); 
	} 
	
    private function factRetweetedCount($tweetID, $retweeted, $retweetedCount){
        global $ourFileHandles; 
         fwrite($ourFileHandles["tweet_retweetedCount"], $retweeted ? "tweet_retweetedCount(".$tweetID.",'".$retweetedCount."').\n" : ''); 
    } 
    
    private function factHashtags($tweetID, $hashtags){
         global $ourFileHandles; 
        
        $result="";
        
        foreach ($hashtags as $indice => $hashtag){
            $result.=  "tweet_hashtag(".$tweetID.",\"".$hashtag['text']."\").\n";
        }
        
        fwrite($ourFileHandles["tweet_hashtag"], $result);
    }
    
	private function factUserMentions($tweetID, $userMentions){
		global $ourFileHandles;
        $result="";
       
        foreach($userMentions as $indice => $userMention){
            $result.=  "tweet_userMention(".$tweetID.",".number_format($userMention['id'],0,'.','').").\n";  
        }
         fwrite($ourFileHandles["tweet_userMention"], $result);
	} 
    
    private function factsUser($tweet){
                
        $this->factUserID($tweet->id, $tweet->userID);
        
        $this->factImage($tweet->userID, $tweet->image); 
        
        $this->factStatusesCount($tweet->userID, $tweet->statusesCount); 
        
        $this->factFavouritesCount($tweet->userID, $tweet->favouritesCount); 
               
        $this->factFriendsCount($tweet->userID, $tweet->friendsCount);  
               
        $this->factFollowersCount($tweet->userID, $tweet->followersCount);  
               
        $this->factListedCount($tweet->userID, $tweet->followersCount);
         
        $this->factLocation($tweet->userID, $tweet->location); 
        
        $this->factName($tweet->userID, $tweet->name);
         
        $this->factScreenName($tweet->userID, $tweet->screenName); 
                
        $this->factVerified($tweet->userID, $tweet->verified);       
    }       
    
    private function factUserID($tweetID, $userID){
        global $ourFileHandles;                   
        fwrite($ourFileHandles["tweet_userID"], "tweet_userID(".$tweetID.",".$userID.").\n"); 
    } 
    
    private function factImage($userID, $image){           
        global $ourFileHandles;                   
        fwrite($ourFileHandles["user_image"], "user_image(".$userID.",'".$image."').\n"); 
    }  
        
    private function factStatusesCount($userID, $statusesCount){           
        global $ourFileHandles;                   
        fwrite($ourFileHandles["user_statusesCount"], "user_statusesCount(".$userID.",".$statusesCount.").\n");
    }  
    
    private function factFavouritesCount($userID, $favouritesCount){
        global $ourFileHandles;                   
        fwrite($ourFileHandles["user_favouritesCount"], "user_favouritesCount(".$userID.",".$favouritesCount.").\n"); 
    } 
    
    private function factFriendsCount($userID, $friendsCount){
        global $ourFileHandles;                   
        fwrite($ourFileHandles["user_friendsCount"], "user_friendsCount(".$userID.",".$friendsCount.").\n");
    } 
    
    private function factFollowersCount($userID, $followersCount){
        global $ourFileHandles;                   
        fwrite($ourFileHandles["user_followersCount"], "user_followersCount(".$userID.",".$followersCount.").\n"); 
    } 
    
    private function factListedCount($userID, $listedCount){
        global $ourFileHandles;                   
        fwrite($ourFileHandles["user_listedCount"], "user_listedCount(".$userID.",".$listedCount.").\n");
    }
     
    private function factLocation($userID, $location){
        global $ourFileHandles;                   
        fwrite($ourFileHandles["user_location"], $location != null ? "user_location(".$userID.",'".$location."').\n": "");    
    } 
    
    private function factName($userID, $name){
        global $ourFileHandles;                   
        fwrite($ourFileHandles["user_name"], "user_name(".$userID.",'".$name."').\n");    
    } 
    
    private function factScreenName($userID, $screenName){
        global $ourFileHandles;                   
        fwrite($ourFileHandles["user_screenName"], "user_screenName(".$userID.",'".$screenName."').\n");    
    } 
    
    private function factVerified($userID, $verified){
        global $ourFileHandles;                   
        fwrite($ourFileHandles["user_verified"], $verified ? "user_verified(".$userID.").\n" : '');    
    } 
    	
	public function cerrarArchivo(){
        global $ourFileHandle;
		global $ourFileHandles;
        
        fclose($ourFileHandle);
        fclose($ourFileHandles["tweet_text"]);
        fclose($ourFileHandles["tweet_userID"]);
        fclose($ourFileHandles["tweet_inReplyToTweetID"]);
        fclose($ourFileHandles["tweet_inReplyToUserID"]);
        fclose($ourFileHandles["tweet_userMention"]);
        fclose($ourFileHandles["tweet_idOriginal"]);
        fclose($ourFileHandles["tweet_tipo"]);
        
        fclose($ourFileHandles["user_statusesCount"]);
        fclose($ourFileHandles["user_favouritesCount"]);
        fclose($ourFileHandles["user_friendsCount"]);
        fclose($ourFileHandles["user_followersCount"]);
        fclose($ourFileHandles["user_listedCount"]);
        fclose($ourFileHandles["user_location"]);
        fclose($ourFileHandles["user_name"]);
        fclose($ourFileHandles["user_screenName"]);
        fclose($ourFileHandles["tweet_hashtag"]);
        fclose($ourFileHandles["tweet_placeCountry"]);
        fclose($ourFileHandles["tweet_placeName"]);
        fclose($ourFileHandles["tweet_retweetedCount"]);
		fclose($ourFileHandles["user_verified"]);
     }
}
?>