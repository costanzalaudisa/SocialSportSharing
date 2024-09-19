USE UNIBOSSS;

/* Caricare file con Corsi di Studio*/
LOAD DATA INFILE 'C:/Users/Costanza/source/repos/SocialSportSharing/database/cds.txt' INTO TABLE CDS;

/*Caricare file categoria */
LOAD DATA INFILE 'C:/Users/Costanza/source/repos/SocialSportSharing/database/categoria.txt' INTO TABLE CATEGORIA;

/*Caricare file impianto*/
LOAD DATA INFILE 'C:/Users/Costanza/source/repos/SocialSportSharing/database/impianto.txt' INTO TABLE IMPIANTO;

/* Creo l'utente premium Gestore:
	- non può essere iscritto a eventi sportivi */
CALL NuovoUtente("Gestore","5f4dcc3b5aa765d61d8327deb882cf99","admin","admin", "0000000000","0000","Bologna","0000000000000","utentegenerico.png","Informatica per il management");
INSERT INTO UP VALUES ("Gestore");

/* Creo un popolamento "base" per la piattaforma */
 #Inserisco nuovi utenti
CALL NuovoUtente("Dolfin","6e6bc4e49dd477ebc98ef4046c067b5f","Silvia","Di Pietro","0000111112","1993","Firenze","3333344444","utentegenerico.png", "Biotecnologie animali");
CALL NuovoUtente("Tia13","740f012345d61ecd008e19690ec193b7","Mattia","Caldara","0000111113","1994","Bergamo","3333355555","utentegenerico.png","Lettere");
CALL NuovoUtente("Agny","6e6bc4e49dd477ebc98ef4046c067b5f","Agnese","Innocente","0000111116","1993","Firenze","3333388888","utentegenerico.png","Lettere");
CALL NuovoUtente("mMCg","7d0a652939fef190954db21b4fb37bcf","Matt","McGorry","0000111117","1987","Milano","3333399999","utentegenerico.png","Giurisprudenza");
CALL NuovoUtente("platypus","0ffab42260504ca68e08b5be7a778897","Jack","Falahee","0000111118","1989","Cesena","3333111111","utentegenerico.png","Tecnologie alimentari");
CALL NuovoUtente("Purple","f156e7995d521f30e6c59a3d6c75e1e5","Viola","Davis","0000111120","1997","Cesena","3333444444","utentegenerico.png","Tecnologie alimentari");
CALL NuovoUtente("agp","f0c0976535cfaf33574b5175e072bce9","Matteo","Perro","0152485859","1998","Como","3527859487","utentegenerico.png","Viticoltura ed enologia");

CALL NuovoUtente("Mag","b63c34932939e7cbb8811b0a5fda2bf8","Hordur","Magnusson","0000000001","1993","Parma","3334445556","utentegenerico.png","Astronomia");
CALL NuovoUtente("Lala","87cf962b35146c21d0dd7f84594662b6","Emma", "Stone", "0000000002","1994","Roma","3334445557","utentegenerico.png","Cinema, televisione e produzione multimediale");
CALL NuovoUtente("Leia", "5af2904a4c73f4d69e3b297462440c15","Carrie","Fisher","0000000003","1993","Milano","3334445558","utentegenerico.png","Relazioni internazionali");
CALL NuovoUtente("Han","26c04769d2607a2234d4329b7630ed35", "Harrison","Ford", "0000000004","1991","Bologna","3334445559","utentegenerico.png","Economia dell'impresa");
CALL NuovoUtente("Luke", "d6cc59c46b0b3f2cf2fef9360d96776e", "Mark", "Hamill","0000000005", "1993", "Milano", "3334445559", "utentegenerico.png", "Astronomia");
CALL NuovoUtente("Gael","4fb845c67d91bcb3178498fc6fe1fedc","Gael", "Garcia Bernal","0000000006", "1991","Cesena", "3334445551","utentegenerico.png","Relazioni internazionali");
CALL NuovoUtente("Gun","b63c34932939e7cbb8811b0a5fda2bf8","Aron","Gunnarsson","0000000007","1993","Parma","3334446666","utentegenerico.png","Lettere");


