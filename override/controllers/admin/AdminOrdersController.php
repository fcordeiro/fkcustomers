<?php


class AdminOrdersController extends AdminOrdersControllerCore {

    public function renderView() {

        parent::renderView();

        // Altera tpl conforme versão
        $versao = str_replace('.', '_', _PS_VERSION_);
        $this->base_tpl_view = 'view_'.$versao.'.tpl';

        $helper = new HelperView($this);
        $helper->module = module::getInstanceByName('fkcustomers');
        $this->setHelperDisplay($helper);

        // TODO: alterar função quando mudar versão do Prestashop
        if (version_compare(_PS_VERSION_, '1.6.0.5', '>=') and version_compare(_PS_VERSION_, '1.6.0.9', '<=')){
            $helper->tpl_vars = $this->tpl_view_vars;
        }else {
            $helper->tpl_vars = $this->getTemplateViewVars();
        }

        if (!is_null($this->base_tpl_view)) {
            $helper->base_tpl = $this->base_tpl_view;
        }

        $view = $helper->generateView();

        return $view;
    }

}