<?php
prado::using ('Application.MainPageM');
class CHome extends MainPageM {
	public function onLoad($param) {		
		parent::onLoad($param);		            
        $this->showDashboard=true;
		if (!$this->IsPostBack&&!$this->IsCallBack) {              
            if (!isset($_SESSION['currentPageHome'])||$_SESSION['currentPageHome']['page_name']!='m.Home') {
                $_SESSION['currentPageHome']=array('page_name'=>'m.Home','page_num'=>0,'idunit'=>'none');												
			}
		}                
	}
}