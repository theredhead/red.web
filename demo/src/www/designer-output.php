<?php

require_once 'bootstrap.php';

error_reporting(E_ALL);
ini_set('display_errors', true);

session_start();

class DesignedPageBase extends \red\web\ui\WebPage
{
    public function __construct(\red\web\http\HttpApplication $application)
    {
        if ($_SERVER['REMOTE_ADDRE'] != '127.0.0.1')
        {
            throw new ErrorException('The designer cannot run off localhost for security reasons.');
        }
        parent::__construct($application);
        $this->clear();
        $this->registerClientScript('/js/events.js');
    }

    protected function init(\red\web\http\HttpRequest $request, \red\web\http\HttpResponse $response)
    {
        parent::init($request, $response);
        $this->autoWireEvents();
    }
}

$codeBehind = isset($_SESSION['designer.code']) ? $_SESSION['designer.code'] : '';

$pageClass = <<<PHP
<?php

class DesignedPage extends \DesignedPageBase
{
    {$codeBehind}
}

PHP;

$tmpFile = tempnam(sys_get_temp_dir(),'designer-template-').'.php';
file_put_contents($tmpFile, $pageClass);
require_once($tmpFile);

class MockApplication extends \red\web\http\HttpApplication
{

    /**
     * Implement this method to handle the request.
     *
     * @param HttpRequest $request
     * @param HttpResponse $response
     * @return void
     */
    public function processRequest(\red\web\http\HttpRequest $request, \red\web\http\HttpResponse $response)
    {
        $template = isset($_SESSION['designer.template']) ? $_SESSION['designer.template'] : '<h1>No template yet</h1>';

        // unlink($tmpFile);

        if (! class_exists('DesignedPage', false))
        {
            throw new \ErrorException('class generation failed.');
        }

        $page = new DesignedPage($this);

        if (strlen($template) > 0)
        {
            $reader = new \red\web\ui\WebPageReader();
            $page = $reader->read($template, $page);
        }

        $page->processRequest($request, $response);
    }
}

/**
 * Register our class loading mechanism
 */
function demo_app_class_loader($fullyQualifiedClassName)
{
	static $baseDir = null;
	$baseDir = $baseDir !== null
			? $baseDir
			: realpath('./../lib');

	$result = false;

	$parts = explode(NAMESPACE_SEPARATOR, $fullyQualifiedClassName);
	$relativePath = strtolower(implode(DIRECTORY_SEPARATOR, $parts)) . '.php';

	$absolutePath = $baseDir .DIRECTORY_SEPARATOR. $relativePath;
	if (file_exists($absolutePath))
	{
		require_once $absolutePath;
		$result = true;
	}
	else
	{
//		throw new ErrorException(sprintf("<code>%s</code> not found in <code>%s</code><br/>", $fullyQualifiedClassName, $absolutePath));
	}

	return $result;
}
spl_autoload_register('demo_app_class_loader');


MockApplication::start();

