<?php

class DZend_Application_Bootstrap_Bootstrap extends
    Zend_Application_Bootstrap_Bootstrap
{
    /**
     * _initPath
     *
     * @return void
     */
    protected function _initPath()
    {
        set_include_path(
            APPLICATION_PATH . '/models' . PATH_SEPARATOR .
            APPLICATION_PATH . '/modules' . PATH_SEPARATOR .
            APPLICATION_PATH . PATH_SEPARATOR . get_include_path()
        );
        require_once 'Zend/Loader/Autoloader.php';
        $zendAutoloader = Zend_Loader_Autoloader::getInstance();
        $zendAutoloader->setFallbackAutoloader(true);
    }

    /**
     * _initDomain
     *
     * @return void
     */
    protected function _initDomain()
    {
        $domain = null;
        if (array_key_exists('HTTP_HOST', $_SERVER)) {
            $domain = 'http://' . $_SERVER['HTTP_HOST'];
            Zend_Registry::set('domain', $domain);
        } else
            Zend_Registry::set('domain', '');

        return $domain;
    }

    public function _initLogger()
    {
        $writer = new Zend_Log_Writer_Stream("tmp/log.txt");
        $logger = new Zend_Log($writer);
        Zend_Registry::set('logger', $logger);
    }

    public function getTranslate($locale)
    {
        return new DZend_Translate(
            array('adapter' => 'array',
                'content' => "../locale/${locale}.php",
                'locale' => $locale)
        );
    }

    public function _initTranslate()
    {
        $this->bootstrap('locale');
        $session = DZend_Session_Namespace::get('session');
        if (!isset($session->translate)) {
            $locale = $session->locale;

            try {
                $translate = $this->getTranslate($locale);
            } catch(Zend_Translate_Exception $e) {
                $translate = $this->getTranslate('en_US');
            }

            $session->translate = $translate;
        }
    }


}
