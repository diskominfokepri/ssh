<?php
prado::using ('Application.MainPageM');
class CProfiles extends MainPageM {    
	public function onLoad($param) {		
		parent::onLoad($param);		
        $this->showProfiles=true;        
		if (!$this->IsPostBack&&!$this->IsCallBack) {	
            if (!isset($_SESSION['currentPageCache'])||$_SESSION['currentPageCache']['page_name']!='m.settings.Profiles') {
				$_SESSION['currentPageCache']=array('page_name'=>'m.settings.Profiles','page_num'=>0);												
			}
            $this->populateData();
		}
	}
    public function populateData () {
        $this->cmbTheme->DataSource=$this->setup->getListThemes();
        $this->cmbTheme->Text=$_SESSION['theme'];
        $this->cmbTheme->DataBind();
    }
    public function saveData ($sender,$param) {
        if ($this->IsValid) {
            $id=$this->Pengguna->getDataUser('userid');
            $theme = $this->cmbTheme->Text;			
            if ($this->txtPassword->Text == '') {   
				$str = "UPDATE user SET theme='$theme' WHERE userid=$id";
				$command=$this->DB->createCommand($str);
				$command->execute();
			}else{				
                $data=$this->Pengguna->createHashPassword($this->txtPassword->Text);
                $salt=$data['salt'];
                $password=$data['password'];
                $str = "UPDATE user SET theme='$theme',userpassword='$password',salt='$salt' WHERE userid=$id";
				$command=$this->DB->createCommand($str);
				$command->execute();
            }          
            $this->redirect('settings.Profiles',true);
        }
    }
}