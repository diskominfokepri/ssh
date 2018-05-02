<?php
prado::using ('Application.MainPageM');
class CTambahUsulan extends MainPageM {       
    public function onLoad ($param) {       
        parent::onLoad ($param);
        $this->createObj('DMaster');
        if (!$this->IsCallback&&!$this->IsPostBack) {    
            try {                
                if (isset($this->request['id'])) {
                   $no_rek5=addslashes($this->request['id']);
                    $str = "SELECT no_rek5,nama_rek5,merek,id_satuan FROM rek5 WHERE no_rek5='$no_rek5'";
                    $r=$this->DB->getRecord($str); 
                     if (isset($r[1])) {
                        $data=$r[1];
                        $this->hiddennorekening->Value=$data['no_rek5'];
                        $this->lblAddKodeNamaUraian->Text=$data['no_rek5'].' / '.$data['nama_rek5'];
                        $this->lblAddNamaMerek->Text=$data['merek'];                          
                        $this->lblAddSatuan->Text=$this->DMaster->getNamaSatuan($data['id_satuan']);                       
                     }else{
                        throw new Exception ("Tidak bisa tambah Uraian karena No. Rekening Belum terdaftar.");
                     }
                }else{
                    $this->idProcess='view'; 
                    $rekening=$this->DMaster->getList('rek1 WHERE id_tipe=1',array('no_rek1','nama_rek1'),'no_rek1',null,7);        
                    $this->cmbAddTransaksi->DataSource=$rekening;
                    $this->cmbAddTransaksi->dataBind();            
                }  
                   
            }catch (Exception $ex) {
                $this->idProcess='view';
                $errormessage='
                <div class="alert alert-warning" id="divLabelErrorMessage">
                    <i class="fa fa-info-circle fa-fw fa-lg"></i>
                    <strong>
                        Info!
                    </strong>'.$ex->getMessage().'
                    
                </div>';
                $this->errorMessage->Text=$errormessage;

                $rekening=$this->DMaster->getList('rek1 WHERE id_tipe=1',array('no_rek1','nama_rek1'),'no_rek1',null,7);        
                $this->cmbAddTransaksi->DataSource=$rekening;
                $this->cmbAddTransaksi->dataBind();         
            }   
        }
    }
    public function changeRekening ($sender,$param) {
        $this->idProcess='view';        
        $ta=$_SESSION['ta'];
        switch ($sender->getId()) {
            case 'cmbAddTransaksi' :
                $no_rek1=$this->cmbAddTransaksi->Text;
                $this->disableComponentRekening1 ();
                if ($no_rek1 != 'none' || $no_rek1 != '') {
                    $result=$this->DMaster->getList("rek2 WHERE no_rek1='$no_rek1'",array('no_rek2','nama_rek2'),'no_rek2',null,7);
                    if (count($result)> 1) {
                        $this->cmbAddKelompok->DataSource=$result;
                        $this->cmbAddKelompok->Enabled=true;
                        $this->cmbAddKelompok->dataBind();
                    }
                }
            break;          
            case 'cmbAddKelompok' :
                $no_rek2 = $this->cmbAddKelompok->Text;
                $this->disableComponentRekening2 ();
                if ($no_rek2 != 'none' || $no_rek2 !='') {
                    $result=$this->DMaster->getList("rek3 WHERE no_rek2='$no_rek2'",array('no_rek3','nama_rek3'),'no_rek3',null,7);
                    if (count($result)> 1) {
                        $this->cmbAddJenis->DataSource=$result;
                        $this->cmbAddJenis->Enabled=true;
                        $this->cmbAddJenis->dataBind();
                    }
                }
            break;
            case 'cmbAddJenis' :
                $no_rek3= $this->cmbAddJenis->Text;
                $this->disableComponentRekening3 ();
                if ($no_rek3 != 'none' || $no_rek3!= '') {
                    $result=$this->DMaster->getList("rek4 WHERE no_rek3='$no_rek3'",array('no_rek4','nama_rek4'),'no_rek4',null,7);
                    if (count($result)> 1) {
                        $this->cmbAddObjek->DataSource=$result;
                        $this->cmbAddObjek->Enabled=true;
                        $this->cmbAddObjek->dataBind();
                    }
                }
            break;          
            case 'cmbAddObjek' :
                $no_rek4 = $this->cmbAddObjek->Text;
                $this->disableComponentRekening4 ();
                if ($no_rek4 != 'none' && $no_rek4 != '') {
                    $result=$this->DMaster->getList("rek5 WHERE no_rek4='$no_rek4' AND no_rek5 NOT IN (SELECT rekening FROM uraian WHERE ta=$ta)",array('no_rek5','nama_rek5'),'no_rek5',null,7);
                    if (count($result)> 1) {                        
                        $this->cmbAddRincian->DataSource=$result;
                        $this->cmbAddRincian->Enabled=true;
                        $this->cmbAddRincian->dataBind();
                    }
                }
            break;
        }
    }   
    private function disableComponentRekening1 () {     
        $this->cmbAddKelompok->DataSource=array();
        $this->cmbAddKelompok->Enabled=false;
        $this->cmbAddKelompok->dataBind();
                    
        $this->cmbAddJenis->DataSource=array();
        $this->cmbAddJenis->Enabled=false;
        $this->cmbAddJenis->dataBind(); 
                    
        $this->cmbAddObjek->DataSource=array();
        $this->cmbAddObjek->Enabled=false;
        $this->cmbAddObjek->dataBind(); 
                    
        $this->cmbAddRincian->DataSource=array();
        $this->cmbAddRincian->Enabled=false;
        $this->cmbAddRincian->dataBind();   
    }
    
