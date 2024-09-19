USE UNIBOSSS;

/* VISUALIZZAZIONE */

/* Visualizza, gli eventi chiusi di cui non è ancora stato inserito l'esito */
DELIMITER $
CREATE VIEW EsitiNonInseriti(IdEvento,Data, Categoria, Impianto, UP) AS
	SELECT Id, Data, CategoriaSport, NomeImpianto, UserUP 
	FROM ES 
	WHERE (ES.Stato = "CHIUSO") AND NOT EXISTS (SELECT * 
												FROM ESITO
												WHERE ES.Id = ESITO.IdEvento);
$ 

/* Visualizzare, per ciascun utente, la lista degli ES a cui si è partecipato */
DELIMITER $
CREATE VIEW ListaESUtente(Utente, Id, Data, Categoria, Impianto) AS
	SELECT UserUtente, Id, ES.Data, CategoriaSport, NomeImpianto
	FROM ISCRIZIONE JOIN ES ON (IdEvento = Id)
	WHERE (ISCRIZIONE.Stato = 'CONFERMATO');	
$

/* Visualizza la lista delle iscrizioni */
DELIMITER $
CREATE VIEW IscrizioniUtente(UserUtente, IdEvento, NomeImpianto, CategoriaSport, Data, Stato) AS
	SELECT UserUtente, IdEvento, NomeImpianto, CategoriaSport, ES.Data, ISCRIZIONE.Stato
	FROM ISCRIZIONE, ES
	WHERE (ISCRIZIONE.IdEvento = ES.Id)
$

/* Visualizzare gli eventi per la registrazione */ 
DELIMITER $
CREATE VIEW EventiConArbitro(Data, Id, Stato, CategoriaSport, NomeImpianto) AS
	SELECT Data, Id, Stato, CategoriaSport, NomeImpianto
	FROM ES
	WHERE EXISTS (SELECT ARBITRO.UserUtente, ARBITRO.IdEvento 
					FROM ARBITRO, ISCRIZIONE
					WHERE ARBITRO.IdEvento = ES.Id AND ISCRIZIONE.IdEvento = ARBITRO.IdEvento 
						AND ARBITRO.UserUtente = ISCRIZIONE.UserUtente AND ISCRIZIONE.Stato = "CONFERMATO");
$


DELIMITER $
CREATE VIEW EventiSenzaArbitro(Data, Id, Stato, CategoriaSport, NomeImpianto) AS
	SELECT Data, Id, Stato, CategoriaSport, NomeImpianto
	FROM ES
	WHERE NOT EXISTS (SELECT ARBITRO.UserUtente, ARBITRO.IdEvento 
						FROM ARBITRO, ISCRIZIONE
						WHERE ARBITRO.IdEvento = ES.Id AND ISCRIZIONE.IdEvento = ARBITRO.IdEvento 
							AND ARBITRO.UserUtente = ISCRIZIONE.UserUtente AND ISCRIZIONE.Stato = "CONFERMATO");
$

/* Visualizzare la lista degli eventi APERTI alla data attuale per le diverse categorie */ 
DELIMITER $
CREATE VIEW ListaEventiAttuali(Data, Stato, Sport) AS
	SELECT Data, Stato, CategoriaSport
	FROM ES
	WHERE (Stato = 'APERTO') AND (Data = CURDATE());
$

/* Visualizzare la lista e l'esito degli ES CHIUSI */
DELIMITER $
CREATE VIEW VisualizzaEsitoGruppo (Id, Data, Impianto, Punti, Squadra, Categoria) AS
	SELECT ES.Id, ES.Data, ES.NomeImpianto, GRUPPO.NumeroPunti, GRUPPO.NomeSquadra, ES.CategoriaSport
	FROM ES, GRUPPO
	WHERE (ES.Stato = "CHIUSO") AND (GRUPPO.IdEventoGruppo = ES.Id);
$

DELIMITER $
CREATE VIEW VisualizzaEsitoSingolo (Id, Data, Impianto, Punti, Squadra, Durata, Categoria) AS
	SELECT ES.Id, ES.Data, ES.NomeImpianto, SINGOLO.NumeroPunti, SINGOLO.UserGiocatore, SINGOLO.Durata, ES.CategoriaSport
	FROM  ES, SINGOLO
	WHERE (ES.Stato = "CHIUSO") AND (SINGOLO.IdEventoSingolo = ES.Id);
$

