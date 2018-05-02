<?php
prado::using ('Application.MainPageM');
class CUserOPD extends MainPageM {	
	public function onLoad ($param) {
		parent::onLoad ($param);        
        $this->showSetting=true;
        $this->showUserOPD=true;
        $this->createObj('DMaster'); 
		if (!$this->IsPostBack&&!$this->IsCallBack) {		
            if (!isset($_SESSION['currentPageUserOPD'])||$_SESSION['currentPageUserOPD']['page_name']!='m.settings.UserOPD') {
                $_SESSION['currentPageUserOPD']=array('page_name'=>'m.settings.UserOPD','page_num'=>0);												
			}
			$this->populateData ();
		}
	}
	public function renderCallback ($sender,$param) {
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {
		$_SESSION['currentPageUserOPD']['page_num']=$param->NewPageIndex;
		$this->populateData();
	} 
	protected function populateData () {
        $this->RepeaterS->CurrentPageIndex=$_SESSION['currentPageUserOPD']['page_num'];
		$jumlah_baris=$this->DB->getCountRowsOfTable ('user','userid');		
		$this->RepeaterS->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterS->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterS->PageSize;		
		$itemcount=$this->RepeaterS->VirtualItemCount;
		$limit=$this->RepeaterS->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=10;$_SESSION['currentPageUserOPD']['page_num']=0;}
        $str = "SELECT userid, username, email, page, user.idunit, unit.nama_unit FROM user left join unit on user.idunit=unit.idunit where page='d' ORDER BY username ASC LIMIT $offset,$limit";
		$r=$this->DB->getRecord($str,$offset+1);        
		$this->RepeaterS->DataSource=$r;
		$this->RepeaterS->dataBind();
	}
	
	protected function addProcess ($sender,$param) {
		$this->idProcess='add';	
		$this->cmbAddOPD->DataSource=$this->DMaster->getListUnitKerja();
		$this->cmbAddOPD->dataBind();	
	}
	public function checkKodeUser ($sender,$param) {
		$this->idProcess=$sender->getId()==='checkAddUsername'?'add':'edit';
        $username=$param->Value;
        if ($username != '') {
            try {
                if ($this->hiddenid->Value != $username){                                        
                    if ($this->DB->checkRecordIsExist ('username','user',$username)) {
                        throw new Exception("($username) sudah tersedia silahkan ganti dengan yang lain.");				
                    }
                }
            }catch (Exception $e) {
                $param->IsValid=false;
                $sender->ErrorMessage=$e->getMessage();
            }
        }		
	}
	public function saveData ($sender,$param) {
		if ($this->Page->IsValid) {
            $username=addslashes($this->txtAddUsername->Text);
			$userpassword=strtoupper(addslashes($this->txtAddPassword->Text));
			$idunit=$this->cmbAddOPD->Text;
			$data_password=$this->Pengguna->createHashPassword($userpassword);
			
			$userpassword=$data_password['password'];
			$salt=$data_password['salt'];

			$str = "INSERT INTO user SET username='$username', userpassword='$userpassword', salt='$salt', page='d', idunit='$idunit', theme='default', photo_profile='resources/userimages/no_photo.png' ";
			$this->DB->insertRecord($str);
            $this->Pengguna->insertNewActivity($this->Page->getPagePath(),"Menambah data user OPD dengan username ( $username) berhasil dilakukan.");
            $this->redirect('settings.UserOPD',true);
		}
	}
	public function editRecord ($sender,$param) {
		$this->idProcess='edit';
		$id=$this->getDataKeyField($sender,$this->RepeaterS);
        $this->createObj('DMaster');
		$result = $this->DB->getRecord("SELECT userid,username,userpassword from user WHERE userid='$id' and page='d' ");
		$this->hiddenid->Value=$id;		
		$this->txtEditNamaUserOPD->Text=$result[1]['username'];
		
	}
	public function updateData($sender,$param) {
		if ($this->Page->IsValid) {
            $id=$this->hiddenid->Value;
			$nama_user=addslashes($this->txtEditNamaUserOPD->Text);
			$str = "UPDATE user SET username='$nama_user' WHERE userid='$id' and page='d' ";
			$this->DB->updateRecord($str);
            $this->Pengguna->insertNewActivity($this->Page->getPagePath(),"Mengubah data USER OPD dengan id ($id) berhasil dilakukan.");
            $this->redirect('settings.UserOPD',true);
		}
	}
	public function deleteRecord ($sender,$param) {
		$id=$this->getDataKeyField($sender,$this->RepeaterS);
		$session_id=$this->page->Pengguna->getUserid();
       	if ($session_id===$id) {
            $this->Pengguna->insertNewActivity($this->Page->getPagePath(),"Tidak bisa menghapus USER OPD ($id) karena sedang digunakan.");
            $this->labelErrorMessage->Text="Tidak bisa menghapus USER ($id) karena sedang digunakan.";
            $this->DialogErrorMessage->Open();
        }else{
    		$this->DB->deleteRecord("user WHERE userid='$id'");
            $this->Pengguna->insertNewActivity($this->Page->getPagePath(),"Menghapus data USER ($id) berhasil dilakukan.");
    		$this->redirect('settings.UserOPD',true);
        }
	}
}