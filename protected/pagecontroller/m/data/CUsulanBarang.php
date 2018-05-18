<?php
prado::using ('Application.MainPageM');
class CUsulanBarang extends MainPageM {
	public function onLoad ($param) {		
		parent::onLoad ($param);	
        $this->createObj('DMaster');
        $this->createObj('Kegiatan');
        $this->showDataUsulanBarang=true;
		if (!$this->IsCallback&&!$this->IsPostBack) {    
            if (!isset($_SESSION['currentPageUsulanBarang'])||$_SESSION['currentPageUsulanBarang']['page_name']!='m.data.UsulanBarang') {
                $_SESSION['currentPageUsulanBarang']=array('page_name'=>'m.data.UsulanBarang','page_num'=>0,'search'=>false);												
            } 
            $_SESSION['currentPageUsulanBarang']['search']=false;
            
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
        $this->populateData ($_SESSION['currentPageUsulanBarang']['search']);
	}
    public function renderCallback ($sender,$param) {
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {
		$_SESSION['currentPageUsulanBarang']['page_num']=$param->NewPageIndex;
		$this->populateData($_SESSION['currentPageUsulanBarang']['search']);
	} 
    public function searchRecord ($sender,$param) {        
        $_SESSION['currentPageUsulanBarang']['search']=true;
        $this->populateData($_SESSION['currentPageUsulanBarang']['search']);
    }
    protected function populateData($search=false) {	        
        // $idunit = $this->Pengguna->getDataUser('idunit');
        $tahun=$_SESSION['ta'];
        if ($search) {
            	

            $str = "SELECT ub.idusulan,ub.rekening,ub.nama_rek5,ub.merek,ub.id_satuan, ub.status, un.nama_unit,s.nama_satuan,ub.batam,ub.bintan,ub.tanjungpinang,ub.karimun,ub.lingga,ub.natuna,ub.anambas FROM usulan_barang ub left join satuan s on s.id_satuan=ub.id_satuan, unit un WHERE ub.ta='$tahun' AND ub.idunit=un.idunit ";

            $str_jumlah="usulan_barang ub left join satuan s on s.id_satuan=ub.id_satuan, unit un WHERE ub.ta='$tahun' AND ub.idunit=un.idunit";
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
                    $str_jumlah = "$str_jumlah AND ub.idusulan='$txtsearch'";
                    $str = "$str AND ub.idusulan='$txtsearch'";
                break;
                case 'rekening' :
                    $str_jumlah = "$str_jumlah AND ub.rekening LIKE '%$txtsearch%'";
                    $str = "$str AND ub.rekening LIKE '%$txtsearch%'";
                break;
                case 'nama_usulan' :
                    $str_jumlah = "$str_jumlah AND ub.nama_usulan LIKE '%$txtsearch%'";
                    $str = "$str AND ub.nama_usulan LIKE '%$txtsearch%'";
                break;            
            }
            $jumlah_baris=$this->DB->getCountRowsOfTable ($str_jumlah,'ub.idusulan');	
        }else{
            $str = "SELECT ub.idusulan,ub.rekening,ub.nama_rek5,ub.merek,ub.id_satuan, ub.status, un.nama_unit,s.nama_satuan,ub.batam,ub.bintan,ub.tanjungpinang,ub.karimun,ub.lingga,ub.natuna,ub.anambas FROM usulan_barang ub left join satuan s on s.id_satuan=ub.id_satuan, unit un WHERE ub.ta='$tahun' AND ub.idunit=un.idunit ";        
            $jumlah_baris=$this->DB->getCountRowsOfTable ("usulan_barang ub left join satuan s on s.id_satuan=ub.id_satuan, unit un WHERE ub.ta='$tahun' AND ub.idunit=un.idunit",'ub.idusulan');	
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
        $str = "$str ORDER BY ub.status ASC, ub.rekening ASC LIMIT $offset,$limit";
		$r=$this->DB->getRecord($str);		
                
        $this->RepeaterS->DataSource=$r;
        $this->RepeaterS->dataBind();
        
        $this->paginationInfo->Text=$this->getInfoPaging($this->RepeaterS);
    }
    public function approveRecord ($sender,$param) {       
        $this->idProcess='edit';
        $id=$this->getDataKeyField($sender,$this->RepeaterS);               

        $ta=$_SESSION['ta'];

        $str = "SELECT u.rekening FROM usulan_barang ub LEFT JOIN uraian u  ON ub.rekening=u.rekening WHERE ub.idusulan=$id AND ub.ta=$ta";
        $r = $this->DB->getRecord($str);
        if ($r[1]['rekening'] == '') {
             $str = "INSERT INTO uraian (rekening,batam,bintan,tanjungpinang,karimun,lingga,natuna,anambas,ta,date_added,date_modified) SELECT rekening,batam,bintan,tanjungpinang,karimun,lingga,natuna,anambas,ta,NOW(),NOW() FROM usulan_barang WHERE  idusulan=$id "; 
             $this->DB->insertRecord($str);
              $str2 = "INSERT INTO rek5 (no_rek5,no_rek4,nama_rek5,merek,id_satuan) SELECT rekening,no_rek4,nama_rek5,merek,id_satuan FROM usulan_barang WHERE  idusulan=$id "; 
             $this->DB->insertRecord($str2);
        }else {
            $str = "UPDATE uraian u JOIN usulan_barang ub ON (u.rekening=ub.rekening AND ub.ta=u.ta) SET u.batam=ub.batam,u.bintan=ub.bintan,u.tanjungpinang=ub.tanjungpinang,u.karimun=ub.karimun,u.lingga=ub.lingga,u.natuna=ub.natuna,u.anambas=ub.anambas,u.date_modified=NOW() WHERE ub.idusulan=$id ";
            $this->DB->updateRecord($str);
            /*$str2 = "UPDATE rek5 r JOIN usulan_barang ub ON (r.no_rek5=ub.rekening) SET u.batam=ub.batam,u.bintan=ub.bintan,u.tanjungpinang=ub.tanjungpinang,u.karimun=ub.karimun,u.lingga=ub.lingga,u.natuna=ub.natuna,u.anambas=ub.anambas,u.date_modified=NOW() WHERE ub.idusulan=$id ";
            $this->DB->updateRecord($str2);*/
        }   

         $str = "UPDATE usulan_barang SET status=1 WHERE idusulan=$id";
         $str1 = "UPDATE usulan_barang SET status=2 WHERE ta=$ta and rekening='".$r[1]['rekening']."' and idusulan<>$id";        
         $this->DB->updateRecord($str);
         $this->DB->updateRecord($str1);
         $this->redirect('data.UsulanBarang', true);
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
            
            $str = "UPDATE usulan_barang SET batam='$txtHargaBatam',bintan='$txtHargaBintan',tanjungpinang='$txtHargaTanjungpinang',karimun='$txtHargaKarimun',lingga='$txtHargaLingga',natuna='$txtHargaNatuna',anambas='$txtHargaAnambas',date_modified=NOW() WHERE idusulan=$id";
            $this->DB->insertRecord($str);
            
            $this->Pengguna->insertNewActivity($this->Page->getPagePath(),"Mengubah usulan nama barang dengan id  ($id) telah berhasil dilakukan");
            
            $this->redirect('data.UsulanBarang', true);
        }
    } 
    public function deleteRecord ($sender,$param) {
        $id=$this->getDataKeyField($sender,$this->RepeaterS);
        
        $this->DB->deleteRecord("usulan_barang WHERE idusulan='$id'");    
        $this->Pengguna->insertNewActivity($this->Page->getPagePath(),"Menghapus data usulan barang dengan id ($id) berhasil dilakukan.");
        $this->redirect('data.UsulanBarang',true);    
        
    }
}