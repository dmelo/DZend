<?php

class DZend_Mail extends Zend_Mail
{
    public function send($transport = null)
    {
        $fakeId = rand();
        $logger = Zend_Registry::get('logger');
        $logger->info(
            "Email: $fakeId. Sending email from: " . $this->_from . ". to: "
            . print_r($this->_to, true) . ". Subject: " . $this->_subject
            . ". BodyText: " . (is_a($this->_bodyText, 'Zend_Mime_Part') ?
                $this->_bodyText->getContent() : 'NONE')
            . ". BodyHtml: " . (is_a($this->_bodyHtml, 'Zend_Mime_Part') ?
                $this->_bodyHtml->getContent() : 'NONE')
        );

        try {
            $ret = parent::send($transport);
            $logger->info("Email $fakeId successfully sent");
            return $ret;
        } catch (Zend_Mail_Transport_Exception $e) {
            $logger->err("Error while sending email $fakeId: " . $e->getMessage() . PHP_EOL . $e->getTraceAsString());
            throw $e;
        }
    }
}
