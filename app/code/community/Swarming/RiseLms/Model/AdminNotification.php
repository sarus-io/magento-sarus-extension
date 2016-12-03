<?php
/**
 * Created by PhpStorm.
 * User: matt
 * Date: 2/4/16
 * Time: 3:04 PM
 */

class Swarming_RiseLms_Model_AdminNotification extends Swarming_RiseLms_Model_Abstract_Notifications
{
    /**
     * Accepts a label and data array
     * @param $notificationLabel
     * @param $notificationData
     */
    public function sendNotification($notificationLabel, $notificationData)
    {
	# Commenting out this line so that mail.log doesn't grow so fast - This will need to be fixed
        #$this->sendEmail($notificationLabel, $notificationData);
    }
}
