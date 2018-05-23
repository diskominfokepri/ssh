<?php
prado::using ('Application.MainPageD');
class CUsulan extends MainPageD {
    public function onLoad ($param) {       
        parent::onLoad ($param);    
        $this->createObj('DMaster');
        $this->createObj('Kegiatan');
        $this->showDataUsulan=true;
        if (!$this->IsCallback&&!$this->IsPostBack) {    
            if (!isset($_SESSION['currentPageUsulan'])||$_SESSION['currentPageUsulan']['page_name']!='d.data.Usulan') {
                $_SESSION['currentPageUsulan']=array('page_name'=>'d.data.Usulan','page_num'=>0,'search'=>false);                                               
            } 
            $_SESSION['currentPageUsulan']['search']=false;
            
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
        $this->populateData ($_SESSION['currentPageUsulan']['search']);
    }
    public function renderCallback ($sender,$param) {
        $this->RepeaterS->render($param->NewWriter);    
    }   
    public function Page_Changed ($sender,$param) {
        $_SESSION['currentPageUsulan']['page_num']=$param->NewPageIndex;
        $this->populateData($_SESSION['currentPageUsulan']['search']);
    } 
    public function searchRecord ($sender,$param) {        
        $_SESSION['currentPageUsulan']['search']=true;
        $this->populateData($_SESSION['currentPageUsulan']['search']);
    }
    protected function populateData($search=false) {    
        $idunit = $this->Pengguna->getDataUser('idunit');
        $tahun=$_SESSION['ta'];
        if ($search) {
            $str = "SELECT us.idusulan,us.rekening,rek5.nama_rek5,rek5.merek,rek5.id_satuan,us.batam,us.bintan,us.tanjungpinang,us.karimun,us.lingga,us.natuna,us.anambas FROM usulan us,rek5 WHERE rek5.no_rek5=us.rekening AND us.ta=$tahun AND idunit='$idunit'";    
            $str_jumlah="usulan us,rek5 WHERE rek5.no_rek5=us.rekening AND idunit='$idunit' AND status=0";
            $txtsearch=addslashes($this->txtKriteria->Text);
            switch ($this->cmbKriteria->Text) {
                case 'rekening' :
                    $str_jumlah = "$str_jumlah AND us.rekening LIKE '%$txtsearch%'";
                    $str = "$str AND us.rekening LIKE '$txtsearch%'";
                break;
                case 'nama_usulan' :
                    $str = "$str  AND rek5.nama_rek5 LIKE '$txtsearch%'";
                    $str_jumlah = "$str_jumlah AND rek5.nama_rek5 LIKE '%$txtsearch%'";
                break;               
            }
            $jumlah_baris=$this->DB->getCountRowsOfTable ($str_jumlah,'us.idusulan');   
        }else{
            $str = "SELECT us.idusulan,us.rekening,rek5.nama_rek5,rek5.merek,rek5.id_satuan,us.batam,us.bintan,us.tanjungpinang,us.karimun,us.lingga,us.natuna,us.anambas FROM usulan us,rek5 WHERE rek5.no_rek5=us.rekening AND us.ta=$tahun AND idunit='$idunit' AND status=0";    
            $jumlah_baris=$this->DB->getCountRowsOfTable ("usulan us WHERE us.ta=$tahun AND idunit='$idunit' AND status=0",'us.idusulan');    
        }
        $this->RepeaterS->CurrentPageIndex=$_SESSION['currentPageUsulan']['page_num'];
        $this->RepeaterS->VirtualItemCount=$jumlah_baris;
        $currentPage=$this->RepeaterS->CurrentPageIndex;
        $offset=$currentPage*$this->RepeaterS->PageSize;        
        $itemcount=$this->RepeaterS->VirtualItemCount;
        $limit=$this->RepeaterS->PageSize;
        if (($offset+$limit)>$itemcount) {
            $limit=$itemcount-$offset;
        }
        if ($limit < 0) {$offset=0;$limit=10;$_SESSION['currentPageUsulan']['page_num']=0;}
        $str = "$str ORDER BY us.rekening ASC LIMIT $offset,$limit";
        $r=$this->DB->getRecord($str);      
                
        $this->RepeaterS->DataSource=$r;
        $this->RepeaterS->dataBind();
        
        $this->paginationInfo->Text=$this->getInfoPaging($this->RepeaterS);
    }
    public function editRecord ($sender,$param) {       
        $this->idProcess='edit';
        $id=$this->getDataKeyField($sender,$this->RepeaterS);        
        $this->hiddenid->Value=$id;     

        $str = "SELECT rekening,nama_rek5,merek,id_satuan,batam,bintan,tanjungpinang,karimun,lingga,natuna,anambas FROM usulan u,rek5 WHERE rek5.no_rek5=u.rekening AND idusulan=$id";
        $r=$this->DB->getRecordOneOnly($str);
        
        $this->lblEditKodeNamausulan->Text=$r['rekening'].' / '.$r['nama_rek5'];
        $this->lblEditNamaMerek->Text=$r['merek'];    

        $this->cmbEditSatuan->DataSource=$this->DMaster->getListSatuan();
        $this->cmbEditSatuan->Text=$r['id_satuan'];
        $this->cmbEditSatuan->dataBind();
        
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
            
            $str = "UPDATE usulan SET batam='$txtHargaBatam',bintan='$txtHargaBintan',tanjungpinang='$txtHargaTanjungpinang',karimun='$txtHargaKarimun',lingga='$txtHargaLingga',natuna='$txtHargaNatuna',anambas='$txtHargaAnambas',date_modified=NOW() WHERE idusulan=$id";
            $this->DB->insertRecord($str);
            
            $this->Pengguna->insertNewActivity($this->Page->getPagePath(),"Mengubah usulan dengan id  ($id) telah berhasil dilakukan");
            
            $this->redirect('data.Usulan', true);
        }
    } 
    public function deleteRecord ($sender,$param) {
        $id=$this->getDataKeyField($sender,$this->RepeaterS);
        
        $this->DB->deleteRecord("usulan WHERE idusulan='$id'");    
        $this->Pengguna->insertNewActivity($this->Page->getPagePath(),"Menghapus data usulan dengan id ($id) berhasil dilakukan.");
        $this->redirect('data.Usulan',true);    
        
    }
}