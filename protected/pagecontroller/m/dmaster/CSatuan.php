<?php
prado::using ('Application.MainPageM');
class CSatuan extends MainPageM {	
	public function onLoad ($param) {
		parent::onLoad ($param);
        $this->showSatuan=true; 
        $this->showDMaster=true;
        $this->createObj('DMaster'); 
		if (!$this->IsPostBack&&!$this->IsCallBack) {		
            if (!isset($_SESSION['currentPageSatuan'])||$_SESSION['currentPageSatuan']['page_name']!='m.dmaster.Satuan') {
                $_SESSION['currentPageSatuan']=array('page_name'=>'m.dmaster.Satuan','page_num'=>0);												
			}
			$this->populateData ();
		}
	}
	public function renderCallback ($sender,$param) {
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {
		$_SESSION['currentPageSatuan']['page_num']=$param->NewPageIndex;
		$this->populateData();
	} 
	protected function populateData () {
        $this->RepeaterS->CurrentPageIndex=$_SESSION['currentPageSatuan']['page_num'];
		$jumlah_baris=$this->DB->getCountRowsOfTable ('satuan','id_satuan');		
		$this->RepeaterS->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterS->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterS->PageSize;		
		$itemcount=$this->RepeaterS->VirtualItemCount;
		$limit=$this->RepeaterS->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=10;$_SESSION['currentPageSatuan']['page_num']=0;}
        $str = "SELECT id_satuan,nama_satuan FROM satuan ORDER BY nama_satuan ASC LIMIT $offset,$limit";
		$r=$this->DB->getRecord($str,$offset+1);        
		$this->RepeaterS->DataSource=$r;
		$this->RepeaterS->dataBind();
	}	
	public function checkNamaSatuan ($sender,$param) {
		$this->idProcess=$sender->getId()==='checkAddNamaSatuan'?'add':'edit';
        $nama_satuan=$param->Value;
        if ($nama_satuan != '') {
            try {
                if ($this->hidden_namasatuan->Value != $nama_satuan){                                        
                    if ($this->DB->checkRecordIsExist ('nama_satuan','satuan',$nama_satuan)) {
                        throw new Exception("Nama Satuan ($nama_satuan) sudah tidak tersedia silahkan ganti dengan yang lain.");				
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
            $id_satuan=addslashes($this->txtAddNamaSatuan->Text);
			$nama_satuan=strtoupper(addslashes($this->txtAddNamaSatuan->Text));
			$str = "INSERT INTO satuan SET id_satuan='$id_satuan',nama_satuan='$nama_satuan'";
			$this->DB->insertRecord($str);
			if ($this->Application->Cache) { 
		        $dataitem=$this->DMaster->getList('satuan',array('id_satuan','nama_satuan'),'nama_satuan',null,1);
		        $dataitem['none']='DAFTAR SATUAN';
		        $this->Application->Cache->set('listsatuan',$dataitem);
		    }            
		    $this->Pengguna->insertNewActivity($this->Page->getPagePath(),"Menambah data master Rekening Satuan dengan id ( $id_satuan) berhasil dilakukan.");
		    $this->redirect('dmaster.Satuan',true);
        }
	}
	public function editRecord ($sender,$param) {
		$this->idProcess='edit';
		$id=$this->getDataKeyField($sender,$this->RepeaterS);
        $this->createObj('DMaster');
		$result = $this->DB->getRecord("SELECT nama_satuan FROM satuan WHERE id_satuan='$id'");
		$this->hiddenid->Value=$id;	
		$this->hidden_namasatuan->Value=$result[1]['nama_satuan'];	
		$this->txtEditNamaSatuan->Text=$result[1]['nama_satuan'];
	}
	public function updateData($sender,$param) {
		if ($this->Page->IsValid) {
            $id=$this->hiddenid->Value;
			$nama_satuan=strtoupper(addslashes($this->txtEditNamaSatuan->Text));
			$str = "UPDATE satuan SET nama_satuan='$nama_satuan' WHERE id_satuan='$id'";
			$this->DB->updateRecord($str);
			if ($this->Application->Cache) { 
		        $dataitem=$this->DMaster->getList('satuan',array('id_satuan','nama_satuan'),'nama_satuan',null,1);
		        $dataitem['none']='DAFTAR SATUAN';
		        $this->Application->Cache->set('listsatuan',$dataitem);
		    }  
            $this->Pengguna->insertNewActivity($this->Page->getPagePath(),"Mengubah data master Rekening Satuan dengan id ($id) berhasil dilakukan.");
            $this->redirect('dmaster.Satuan',true);
		}
	}
	public function deleteRecord ($sender,$param) {
		$id=$this->getDataKeyField($sender,$this->RepeaterS);
		try {
			if ($this->DB->checkRecordIsExist('id_satuan','rek5',$id)) {
				throw new Exception("Tidak bisa menghapus data master Rekening Satuan dengan ID ($id) karena sedang digunakan di Kelompok");            
            
	        }
    		$this->DB->deleteRecord("satuan WHERE id_satuan='$id'");
    		if ($this->Application->Cache) { 
		        $dataitem=$this->DMaster->getList('satuan',array('id_satuan','nama_satuan'),'nama_satuan',null,1);
		        $dataitem['none']='DAFTAR SATUAN';
		        $this->Application->Cache->set('listsatuan',$dataitem);
		    }  
            $this->Pengguna->insertNewActivity($this->Page->getPagePath(),"Menghapus data master Rekening Satuan dengan id ($id) berhasil dilakukan.");
    		$this->redirect('dmaster.Satuan',true);	        
		}catch (Exception $e) {
	        $param->IsValid=false;
	        $this->Pengguna->insertNewActivity($this->Page->getPagePath(),"Tidak bisa menghapus data master Satuan ($id) karena sedang digunakan di Objek.");
	    	$this->labelErrorMessage->Text=$e->getMessage();
	        $this->DialogErrorMessage->Open();
        }
        
	}
}