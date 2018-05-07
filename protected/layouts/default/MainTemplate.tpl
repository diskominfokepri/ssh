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
        <a class="navbar-brand" href="<%=$this->Page->constructUrl('Home',true)%>"><img src="<%=$this->Page->setup->getAddress()%>/resources/logo.png" style="height:44px;margin-top:-10px"></a>
        <ul class="nav navbar-nav pull-right visible-xs-block">
            <li><a data-toggle="collapse" data-target="#navbar-mobile"><i class="icon-tree5"></i></a></li>
            <li><a class="sidebar-mobile-main-toggle"><i class="icon-paragraph-justify3"></i></a></li>
        </ul>
    </div>
    <div class="navbar-collapse collapse" id="navbar-mobile">   
        <p class="navbar-text">
            <com:THyperLink ID="linkTopTASemester">
                <span class="label bg-success-400">
                    Saat ini Anda berada di Tahun 2018
                </span>
            </com:THyperLink>
        </p>
        <ul class="nav navbar-nav navbar-right">
            <li class="dropdown dropdown-user visible">
                <a class="dropdown-toggle" data-toggle="dropdown">
                    <img src="<%=$this->Page->setup->getAddress()%>/<%=$_SESSION['photo_profile']%>" alt="<%=$this->page->Pengguna->getUsername()%>">
                    <span><%=$this->page->Pengguna->getUsername()%></span>
                    <i class="caret"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-right">
                    <li><a href="<%=$this->Page->constructUrl('settings.Myprofiles',true)%>"><i class="icon-user-plus"></i> My profile</a></li>                    
                    <li class="divider"></li>                    
                    <li>                       
                        <com:TActiveLinkButton ID="btnLogout" OnClick="logoutUser" ClientSide.PostState="false">
                            <i class="icon-switch2"></i> Logout
                            <prop:ClientSide.OnPreDispatch>
                                Pace.stop();
                                Pace.start();
                                $('#<%=$this->btnLogout->ClientId%>').prop('disabled',true);                        
                            </prop:ClientSide.OnPreDispatch>
                            <prop:ClientSide.OnLoading>
                                $('#<%=$this->btnLogout->ClientId%>').prop('disabled',true);                                                                
                            </prop:ClientSide.OnLoading>
                            <prop:ClientSide.onComplete>
                                $('#<%=$this->btnLogout->ClientId%>').prop('disabled',true);                                                                
                            </prop:ClientSide.OnComplete>
                        </com:TActiveLinkButton>
                    </li>
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
                <a href="<%=$this->Page->constructUrl('Home',true)%>">
                    <i class="icon-display4 position-left"></i> 
                    <span>DASHBOARD</span>                                          
                </a>                                        
            </li>
            <com:TLiteral Visible="<%=$this->Page->Pengguna->getTipeUser()=='m'%>">
                <li class="dropdown mega-menu mega-menu-wide<%=$this->Page->showDMaster==true?' active':''%> visible">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="icon-puzzle4 position-left"></i> DATA MASTER <span class="caret"></span>
                    </a>
                    <div class="dropdown-menu dropdown-content">
                        <div class="dropdown-content-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <span class="menu-heading underlined"><i class="icon-grid2"></i> FUNGSIONAL</span>
                                    <ul class="menu-list">
                                        <li<%=$this->Page->showSatuan==true?' class="active"':''%>>
                                            <a href="<%=$this->Page->constructUrl('dmaster.Satuan',true)%>" title="Transaksi">
                                                <i class="icon-grid2"></i> 
                                                Satuan
                                            </a>
                                        </li>
                                        <li<%=$this->Page->showUnitKerja==true?' class="active"':''%>>
                                            <a href="<%=$this->Page->constructUrl('dmaster.OPD',true)%>" title="Transaksi">
                                                <i class="icon-grid2"></i> 
                                                Organisasi Perangkat Daerah
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-3">
                                    <span class="menu-heading underlined"><i class="icon-calculator4"></i> REKENING</span>
                                    <ul class="menu-list">
                                        <li<%=$this->Page->showTransaksi==true?' class="active"':''%>>
                                            <a href="<%=$this->Page->constructUrl('dmaster.Transaksi',true)%>" title="Transaksi">
                                                <i class="icon-calculator2"></i> 
                                                Transaksi
                                            </a>
                                        </li>
                                        <li<%=$this->Page->showKelompok==true?' class="active"':''%>>
                                            <a href="<%=$this->Page->constructUrl('dmaster.Kelompok',true)%>">
                                                <i class="icon-calculator2"></i> 
                                                Kelompok
                                            </a>
                                        </li>
                                        <li<%=$this->Page->showJenis==true?' class="active"':''%>>
                                            <a href="<%=$this->Page->constructUrl('dmaster.Jenis',true)%>">
                                                <i class="icon-calculator2"></i> 
                                                Jenis
                                            </a>
                                        </li>
                                        <li<%=$this->Page->showRincian==true?' class="active"':''%>>
                                            <a href="<%=$this->Page->constructUrl('dmaster.Rincian',true)%>">
                                                <i class="icon-calculator2"></i> 
                                                Rincian
                                            </a>
                                        </li>
                                        <li<%=$this->Page->showObjek==true?' class="active"':''%>>
                                            <a href="<%=$this->Page->constructUrl('dmaster.Objek',true)%>">
                                                <i class="icon-calculator2"></i> 
                                                Objek Barang/Peralatan
                                            </a>
                                        </li> 
                                        <li<%=$this->Page->showObjekTransportasi==true?' class="active"':''%>>
                                            <a href="<%=$this->Page->constructUrl('dmaster.ObjekTransportasi',true)%>">
                                                <i class="icon-calculator2"></i> 
                                                Objek Transportasi
                                            </a>
                                        </li>              
                                    </ul>
                                </div>                            
                            </div>
                        </div>
                    </div>
                </li>
                <li class="dropdown visible<%=$this->Page->showData==true?' active':''%>">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="icon-align-left position-left"></i> DATA <span class="caret"></span>
                    </a>                
                    <ul class="dropdown-menu width-300">
                        <li class="dropdown-header">URAIAN</li>                        
                        <li<%=$this->Page->showDataUraian==true?' class="active"':''%>>
                            <a href="<%=$this->Page->constructUrl('data.Uraian',true)%>">
                                <i class="icon-usb-stick"></i> Uraian Barang/Peralatan
                            </a>
                        </li>
                        <li class="dropdown-header">USULAN</li>                       
                        <li<%=$this->Page->showCache==true?' class="active"':''%>>
                            <a href="<%=$this->Page->constructUrl('data.Usulan',true)%>">
                                <i class="icon-usb-stick"></i> Usulan Barang/Peralatan
                            </a>
                        </li>                                            
                    </ul>
                </li>
                <li class="dropdown visible<%=$this->Page->showReport==true?' active':''%>">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="icon-file-empty position-left"></i> REPORT <span class="caret"></span>
                    </a>                
                    <ul class="dropdown-menu width-300">
                        <li<%=$this->Page->showReportDataUraian==true?' class="active"':''%>>
                            <a href="<%=$this->Page->constructUrl('report.ReportDataUraian',true)%>">
                                <i class="icon-file-text3"></i> Uraian Barang/Peralatan
                            </a>
                        </li>                                            
                    </ul>
                </li>
                <li class="dropdown visible<%=$this->Page->showSetting==true?' active':''%>">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="icon-cogs position-left"></i> SETTING <span class="caret"></span>
                    </a>                
                    <ul class="dropdown-menu width-300">
                        <li class="dropdown-header">USER</li>
                        <li<%=$this->Page->showUserBiroOrtal==true?' class="active"':''%>>
                            <a href="<%=$this->Page->constructUrl('settings.User',true)%>">
                                <i class="icon-user"></i> BIRO ORTAL
                            </a>
                        </li>                    
                        <li<%=$this->Page->showUserOPD==true?' class="active"':''%>>
                            <a href="<%=$this->Page->constructUrl('settings.UserOPD',true)%>">
                                <i class="icon-users"></i> Organisasi Perangkat Daerah
                            </a>
                        </li>    
                        <li class="dropdown-header">Sistem</li>                       
                        <li<%=$this->Page->showCache==true?' class="active"':''%>>
                            <a href="<%=$this->Page->constructUrl('settings.Cache',true)%>">
                                <i class="icon-database2"></i> Cache
                            </a>
                        </li>
                    </ul>
                </li>
            </com:TLiteral>
            <com:TLiteral Visible="<%=$this->Page->Pengguna->getTipeUser()=='d'%>">
                <li class="dropdown mega-menu mega-menu-wide<%=$this->Page->showDMaster==true?' active':''%> visible">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="icon-puzzle4 position-left"></i> DATA MASTER <span class="caret"></span>
                    </a>
                    <div class="dropdown-menu dropdown-content">
                        <div class="dropdown-content-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <span class="menu-heading underlined"><i class="icon-grid2"></i> FUNGSIONAL</span>
                                    <ul class="menu-list">
                                        <li<%=$this->Page->showSatuan==true?' class="active"':''%>>
                                            <a href="<%=$this->Page->constructUrl('dmaster.Satuan',true)%>" title="Transaksi">
                                                <i class="icon-grid2"></i> 
                                                Satuan
                                            </a>
                                        </li>                                        
                                    </ul>
                                </div>
                                <div class="col-md-3">
                                    <span class="menu-heading underlined"><i class="icon-calculator4"></i> REKENING</span>
                                    <ul class="menu-list">
                                        <li<%=$this->Page->showTransaksi==true?' class="active"':''%>>
                                            <a href="<%=$this->Page->constructUrl('dmaster.Transaksi',true)%>" title="Transaksi">
                                                <i class="icon-calculator2"></i> 
                                                Transaksi
                                            </a>
                                        </li>
                                        <li<%=$this->Page->showKelompok==true?' class="active"':''%>>
                                            <a href="<%=$this->Page->constructUrl('dmaster.Kelompok',true)%>">
                                                <i class="icon-calculator2"></i> 
                                                Kelompok
                                            </a>
                                        </li>
                                        <li<%=$this->Page->showJenis==true?' class="active"':''%>>
                                            <a href="<%=$this->Page->constructUrl('dmaster.Jenis',true)%>">
                                                <i class="icon-calculator2"></i> 
                                                Jenis
                                            </a>
                                        </li>
                                        <li<%=$this->Page->showRincian==true?' class="active"':''%>>
                                            <a href="<%=$this->Page->constructUrl('dmaster.Rincian',true)%>">
                                                <i class="icon-calculator2"></i> 
                                                Rincian
                                            </a>
                                        </li>
                                        <li<%=$this->Page->showObjek==true?' class="active"':''%>>
                                            <a href="<%=$this->Page->constructUrl('dmaster.Objek',true)%>">
                                                <i class="icon-calculator2"></i> 
                                                Objek Barang/Peralatan
                                            </a>
                                        </li> 
                                        <li<%=$this->Page->showObjekTransportasi==true?' class="active"':''%>>
                                            <a href="<%=$this->Page->constructUrl('dmaster.ObjekTransportasi',true)%>">
                                                <i class="icon-calculator2"></i> 
                                                Objek Transportasi
                                            </a>
                                        </li>              
                                    </ul>
                                </div>                            
                            </div>
                        </div>
                    </div>
                </li>
                <li class="dropdown visible<%=$this->Page->showData==true?' active':''%>">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="icon-align-left position-left"></i> DATA <span class="caret"></span>
                    </a>                
                    <ul class="dropdown-menu width-300">
                        <li class="dropdown-header">URAIAN</li>                        
                        <li<%=$this->Page->showDataUraian==true?' class="active"':''%>>
                            <a href="<%=$this->Page->constructUrl('data.Uraian',true)%>">
                                <i class="icon-usb-stick"></i> Uraian Barang/Peralatan
                            </a>
                        </li>
                        <li class="dropdown-header">USULAN</li> 
                        <li<%=$this->Page->showDataUsulan==true?' class="active"':''%>>
                            <a href="<%=$this->Page->constructUrl('data.Usulan',true)%>">
                                <i class="icon-usb-stick"></i> Usulan Barang/Peralatan
                            </a>
                        </li>

                    </ul>
                </li>
                <li class="dropdown visible<%=$this->Page->showReport==true?' active':''%>">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="icon-file-empty position-left"></i> REPORT <span class="caret"></span>
                    </a>                
                    <ul class="dropdown-menu width-300">
                        <li<%=$this->Page->showReportDataUraian==true?' class="active"':''%>>
                            <a href="<%=$this->Page->constructUrl('report.ReportDataUraian',true)%>">
                                <i class="icon-file-text3"></i> Uraian Barang/Peralatan
                            </a>
                        </li>                                            
                    </ul>
                </li>
            </com:TLiteral>
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