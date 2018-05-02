<?php
class MainPageF extends MainPage {
    public function OnPreInit ($param) {	
		parent::onPreInit ($param);	
		$this->MasterClass="Application.layouts.FrontendTemplate";	
        $this->Theme='default';
	}
	public function onLoad ($param) {		
		parent::onLoad($param);	       
	}
}