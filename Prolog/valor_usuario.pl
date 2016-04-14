maximo(A,B,A):- A >= B.
maximo(A,B,B):- A < B.

valor_usuario(US,0):- user_followersCount(US,0).
valor_usuario(US,X):- user_followersCount(US,Y), user_friendsCount(US,Z), 
						maximo(Y,Z,M), 
						Resta is Y-Z, 
						Coci is Resta / M, 
						X is Coci + 1 .

tweetsxfollowers(US,X):- user_statusesCount(US,X), user_followersCount(US,0).
						
tweetsxfollowers(US,X):- user_statusesCount(US,Y), user_followersCount(US,Z),
						X is Y / Z.
						
influencia_usuario(US,X):- valor_usuario(US,Y), tweetsxfollowers(US,Z),
						X is (Y / Z) + Y, !.