<?php
prado::using ('Application.MainPageM');
class COPD extends MainPageM {	
	public function onLoad ($param) {
		parent::onLoad ($param);
       
        $this->showDMaster=true;
        $this->createObj('DMaster'); 
		if (!$this->IsPostBack&&!$this->IsCallBack) {		
            if (!isset($_SESSION['currentPageOPD'])||$_SESSION['currentPageOPD']['page_name']!='m.dmaster.OPD') {
                $_SESSION['currentPageOPD']=array('page_name'=>'m.dmaster.OPD','page_num'=>0);												
			}
			$this->populateData ();
		}
	}
	public function renderCallback ($sender,$param) {
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {
		$_SESSION['currentPageOPD']['page_num']=$param->NewPageIndex;
		$this->populateData();
	} 
	protected function populateData () {
        $this->RepeaterS->CurrentPageIndex=$_SESSION['currentPageOPD']['page_num'];
		$jumlah_baris=$this->DB->getCountRowsOfTable ('unit','idunit');		
		$this->RepeaterS->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterS->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterS->PageSize;		
		$itemcount=$this->RepeaterS->VirtualItemCount;
		$limit=$this->RepeaterS->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=10;$_SESSION['currentPageOPD']['page_num']=0;}
        $str = "SELECT idunit, kode_unit, nama_unit FROM unit ORDER BY idunit ASC LIMIT $offset,$limit";
		$r=$this->DB->getRecord($str,$offset+1);        
		$this->RepeaterS->DataSource=$r;
		$this->RepeaterS->dataBind();
	}
	protected function addProcess ($sender,$param) {
		$this->idProcess='add';	
		
	}
	public function checkOPD ($sender,$param) {
		$this->idProcess=$sender->getId()==='checkAddOPD'?'add':'edit';
        $idunit=$param->Value;
        if ($idunit != '') {
            try {
                if ($this->hiddenid->Value != $idunit){                                        
                    if ($this->DB->checkRecordIsExist ('idunit','unit',$idunit)) {
                        throw new Exception("Kode OPD ($idunit) sudah digunakan silahkan ganti dengan yang lain.");				
                    }
                }
            }catch (Exception $e) {
                $param->IsValid=false;
                $sender->ErrorMessage=$e->getMessage();
            }
        }		
	}
	public function saveData ($sender,$param) {
		if ($this->Page->IsValid) {
            $idunit=addslashes($this->txtAddKodeOPD->Text);
			$nama_unit=strtoupper(addslashes($this->txtAddNamaOPD->Text));			
			$str = "INSERT INTO unit SET kode_unit='$idunit',nama_unit='$nama_unit' ";
			$this->DB->insertRecord($str);
			if ($this->Application->Cache) {
                $dataitem=$this->DMaster->getList('unit',array('idunit', 'nama_unit'),'nama_unit',null,7);
                $dataitem['none']='DAFTAR OPD';    
                $this->Application->Cache->set('listunitkerja',$dataitem);
	        }
            $this->Pengguna->insertNewActivity($this->Page->getPagePath(),"Menambah data master OPD dengan id ( $idunit) berhasil dilakukan.");
            $this->redirect('dmaster.OPD',true);
		}
	}
	public function editRecord ($sender,$param) {
		$this->idProcess='edit';
		$id=$this->getDataKeyField($sender,$this->RepeaterS); 
        $this->createObj('DMaster');
		$result = $this->DB->getRecord("SELECT idunit, kode_unit, nama_unit from unit WHERE idunit='$id'");
		$this->hiddenid->Value=$id;		
		$this->txtEditKodeOPD->Text=$result[1]['kode_unit'];
		$this->txtEditNamaOPD->Text=$result[1]['nama_unit'];
	}
	public function updateData($sender,$param) {
		if ($this->Page->IsValid) {
            $id=$this->hiddenid->Value;
            $kode_unit=strtoupper(addslashes($this->txtEditKodeOPD->Text));
			$nama_unit=strtoupper(addslashes($this->txtEditNamaOPD->Text));
			$str = "UPDATE unit SET kode_unit='$kode_unit', nama_unit='$nama_unit' WHERE idunit='$id'";
			$this->DB->updateRecord($str);
			if ($this->Application->Cache) {
                $dataitem=$this->DMaster->getList('unit',array('idunit', 'nama_unit'),'nama_unit',null,7);
                $dataitem['none']='DAFTAR OPD';    
                $this->Application->Cache->set('listunitkerja',$dataitem);
	        }
            $this->Pengguna->insertNewActivity($this->Page->getPagePath(),"Mengubah data OPD dengan id ( $id) berhasil dilakukan.");
            $this->redirect('dmaster.OPD',true);
		}
	}
	public function deleteRecord ($sender,$param) 
	{
		$id=$this->getDataKeyField($sender,$this->RepeaterS);        
		$this->DB->deleteRecord("unit WHERE idunit='$id'");  
		if ($this->Application->Cache) {
                $dataitem=$this->DMaster->getList('unit',array('idunit', 'nama_unit'),'nama_unit',null,7);
                $dataitem['none']='DAFTAR OPD';    
                $this->Application->Cache->set('listunitkerja',$dataitem);
	        }
            $this->Pengguna->insertNewActivity($this->Page->getPagePath(),"Menambah data master OPD dengan id ( $id) berhasil dilakukan.");  		
        $this->Pengguna->insertNewActivity($this->Page->getPagePath(),"Menghapus data OPD dengan id ($id) berhasil dilakukan.");
		$this->redirect('dmaster.OPD',true);       
	}
}