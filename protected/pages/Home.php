<?php
prado::using ('Application.MainPageF');
class Home extends MainPageF {
	public function onLoad($param) {		
		parent::onLoad($param);		            
        $this->showDashboard=true;        
        $this->createObj('DMaster');
		if (!$this->IsPostBack&&!$this->IsCallBack) {              
            if (!isset($_SESSION['currentPageHomeF'])||$_SESSION['currentPageHomeF']['page_name']!='Home') {
                $_SESSION['currentPageHomeF']=array('page_name'=>'Home','page_num'=>0,'modeuraian'=>'barang_jasa');												
			} 
			$_SESSION['currentPageHomeF']['search']=false;
			$this->cmbKriteria->Text=$_SESSION['currentPageHomeF']['modeuraian'];

			$this->populateData();
		}                
	}
	public function changeModeUraian ($sender,$param) {
		$_SESSION['currentPageHomeF']['modeuraian']=$this->cmbKriteria->Text;
		$this->populateData();
	}  
	public function renderCallback ($sender,$param) {
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {
		$_SESSION['currentPageUraian']['page_num']=$param->NewPageIndex;
		$this->populateData($_SESSION['currentPageHomeF']['search']);
	} 
	public function renderCallbackTransport ($sender,$param) {
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_ChangedTransport ($sender,$param) {
		$_SESSION['currentPageUraian']['page_num']=$param->NewPageIndex;
		$this->populateData($_SESSION['currentPageHomeF']['search']);
	} 
	public function doSearch($sender,$param) {
		$_SESSION['currentPageHomeF']['search']=true;
		$this->populateData($_SESSION['currentPageHomeF']['search']);
	}
	public function populateData ($search=false) {
		$tahun=$this->setup->getSettingValue('default_ta'); ;
		$modeuraian=$_SESSION['currentPageHomeF']['modeuraian'];
		if ($search) {
			$txtsearch=addslashes($this->txtSearch->Text);
			switch ($modeuraian) {
				case 'barang_jasa' :
					$str = "SELECT um.iduraian,um.rekening,rek5.nama_rek5,rek5.merek,rek5.id_satuan,um.batam,um.bintan,um.tanjungpinang,um.karimun,um.lingga,um.natuna,um.anambas FROM uraian um,v_rekening rek5 WHERE rek5.no_rek5=um.rekening AND um.ta=$tahun AND rek5.id_tipe=1 AND (rek5.nama_rek5 LIKE '%$txtsearch%' OR um.rekening  LIKE '%$txtsearch%')";    
            		$jumlah_baris=$this->DB->getCountRowsOfTable (" uraian um,v_rekening rek5 WHERE rek5.no_rek5=um.rekening AND um.ta=$tahun AND rek5.id_tipe=1 AND (rek5.nama_rek5 LIKE '%$txtsearch%' OR um.rekening  LIKE '%$txtsearch%')",'um.iduraian');	
            		$repeaters=$this->RepeaterS;
				break;
				case 'transportasi' :
					$str = "SELECT um.iduraian,um.rekening,rek5.nama_rek5,rek5.merek,rek5.id_satuan,um.batam,um.bintan,um.tanjungpinang,um.karimun,um.lingga,um.natuna,um.anambas FROM uraian um,v_rekening rek5 WHERE rek5.no_rek5=um.rekening AND um.ta=$tahun AND rek5.id_tipe=2 AND (rek5.nama_rek5 LIKE '%$txtsearch%' OR um.rekening  LIKE '%$txtsearch%')";    
            		$jumlah_baris=$this->DB->getCountRowsOfTable (" uraian um,v_rekening rek5 WHERE rek5.no_rek5=um.rekening AND um.ta=$tahun AND rek5.id_tipe=2 AND (rek5.nama_rek5 LIKE '%$txtsearch%' OR um.rekening  LIKE '%$txtsearch%')",'um.iduraian');	
            		$repeaters=$this->RepeaterTransport;
				break;
			}
		}else{
			switch ($modeuraian) {
				case 'barang_jasa' :
					$str = "SELECT um.iduraian,um.rekening,rek5.nama_rek5,rek5.merek,rek5.id_satuan,um.batam,um.bintan,um.tanjungpinang,um.karimun,um.lingga,um.natuna,um.anambas FROM uraian um,v_rekening rek5 WHERE rek5.no_rek5=um.rekening AND um.ta=$tahun AND rek5.id_tipe=1";    
            		$jumlah_baris=$this->DB->getCountRowsOfTable (" uraian um,v_rekening rek5 WHERE rek5.no_rek5=um.rekening AND um.ta=$tahun AND rek5.id_tipe=1",'um.iduraian');	
					$repeaters=$this->RepeaterS;
				break;
				case 'transportasi' :
					$str = "SELECT um.iduraian,um.rekening,rek5.nama_rek5,rek5.merek,rek5.id_satuan,um.batam,um.bintan,um.tanjungpinang,um.karimun,um.lingga,um.natuna,um.anambas FROM uraian um,v_rekening rek5 WHERE rek5.no_rek5=um.rekening AND um.ta=$tahun AND rek5.id_tipe=2";    
            		$jumlah_baris=$this->DB->getCountRowsOfTable (" uraian um,v_rekening rek5 WHERE rek5.no_rek5=um.rekening AND um.ta=$tahun AND rek5.id_tipe=2",'um.iduraian');	
            		$repeaters=$this->RepeaterTransport;
				break;
			}
		}		

		$repeaters->CurrentPageIndex=$_SESSION['currentPageHomeF']['page_num'];
		$repeaters->VirtualItemCount=$jumlah_baris;
		$currentPage=$repeaters->CurrentPageIndex;
		$offset=$currentPage*$repeaters->PageSize;		
		$itemcount=$repeaters->VirtualItemCount;
		$limit=$repeaters->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=10;$_SESSION['currentPageHomeF']['page_num']=0;}
        $str = "$str ORDER BY um.rating DESC,um.rekening ASC LIMIT $offset,$limit";
		$r=$this->DB->getRecord($str);		
                
        $repeaters->DataSource=$r;
        $repeaters->dataBind();

        $this->paginationInfo->Text=$this->getInfoPaging($repeaters);
	}
}