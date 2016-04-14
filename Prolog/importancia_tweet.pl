:- dynamic n/2.
:- dynamic n_hashtag/2.
:- dynamic ambas/3.
:- dynamic ambas_hashtag/3.
:- dynamic n/3.
:- dynamic n_hashtag/3.
:- dynamic c/3.
:- dynamic c_hashtag/3.

%tolower(+palabra,-palabra_minuscula)
%asocia un string palabra con su string correpondiente en letras minusculas
tolower([], []).
tolower([Upper|UpperTail], [Lower|LowerTail]) :-
	to_lower(Upper,Lower),
	tolower(UpperTail, LowerTail).

%lista_nuevo_elto(+elto,+lista,-lista_nuevo_miembro)
%asocia un elemento y una lista a una lista nueva que contiene a elto, si elto no esta en lista
lista_nuevo_elto(Elto,Lista,[]):-
	member(Elto,Lista),!.
lista_nuevo_elto(Elto,_Lista,[Elto]).
	
%limpiar_dupli(Lista_dupli,Lista_sin_dupli)
%asocia una lista con una lista que contiene los mismos elementos pero sin duplicados
limpiar_dupli([],[]):-!.
limpiar_dupli([H|T],L):-
	limpiar_dupli(T,Lista),
	lista_nuevo_elto(H,Lista,ListaC),
	append(ListaC,Lista,L).

%contiene_keyword(+Tid,+Keyword)
%asocia un tweet con sus palabras, pero en minusculas	
contiene_keyword(Tid,Keyword):-
	tweet_palabra(Tid,K),
	tolower(K,Keyword).

%contiene_keyword(+Tid,+Keyword)
%asocia un tweet con sus palabras, pero en minusculas, incluyendo hashtags    
contiene_keyword(Tid,Keyword):-
    tweet_hashtag(Tid,K),
    tolower(K,Keyword).


	
%contiene_hashtag(+Tid,+Hashtag)
%asocia un tweet con sus hashtags, pero en minusculas	
contiene_hashtag(Tid,Hashtag):-
	tweet_hashtag(Tid,H),
	tolower(H,Hashtag).
	
n(Keyword,Count):- 
	tolower(Keyword,K),
	findall(Tid,contiene_keyword(Tid,K),L),
	limpiar_dupli(L,Lista),
    length(Lista,Count),
    asserta(n(Keyword,Count):-!).
	
n_hashtag(Hashtag,Count):- 
	tolower(Hashtag,H),
	findall(Tid,contiene_hashtag(Tid,H),L),
	limpiar_dupli(L,Lista),
    length(Lista,Count),
    asserta(n_hashtag(Hashtag,Count):-!).
	
ambas(Tid,Ki,Kj):-
	tolower(Ki,Ki2),
	tolower(Kj,Kj2),
	contiene_keyword(Tid,Ki2),
	contiene_keyword(Tid,Kj2),
    asserta(ambas(Tid,Ki,Kj):-!).

ambas_hashtag(Tid,Hi,Hj):-
	tolower(Hi,Hi2),
	tolower(Hj,Hj2),
	contiene_hashtag(Tid,Hi2),
	contiene_hashtag(Tid,Hj2),
    asserta(ambas_hashtag(Tid,Hi,Hj):-!).    

n(Ki,Kj,Count):-
	findall(Tid,ambas(Tid,Ki,Kj),L),
	limpiar_dupli(L,Lista),
    length(Lista,Count),
    asserta(n(Ki,Kj,Count):-!).

n_hashtag(Hi,Hj,Count):-
	findall(Tid,ambas_hashtag(Tid,Hi,Hj),L),
	limpiar_dupli(L,Lista),
    length(Lista,Count),
    asserta(n_hashtag(Hi,Hj,Count):-!).
	
c(Ki,Kj,Factor):-
	n(Ki,Ni),
	n(Kj,Nj),
	n(Ki,Kj,Nij),
	Divisor is Ni + Nj - Nij,
	Factor is Nij / Divisor,
    asserta(c(Ki,Kj,Factor):-!).
	
c_hashtag(Hi,Hj,Factor):-
	n_hashtag(Hi,Ni),
	n_hashtag(Hj,Nj),
	n_hashtag(Hi,Hj,Nij),
	Divisor is Ni + Nj - Nij,
	Factor is Nij / Divisor,
    asserta(c_hashtag(Hi,Hj,Factor):-!).
	
factor(Tid,Factor):-
	busqueda(Query),
	tolower(Query,Q),
	contiene_keyword(Tid,K),
	K \= Q,
	c(Q,K,C),
	Factor is 1 - C.

prod([],1).
prod([H|T],P):-
	prod(T,Prod),
	P is H * Prod.
	
importancia_tweet(Tid,Importancia):-
	findall(Cil,factor(Tid,Cil),L),
    prod(L,I),
	Importancia is 1 - I.
	