/* seleziona gli eventi per cui un utente può scegliere un giocatore da valutare */
DELIMITER $
CREATE VIEW EventiDaValutare(User, IdEvento, Categoria, Impianto, Data) AS
	SELECT GIOCATORE.UserUtente, GIOCATORE.IdEvento, ES.CategoriaSport, ES.NomeImpianto, ES.Data
	FROM GIOCATORE, ES
	WHERE (GIOCATORE.IdEvento = ES.Id) AND ( EXISTS (SELECT IdEvento
														FROM ESITO
														WHERE ES.Id = ESITO.IdEvento));
$


/* INSERIMENTO */

/* Procedure per inserire utente nel database */ 
DELIMITER $
CREATE PROCEDURE NuovoUtente(IN User VARCHAR(50), IN Password VARCHAR(32), IN Nome VARCHAR(50),
	IN Cognome VARCHAR(50), IN NumMatricola VARCHAR(10), IN AnnoNascita INT, 
	IN LuogoNascita VARCHAR(50), IN Telefono VARCHAR(10), IN Foto VARCHAR(100), IN NomeCdS VARCHAR(100))
BEGIN
	DECLARE confermaMatricola VARCHAR(10);

	SELECT Matricola INTO confermaMatricola 
	FROM UTENTE 
	WHERE (Matricola = NumMatricola);

	IF (confermaMatricola = NumMatricola) THEN
		SELECT "[ERRORE] Matricola già in uso";
	ELSE 
		START TRANSACTION;
		INSERT INTO UTENTE VALUES (User, Password, Nome, Cognome, NumMatricola, AnnoNascita, LuogoNascita, Telefono, Foto, NomeCdS,"0","0","0");
		#Inserisco automaticamente l'utente tra gli US
		INSERT INTO US VALUES (User);
		COMMIT;
	END IF;
END;
$

/* Organizzare un nuovo ES (per UP) */
DELIMITER $
CREATE PROCEDURE NuovoES(IN DataEvento DATE, IN Sport VARCHAR(50), IN Impianto VARCHAR(50),  IN Up VARCHAR(50))
BEGIN
	DECLARE fine INT DEFAULT 0;
	DECLARE DatEve DATE;
	DECLARE CatSpo VARCHAR(50);
	DECLARE Imp VARCHAR(50);
	DECLARE esiste INT DEFAULT 0;
	
	DECLARE controllo CURSOR FOR SELECT Data, CategoriaSport, NomeImpianto FROM ES;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET fine = 1;		

	OPEN controllo;
		ciclo: WHILE NOT fine DO
		FETCH controllo INTO DatEve, CatSpo, Imp;
			IF (DatEve = DataEvento AND CatSpo = Sport AND Imp = Impianto) THEN
				SELECT "Evento gia' inserito";
				SET esiste = 1;
				SET fine = 1;
			END IF;			   
		END WHILE ciclo;		
		
		IF (esiste = 0) THEN
			START TRANSACTION;
			INSERT INTO ES(Data, CategoriaSport, NomeImpianto, UserUP) VALUES(DataEvento, Sport, Impianto, Up);
			COMMIT;	
		END IF;
	CLOSE controllo;
END;		
$ 

