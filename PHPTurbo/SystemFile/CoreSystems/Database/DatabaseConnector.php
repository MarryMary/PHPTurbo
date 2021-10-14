<?php

class DatabaseConnector
{
    private $dbh;
    private $dbuser;
    private $dbpass;
    private $DBInstance;
    private $configDir;
    private $LogDir;
    private $ExceptionProcessor;
    private $SysData;
    private $langPack;
    private $Initialized;
    private $QueryMode;
    private $QueryCommand;
    private $prepared;
    private $isSubQuery;
    private $subQueryCommand;
    private $whereTerms;
    private $now;
    private $nextSubQuery;
    private $executed;
    private $statement;

    public function Initializer(){
        $this->dbh = Null;
        $this->dbuser = Null;
        $this->dbpass = Null;
        $this->DBInstance = Null;
        $this->LogDir = "../../Logs/";
        $this->ExceptionProcessor = "../ErrProcessor/ErrorProcessor.php";
        $this->SysData = Null;
        $this->langPack = Null;
        $this->Initialized = False;
        $this->QueryMode = Null;
        $this->QueryCommand = Null;
        $this->prepared = Null;
        $this->isSubQuery = False;
        $this->subQueryCommand = Null;
        $this->whereTerms = array();
        $this->now = Null;
        $this->nextSubQuery = False;
        $this->executed = False;
        $this->statement = Null;

        require_once dirname(__FILE__)."/../SystemFileReader/SysFileLoader.php";
        $loader = new SystemFileReader();
        $this->SysData = $loader->SettingLoader();
        $this->LangPack = $loader->LangPackLoader();
        try {
            $this->dbh = "{$this->SysData['Database']}:dbname={$this->SysData['DatabaseName']};host={$this->SysData['DatabaseHost']};charset={$this->SysData['DatabaseCharSet']};port={$this->SysData['DatabasePort']}";
            $this->dbuser = $this->SysData['DatabaseUser'];
            $this->dbpass = $this->SysData['DatabasePassword'];
            $this->DBInstance = new PDO($this->dbh, $this->dbuser, $this->dbpass);
            if($this->SysData['DatabaseShowError'] == "Yes" or $this->SysData['Mode'] == "TestOnly"){
                $this->DBInstance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->DBInstance->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            }
            $this->Initialized = True;
            return $this;
        }catch(Exception $e){
            require_once(dirname(__FILE__)."/".$this->ExceptionProcessor);
            $CreateException = new ErrorProcessor();
            $CreateException->EchoError($this->SysData, $this->langPack, "DBInitializerHint", dirname(__FILE__)."/".$this->LogDir, $e);
            return False;
        }
    }

    public function Self($query, $bindValue = Null)
    {
        require_once(dirname(__FILE__)."/".$this->ExceptionProcessor);
        $CreateException = new ErrorProcessor();
        if($this->Initialized){
            if(empty($this->QueryMode) and !$this->isSubQuery){
                $this->QueryMode = "SELF";
                if(!empty($query)){
                    $command = $this->DBInstance->prepare($query);
                    if(!empty($bindValue)) {
                        if(is_array($bindValue)) {
                            foreach ($bindValue as $key => $value) {
                                if(is_numeric($value)) {
                                    $command->bindValue($key, htmlspecialchars($value), PDO::PARAM_INT);
                                }else if(is_bool($value)){
                                    $command->bindValue($key, htmlspecialchars($value), PDO::PARAM_BOOL);
                                }else{
                                    $command->bindValue($key, htmlspecialchars($value), PDO::PARAM_STR);
                                }
                            }
                            $this->prepared = $command;
                            return $this;
                        }else{
                            $CreateException->EchoError($this->SysData, $this->langPack, "ValueMustBeArray", dirname(__FILE__)."/".$this->LogDir);
                            return False;
                        }
                    }else {
                        $this->prepared = $command;
                        return $this;
                    }
                }else{
                    $CreateException->EchoError($this->SysData, $this->langPack, "QueryMustBeNeed", dirname(__FILE__)."/".$this->LogDir);
                    return False;
                }
            }else{
                $CreateException->EchoError($this->SysData, $this->langPack, "SelfmodeCantUseOther", dirname(__FILE__)."/".$this->LogDir);
                return False;
            }
        }else{
            $CreateException->EchoError($this->SysData, $this->langPack, "QueryCantUseBeforeInitialize", dirname(__FILE__)."/".$this->LogDir);
            return False;
        }
    }

