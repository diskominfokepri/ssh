<?php
prado::using ('Application.MainPageM');
class CKelompok extends MainPageM {	
	public function onLoad ($param) {
		parent::onLoad ($param);
        $this->showKelompok=true;    
        $this->showDMaster=true;
        $this->createObj('DMaster');
		if (!$this->IsPostBack&&!$this->IsCallBack) {		
            if (!isset($_SESSION['currentPageKelompok'])||$_SESSION['currentPageKelompok']['page_name']!='m.dmaster.Kelompok') {
                $_SESSION['currentPageKelompok']=array('page_name'=>'m.dmaster.Kelompok','page_num'=>0);												
			}			
            $this->populateData ();
		}
	}
    public function renderCallback ($sender,$param) {
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {
		$_SESSION['currentPageKelompok']['page_num']=$param->NewPageIndex;
		$this->populateData();
	} 
	protected function populateData () {
		$this->RepeaterS->CurrentPageIndex=$_SESSION['currentPageKelompok']['page_num'];
		$jumlah_baris=$this->DB->getCountRowsOfTable ('rek2','no_rek2');		
		$this->RepeaterS->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterS->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterS->PageSize;		
		$itemcount=$this->RepeaterS->VirtualItemCount;
		$limit=$this->RepeaterS->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=10;$_SESSION['currentPageKelompok']['page_num']=0;}
        $str = "SELECT no_rek2,nama_rek2 FROM rek2 ORDER BY no_rek2 ASC LIMIT $offset,$limit";
		$r=$this->DB->getRecord($str,$offset+1);        
		$this->RepeaterS->DataSource=$r;
		$this->RepeaterS->dataBind();
	}
	public function addProcess ($sender,$param) {
		$this->idProcess='add';		
        $this->cmbAddTransaksi->DataSource=$this->DMaster->getList('rek1',array('no_rek1','nama_rek1'),'no_rek1',null,7);  
        $this->cmbAddTransaksi->dataBind();
	}    
	public function cmbTransaksiChanged ($sender,$param) {
		$this->idProcess='add';		
        $transaksi=$this->cmbAddTransaksi->Text;
        if ($transaksi=='none')
            $this->lblAddKodeTransaksi->Text='';
        else
            $this->lblAddKodeTransaksi->Text="$transaksi.";
	}
	public function checkKodeKelompok ($sender,$param) {
		$this->idProcess=$sender->getId()==='checkAddKodeKelompok'?'add':'edit';
        $no_rek2=$param->Value;
        if ($no_rek2 != '') {
            try {
                $kode_transaksi=$sender->getId()==='checkAddKodeKelompok'?$this->lblAddKodeTransaksi->Text:$this->lblEditKodeTransaksi->Text;
                $no_rek2 = $kode_transaksi.$no_rek2;
                if ($this->hiddennorek2->Value != $no_rek2){                                        
                    if ($this->DB->checkRecordIsExist ('no_rek2','rek2',$no_rek2)) {
                        throw new Exception("No. Rekening ($no_rek2) sudah tidak tersedia silahkan ganti dengan yang lain.");
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
			$nama_kelompok=strtoupper(addslashes($this->txtAddNamaKelompok->Text));
            $kode_transaksi=$this->cmbAddTransaksi->Text;
			$kode_kelompok=$this->lblAddKodeTransaksi->Text.$this->txtAddKodeKelompok->Text;
			$str = "INSERT INTO rek2 SET no_rek2='$kode_kelompok',no_rek1='$kode_transaksi',nama_rek2='$nama_kelompok'";
			$this->Pengguna->insertNewActivity($this->Page->getPagePath(),"Menambah data master Rekening Kelompok dengan id ($kode_kelompok) berhasil dilakukan.");
            $this->DB->insertRecord($str);
            $this->redirect('dmaster.Kelompok',true);
		}
	}
	public function editRecord ($sender,$param) {		
		$this->idProcess='edit';
		$id=$this->getDataKeyField($sender,$this->RepeaterS);
		$result = $this->DMaster->getList("rek2 WHERE no_rek2='$id'",array('no_rek2','no_rek1','nama_rek2'));		
		$this->hiddennorek2->Value=$id;		
		$this->lblEditKodeTransaksi->Text=$result[1]['no_rek1'].'.';
		$this->txtEditKodeKelompok->Text=$this->DMaster->getKodeRekeningTerakhir($result[1]['no_rek2']);
		$this->txtEditNamaKelompok->Text=$result[1]['nama_rek2'];
	}	
	
	public function updateData($sender,$param) {
		if ($this->Page->IsValid) {
            $id=$this->hiddennorek2->Value;
			$kode=$this->lblEditKodeTransaksi->Text.$this->txtEditKodeKelompok->Text;			
			$nama_kelompok=strtoupper(addslashes($this->txtEditNamaKelompok->Text));
			$str = "UPDATE rek2 SET no_rek2='$kode',nama_rek2='$nama_kelompok' WHERE no_rek2='$id'";
			$this->DB->updateRecord($str);
            $this->Pengguna->insertNewActivity($this->Page->getPagePath(),"Mengubah data master Rekening Kelompok dengan id ($id) berhasil dilakukan.");
            $this->redirect('dmaster.Kelompok',true);			
		}
	}
    public function deleteRecord ($sender,$param) {
        $id=$this->getDataKeyField($sender,$this->RepeaterS);
        if ($this->DB->checkRecordIsExist('no_rek2','rek3',$id)) {
            $this->Pengguna->insertNewActivity($this->Page->getPagePath(),"Tidak bisa menghapus data master Rekening ($id) karena sedang digunakan di Jenis.");
            $this->labelErrorMessage->Text="Tidak bisa menghapus data master Rekening Kelompok dengan ID ($id) karena sedang digunakan di Jenis";
            $this->DialogErrorMessage->Open();
        }else{
    		$this->DB->deleteRecord("rek2 WHERE no_rek2='$id'");
            $this->Pengguna->insertNewActivity($this->Page->getPagePath(),"Menghapus data master Rekening Kelompok dengan id ($id) berhasil dilakukan.");
    		$this->redirect('dmaster.Kelompok',true);
        }
	}
}