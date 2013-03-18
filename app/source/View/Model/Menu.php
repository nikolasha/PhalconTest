<?php

namespace AppTest\View\Model;

use PhalconExt\Mvc\View\Model as ViewModel;

class Menu extends ViewModel
{
    protected $captureTo = 'menu';

    protected $template = 'partials/menu';

    public function initialize()
    {
        $this->setVar('items', [
            'home'  => ['Home',     '/'],
            'news'  => ['News',     '/news'],
            'forum' => ['Forum',    '/forum'],
            'about' => ['About us', '/about']
        ]);

        $this->setVar('itemSelected', 'home');
    }

    public function setItemSelected($key)
    {
        $this->itemSelected = $key;
    }
}