    public function Select($column, $table)
    {
        require_once(dirname(__FILE__)."/".$this->ExceptionProcessor);
        $CreateException = new ErrorProcessor();
        if($this->nextSubQuery){
            $this->nextSubQuery = False;
        }
        if(empty($this->QueryMode) or $this->isSubQuery){
            $this->QueryMode = "SELECT";
            if(!empty($table) and !empty($column)) {
                if (is_string($table) and is_array($column)) {
                    $col = Null;
                    foreach ($column as $colum) {
                        $col .= "{$colum} ";
                    }
                    if (!$this->isSubQuery) {
                        $this->QueryCommand .= "SELECT {$col}FROM {$table}";
                    } else {
                        $this->subQueryCommand .= "SELECT {$col}FROM {$table}";
                    }
                    return $this;
                } else {
                    $CreateException->EchoError($this->SysData, $this->langPack, "ColTableMustBeArray", dirname(__FILE__)."/".$this->LogDir);
                    return False;
                }
            }else{
                $CreateException->EchoError($this->SysData, $this->langPack, "ColTableMustBeSet", dirname(__FILE__)."/".$this->LogDir);
                return False;
            }
        }else{
            $CreateException->EchoError($this->SysData, $this->langPack, "MustSubQuery", dirname(__FILE__)."/".$this->LogDir);
            return False;
        }
    }

    public function Insert($table, $mode, $column, $value=Null)
    {
        if($this->nextSubQuery){
            $this->nextSubQuery = False;
        }
        require_once(dirname(__FILE__)."/".$this->ExceptionProcessor);
        $CreateException = new ErrorProcessor();
        if(empty($this->QueryMode) or $this->isSubQuery){
            $this->QueryMode = "INSERT";
            if(!empty($table) and !empty($column)) {
                if (is_string($table) and is_array($column)) {
                    $col = Null;
                    $val = Null;
                    if(!empty($mode)) {
                        if ($mode == "Separate" and !empty($value) and is_array($value)) {
                            foreach ($column as $colum) {
                                $col .= "{$colum},";
                            }
                            foreach ($value as $valu) {
                                $val .= "{$valu},";
                            }
                        } else if ($mode == "Associative") {
                            foreach ($column as $key => $valu) {
                                $col .= "{$key},";
                                $val .= "{$valu},";
                            }
                        }
                        if(!$this->isSubQuery){
                            $col = rtrim($col, ',');
                            $val = rtrim($val, ',');
                            $this->QueryCommand .= "INSERT INTO {$table} ({$col}) VALUES ({$val})";
                        }else{
                            $col = rtrim($col, ',');
                            $val = rtrim($val, ',');
                            $this->subQueryCommand .= "INSERT INTO {$table} ({$col}) VALUES ({$val})";
                        }
                        return $this;
                    }else{
                        $CreateException->EchoError($this->SysData, $this->langPack, "MustBeSetMode", dirname(__FILE__)."/".$this->LogDir);
                        return False;
                    }
                }
            }else{
                $CreateException->EchoError($this->SysData, $this->langPack, "ColTableMustBeSet", dirname(__FILE__)."/".$this->LogDir);
                return False;
            }
        }else{
            $CreateException->EchoError($this->SysData, $this->langPack, "MustSubQuery", dirname(__FILE__)."/".$this->LogDir);
            return False;
        }
    }

    public function Update($table, $mode, $column, $value=Null)
    {
        if($this->nextSubQuery){
            $this->nextSubQuery = False;
        }
        require_once(dirname(__FILE__)."/".$this->ExceptionProcessor);
        $CreateException = new ErrorProcessor();
        if(empty($this->QueryMode) or $this->isSubQuery){
            $this->QueryMode = "UPDATE";
            if(!empty($table) and !empty($column)) {
                if (is_string($table) and is_array($column)) {
                    $dock = array();
                    if(!empty($mode)) {
                        if ($mode == "Separate" and !empty($value) and is_array($value)) {
                            try {
                                for ($i = 0; $i < count($value); $i++) {
                                    $dock .= "{$column[$i]}={$value[$i]}";
                                }
                            }catch(Exception $e){
                                $CreateException->EchoError($this->SysData, $this->langPack, "ColValDoesntMatch", dirname(__FILE__)."/".$this->LogDir);
                            }
                        } else if ($mode == "Associative") {
                            foreach ($column as $key => $valu) {
                                $dock .= "{$key}={$valu},";
                            }
                        }
                        if(!$this->isSubQuery){
                            $dock = rtrim($dock, ',');
                            $this->QueryCommand .= "UPDATE {$table} SET {$dock}";
                        }else{
                            $dock = rtrim($dock, ',');
                            $this->subQueryCommand .= "UPDATE {$table} SET {$dock}";
                        }
                        return $this;
                    }else{
                        $CreateException->EchoError($this->SysData, $this->langPack, "MustBeSetMode", dirname(__FILE__)."/".$this->LogDir);
                        return False;
                    }
                }
            }else{
                $CreateException->EchoError($this->SysData, $this->langPack, "ColTableMustBeSet", dirname(__FILE__)."/".$this->LogDir);
                return False;
            }
        }else{
            $CreateException->EchoError($this->SysData, $this->langPack, "MustSubQuery", dirname(__FILE__)."/".$this->LogDir);
            return False;
        }
    }

