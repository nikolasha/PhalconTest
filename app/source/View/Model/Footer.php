<?php

namespace AppTest\View\Model;

use PhalconExt\Mvc\View\Model as ViewModel;

class Footer extends ViewModel
{
    protected $captureTo = 'footer';

    protected $template = 'partials/footer';
}
