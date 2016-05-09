<?php

class AuthController extends AuthControllerCore {

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
        $this->addJS(_PS_JS_DIR_.'jquery/plugins/fancybox/jquery.fancybox.js');
    }

    protected function processSubmitAccount() {

        include_once(_PS_MODULE_DIR_.'fkcustomers/models/FKcustomersClass.php');

        // Instancia FKcustomersClass
        $fkcustomersClass = new FKcustomersClass();

        // Valida CPF/CNPJ
        $cpf_cnpj = Tools::getValue('cpf_cnpj');

        if (!$cpf_cnpj) {
            if (Tools::getValue('tipo') == 'pf') {
                $this->errors[] = Tools::displayError('O campo CPF é obrigatório.');
            }else {
                $this->errors[] = Tools::displayError('O campo CNPJ é obrigatório.');
            }
        }else {
            if (Configuration::get('FKCUSTOMERS_DUPL_CPF_CNPJ') == 'on') {

                if ($fkcustomersClass->duplicidadeCPF_CNPJ($cpf_cnpj, '0')) {
                    if (Tools::getValue('tipo') == 'pf') {
                        $this->errors[] = Tools::displayError('CPF já cadastrado.');
                    }else {
                        $this->errors[] = Tools::displayError('CNPJ já cadastrado.');
                    }
                }
            }
        }

        // Valida RG/IE
        if (!Tools::getValue('rg_ie')) {
            if (Tools::getValue('tipo') == 'pf') {
                if (Configuration::get('FKCUSTOMERS_RG_REQ') == 'on') {
                    $this->errors[] = Tools::displayError('O campo RG é obrigatório.');
                }
            }else {
                if (Configuration::get('FKCUSTOMERS_IE_REQ') == 'on') {
                    $this->errors[] = Tools::displayError('O campo IE é obrigatório.');
                }
            }
        }

        // Valida Numero/Telefone/Celular se for One Step e Modo Completo
        if (Configuration::get('PS_REGISTRATION_PROCESS_TYPE') or $this->ajax) {

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

        }

        parent::processSubmitAccount();
    }

}