    public function Delete($table)
    {
        if($this->nextSubQuery){
            $this->nextSubQuery = False;
        }
        require_once(dirname(__FILE__)."/".$this->ExceptionProcessor);
        $CreateException = new ErrorProcessor();
        if(empty($this->QueryMode) or $this->isSubQuery){
            $this->QueryMode = "DELETE";
            if(!empty($table)) {
                if(!$this->isSubQuery) {
                    $this->QueryCommand .= "DELETE FROM {$table}";
                }else{
                    $this->subQueryCommand .= "DELETE FROM {$table}";
                }
                return $this;
            }else{
                $CreateException->EchoError($this->SysData, $this->langPack, "TableMustBeSet", dirname(__FILE__)."/".$this->LogDir);
                return False;
            }
        }else{
            $CreateException->EchoError($this->SysData, $this->langPack, "MustSubQuery", dirname(__FILE__)."/".$this->LogDir);
            return False;
        }
    }

    public function Where($terms1, $terms2, $type)
    {
        require_once(dirname(__FILE__)."/".$this->ExceptionProcessor);
        $CreateException = new ErrorProcessor();
        if(!$this->nextSubQuery) {
            if(!empty($this->QueryMode)) {
                if (empty($this->now) or !empty($this->now) and $this->now != "WHERE") {
                    if (!empty($terms1) and is_string($terms1) and !empty($terms2) and is_string($terms2) and !empty($type) and is_string($type)) {
                        if ($type == "eq" or $type == "noteq" or $type == "subquery") {
                            $mode = "";
                            if ($type == "eq") {
                                $mode = "=";
                            } else if ($type == "noteq") {
                                $mode = "!=";
                            }
                            if (!$this->isSubQuery) {
                                $this->QueryCommand .= " WHERE {$terms1} {$mode} ?";
                                $this->now = "WHERE";
                                array_push($this->whereTerms, $terms2);
                                #$this->whereTerms += $terms2;
                            } else {
                                $this->subQueryCommand .= " WHERE {$terms1} {$mode} ?";
                                $this->now = "WHERE";
                                array_push($this->whereTerms, $terms2);
                                #$this->whereTerms += $terms2;
                            }
                            return $this;
                        } else {
                            $CreateException->EchoError($this->SysData, $this->langPack, "CantRecognizeType", dirname(__FILE__)."/".$this->LogDir);
                            return False;
                        }
                    } else {
                        $CreateException->EchoError($this->SysData, $this->langPack, "TermsTypeMustArray", dirname(__FILE__)."/".$this->LogDir);
                        return False;
                    }
                } else {
                    $CreateException->EchoError($this->SysData, $this->langPack, "CantWUse", dirname(__FILE__)."/".$this->LogDir);
                    return False;
                }
            }else{
                $CreateException->EchoError($this->SysData, $this->langPack, "MustMainQuery", dirname(__FILE__)."/".$this->LogDir);
                return False;
            }
        }else{
            $CreateException->EchoError($this->SysData, $this->langPack, "MustMainQuery", dirname(__FILE__)."/".$this->LogDir);
            return False;
        }
    }