/* Iscriversi ad un ES (per UP o US) */
DELIMITER $
CREATE PROCEDURE IscriversiEvento(IN DataIscrizione DATE, IN Utente VARCHAR(50), IN Evento INT, IN UP VARCHAR(50), IN Ruolo VARCHAR(50))
BEGIN
	DECLARE Fine INT DEFAULT 0;
	DECLARE User VARCHAR(50);
	DECLARE ES INT;
	DECLARE Esiste INT DEFAULT 0;
	DECLARE NumArbitri INT;
	DECLARE StatoES VARCHAR(50);

	DECLARE Controllo CURSOR FOR SELECT UserUtente, IdEvento FROM ISCRIZIONE;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET Fine = 1;		

	SELECT Stato INTO StatoES FROM ES WHERE Id = Evento;

	SELECT COUNT(*) INTO NumArbitri 
	FROM ARBITRO JOIN ISCRIZIONE ON (ARBITRO.UserUtente = ISCRIZIONE.UserUtente) 
	WHERE ARBITRO.IdEvento = ISCRIZIONE.IdEvento AND ARBITRO.IdEvento = Evento AND ISCRIZIONE.Stato = 'CONFERMATO';

	OPEN Controllo;
		ciclo: WHILE NOT Fine DO 
		FETCH Controllo INTO User, ES;
			IF (User = Utente AND ES = Evento) THEN
				SELECT "L'utente è già iscritto a quell'evento";
				SET Esiste = 1;
				SET Fine = 1;
			END IF;			   
		END WHILE ciclo;		
		
		IF (Esiste = 0) THEN
			IF (Ruolo = "GIOCATORE") THEN
				START TRANSACTION;
				INSERT INTO ISCRIZIONE(Data, UserUtente, IdEvento, UPApprovazione) VALUES (DataIscrizione, Utente, Evento, UP);	
				IF (StatoES = 'APERTO') THEN
					/*Inserimento valore nella tabella giocatore */
					INSERT INTO GIOCATORE(UserUtente, IdEvento) VALUES (Utente, Evento);
				ELSE 
					SELECT "[ERRORE] L'evento è chiuso.";
					UPDATE ISCRIZIONE SET Stato = 'RIFIUTATO' WHERE (UserUtente = Utente) AND (IdEvento = Evento);                                     
				END IF;
				COMMIT;
			END IF;		
			
			IF (Ruolo = "ARBITRO") THEN
				START TRANSACTION;
				INSERT INTO ISCRIZIONE(Data, UserUtente, IdEvento, UPApprovazione) VALUES (DataIscrizione, Utente, Evento, UP);
				IF (NumArbitri = 1) THEN
					SELECT "[ERRORE] Un arbitro è già stato confermato per questo evento. Non puoi iscriverti come arbitro.";
					UPDATE ISCRIZIONE SET Stato = 'RIFIUTATO' WHERE (UserUtente = Utente) AND (IdEvento = Evento);
				ELSE
					INSERT INTO ARBITRO(UserUtente, IdEvento) VALUES (Utente, Evento);	
				END IF;
				COMMIT;
			END IF;				
		END IF;		
	CLOSE controllo;
END;
$

/* Confermare/rifiutare la partecipazione di utenti all'ES organizzato (per UP) */
DELIMITER $
CREATE Procedure Approvazione(IN UP VARCHAR(50), User VARCHAR(50), IN Evento INT, IN Approvato BOOLEAN)
BEGIN	
	DECLARE Revisione BOOLEAN;
	DECLARE StatoIscrizione VARCHAR(50);

	SELECT Stato INTO StatoIscrizione FROM ISCRIZIONE WHERE (UserUtente = User) AND (IdEvento = Evento);

	IF (StatoIscrizione = 'IN ATTESA') THEN
		SET Revisione = 0; #FALSE
	ELSE
		SET Revisione = 1; #TRUE
	END IF;

	IF (Revisione) THEN
		SELECT "[ERRORE] ISCRIZIONE NON AGGIORNATA: L'ISCRIZIONE ERA GIA' STATA REVISIONATA o NON ESISTE.";
	ELSE
		IF (Approvato) THEN
			START TRANSACTION;
			UPDATE ISCRIZIONE SET Stato = 'CONFERMATO' WHERE (UserUtente = User) AND (IdEvento = Evento);
			COMMIT;
		ELSE 
			START TRANSACTION;
			UPDATE ISCRIZIONE SET Stato = 'RIFIUTATO' WHERE (UserUtente = User) AND (IdEvento = Evento);
			DELETE FROM GIOCATORE WHERE (UserUtente = User) AND (IdEvento = Evento);
			DELETE FROM ARBITRO WHERE (UserUtente = User) AND (IdEvento = Evento);
			COMMIT;
		END IF;
	END IF;	
END;
$

/* Inserire l'esito dell' ES organizzato (per UP) */
#inserire esito dell'ES - Gruppo
DELIMITER $
CREATE Procedure NuovoEsitoGruppo(IN Evento INT, IN Data DATE, IN UP VARCHAR(50), 
	IN Punti1 INT, IN Squadra1 VARCHAR(50), IN Punti2 INT, IN Squadra2 VARCHAR(50), IN UserArbitro VARCHAR(50))
