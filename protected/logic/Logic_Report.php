<?php
prado::using ('Application.logic.Logic_Global');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
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
    /*
     * digunakan untuk mencetak Data Uraian Barang/Peralatan
     */
    public function printDataUraian () {
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
                $sheet->mergeCells("A$row:K$row");                              
                $sheet->setCellValue("A$row",'STANDAR SATUAN HARGA');
                $row+=1;
                $sheet->mergeCells("A$row:K$row");                              
                $sheet->setCellValue("A$row",'PROVINSI KEPULAUAN RIAU');
                $row+=1;
                $sheet->mergeCells("A$row:K$row");                              
                $sheet->setCellValue("A$row","TAHUN $ta");
                
                $styleArray = new Style();
                $styleArray->applyFromArray(array('alignment'=>array('horizontal'=>Alignment::HORIZONTAL_CENTER,
                                                                     'vertical'=>Alignment::VERTICAL_CENTER
                                                                    ),
                                                  'font'=>array('bold'=>TRUE,
                                                                'size'=>12)
                                                 )
                                            );
                $sheet->duplicateStyle($styleArray, "A1:A$row");

                $row+=4;
                $row_merge=$row+1;
                $sheet->mergeCells("A$row:A$row_merge");
                $sheet->setCellValue("A$row",'KODE BARANG');
                $sheet->mergeCells("B$row:B$row_merge");
                $sheet->setCellValue("B$row",'NAMA BARANG');
                $sheet->mergeCells("C$row:D$row_merge");
                $sheet->setCellValue("C$row",'MEREK');
                $sheet->mergeCells("D$row:D$row_merge");
                $sheet->setCellValue("D$row",'SATUAN');
                $sheet->mergeCells("E$row:K$row");
                $sheet->setCellValue("E$row",'HARGA (RP.)');
                $row+=1;
                $sheet->setCellValue("E$row",'BATAM');
                $sheet->setCellValue("F$row",'BINTAN');
                $sheet->setCellValue("G$row",'TANJUNGPINANG');
                $sheet->setCellValue("H$row",'KARIMUN');
                $sheet->setCellValue("Irow",'LINGGA');
                $sheet->setCellValue("J$row",'NATUNA');
                $sheet->setCellValue("K$row",'ANAMBAS');

                $this->printOut($no_rek1.$ta);
            break;
        }
        $this->setLink($this->dataReport['linkoutput'],"Daftar Barang / Peralatan");
    }
}