    private function disableComponentRekening2 () { 
        $this->cmbAddJenis->DataSource=array();
        $this->cmbAddJenis->Enabled=false;
        $this->cmbAddJenis->dataBind(); 
                    
        $this->cmbAddObjek->DataSource=array();
        $this->cmbAddObjek->Enabled=false;
        $this->cmbAddObjek->dataBind(); 
                    
        $this->cmbAddRincian->DataSource=array();
        $this->cmbAddRincian->Enabled=false;
        $this->cmbAddRincian->dataBind();   
    }
    private function disableComponentRekening3 () {
                                
        $this->cmbAddObjek->DataSource=array();
        $this->cmbAddObjek->Enabled=false;
        $this->cmbAddObjek->dataBind(); 
                    
        $this->cmbAddRincian->DataSource=array();
        $this->cmbAddRincian->Enabled=false;
        $this->cmbAddRincian->dataBind();   
    }
    
    private function disableComponentRekening4 () {                 
        $this->cmbAddRincian->DataSource=array();
        $this->cmbAddRincian->Enabled=false;
        $this->cmbAddRincian->dataBind();   
    }    
    public function saveData ($sender,$param) {
        if ($this->IsValid) {

            $no_rekening=$this->hiddennorekening->Value;             
            $txtHargaBatam=str_replace(',','',$this->txtAddHargaBatam->Text);
            $txtHargaBintan=str_replace(',','',$this->txtAddHargaBintan->Text);
            $txtHargaTanjungpinang=str_replace(',','',$this->txtAddHargaTanjungpinang->Text);
            $txtHargaKarimun=str_replace(',','',$this->txtAddHargaKarimun->Text);
            $txtHargaLingga=str_replace(',','',$this->txtAddHargaLingga->Text);
            $txtHargaNatuna=str_replace(',','',$this->txtAddHargaNatuna->Text);
            $txtHargaAnambas=str_replace(',','',$this->txtAddHargaAnambas->Text);
            $ta=$_SESSION['ta'];
            
            $str = "INSERT INTO uraian SET rekening='$no_rekening',batam='$txtHargaBatam',bintan='$txtHargaBintan',tanjungpinang='$txtHargaTanjungpinang',karimun='$txtHargaKarimun',lingga='$txtHargaLingga',natuna='$txtHargaNatuna',anambas='$txtHargaAnambas',ta=$ta,date_added=NOW(),date_modified=NOW()";
            $this->DB->insertRecord($str);
            
            $this->Pengguna->insertNewActivity($this->Page->getPagePath(),"Menambah uraian baru dengan Rekening  ($no_rekening) telah berhasil dilakukan");
            
            $this->redirect('data.TambahUraian', true);
        }
    }
    public function lanjutInput($sender,$param) {
        if ($this->IsValid) {
            $no_rek5=$sender->Text;
            $this->redirect('data.TambahUraian', true,array('id'=>$no_rek5));   
        }
    }
}