<?php

namespace AppTest\View\Model\Widget;

use PhalconExt\Mvc\View\Model as ViewModel;
use AppTest\Model\News;

class TopNews extends ViewModel
{
    protected $captureTo = 'topNews';

    protected $template = 'partials/widgets/top-news';

    public function initialize()
    {
        $this->setVar('title', 'Top News')
             ->setVar('list', News::getTopNews(10));
    }
}
