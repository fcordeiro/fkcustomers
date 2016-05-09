<?php

class fkcustomers extends Module {

    private $_html = '';
    private $_postErrors = array();
    private $_tab_select = '';

    private $_urlWs = 'http://www.fokusfirst.com/fokusfirst/loja/modules/fkcontrol/fkservices.wsdl';
    private $_erroWs = '';
    private $_idProduto = '53';

    public function __construct() {

        $this->name = 'fkcustomers';
        $this->tab = 'Others';
        $this->version = '161.5.0';
        $this->author = 'FokusFirst';

        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this -> l('FKcustomers');
        $this->description = $this -> l('Inclui novos campos e automatiza o preenchimento do cadastro de clientes.');

        // URL/URI que variam conforme endereco do dominio
        Configuration::updateValue('FKCUSTOMERS_URL_IMG', Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/img/');
    }

    public function install() {
        
        $modo_operacao = '1';
        
        if (!parent::install()
            Or !$this->registerHook('displayHeader')
            Or !$this->registerHook('displayBackOfficeHeader')
            Or !$this->registerHook('DisplayOverrideTemplate')
            Or !$this->registerHook('actionCustomerAccountAdd')
            Or !$this->registerHook('displayCustomerAccountFormTop')
            Or !$this->alteraTabela()
            Or !$this->alteraFormatoEndereco($modo_operacao)
            Or !Configuration::updateValue('FKCUSTOMERS_REFERENCIA', 'Registro liberado')
            Or !Configuration::updateValue('FKCUSTOMERS_DOMINIO', '')
            Or !Configuration::updateValue('FKCUSTOMERS_PROPRIETARIO', 'Registro liberado')
            Or !Configuration::updateValue('FKCUSTOMERS_MODO', $modo_operacao)
            Or !Configuration::updateValue('FKCUSTOMERS_WS', 'CO')
            Or !Configuration::updateValue('FKCUSTOMERS_USUARIOBY', '')
            Or !Configuration::updateValue('FKCUSTOMERS_SENHABY', '')
            Or !Configuration::updateValue('FKCUSTOMERS_CODIGOAC', '')
            Or !Configuration::updateValue('FKCUSTOMERS_CHAVEAC', '')
            Or !Configuration::updateValue('FKCUSTOMERS_GRUPO', '3')
            Or !Configuration::updateValue('FKCUSTOMERS_DDD', '|11|12|13|14|15|16|17|18|19|21|22|24|27|28|31|32|33|34|35|37|38|41|42|43|44|45|46|47|48|49|51|53|54|55|61|62|63|64|65|66|67|68|69|71|73|74|75|77|79|81|82|83|84|85|86|87|88|89|91|92|93|94|95|96|97|98|99|')
            Or !Configuration::updateValue('FKCUSTOMERS_DELCAMPOS', '')
            Or !Configuration::updateValue('FKCUSTOMERS_RG_REQ', '')
            Or !Configuration::updateValue('FKCUSTOMERS_IE_REQ', '')
            Or !Configuration::updateValue('FKCUSTOMERS_DUPL_CPF_CNPJ', '')) {
            return false;
        }

        return true;
    }

    public function uninstall() {

        if (!parent::uninstall()
            Or !$this->unregisterHook('displayHeader')
            Or !$this->unregisterHook('displayBackOfficeHeader')
            Or !$this->unregisterHook('DisplayOverrideTemplate')
            Or !$this->unregisterHook('actionCustomerAccountAdd')
            Or !$this->unregisterHook('displayCustomerAccountFormTop')) {
            return false;
        }

        // Exclui dados de Configuração
        if (!Db::getInstance()->delete("configuration", "name LIKE 'FKCUSTOMERS_%'")) {
            return false;
        }

        if (Configuration::get('FKCUSTOMERS_DELCAMPOS') == 'on'){

            $Query =   "ALTER TABLE "._DB_PREFIX_."customer DROP COLUMN tipo;";
            if (!Db::getInstance()->Execute($Query)) {
                return false;
            }

            $Query =   "ALTER TABLE "._DB_PREFIX_."customer DROP COLUMN cpf_cnpj;";
            if (!Db::getInstance()->Execute($Query)) {
                return false;
            }

            $Query =   "ALTER TABLE "._DB_PREFIX_."customer DROP COLUMN rg_ie;";
            if (!Db::getInstance()->Execute($Query)) {
                return false;
            }

            $Query =   "ALTER TABLE "._DB_PREFIX_."address DROP COLUMN numend;";
            if (!Db::getInstance()->Execute($Query)) {
                return false;
            }

            $Query =   "ALTER TABLE "._DB_PREFIX_. "address DROP COLUMN compl;";
            if (!Db::getInstance()->Execute($Query)) {
                return false;
            }

        }

        return true;
    }
    
    public function hookdisplayHeader($params) {
        // JS
        $this->context->controller->addJS(_PS_JS_DIR_.'jquery/plugins/fancybox/jquery.fancybox.js');
        
        // Adiciona Fancybox caso QuickView esteja desativado
        if (!Configuration::get('PS_QUICK_VIEW')) {
            $this->context->controller->addjqueryPlugin('fancybox');
        }    
    }

    public function hookDisplayBackOfficeHeader() {
        // CSS
        $this->context->controller->addCSS($this->_path.'css/fkcustomers_admin_16x.css');

        // JS
        $this->context->controller->addJS($this->_path.'js/fkcustomers_config.js');
    }

    // TODO: alterar função quando mudar versão do Prestashop
    public function hookDisplayOverrideTemplate($params) {

        if (Configuration::get('FKCUSTOMERS_MODO') == '1') {

            $controllerName = get_class($params['controller']);
            $tpl ='';

            switch ($controllerName) {

                case 'AuthController':

                    // Verifica se existe tpl customizado
                    $custom = _PS_MODULE_DIR_.'fkcustomers/views/custom/authentication.tpl';

                    if (file_exists($custom)) {
                        $tpl = $custom;
                    }else {
                        // Seleciona tpl do fkcustomers conforme versao
                        if (version_compare(_PS_VERSION_, '1.6.0.5', '==')) {
                            $tpl = _PS_MODULE_DIR_.'fkcustomers/views/front/v160_0_5/authentication.tpl';
                        }elseif (version_compare(_PS_VERSION_, '1.6.0.6', '==')) {
                            $tpl = _PS_MODULE_DIR_.'fkcustomers/views/front/v160_0_6/authentication.tpl';
                        }elseif (version_compare(_PS_VERSION_, '1.6.0.7', '==') or version_compare(_PS_VERSION_, '1.6.0.8', '==')) {
                            $tpl = _PS_MODULE_DIR_.'fkcustomers/views/front/v160_0_7/authentication.tpl';
                        }elseif (version_compare(_PS_VERSION_, '1.6.0.9', '==')) {
                            $tpl = _PS_MODULE_DIR_.'fkcustomers/views/front/v160_0_9/authentication.tpl';
                        }elseif (version_compare(_PS_VERSION_, '1.6.0.11', '==')) {
                            $tpl = _PS_MODULE_DIR_.'fkcustomers/views/front/v160_0_11/authentication.tpl';
                        }elseif (version_compare(_PS_VERSION_, '1.6.0.13', '==') or version_compare(_PS_VERSION_, '1.6.0.14', '==')) {
                            $tpl = _PS_MODULE_DIR_.'fkcustomers/views/front/v160_0_13/authentication.tpl';
                        }else {
                            $tpl = _PS_MODULE_DIR_.'fkcustomers/views/front/v161_0/authentication.tpl';
                        }
                    }

                    break;

                case 'AddressController':

                    // Verifica se existe tpl customizado
                    $custom = _PS_MODULE_DIR_.'fkcustomers/views/custom/address.tpl';

                    if (file_exists($custom)) {
                        $tpl = $custom;
                    }else {
                        // Seleciona tpl do fkcustomers conforme versao
                        if (version_compare(_PS_VERSION_, '1.6.0.5', '==')) {
                            $tpl = _PS_MODULE_DIR_ . 'fkcustomers/views/front/v160_0_5/address.tpl';
                        } elseif (version_compare(_PS_VERSION_, '1.6.0.6', '==')) {
                            $tpl = _PS_MODULE_DIR_ . 'fkcustomers/views/front/v160_0_6/address.tpl';
                        } elseif (version_compare(_PS_VERSION_, '1.6.0.7', '==') or version_compare(_PS_VERSION_, '1.6.0.8', '==')) {
                            $tpl = _PS_MODULE_DIR_ . 'fkcustomers/views/front/v160_0_7/address.tpl';
                        }elseif (version_compare(_PS_VERSION_, '1.6.0.9', '==')) {
                            $tpl = _PS_MODULE_DIR_.'fkcustomers/views/front/v160_0_9/address.tpl';
                        }elseif (version_compare(_PS_VERSION_, '1.6.0.11', '==')) {
                            $tpl = _PS_MODULE_DIR_.'fkcustomers/views/front/v160_0_11/address.tpl';
                        }elseif (version_compare(_PS_VERSION_, '1.6.0.13', '==') or version_compare(_PS_VERSION_, '1.6.0.14', '==')) {
                            $tpl = _PS_MODULE_DIR_.'fkcustomers/views/front/v160_0_13/address.tpl';
                        }else {
                            $tpl = _PS_MODULE_DIR_.'fkcustomers/views/front/v161_0/address.tpl';
                        }
                    }

                    break;

                case 'IdentityController':

                    // Verifica se existe tpl customizado
                    $custom = _PS_MODULE_DIR_.'fkcustomers/views/custom/identity.tpl';

                    if (file_exists($custom)) {
                        $tpl = $custom;
                    }else {
                        // Seleciona tpl do fkcustomers conforme versao
                        if (version_compare(_PS_VERSION_, '1.6.0.5', '==')) {
                            $tpl = _PS_MODULE_DIR_ . 'fkcustomers/views/front/v160_0_5/identity.tpl';
                        } elseif (version_compare(_PS_VERSION_, '1.6.0.6', '==')) {
                            $tpl = _PS_MODULE_DIR_ . 'fkcustomers/views/front/v160_0_6/identity.tpl';
                        } elseif (version_compare(_PS_VERSION_, '1.6.0.7', '==') or version_compare(_PS_VERSION_, '1.6.0.8', '==')) {
                            $tpl = _PS_MODULE_DIR_ . 'fkcustomers/views/front/v160_0_7/identity.tpl';
                        }elseif (version_compare(_PS_VERSION_, '1.6.0.9', '==')) {
                            $tpl = _PS_MODULE_DIR_.'fkcustomers/views/front/v160_0_9/identity.tpl';
                        }elseif (version_compare(_PS_VERSION_, '1.6.0.11', '==')) {
                            $tpl = _PS_MODULE_DIR_.'fkcustomers/views/front/v160_0_11/identity.tpl';
                        }elseif (version_compare(_PS_VERSION_, '1.6.0.13', '==') or version_compare(_PS_VERSION_, '1.6.0.14', '==')) {
                            $tpl = _PS_MODULE_DIR_.'fkcustomers/views/front/v160_0_13/identity.tpl';
                        }else {
                            $tpl = _PS_MODULE_DIR_.'fkcustomers/views/front/v161_0/identity.tpl';
                        }
                    }

                    break;

                case 'OrderOpcController':

                    // Verifica se é o template a ser alterado visto que neste Controller existem retorno de vários templates
                    if (strpos($params['default_template'], 'order-opc.tpl') === false) {
                        return false;
                    }

                    // Verifica se existe tpl customizado
                    $custom = _PS_MODULE_DIR_.'fkcustomers/views/custom/order-opc.tpl';

                    if (file_exists($custom)) {
                        $tpl = $custom;
                    }else {
                        // Seleciona tpl do fkcustomers conforme versao
                        if (version_compare(_PS_VERSION_, '1.6.0.5', '==')) {
                            $tpl = _PS_MODULE_DIR_ . 'fkcustomers/views/front/v160_0_5/order-opc.tpl';
                        } elseif (version_compare(_PS_VERSION_, '1.6.0.6', '==')) {
                            $tpl = _PS_MODULE_DIR_ . 'fkcustomers/views/front/v160_0_6/order-opc.tpl';
                        } elseif (version_compare(_PS_VERSION_, '1.6.0.7', '==') or version_compare(_PS_VERSION_, '1.6.0.8', '==')) {
                            $tpl = _PS_MODULE_DIR_ . 'fkcustomers/views/front/v160_0_7/order-opc.tpl';
                        }elseif (version_compare(_PS_VERSION_, '1.6.0.9', '==')) {
                            $tpl = _PS_MODULE_DIR_.'fkcustomers/views/front/v160_0_9/order-opc.tpl';
                        }elseif (version_compare(_PS_VERSION_, '1.6.0.11', '==')) {
                            $tpl = _PS_MODULE_DIR_.'fkcustomers/views/front/v160_0_11/order-opc.tpl';
                        }elseif (version_compare(_PS_VERSION_, '1.6.0.13', '==') or version_compare(_PS_VERSION_, '1.6.0.14', '==')) {
                            $tpl = _PS_MODULE_DIR_.'fkcustomers/views/front/v160_0_13/order-opc.tpl';
                        }else {
                            $tpl = _PS_MODULE_DIR_.'fkcustomers/views/front/v161_0/order-opc.tpl';
                        }
                    }

                    break;

            }

            if (file_exists($tpl)) {
                return $tpl;
            }

        }

        return false;
    }

    public function hookdisplayCustomerAccountFormTop($params) {
        // Carrega tpl
        return $this->display(__FILE__, 'views/front/cpf_cnpj.tpl');
    }

    public function hookactionCustomerAccountAdd($params) {

        $cliente = $params['newCustomer'];

        if (Configuration::get('FKCUSTOMERS_GRUPO') != '0' AND $cliente->tipo == 'pj') {
            $customer = new Customer($cliente->id);
            $customer->cleanGroups();
            $customer->addGroups(array(Configuration::get('FKCUSTOMERS_GRUPO')));
        }
    }

    public function getContent() {

        if (Tools::isSubmit('btnSubmit')) {

            $this->postValidation();

            if (!sizeof($this ->_postErrors)) {
                $this->_html .= $this->displayConfirmation($this->l('Configuração alterada'));
            }else {
                foreach ($this->_postErrors AS $err) {
                    $this->_html .= $this->displayError($err);
                }
            }
        }

        $this->_html .= $this->renderForm();

        return $this->_html;

    }

    public function renderForm() {

        // Inicializa variaveis
        $msg_err = '';

        if (Configuration::get('FKCUSTOMERS_REFERENCIA') != '' and Configuration::get('FKCUSTOMERS_DOMINIO') != '' and Configuration::get('FKCUSTOMERS_PROPRIETARIO') != '') {

            if (!$this->wsVerificaLicenca(Configuration::get('FKCUSTOMERS_REFERENCIA'), Tools::getShopDomain(false, true))) {
                // Erro com registro do modulo
                $msg_err .= $this->displayError($this->_erroWs);

                $this->_tab_select = '1';
                $this->incRegistro();

            }else {
                // Modulo registrado
                $this->altRegistro();
                $this->procConfigGeral();
            }

        }else {
            // Modulo ainda nao registrado
            $this->_tab_select = '3';
            $this->incRegistro();
        }

        // Informacoes da configuracao
        $this->infConfiguracao();

        $this->smarty->assign(array(
            'pathInclude'   => _PS_MODULE_DIR_.$this->name.'/views/config/',
            'msgErr'        => $msg_err,
            'tabSelect'     => $this->_tab_select,
        ));

        return $this->display(__FILE__, 'views/config/mainConfig_16x.tpl');
    }

    private function incRegistro() {

        // TPL a ser utilizado
        $name_tpl ='incRegistro_16x.tpl';

        $this->smarty->assign(array(
            'tab_1' => array(
                'nameTpl'                   => $name_tpl,
                'formAction'                => Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI']),
                'fkcustomers_referencia'     => Tools::getValue('fkcustomers_referencia', Configuration::get('FKCUSTOMERS_REFERENCIA')),
                'fkcustomers_dominio'        => Tools::getValue('fkcustomers_dominio', Trim(Tools::getShopDomain(false,true))),
                'fkcustomers_proprietario'   => Tools::getValue('fkcustomers_proprietario', Configuration::get('FKCUSTOMERS_PROPRIETARIO')),
            )

        ));
    }

    private function altRegistro() {

        // TPL a ser utilizado
        $name_tpl ='altRegistro_16x.tpl';

        $this->smarty->assign(array(
            'tab_1' => array(
                'nameTpl'                   => $name_tpl,
                'formAction'                => Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI']),
                'fkcustomers_referencia'     => Tools::getValue('fkcustomers_referencia', Configuration::get('FKCUSTOMERS_REFERENCIA')),
                'fkcustomers_dominio'        => Tools::getValue('fkcustomers_dominio', Trim(Tools::getShopDomain(false,true))),
                'fkcustomers_proprietario'   => Tools::getValue('fkcustomers_proprietario', Configuration::get('FKCUSTOMERS_PROPRIETARIO')),
            )

        ));
    }

    private function procConfigGeral() {

        // TPL a ser utilizado
        $name_tpl ='configGeral_16x.tpl';

        // Recupera usuario e senha
        $usuario = '';
        $senha = '';

        if (Configuration::get('FKCUSTOMERS_WS') == 'BY') {
            $usuario = Configuration::get('FKCUSTOMERS_USUARIOBY');
            $senha = Configuration::get('FKCUSTOMERS_SENHABY');
        }else {
            if (Configuration::get('FKCUSTOMERS_WS') == 'AC') {
                $usuario = Configuration::get('FKCUSTOMERS_CODIGOAC');
                $senha = Configuration::get('FKCUSTOMERS_CHAVEAC');
            }
        }

        // Recupera grupo de clientes
        $group = new Group();
        $grupo_clientes = $group->getGroups($this->context->language->id);

        $this->smarty->assign(array(
            'tab_2' => array(
                'nameTpl'                   => $name_tpl,
                'formAction'                => Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI']),
                'fkcustomers_modo'          => Configuration::get('FKCUSTOMERS_MODO'),
                'fkcustomers_ws'            => Configuration::get('FKCUSTOMERS_WS'),
                'usuario' 		            => $usuario,
                'senha' 			        => $senha,
                'grupo_clientes'            => $grupo_clientes,
                'fkcustomers_grupo'         => Configuration::get('FKCUSTOMERS_GRUPO'),
                'fkcustomers_ddd'           => Tools::getValue('fkcustomers_ddd', Configuration::get('FKCUSTOMERS_DDD')),
                'fkcustomers_dupl_cpf_cnpj' => Tools::getValue('fkcustomers_dupl_cpf_cnpj', Configuration::get('FKCUSTOMERS_DUPL_CPF_CNPJ')),
                'fkcustomers_rg_req'        => Tools::getValue('fkcustomers_rg_req', Configuration::get('FKCUSTOMERS_RG_REQ')),
                'fkcustomers_ie_req'        => Tools::getValue('fkcustomers_ie_req', Configuration::get('FKCUSTOMERS_IE_REQ')),
                'fkcustomers_delcampos'     => Tools::getValue('fkcustomers_delcampos', Configuration::get('FKCUSTOMERS_DELCAMPOS')),
            )
        ));
    }

    private function infConfiguracao() {

        // TPL a ser utilizado
        $name_tpl ='infConfiguracao.tpl';

        // Verifica SOAP
        $soap = true;
        $msgSoap = 'Habilite o SOAP em seu PHP';

        if (!extension_loaded('soap')) {
            $soap = false;
        }

        // Verifica fopen
        $fopen = true;
        $msgFopen = 'Habilite o fopen em seu PHP';

        if (ini_get('allow_url_fopen') != '1') {
            $fopen = false;
        }

        // Verifica output_buffering
        $outBuffering = true;
        $msgOutBuffering = 'Habilite o output_buffering em seu PHP (output_buffering=on ou output_buffering=4096)';

        if (ini_get('output_buffering') != 1 and ini_get('output_buffering') != '4096') {
            $outBuffering = false;
        }

        // Verifica Modulos Nativos
        $modulosNativos = true;
        $msgModulosNativos = 'A execução de Módulos não Nativos está desabilitada. Habilite a execução de Módulos não Nativos.';

        if (Configuration::get('PS_DISABLE_NON_NATIVE_MODULE') == '1') {
            $modulosNativos = false;
        }

        // Verifica Overrides
        $overrides = true;
        $msgOverrides = 'A execução de Overrides está desabilitada. Habilite a execução de Overrides.';

        if (Configuration::get('PS_DISABLE_OVERRIDES') == '1') {
            $overrides = false;
        }


        $this->smarty->assign(array(
            'tab_3' => array(
                'nameTpl'                   => $name_tpl,
                'urlImg'                    => Configuration::get('FKCUSTOMERS_URL_IMG'),
                'soap'                      => $soap,
                'msgSoap'                   => $msgSoap,
                'fopen'                     => $fopen,
                'msgFopen'                  => $msgFopen,
                'outBuffering'              => $outBuffering,
                'msgOutBuffering'           => $msgOutBuffering,
                'modulosNativos'            => $modulosNativos,
                'msgModulosNativos'         => $msgModulosNativos,
                'overrides'                 => $overrides,
                'msgOverrides'              => $msgOverrides,
            )
        ));
    }
    
    
    

    private function postValidation() {

        $sessao = Tools::getValue('origem');

        switch($sessao) {

            case 'incRegistro':

                // Recupera valores
                $referencia = Trim(Tools::getValue('fkcustomers_referencia'));
                $dominio = Trim(Tools::getShopDomain(false, true));
                $proprietario = Trim(Tools::getValue('fkcustomers_proprietario'));

                if ($referencia == '' or $dominio == '' or $proprietario == '') {
                    $this->_postErrors[] = $this->l('Todos os campos são necessários para Registro da Licença.');
                    break;
                }

                if (!$this->wsRegistraLicenca($referencia, $dominio, $proprietario)) {
                    $this->_postErrors[] = $this->_erroWs;
                }

                if (!$this->_postErrors) {
                    $this->postProcess($sessao);
                }

                break;

            case 'altRegistro':

                if (!$this->wsAlteraLicenca(Configuration::get('FKCUSTOMERS_REFERENCIA'), Tools::getShopDomain(false,true))) {
                    $this->_postErrors[] = $this->_erroWs;
                }

                if (!$this->_postErrors) {
                    $this->postProcess($sessao);
                }

                break;

            case 'configGeral':
                // Posicionamento da tab
                $this->_tab_select = '2';

                // Verifica os valores dos campos
                if (Tools::getValue('fkcustomers_ws') == 'BY') {
                    if (trim(Tools::getValue('fkcustomers_usuarioby')) == ''  or trim(Tools::getValue('fkcustomers_senhaby')) == '') {
                        $this->_postErrors[] = $this->l('Usuário e senha são obrigatórios para o serviço BYJG');
                    }
                }else {
                    if (Tools::getValue('fkcustomers_ws') == 'AC') {
                        if (trim(Tools::getValue('fkcustomers_codigoac')) == '' or trim(Tools::getValue('fkcustomers_chaveac')) == '') {
                            $this->_postErrors[] = $this->l('Código e chave são obrigatórios para o serviço AutoCep');
                        }
                    }
                }

                if (!$this->_postErrors) {
                    $this->postProcess($sessao);
                }

                break;

        }
    }

    private function postProcess($sessao) {

        switch($sessao) {

            case 'incRegistro':

                // Salva as configurações
                Configuration::updateValue('FKCUSTOMERS_REFERENCIA', Trim(Tools::getValue('fkcustomers_referencia')));
                Configuration::updateValue('FKCUSTOMERS_DOMINIO', Trim(Tools::getShopDomain(false,true)));
                Configuration::updateValue('FKCUSTOMERS_PROPRIETARIO', Trim(Tools::getValue('fkcustomers_proprietario')));

                break;

            case 'altRegistro':

                // Limpa as configurações
                Configuration::updateValue('FKCUSTOMERS_REFERENCIA', '');
                Configuration::updateValue('FKCUSTOMERS_DOMINIO', '');
                Configuration::updateValue('FKCUSTOMERS_PROPRIETARIO', '');

                break;

            case 'configGeral':

                $modo = Tools::getValue('fkcustomers_modo');

                // Altera formato do endereco
                $this->alteraFormatoEndereco($modo);

                Configuration::updateValue('FKCUSTOMERS_MODO', $modo);
                Configuration::updateValue('FKCUSTOMERS_WS', Tools::getValue('fkcustomers_ws'));
                Configuration::updateValue('FKCUSTOMERS_USUARIOBY', Tools::getValue('fkcustomers_usuarioby'));
                Configuration::updateValue('FKCUSTOMERS_SENHABY', Tools::getValue('fkcustomers_senhaby'));
                Configuration::updateValue('FKCUSTOMERS_CODIGOAC', Tools::getValue('fkcustomers_codigoac'));
                Configuration::updateValue('FKCUSTOMERS_CHAVEAC', Tools::getValue('fkcustomers_chaveac'));
                Configuration::updateValue('FKCUSTOMERS_GRUPO', Tools::getValue('fkcustomers_grupo'));
                Configuration::updateValue('FKCUSTOMERS_DUPL_CPF_CNPJ', Trim(Tools::getValue('fkcustomers_dupl_cpf_cnpj')));
                Configuration::updateValue('FKCUSTOMERS_DDD', Trim(Tools::getValue('fkcustomers_ddd')));
                Configuration::updateValue('FKCUSTOMERS_RG_REQ', Trim(Tools::getValue('fkcustomers_rg_req')));
                Configuration::updateValue('FKCUSTOMERS_IE_REQ', Trim(Tools::getValue('fkcustomers_ie_req')));
                Configuration::updateValue('FKCUSTOMERS_DELCAMPOS', Trim(Tools::getValue('fkcustomers_delcampos')));

                break;

        }
    }
    
    private function alteraTabela() {

        $db = Db::getInstance();

        $sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '"._DB_PREFIX_."customer' AND column_name = 'tipo' AND table_schema = '"._DB_NAME_."'";
        $dados = $db->getRow($sql);
        if (!$dados) {
            $sql =   "ALTER TABLE "._DB_PREFIX_."customer ADD tipo varchar(2) DEFAULT ' ';";
            $db-> Execute($sql);
        }

        $sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '"._DB_PREFIX_."customer' AND column_name = 'cpf_cnpj' AND table_schema = '"._DB_NAME_."'";
        $dados = $db->getRow($sql);
        if (!$dados) {
            $sql =   "ALTER TABLE "._DB_PREFIX_."customer ADD cpf_cnpj varchar(20) DEFAULT ' ';";
            $db-> Execute($sql);
        }

        $sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '"._DB_PREFIX_."customer' AND column_name = 'rg_ie' AND table_schema = '"._DB_NAME_."'";
        $dados = $db->getRow($sql);
        if (!$dados) {
            $sql =   "ALTER TABLE "._DB_PREFIX_."customer ADD rg_ie varchar(20) DEFAULT ' ';";
            $db-> Execute($sql);
        }

        $sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '"._DB_PREFIX_."address' AND column_name = 'numend' AND table_schema = '"._DB_NAME_."'";
        $dados = $db->getRow($sql);
        if (!$dados) {
            $sql =   "ALTER TABLE`" . _DB_PREFIX_ . "address` ADD `numend` varchar(20) DEFAULT ' ';";
            $db-> Execute($sql);
        }

        $sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '"._DB_PREFIX_."address' AND column_name = 'compl' AND table_schema = '"._DB_NAME_."'";
        $dados = $db->getRow($sql);
        if (!$dados) {
            $sql =   "ALTER TABLE`" . _DB_PREFIX_ . "address` ADD `compl` varchar(20) DEFAULT ' ';";
            $db-> Execute($sql);
        }

        return true;
    }

    private function alteraFormatoEndereco($modo) {

        // Recupera id_country do Brasil
        $dados = Db::getInstance()->getRow('SELECT id_country FROM `'._DB_PREFIX_.'country` WHERE `iso_code` = "br" Or `iso_code` = "BR"');
        $id_country = $dados['id_country'];

        // Altera o formato do endereço
        if ($modo == '1') {
            $formato = array('format' => 'firstname lastname'.chr(10).'company'.chr(10).'postcode'.chr(10).'address1'.chr(10).'numend'.chr(10).'compl'.chr(10).'address2'.chr(10).'city'.chr(10).'State:name'.chr(10).'Country:name'.chr(10).'phone'.chr(10).'phone_mobile');
        }else {
            $formato = array('format' => 'firstname lastname'.chr(10).'company'.chr(10).'postcode'.chr(10).'address1'.chr(10).'address2'.chr(10).'city'.chr(10).'State:name'.chr(10).'Country:name'.chr(10).'phone'.chr(10).'phone_mobile');
        }

        Db::getInstance()->update('address_format', $formato, '`id_country` = '.$id_country);

        return true;
    }

    private function wsRegistraLicenca($referencia, $dominio, $proprietario) {
        return true;
    }

    private function wsVerificaLicenca($referencia, $dominio) {
        return true;
    }

    private function wsAlteraLicenca($referencia, $dominio) {
        return true;
    }


}