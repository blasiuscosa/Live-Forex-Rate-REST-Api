<?php
require_once 'RestApi.php';
if(isset($_REQUEST["request"]))
{
$api = new RestApi($_REQUEST["request"]);
echo $api->executeApi();
}
else
{
header("Location: http://www.sauryatech.com");
}