<!DOCTYPE html>
<html lang="id">
<com:THead>     
    <meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="<%=$this->Page->Theme->baseUrl%>/assets/css/icons/icomoon/styles.css" rel="stylesheet" type="text/css">
    <link href="<%=$this->Page->Theme->baseUrl%>/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="<%=$this->Page->Theme->baseUrl%>/assets/css/core.min.css" rel="stylesheet" type="text/css">
    <link href="<%=$this->Page->Theme->baseUrl%>/assets/css/components.min.css" rel="stylesheet" type="text/css">
	<link href="<%=$this->Page->Theme->baseUrl%>/assets/css/colors.min.css" rel="stylesheet" type="text/css">
    <link href="<%=$this->Page->Theme->baseUrl%>/assets/css/pace.css" rel="stylesheet" type="text/css">
    <link href="<%=$this->Page->Theme->baseUrl%>/assets/css/ssh.css" rel="stylesheet" type="text/css">
    <link rel="icon" type="image/png" href="<%=$this->Page->setup->getAddress()%>/resources/ikon.png"/>
    <com:TContentPlaceHolder ID="csscontent" />	
</com:THead>
<body>        
<com:TForm id="mainform" Attributes.role="form">
<com:TOutputCache>
    <com:TClientScript PradoScripts="bootstrap,effects" />
</com:TOutputCache> 
<!-- Main navbar -->
<div class="navbar navbar-inverse bg-indigo navbar-lg">
    <div class="navbar-header">
        <a class="navbar-brand" href="<%=$this->Page->constructUrl('Home')%>">
        	<img src="<%=$this->Page->setup->getAddress()%>/resources/logo.png" style="height:44px;margin-top:-10px">		
        </a>		
        <ul class="nav navbar-nav pull-right visible-xs-block">
            <li><a data-toggle="collapse" data-target="#navbar-mobile"><i class="icon-tree5"></i></a></li>
            <li><a class="sidebar-mobile-main-toggle"><i class="icon-paragraph-justify3"></i></a></li>
        </ul>
    </div>
    <div class="navbar-collapse collapse" id="navbar-mobile">   
        <p class="navbar-text">
            <com:THyperLink ID="linkTopTASemester">
                <span class="label bg-success-400">
                    Saat ini Anda berada di tahun 2018
                </span>
            </com:THyperLink>
        </p>
        <ul class="nav navbar-nav navbar-right">
            <li class="dropdown dropdown-user visible">
                <a class="dropdown-toggle" data-toggle="dropdown">
                    <img src="<%=$this->Page->setup->getAddress()%>/resources/userimages/no_photo.png" alt="<%=$this->page->Pengguna->getUsername()%>">                   
                    <span>Guest</span>
                    <i class="caret"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-right">
                    <li><a href="<%=$this->Page->constructUrl('Login')%>"><i class="icon-user-plus"></i> Login</a></li>                                       
                </ul>
            </li>
        </ul>
    </div>
</div>
<!-- /main navbar -->
<!-- Second navbar -->
<div class="navbar navbar-default" id="navbar-second">
    <ul class="nav navbar-nav no-border visible-xs-block">
        <li><a class="text-center collapsed" data-toggle="collapse" data-target="#navbar-second-toggle"><i class="icon-menu7"></i></a></li>
    </ul>
    <div class="navbar-collapse collapse" id="navbar-second-toggle">
        <ul class="nav navbar-nav">            
            <li<%=$this->Page->showDashboard==true?' class="active"':''%>>
                <a href="<%=$this->Page->constructUrl('Home')%>">
                    <i class="icon-display4 position-left"></i> 
                    <span>DASHBOARD</span>											
                </a>                                        
            </li>	            
        </ul>
    </div>
</div>
<!-- /second navbar -->
<!-- Page header -->
<div class="page-header">
    <div class="page-header-content">
        <div class="page-title">
            <h4><com:TContentPlaceHolder ID="moduleheader" /></h4>
            <ul class="breadcrumb breadcrumb-caret position-right">
                <li><a href="<%=$this->Page->constructUrl('Home',true)%>">HOME</a></li>            
                <com:TContentPlaceHolder ID="modulebreadcrumb" />
            </ul>
            <com:TContentPlaceHolder ID="modulebreadcrumbelement" />
        </div>
        <com:TContentPlaceHolder ID="moduleheaderelement" />        
    </div>
</div>
<!-- /page header -->
<div class="page-container">    
    <div class="page-content">
        <com:TContentPlaceHolder ID="sidebarcontent" />
        <div class="content-wrapper">
            
            <com:TContentPlaceHolder ID="maincontent" />
            <com:TJavascriptLogger />
        </div>        
    </div>    
</div>
<!-- Footer -->
<div class="footer text-muted">
    <%=$this->Application->getID()%> Powered by <a href="http://kiis.kepriprov.go.id">TIM Developer KIIS Diskominfo Kepulauan Riau</a>
</div>
<!-- /footer -->
</com:TForm>
<script type="text/javascript" src="<%=$this->Page->Theme->baseUrl%>/assets/js/plugins/ui/nicescroll.min.js"></script>
<script type="text/javascript" src="<%=$this->Page->Theme->baseUrl%>/assets/js/plugins/ui/drilldown.js"></script>
<script type="text/javascript" src="<%=$this->Page->Theme->baseUrl%>/assets/js/core/app.min.js"></script>
<script type="text/javascript" src="<%=$this->Page->Theme->baseUrl%>/assets/js/plugins/loaders/pace.min.js"></script>
<script type="text/javascript" src="<%=$this->Page->Theme->baseUrl%>/assets/js/ssh.js"></script>
<com:TContentPlaceHolder ID="jscontent" />
<com:TContentPlaceHolder ID="jsinlinecontent" />
</body>
</html>
