<?php
prado::using ('Application.MainPageD');
class CMyprofiles extends MainPageD {    
	public function onLoad($param) {		
		parent::onLoad($param);		
        $this->showProfiles=true;        
		if (!$this->IsPostBack&&!$this->IsCallBack) {	
            if (!isset($_SESSION['currentPageCache'])||$_SESSION['currentPageCache']['page_name']!='d.settings.Myprofiles') {
				$_SESSION['currentPageCache']=array('page_name'=>'d.settings.Myprofiles','page_num'=>0);												
			}
            $this->populateData();
		}
	}
    public function populateData () {
        $this->cmbTheme->DataSource=$this->setup->getListThemes();
        $this->cmbTheme->Text=$_SESSION['theme'];
        $this->cmbTheme->DataBind();
    }

    public function photoUpload($sender, $parameter)
    {
        $id                 = $this->Pengguna->getDataUser('userid');
        $ekstensiFileValid  = ['png', 'PNG', 'jpg', 'JPG', 'jpeg', 'JPEG'];
        $ekstensiFile       = explode('.', $sender->FileName);
        $ekstensiFile       = strtolower(end($ekstensiFile));

        if(!in_array($ekstensiFile, $ekstensiFileValid))
        {
            $this->Hasil->Text='Yang anda Upload bukan gambar!';
            return false;
        }

        if($sender->HasFile)
        {
            $path           = 'resources/userimages/';
            $file           = uniqid().'_'.$sender->FileName;
            $this->hiddenpath->Value=$path;
            $this->hiddenfile->Value=$file;
            $photo          = $path.$file;
            $sender->saveAs($photo,$deleteTemFile=true);

            $str            = "UPDATE user SET photo_profile='$photo' WHERE userid='$id'";
            $this->DB->updateRecord($str);
            chmod($photo, 644);
            $_SESSION['photo_profile']=$photo;
            $this->redirect('settings.Myprofiles',true);
        }
    }

    public function saveData ($sender,$param) {
        if ($this->IsValid) {
            $id=$this->Pengguna->getDataUser('userid');
            $theme = $this->cmbTheme->Text;			
            if ($this->txtPassword->Text == '') {   
				$str = "UPDATE user SET theme='$theme' WHERE userid=$id";
				$this->DB->updateRecord($str);
                $this->redirect('settings.Myprofiles',true);
			}else{				
                $data=$this->Pengguna->createHashPassword($this->txtPassword->Text);
                $salt=$data['salt'];
                $password=$data['password'];
                $str = "UPDATE user SET theme='$theme',userpassword='$password',salt='$salt' WHERE userid=$id";
				$this->DB->updateRecord($str);
            }          
            $this->redirect('settings.Myprofiles',true);
        }
    }
}