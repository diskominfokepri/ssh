<br/>
<div class="col-md-4">            
    <com:TActiveLabel ID="paginationInfo"/>        
</div>
<div class="col-md-8">        
    <com:TActivePager ID="pager" OnCallBack="Page.renderCallback" ControlToPaginate="RepeaterS" Mode="Numeric" OnPageIndexChanged="Page.Page_Changed" PrevPageText="&laquo; Previous" NextPageText="Next &raquo;" PageButtonCount="10" FirstPageText="First" LastPageText="Last" CssClass="custompaging text-right" ButtonCssClass="page">	
        <prop:ClientSide.OnPreDispatch>
            $('#<%=$this->pager->ClientId%>').prop('disabled',true);							            
            Pace.stop();
            Pace.start();                                                                                                             
        </prop:ClientSide.OnPreDispatch>                    
        <prop:ClientSide.OnLoading>
            $('#<%=$this->pager->ClientId%>').prop('disabled',true);							            
        </prop:ClientSide.OnLoading>
        <prop:ClientSide.onComplete>                                            
            $('#<%=$this->pager->ClientId%>').prop('disabled',false);									            
        </prop:ClientSide.OnComplete>
    </com:TActivePager>            
</div>    
<br/><br/>



     


