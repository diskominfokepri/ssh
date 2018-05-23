<?php
prado::using ('Application.MainPageM');
class CUraianTransportasi extends MainPageM {
	public function onLoad ($param) {		
		parent::onLoad ($param);	
        $this->createObj('DMaster');
        $this->showDataUraianTransportasi=true;
		if (!$this->IsCallback&&!$this->IsPostBack) {    
            if (!isset($_SESSION['currentPageUraianTransportasi'])||$_SESSION['currentPageUraianTransportasi']['page_name']!='m.data.UraianTransportasi') {
                $_SESSION['currentPageUraianTransportasi']=array('page_name'=>'m.data.UraianTransportasi','page_num'=>0,'search'=>false);												
            } 
            $_SESSION['currentPageUraianTransportasi']['search']=false;
            
            $ta=$_SESSION['ta'];
            $daftar_ta=$this->DMaster->getListTahunAnggaran();
            $this->tbCmbTA->DataSource=$this->DMaster->removeIdFromArray($daftar_ta,'none');
            $this->tbCmbTA->Text=$ta;
            $this->tbCmbTA->dataBind();
            
            $this->populateData();
            $this->setLabelModuleHeader();
		}
		
	}
    private function setLabelModuleHeader () {
        $ta=$_SESSION['ta'];
        $this->lblmoduleheader->Text="T.A $ta";
    }
    public function changeTbTA($sender,$param) {	
        $_SESSION['ta']=$this->tbCmbTA->Text;
        $this->setLabelModuleHeader();
        $this->populateData ($_SESSION['currentPageUraianTransportasi']['search']);
	}
    public function renderCallback ($sender,$param) {
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {
		$_SESSION['currentPageUraianTransportasi']['page_num']=$param->NewPageIndex;
		$this->populateData($_SESSION['currentPageUraianTransportasi']['search']);
	} 
    public function searchRecord ($sender,$param) {        
        $_SESSION['currentPageUraianTransportasi']['search']=true;
        $this->populateData($_SESSION['currentPageUraianTransportasi']['search']);
    }
    protected function populateData($search=false) {	
        $tahun=$_SESSION['ta'];
        if ($search) {
            $str = "SELECT ut.iduraian,ut.rekening,rek5.nama_rek5,rek5.merek,rek5.id_satuan,ut.pesawat_kelas,ut.pesawat_ekonomi,ut.kapal_kelas,ut.kapal_ekonomi FROM uraian ut,v_rekening rek5 WHERE rek5.no_rek5=ut.rekening AND ut.ta=$tahun AND rek5.id_tipe=2";    
            $str_jumlah=" uraian ut,v_rekening rek5 WHERE rek5.no_rek5=ut.rekening AND ut.ta=$tahun AND rek5.id_tipe=2";
            $txtsearch=addslashes($this->txtKriteria->Text);
            switch ($this->cmbKriteria->Text) {
                case 'rekening' :
                    $str_jumlah = "$str_jumlah AND ut.rekening LIKE '%$txtsearch%'";
                    $str = "$str AND ut.rekening LIKE '%$txtsearch%'";
                break;
                case 'nama_objek' :
                    $str_jumlah = "$str_jumlah AND rek5.nama_rek5 LIKE '%$txtsearch%'";
                    $str = "$str AND rek5.nama_rek5 LIKE '%$txtsearch%'";
                break;            
            }
            $jumlah_baris=$this->DB->getCountRowsOfTable ($str_jumlah,'ut.iduraian');	
        }else{
             $str = "SELECT ut.iduraian,ut.rekening,rek5.nama_rek5,rek5.merek,rek5.id_satuan,ut.pesawat_kelas,ut.pesawat_ekonomi,ut.kapal_kelas,ut.kapal_ekonomi FROM uraian ut,v_rekening rek5 WHERE rek5.no_rek5=ut.rekening AND ut.ta=$tahun AND rek5.id_tipe=2";    
            $jumlah_baris=$this->DB->getCountRowsOfTable (" uraian ut,v_rekening rek5 WHERE rek5.no_rek5=ut.rekening AND ut.ta=$tahun AND rek5.id_tipe=2",'ut.iduraian');   	
        }
        $this->RepeaterS->CurrentPageIndex=$_SESSION['currentPageUraianTransportasi']['page_num'];
		$this->RepeaterS->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterS->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterS->PageSize;		
		$itemcount=$this->RepeaterS->VirtualItemCount;
		$limit=$this->RepeaterS->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=10;$_SESSION['currentPageUraianTransportasi']['page_num']=0;}
        $str = "$str ORDER BY ut.rekening ASC LIMIT $offset,$limit";
		$r=$this->DB->getRecord($str,$offset+1);		
                
        $this->RepeaterS->DataSource=$r;
        $this->RepeaterS->dataBind();
        
        $this->paginationInfo->Text=$this->getInfoPaging($this->RepeaterS);
    }
    public function editRecord ($sender,$param) {       
        $this->idProcess='edit';
        $id=$this->getDataKeyField($sender,$this->RepeaterS);        
        $this->hiddenid->Value=$id;     

        $str = "SELECT rekening,nama_rek5,merek,id_satuan,pesawat_kelas,pesawat_ekonomi,kapal_kelas,kapal_ekonomi FROM uraian ut,rek5 WHERE rek5.no_rek5=ut.rekening AND iduraian=$id";
        $r=$this->DB->getRecordOneOnly($str);
        
        $this->lblEditKodeNamaUraian->Text=$r['rekening'].' / '.$r['nama_rek5'];
        $this->lblEditNamaMerek->Text=$r['merek'];                          
        $this->lblEditSatuan->Text=$this->DMaster->getNamaSatuan($r['id_satuan']);                       

        $this->txtEditHargaPesawatKelas->Text=$r['pesawat_kelas'];
        $this->txtEditHargaPesawatEkonomi->Text=$r['pesawat_ekonomi'];
        $this->txtEditHargaKapalKelas->Text=$r['kapal_kelas'];
        $this->txtEditHargaKapalEkonomi->Text=$r['kapal_ekonomi'];
    }  
    public function updateData ($sender,$param) {
        if ($this->IsValid) {

            $id=$this->hiddenid->Value;             
            $txtHargaPesawatKelas=str_replace(',','',$this->txtEditHargaPesawatKelas->Text);
            $txtHargaPesawatEkonomi=str_replace(',','',$this->txtEditHargaPesawatEkonomi->Text);
            $txtHargaKapalKelas=str_replace(',','',$this->txtEditHargaKapalKelas->Text);
            $txtHargaKapalEkonomi=str_replace(',','',$this->txtEditHargaKapalEkonomi->Text);
            
            $str = "UPDATE uraian SET pesawat_kelas='$txtHargaPesawatKelas',pesawat_ekonomi='$txtHargaPesawatEkonomi',kapal_kelas='$txtHargaKapalKelas',kapal_ekonomi='$txtHargaKapalEkonomi',date_modified=NOW() WHERE iduraian=$id";
            $this->DB->insertRecord($str);
            
            $this->Pengguna->insertNewActivity($this->Page->getPagePath(),"Mengubah uraian transportasi dengan id  ($id) telah berhasil dilakukan");
            
            $this->redirect('data.UraianTransportasi', true);
        }
    } 
    public function deleteRecord ($sender,$param) {
        $id=$this->getDataKeyField($sender,$this->RepeaterS);
        
        $this->DB->deleteRecord("uraian WHERE iduraian='$id'");    
        $this->Pengguna->insertNewActivity($this->Page->getPagePath(),"Menghapus data uraian transportasi dengan id ($id) berhasil dilakukan.");
        $this->redirect('data.UraianTransportasi',true);    
        
    }
}