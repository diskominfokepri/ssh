<?php
prado::using ('Application.MainPageM');
class CJenis extends MainPageM {	
	public function onLoad ($param) {
		parent::onLoad ($param);        
        $this->createObj('DMaster'); 
        $this->showDMaster=true;
        $this->showJenis=true;        
		if (!$this->IsPostBack&&!$this->IsCallBack) {
            if (!isset($_SESSION['currentPageJenis'])||$_SESSION['currentPageJenis']['page_name']!='m.dmaster.Jenis') {
                $_SESSION['currentPageJenis']=array('page_name'=>'m.dmaster.Jenis','page_num'=>0);												
			}
			$this->populateData ();		
		}
	}
	public function renderCallback ($sender,$param) {
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {
		$_SESSION['currentPageJenis']['page_num']=$param->NewPageIndex;
		$this->populateData();
	} 
	protected function populateData () {
		$this->RepeaterS->CurrentPageIndex=$_SESSION['currentPageJenis']['page_num'];
		$jumlah_baris=$this->DB->getCountRowsOfTable ('rek3','no_rek3');		
		$this->RepeaterS->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterS->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterS->PageSize;		
		$itemcount=$this->RepeaterS->VirtualItemCount;
		$limit=$this->RepeaterS->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=10;$_SESSION['currentPageJenis']['page_num']=0;}
        $str = "SELECT no_rek3,nama_rek3 FROM rek3 ORDER BY no_rek3 ASC LIMIT $offset,$limit";
		$r=$this->DB->getRecord($str,$offset+1);        
		$this->RepeaterS->DataSource=$r;
		$this->RepeaterS->dataBind();
	}
	public function addProcess ($sender,$param) {
		$this->idProcess='add';		        
        $this->cmbAddKelompok->DataSource=$this->DMaster->getList('rek2',array('no_rek2','nama_rek2'),'no_rek2',null,7);  ;
        $this->cmbAddKelompok->dataBind();	
	} 
	public function cmbKelompokChanged ($sender,$param) {
		$this->idProcess='add';		
		$transaksi=$this->cmbAddKelompok->Text;
        if ($transaksi=='none')
            $this->lblAddKodeKelompok->Text='';
        else
            $this->lblAddKodeKelompok->Text="$transaksi.";
	}
	public function checkKodeJenis ($sender,$param) {
		$this->idProcess=$sender->getId()==='checkAddKodeJenis'?'add':'edit';
        $no_rek3=$param->Value;
        if ($no_rek3 != '') {
            try {
                $kode_transaksi=$sender->getId()==='checkAddKodeJenis'?$this->lblAddKodeKelompok->Text:$this->lblEditKodeKelompok->Text;
                $no_rek3 = $kode_transaksi.$no_rek3;
                if ($this->hiddennorek3->Value != $no_rek3){                                        
                    if ($this->DB->checkRecordIsExist ('no_rek3','rek3',$no_rek3)) {
                        throw new Exception("No. Rekening ($no_rek3) sudah tidak tersedia silahkan ganti dengan yang lain.");				
                    }
                }
            }catch (Exception $e) {
                $param->IsValid=false;
                $sender->ErrorMessage=$e->getMessage();
            }
        }		
	}
	public function saveData($sender,$param) {
		if ($this->Page->IsValid) {
			$nama_jenis=strtoupper(addslashes($this->txtAddNamaJenis->Text));
            $kode_kelompok=$this->cmbAddKelompok->Text;
			$kode_jenis=$kode_kelompok.'.'.$this->txtAddKodeJenis->Text;
			$str = "INSERT INTO rek3 SET no_rek3='$kode_jenis',no_rek2='$kode_kelompok',nama_rek3='$nama_jenis'";			
            $this->DB->insertRecord($str);
            $this->Pengguna->insertNewActivity($this->Page->getPagePath(),"Menambah data master Jenis Transaksi dengan id ($kode_jenis) berhasil dilakukan.");
            $this->redirect('dmaster.Jenis',true);
		}
	}
	public function editRecord ($sender,$param) {		
		$this->idProcess='edit';
		$id=$this->getDataKeyField($sender,$this->RepeaterS);        
		$result = $this->DMaster->getList("rek3 WHERE no_rek3='$id'",array('no_rek3','no_rek2','nama_rek3'));		
    	$this->hiddennorek3->Value=$id;
		$this->lblEditKodeKelompok->Text=$result[1]['no_rek2'].'.';
		$this->txtEditKodeJenis->Text=$this->DMaster->getKodeRekeningTerakhir($result[1]['no_rek3']);
		$this->txtEditNamaJenis->Text=$result[1]['nama_rek3'];
	}	
	
	public function updateData($sender,$param) {
		if ($this->Page->IsValid) {
			$id=$this->hiddennorek3->Value;
            $no_rek2=$this->lblEditKodeKelompok->Text;
			$no_rek3=$no_rek2.$this->txtEditKodeJenis->Text;
			$nama_jenis=strtoupper(addslashes($this->txtEditNamaJenis->Text));
			$str = "UPDATE rek3 SET no_rek3='$no_rek3',nama_rek3='$nama_jenis' WHERE no_rek3='$id'";
			$this->DB->updateRecord($str);
            $this->Pengguna->insertNewActivity($this->Page->getPagePath(),"Mengubah data master Jenis Transaksi dengan id ($id) berhasil dilakukan.");
            $this->redirect('dmaster.Jenis',true);				
		}
	}
	public function deleteRecord ($sender,$param) {
		$id=$this->getDataKeyField($sender,$this->RepeaterS);
        if ($this->DB->checkRecordIsExist('no_rek3','rek4',$id)) {
            $this->Pengguna->insertNewActivity($this->Page->getPagePath(),"Tidak bisa menghapus data master Rekening ($id) karena sedang digunakan di Kelompok.");
            $this->labelErrorMessage->Text="Tidak bisa menghapus data master Rekening Jenis dengan ID ($id) karena sedang digunakan di Rincian";
            $this->DialogErrorMessage->Open();
        }else{
    		$this->DB->deleteRecord("rek3 WHERE no_rek3='$id'");	
            $this->Pengguna->insertNewActivity($this->Page->getPagePath(),"Menghapus data master Jenis Transaksi dengan id ($id) berhasil dilakukan.");
    		$this->redirect('dmaster.Jenis',true);
        }
		
	}
}