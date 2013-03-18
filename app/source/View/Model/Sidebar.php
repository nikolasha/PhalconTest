<?php

namespace AppTest\View\Model;

use PhalconExt\Mvc\View\Model as ViewModel;

class Sidebar extends ViewModel
{
    protected $captureTo = 'sidebar';

    protected $template = 'partials/sidebar';

    public function initialize()
    {
        $this->title = 'Sidebar Title';
        $this->addChild(new Widget\TopNews());
        $this->addChild(new Widget\Weather());
    }
}