#Inserisco degli eventi con esito.
#EVENTO 1 BASKET
CALL NuovoES("2017-04-21","Basket","Terrapieno","Gestore");  

CALL IscriversiEvento("2017-04-20","Mag","1","Gestore", "Giocatore");
CALL IscriversiEvento("2017-04-20","Lala","1","Gestore", "Giocatore");
CALL IscriversiEvento("2017-04-20","Gael","1","Gestore", "Giocatore");
CALL IscriversiEvento("2017-04-20","Leia","1","Gestore", "Giocatore");
CALL IscriversiEvento("2017-04-20","Luke","1","Gestore", "Giocatore");
CALL IscriversiEvento("2017-04-20","Han","1","Gestore", "Giocatore");
CALL IscriversiEvento("2017-04-20","mMCg","1","Gestore","Giocatore");
CALL IscriversiEvento("2017-04-20","Purple","1","Gestore","Giocatore");
CALL IscriversiEvento("2017-04-20","Agny","1","Gestore","Giocatore");
CALL IscriversiEvento("2017-04-20","Tia13","1","Gestore","Giocatore");
CALL IscriversiEvento("2017-04-20","Gun","1","Gestore","Arbitro");

CALL Approvazione("Gestore","Mag","1","1");
CALL Approvazione("Gestore","Lala","1","1");
CALL Approvazione("Gestore","Gael","1", "1");
CALL Approvazione("Gestore","Leia","1","1");
CALL Approvazione("Gestore","Luke","1","1");
CALL Approvazione("Gestore","Han","1","1");
CALL Approvazione("Gestore","mMCg","1","1");
CALL Approvazione("Gestore","Purple","1","1");
CALL Approvazione("Gestore","Agny","1","1");
CALL Approvazione("Gestore","Tia13","1","1");
CALL Approvazione("Gestore", "Gun","1","1");

CALL NuovaSquadra("Pino");
CALL NuovaSquadra("Orchidea");

CALL NuovoEsitoGruppo("1","2017-04-22","Gestore","70", "Pino","80","Orchidea", "Gun");

CALL ComposizioneSquadra("Pino","Mag","1","25");
CALL ComposizioneSquadra("Pino","Lala","1","20");
CALL ComposizioneSquadra("Pino","Gael","1","10");
CALL ComposizioneSquadra("Pino","mMCg","1","10");
CALL ComposizioneSquadra("Pino","Purple","1","5");

CALL ComposizioneSquadra("Orchidea","Leia","1","22");
CALL ComposizioneSquadra("Orchidea","Han","1","23");
CALL ComposizioneSquadra("Orchidea","Luke","1","15");
CALL ComposizioneSquadra("Orchidea","Agny","1","10");
CALL ComposizioneSquadra("Orchidea","Tia13","1","10");

CALL NuovaValutazione("2017-04-23","9","Bravissimo! Hai giocato una bella partita.","Han","1","Mag","1");

#EVENTO 2 TENNIS
CALL NuovoES("2017-04-22","Tennis","Palacus","Gestore");#2

CALL IscriversiEvento("2017-04-21","Leia","2","Gestore","Giocatore");
CALL IscriversiEvento("2017-04-21","Luke","2","Gestore","Giocatore");
CALL IscriversiEvento("2017-04-21","Gun","2","Gestore","Arbitro");

CALL Approvazione("Gestore","Leia","2","1");
CALL Approvazione("Gestore","Luke","2","1");
CALL Approvazione("Gestore","Gun","2","1");

CALL NuovoEsitoSingolo("2","2017-04-23","Gestore","3","Leia","2","Luke","03:04:05", "Gun");

CALL NuovaValutazione("2017-03-22","8","Complimenti per la vittoria, aspetto la rivincita.","Leia","2","Luke","2");

