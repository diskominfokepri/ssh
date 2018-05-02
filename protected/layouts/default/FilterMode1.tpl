<div class="sidebar-category">
    <div class="category-title">
        <span>FILTER DATA</span>
        <ul class="icons-list">
            <li><a href="#" data-action="collapse"></a></li>
        </ul>
    </div>
    <div class="category-content">
        <div class="form-group">
            <label><strong>Tahun :</strong></label>
            <com:TActiveDropDownList ID="tbCmbTA" OnCallback="Page.changeTbTA" CssClass="form-control" Width="120px">
                <prop:ClientSide.OnLoading>     
                    Pace.stop();
                    Pace.start();
                    $('#<%=$this->tbCmbTA->ClientId%>').prop('disabled',true); 
                </prop:ClientSide.OnLoading>
                <prop:ClientSide.OnComplete>     
                    $('#<%=$this->tbCmbTA->ClientId%>').prop('disabled',false);
                </prop:ClientSide.OnComplete>	
            </com:TActiveDropDownList>
        </div>
    </div>
</div>