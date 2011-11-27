<?php


require_once dirname(__FILE__) . '/../src/lib/bootstrap.php';

\red\cli\CommandLineTool::run(new \tools\UnitTestRunner());
