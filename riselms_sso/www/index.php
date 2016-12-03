<?php

require_once('_include.php');
#echo "test";
\SimpleSAML\Utils\HTTP::redirectTrustedURL(SimpleSAML_Module::getModuleURL('core/frontpage_welcome.php'));
