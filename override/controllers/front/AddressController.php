<?php

class AddressController extends AddressControllerCore {

    public function initContent() {
        // Inclui as variaveis smarty
        include_once(_PS_MODULE_DIR_.'fkcustomers/includes/variaveis_smarty.php');

        // Inclui os cookies que serao utilizados pelo js
        include_once(_PS_MODULE_DIR_.'fkcustomers/includes/variaveis_cookie.php');

        parent::initContent();
    }

    public function setMedia() {

        parent::setMedia();

        // CSS
        $this->addCSS(_PS_MODULE_DIR_.'fkcustomers/css/fkcustomers_front_16x.css');

        // JS
        // MaskedInput não funciona corretamente em dispositivos móveis
        // $this->addJS(_PS_MODULE_DIR_.'fkcustomers/js/jquery.maskedinput.js');
        $this->addJS(_PS_MODULE_DIR_.'fkcustomers/js/mask.js');
        
        $this->addJS(_PS_JS_DIR_.'jquery/plugins/fancybox/jquery.fancybox.js');
        $this->addJS(_PS_MODULE_DIR_.'fkcustomers/js/fkcustomers_cookie.js');
        $this->addJS(_PS_MODULE_DIR_.'fkcustomers/js/fkcustomers_cpf.js');
        $this->addJS(_PS_MODULE_DIR_.'fkcustomers/js/fkcustomers_cnpj.js');
        $this->addJS(_PS_MODULE_DIR_.'fkcustomers/js/fkcustomers_cep.js');
        $this->addJS(_PS_MODULE_DIR_.'fkcustomers/js/fkcustomers_endereco.js');
        $this->addJS(_PS_MODULE_DIR_.'fkcustomers/js/fkcustomers_front.js');
    }

    protected function processSubmitAddress() {

        include_once(_PS_MODULE_DIR_.'fkcustomers/models/FKcustomersClass.php');

        // Instancia FKcustomersClass
        $fkcustomersClass = new FKcustomersClass();

        if ( Configuration::get('FKCUSTOMERS_MODO') == '1') {
            // Numero
            if (!Tools::getValue('numend')) {
                $this->errors[] = Tools::displayError('O campo Número é obrigatório.');
            }
        }

        // Telefone
        $telefone = Tools::getValue('phone');

        if ($telefone) {
            if (!$fkcustomersClass->validaDDD($telefone)) {
                $this->errors[] = Tools::displayError('DDD do Telefone é inválido.');
            }
        }

        // Celular
        $celular = Tools::getValue('phone_mobile');

        if ($celular) {
            if (!$fkcustomersClass->validaDDD($celular)) {
                $this->errors[] = Tools::displayError('DDD do Celular é inválido.');
            }
        }

        if (count($this->errors)) {
            return;
        }

        parent::processSubmitAddress();

    }

}

?>
