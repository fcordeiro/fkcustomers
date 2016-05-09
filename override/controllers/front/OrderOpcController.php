<?php

class OrderOpcController extends OrderOpcControllerCore {

    private $_template;

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

    public function setTemplate($default_template) {

        $this->_template = $default_template;
        parent::setTemplate($default_template);

    }

    public function getOverrideTemplate() {
        return Hook::exec('DisplayOverrideTemplate', array('controller' => $this, 'default_template' => $this->_template));
    }
}

?>

