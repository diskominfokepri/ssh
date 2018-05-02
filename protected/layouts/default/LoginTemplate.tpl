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
</com:THead>
<body class="login-container">        
<com:TForm Attributes.role="form">   
<com:TOutputCache>
    <com:TClientScript PradoScripts="bootstrap,effects" />
</com:TOutputCache>    
<com:TContentPlaceHolder ID="maincontent" />
</com:TForm>
<script type="text/javascript" src="<%=$this->Page->Theme->baseUrl%>/assets/js/plugins/ui/nicescroll.min.js"></script>
<script type="text/javascript" src="<%=$this->Page->Theme->baseUrl%>/assets/js/plugins/ui/drilldown.js"></script>
<script type="text/javascript" src="<%=$this->Page->Theme->baseUrl%>/assets/js/core/app.min.js"></script>
<script type="text/javascript" src="<%=$this->Page->Theme->baseUrl%>/assets/js/plugins/loaders/pace.min.js"></script>
</body>
</html>
