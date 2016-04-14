<?php

/*  Clase para contener datos de un tweet   */
class Tweet {
    /*Id del tweet en el sistema de Twitter*/
	public $id;
    /*Texto del tweet*/
	public $text;
    /*Id del tweet al que responde el tweet actual, si es una respuesta*/ 
	public $inReplyToTweetID;
	/*Id del usuario al que responde el tweet actual, si es una respuesta*/ 
	public $inReplyToUserID;
    /* Pais asociado al tweet*/
    public $placeCountry;
    /* Lugar asociado al tweet*/
    public $placeName;             
    /*Cantidad de retweeteos del tweet*/ 
	public $retweetedCount;
    /*Valor booleano verdadero si el tweet fue retweeteado*/ 
	public $retweeted; 
    /*lista de urls del tweet*/
    public $urls;
    /*lista de hashtags del tweet*/
    public $hashtags;
    /*lista menciones a otros usuarios en el tweet*/
    public $userMentions;
    
    //USER properties
    /*Id del usuario que publico el tweet*/
    public $userID;
    /*direccion de la imagen de perfil del usuario */
    public $image;
     /*Cantidad de estados(tweets) publicados por el usuario */
    public $statusesCount;
    /*Cantidad de tweets que este usuario tiene como favoritos*/
    public $favouritesCount;
    /*Cantidad de usuarios seguidos por el usuario*/         
    public $friendsCount;
    /*Cantidad de seguidores del usuario*/
    public $followersCount;
    /*Cantidad de listas publicas a las que pertenece el usuario*/
    public $listedCount;
    /*Ubicacion definida del usuario*/
    public $location;
    /*Nombre elegido por el usuario, no necesariamente su nombre real*/
    public $name;
    /*Id de usuario elegido por el usuario*/
    public $screenName;
    /*Cuando es verdadero, indica que el usuario tiene una cuenta verificada */
    public $verified;
    /*  Si es un RT, contiene el id del tweet original. Si no es RT entonces contiene el propio id  */
    public $id_original;
    
	function Tweet($tweet){
        //recibe un objeto json y carga los atributos de la clase
		//var_dump($tweet);
		$this->id = number_format($tweet['id_str'],0,'.','');
        if(isset($tweet['retweeted_status'])){
            $tweet_text= $tweet['retweeted_status']['text'];
        }else{
		    $tweet_text= $tweet['text'];
        }
        $this->text = str_replace(array("\n","'","\"",'\\'), '', $tweet_text);
        
		$this->inReplyToTweetID = $tweet['in_reply_to_status_id_str'] != null ? number_format($tweet['in_reply_to_status_id_str'],0,'.','') : null;
		$this->inReplyToUserID = $tweet['in_reply_to_user_id_str'] != null ? number_format($tweet['in_reply_to_user_id_str'],0,'.','') : null;
        $this->placeCountry = $tweet['place']['country'];
        $this->placeName = $tweet['place']['name'];
		$this->retweetedCount = $tweet['retweet_count'] != 0 ? $tweet['retweet_count'] : null;
		$this->retweeted = $tweet['retweeted'];
        if(isset($tweet['retweeted_status'])){
            $this->urls = $tweet['retweeted_status']['entities']['urls'];
            $this->hashtags = $tweet['retweeted_status']['entities']['hashtags'];
            $this->userMentions = $tweet['retweeted_status']['entities']['user_mentions'];            
            $this->id_original =  number_format($tweet['retweeted_status']['id_str'],0,'.','');
        }else{
            $this->urls = $tweet['entities']['urls'];
            $this->hashtags = $tweet['entities']['hashtags'];
            $this->userMentions = $tweet['entities']['user_mentions'];
            $this->id_original =  number_format($tweet['id_str'],0,'.','');
        }
        
        $this->userID = number_format($tweet['user']["id_str"],0,'.','');
        $this->favouritesCount =  number_format($tweet['user']["favourites_count"],0,'.',''); 
        $this->image = $tweet['user']["profile_image_url"];        
        $this->statusesCount = $tweet['user']["statuses_count"];        
        $this->friendsCount = $tweet['user']["friends_count"];        
        $this->followersCount = $tweet['user']["followers_count"];        
        $this->listedCount = $tweet['user']["listed_count"]; 
        $this->location = str_replace(array("\n","'","\"",'\\'), '', $tweet['user']["location"]);
        $this->name =  str_replace(array("\n","'","\"",'\\'), '', $tweet['user']["name"]);
        $this->screenName = str_replace(array("\n","'","\"",'\\'), '', $tweet['user']["screen_name"]);
        $this->verified = $tweet['user']["verified"];
	}

}
?>