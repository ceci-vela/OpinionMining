
%%% MI CODIGO para separar string en lista de palabras

texto_a_palabras(Texto,Palabras):- 
	limpiar_multi_espacios(Texto,Texto2),
	split(Texto2," ",Lista), 
	codigo_a_string(Lista,Palabras).

codigo_a_string([LHead|LTail],[PHead|PTail]):-
	codigo_a_string(LTail,PTail),
	string_to_list(PHead,LHead).
codigo_a_string([],[]):-!.

% split(+OldString,+Pattern,-ListStrings)

split(OldString,Pattern,ListStrings):-
	split(OldString,Pattern,[],ListStrings).

% split(+OldString,+Pattern,+PartialStart,-ListStrings).

split([],_Pattern,[],[]):- !.
split([],_Pattern,PartialStart,[PartialStart]):- !.
split(OldString,Pattern,[],[RestStrings]):-
	startsWith(OldString,Pattern,Rest),
	!,
	split(Rest,Pattern,[],RestStrings).
split(OldString,Pattern,PartialStart,[PartialStart|RestStrings]):-
	startsWith(OldString,Pattern,Rest),
	!,
	split(Rest,Pattern,[],RestStrings).
split([H|T],Pattern,PartialStart,RestStrings):-
	!,
	append(PartialStart,[H],PartialStartTemp),
	split(T,Pattern,PartialStartTemp,RestStrings).

% startsWith(OldString,Pattern,Rest)

startsWith(OldString,[],OldString):-
	!.
startsWith([H|TOldString],[H|T],Rest):-
	!,
	startsWith(TOldString,T,Rest).


%%% MI CODIGO para limpiar multiples espacios seguidos en la cadena
%limpiar_multi_espacios/2 
%Limpia subcadenas de multiples espacios en el string de estrada, dejando un solo espacio 
limpiar_multi_espacios([],[]):- !.
limpiar_multi_espacios([HString|TString],[HString|TSLimpia]):-
	HString =:= " ", 
	limpiar_espacios_siguientes(TString,TSParcial),
	limpiar_multi_espacios(TSParcial,TSLimpia), !.
limpiar_multi_espacios([HString|TString],[HString|TSLimpia]):-
	HString =\= " ", 
	limpiar_multi_espacios(TString,TSLimpia),!.

%limpiar_espacios_siguientes/2
%si el string de entrada comienza con un espacio, consume los espacios inmediatamente siguientes
limpiar_espacios_siguientes([],[]) :- !.
limpiar_espacios_siguientes([HString|TString],TSLimpia):-
	HString =:= " ", 
	limpiar_espacios_siguientes(TString,TSLimpia),
	!.
limpiar_espacios_siguientes([HString|TString],[HString|TString]):-
	HString =\= " ", !.