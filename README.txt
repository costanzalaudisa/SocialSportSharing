COME AVVIARE IL PROGETTO

1. Aprire la cartella “database”
2. Aprire il file “popolamento.sql” e inserire al posto di *PATH* il path della cartella “database” in cui sono situati i file .txt
3. Aprire il terminale e posizionarsi nella cartella “database” in cui sono situati i file .sql
4. Eseguire il comando mysql -u *USERNAME* -p *PASSWORD* <*NOMEFILE*.sql
	I file da eseguire sono, in ordine:
	- database.sql
	- procedure.sql
	- popolamento.sql
5. Se si utilizza XAMPP, copiare la cartella “website” nella cartella “htdocs” nel path di installazione di XAMPP
6. Avviare i servizi Apache e MySQL
7. Per visualizzare il sito web, aprire il proprio broswer e digitare “localhost/website”
	Per eseguire operazioni come utente premium, accedere al profilo del gestore.
	Username: Gestore
	Password: password
	NB: abbiamo stabilito che il Gestore non può iscriversi agli eventi, per cui sono stati inseriti dei controlli in alcuni file php