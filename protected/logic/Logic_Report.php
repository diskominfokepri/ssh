<?php
prado::using ('Application.logic.Logic_Global');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Borders;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;


class Logic_Report extends Logic_Global {	
    /**
	* mode dari driver
	*
	*/
	protected $driver;
	/**
	* object dari driver2 report misalnya PHPExcel, TCPDF, dll.
	*
	*/
	public $rpt;	
    /**
	* object setup;	
	*/
	public $setup;	
    /**
	* object tanggal;	
	*/
    public $tgl;
	/**
	* Exported Dir
	*
	*/
	protected $exportedDir;	
	/**
	* posisi row sekarang
	*
	*/
	public $currentRow=1;		
    /**
     * 
     * data report	
	*/
	public $dataReport;	
    /**
     * 
     * data array  
    */
    public $result=array(); 
	public function __construct ($db) {
		parent::__construct ($db);	
        $this->setup = $this->getLogic ('Setup');
		$this->tgl = $this->getLogic ('Penanggalan');
	}		
    /**
     * digunakan untuk mengeset data report
     * @param type $dataReport
     */
    public function setDataReport ($dataReport) {
        $this->dataReport=$dataReport;
    }
    /**
	*
	* set mode driver
	*/
	public function setMode ($driver) {
		$this->driver = $driver;
		$path = dirname($this->getPath()).'/';								
		$host=$this->setup->getAddress().'/';				
		switch ($driver) {
            case 'excel2003' :								
                $phpexcel=BASEPATH.'protected/lib/excel/';
                define ('PHPEXCEL_ROOT',$phpexcel);
                set_include_path(get_include_path() . PATH_SEPARATOR . $phpexcel);
                
                require_once ('PHPExcel.php');                
				$this->rpt=new PHPExcel();                
                $this->exportedDir['excel_path']=$host.'exported/excel/';
				$this->exportedDir['full_path']=$path.'exported/excel/';
			break;
			case 'excel2007' :							
                //phpspeadsheet
				$this->rpt=new Spreadsheet();                                
				$this->exportedDir['excel_path']=$host.'exported/excel/';
				$this->exportedDir['full_path']=$path.'exported/excel/';
			break;					
            case 'pdf' :				
                require_once (BASEPATH.'protected/lib/tcpdf/tcpdf.php');
				$this->rpt=new TCPDF();			
				$this->rpt->setCreator ($this->Application->getID());
				$this->rpt->setAuthor ($this->setup->getSettingValue('config_name'));
				$this->rpt->setPrintHeader(false);
				$this->rpt->setPrintFooter(false);				
				$this->exportedDir['pdf_path']=$host.'exported/pdf/';	
				$this->exportedDir['full_path']=$path.'exported/pdf/';
			break;	
            case 'pdfzip' :
                $this->exportedDir['pdf_path']=$host.'exported/pdf/';	
				$this->exportedDir['full_path']=$path.'exported/pdf/';
            break;
		}
	}
    /**
     * digunakan untuk mendapatkan driver saat ini
     */
	public function getDriver () {
        return $this->driver;
    }
    /**
     * digunakan untuk mencetak laporan
     * @param type $filename
     * @param type $debug
     */
	public function printOut ($filename,$debug=false) {	
		$filename_to_write = $debug == true ? $filename  : $filename.'_'.date('Y_m_d_H_m_s');	
		switch ($this->driver) {
			case 'excel2003' :
                //$writer=new PHPExcel_Writer_Excel5($this->rpt);								
                $writer=PHPExcel_IOFactory::createWriter($this->rpt, 'Excel5');
                $writer->setPreCalculateFormulas(false);
				$filename_to_write = "$filename_to_write.xls";
				$writer->save ($this->exportedDir['full_path'].$filename_to_write);		
				$this->exportedDir['filename']=$filename;
				$this->exportedDir['excel_path'].=$filename_to_write;		
            break;
			case 'excel2007' :
				$writer = new Xlsx($this->rpt);
				$filename_to_write = "$filename_to_write.xlsx";
				$writer->save ($this->exportedDir['full_path'].$filename_to_write);		
				$this->exportedDir['filename']=$filename;
				$this->exportedDir['excel_path'].=$filename_to_write;		
			break;	
            case 'pdf' :
				$filename_to_write="$filename_to_write.pdf";
				$this->rpt->output ($this->exportedDir['full_path'].$filename_to_write,'F');
				$this->exportedDir['filename']=$filename;
				$this->exportedDir['pdf_path'].=$filename_to_write;		
			break;            
		}
	}    
    /**
     * digunakan untuk printout ke dalam bentuk archive
     * @param type $DataFile
     * @param type $FileName
     * @param type $FormatArchive
     */
    public function printOutArchive ($DataFile,$FileName,$FormatArchive) {	
        switch ($FormatArchive) {
            case 'zip' :                        
                $namafile=$FileName.'_'.date('Y_m_d_H_m_s').'.zip';
                $destinationfile=$this->exportedDir['full_path'].$namafile;
                $this->setup->createZIP($DataFile,$destinationfile);
                $this->exportedDir['pdf_path'].=$namafile;		
            break;
        }
    }
    /**
	* digunakan untuk mendapatkan link ke sebuah file hasil dari export	
	* @param obj_out object 
	* @param text in override text result
	*/
	public function setLink ($obj_out,$text='') {
		$filename=$text==''?$this->exportedDir['filename']:$text;		        
		switch ($this->driver) {
			case 'excel2003' :
                $obj_out->Text = "$filename.xls";
				$obj_out->NavigateUrl=$this->exportedDir['excel_path'];				
            break;
			case 'excel2007' :                
				$obj_out->Text = "$filename.xlsx";
				$obj_out->NavigateUrl=$this->exportedDir['excel_path'];				
			break;	
            case 'pdf' :
				$obj_out->Text = "$filename.pdf";
				$obj_out->NavigateUrl=$this->exportedDir['pdf_path'];	
			break;
            case 'pdfzip' :
				$obj_out->Text = "$filename.zip";
				$obj_out->NavigateUrl=$this->exportedDir['pdf_path'];	
			break;
		}
	} 
    /**
    * digunakan untuk mendapatkan data uraian   
    */  
    private function getDataUraian ($objDMaster) {     
        $ta=$this->dataReport['ta'];
        $no_rek1=$this->dataReport['no_rek1'];
        $str = "SELECT rek1.no_rek1,rek1.nama_rek1,rek2.no_rek2,rek2.nama_rek2,rek3.no_rek3,rek3.nama_rek3,rek4.no_rek4,rek4.nama_rek4,rek5.no_rek5,rek5.nama_rek5,u.iduraian,rek5.merek,rek5.id_satuan,u.batam,u.bintan,u.tanjungpinang,u.karimun,u.lingga,u.natuna,u.anambas FROM uraian u JOIN rek5 ON (u.rekening=rek5.no_rek5) JOIN rek4 ON (rek4.no_rek4=rek5.no_rek4) JOIN rek3 ON (rek3.no_rek3=rek4.no_rek3) JOIN rek2 ON (rek2.no_rek2=rek3.no_rek2) JOIN rek1 ON (rek1.no_rek1=rek2.no_rek1) WHERE rek1.no_rek1='$no_rek1' AND u.ta=$ta ORDER BY u.rekening ASC";        
        $r1=$this->db->getRecord($str);        
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
                                           'satuan'=>$objDMaster->getNamaSatuan($datauraianproyek['id_satuan']),                                               
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
	/*
     * digunakan untuk mencetak Data Uraian Barang/Peralatan
     */
    public function printDataUraian ($objDMaster) {
        $datauraian=$this->dataReport; 
        $no_rek1=$datauraian['no_rek1'];
        $ta=$datauraian['ta'];
        switch ($this->getDriver()) {
            case 'excel2003' :               
            case 'excel2007' :    
            	$sheet=$this->rpt->getActiveSheet();
            	$sheet->setTitle ("ssh_$no_rek1");
            	$default_style['font']=array('name'=>'Arial',
            								 'size'=>9);

                $sheet->getParent()->getDefaultStyle()->applyFromArray($default_style);
                $row=2;
                $sheet->mergeCells("A$row:O$row");				                
                $sheet->setCellValue("A$row",'STANDAR SATUAN HARGA');
                $row+=1;
                $sheet->mergeCells("A$row:O$row");				                
                $sheet->setCellValue("A$row",'PROVINSI KEPULAUAN RIAU');
                $row+=1;
                $sheet->mergeCells("A$row:O$row");				                
                $sheet->setCellValue("A$row","TAHUN $ta");
                
                $styleArray = new Style();
                $styleArray->applyFromArray(array('alignment'=>array('horizontal'=>Alignment::HORIZONTAL_CENTER,
                										             'vertical'=>Alignment::VERTICAL_CENTER
                										            ),
                								  'font'=>array('bold'=>TRUE,
                								 				'size'=>12),
            									 )
                							);
                $sheet->duplicateStyle($styleArray, "A1:A$row");

                $row+=2;
                $row_merge=$row+1;
                $styleArray = new Style();
                $styleArray->applyFromArray(array('alignment'=>array('horizontal'=>Alignment::HORIZONTAL_CENTER,
                                                                     'vertical'=>Alignment::VERTICAL_CENTER
                                                                    ),
                                                  'font'=>array('bold'=>TRUE,
                                                                'size'=>12),
                                                  'borders'=>array('allBorders'=>array('borderStyle' => Border::BORDER_THIN))

                                                 )
                                            );
                $sheet->duplicateStyle($styleArray, "A$row:O$row_merge");

                $sheet->mergeCells("A$row:E$row_merge");
                $sheet->setCellValue("A$row",'KODE BARANG');
                $sheet->mergeCells("F$row:F$row_merge");
                $sheet->setCellValue("F$row",'NAMA BARANG');
                $sheet->mergeCells("G$row:G$row_merge");
                $sheet->setCellValue("G$row",'MEREK');
                $sheet->mergeCells("H$row:H$row_merge");
                $sheet->setCellValue("H$row",'SATUAN');
                $sheet->mergeCells("I$row:O$row");
                $sheet->setCellValue("I$row",'HARGA (RP.)');
                $row+=1;
                $sheet->setCellValue("I$row",'BATAM');
                $sheet->setCellValue("J$row",'BINTAN');
                $sheet->setCellValue("K$row",'TANJUNGPINANG');
                $sheet->setCellValue("L$row",'KARIMUN');
                $sheet->setCellValue("M$row",'LINGGA');
                $sheet->setCellValue("N$row",'NATUNA');
                $sheet->setCellValue("O$row",'ANAMBAS');
                

                //setting lebar kolom
                $sheet->getColumnDimension('A')->setWidth(5);
                $sheet->getColumnDimension('B')->setWidth(5);
                $sheet->getColumnDimension('C')->setWidth(5);
                $sheet->getColumnDimension('D')->setWidth(5);
                $sheet->getColumnDimension('E')->setWidth(5);
                $sheet->getColumnDimension('F')->setWidth(40);
                $sheet->getColumnDimension('G')->setWidth(15);
                $sheet->getColumnDimension('H')->setWidth(15);
                $sheet->getColumnDimension('I')->setWidth(20);
                $sheet->getColumnDimension('J')->setWidth(20);
                $sheet->getColumnDimension('K')->setWidth(20);
            	$sheet->getColumnDimension('L')->setWidth(20);
                $sheet->getColumnDimension('M')->setWidth(20);
                $sheet->getColumnDimension('N')->setWidth(20);
                $sheet->getColumnDimension('O')->setWidth(20);

                $dataproyek=$this->getDataUraian($objDMaster);           
                $tingkat = $this->getRekeningUraian();

                if (isset($tingkat[1])) {                  
                    $tingkat_1=$tingkat[1];            
                    $tingkat_2=$tingkat[2];
                    $tingkat_3=$tingkat[3];
                    $tingkat_4=$tingkat[4];
                    $tingkat_5=$tingkat[5];     
                    $row=$row+1;               
                    $row_awal=$row;
                    while (list($k1,$v1)=each($tingkat_1)) {
                        foreach ($tingkat_5 as $k5=>$v5) {
                            $rek1=substr($k5,0,2);                    
                            if ($rek1 == $k1) {
                                //tingkat i 
                                $sheet->setCellValue("A$row",$k1);
                                $sheet->setCellValue("F$row",$v1);                                
                                $row=$row+1;    

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
                                        $sheet->setCellValue("A$row",$no_[0]); 
                                        $sheet->setCellValue("B$row",$no_[1]); 
                                        $sheet->setCellValue("F$row",$b); 
                                        $row=$row+1;    

                                        //tingkat iii
                                        foreach ($tingkat_3 as $k3=>$v3) {  
                                            $rek3=substr($k3,0,5);                                    
                                            if ($a==$rek3) {                                                                                
                                                $no_=explode (".",$k3);  
                                                $sheet->setCellValue("A$row",$no_[0]); 
                                                $sheet->setCellValue("B$row",$no_[1]); 
                                                $sheet->setCellValue("C$row",$no_[2]);
                                                $sheet->setCellValue("F$row",$v3); 
                                                $row=$row+1; 

                                                //tingkat iv
                                                foreach ($tingkat_4 as $k4=>$v4) {
                                                    if (preg_match("/^$k3/", $k4)) {                                                                                                        
                                                        $no_=explode (".",$k4); 
                                                        $sheet->setCellValue("A$row",$no_[0]); 
                                                        $sheet->setCellValue("B$row",$no_[1]); 
                                                        $sheet->setCellValue("C$row",$no_[2]);
                                                        $sheet->setCellValue("D$row",$no_[3]);
                                                        $sheet->setCellValue("F$row",$v4);
                                                        $row=$row+1; 

                                                        //tingkat v
                                                        foreach ($tingkat_5 as $k5=>$v5) {
                                                            if (preg_match("/^$k4/", $k5)) {                                                              
                                                                $iduraian=$dataproyek[$k5]['iduraian'];
                                                                $nama_uraian=$dataproyek[$k5]['nama_rek5']; 
                                                                $no_=explode (".",$k5);    
                                                                $merek=$dataproyek[$k5]['merek'];
                                                                $satuan=$dataproyek[$k5]['satuan'];
                                                                $batam=$objDMaster->formatUang($dataproyek[$k5]['batam']);
                                                                $bintan=$objDMaster->formatUang($dataproyek[$k5]['bintan']);
                                                                $tanjungpinang=$objDMaster->formatUang($dataproyek[$k5]['tanjungpinang']);
                                                                $karimun=$objDMaster->formatUang($dataproyek[$k5]['karimun']);
                                                                $lingga=$objDMaster->formatUang($dataproyek[$k5]['lingga']);
                                                                $natuna=$objDMaster->formatUang($dataproyek[$k5]['natuna']);                                                        
                                                                $anambas=$objDMaster->formatUang($dataproyek[$k5]['anambas']);

                                                                $sheet->setCellValue("A$row",$no_[0]); 
                                                                $sheet->setCellValue("B$row",$no_[1]); 
                                                                $sheet->setCellValue("C$row",$no_[2]);
                                                                $sheet->setCellValue("D$row",$no_[3]);
                                                                $sheet->setCellValue("E$row",$no_[4]);
                                                                $sheet->setCellValue("F$row",$v5);
                                                                $sheet->setCellValue("G$row",$merek);
                                                                $sheet->setCellValue("H$row",$satuan);
                                                                $sheet->setCellValue("I$row",$batam);
                                                                $sheet->setCellValue("J$row",$bintan);
                                                                $sheet->setCellValue("K$row",$tanjungpinang);
                                                                $sheet->setCellValue("L$row",$karimun);
                                                                $sheet->setCellValue("M$row",$lingga);
                                                                $sheet->setCellValue("N$row",$natuna);
                                                                $sheet->setCellValue("O$row",$anambas);
                                                                $row=$row+1;  
                                                                
                                                            }   
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
                $row=$row-1;
                 $styleArray2 = new Style();
                 $style['alignment']=array('horizontal'=>Alignment::HORIZONTAL_CENTER,
                                          'vertical'=>Alignment::VERTICAL_CENTER,
                                          'wrapText'     => TRUE);
                 $style['borders']=array('allBorders'=>array('borderStyle' => Border::BORDER_THIN));

                
                 $styleArray2->applyFromArray($style);

                 $sheet->duplicateStyle($styleArray2, "A6:O$row");

                 $styleArray3 = new Style();
                 $style3['alignment']=array('horizontal'=>Alignment::HORIZONTAL_LEFT,
                                          'vertical'=>Alignment::VERTICAL_CENTER,
                                          'wrapText'     => TRUE);
                 $style3['borders']=array('allBorders'=>array('borderStyle' => Border::BORDER_THIN));
                 $styleArray3->applyFromArray($style3);
                 $sheet->duplicateStyle($styleArray3, "F$row_awal:F$row");

                $this->printOut($no_rek1.$ta);
            break;
        }
        $this->setLink($this->dataReport['linkoutput'],"Daftar Barang / Peralatan");
    }
}