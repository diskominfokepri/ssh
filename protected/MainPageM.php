<?php

class MainPageM extends MainPage {
    /**
    * tab Satuan [rekening]
    */
    public $showSatuan=false;
    /**     
     * show page unitkerja fungsional [data master]
     */
    public $showUnitKerja=false;
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
     * show menu setting
     */
    public $showSetting=false;   
    /**     
     * show page user biro ortal[setting]
     */
    public $showUserBiroOrtal=false;
    /**     
     * show page user opd[setting]
     */
    public $showUserOPD=false;
    /**     
     * show page cache[setting]
     */
    public $showCache=false;
	public function onLoad ($param) {		
		parent::onLoad($param);	
	}
}