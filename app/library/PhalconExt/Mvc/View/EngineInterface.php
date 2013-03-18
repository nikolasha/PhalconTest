<?php

namespace PhalconExt\Mvc\View;

interface EngineInterface
{
    /**
     * \Phalcon\Mvc\View\Engine constructor
     *
     * @param \Phalcon\Mvc\ViewInterface $view
     * @param \Phalcon\DiInterface $dependencyInjector
     */
    public function __construct($view, $dependencyInjector=null);

    /**
     * Returns cached ouput on another view stage
     *
     * @return string
     */
    public function getContent();

    /**
     * Displays cached ouput on another view stage.
     * For conformance with the partial(), which immediately produces output.
     *
     * @return void
     */
    public function content();

    /**
     * Renders a partial inside another view
     *
     * @param  string $template
     * @param  array  $vars
     * @return void
     */
    public function partial($template, array $vars = null);

    /**
     * Renders a template.
     *
     * @param  string $template  Full path to the template
     * @param  array  $vars      Variables passed to the template
     * @param  bool   $mustClean Clean the output buffer, subsequent output sets in the content View
     * @return void
     */
    public function render($template, array $vars = null, $mustClean = true);
}
