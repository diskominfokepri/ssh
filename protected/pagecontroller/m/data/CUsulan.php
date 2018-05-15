<?php
prado::using ('Application.MainPageM');
class CUsulan extends MainPageM {
	public function onLoad ($param) {		
		parent::onLoad ($param);	
        $this->createObj('DMaster');
        $this->createObj('Kegiatan');
        $this->showDataUsulan=true;
		if (!$this->IsCallback&&!$this->IsPostBack) {    
            if (!isset($_SESSION['currentPageUsulan'])||$_SESSION['currentPageUsulan']['page_name']!='m.data.Usulan') {
                $_SESSION['currentPageUsulan']=array('page_name'=>'m.data.Usulan','page_num'=>0,'search'=>false);												
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
        // $idunit = $this->Pengguna->getDataUser('idunit');
        $tahun=$_SESSION['ta'];
        if ($search) {
            $str = "SELECT us.idusulan,us.rekening,rek5.nama_rek5,rek5.merek,rek5.id_satuan,us.batam,us.bintan,us.tanjungpinang,us.karimun,us.lingga,us.natuna,us.anambas, us.status, un.nama_unit FROM usulan us, unit un, rek5 WHERE rek5.no_rek5=us.rekening AND us.ta=$tahun AND us.idunit=un.idunit ";	
            $str_jumlah="usulan us,proyek_m pm WHERE pm.idproyek=us.idproyek AND pm.tahun_anggaran=$tahun";
            $txtsearch=addslashes($this->txtKriteria->Text);
            switch ($this->cmbKriteria->Text) {
                case 'id' :
                    $str_jumlah = "$str_jumlah AND pm.idproyek='$txtsearch%'";
                    $str = "$str AND pm.idproyek='$txtsearch%'";
                break;
                case 'kode' :
                    $str_jumlah = "$str_jumlah AND pm.kode_proyek LIKE '$txtsearch%'";
                    $str = "$str AND pm.kode_proyek LIKE '$txtsearch%'";
                break;
                case 'nama' :
                    $str_jumlah = "$str_jumlah AND pm.nama_proyek LIKE '%$txtsearch%'";
                    $str = "$str AND pm.nama_proyek LIKE '%$txtsearch%'";
                break;
                case 'idusulan' :
                    $str_jumlah = "$str_jumlah AND us.idusulan='$txtsearch'";
                    $str = "$str AND us.idusulan='$txtsearch'";
                break;
                case 'rekening' :
                    $str_jumlah = "$str_jumlah AND us.rekening LIKE '%$txtsearch%'";
                    $str = "$str AND us.rekening LIKE '%$txtsearch%'";
                break;
                case 'nama_usulan' :
                    $str_jumlah = "$str_jumlah AND us.nama_usulan LIKE '%$txtsearch%'";
                    $str = "$str AND us.nama_usulan LIKE '%$txtsearch%'";
                break;            
            }
            $jumlah_baris=$this->DB->getCountRowsOfTable ($str_jumlah,'us.idusulan');	
        }else{
            $str = "SELECT us.idusulan,us.rekening,rek5.nama_rek5,rek5.merek,rek5.id_satuan,us.batam,us.bintan,us.tanjungpinang,us.karimun,us.lingga,us.natuna,us.anambas, us.status, un.nama_unit FROM usulan us, unit un,v_rekening rek5 WHERE rek5.no_rek5=us.rekening AND us.ta=$tahun AND us.idunit=un.idunit AND rek5.id_tipe=1";    
            $jumlah_baris=$this->DB->getCountRowsOfTable ("usulan us, v_rekening rek5 WHERE us.ta=$tahun AND rek5.no_rek5=us.rekening AND rek5.id_tipe=1 ",'us.idusulan');	
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
        $str = "$str ORDER BY us.status ASC, us.rekening ASC LIMIT $offset,$limit";
		$r=$this->DB->getRecord($str);		
                
        $this->RepeaterS->DataSource=$r;
        $this->RepeaterS->dataBind();
        
        $this->paginationInfo->Text=$this->getInfoPaging($this->RepeaterS);
    }
    public function approveRecord ($sender,$param) {       
        $this->idProcess='edit';
        $id=$this->getDataKeyField($sender,$this->RepeaterS);               

        $ta=$_SESSION['ta'];

        $str = "SELECT u.rekening FROM usulan us LEFT JOIN uraian u  ON us.rekening=u.rekening WHERE us.idusulan=$id AND us.ta=$ta";
        $r = $this->DB->getRecord($str);
        if ($r[1]['rekening'] == '') {
             $str = "INSERT INTO uraian (rekening,batam,bintan,tanjungpinang,karimun,lingga,natuna,anambas,ta,date_added,date_modified) SELECT rekening,batam,bintan,tanjungpinang,karimun,lingga,natuna,anambas,ta,NOW(),NOW() FROM usulan WHERE  idusulan=$id "; 
             $this->DB->insertRecord($str);
        }else {
            $str = "UPDATE uraian u JOIN usulan us ON (u.rekening=us.rekening AND us.ta=u.ta) SET u.batam=us.batam,u.bintan=us.bintan,u.tanjungpinang=us.tanjungpinang,u.karimun=us.karimun,u.lingga=us.lingga,u.natuna=us.natuna,u.anambas=us.anambas,u.date_modified=NOW() WHERE us.idusulan=$id ";
            $this->DB->updateRecord($str);
        }   
        

         $str = "UPDATE usulan SET status=1 WHERE idusulan=$id";
         $str1 = "UPDATE usulan SET status=2 WHERE ta=$ta and rekening='".$r[1]['rekening']."' and idusulan<>$id";        
         $this->DB->updateRecord($str);
         $this->DB->updateRecord($str1);
         $this->redirect('data.Usulan', true);
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