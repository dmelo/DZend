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
            $domain = array_key_exists('REQUEST_SCHEME', $_SERVER)
                ? $_SERVER['REQUEST_SCHEME'] : 'http';
            $domain .= '://' . $_SERVER['HTTP_HOST'];
            Zend_Registry::set('domain', $domain);
        } else {
            Zend_Registry::set('domain', '');
        }


        return $domain;
    }

    public function _initLogger()
    {
        $file = $this->getOption('logger_output_file');
        $writer = new Zend_Log_Writer_Stream(
            null === $file ? "/var/tmp/log.txt" : $file
        );
        $logger = new Zend_Log($writer);
        Zend_Registry::set('logger', $logger);
    }

    public function _initLocale()
    {
        $this->bootstrap('path');

        try {
            $locale = new Zend_Locale('auto');
        } catch(Zend_Locale_Exception $e) {
            $locale = new Zend_Locale('en_US');
        }
        Zend_Registry::set('locale', $locale);
    }

    public function getTranslate($locale)
    {
        $file = (file_exists("../locale/${locale}.php") ?
            '../' : '') . "locale/${locale}.php";
        return new DZend_Translate(
            array(
                'adapter' => 'array',
                'content' => $file,
                'locale' => $locale
            )
        );
    }

    public function _initTranslate()
    {
        $this->bootstrap('locale');
        $locale = Zend_Registry::get('locale');

        try {
            $translate = $this->getTranslate($locale);
        } catch(Zend_Translate_Exception $e) {
            $translate = $this->getTranslate('en_US');
        }
        Zend_Registry::set('translate', $translate);
    }

    public function _initFacebook()
    {
        Zend_Registry::set('facebookId', $this->getOption('facebook')['id']);
        Zend_Registry::set(
            'facebookSecret', $this->getOption('facebook')['secret']
        );
    }

    public function _initConfig()
    {
        $config = new Zend_Config_Ini(
            APPLICATION_PATH . "/configs/application.ini", APPLICATION_ENV
        );
        Zend_Registry::set('config', $config);
    }

    public function _initBootstrap()
    {
        Zend_Registry::set('bootstrap', $this);
    }
}
