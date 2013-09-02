<?php
include(dirname(__FILE__) . '/inc_no_session.php');
new SessionCache('native'); // native | apc | memcache | files