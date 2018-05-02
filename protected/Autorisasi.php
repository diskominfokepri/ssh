<?php
class Autorisasi extends TModule implements IUserManager {	
    /**
	* Obj DB
	*/
	private $db;
    public function __construct () {
		$this->db = $this->Application->getModule('db');						
	}
	/**
	* @return string name for a guest user	
	*/		
	public function getGuestName () {
		return 'Guest';
	}
	/**
	* returns a user instance given the username
	* @param string username, null if it is a guest
	* @return TUser the user instance, null if the specified username is not the user database
	*
	*/
	public function getUser ($username=null) {				
		if ($username === null) {
			$user = new TUser ($this);
			$user->setIsGuest(true);
			return $user;
		}else {			
			$user = new TUser ($this);	
            $str = "SELECT userid,username,page,user.idunit,kode_unit,nama_unit,theme,photo_profile,active FROM user LEFT JOIN unit ON (unit.idunit=user.idunit) WHERE username='$username'";
			$command=$this->db->Link->createCommand($str);
			$r = $command->queryRow();         
            switch ($r['page']) {
                case 'm' :
                    $datauser['userid']=$r['userid'];
                    $datauser['username']=$r['username'];
                    $datauser['page']=$r['page'];
                    $datauser['theme']=$r['theme'];
                    $datauser['photo_profile']=$r['photo_profile'];
                    $datauser['active']=$r['active'];
                break;
                case 'd' :
                    $datauser['userid']=$r['userid'];
                    $datauser['username']=$r['username'];
                    $datauser['idunit']=$r['idunit'];
                    $datauser['kode_unit']=$r['kode_unit'];
                    $datauser['nama_unit']=$r['nama_unit'];                    
                    $datauser['page']=$r['page'];
                    $datauser['theme']=$r['theme'];
                    $datauser['photo_profile']=$r['photo_profile'];
                    $datauser['active']=$r['active'];
                break;                
            }
            $user->setIsGuest (false);
			$user->setRoles($datauser['page']);						
			$user->setName ($datauser);									
			return $user;		
		}
	}
	
	/**
	* validate if the username and password is correct
	* @param string username
	* @param string password
	* @return boolean true if validation is sucessful, false otherwise
	*
	*/
	public function validateUser ($username,$password) {		
		$str = "SELECT userpassword,page,salt FROM `user` WHERE username=? AND active=1";
		$command=$this->db->Link->createCommand($str);
		$command->bindParameter(1,$username);
		$r=$command->queryRow();
		$passwd=hash('sha256', $r['salt'] . hash('sha256', $password));		        
		if (($r['userpassword']==$passwd)) {
			return true;		
        }else{
			return false;
        }
	}	
	/**
	* Dua method dibawah ini tidak dipakai. Tetapi harus tetap Ada.
	*
	*/			
	public function saveUserToCookie($cookie) { }
	
	public function getUserFromCookie($cookie) { }
}