#EVENTO 3 CALCIO
CALL NuovoES("2017-04-23","Calcio","Record","Gestore"); 

CALL IscriversiEvento("2017-04-22","Mag","3","Gestore","Giocatore");
CALL IscriversiEvento("2017-04-22","Lala","3","Gestore","Giocatore");
CALL IscriversiEvento("2017-04-22","Gael","3","Gestore","Giocatore");
CALL IscriversiEvento("2017-04-22","Leia","3","Gestore","Giocatore");
CALL IscriversiEvento("2017-04-22","Luke","3","Gestore","Giocatore");
CALL IscriversiEvento("2017-04-22","Han","3","Gestore","Giocatore");
CALL IscriversiEvento("2017-04-22","mMCg","3","Gestore","Giocatore");
CALL IscriversiEvento("2017-04-22","Purple","3","Gestore","Giocatore");
CALL IscriversiEvento("2017-04-22","Agny","3","Gestore","Giocatore");
CALL IscriversiEvento("2017-04-22","Gun","3","Gestore","Giocatore");

CALL Approvazione("Gestore","Mag","3","1");
CALL Approvazione("Gestore","Lala","3","1");
CALL Approvazione("Gestore","Gael","3","1");
CALL Approvazione("Gestore","Leia","3","1");
CALL Approvazione("Gestore","Luke","3","1");
CALL Approvazione("Gestore","Han","3","1");
CALL Approvazione("Gestore","mMCg","3","1");
CALL Approvazione("Gestore","Purple","3","1");
CALL Approvazione("Gestore","Agny","3","1");
CALL Approvazione("Gestore","Gun","3","1");

CALL NuovaSquadra("Cactus");
CALL NuovaSquadra("Baobab");

CALL NuovoEsitoGruppo("3","2017-04-24","Gestore","4","Cactus","5","Baobab", "NULL");

CALL ComposizioneSquadra("Cactus","Mag","3","0");
CALL ComposizioneSquadra("Cactus","Lala","3","1");
CALL ComposizioneSquadra("Cactus","Gael","3","1");
CALL ComposizioneSquadra("Cactus","mMCg","3","2");
CALL ComposizioneSquadra("Cactus","Purple","3","0");

CALL ComposizioneSquadra("Baobab","Leia","3","2");
CALL ComposizioneSquadra("Baobab","Han","3","0");
CALL ComposizioneSquadra("Baobab","Luke","3","0");
CALL ComposizioneSquadra("Baobab","Agny","3","0");
CALL ComposizioneSquadra("Baobab","Gun","3","3");

CALL NuovaValutazione("2017-04-26","9","Che bello giocare insieme a te!","Gun","3","Leia","3");


#Inserisco degli eventi senza esito
#EVENTO 4 BASKET
CALL NuovoES("2017-04-25","Basket","Palacus","Gestore");

CALL IscriversiEvento("2017-04-24","Mag","4","Gestore","Giocatore");
CALL IscriversiEvento("2017-04-24","Lala","4","Gestore","Giocatore");
CALL IscriversiEvento("2017-04-24","Gael","4","Gestore","Giocatore");
CALL IscriversiEvento("2017-04-24","Leia","4","Gestore","Giocatore");
CALL IscriversiEvento("2017-04-24","Luke","4","Gestore","Giocatore");
CALL IscriversiEvento("2017-04-24","Han","4","Gestore","Giocatore");
CALL IscriversiEvento("2017-04-24","mMCg","4","Gestore","Giocatore");
CALL IscriversiEvento("2017-04-24","Purple","4","Gestore","Giocatore");
CALL IscriversiEvento("2017-04-24","Agny","4","Gestore","Giocatore");
CALL IscriversiEvento("2017-04-24","Gun","4","Gestore","Giocatore");
CALL IscriversiEvento("2017-04-24","agp","4","Gestore","Arbitro");

