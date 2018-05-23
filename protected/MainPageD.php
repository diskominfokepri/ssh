<?php
class MainPageD extends MainPage {
    /**
    * tab Satuan [rekening]
    */
    public $showSatuan=false;      
	/**     
     * show page rekening [dmaster]
     */
    public $showRekening=false;    
    /**
	* tab Transaksi [rekening]
	*/
	public $showTransaksi=false;	
    /**
	* tab Kelompok [rekening]
	*/
	public $showKelompok=false;	
    /**
	* tab Jenis [rekening]
	*/
	public $showJenis=false;	
    /**
	* tab Objek [rekening]
	*/
	public $showObjek=false;
     /**
    * tab Objek Transportasi [rekening]
    */
    public $showObjekTransportasi=false;	
    /**
	* tab Rincian [rekening]
	*/
	public $showRincian=false;
    /**     
     * show menu data
     */
    public $showData=false;
    /**     
     * show menu data uraian
     */
    public $showDataUraian=false; 
    /**     
     * show menu data uraian transportasi
     */
    public $showDataUraianTransportasi=false;
    /**     
     * show menu data uraian transportasi
     */
    public $showReportDataUraianTransportasi=false;
    /**     
     * show menu data usulan
     */
    public $showDataUsulan=false; 
    /**     
     * show menu data usulan barang
     */
    public $showUsulanBarang=false;
    /**     
     * show menu data usulan transportasi
     */
    public $showDataUsulanTransportasi=false;
    /**     
     * show menu data usulan barang
     */
    public $showDataUsulanBarang=false;   
   
	public function onLoad ($param) {		
		parent::onLoad($param);	
	}
}