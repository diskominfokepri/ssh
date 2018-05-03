<?php
prado::using ('Application.MainPageD');
class CReportDataUraian extends MainPageD {	
    public $result=array();		
	public function onLoad ($param) {		
		parent::onLoad ($param);
        $this->showReportDataUraian=true;
        $this->createObj('DMaster');
        if (!$this->IsPostBack&&!$this->IsCallBack) {
            if (!isset($_SESSION['currentPageReportDataUraian'])||$_SESSION['currentPageReportDataUraian']['page_name']!='d.report.ReportDataUraian') {
                $_SESSION['currentPageReportDataUraian']=array('page_name'=>'d.report.ReportDataUraian','page_num'=>0,'rek1'=>'none');                                                
            }  
            try {
                if ($_SESSION['currentPageReportDataUraian']['rek1']=='none') {
                   throw new Exception ("Mohon pilih rekening awal terlebih dahulu.");
                }else{
                    $rekening=$this->DMaster->getList('rek1 WHERE id_tipe=1',array('no_rek1','nama_rek1'),'no_rek1',null,7);        
                    $this->cmbTransaksi->DataSource=$rekening;
                    $this->cmbTransaksi->Text=$_SESSION['currentPageReportDataUraian']['rek1'];
                    $this->cmbTransaksi->dataBind();   
            
                    $this->setLabelModuleHeader();
                    $this->populateData();                    
                }
            } catch (Exception $ex) {
                $this->idProcess='view';	
                $this->errorMessage->Text=$ex->getMessage();

                $rekening=$this->DMaster->getList('rek1 WHERE id_tipe=1',array('no_rek1','nama_rek1'),'no_rek1',null,7);        
                $this->cmbViewTransaksi->DataSource=$rekening;
                $this->cmbViewTransaksi->dataBind();   
            }
		}		
	}
    public function changeRekening ($sender,$param) {         
        $ta=$_SESSION['ta'];
        switch ($sender->getId()) {
            case 'cmbViewTransaksi' :
                $this->idProcess='view';       
                $no_rek1=$this->cmbViewTransaksi->Text;
                $_SESSION['currentPageReportDataUraian']['rek1']=$no_rek1;
            break; 
            case 'cmbTransaksi' :                
                $no_rek1=$this->cmbTransaksi->Text;
                $_SESSION['currentPageReportDataUraian']['rek1']=$no_rek1;
            break;      
        }
        $this->redirect('report.ReportDataUraian',true); 
    }
    private function setLabelModuleHeader () {
        $ta=$_SESSION['ta'];
        $this->lblmoduleheader->Text=$ta;
    }
    public function changeTbBulan ($sender,$param) {	
        $_SESSION['bulanrealisasi_murni']=$this->tbCmbBulan->Text;
        $this->redirect('report.ReportDataUraian',true);    
	}
    public function getDataKegiatan($idx) {
        $datakegiatan=$_SESSION['currentPageFormA']['DataKegiatan'];
        return $datakegiatan[$idx];
    }
    protected function populateData () {		
        $this->contentReport->Text=$this->printContent();
	}
	/**
	* digunakan untuk mendapatkan data uraian	
	*/	
	private function getDataUraian () {		
        $ta=$_SESSION['ta'];
        $no_rek1=$_SESSION['currentPageReportDataUraian']['rek1'];
        $str = "SELECT rek1.no_rek1,rek1.nama_rek1,rek2.no_rek2,rek2.nama_rek2,rek3.no_rek3,rek3.nama_rek3,rek4.no_rek4,rek4.nama_rek4,rek5.no_rek5,rek5.nama_rek5,u.iduraian,rek5.merek,rek5.id_satuan,u.batam,u.bintan,u.tanjungpinang,u.karimun,u.lingga,u.natuna,u.anambas FROM uraian u JOIN rek5 ON (u.rekening=rek5.no_rek5) JOIN rek4 ON (rek4.no_rek4=rek5.no_rek4) JOIN rek3 ON (rek3.no_rek3=rek4.no_rek3) JOIN rek2 ON (rek2.no_rek2=rek3.no_rek2) JOIN rek1 ON (rek1.no_rek1=rek2.no_rek1) WHERE rek1.no_rek1='$no_rek1' AND u.ta=$ta ORDER BY u.rekening ASC";        
        $r1=$this->DB->getRecord($str);        
        $dataAkhir=array();		
        if (isset($r1[1])) {
            while (list($k,$datauraianproyek)=each($r1)) {                
                $iduraian=$datauraianproyek['iduraian'];                                   
                $no_rek5=$datauraianproyek['no_rek5'];
                $dataAkhir[$no_rek5]=array('no_rek1'=>$datauraianproyek['no_rek1'],
                                           'nama_rek1'=>$datauraianproyek['nama_rek1'],
                                           'no_rek2'=>$datauraianproyek['no_rek2'],
                                           'nama_rek2'=>$datauraianproyek['nama_rek2'],
                                           'no_rek3'=>$datauraianproyek['no_rek3'],
                                           'nama_rek3'=>$datauraianproyek['nama_rek3'],
                                           'no_rek4'=>$datauraianproyek['no_rek4'],
                                           'nama_rek4'=>$datauraianproyek['nama_rek4'],
                                           'no_rek5'=>$datauraianproyek['no_rek5'],
                                           'nama_rek5'=>$datauraianproyek['nama_rek5'],
                                           'iduraian'=>$iduraian,                                               
                                           'merek'=>$datauraianproyek['merek'],
                                           'satuan'=>$this->DMaster->getNamaSatuan($datauraianproyek['id_satuan']),                                               
                                           'batam'=>$datauraianproyek['batam'],
                                           'bintan'=>$datauraianproyek['bintan'],
                                           'tanjungpinang'=>$datauraianproyek['tanjungpinang'],
                                           'karimun'=>$datauraianproyek['karimun'],
                                           'lingga'=>$datauraianproyek['lingga'],
                                           'natuna'=>$datauraianproyek['natuna'],
                                           'anambas'=>$datauraianproyek['anambas']);                                  
            
                
            }
        }
        $this->result = $dataAkhir;			        
        return $dataAkhir;
	}
	/**
	* digunakan untuk mendapatkan tingkat rekening		
	*/
	private function getRekeningUraian () {		 
		$a=$this->result;
        $tingkat=array();
		foreach ($a as $v) {					
			$tingkat[1][$v['no_rek1']]=$v['nama_rek1'];
			$tingkat[2][$v['no_rek2']]=$v['nama_rek2'];
			$tingkat[3][$v['no_rek3']]=$v['nama_rek3'];
			$tingkat[4][$v['no_rek4']]=$v['nama_rek4'];
			$tingkat[5][$v['no_rek5']]=$v['nama_rek5'];				
		}
		return $tingkat;
	}
	public function printContent() {		
		$tahun=$_SESSION['ta'];
		$content = '<table border="1" width="100%" style="font-size:11px">';
        $content.= '<thead>';
		$content.= '<tr class="bg-teal-700">';
		$content.= '<th rowspan="2" colspan="5" class="text-center" width="150">KODE <BR>REKENING</th>';				
		$content.= '<th rowspan="2" width="400" class="text-center">URAIAN</th>';
		$content.= '<th rowspan="2" width="100" class="text-center">MEREK</th>';				        				
        $content.= '<th rowspan="2" width="40" class="text-center">SATUAN</th>';				
        $content.= '<th colspan="7" class="text-center">HARGA</th>';		
		$content.= '</tr>';	
        $content.= '<tr class="bg-teal-700">';		
		$content.= '<td class="text-center">BATAM</td>';		        
		$content.= '<td class="text-center">BINTAN</td>';				
        $content.= '<td class="text-center">TANJUNGPINANG</td>';	
        $content.= '<td class="text-center">KARIMUN</td>';	
        $content.= '<td class="text-center">LINGGA</td>';
        $content.= '<td class="text-center">NATUNA</td>';	
        $content.= '<td class="text-center">ANAMBAS</td>';	        					;				
		$content.= '</thead>';
        
		$dataproyek=$this->getDataUraian();           
        $tingkat = $this->getRekeningUraian(); 
        if (isset($tingkat[1])) {                  
            $tingkat_1=$tingkat[1];            
            $tingkat_2=$tingkat[2];
            $tingkat_3=$tingkat[3];
            $tingkat_4=$tingkat[4];
            $tingkat_5=$tingkat[5];                    
            while (list($k1,$v1)=each($tingkat_1)) {
                foreach ($tingkat_5 as $k5=>$v5) {
                    $rek1=substr($k5,0,2);                    
                    if ($rek1 == $k1) {
                        //tingkat i                        
                        $content.= '<tr>';
                        $content.= '<td width="10" class="text-center">'.$k1.'.</td>';
                        $content.= '<td width="10" class="text-center">&nbsp;</td>';
                        $content.= '<td width="10" class="text-center">&nbsp;</td>';
                        $content.= '<td width="10" class="text-center">&nbsp;</td>';
                        $content.= '<td width="10" class="text-center">&nbsp;</td>';
                        $content.= '<td class="left" colspan="10">'.$v1.'</td>';
                        $content.= '</tr>';
                        //tingkat ii
                        foreach ($tingkat_2 as $k2=>$v2) {
                            $rek2_tampil=array();
                            foreach ($tingkat_5 as $k5_level2=>$v5_level2) {
                                $rek2=substr($k5_level2,0,5);
                                if ($rek2 == $k2) {
                                    if (!array_key_exists($k2,$rek2_tampil)){															
                                        $rek2_tampil[$rek2]=$v2;
                                    }							
                                }
                            }
                            foreach ($rek2_tampil as $a=>$b) {                                
                                $no_=explode ('.',$a);                                
                                $content.= '<tr>';
                                $content.= '<td class="text-center">'.$no_[0].'.</td>';
                                $content.= '<td class="text-center">'.$no_[1].'.</td>';
                                $content.= '<td class="text-center">&nbsp;</td>';
                                $content.= '<td class="text-center">&nbsp;</td>';
                                $content.= '<td class="text-center">&nbsp;</td>';
                                $content.= '<td class="left" colspan="10">'.$b.'</td>';                                
                                $content.= '</tr>';

                                //tingkat iii
                                foreach ($tingkat_3 as $k3=>$v3) {	
                                    $rek3=substr($k3,0,5);                                    
                                    if ($a==$rek3) {                                                                                
                                        $no_=explode (".",$k3);                                       
                                        $content.= '<tr>';
                                        $content.= '<td class="text-center">'.$no_[0].'.</td>';
                                        $content.= '<td class="text-center">'.$no_[1].'.</td>';
                                        $content.= '<td class="text-center">'.$no_[2].'.</td>';
                                        $content.= '<td class="text-center">&nbsp;</td>';
                                        $content.= '<td class="text-center">&nbsp;</td>';											
                                        $content.= '<td class="left" colspan="10">'.$v3.'</td>';									                                                                     
                                        $content.= '</tr>';

                                        foreach ($tingkat_4 as $k4=>$v4) {
                                            if (preg_match("/^$k3/", $k4)) {                                           				                                                
                                                $no_=explode (".",$k4);                                                
                                                $content.= '<tr>';
                                                $content.= '<td class="text-center">'.$no_[0].'.</td>';
                                                $content.= '<td class="text-center">'.$no_[1].'.</td>';
                                                $content.= '<td class="text-center">'.$no_[2].'.</td>';
                                                $content.= '<td class="text-center">'.$no_[3].'.</td>';											
                                                $content.= '<td class="text-center">&nbsp;</td>';		
                                                $content.= '<td class="left" colspan="10">'.$v4.'</td>';																					                                                                                                                                         
                                                $content.= '</tr>';

                                                foreach ($tingkat_5 as $k5=>$v5) {
                                                    if (preg_match("/^$k4/", $k5)) {                                                              
                                                        $iduraian=$dataproyek[$k5]['iduraian'];
                                                        $nama_uraian=$dataproyek[$k5]['nama_rek5']; 
                                                        $no_=explode (".",$k5);    
                                                        $merek=$dataproyek[$k5]['merek'];
                                                        $satuan=$dataproyek[$k5]['satuan'];
                                                        $batam=$this->DMaster->formatUang($dataproyek[$k5]['batam']);
                                                        $bintan=$this->DMaster->formatUang($dataproyek[$k5]['bintan']);
                                                        $tanjungpinang=$this->DMaster->formatUang($dataproyek[$k5]['tanjungpinang']);
                                                        $karimun=$this->DMaster->formatUang($dataproyek[$k5]['karimun']);
                                                        $lingga=$this->DMaster->formatUang($dataproyek[$k5]['lingga']);
                                                        $natuna=$this->DMaster->formatUang($dataproyek[$k5]['natuna']);                                                        
                                                        $anambas=$this->DMaster->formatUang($dataproyek[$k5]['anambas']);                                                        
                                                        $content.= '<tr>';
                                                        $content.= '<td class="text-center">'.$no_[0].'.</td>';
                                                        $content.= '<td class="text-center">'.$no_[1].'.</td>';
                                                        $content.= '<td class="text-center">'.$no_[2].'.</td>';
                                                        $content.= '<td class="text-center">'.$no_[3].'.</td>';															
                                                        $content.= '<td class="text-center">'.$no_[4].'</td>';
                                                        $content.= '<td class="left">'.$v5.'</td>';
                                                        $content.= '<td class="text-center">'.$merek.'</td>';
                                                        $content.= '<td class="text-center">'.$satuan.'</td>';         
                                                        $content.= '<td class="text-center">'.$batam.'</td>';                                                        
                                                        $content.= '<td class="text-center">'.$bintan.'</td>';
                                                        $content.= '<td class="text-center">'.$tanjungpinang.'</td>';
                                                        $content.= '<td class="text-center">'.$karimun.'</td>';
                                                        $content.= '<td class="text-center">'.$lingga.'</td>';
                                                        $content.= '<td class="text-center">'.$natuna.'</td>';
                                                        $content.= '<td class="text-center">'.$anambas.'</td>';                                                                                                                
                                                        $content.= '</tr>';	                                                        															
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }						
                        }
                        break;
                    }
                    continue;
                }
            }            
            $content.= '</table>';		

            return $content;
        }else {
            $this->btnPrint->Enabled=false;
            return "Belum ada ada data T.A $tahun";
        }
	}
	public function printOut ($sender,$param) {   
        $this->createObj('report');
        $this->linkOutput->Text='';
        $this->linkOutput->NavigateUrl='#';

        $tahun=$_SESSION['ta'];
        
        $dataReport['no_rek1']=$_SESSION['currentPageReportDataUraian']['rek1'];
        $dataReport['ta']=$tahun;
        $dataReport['linkoutput']=$this->linkOutput; 
        
        $this->report->setDataReport($dataReport); 
        $this->report->setMode('excel2007');
        
        $this->report->printDataUraian($this->DMaster);
        
        $this->labelPrintOut->Text="Report Data Harga Barang / Peralatan T.A $tahun";
        $this->DialogPrintOut->Open();
    }  
}