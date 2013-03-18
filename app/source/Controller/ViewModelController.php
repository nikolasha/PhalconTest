<?php

namespace AppTest\Controller;

use Phalcon\Mvc\Controller;
use PhalconExt\Mvc\View\Model as ViewModel;

/**
 * Examples of simple use of the ViewModel
 */
class ViewModelController extends Controller
{
    public function initialize()
    {
        $this->view->setMainView('main');

        // Variable is set to the object view available in all templates
        $this->view->title = 'This is a common title';
    }

    public function returnArrayAction()
    {
        // Variables $title and $content will be available
        // only in the template "view-model/return-array"
        return array(
            'title'   => 'This is title of returnArray view!',
            'content' => 'Content of returnArray view'
        );
    }

    public function returnModelAction()
    {
        $view = new ViewModel([
            'title'   => 'This is title of returnObject view!',
            'content' => 'Content of returnObject view'
        ]);

        // Same as $this->view->pick('view-model/return-array')
        $view->setTemplate('view-model/return-array');

        return $view;
    }

    public function returnFalseAction()
    {
        // Same as $this->view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_NO_RENDER)
        return false;
    }

    public function layoutModelAction()
    {
        $layout = new ViewModel();
        $layout->setVar('title', 'This is title of layout');

        $this->view->setLayout($layout);

        return array('content' => 'This is layoutModel view');
    }
}
