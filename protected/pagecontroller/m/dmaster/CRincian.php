<?php
prado::using ('Application.MainPageM');
class CRincian extends MainPageM {	
	public function onLoad ($param) {
		parent::onLoad ($param);        
        $this->createObj('DMaster'); 
        $this->showDMaster=true;
        $this->showRincian=true;        
		if (!$this->IsPostBack&&!$this->IsCallBack) {
            if (!isset($_SESSION['currentPageRincian'])||$_SESSION['currentPageRincian']['page_name']!='m.dmaster.Rincian') {
                $_SESSION['currentPageRincian']=array('page_name'=>'m.dmaster.Rincian','page_num'=>0,'search'=>false,'no_rek3'=>'none');												
			}
            $_SESSION['currentPageRincian']['search']=false; 
            
            $daftar_jenis=$this->DMaster->getList('rek3',array('no_rek3','nama_rek3'),'no_rek3',null,7);
            $daftar_jenis['none']='DAFTAR REKENING';
            $this->cmbJenis->DataSource=$daftar_jenis;
            $this->cmbJenis->Text=$_SESSION['currentPageRincian']['no_rek3'];
            $this->cmbJenis->dataBind();
            $this->labelfiltered->Text=$_SESSION['currentPageRincian']['no_rek3']=='none'?'[none]':'[selected]';
			$this->populateData ();		
		}
	}
	public function renderCallback ($sender,$param) {
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {
		$_SESSION['currentPageRincian']['page_num']=$param->NewPageIndex;
		$this->populateData();
	} 
    public function searchRecord ($sender,$param) {
		$_SESSION['currentPageRincian']['search']=true;
		$this->populateData($_SESSION['currentPageRincian']['search']);
	}
    public function filterJenis ($sender,$param) {
        $no_rek3=$this->cmbJenis->Text;
        $_SESSION['currentPageRincian']['no_rek3']=$no_rek3; 
        $this->labelfiltered->Text=$no_rek3=='none'?'[none]':'[selected]';
        $this->populateData();
    }
	protected function populateData ($search=false) {
        $no_rek3=$_SESSION['currentPageRincian']['no_rek3'];
        $str_rek=$no_rek3=='none'?'':" WHERE no_rek3='$no_rek3'";
        if ($search) {
            $str = "SELECT no_rek4,nama_rek4 FROM rek4";
            $txtsearch=addslashes($this->txtKriteria->Text);
            switch ($this->cmbKriteria->Text) {                
                case 'kode' :
                    $clausa=" WHERE no_rek4 LIKE '%$txtsearch%'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("rek4$clausa",'no_rek4');
                    $str = "$str $clausa";
                break;
                case 'nama' :
                    $clausa=" WHERE nama_rek4 LIKE '%$txtsearch%'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("rek4$clausa",'no_rek4');
                    $str = "$str $clausa";
                break;
            }
        }else{
            $str = "SELECT no_rek4,nama_rek4 FROM rek4$str_rek";
            $jumlah_baris=$this->DB->getCountRowsOfTable ("rek4$str_rek",'no_rek4');
        }
		$this->RepeaterS->CurrentPageIndex=$_SESSION['currentPageRincian']['page_num'];				
		$this->RepeaterS->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterS->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterS->PageSize;		
		$itemcount=$this->RepeaterS->VirtualItemCount;
		$limit=$this->RepeaterS->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=10;$_SESSION['currentPageRincian']['page_num']=0;}
        $str = "$str ORDER BY no_rek4 ASC LIMIT $offset,$limit";
		$r=$this->DB->getRecord($str,$offset+1);        
		$this->RepeaterS->DataSource=$r;
		$this->RepeaterS->dataBind();
	}
	public function addProcess ($sender,$param) {
		$this->idProcess='add';		        
        $this->cmbAddJenis->DataSource=$this->DMaster->getList('rek3',array('no_rek3','nama_rek3'),'no_rek3',null,7);
        $this->cmbAddJenis->dataBind();	
	} 
	public function cmbJenisChanged ($sender,$param) {
		$this->idProcess='add';		
		$transaksi=$this->cmbAddJenis->Text;
        if ($transaksi=='none')
            $this->lblAddKodeJenis->Text='';
        else
            $this->lblAddKodeJenis->Text="$transaksi.";
	}
	public function checkKodeRincian ($sender,$param) {
		$this->idProcess=$sender->getId()==='checkAddKodeRincian'?'add':'edit';
        $no_rek4=$param->Value;
        if ($no_rek4 != '') {
            try {
                $kode_transaksi=$sender->getId()==='checkAddKodeRincian'?$this->lblAddKodeJenis->Text:$this->lblEditKodeJenis->Text;
                $no_rek4 = $kode_transaksi.$no_rek4;
                if ($this->hiddennorek4->Value != $no_rek4){                                        
                    if ($this->DB->checkRecordIsExist ('no_rek4','rek4',$no_rek4)) {
                        throw new Exception("No. Rekening ($no_rek4) sudah tidak tersedia silahkan ganti dengan yang lain.");				
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
			$nama_rincian=strtoupper(addslashes($this->txtAddNamaRincian->Text));
            $kode_jenis=$this->cmbAddJenis->Text;
			$kode_rincian=$kode_jenis.'.'.$this->txtAddKodeRincian->Text;
			$str = "INSERT INTO rek4 SET no_rek4='$kode_rincian',no_rek3='$kode_jenis',nama_rek4='$nama_rincian'";			
            $this->DB->insertRecord($str);
            $this->Pengguna->insertNewActivity($this->Page->getPagePath(),"Menambah data master Rekening Rincian dengan id ($kode_rincian) berhasil dilakukan.");
            $this->redirect('dmaster.Rincian',true);
		}
	}
	public function editRecord ($sender,$param) {		
		$this->idProcess='edit';
		$id=$this->getDataKeyField($sender,$this->RepeaterS);        
		$result = $this->DMaster->getList("rek4 WHERE no_rek4='$id'",array('no_rek4','no_rek3','nama_rek4'));		
    	$this->hiddennorek4->Value=$id;
		$this->lblEditKodeJenis->Text=$result[1]['no_rek3'].'.';
		$this->txtEditKodeRincian->Text=$this->DMaster->getKodeRekeningTerakhir($result[1]['no_rek4']);
		$this->txtEditNamaRincian->Text=$result[1]['nama_rek4'];
	}	
	
	public function updateData($sender,$param) {
		if ($this->Page->IsValid) {
			$id=$this->hiddennorek4->Value;
            $no_rek3=$this->lblEditKodeJenis->Text;
			$no_rek4=$no_rek3.$this->txtEditKodeRincian->Text;
			$nama_rincian=strtoupper(addslashes($this->txtEditNamaRincian->Text));
			$str = "UPDATE rek4 SET no_rek4='$no_rek4',nama_rek4='$nama_rincian' WHERE no_rek4='$id'";
			$this->DB->updateRecord($str);
            $this->Pengguna->insertNewActivity($this->Page->getPagePath(),"Mengubah data master Rekening Rincian dengan id ($id) berhasil dilakukan.");
            $this->redirect('dmaster.Rincian',true);				
		}
	}
	public function deleteRecord ($sender,$param) {
		$id=$this->getDataKeyField($sender,$this->RepeaterS);
        if ($this->DB->checkRecordIsExist('no_rek4','rek5',$id)) {
            $this->Pengguna->insertNewActivity($this->Page->getPagePath(),"Tidak bisa menghapus data master Rekening ($id) karena sedang digunakan di Objek.");
            $this->labelErrorMessage->Text="Tidak bisa menghapus data master Rekening Rincian dengan ID ($id) karena sedang digunakan di Objek";
            $this->DialogErrorMessage->Open();
        }else{
    		$this->DB->deleteRecord("rek4 WHERE no_rek4='$id'");	
            $this->Pengguna->insertNewActivity($this->Page->getPagePath(),"Menghapus data master Rekening Rincian dengan id ($id) berhasil dilakukan.");
    		$this->redirect('dmaster.Rincian',true);
        }
		
	}
}