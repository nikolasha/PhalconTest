<?php

namespace AppTest\View\Model\Widget;

use PhalconExt\Mvc\View\Model as ViewModel;

class Weather extends ViewModel
{
    protected $captureTo = 'weather';

    protected $template = 'partials/widgets/weather';

    public function initialize()
    {
        $this->setVar('title', 'Weather Widget')
             ->setVar('content', 'Weather content');
    }
}
