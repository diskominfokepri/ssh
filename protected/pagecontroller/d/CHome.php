<?php
prado::using ('Application.MainPageD');
class CHome extends MainPageD {
	public function onLoad($param) {		
		parent::onLoad($param);		            
        $this->showDashboard=true;
		if (!$this->IsPostBack&&!$this->IsCallBack) {              
            if (!isset($_SESSION['currentPageHome'])||$_SESSION['currentPageHome']['page_name']!='d.Home') {
                $_SESSION['currentPageHome']=array('page_name'=>'d.Home','page_num'=>0,'idunit'=>'none');												
			}
		}                
	}
}