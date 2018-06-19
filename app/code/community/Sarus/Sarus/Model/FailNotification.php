<?php

class Sarus_Sarus_Model_FailNotification
{
    /**
     * @var Sarus_Sarus_Model_Config_Api
     */
    protected $_configApi;

    public function __construct()
    {
        $this->_configApi = Mage::getModel('sarus_sarus/config_api');
    }

    /**
     * @param int $storeId
     * @param string|null $customerEmail
     * @param \Psr\Http\Message\RequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface|null $response
     * @return void
     */
    public function notify($storeId, $customerEmail, $request, $response = null)
    {
        $recipients = $this->_configApi->getNotificationRecipients($storeId);
        $data = $this->_getEmailBody($customerEmail, $request, $response);

        foreach ($recipients as $recipient) {
            $this->_sendEmail($recipient, $data);
        }
    }

    /**
     * @param string $recipient
     * @param string $body
     * @return void
     */
    protected function _sendEmail($recipient, $body)
    {
        /** @var \Mage_Core_Model_Email $mail */
        $mail = Mage::getModel('core/email');
        $mail->setType('html');

        $mail->setFromName($this->_configApi->getNotificationSenderName());
        $mail->setFromEmail($this->_configApi->getNotificationSenderEmail());

        $mail->setToName('Sarus Administrator');
        $mail->setToEmail($recipient);

        $mail->setSubject('Sarus Fail Notification');
        $mail->setBody($body);

        try {
            $mail->send();
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * Prepare the body of the email
     *
     * @param string|null $customerEmail
     * @param \Psr\Http\Message\RequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface|null $response
     * @return string
     */
    protected function _getEmailBody($customerEmail, $request, $response = null)
    {
        $body = '<h3>Magento to Sarus status - Connection to Sarus could not be established</h3>';
        $body .= '<div>';

        if ($customerEmail) {
            $body .= '<span><b>USER EMAIL:</b></span>' . htmlentities($customerEmail) . '<br>';
        }

        $body .= '<span><b>ENDPOINT:</b></span>' . htmlentities($request->getUri()) . '<br>';

        if ($response) {
            $body .= '<span><b>RESPONSE:</b></span>';
            $body .= '<ul>';
            $body .= '    <li>Response Code: ' . htmlentities((string)$response->getStatusCode()) . '</li>';
            $body .= '    <li>Response Message: ' . htmlentities((string)$response->getReasonPhrase()) . '</li>';
            $body .= '    <li>Response Body: ' . htmlentities((string)$response->getBody()) . '</li>';
            $body .= '</ul>';
        }

        $body .= '</div>';

        return $body;
    }
}
