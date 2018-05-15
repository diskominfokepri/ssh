<?php
prado::using ('Application.MainPageD');
class CUsulanBarang extends MainPageD {    
    public function onLoad ($param) {
        parent::onLoad ($param);        
        $this->createObj('DMaster'); 
        $this->showUsulanBarang=true;        
        if (!$this->IsPostBack&&!$this->IsCallBack) {
            if (!isset($_SESSION['currentPageUsulanBarang'])||$_SESSION['currentPageUsulanBarang']['page_name']!='d.dmaster.UsulanBarang') {
                $_SESSION['currentPageUsulanBarang']=array('page_name'=>'d.dmaster.UsulanBarang','page_num'=>0,'search'=>false,'no_rek4'=>'none');                                                
            }
            $_SESSION['currentPageUsulanBarang']['search']=false; 
            
            $daftar_jenis=$this->DMaster->getList("v_rekening_4 WHERE id_tipe=1",array('no_rek4','nama_rek4'),'no_rek4',null,7);
            $daftar_jenis['none']='DAFTAR REKENING';
            $this->cmbRincian->DataSource=$daftar_jenis;
            $this->cmbRincian->Text=$_SESSION['currentPageUsulanBarang']['no_rek4'];
            $this->cmbRincian->dataBind();
            $this->labelfiltered->Text=$_SESSION['currentPageUsulanBarang']['no_rek4']=='none'?'[none]':'[selected]';
            $this->populateData ();     
        }
    }
    public function renderCallback ($sender,$param) {
        $this->RepeaterS->render($param->NewWriter);    
    }   
    public function Page_Changed ($sender,$param) {
        $_SESSION['currentPageUsulanBarang']['page_num']=$param->NewPageIndex;
        $this->populateData();
    } 
    public function searchRecord ($sender,$param) {
        $_SESSION['currentPageUsulanBarang']['search']=true;
        $this->populateData($_SESSION['currentPageUsulanBarang']['search']);
    }
    public function filterRincian ($sender,$param) {
        $no_rek4=$this->cmbRincian->Text;
        $_SESSION['currentPageUsulanBarang']['no_rek4']=$no_rek4; 
        $this->labelfiltered->Text=$no_rek4=='none'?'[none]':'[selected]';
        $this->populateData();
    }
    protected function populateData ($search=false) {
        $no_rek4=$_SESSION['currentPageUsulanBarang']['no_rek4'];
        $str_rek=$no_rek4=='none'?'':" AND no_rek4='$no_rek4'";
        if ($search) {
            $str = "SELECT rek5.no_rek5,rek5.nama_rek5,rek5.merek,s.nama_satuan FROM usulan_barang, v_rekening rek5 LEFT JOIN satuan s ON (s.id_satuan=rek5.id_satuan) WHERE rek5.id_tipe=1";
            $txtsearch=addslashes($this->txtKriteria->Text);
            switch ($this->cmbKriteria->Text) {                
                case 'kode' :
                    $clausa=" AND no_rek5 LIKE '%$txtsearch%'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("v_rekening WHERE id_tipe=1$clausa",'no_rek5');
                    $str = "$str $clausa";
                break;
                case 'nama' :
                    $clausa=" AND nama_rek5 LIKE '%$txtsearch%'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("v_rekening WHERE id_tipe=1$clausa",'no_rek5');
                    $str = "$str $clausa";
                break;
            }
        }else{
           $str = "SELECT rek5.no_rek5,rek5.nama_rek5,rek5.merek,s.nama_satuan FROM usulan_barang, v_rekening rek5 LEFT JOIN satuan s ON (s.id_satuan=rek5.id_satuan) WHERE rek5.id_tipe=1$str_rek";
            $jumlah_baris=$this->DB->getCountRowsOfTable ("v_rekening WHERE id_tipe=1$str_rek",'no_rek5');
        }
        $this->RepeaterS->CurrentPageIndex=$_SESSION['currentPageUsulanBarang']['page_num'];               
        $this->RepeaterS->VirtualItemCount=$jumlah_baris;
        $currentPage=$this->RepeaterS->CurrentPageIndex;
        $offset=$currentPage*$this->RepeaterS->PageSize;        
        $itemcount=$this->RepeaterS->VirtualItemCount;
        $limit=$this->RepeaterS->PageSize;
        if (($offset+$limit)>$itemcount) {
            $limit=$itemcount-$offset;
        }
        if ($limit < 0) {$offset=0;$limit=10;$_SESSION['currentPageUsulanBarang']['page_num']=0;}
        $str = "$str ORDER BY no_rek5 ASC LIMIT $offset,$limit";
        $r=$this->DB->getRecord($str,$offset+1);        
        $this->RepeaterS->DataSource=$r;
        $this->RepeaterS->dataBind();
    }
    public function addProcess ($sender,$param) {
        $this->idProcess='add';             
        $this->cmbAddRincian->DataSource=$this->DMaster->getList("v_rekening_4 WHERE id_tipe=1",array('no_rek4','nama_rek4'),'no_rek4',null,7);
        $this->cmbAddRincian->dataBind();   

        $this->cmbAddSatuan->DataSource=$this->DMaster->getListSatuan();
        $this->cmbAddSatuan->dataBind();
    } 
    public function cmbRincianChanged ($sender,$param) {
        $this->idProcess='add';     
        $transaksi=$this->cmbAddRincian->Text;
        if ($transaksi=='none')
            $this->lblAddKodeRincian->Text='';
        else
            $this->lblAddKodeRincian->Text="$transaksi.";
    }
    public function checkKodeObjek ($sender,$param) {
        $this->idProcess=$sender->getId()==='checkAddKodeObjek'?'add':'edit';
        $no_rek5=$param->Value;
        if ($no_rek5 != '') {
            try {
                $kode_transaksi=$sender->getId()==='checkAddKodeObjek'?$this->lblAddKodeRincian->Text:$this->lblEditKodeRincian->Text;
                $no_rek5 = $kode_transaksi.$no_rek5;
                if ($this->hiddennorek5->Value != $no_rek5){                                        
                    if ($this->DB->checkRecordIsExist ('no_rek5','rek5',$no_rek5)) {
                        throw new Exception("No. Rekening ($no_rek5) sudah tidak tersedia silahkan ganti dengan yang lain.");               
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
            $nama_objek=strtoupper(addslashes($this->txtAddNamaObjek->Text));
            $kode_rincian=$this->cmbAddRincian->Text;
            $kode_objek=$kode_rincian.'.'.$this->txtAddKodeObjek->Text;
            $merek=addslashes($this->txtAddNamaMerek->Text);
            $id_satuan=addslashes($this->cmbAddSatuan->Text);
            $str = "INSERT INTO rek5 SET no_rek5='$kode_objek',no_rek4='$kode_rincian',nama_rek5='$nama_objek',merek='$merek',id_satuan='$id_satuan'";          
            $this->DB->insertRecord($str);
            $this->Pengguna->insertNewActivity($this->Page->getPagePath(),"Menambah data master Objek dengan id ($kode_objek) berhasil dilakukan.");
            $this->redirect('dmaster.Objek',true);
        }
    }
    public function editRecord ($sender,$param) {       
        $this->idProcess='edit';
        $id=$this->getDataKeyField($sender,$this->RepeaterS);        
        $result = $this->DMaster->getList("rek5 WHERE no_rek5='$id'",array('no_rek5','no_rek4','nama_rek5','merek','id_satuan'));       
        $this->hiddennorek5->Value=$id;
        $this->lblEditKodeRincian->Text=$result[1]['no_rek4'].'.';
        $this->txtEditKodeObjek->Text=$this->DMaster->getKodeRekeningTerakhir($result[1]['no_rek5']);
        $this->txtEditNamaObjek->Text=$result[1]['nama_rek5'];
        $this->txtEditNamaMerek->Text=$result[1]['merek'];
        $this->cmbEditSatuan->DataSource=$this->DMaster->getListSatuan();
        $this->cmbEditSatuan->Text=$r[1]['id_satuan'];
        $this->cmbEditSatuan->dataBind();
    }   
    
    public function updateData($sender,$param) {
        if ($this->Page->IsValid) {
            $id=$this->hiddennorek5->Value;
            $no_rek4=$this->lblEditKodeRincian->Text;
            $no_rek5=$no_rek4.$this->txtEditKodeObjek->Text;
            $nama_objek=strtoupper(addslashes($this->txtEditNamaObjek->Text));
            $merek=addslashes($this->txtEditNamaMerek->Text);
            $id_satuan=addslashes($this->cmbEditSatuan->Text);
            $str = "UPDATE rek5 SET no_rek5='$no_rek5',nama_rek5='$nama_objek',merek='$merek',id_satuan='$id_satuan' WHERE no_rek5='$id'";
            $this->DB->updateRecord($str);
            $this->Pengguna->insertNewActivity($this->Page->getPagePath(),"Mengubah data master Rekening Objek dengan id ($id) berhasil dilakukan.");
            $this->redirect('dmaster.Objek',true);              
        }
    }
    public function deleteRecord ($sender,$param) {
        $id=$this->getDataKeyField($sender,$this->RepeaterS);
        if ($this->DB->checkRecordIsExist('rekening','uraian',$id)) {
            $this->Pengguna->insertNewActivity($this->Page->getPagePath(),"Tidak bisa menghapus data master Rekening ($id) karena sedang digunakan di Uraian.");
            $this->labelErrorMessage->Text="Tidak bisa menghapus data master Rekening Objek dengan ID ($id) karena sedang digunakan di Uraian Murni";
            $this->DialogErrorMessage->Open();
        }else{
            $this->DB->deleteRecord("rek5 WHERE no_rek5='$id'");    
            $this->Pengguna->insertNewActivity($this->Page->getPagePath(),"Menghapus data master Rekening Objek dengan id ($id) berhasil dilakukan.");
            $this->redirect('dmaster.Objek',true);
        }
        
    }
}