<?php
class Login extends MainPage {
    public function OnPreInit ($param) {	
		parent::onPreInit ($param);	
        $theme='default';
		$this->MasterClass="Application.layouts.$theme.LoginTemplate";	
        $this->Theme=$theme;
	}
    public function onLoad ($param) {		
		parent::onLoad($param);
        $this->loggerJS->Visible=true;
    }
    public function checkUsernameAndPassword($sender,$param) {
        $username=$param->Value;
        if ($username != '') {
            try {
                $auth = $this->Application->getModule ('auth');
                $userpassword=addslashes(trim($this->txtPassword->Text));
                if (!$auth->login ($username,$userpassword)){			                    
                    throw new Exception ('Username atau password salah!.Silahkan ulangi kembali');						
                }
            } catch (Exception $ex) {
                $message='<div class="alert alert-danger">
                    <strong>Error!</strong>
                    '.$ex->getMessage().'</div>';
                $sender->ErrorMessage=$message;					
                $param->IsValid=false;		
            }
        }
    }   
    public function doLogin ($sender,$param) {
        if ($this->IsValid) {   
            $pengguna=$this->getLogic('Users');
            $_SESSION['theme']=$pengguna->getDataUser('theme');
            $_SESSION['photo_profile']=$pengguna->getDataUser('photo_profile');  
            $_SESSION['ta']=$this->setup->getSettingValue('default_ta');           
            $_SESSION['outputreport']='excel2007';
            switch ($pengguna->getRoles()) { 
                case 'admin' :
                    $pengguna->insertNewActivity('login',"melakukan login sebagai admin dan berhasil masuk ke dalam sistem.");
                break;
                case 'skpd' :
                    $pengguna->insertNewActivity('login',"melakukan login sebagai OPD dan berhasil masuk ke dalam sistem.");
                break;
            }
            $this->redirect('Home',true);
        }
    }
}