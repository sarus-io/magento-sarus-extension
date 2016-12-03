<?php
/**
 * Created by PhpStorm.
 * User: matt
 * Date: 1/26/16
 * Time: 1:40 PM
 */

class Swarming_RiseLms_Model_Courses extends Swarming_RiseLms_Model_Abstract_Connector
{
    /** Override @var $apiPath if you need to hit another API */
    /** @var string set to amenunivercity.com when Aken says we go live */
    protected $_rewriteApiPath = '';

    private $_endpointConfigPath = 'riselms_general/general/rise_api_courses_endpoint';

    /**
     * Called before the connector is initialized
     */
    protected function _beforeInitConnector()
    {
        $this->_endpointPath = Mage::getStoreConfig($this->_endpointConfigPath);
        parent::_beforeInitConnector();
    }

    /**
     * Add data params to order related data
     * @param $label
     * @param $data
     */
    public function addCourseHeaderParam($label, $data)
    {
        $this->addHeaderParam($label, $data);
    }

    /**
     * Add data params to order related data
     * @param $label
     * @param $data
     */
    public function addCourseData($label, $data)
    {
        $this->addBodyData($label, $data);
    }

    public function participationsGet()
    {
        return $this->get();
    }
}