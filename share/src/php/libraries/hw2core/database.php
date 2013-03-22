<?php namespace Hw2;
S_Core::checkAccess();
/**
 * @name SQLManager
 * @description Classe per Gestire il Database
 * @author StefanoV
 * @copyright 2010
 */

//     esempi
//    $db = new SQLManager(true, "", "log.txt", true);
//     utilizza una connessione gia stabilita in precedenza
//    $db->useResource($connessione);
//     connette al db (host, user, pass, db)
//    $db->Open("localhost", "root", "test", "cmsv");
//     esegue la query settando un errore personale in caso di fallimento
//    $ri = $db->Query("SELECT * FROM cmsv_sezioni", "Errore Query Clienti!!");
//     ottiene i data della SELECT da ciclare restituendoli come oggetti
//    while($riga = $db->getObject($ri))
//    {
//    	echo $riga->field;
//    }
//     se trova almeno un valore
//    if($db->Found($ri))
//     stampa i valori a partire dal terzo
//    $db->dataSeek($ri, 3);
//     ottiene un array di valori, e impostando l'array interno come associativo (true)
//    $array_val = $db->getArray($ri, true);
//     conta i record restituiti
//    $righe = $db->Count($ri);
//     inserisce un array in una tabella
//     $data = array();
//     $data["field"] = "valore";
//     $data["field2"] = "valore2";
//     $db->insertArray("tabella", $data);
//     chiude la connessione al database
//     $db->Close();
//     resetta l'autoincrement della tabella portandolo al primo id disponibile
//     $db->resetIncrement("tabella");
//     effettua una select e restituisce un field
//     echo $db->getField("field", "tabella", "id = 1");

class S_SQLManager
{
	var $resource; // resource del db
	var $die_error = true; // esce con il mysql error
	var $mailerror = ""; // invia una mail con l'errore della query.
	var $logfile = ""; // percorso dove salvare il log delle query
	//var $debug = 0; // 0 - no logs , 1 - logs only errors , 2 - logs all queries

	function SQLManager($die_error = true, $mailerror = "", $logfile = "")
	{
		$this->die_error = $die_error;
		$this->mailerror = $mailerror;
		$this->logfile = $logfile;
	}
	/*********************************** Funzioni Base ****************************************/

	/**
	 * Permette di usare una resource già aperta
	 *
	 * Param: $res (resource) - resource da utilizzare
	 */
	function useResource($res)
	{
		// se $res è settata, non vuota, ed è una resource
		if(isset($res) && !empty($res) && is_resource($res))
		{
			// applicala come resource globale
			$this->resource = $res;
		}
	}

	/**
	 * Connette lo script al Database <acronym title="My Structured Query Language">MySQL</acronym>
	 *
	 * Param: $host (string) - server del database
	 * Param: $user (string) - username del database
	 * Param: $pass (string) - password del database
	 * Param: $db (string) - nome del database
	 */
	function Open($host, $user, $pass, $db, $newlink = false)
	{
		// se i campi sono inseriti
		if(empty($host) || empty($user) || empty($db))
                    S_Exception::raise ("Cannot open database..", S_Exception_type::error ());

		// connetto al' host
		$ris = mysql_connect($host, $user, $pass, $newlink) or $this->getErr("","Connection Error");

		// seleziono il db
		mysql_select_db($db, $ris) or $this->getErr("","Error during db selection!");

		// setto la resource come globale della classe
		$this->resource = $ris;
	}