BEGIN	
	DECLARE AlreadyExists BOOLEAN;
	SELECT EXISTS (SELECT IdEventoGruppo FROM GRUPPO WHERE IdEventoGruppo = Evento) INTO AlreadyExists;
	
	IF (AlreadyExists = 1) THEN
		SELECT "[ERRORE] Un esito è già stato inserito per questo evento.";
	ELSE
		START TRANSACTION;
		INSERT INTO ESITO
		VALUES (Evento, Data, UP);

		INSERT INTO GRUPPO
		VALUES (Evento, Punti1, Squadra1);

		INSERT INTO GRUPPO 
		VALUES (Evento, Punti2, Squadra2);

		IF (UserArbitro != "NULL") THEN 
			INSERT INTO ARBITRAGGIO
			VALUES (UserArbitro, Evento, Evento);
		END IF;
		COMMIT;
	END IF;	
END;
$

#inserire esito dell'ES - Singolo
DELIMITER $
CREATE Procedure NuovoEsitoSingolo(IN Evento INT, IN Data DATE, IN UP VARCHAR(50), 
	IN Punti1 INT, IN Giocatore1 VARCHAR(50), IN Punti2 INT, IN Giocatore2 VARCHAR(50), IN Durata TIME, IN UserArbitro VARCHAR(50))
BEGIN
	DECLARE AlreadyExists BOOLEAN;

	SELECT EXISTS (SELECT IdEventoSingolo FROM SINGOLO WHERE IdEventoSingolo = Evento) INTO AlreadyExists;
	
	IF (AlreadyExists = 1) THEN		
		SELECT "[ERRORE] Un esito è già stato inserito per questo evento.";
	ELSE
		START TRANSACTION;
		INSERT INTO ESITO
		VALUES (Evento, Data, UP);

		INSERT INTO SINGOLO
		VALUES (Evento, Giocatore1, Evento, Punti1, Durata);
		UPDATE UTENTE SET PartiteTennis = PartiteTennis + 1 WHERE (UTENTE.Username = Giocatore1);

		INSERT INTO SINGOLO
		VALUES (Evento, Giocatore2, Evento, Punti2, Durata);
		UPDATE UTENTE SET PartiteTennis = PartiteTennis + 1 WHERE (UTENTE.Username = Giocatore2); 

		IF (UserArbitro != "NULL") THEN 
			INSERT INTO ARBITRAGGIO
			VALUES (UserArbitro, Evento, Evento);
		END IF;
		COMMIT;
	END IF;	
END;
$

/* Composizione Squadra */
DELIMITER $
CREATE PROCEDURE ComposizioneSquadra(IN _NomeSquadra VARCHAR(50), IN _UserGiocatore VARCHAR(50), IN _IdEvento INT, IN _Punti INT)
BEGIN	
	DECLARE UtenteConfermato VARCHAR(50);
	DECLARE Categoria VARCHAR(50);

	SELECT GIOCATORE.UserUtente INTO UtenteConfermato
	FROM GIOCATORE, ISCRIZIONE 
	WHERE (GIOCATORE.UserUtente = ISCRIZIONE.UserUtente) AND (GIOCATORE.IdEvento = ISCRIZIONE.IdEvento)
			AND (GIOCATORE.UserUtente = _UserGiocatore ) AND (GIOCATORE.IdEvento = _IdEvento) 
			AND (ISCRIZIONE.Stato = "CONFERMATO");

	SELECT CategoriaSport INTO Categoria
	FROM ES
	WHERE (_IdEvento = ES.Id);

	IF (UtenteConfermato = _UserGiocatore) THEN
		START TRANSACTION;
		INSERT INTO COMPOSIZIONE 
		VALUES (_Punti, _NomeSquadra, _UserGiocatore, _IdEvento);
		IF (Categoria = "Calcio") THEN
			UPDATE UTENTE SET PartiteCalcio = PartiteCalcio + 1 WHERE (UTENTE.Username = _UserGiocatore);
		END IF;
		IF (Categoria = "Basket") THEN
			UPDATE UTENTE SET PartiteBasket = PartiteBasket + 1 WHERE (UTENTE.Username = _UserGiocatore);
		END IF;
		COMMIT;
	ELSE 
		SELECT "[ERRORE] GIOCATORE non CONFERMATO";
	END IF;	
END;
$	

/* Inserisce una nuova squadra */
DELIMITER $
CREATE PROCEDURE NuovaSquadra(IN Squadra VARCHAR(50))
	BEGIN 
		START TRANSACTION;
		INSERT INTO SQUADRA VALUES(Squadra);
		COMMIT;
	END;
$

