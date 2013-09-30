<?php

include_once("CELConfig/CELConfig.inc");
include_once("bd.inc");

$ipNome = "IpBD =";
$ipValor = CELConfig_ReadVar("BD_ip");
$DBNAME = CELConfig_ReadVar("BD_ip");
$DBUSER = CELConfig_ReadVar("BD_user");
$DBPASSWD = CELConfig_ReadVar("BD_password");
$DBDATABASE = CELConfig_ReadVar("BD_database");
$DBHOST = CELConfig_ReadVar("BD_host");
$DBPORT = CELConfig_ReadVar("BD_porta");

class Abstract_DB {

    var $db_linkid = 0;

    function open($dbname, $user, $pass, $host, $port) {
        
    }

    function close() {
        
    }

}

class PGDB extends Abstract_DB {

    function PGDB() {
        global $DBNAME;
        global $DBUSER;
        global $DBPASSWD;
        global $DBHOST;
        global $DBPORT;
        global $DBDATABASE;

        $this->open($DBNAME, $DBUSER, $DBPASSWD, $DBHOST, $DBPORT);
    }

    function _PGDB() {
        $this->close();
    }

    function open($dbname, $user, $passwd, $host, $port) {
        $this->db_linkid = bd_connect() or die("Erro na conex�o � BD : " . mysql_error());
        if ($this->db_linkid) {
            return $this->db_linkid;
        } 
        
        else {
            return(FALSE);
        }
    }

    function close() {
        return mysql_close($this->db_linkid);
    }

}

class QUERY {

   
    var $ntuples;
    var $operationresult;
    var $resultset;
    var $currentrow = 0;

    function QUERY($pdbobject) {
        if ($pdbobject){
            $this->associate($pdbobject);
        }
        
        else{
            //nothing to do
        }
    }

    function associate($pdbobject) {
        $this->dbobject = $pdbobject;
    }

    function execute($querystring) {
        $this->operationresult = mysql_query($querystring) or die(mysql_error() . "<br>" . $querystring);
        return $this->operationresult;
    }

    function getntuples() {
        $this->ntuples = mysql_numrows($this->operationresult);
        return $this->ntuples;
    }

    function getfieldname($fieldnumber) {
        return mysql_fieldname($this->operationresult, $fieldnumber);
    }

    function readrow() {
        $this->resultset = mysql_fetch_array($this->operationresult);
        return ($this->currentresultset = $this->resultset);
    }

    function gofirst() {
        $this->currentrow = 0;
        return $this->readrow();
    }

    function golast() {
        $this->currentrow = ($this->getntuples()) - 1;
        return $this->readrow();
    }

    function getLastId() {
        return mysql_insert_id($this->dbobject->db_linkid);
    }

    function gonext() {
        $this->currentrow++;
        if ($this->currentrow < $this->getntuples()) {
            $this->resultset = $this->readrow();
            return $this->resultset;
        }
        
        else{
            return "LAST_RECORD_REACHED";
        }
    }

    function goprevious() {
        $this->currentrow--;
        if ($this->currentrow >= 0) {
            $this->resultset = $this->readrow();
            return $this->resultset;
        }
        else
            return "FIRST_RECORD_REACHED";
    }

    function beginTransaction() {
        if (!$this->execute("BEGIN")){
            return false;
        }
     
        else{
         //nothing to do
        }
        
        return true;
    }

    function commitTransaction() {
        if (!$this->execute("COMMIT")){
            return false;
        }
        
        else{
         //nothing to do
        }
        
        return true;
    }

    function rollbackTransaction() {
        if (!$this->execute("ROLLBACK")){
            return false;
        }
        
        else{
         //nothing to do
        }
        
        return true;
    }

}

?>