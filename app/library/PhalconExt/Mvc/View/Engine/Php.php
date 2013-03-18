<?php

namespace PhalconExt\Mvc\View\Engine;

use PhalconExt\Mvc\View\EngineInterface;

class Php implements EngineInterface
{
    /**
     * @var \PhalconExt\Mvc\View
     */
    protected $__view;

    /**
     * @var \Phalcon\DiInterface
     */
    protected $__di;

    /**
     * Constructor
     *
     * @param \PhalconExt\Mvc\View $view
     * @param \Phalcon\DiInterface $dependencyInjector
     */
    public function __construct($view, $dependencyInjector = null)
    {
        $this->__view = $view;
        $this->__di = $dependencyInjector;
    }

    /**
     * Returns cached ouput on another view stage.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->__view->getContent();
    }

    /**
     * Displays cached ouput on another view stage.
     * For conformance with the partial(), which immediately produces output.
     *
     * @return void
     */
    public function content()
    {
        echo $this->getContent();
    }

    /**
     * Renders a partial inside another view.
     *
     * @param  string $template
     * @param  array  $vars
     * @return void
     */
    public function partial($template, array $vars = null)
    {
        $this->__view->partial($template, $vars);
    }

    /**
     * Renders a template.
     *
     * @param  string $__template  Full path to the template
     * @param  array  $__vars      Variables passed to the template
     * @param  bool   $__mustClean Clean the output buffer, subsequent output sets in the content View
     * @return void
     */
    public function render($__template, array $__vars = null, $__mustClean = false)
    {
        $__vars = (array) $__vars;
        if (array_key_exists('this', $__vars)) {
            unset($__vars['this']);
        }

        if ($__mustClean) {
            ob_clean();
        }

        extract($__vars);
        include $__template;

        if ($__mustClean) {
            $this->__view->setContent(ob_get_contents());
        }
    }
}
