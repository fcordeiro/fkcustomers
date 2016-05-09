
<form id="configuration_form" class="defaultForm  form-horizontal" action="{$tab_1['formAction']}&origem=incRegistro" method="post" enctype="multipart/form-data">

    <div class="fkcustomers-panel" style="border-top-left-radius: 0">

        <div class="fkcustomers-panel-heading">
            {l s="Licença" mod="fkcustomers"}
        </div>

        <div class="fkcustomers-panel-header">
            <button type="button" value="1" name="btnAjuda" class="fkcustomers-button fkcustomers-float-right" onClick="window.open('http://www.fokusfirst.com/fokusfirst/ajuda/fkcustomers.pdf','Janela','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=900,height=500,left=500,top=150'); return false;">
                <i class="process-icon-help"></i>
                {l s="Ajuda" mod="fkcustomers"}
            </button>
        </div>

        <div class="fkcustomers-panel">

            <div class="fkcustomers-panel-heading">
                {l s="Registro da Licença" mod="fkcustomers"}
            </div>

            <div class="fkcustomers-form">
                <label for="fkcustomers_referencia" class="fkcustomers-label fkcustomers-col-lg-25">
                    {l s="Referência do pedido" mod="fkcustomers"}
                </label>
                <div class="fkcustomers-col-lg-25 fkcustomers-float-left">
                    <input type="text" name="fkcustomers_referencia" id="fkcustomers_referencia" value="{$tab_1['fkcustomers_referencia']}">
                </div>
            </div>

            <div class="fkcustomers-form">
                <label for="fkcustomers_dominio" class="fkcustomers-label fkcustomers-col-lg-25">
                    {l s="Domínio licenciado" mod="fkcustomers"}
                </label>
                <div class="fkcustomers-col-lg-25 fkcustomers-float-left">
                    <input disabled="disabled" type="text" name="fkcustomers_dominio" id="fkcustomers_dominio" value="{$tab_1['fkcustomers_dominio']}">
                </div>
            </div>

            <div class="fkcustomers-form">
                <label for="fkcustomers_proprietario" class="fkcustomers-label fkcustomers-col-lg-25">
                    {l s="Proprietário do domínio" mod="fkcustomers"}
                </label>
                <div class="fkcustomers-col-lg-25 fkcustomers-float-left">
                    <input type="text" name="fkcustomers_proprietario" id="fkcustomers_proprietario" value="{$tab_1['fkcustomers_proprietario']}">
                </div>
            </div>

            <div class="fkcustomers-panel-footer">
                <button type="submit" value="1" name="btnSubmit" class="fkcustomers-button fkcustomers-float-right">
                    <i class="process-icon-save"></i>
                    {l s="Salvar" mod="fkcustomers"}
                </button>
            </div>
        </div>

    </div>

</form>