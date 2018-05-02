<?php
prado::using ('Application.MainPageD');
class CTransaksi extends MainPageD {	
	public function onLoad ($param) {
		parent::onLoad ($param);
        $this->showTransaksi=true; 
        $this->showDMaster=true;
        $this->createObj('DMaster'); 
		if (!$this->IsPostBack&&!$this->IsCallBack) {		
            if (!isset($_SESSION['currentPageTransaksi'])||$_SESSION['currentPageTransaksi']['page_name']!='d.dmaster.Transaksi') {
                $_SESSION['currentPageTransaksi']=array('page_name'=>'d.dmaster.Transaksi','page_num'=>0);												
			}
			$this->populateData ();
		}
	}
	public function renderCallback ($sender,$param) {
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {
		$_SESSION['currentPageTransaksi']['page_num']=$param->NewPageIndex;
		$this->populateData();
	} 
	protected function populateData () {
        $this->RepeaterS->CurrentPageIndex=$_SESSION['currentPageTransaksi']['page_num'];
		$jumlah_baris=$this->DB->getCountRowsOfTable ('rek1','no_rek1');		
		$this->RepeaterS->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterS->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterS->PageSize;		
		$itemcount=$this->RepeaterS->VirtualItemCount;
		$limit=$this->RepeaterS->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=10;$_SESSION['currentPageTransaksi']['page_num']=0;}
        $str = "SELECT rek1.no_rek1,rek1.nama_rek1,tb.nama_tipe FROM rek1 LEFT JOIN tipe_barang tb ON (tb.id_tipe=rek1.id_tipe) ORDER BY no_rek1 ASC LIMIT $offset,$limit";
		$r=$this->DB->getRecord($str,$offset+1);        
		$this->RepeaterS->DataSource=$r;
		$this->RepeaterS->dataBind();
	}
	protected function addProcess ($sender,$param) {
		$this->idProcess='add';		
		$this->cmbAddTipe->DataSource=$this->DMaster->getListTipeBarang();
		$this->cmbAddTipe->DataBind();
	}
	public function checkKodeTransaksi ($sender,$param) {
		$this->idProcess=$sender->getId()==='checkAddKodeTransaksi'?'add':'edit';
        $no_rek1=$param->Value;
        if ($no_rek1 != '') {
            try {
                if ($this->hiddenid->Value != $no_rek1){                                        
                    if ($this->DB->checkRecordIsExist ('no_rek1','rek1',$no_rek1)) {
                        throw new Exception("No. Rekening ($no_rek1) sudah tidak tersedia silahkan ganti dengan yang lain.");				
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
            $no_rek1=addslashes($this->txtAddKodeTransaksi->Text);
			$nama_transaksi=strtoupper(addslashes($this->txtAddNamaTransaksi->Text));
			$id_tipe=$this->cmbAddTipe->Text;
			$str = "INSERT INTO rek1 SET no_rek1='$no_rek1',nama_rek1='$nama_transaksi',id_tipe=$id_tipe";
			$this->DB->insertRecord($str);
            $this->Pengguna->insertNewActivity($this->Page->getPagePath(),"Menambah data master Rekening Transaksi dengan id ( $no_rek1) berhasil dilakukan.");
            $this->redirect('dmaster.Transaksi',true);
		}
	}
	public function editRecord ($sender,$param) {
		$this->idProcess='edit';
		$id=$this->getDataKeyField($sender,$this->RepeaterS);
        $this->createObj('DMaster');
		$result = $this->DB->getRecord("SELECT no_rek1,nama_rek1,id_tipe FROM rek1 WHERE no_rek1='$id'");
		$this->hiddenid->Value=$id;		
		$this->txtEditNamaTransaksi->Text=$result[1]['nama_rek1'];
		$this->cmbEditTipe->DataSource=$this->DMaster->removeIdFromArray($this->DMaster->getListTipeBarang(),'none');
		$this->cmbEditTipe->DataBind();
		$this->cmbEditTipe->Text=$result[1]['id_tipe'];
	}
	public function updateData($sender,$param) {
		if ($this->Page->IsValid) {
            $id=$this->hiddenid->Value;
			$nama_transaksi=strtoupper(addslashes($this->txtEditNamaTransaksi->Text));
			$id_tipe=$this->cmbEditTipe->Text;
			$str = "UPDATE rek1 SET nama_rek1='$nama_transaksi',id_tipe=$id_tipe WHERE no_rek1='$id'";
			$this->DB->updateRecord($str);
            $this->Pengguna->insertNewActivity($this->Page->getPagePath(),"Mengubah data master Rekening Transaksi dengan id ($id) berhasil dilakukan.");
            $this->redirect('dmaster.Transaksi',true);
		}
	}
	public function deleteRecord ($sender,$param) {
		$id=$this->getDataKeyField($sender,$this->RepeaterS);
        if ($this->DB->checkRecordIsExist('no_rek1','rek2',$id)) {
            $this->Pengguna->insertNewActivity($this->Page->getPagePath(),"Tidak bisa menghapus data master Rekening ($id) karena sedang digunakan di Kelompok.");
            $this->labelErrorMessage->Text="Tidak bisa menghapus data master Rekening Transaksi dengan ID ($id) karena sedang digunakan di Kelompok";
            $this->DialogErrorMessage->Open();
        }else{
    		$this->DB->deleteRecord("rek1 WHERE no_rek1='$id'");
            $this->Pengguna->insertNewActivity($this->Page->getPagePath(),"Menghapus data master Rekening Transaksi dengan id ($id) berhasil dilakukan.");
    		$this->redirect('dmaster.Transaksi',true);
        }
	}
}