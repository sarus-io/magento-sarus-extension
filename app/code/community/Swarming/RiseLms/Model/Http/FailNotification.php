<?php

class Swarming_RiseLms_Model_Http_FailNotification
{
    const LOG_FILE = 'riselms_api_notification.log';

    /**
     * @var Swarming_RiseLms_Model_Config_Api
     */
    protected $_configApi;

    public function __construct()
    {
        $this->_configApi = Mage::getModel('swarming_riselms/config_api');
    }
    /**
     * @param string $path
     * @param array $data
     * @param Zend_Http_Response $response
     * @param int|null $storeId
     * @return void
     */
    public function notify($path, $data, $response, $storeId = null)
    {
        $subject = 'Connection to Rise LMS could not be established';
        $body = $this->prepEmailBody(
            $subject,
            array(
                'User Email' => (!empty($data['email']) ? $data['email'] : ''),
                'Endpoint' => $path,
                'API Request Failed' => $response->getStatus() . ' has been returned',
                'Response' => array(
                    'Response Headers: ' => $response->getheaders(),
                    'Response Body: ' => $response->getBody(),
                    'Response Message: ' => $response->getMessage()
                ),
            )
        );

        $recipients = $this->_configApi->getNotificationRecipients($storeId);
        foreach ($recipients as $recipient) {
            $this->sendEmail($recipient, $recipient, $body);
        }
    }

    /**
     * @param string $recipient
     * @param string $subject
     * @param string $body
     */
    protected function sendEmail($recipient, $subject, $body)
    {
        try {
            $mail = new Zend_Mail('UTF-8');
            $mail->addTo($recipient, 'Administrator');
            $mail->setBodyHtml($body);
            $mail->setSubject($subject);
            $mail->setFrom('support@brainmdhealth.com');
            $mail->send();
        } catch (Exception $e) {
            Mage::log(
                'The automatic notification could not be sent, please check your mail configurations.',
                null,
                self::LOG_FILE,
                true
            );
        }
    }

    /**
     * Prepare the body of the email
     *
     * @param string $notificationLabel
     * @param array $notificationData
     * @return string
     */
    protected function prepEmailBody($notificationLabel, array $notificationData)
    {
        $body = '<h3>Magento to Rise LMS status - ' . $notificationLabel . '</h3>';
        $body .= '<p>';
        foreach ($notificationData as $key => $value)
        {
            if (is_array($value))
            {
                $body .= '<span><b>' . ucwords($key) . ':</b></span>';
                $body .= '<ul>';
                foreach ($value as $key => $subvalue)
                {
                    if (is_array($subvalue))
                    {
                        $body .= '<li>' . $key;
                        $body .= '<ul>';
                        foreach ($subvalue as $key => $subsubvalue)
                        {
                            $body .= '<li>' . $key . ': '. $subsubvalue . '</li>';
                        }
                        $body .= '</ul>';
                    } else {
                        $body .= '<li>' . $key . $subvalue . '</li>';
                    }
                }
                $body .= '</ul>';
            } else {
                $body .= '<span><b>' . ucwords($key) . ':</b></span> ' . $value . '<br>';
            }
        }
        $body .= '</p>';

        return $body;
    }
}