    public function GroupBy($column)
    {
        require_once(dirname(__FILE__)."/".$this->ExceptionProcessor);
        $CreateException = new ErrorProcessor();
        if(!$this->nextSubQuery){
            if(empty($this->now) or !empty($this->now) and $this->now != "GROUPBY"){
                if(is_string($column)){
                    if(!$this->isSubQuery){
                        $this->now = "GROUPBY";
                        $colum = htmlspecialchars($column);
                        $this->QueryCommand .= "GROUP BY {$colum}";
                    }else{
                        $this->now = "GROUPBY";
                        $colum = htmlspecialchars($column);
                        $this->QueryCommand .= "GROUP BY {$colum}";
                    }
                    return $this;
                }else{
                    $CreateException->EchoError($this->SysData, $this->langPack, "OnlyString", dirname(__FILE__)."/".$this->LogDir);
                    return False;
                }
            }else{
                $CreateException->EchoError($this->SysData, $this->langPack, "CantWUse", dirname(__FILE__)."/".$this->LogDir);
                return False;
            }
        }else{
            $CreateException->EchoError($this->SysData, $this->langPack, "MustMainQuery", dirname(__FILE__)."/".$this->LogDir);
            return False;
        }
    }

    public function OrderBy($column, $mode="DESC")
    {
        require_once(dirname(__FILE__)."/".$this->ExceptionProcessor);
        $CreateException = new ErrorProcessor();
        if(!$this->nextSubQuery){
            if(empty($this->now) or !empty($this->now) and $this->now != "GROUPBY"){
                if(is_string($column)){
                    if(!$this->isSubQuery){
                        $this->now = "GROUPBY";
                        $colum = htmlspecialchars($column);
                        $this->QueryCommand .= "GROUP BY {$colum} {$mode}";
                    }else{
                        $this->now = "GROUPBY";
                        $colum = htmlspecialchars($column);
                        $this->QueryCommand .= "GROUP BY {$colum} {$mode}";
                    }
                    return $this;
                }else{
                    $CreateException->EchoError($this->SysData, $this->langPack, "OnlyString", dirname(__FILE__)."/".$this->LogDir);
                    return False;
                }
            }else{
                $CreateException->EchoError($this->SysData, $this->langPack, "CantWUse", dirname(__FILE__)."/".$this->LogDir);
                return False;
            }
        }else{
            $CreateException->EchoError($this->SysData, $this->langPack, "MustMainQuery", dirname(__FILE__)."/".$this->LogDir);
            return False;
        }
    }

    public function AndQuery($terms1, $terms2, $type){
        require_once(dirname(__FILE__)."/".$this->ExceptionProcessor);
        $CreateException = new ErrorProcessor();
        if(!$this->nextSubQuery){
            if (!empty($terms1) and is_string($terms1) and !empty($terms2) and is_string($terms2) and !empty($type) and is_string($type)) {
                if ($type == "eq" or $type == "noteq" or $type == "subquery") {
                    $mode = "";
                    if ($type == "eq") {
                        $mode = "=";
                    } else if ($type == "noteq") {
                        $mode = "!=";
                    }
                    if (!$this->isSubQuery) {
                        $this->QueryCommand .= " AND {$terms1} {$mode} ?";
                        $this->now = "AND";
                        $this->whereTerms += $terms2;
                    } else {
                        $this->subQueryCommand .= " AND {$terms1} {$mode} ?";
                        $this->now = "AND";
                        $this->whereTerms += $terms2;
                    }
                    return $this;
                } else {
                    $CreateException->EchoError($this->SysData, $this->langPack, "CantRecognizeType", dirname(__FILE__)."/".$this->LogDir);
                    return False;
                }
            } else {
                $CreateException->EchoError($this->SysData, $this->langPack, "TermsTypeMustArray", dirname(__FILE__)."/".$this->LogDir);
                return False;
            }
        }else{
            $CreateException->EchoError($this->SysData, $this->langPack, "MustMainQuery", dirname(__FILE__)."/".$this->LogDir);
            return False;
        }
    }

    public function StartSubQuery(){
        require_once(dirname(__FILE__)."/".$this->ExceptionProcessor);
        $CreateException = new ErrorProcessor();
        if(empty($this->now) or !empty($this->now) and $this->now != "STARTSUBQ"){
            $this->isSubQuery = True;
            $this->now = "STARTSUBQ";
            $this->QueryCommand .= "(";
            return $this;
        }else{
            $CreateException->EchoError($this->SysData, $this->langPack, "CantWUse", dirname(__FILE__)."/".$this->LogDir);
            return False;
        }
    }