/* Inserire la valutazione di un altro utente giocatore per un ES cui si è partecipato */
DELIMITER $
CREATE PROCEDURE NuovaValutazione(IN _Data DATE, IN _Voto INT, IN _Commento VARCHAR(500), 
	IN _UserGiocatore VARCHAR(50),IN _IdESGiocatore INT, IN _UserValutante VARCHAR(50), IN _IdESValutante INT)
BEGIN
	DECLARE GiocatoreConfermato VARCHAR(50);
	DECLARE ValutanteConfermato VARCHAR(50);

	SELECT GIOCATORE.UserUtente INTO GiocatoreConfermato
	FROM GIOCATORE, ISCRIZIONE 
	WHERE (GIOCATORE.UserUtente = ISCRIZIONE.UserUtente) AND (GIOCATORE.IdEvento = ISCRIZIONE.IdEvento)
			AND (GIOCATORE.UserUtente = _UserGiocatore ) AND (GIOCATORE.IdEvento = _IdESGiocatore) 
			AND (ISCRIZIONE.Stato = "CONFERMATO");

	SELECT GIOCATORE.UserUtente INTO ValutanteConfermato
	FROM GIOCATORE, ISCRIZIONE 
	WHERE (GIOCATORE.UserUtente = ISCRIZIONE.UserUtente) AND (GIOCATORE.IdEvento = ISCRIZIONE.IdEvento)
			AND (GIOCATORE.UserUtente = _UserValutante ) AND (GIOCATORE.IdEvento = _IdESValutante) 
			AND (ISCRIZIONE.Stato = "CONFERMATO");

	IF (GiocatoreConfermato = _UserGiocatore AND ValutanteConfermato = _UserValutante) THEN
		IF (_IdESValutante = _IdESGiocatore AND _UserValutante != _UserGiocatore ) THEN
			START TRANSACTION;
			INSERT INTO VALUTAZIONE
			VALUES (_Data, _Voto, _Commento, _UserGiocatore, _IdESGiocatore, _UserValutante, _IdESValutante);
			COMMIT;
		ELSE 
			SELECT "[ERRORE] Non puoi commentare, non puoi commentare te stesso o non hai partecipato all'evento.";
		END IF;
	ELSE
		SELECT "[ERRORE] Giocatore o valutante non confermati";
	END IF;
END;
$


/* Inserire un post in un forum */
DELIMITER $
CREATE PROCEDURE NuovoPost(IN _Data DATE, IN _Testo VARCHAR(1000), IN _Foto VARCHAR(100), 
	IN _UserUtente VARCHAR(50), _NomeCdS VARCHAR(100), _CategoriaSport VARCHAR(50)) 
BEGIN
	START TRANSACTION;
	INSERT INTO POST (Data, Testo, Foto, UserUtente, NomeCdS, CategoriaSport)
			VALUES (_Data, _Testo, _Foto, _UserUtente, _NomeCdS, _CategoriaSport);
	COMMIT;
END;  
$

DELIMITER $
CREATE Procedure RimozioneUtente(IN User VARCHAR(50))
BEGIN
	DECLARE fine INT DEFAULT 0;
	DECLARE newUsername VARCHAR(50);
	DECLARE concat VARCHAR(50);
	DECLARE i INT DEFAULT 0;

	DECLARE controllo CURSOR FOR SELECT Username FROM UTENTE;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET fine = 1;

	OPEN controllo;
		ciclo: WHILE NOT fine DO
		FETCH controllo INTO newUsername; 
			IF (newUsername = User) THEN
				START TRANSACTION;
				DELETE FROM POST WHERE UserUtente = User;
				DELETE FROM VALUTAZIONE WHERE UserGiocatore = User;
				
				SELECT CAST(i AS CHAR);
				SELECT CONCAT("UTENTE ANONIMO ", i) INTO concat;

				UPDATE UTENTE SET Username = concat, Password = NULL, Nome = NULL, Cognome = NULL,
									Matricola = "0000000000", AnnoNascita = NULL, LuogoNascita = NULL,
									Telefono = NULL, Foto = "utentegenerico.png", NomeCdS = NULL, 
									PartiteCalcio = "0", PartiteBasket = "0", PartiteTennis = "0"
				WHERE Username = User;

				SELECT CAST(i AS UNSIGNED INTEGER);	
				COMMIT;
				SET fine = 1;	
			END IF;

			SET i= i + 1;
		END WHILE ciclo;
	CLOSE controllo;	
END;
$

/* STATISTICHE */

