<?php

class DZend_Mail extends Zend_Mail
{
    protected $_logger = null;

    protected function _getLogger()
    {
        if (null === $this->_logger) {
            $config = Zend_Registry::get('config');
            if (isset($config->mail) && isset($config->mail->logFile)) {
                $writer = new Zend_Log_Writer_Stream($config->mail->logFile);
                $this->_logger = new Zend_Log($writer);
            } else {
                $this->_logger = Zend_Registry::get('logger');
            }
        }

        return $this->_logger;
    }

    public function send($transport = null)
    {
        $fakeId = rand();
        $this->_getLogger()->info("Email: $fakeId. Sending email from: " . $this->_from . ". to: "
            . print_r($this->_to, true) . ". Subject: " . $this->_subject
            . ". BodyText: " . (is_a($this->_bodyText, 'Zend_Mime_Part') ?
                $this->_bodyText->getContent() : 'NONE')
            . ". BodyHtml: " . (is_a($this->_bodyHtml, 'Zend_Mime_Part') ?
                $this->_bodyHtml->getContent() : 'NONE') . PHP_EOL
        );

        try {
            $ret = false;
            if (DZend_Mail::emailSendingEnabled()) {
                $ret = parent::send($transport);
                $this->_getLogger()->info("Email $fakeId successfully sent");
            } else {
                $this->_getLogger()->info("Email $fakeId successfully sent ONLY TO LOG");
            }

            return $ret;
        } catch (Zend_Mail_Transport_Exception $e) {
            $this->_getLogger()->err(
                "Error while sending email $fakeId: " . $e->getMessage()
                . PHP_EOL . $e->getTraceAsString()
            );
            throw $e;
        }
    }

    public static function emailSendingEnabled()
    {
        return 'production' === APPLICATION_ENV;
    }
}
