<?php

namespace demo\pages
{
	class Designer extends BasePage
	{
        /**
         * @var \red\web\ui\controls\Textbox
         */
        protected $txtTemplate;

        /**
         * @var \red\web\ui\controls\Textbox
         */
        protected $txtCode;

        /**
         * @var \red\web\ui\html\HtmlTag
         */
        protected $iframe;

        public function __construct(\red\web\http\HttpApplication $application)
        {
            parent::__construct($application);

            $this->unregisterStylesheet($this->registerStyleSheet(static::CSS_MAIN_STYLESHEET));
            $this->iframe = $this->findFirst(function($o) {
                return $o instanceof \red\web\ui\html\HtmlTag && $o->getTagName() == 'iframe';
            });
        }

        protected function init(\red\web\http\HttpRequest $request, \red\web\http\HttpResponse $response)
        {
            parent::init($request, $response);
            $defaultTemplate = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<html xmlns:red="red.web.ui.controls" xmlns:data="demo.data">
	<head>
		<title>Hello, World!</title>
	</head>
	<body>
		<red:Form>
			<red:Button id="btnHello">Click Me!</red:Button>
		</red:Form>
	</body>
</html>
XML;
            $this->txtTemplate->setValue($defaultTemplate);

            $defaultCode = <<<PHP

    /**
     * Event handler for btnHello
     */
    private function onBtnHello_Clicked(\\red\\web\\ui\\controls\\Button \$sender, \\red\\EventArgument \$arg)
    {
        \$this->alert('Hello, World!');
    }
PHP;
            $this->txtCode->setValue($defaultCode);
        }


        protected function load(\red\web\http\HttpRequest $request, \red\web\http\HttpResponse $response)
        {
            parent::load($request, $response);

            if ($request->getRequestUrl()->offsetExists('output'))
            {

                exit(0);
            }
            else
            {
                $_SESSION['designer.code'] = $this->txtCode->getValue();
                $_SESSION['designer.template'] = $this->txtTemplate->getValue();
            }
        }
    }
}

#EOF