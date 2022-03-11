<?php
use library\UsualToolInc\UTInc;
use library\UsualToolData\UTData;
$setup=UTInc::InstallDev() ? 1 : 0;
$app->Runin(array("setup","title"),array($setup,"Hello!UT"));
$app->Open("index.cms");