/*	
	Mostrare per ogni utente, statistche relative a valutazioni ricevute:
		- voto medio come calciatore: X1
		- media goal/partita:X2
		- voto medio come cestista: Y1
		- media punti/partita: Y2
		- voto medio come tennista: Z1
		- numero partite tennis vinte: Z2
*/

DELIMITER $
CREATE VIEW ValutazioniMediaCalcio(Username,Voto) AS
	SELECT UserGiocatore, Avg(Voto)
	FROM VALUTAZIONE, ES
	WHERE (VALUTAZIONE.IdESGiocatore = ES.Id ) AND (ES.CategoriaSport = "Calcio")
	GROUP BY UserGiocatore;
$

DELIMITER $
CREATE VIEW MediaPuntiCalcio(Username, Punti) AS

	SELECT UserGiocatore, Avg(Punti)
	FROM COMPOSIZIONE, ES
	WHERE (COMPOSIZIONE.IdEvento = ES.Id) AND (ES.CategoriaSport = "Calcio")
	GROUP BY UserGiocatore;
$

DELIMITER $
CREATE VIEW ValutazioniMediaBasket(Username,Voto) AS
	SELECT UserGiocatore, Avg(Voto)
	FROM VALUTAZIONE, ES
	WHERE (VALUTAZIONE.IdESGiocatore = ES.Id ) AND (ES.CategoriaSport = "Basket")
	GROUP BY UserGiocatore;
$

DELIMITER $
CREATE VIEW MediaPuntiBasket(Username, Punti) AS 
	SELECT UserGiocatore, Avg(Punti)
	FROM COMPOSIZIONE, ES
	WHERE (COMPOSIZIONE.IdEvento = ES.Id) AND (ES.CategoriaSport = "Basket")
	GROUP BY UserGiocatore;
$

DELIMITER $
CREATE VIEW ValutazioniMediaTennis(Username,Voto) AS
	SELECT UserGiocatore, Avg(Voto)
	FROM VALUTAZIONE, ES
	WHERE (VALUTAZIONE.IdESGiocatore = ES.Id ) AND (ES.CategoriaSport = "Tennis")
	GROUP BY UserGiocatore;
$

DELIMITER $
CREATE VIEW VittorieTennis(UserGiocatore, Vittorie) AS 
	SELECT UserGiocatore, Count(*) 
	FROM SINGOLO AS S1
	WHERE (NumeroPunti > ALL (SELECT NumeroPunti
								FROM SINGOLO AS S2
								WHERE (S2.UserGiocatore != S1.UserGiocatore )
										AND (S2.IdEventoGiocatore = S1.IdEventoGiocatore)))
	GROUP BY UserGiocatore;
$

/*	Mostrare le statistiche del giocatore TOP:	
		- giocatore che ha partecipato al maggior numero di ES, nelle varie categorie
		- giocatore con voto medio massimo, nelle varie categorie
*/

DELIMITER $
CREATE VIEW TopPartiteCalcio(Username, Partite) AS
	SELECT Username, PartiteCalcio
	FROM UTENTE 
	WHERE PartiteCalcio >= ALL (SELECT PartiteCalcio
								FROM UTENTE);
$

DELIMITER $
CREATE VIEW TopPartiteBasket(Username, Partite) AS
	SELECT Username, PartiteBasket
	FROM UTENTE 
	WHERE PartiteBasket >= ALL (SELECT PartiteBasket
								FROM UTENTE);
$

DELIMITER $
CREATE VIEW TopPartiteTennis(Username, Partite) AS 
	SELECT Username, PartiteTennis
	FROM UTENTE 
	WHERE PartiteTennis >= ALL (SELECT PartiteTennis
								FROM UTENTE);
$

DELIMITER $
CREATE VIEW VotoMassimoGiocatore(UserGiocatore, CategoriaSport, Voto) AS
	SELECT UserGiocatore, CategoriaSport, AVG(Voto)
	FROM VALUTAZIONE, ES
	WHERE (IdESGiocatore = Id)
	GROUP BY UserGiocatore, CategoriaSport;
$

DELIMITER $
CREATE PROCEDURE TopGiocatore(IN Categoria VARCHAR(50))
BEGIN
	SELECT UserGiocatore, CategoriaSport, Voto
	FROM VotoMassimoGiocatore
	WHERE (CategoriaSport = Categoria) AND Voto = (SELECT MAX(Voto)
												  	FROM VotoMassimoGiocatore
												  	WHERE CategoriaSport = Categoria);
