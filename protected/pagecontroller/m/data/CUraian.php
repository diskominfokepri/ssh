<?php
prado::using ('Application.MainPageM');
class CUraian extends MainPageM {
	public function onLoad ($param) {		
		parent::onLoad ($param);	
        $this->createObj('DMaster');                
        $this->showDataUraian=true;
		if (!$this->IsCallback&&!$this->IsPostBack) {    
            if (!isset($_SESSION['currentPageUraian'])||$_SESSION['currentPageUraian']['page_name']!='m.data.Uraian') {
                $_SESSION['currentPageUraian']=array('page_name'=>'m.data.Uraian','page_num'=>0,'search'=>false);												
            } 
            $_SESSION['currentPageUraian']['search']=false;
            
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
        $this->populateData ($_SESSION['currentPageUraian']['search']);
	}
    public function renderCallback ($sender,$param) {
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {
		$_SESSION['currentPageUraian']['page_num']=$param->NewPageIndex;
		$this->populateData($_SESSION['currentPageUraian']['search']);
	} 
    public function searchRecord ($sender,$param) {        
        $_SESSION['currentPageUraian']['search']=true;
        $this->populateData($_SESSION['currentPageUraian']['search']);
    }
    protected function populateData($search=false) {	
        $tahun=$_SESSION['ta'];
        if ($search) {
            $str = "SELECT um.iduraian,um.rekening,rek5.nama_rek5,rek5.merek,rek5.id_satuan,um.batam,um.bintan,um.tanjungpinang,um.karimun,um.lingga,um.natuna,um.anambas FROM uraian um,v_rekening rek5 WHERE rek5.no_rek5=um.rekening AND um.ta=$tahun AND rek5.id_tipe=1";    
            $str_jumlah=" uraian um,v_rekening rek5 WHERE rek5.no_rek5=um.rekening AND um.ta=$tahun AND rek5.id_tipe=1";
            $txtsearch=addslashes($this->txtKriteria->Text);
            switch ($this->cmbKriteria->Text) {
                case 'rekening' :
                    $str_jumlah = "$str_jumlah AND um.rekening LIKE '%$txtsearch%'";
                    $str = "$str AND um.rekening LIKE '%$txtsearch%'";
                break;
                case 'nama_objek' :
                    $str_jumlah = "$str_jumlah AND rek5.nama_rek5 LIKE '%$txtsearch%'";
                    $str = "$str AND rek5.nama_rek5 LIKE '%$txtsearch%'";
                break;            
            }
            $jumlah_baris=$this->DB->getCountRowsOfTable ($str_jumlah,'um.iduraian');	
        }else{
            $str = "SELECT um.iduraian,um.rekening,rek5.nama_rek5,rek5.merek,rek5.id_satuan,um.batam,um.bintan,um.tanjungpinang,um.karimun,um.lingga,um.natuna,um.anambas FROM uraian um,v_rekening rek5 WHERE rek5.no_rek5=um.rekening AND um.ta=$tahun AND rek5.id_tipe=1";    
            $jumlah_baris=$this->DB->getCountRowsOfTable (" uraian um,v_rekening rek5 WHERE rek5.no_rek5=um.rekening AND um.ta=$tahun AND rek5.id_tipe=1",'um.iduraian');	
        }
        $this->RepeaterS->CurrentPageIndex=$_SESSION['currentPageUraian']['page_num'];
		$this->RepeaterS->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterS->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterS->PageSize;		
		$itemcount=$this->RepeaterS->VirtualItemCount;
		$limit=$this->RepeaterS->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=10;$_SESSION['currentPageUraian']['page_num']=0;}
        $str = "$str ORDER BY um.rekening ASC LIMIT $offset,$limit";
		$r=$this->DB->getRecord($str);		
                
        $this->RepeaterS->DataSource=$r;
        $this->RepeaterS->dataBind();
        
        $this->paginationInfo->Text=$this->getInfoPaging($this->RepeaterS);
    }
    public function editRecord ($sender,$param) {       
        $this->idProcess='edit';
        $id=$this->getDataKeyField($sender,$this->RepeaterS);        
        $this->hiddenid->Value=$id;     

        $str = "SELECT rekening,nama_rek5,merek,id_satuan,batam,bintan,tanjungpinang,karimun,lingga,natuna,anambas FROM uraian u,rek5 WHERE rek5.no_rek5=u.rekening AND iduraian=$id";
        $r=$this->DB->getRecordOneOnly($str);
        
        $this->lblEditKodeNamaUraian->Text=$r['rekening'].' / '.$r['nama_rek5'];
        $this->lblEditNamaMerek->Text=$r['merek'];                          
        $this->lblEditSatuan->Text=$this->DMaster->getNamaSatuan($r['id_satuan']);                       

        $this->txtEditHargaBatam->Text=$r['batam'];
        $this->txtEditHargaBintan->Text=$r['bintan'];
        $this->txtEditHargaTanjungpinang->Text=$r['tanjungpinang'];
        $this->txtEditHargaKarimun->Text=$r['karimun'];
        $this->txtEditHargaLingga->Text=$r['lingga'];
        $this->txtEditHargaNatuna->Text=$r['natuna'];
        $this->txtEditHargaAnambas->Text=$r['anambas'];   
    }  
    public function updateData ($sender,$param) {
        if ($this->IsValid) {

            $id=$this->hiddenid->Value;             
            $txtHargaBatam=str_replace(',','',$this->txtEditHargaBatam->Text);
            $txtHargaBintan=str_replace(',','',$this->txtEditHargaBintan->Text);
            $txtHargaTanjungpinang=str_replace(',','',$this->txtEditHargaTanjungpinang->Text);
            $txtHargaKarimun=str_replace(',','',$this->txtEditHargaKarimun->Text);
            $txtHargaLingga=str_replace(',','',$this->txtEditHargaLingga->Text);
            $txtHargaNatuna=str_replace(',','',$this->txtEditHargaNatuna->Text);
            $txtHargaAnambas=str_replace(',','',$this->txtEditHargaAnambas->Text);
            
            $str = "UPDATE uraian SET batam='$txtHargaBatam',bintan='$txtHargaBintan',tanjungpinang='$txtHargaTanjungpinang',karimun='$txtHargaKarimun',lingga='$txtHargaLingga',natuna='$txtHargaNatuna',anambas='$txtHargaAnambas',date_modified=NOW() WHERE iduraian=$id";
            $this->DB->insertRecord($str);
            
            $this->Pengguna->insertNewActivity($this->Page->getPagePath(),"Mengubah uraian dengan id  ($id) telah berhasil dilakukan");
            
            $this->redirect('data.Uraian', true);
        }
    } 
    public function deleteRecord ($sender,$param) {
        $id=$this->getDataKeyField($sender,$this->RepeaterS);
        
        $this->DB->deleteRecord("uraian WHERE iduraian='$id'");    
        $this->Pengguna->insertNewActivity($this->Page->getPagePath(),"Menghapus data Uraian dengan id ($id) berhasil dilakukan.");
        $this->redirect('data.Uraian',true);    
        
    }
}