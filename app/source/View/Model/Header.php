<?php

namespace AppTest\View\Model;

use PhalconExt\Mvc\View\Model as ViewModel;
use AppTest\Model\Entity\User;

class Header extends ViewModel
{
    protected $captureTo = 'header';

    protected $template = 'partials/header';

    /**
     * @var Menu
     */
    protected $menu;

    public function __construct(User $user)
    {
        $this->menu = new Menu();
        $this->addChild($this->menu);

        if ($user->isAuthorized()) {
            $this->user = $user;
        } else {
            $this->setTemplate('partials/header-login');
            $this->addChild(new ViewModel(null, 'partials/login-form'), 'loginForm');
        }
    }

    public function getMenu()
    {
        return $this->menu;
    }
}