END;
$

/*
	Mostrare le statistiche della squadra TOP:
		- Squadra che ha partecipato al maggior numero di ES, nelle varie categorie
		- Squadra che ha vinto il maggiorn numero di partite, nelle varie categorie
*/

DELIMITER $
CREATE VIEW PartiteSquadraCalcio (NomeSquadra, Partecipazioni) AS
	SELECT NomeSquadra, COUNT(*) 
	FROM GRUPPO,ES
	WHERE (GRUPPO.IdEventoGruppo = ES.Id) AND (ES.CategoriaSport = "Calcio")
	GROUP BY NomeSquadra;		
$

DELIMITER $
CREATE VIEW PartiteSquadraBasket (NomeSquadra, Partecipazioni) AS
	SELECT NomeSquadra, COUNT(*) 
	FROM GRUPPO,ES
	WHERE (GRUPPO.IdEventoGruppo = ES.Id) AND (ES.CategoriaSport = "Basket")
	GROUP BY NomeSquadra;		
$
		
DELIMITER $
CREATE PROCEDURE TopPartecipazioniES(IN Categoria VARCHAR(50))
BEGIN		
	IF (Categoria = "Calcio") THEN
		SELECT NomeSquadra, Partecipazioni
		FROM PartiteSquadraCalcio
		WHERE Partecipazioni = (SELECT MAX(Partecipazioni)
								FROM PartiteSquadraCalcio);	
	END IF;

	IF (Categoria = "Basket") THEN
		SELECT NomeSquadra, Partecipazioni
		FROM PartiteSquadraBasket
		WHERE Partecipazioni = (SELECT MAX(Partecipazioni)
								FROM PartiteSquadraBasket);
	END IF;

END;
$

DELIMITER $
CREATE VIEW PartiteVinteCalcio(Squadra, PartiteVinte) AS
	SELECT NomeSquadra, Count(*)
	FROM GRUPPO AS C1, ES
	WHERE (C1.IdEventoGruppo = ES.Id) AND (ES.CategoriaSport = "Calcio") 
			AND (NumeroPunti > ALL (SELECT NumeroPunti
									FROM GRUPPO AS C2
									WHERE (C2.NomeSquadra != C1.NomeSquadra )
											AND (C2.IdEventoGruppo = C1.IdEventoGruppo)))
	GROUP BY NomeSquadra;
$

DELIMITER $
CREATE VIEW PartiteVinteBasket(Squadra, PartiteVinte) AS
	SELECT NomeSquadra, Count(*)
	FROM GRUPPO AS B1, ES
	WHERE (B1.IdEventoGruppo = ES.Id) AND (ES.CategoriaSport = "Basket") 
			AND	(NumeroPunti > ALL (SELECT NumeroPunti 
									FROM GRUPPO AS B2
									WHERE (B2.NomeSquadra != B1.NomeSquadra )
											AND (B2.IdEventoGruppo = B1.IdEventoGruppo)))
	GROUP BY NomeSquadra;
$

DELIMITER $
CREATE PROCEDURE TopNumeriPartite(IN Categoria VARCHAR(50))
BEGIN
	IF (Categoria = "Calcio") THEN
		SELECT Squadra, PartiteVinte
		FROM PartiteVinteCalcio
		WHERE PartiteVinte = (SELECT MAX(PartiteVinte)
								FROM PartiteVinteCalcio);
	END IF; 

	IF (Categoria = "Basket") THEN
		SELECT Squadra, PartiteVinte
		FROM PartiteVinteBasket
		WHERE PartiteVinte = (SELECT MAX(PartiteVinte)
								FROM PartiteVinteBasket);
	END IF;
END;
$

