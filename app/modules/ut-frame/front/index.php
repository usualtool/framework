<?php
use library\UsualToolInc\UTInc;
$setup=UTInc::InstallDev() ? 1 : 0;
$app->Runin(array("setup","title"),array($setup,"Hello UsualTool Framework"));
$app->Open("index.cms");