CALL Approvazione("Gestore","Mag","4","1");
CALL Approvazione("Gestore","Lala","4","1");
CALL Approvazione("Gestore","Gael","4","1");
CALL Approvazione("Gestore","Leia","4","1");
CALL Approvazione("Gestore","Luke","4","1");
CALL Approvazione("Gestore","Han","4","1");
CALL Approvazione("Gestore","mMCg","4","1");
CALL Approvazione("Gestore","Purple","4","1");
CALL Approvazione("Gestore","Agny","4","1");
CALL Approvazione("Gestore","Gun","4","1");
CALL Approvazione("Gestore","agp","4","1");

# EVENTO 5 TENNIS
CALL NuovoES("2017-04-21","Tennis","Preziosi","Gestore");#14 

CALL IscriversiEvento("2017-04-21","Leia","5","Gestore","Giocatore");
CALL IscriversiEvento("2017-04-21","Han","5","Gestore","Giocatore");

CALL Approvazione("Gestore","Leia","5","1");
CALL Approvazione("Gestore","Han","5","1");

#EVENTO 6 CALCIO
CALL NuovoES("2017-04-28","Calcio","Record","Gestore"); 

CALL IscriversiEvento("2017-04-27","Mag","6","Gestore","Giocatore");
CALL IscriversiEvento("2017-04-27","Lala","6","Gestore","Giocatore");
CALL IscriversiEvento("2017-04-27","Gael","6","Gestore","Giocatore");
CALL IscriversiEvento("2017-04-27","Leia","6","Gestore","Giocatore");
CALL IscriversiEvento("2017-04-27","Luke","6","Gestore","Giocatore");
CALL IscriversiEvento("2017-04-27","Han","6","Gestore","Giocatore");
CALL IscriversiEvento("2017-04-27","mMCg","6","Gestore","Giocatore");
CALL IscriversiEvento("2017-04-27","Purple","6","Gestore","Giocatore");
CALL IscriversiEvento("2017-04-27","Agny","6","Gestore","Giocatore");
CALL IscriversiEvento("2017-04-27","Gun","6","Gestore","Giocatore");

CALL Approvazione("Gestore","Mag","6","1");
CALL Approvazione("Gestore","Lala","6","1");
CALL Approvazione("Gestore","Gael","6","1");
CALL Approvazione("Gestore","Leia","6","1");
CALL Approvazione("Gestore","Luke","6","1");
CALL Approvazione("Gestore","mMCg","6","1");
CALL Approvazione("Gestore","Purple","6","1");
CALL Approvazione("Gestore","Agny","6","1");
CALL Approvazione("Gestore","Gun","6","1");
CALL Approvazione("Gestore","Han","6","1");


#Inserisco degli eventi a cui è possibile iscriversi
CALL NuovoES("2017-05-10","Calcio","Record","Gestore"); 

CALL IscriversiEvento("2017-05-08","Mag","7","Gestore","Arbitro");
CALL Approvazione("Gestore","Mag","7","1");

CALL NuovoES("2017-05-11","Tennis","Record","Gestore");

CALL IscriversiEvento("2017-05-01","Leia","8","Gestore","Giocatore");
CALL IscriversiEvento("2017-05-01","Gun","8","Gestore","Giocatore");
CALL Approvazione("Gestore","Leia","8","1");
CALL Approvazione("Gestore","Gun","8","1");

CALL NuovoES("2017-05-10","Basket","Preziosi","Gestore"); 

CALL NuovoES("2017-05-12","Basket","Terrapieno","Gestore");
CALL NuovoES("2017-05-13","Tennis","Preziosi","Gestore"); 
CALL NuovoES("2017-05-24","Calcio","Record","Gestore");  


# Inserisco post di esempio nel forum Calcio - Astronomia 
CALL NuovoPost("2017-04-21","Ciao! Sono Mark, c' e' qualche appassionato di calcio con cui parlare?","stroom.jpg","Luke","Astronomia", "Calcio");
CALL NuovoPost("2017-04-22","Ciao Mark! Io amo il calcio, qual e' la tua squadra preferita?", "calcio.png","Mag","Astronomia", "Calcio");