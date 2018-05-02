<?php
/**
* digunakan untuk memproses data master
*/
prado::using ('Application.logic.Logic_Global');
class Logic_DMaster extends Logic_Global {  
    /**
     * @param type $db
     */
   	public function __construct ($db) {
		parent::__construct ($db);	        
	}
    /**
     * digunakan untuk mendapatkan daftar satuan
     */
    public function getListSatuan () {
        if ($this->Application->Cache) {            
            $dataitem=$this->Application->Cache->get('listsatuan');              
            if (!isset($dataitem['none'])) {
                $dataitem=$this->getList('satuan',array('id_satuan','nama_satuan'),'nama_satuan',null,1);
                $dataitem['none']='DAFTAR SATUAN';
                $this->Application->Cache->set('listsatuan',$dataitem);
            }            
        }else {                        
            $dataitem=$this->getList('id_satuan',array('id_satuan','nama_satuan'),'nama_satuan',null,1);
            $dataitem['none']='DAFTAR SATUAN';    
        }
        return $dataitem;
    }
    /**
     * digunakan untuk mendapatkan nama satuan
     * @param type $id_satuan
     * @return type
     */
    public function getNamaSatuan ($id_tipe=null) {
        if ($this->Application->Cache) {            
            $data=$this->getListSatuan();            
            $dataitem=isset($data[$id_tipe])?$data[$id_tipe]:'-';
        }else {  
            $str="SELECT nama_satuan FROM nama_satuan WHERE id_tipe=$id_tipe";
            $r=$this->db->getRecord($str);
            $dataitem=$r[1]['nama_satuan'];            
        }
        return $dataitem;
    }
	/**
     * digunakan untuk mendapatkan daftar tipe barang
     */
	public function getListTipeBarang () {
		if ($this->Application->Cache) {            
            $dataitem=$this->Application->Cache->get('listtipe');            
            if (!isset($dataitem['none'])) {
				$dataitem=$this->getList('tipe_barang',array('id_tipe','nama_tipe'),'nama_tipe',null,1);
				$dataitem['none']='DAFTAR TIPE BARANG';
				$this->Application->Cache->set('listtipe',$dataitem);
			}
		}else {                        
            $dataitem=$this->getList('tipe',array('id_tipe','nama_tipe'),'nama_tipe',null,1);
            $dataitem['none']='DAFTAR TIPE BARANG';    
        }
		return $dataitem;
	}    
	/**
     * digunakan untuk mendapatkan nama tipe barang
     * @param type $id_tipe
     * @return type
     */
    public function getNamaTipeBarang ($id_tipe=null) {
        if ($this->Application->Cache) {            
            $data=$this->getListTipeBarang ();            
            $dataitem=$data[$id_tipe];
        }else {  
            $str="SELECT nama_tipe FROM nama_tipe WHERE id_tipe=$id_tipe";
            $r=$this->db->getRecord($str);
            $dataitem=$r[1]['nama_tipe'];            
        }
        return $dataitem;
    }
    /**
     * digunakan untuk mendapatkan daftar unit kerja
     */
    public function getListTahunAnggaran () {
        if ($this->Application->Cache) {            
            $dataitem=$this->Application->Cache->get('listta');            
            if (!isset($dataitem['none'])) {
                $dataitem=$this->getList('ta',array('tahun','nama_tahun'),'tahun',null,1);
                $dataitem['none']='DAFTAR T.A';    
                $this->Application->Cache->set('listta',$dataitem);
            }
        }else {                        
            $dataitem=$this->getList('unit',array('tahun','nama_tahun'),'tahun',null,1);
            $dataitem['none']='DAFTAR T.A';    
        }
        return $dataitem;
    }
    /**
     * digunakan untuk mendapatkan daftar unit kerja
     */
    public function getListUnitKerja () {
        if ($this->Application->Cache) {            
            $dataitem=$this->Application->Cache->get('listunitkerja');            
            if (!isset($dataitem['none'])) {
                $dataitem=$this->getList('unit',array('idunit','kode_unit','nama_unit'),'kode_unit',null,8);
                $dataitem['none']='DAFTAR OPD';    
                $this->Application->Cache->set('listunitkerja',$dataitem);
            }
        }else {                        
            $dataitem=$this->getList('unit',array('idunit','kode_unit','nama_unit'),'kode_unit',null,8);
            $dataitem['none']='DAFTAR OPD';    
        }
        return $dataitem;        
    }
        /**
     * digunakan untuk mendapatkan nama unit kerja
     * @param type $idunit
     * @return type
     */
    public function getNamaUnitKerja ($idunit=null) {
        if ($this->Application->Cache) {            
            $data=$this->getListUnitKerja ();            
            $dataitem=$data[$idunit];
        }else {  
            $str="SELECT nama_unit FROM unit WHERE idunit=$idunit";
            $this->db->setFieldTable(array('nama_unit'));
            $r=$this->db->getRecord($str);
            $dataitem=$r[1]['nama_unit'];            
        }
        return $dataitem;
    }
    /**
     * digunakan untuk mendapatkan kode rekening terakhir
     */
    public function getKodeRekeningTerakhir($rek) {
        $rekening=explode('.',$rek);
        $countarray=count ($rekening);
        $account=false;
        if ($countarray > 0) {
            $account=$rekening[$countarray-1];
        }
        return $account;
    }
    /**
     * digunakan untuk mendapatkan informasi lokasi
     * @param type $idlok
     * @param type $ket_lok
     * @return string
     */
    public function getLokasi ($idlok,$ket_lok) {
        if ($idlok > 0) {
            switch ($ket_lok) {
                case 'negara' :
                case 'neg' :
                    $sql="SELECT nama_negara AS lokasi FROM negara WHERE idnegara='$idlok'";				
                break;
                case 'dt1' :
                    $sql="SELECT dt1.nama_dt1 AS lokasi FROM dt1,negara n WHERE dt1.idnegara=n.idnegara AND dt1.iddt1='$idlok'";	
                break;
                case 'dt2' :
                    $sql="SELECT dt2.nama_dt2 AS lokasi FROM dt2,dt1,negara n WHERE dt2.iddt1=dt1.iddt1 AND dt1.idnegara=n.idnegara AND dt2.iddt2='$idlok'";	
                break;
                case 'kecamatan' :
                case 'kec' :
                    $sql="SELECT kec.nama_kecamatan AS lokasi FROM kecamatan kec,dt2,dt1,negara n WHERE kec.iddt2=dt2.iddt2 AND dt2.iddt1=dt1.iddt1 AND dt1.idnegara=n.idnegara AND kec.idkecamatan='$idlok'";	
                break;            
                case 'kel' :
                    $sql="SELECT CONCAT(kel.nama_kelurahan,',',kec.nama_kecamatan) AS lokasi FROM kelurahan kel,kecamatan kec,dt2 WHERE kec.idkecamatan=kel.idkecamatan AND dt2.iddt2=kec.iddt2 AND kel.idkelurahan='$idlok'";	
                break;                
            }          
            $this->db->setFieldTable(array('lokasi'));
            $r=$this->db->getRecord($sql);
            if (isset($r[1])){
				$lokasi=$r[1]['lokasi'];
			}else {
				$lokasi='N.A';
			}
        }else{
            $lokasi='N.A';
        }  
        return $lokasi;
    }
}