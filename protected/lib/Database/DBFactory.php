<?php
use Prado\Data\TDbConnection;
class DBFactory extends TModule {	
	public $Link;
	private $Host;
	private $DbPort;
	private $UserName;
	private $UserPassword;	
	private $DbName;	
	private $DbType;	
	public function init($config)
	{
		$this->linkOpen();
	}	
	private function prepareParameters () {
		$db=$this->Application->getParameters ();		
		$this->Host = $db['db_host'];
		$this->UserName=$db['db_username'];
		$this->UserPassword=$db['db_userpassword'];
		$this->DbName=$db['db_name'];
		$this->DbType=$db['db_type'];
		$this->DbPort=$db['db_port'];			
	}	
	private function linkOpen() {
		try {
			$this->prepareParameters();
			$this->Link=new TDbConnection($this->DbType.':host='.$this->Host.';dbname='.$this->DbName,$this->UserName,$this->UserPassword);
			$this->Link->Active=true;
		}
		catch(TDbException $e)
		{
			throw new Exception ($e->getErrorMessage());
		}
	}
	/**
	* mengambil record dari sebuah tabel dalam bentuk array multiple dimensi
	* @param sqlString ini sql string
	* @param offset 
	*
	*/	
	public function getRecord ($sqlString,$offset=1) {				
        $command=$this->Link->createCommand($sqlString);
		$result=$command->query();
		$res = array();
		$counter = 1;
		foreach($result as $row) {
			$row['no']=$offset;
			$res[$counter]=$row;
			$offset+=1;
			$counter+=1;
		}
		return $res;
	}
	/**
	* mengambil record dari sebuah tabel dalam bentuk array satu dimensi
	* @param sqlString ini sql string
	*
	*/	
	public function getRecordOneOnly ($sqlString) {				
        $command=$this->Link->createCommand($sqlString);
		$result=$command->queryRow();
		if (is_array($result)) {			
			return $result;
		}else{
			return array();
		}
	}
	/**
	* Dapatkan jumlah baris dari sebuah tabel
	* @param tableName
	* @return count of rows 
	*/
	public function getCountRowsOfTable ($tableName,$fieldname='*') {
		$str="SELECT COUNT($fieldname) AS jumlah_baris FROM $tableName";	
		$command=$this->Link->createCommand($str);
		return $command->queryScalar();		 
	}
	/**
	* digunakan untuk insert data ke dalam tabel
	* @param param sqlString bila menggunakan Excel2MySQL sqlString menjadi nama tabel
	* @return void
	*/
	public function insertRecord ($sqlString) { 
		$command=$this->Link->createCommand($sqlString);
		$command->execute();		
	}
	
	/**
	* update record in tabel
	* @return void
	*/
	public function updateRecord ($sqlString) {
		$command=$this->Link->createCommand($sqlString);
		$command->execute();	
	}
	/**
	* delete record
	* @return void
	*/
	public function deleteRecord ($str) {
		$sqlString = "DELETE FROM ".$str;
		$command=$this->Link->createCommand($sqlString);
		$command->execute();	
	}
	/**
	* untuk mengecek apakah sebuah record sudah ada atau belum berdasarkan primary key
	* @param field dari tabel
	* @param tabel nama tabel
	* @param idrecord nilai id record
	*/
	public function checkRecordIsExist ($field,$table,$idrecord,$opt=null) {		
		$str = "SELECT 1 FROM $table WHERE $field='$idrecord' $opt";							
		$command = $this->Link->createCommand($str);
		$bool=$command->queryScalar ()>0;				
		return $bool;
	}
}