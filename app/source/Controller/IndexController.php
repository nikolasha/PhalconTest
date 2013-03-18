<?php

namespace AppTest\Controller;

/**
 * This examples of standart workflow with view
 */
class IndexController extends \Phalcon\Mvc\Controller
{
    public function indexAction()
    {
        $view = $this->view;
        $view->setMainView('index');
        $view->setTemplateBefore('before');
        $view->setTemplateAfter('after');

        $view->mainViewTitle = 'This is main layout!';
    }

    public function showAction($postId)
    {
        $view = $this->view;
        $view->setMainView('index');

        // Pass the $postId parameter to the view
        $view->postId = $postId;
        $view->title = 'This is show view!';
        $view->mainViewTitle = 'This is main layout!';
    }
}
