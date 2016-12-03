<?php
/**
 * Created by PhpStorm.
 * User: matt
 * Date: 2/4/16
 * Time: 3:04 PM
 */

class Swarming_RiseLms_Model_Abstract_Notifications
{
    protected $_adminEmail = array('matt@swarmingtech.com');

    /**
     * Send the email
     * @param $notificationLabel
     * @param $notificationData
     */
    protected function sendEmail($notificationLabel, $notificationData)
    {
        try {
            $body = $this->prepEmailBody($notificationLabel, $notificationData);

            if ($recipient = Mage::getStoreConfig('riselms_general/general/rise_api_notification_recipient'))
            {
                $this->_adminEmail = array($recipient);
            }
            foreach ($this->_adminEmail as $emailAddress) {
                $mail = new Zend_Mail('UTF-8');
                $mail->addTo($emailAddress, 'Administrator');
                $mail->setBodyHtml($body);
                $mail->setSubject($notificationLabel);
                $mail->setFrom('support@brainmdhealth.com');
                $mail->send();
            }

        } catch (Exception $e) {
            Mage::log(
                'The automatic notification could not be sent, please check your mail configurations.',
                null,
                'riselms_api_status.log',
                true
            );
        }
    }

    /**
     * Prepare the body of the email
     * @param $notificationLabel
     * @param $notificationData
     * @return string
     */
    protected function prepEmailBody($notificationLabel, $notificationData)
    {
        $body = '<h3>Magento to RiseLMS status - ' . $notificationLabel . '</h3>';
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