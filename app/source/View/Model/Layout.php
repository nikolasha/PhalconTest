<?php

namespace AppTest\View\Model;

use PhalconExt\Mvc\View\Model as ViewModel;
use AppTest\Model\Entity\User;

class Layout extends ViewModel
{
    protected $template = 'layout';

    /**
     * @var User
     */
    protected $user;

    /**
     * @var Header
     */
    protected $header;

    /**
     * @var Sidebar
     */
    protected $sidebar;

    public function __construct(User $user)
    {
        $this->header = new Header($user);
        $this->addChild($this->header);

        $this->sidebar = new Sidebar();
        $this->addChild($this->sidebar);

        $this->addChild(new Footer());
    }

    /**
     * @return Header
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @return Sidebar
     */
    public function getSidebar()
    {
        return $this->sidebar;
    }
}
