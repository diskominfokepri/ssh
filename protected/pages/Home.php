<?php
prado::using ('Application.MainPageF');
class Home extends MainPageF {
	public function onLoad($param) {		
		parent::onLoad($param);		            
        $this->showDashboard=true;
		if (!$this->IsPostBack&&!$this->IsCallBack) {              
            if (!isset($_SESSION['currentPageHomeF'])||$_SESSION['currentPageHomeF']['page_name']!='Home') {
                $_SESSION['currentPageHomeF']=array('page_name'=>'Home','page_num'=>0,'idunit'=>'none');												
			} 
		}                
	}  
}