    public function EndSubQuery(){
        require_once(dirname(__FILE__)."/".$this->ExceptionProcessor);
        $CreateException = new ErrorProcessor();
        if(!empty($this->now) and $this->now != "ENDSUBQ"){
            if(!$this->nextSubQuery) {
                $this->isSubQuery = False;
                $this->now = "ENDSUBQ";
                $this->QueryCommand .= $this->subQueryCommand . ")";
                $this->subQueryCommand = Null;
                return $this;
            }else{
                $CreateException->EchoError($this->SysData, $this->langPack, "MustMainQuery", dirname(__FILE__)."/".$this->LogDir);
                return False;
            }
        }else{
            $CreateException->EchoError($this->SysData, $this->langPack, "CantWUse", dirname(__FILE__)."/".$this->LogDir);
            return False;
        }
    }

    public function MFetch($fetchmode="both")
    {
        require_once(dirname(__FILE__)."/".$this->ExceptionProcessor);
        $CreateException = new ErrorProcessor();
        if($this->executed){
            if($fetchmode == "both" or $fetchmode == "assoc" or $fetchmode == "keypair" or $fetchmode == "column"){
                $fetchM = Null;
                if($fetchmode == "both"){
                    $fetchM = PDO::FETCH_BOTH;
                }else if($fetchmode == "assoc"){
                    $fetchM = PDO::FETCH_ASSOC;
                }else if($fetchmode == "keypair"){
                    $fetchM = PDO::FETCH_KEY_PAIR;
                }else if($fetchmode == "column"){
                    $fetchM = PDO::FETCH_COLUMN;
                }
                return $this->statement->fetch($fetchM);
            }else{
                $CreateException->EchoError($this->SysData, $this->langPack, "WrongFetchMode", dirname(__FILE__)."/".$this->LogDir);
                return False;
            }
        }else{
            $CreateException->EchoError($this->SysData, $this->langPack, "CantFetchBeforeRun", dirname(__FILE__)."/".$this->LogDir);
            return False;
        }
    }

    public function MFetchAll($fetchmode="both")
    {
        require_once(dirname(__FILE__)."/".$this->ExceptionProcessor);
        $CreateException = new ErrorProcessor();
        if($fetchmode == "both" or $fetchmode == "assoc" or $fetchmode == "keypair" or $fetchmode == "column"){
            $fetchM = Null;
            if($fetchmode == "both"){
                $fetchM = PDO::FETCH_BOTH;
            }else if($fetchmode == "assoc"){
                $fetchM = PDO::FETCH_ASSOC;
            }else if($fetchmode == "keypair"){
                $fetchM = PDO::FETCH_KEY_PAIR;
            }else if($fetchmode == "column"){
                $fetchM = PDO::FETCH_COLUMN;
            }else if($fetchmode == "unique"){
                $fetchM = PDO::FETCH_UNIQUE;
            }else if($fetchmode == "group"){
                $fetchM = PDO::FETCH_GROUP;
            }
            $this->statement->fetch($fetchM);
            return $this;
        }else{
            $CreateException->EchoError($this->SysData, $this->langPack, "WrongFetchMode", dirname(__FILE__)."/".$this->LogDir);
            return False;
        }
    }

    public function Run()
    {
        require_once(dirname(__FILE__)."/".$this->ExceptionProcessor);
        $CreateException = new ErrorProcessor();
        if(!$this->isSubQuery) {
            if(empty($this->prepared)) {
                $this->statement = $this->DBInstance->prepare($this->QueryCommand);
                $mode = Null;
                foreach ($this->whereTerms as $key => $value) {
                    if (is_numeric($value)) {
                        $mode = PDO::PARAM_INT;
                    } else if (is_bool($value)) {
                        $mode = PDO::PARAM_BOOL;
                    } else {
                        $mode = PDO::PARAM_STR;
                    }
                    $this->statement->bindValue($key + 1, htmlspecialchars($value), $mode);
                }
                try {
                    $this->statement->execute();
                    $this->executed = True;
                    return $this;
                } catch (Exception $e) {
                    $CreateException->EchoError($this->SysData, $this->langPack, "PHPSystemException", dirname(__FILE__) . "/" . $this->LogDir, $e);
                    return False;
                }
            }else{
                $this->statement = $this->prepared;
                $this->statement->execute();
                $this->executed = True;
                return $this;
            }
        }else{
            $CreateException->EchoError($this->SysData, $this->langPack, "MustBeCloseSubQuery", dirname(__FILE__)."/".$this->LogDir);
            return False;
        }
    }

}