/* Procedure per chiusura/annullamento */
DELIMITER $
CREATE PROCEDURE ControlloData()
BEGIN
	DECLARE NumEventi INT;
	DECLARE i INT;
	DECLARE Evento INT;
	DECLARE Categoria VARCHAR(50);
	DECLARE MaxGiocatori INT;
	DECLARE Counter INT;
	DECLARE DataES DATE;
	SET i = 1;
	SELECT COUNT(*) INTO NumEventi FROM ES;

	WHILE i <= NumEventi DO
		SELECT Id INTO Evento FROM ES WHERE Id = i;
		SELECT Data INTO DataES FROM ES WHERE Id = i;
		SELECT CategoriaSport INTO Categoria FROM ES WHERE Id = Evento;
		SELECT NumGiocatori INTO MaxGiocatori FROM CATEGORIA WHERE Sport = Categoria;
		SELECT COUNT(*) INTO Counter FROM GIOCATORE
			JOIN ISCRIZIONE ON (GIOCATORE.UserUtente = ISCRIZIONE.UserUtente) 
			WHERE GIOCATORE.IdEvento = ISCRIZIONE.IdEvento AND GIOCATORE.IdEvento = Evento AND ISCRIZIONE.Stato = 'CONFERMATO';

		IF ((Counter < MaxGiocatori) && (DataES <= CURDATE())) THEN
			START TRANSACTION;
			UPDATE ES SET Stato = 'ANNULLATO' WHERE Id = Evento;
			UPDATE ISCRIZIONE SET Stato = 'RIFIUTATO' WHERE (IdEvento = Evento);
			DELETE FROM GIOCATORE WHERE (IdEvento = Evento);
			DELETE FROM ARBITRO WHERE (IdEvento = Evento);
			COMMIT;
		END IF;
		SET i = i + 1;
	END WHILE;
END;
$

/* Controllo giocatori max */
CREATE PROCEDURE ControlloGiocatori(IN Evento INT, OUT Controllo BOOLEAN)
BEGIN
	DECLARE Categoria VARCHAR(50);
	DECLARE MaxGiocatori INT;
	DECLARE Counter INT;

	SELECT CategoriaSport INTO Categoria FROM ES WHERE Id = Evento;
	SELECT NumGiocatori INTO MaxGiocatori FROM CATEGORIA WHERE Sport = Categoria;
	SELECT COUNT(*) INTO Counter FROM GIOCATORE
		JOIN ISCRIZIONE ON (GIOCATORE.UserUtente = ISCRIZIONE.UserUtente) 
		WHERE GIOCATORE.IdEvento = ISCRIZIONE.IdEvento AND GIOCATORE.IdEvento = Evento AND ISCRIZIONE.Stato = 'CONFERMATO';

	IF (Counter = MaxGiocatori) THEN
		SET Controllo = TRUE;
	END IF;
END;
$

/* Trigger per cambio stato dell'evento che ha già abbastanza giocatori */
DELIMITER $
CREATE Trigger CambioStatoES AFTER UPDATE ON ISCRIZIONE
FOR EACH ROW
BEGIN
	DECLARE NumEventi INT;
	DECLARE i INT;
	DECLARE Evento INT;
	DECLARE Categoria VARCHAR(50);
	DECLARE MaxGiocatori INT;
	DECLARE Counter INT;
	SET i = 1;
	SELECT COUNT(*) INTO NumEventi FROM ES;

	WHILE i <= NumEventi DO
		SELECT Id INTO Evento FROM ES WHERE Id = i;
		SELECT CategoriaSport INTO Categoria FROM ES WHERE Id = Evento;
		SELECT NumGiocatori INTO MaxGiocatori FROM CATEGORIA WHERE Sport = Categoria;
		SELECT COUNT(*) INTO Counter FROM GIOCATORE
			JOIN ISCRIZIONE ON (GIOCATORE.UserUtente = ISCRIZIONE.UserUtente) 
			WHERE GIOCATORE.IdEvento = ISCRIZIONE.IdEvento AND GIOCATORE.IdEvento = Evento AND ISCRIZIONE.Stato = 'CONFERMATO';

	IF (Counter = MaxGiocatori) THEN
		UPDATE ES SET Stato = 'CHIUSO' WHERE Id = Evento;
	END IF;
		SET i = i + 1;
	END WHILE;	
END;
$

/* Trigger per update Utente da US a UP dopo 10 partite */
DELIMITER $
CREATE TRIGGER UtenteUP AFTER UPDATE ON UTENTE
FOR EACH ROW
BEGIN	
	DECLARE Calcio INT;
	DECLARE Basket INT;
	DECLARE Tennis INT;
	DECLARE Totale INT;

	SELECT PartiteCalcio + PartiteBasket + PartiteTennis INTO Totale
	FROM UTENTE
	WHERE (Username = NEW.Username);

	IF (Totale = 10) THEN
		DELETE FROM US WHERE (UserUs = NEW.Username);
		INSERT INTO UP(UserUP) VALUES(NEW.Username);
	END IF;
END;
$