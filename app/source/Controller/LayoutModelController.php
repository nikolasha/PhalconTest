<?php

namespace AppTest\Controller;

use Phalcon\Mvc\Controller;
use AppTest\View\Model\Layout;
use AppTest\Model\Entity\User;

/**
 * Examples of extended use of the ViewModel
 */
class LayoutModelController extends Controller
{
    public function initialize()
    {
        // Should be in Application::bootstrap()
        if (!$this->request->isAjax()) {
            $user = new User();
            $layout = new Layout($user);
            $this->di->setShared('layout', $layout);
            $this->view->setMainView($layout);
        }
    }

    public function indexAction()
    {
        /** @var $layout \AppTest\View\Model\Layout */
        $layout = $this->layout;
        $layout->getHeader()->getMenu()->setItemSelected('news');

        return ['content' => 'This is content of view'];
    }

    public function noRenderSidebarAction()
    {
        /** @var $layout \AppTest\View\Model\Layout */
        $layout = $this->layout;
        $layout->getSidebar()->disable();
    }
}
