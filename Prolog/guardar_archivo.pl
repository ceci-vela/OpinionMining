%:-consult(tweets).

:-dynamic original_text/2.
:-dynamic original_palabra/2.
:-dynamic original_hashtag/2.
:-dynamic original_tipo/2.

:-consult(tweet_text).
:-consult(tweet_palabras).
:-consult(query).

:-consult(tweet_userID).
%:-consult(tweet_inReplyToTweetID).
%:-consult(tweet_inReplyToUserID).
:-consult(tweet_userMention).
:-consult(tweet_idOriginal).
:-consult(user_image).
:-consult(user_statusesCount).
:-consult(user_favouritesCount).
:-consult(user_friendsCount).
:-consult(user_followersCount).
%:-consult(user_listedCount).
%:-consult(user_location).
:-consult(user_name).
:-consult(user_screenName).
:-consult(tweet_hashtag).
%:-consult(tweet_placeCountry).
%:-consult(tweet_placeName).
:-consult(tweet_retweetedCount).
%:-consult(user_verified).
:-consult(tweet_tipo).

:-consult(valor_usuario).
:-consult(importancia_tweet).

xml_escribir_id(Stream,U):-
	write(Stream,'<id>'),
	write(Stream,U),
	write(Stream,'</id>'),
    nl(Stream).

xml_escribir_tipo(Stream,Tipo):-
	write(Stream,'		<tipo>'),
	write(Stream,Tipo),
	write(Stream,'</tipo>'),
    nl(Stream).	
	
	
xml_escribir_text(Stream,Text):-
	write(Stream,'      <text>'),
	string_to_list(String,Text),
	write(Stream,String),
	write(Stream,'</text>'),
    nl(Stream).

xml_escribir_id_tweet_original(Stream,TidO):-
    write(Stream,'      <id_original>'),
    write(Stream,TidO),
    write(Stream,'</id_original>'),
    nl(Stream).

xml_escribir_keywords(Stream,Tid):-  
    write(Stream,'      <keywords>'),
    nl(Stream),                    
    forall(
        original_palabra(Tid,Keyword),
        (
             write(Stream,'         <keyword>'),
             write(Stream,'             <word>'),             
             string_to_list(String,Keyword),
             write(Stream, String),
             write(Stream,'</word>'),             
             write(Stream,'             <importancia>'),
             busqueda(Query),
             tolower(Keyword,K),
             c(Query,K,Factor),
             write(Stream,Factor),
             write(Stream,'</importancia>'),
             write(Stream,'</keyword>'),!,
             nl(Stream)
        )
    ),
    write(Stream,'      </keywords>'),
    nl(Stream).

xml_escribir_hashtags(Stream,Tid):-  
    write(Stream,'      <hashtags>'),
    nl(Stream),                    
    forall(
        original_hashtag(Tid,Hashtag),
        (
             write(Stream,'         <hashtag>'),
             write(Stream,'             <word>'),             
             string_to_list(String,Hashtag),
             write(Stream, String),
             write(Stream,'</word>'),             
             write(Stream,'             <importancia>'),
             busqueda(Query),
             tolower(Hashtag,H),
             c_hashtag(Query,H,Factor),
             write(Stream,Factor),
             write(Stream,'</importancia>'),
             write(Stream,'</hashtag>'),!,
             nl(Stream)
        )
    ),
    write(Stream,'      </hashtags>'),
    nl(Stream).
	
xml_escribir_userScreenName(Stream,Uscrname):-
	write(Stream,'      <userScreenName>'),
	write(Stream,Uscrname),
	write(Stream,'</userScreenName>').
	
xml_escribir_userImage(Stream,Image):-
	write(Stream,'      <userImage>'),
	write(Stream,Image),
	write(Stream,'</userImage>').
	
xml_escribir_influencia_usuario(Stream,Inf):-
	write(Stream,'      <influencia_usuario>'),
	write(Stream,Inf),
	write(Stream,'</influencia_usuario>').

xml_escribir_id_usuario(Stream,Uid):-
	write(Stream,'      <id_usuario>'),
	write(Stream,Uid),
	write(Stream,'</id_usuario>').

xml_escribir_usuarios(Stream, TidO):-
	write(Stream, '      <usuarios>'),
	nl(Stream),
	forall(
		tweet_idOriginal(Id, TidO),
		(
			write(Stream, '      	<usuario>'),
			tweet_userID(Id, Uid),
			xml_escribir_id_usuario(Stream, Uid),
			influencia_usuario(Uid, Inf), 
			xml_escribir_influencia_usuario(Stream, Inf),
			user_screenName(Uid, Name),
			xml_escribir_userScreenName(Stream, Name),
			user_image(Uid, Image),
			xml_escribir_userImage(Stream, Image),
			write(Stream, '      	</usuario>'),
			nl(Stream)
		)
	), 
	write(Stream, '      </usuarios>'),
	nl(Stream).
    
xml_escribir_importancia_tweet(Stream,Imp):-
    write(Stream,'      <importancia_tweet>'),
    write(Stream,Imp),
    write(Stream,'</importancia_tweet>'),
    nl(Stream).

	
xml_escribir_tweet(Stream,TidO,Text,Imp,Tipo):-
	write(Stream,'<tweet>'),
	%xml_escribir_id(Stream,U),
    xml_escribir_id_tweet_original(Stream,TidO),
    xml_escribir_tipo(Stream,Tipo),
	%xml_escribir_userScreenName(Stream,Uscrname),
	%xml_escribir_userImage(Stream,Image),
	%xml_escribir_influencia_usuario(Stream,Inf),
    xml_escribir_importancia_tweet(Stream,Imp),
	xml_escribir_text(Stream,Text),
	xml_escribir_usuarios(Stream, TidO),
    xml_escribir_keywords(Stream,TidO),
	xml_escribir_hashtags(Stream,TidO),
	write(Stream,'  </tweet>'),
	nl(Stream).

xml_escribir_tweets(Stream):-
	write(Stream,'<tweets>'),
	nl(Stream),	
	forall(
		original_text(TidO,Text),
		(	
            tweet_idOriginal(Tid,TidO),
			%tweet_userID(Tid,U),
			original_tipo(TidO,Tipo),
		    %user_image(U,Image),
			%user_screenName(U,Uscrname),
			%influencia_usuario(U,Inf),
            importancia_tweet(Tid,Imp),
            
			write(Stream,'	'),
			%xml_escribir_tweet(Stream,U,Image,Inf,Imp,Uscrname,Text,Tid,TidO,Tipo),!
			xml_escribir_tweet(Stream,TidO,Text,Imp,Tipo),!
		)
	),
	write(Stream,'</tweets>'),
	nl(Stream).

crear_archivo :-  
		open('argumentacion.xml',write,Stream),
        xml_escribir_tweets(Stream),
        close(Stream). 

%obtiene y agrega todos los tweets originales como hechos al programa	
obtencion_tweets_originales:-
	forall(
		bagof(Id, tweet_idOriginal(Id, IdO), _IdOs),
		(
			tweet_idOriginal(Id, IdO),
			tweet_text(Id, Text),
			asserta(original_text(IdO, Text)),
			tweet_tipo(Id,Tipo),
			asserta(original_tipo(IdO, Tipo)),
			forall(
				tweet_palabra(Id, Pal),
				asserta(original_palabra(IdO, Pal))
			),
			forall(
				tweet_hashtag(Id, Hash),
				asserta(original_hashtag(IdO, Hash))
			)
		)
	).
	
main :-
	obtencion_tweets_originales,
	crear_archivo,!,nl.

%:- main.		
:- main,!,halt.
