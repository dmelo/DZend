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
        $this->bootstrap('logger');
        $c = new DZend_Chronometer();
        $c->start();

        set_include_path(
            APPLICATION_PATH . '/models' . PATH_SEPARATOR .
            APPLICATION_PATH . '/modules' . PATH_SEPARATOR .
            APPLICATION_PATH . PATH_SEPARATOR . get_include_path()
        );
        require_once 'Zend/Loader/Autoloader.php';
        $zendAutoloader = Zend_Loader_Autoloader::getInstance();
        $zendAutoloader->setFallbackAutoloader(true);

        $c->stop();
        Zend_Registry::get('logger')->debug('_initPath ' . $c->get());
    }

    /**
     * _initDomain
     *
     * @return void
     */
    protected function _initDomain()
    {
        $this->bootstrap('logger');
        $c = new DZend_Chronometer();
        $c->start();

        $domain = null;
        if (array_key_exists('HTTP_HOST', $_SERVER)) {
            $domain = 'http://' . $_SERVER['HTTP_HOST'];
            Zend_Registry::set('domain', $domain);
        } else
            Zend_Registry::set('domain', '');

        $c->stop();
        Zend_Registry::get('logger')->debug('_initDomain ' . $c->get());

        return $domain;
    }

    public function _initLogger()
    {
        $c = new DZend_Chronometer();
        $c->start();

        $writer = new Zend_Log_Writer_Stream("../public/tmp/log.txt");
        $logger = new Zend_Log($writer);
        Zend_Registry::set('logger', $logger);

        $c->stop();
        Zend_Registry::get('logger')->debug('_initLogger ' . $c->get());
    }

    public function _initLocale()
    {
        $this->bootstrap('path');

        $this->bootstrap('logger');
        $c = new DZend_Chronometer();
        $c->start();

        try {
            $locale = new Zend_Locale('auto');
            Zend_Registry::set('locale', $locale);
        } catch(Zend_Locale_Exception $e) {
            $locale = new Zend_Locale('en_US');
        }

        $c->stop();
        Zend_Registry::get('logger')->debug('_initLocale ' . $c->get());

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
        $this->bootstrap('logger');
        $c = new DZend_Chronometer();
        $c->start();

        $this->bootstrap('locale');
        $locale = Zend_Registry::get('locale');

        try {
            $translate = $this->getTranslate($locale);
        } catch(Zend_Translate_Exception $e) {
            $translate = $this->getTranslate('en_US');
        }
        Zend_Registry::set('translate', $translate);
        $c->stop();
        Zend_Registry::get('logger')->debug('_initTranslate ' . $c->get());

    }
}