	/**
	 * Libera le risorse della resource risultante dalla query
	 *
	 * Param: $query (resource link) - la resource ottenuta dalla funzione doQuery
	 */
	function Free($query)
	{
		if(mysql_free_result($query))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Restituisce l'ID generato dall' ultima query INSERT
	 */
	function lastID()
	{
		return @mysql_insert_id();
	}

	/**
	 * Restituisce le righe contate nella query
	 *
	 * Param: $query (resource link) - la resource ottenuta dalla funzione doQuery
	 */
	function Count($query)
	{
		return @mysql_num_rows($query);
	}

	/**
	 * Restituisce true se trova almeno un record
	 *
	 * Param: $query (resource link) - la resource ottenuta dalla funzione doQuery
	 */
	function Found($query)
	{
		if($this->Count($query) != 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Mostra l'errore o manda la mail con l'errore
	 *
	 * Param: $error (string) - stringa di errore restituita da <acronym title="My Structured Query Language">MySQL</acronym>
	 * Param: $mErr (string) - stringa di errore da mostrare scelta da noi
	 */
	private function getErr($query,$debug=0,$error = "")
	{

		if(empty($error)) 
                    $error = mysql_error();
                if(!empty($query))
                    $error .= "</br> query: ".$query;

		// se bisogna mandare la mail
		if(!empty($this->mailerror))
		{
			// testo mail
			$testo = "error: ".$error." \r\n \r\n url of called file: ".$_SERVER['REQUEST_URI'];

			// destinatario
			$to = $this->mailerror;

			// soggetto
			$subject = "SQLManager: query error.";

			// headers
			$headers = "From: $to\r\n";
			$headers .= "Reply-To: $to\r\n";
			$headers .= "Return-Path: $to\r\n";

			// manda la mail
			if (!mail($to, $subject, $testo, $headers))
			{
				// se non va a buon fine, mostra l'errore
				die("Errore durante l'invio della Segnalazione!");
			}

		}

		if(!empty($this->logfile) && $debug >= 1)
		{
			$fp = @fopen($this->logfile, "a");
			@fwrite($fp,  date("d/m/Y H:i:s")." - error: $error\r\n");
			@fclose($fp);
		}

		if($this->die_error)
		{
			// mostra errore personale
			die($error);
		}
	}

	/**
	 * Esegue la query al database
	 *
	 * Param: $query (string) - la query da eseguire al database
	 * Param: $manualError (string) - errore personalizzato in caso di fallimento della query
         * @return resource Description
	 */
	function Query($query, $manualError = "",$debug=0)
	{
		//$query = stripslashes($query);
		//$query = addslashes($query);

		// eseguo la query
		$rs = mysql_query($query) or $this->getErr($query,$debug,$manualError);

		// se è andata bene
		if($rs)
		{
			if(!empty($this->logfile) && $debug >= 2)
			{
				$fp = @fopen($this->logfile, "a");
				//@fwrite($fp, date("d/m/Y H:i:s")." - Query executed successfully:\r\n");
				@fwrite($fp,$query."\r\n");
				@fclose($fp);
			}
			// restituisco il link di resource
			return $rs;
		}
	}

	/**
	 * Ottiene i data come oggetti (da usare come mysql_fetch_object)
	 *
	 * Param: $query (resource link) - la resource ottenuta dalla funzione doQuery
	 */
	function getObject($query)
	{
		// ottiene le righe come oggetto
		$rig = @mysql_fetch_object($query);

		// restituisce il tutto
		return $rig;
	}

	/**
	 * Chiude la connessione al Database
	 */
	function Close()
	{
		mysql_close($this->resource);
	}
        
        /*********************************** Funzioni Utili ****************************************/

	/**
	 * Ottiene i data ottenuti da una query SELECT in un array
	 *
	 * Param: $query (resource link) - la resource ottenuta dalla funzione doQuery
	 * Param: $associativo (boolean) - determina se creare un sotto-array associativo per ogni riga, oppure no
	 */
	public static function getArray($query, $associativo = true)
	{
		// dichiaro e svuoto l'array
		$arrayFileds = array();

		// ciclo i nomi dei campi nella SELECT e li metto in array
		for($i = 0; $i < @mysql_num_fields($query); $i++)
		{
			$arrayFileds[] = @mysql_fetch_field($query)->name;
		}

		// dichiarazione e svuotamento array $data
		$data = array();

		// ciclo per ottenere i valori associativi in $link
		while($link = @mysql_fetch_array($query, MYSQL_ASSOC))
		{

			// dichiaro e svuoto l'array $par
			$par = array();

			// ciclo i nomi passati nell'array $arrayFileds
			foreach($arrayFileds as $nomi)
			{
				// se è impostato l'associativo
				if($associativo)
				{
					// mette in $par i valori come array associativo
					$par[$nomi] = $link[$nomi];
				}
				else // ... altrimenti ...
				{
					// mette in $par i valori come array numerato
					$par[] = $link[$nomi];
				}

			}

			// aggiunge all'array $data, l'array $par
			$data[] = $par;
		}

		// restituisce i data
		return $data;
	}

	/**
	 * Muove il puntatore interno ad una riga
	 *
	 * Param: $query (resource) - la resource della query
	 * Param: $riga (int) - la riga da cui iniziare - Default: 0
	 */
	public static function dataSeek($query, $riga = 0)
	{
		return @mysql_data_seek($query, $riga);
	}

	/**
	 * Inserisce un array (chiave => valore) nel database
	 *
	 * Param: $table (string) - la tabella del database
	 * Param: $array (array) - l'array da cui prendere i valori
	 */
	public static function insertArray($table, $array)
	{
		return($this->Query(S_SqlHelper::insertByArray($table, $array)));
	}

	/**
	 * Resetta l'ultimo ID autoincrement
	 *
	 * Param: $table (string) - la tabella del database
	 */
	function resetIncrement($table)
	{
		$get = $this->Query("SELECT MAX(id) as mxid FROM $table");

		if($this->Found($get))
		{
			$max = $this->getObject($get);

			$mxid = (int)$max->mxid;

			$mxid++;

			$this->Query("ALTER TABLE $table AUTO_INCREMENT = $mxid");
		}
	}

	/**
	 * Ottiene un field specifico
	 *
	 * Param: $field (string) - il field da restituire
	 * Param: $table (string) - la tabella da cui estrarre il field
	 * Param: $where (string) - la clausula where
	 */
	public static function getField($field, $table, $where)
	{
		$query = $this->Query("SELECT $field FROM $table WHERE $where LIMIT 1");

		$result = $this->getObject($query);

		return $result->$field;
	}
        
}

class S_SqlHelper {

    
    public static function insertByArray($table, $array) {
        $keys = array_keys($array);

        $values = array_values($array);

        $sql = 'INSERT INTO ' . $table . '(' . implode(', ', $keys) . ') VALUES ("' . implode('", "', $values) . '")';
        return $sql;
    }

}