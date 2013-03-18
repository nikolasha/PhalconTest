<?php

namespace PhalconExt\Mvc;

use PhalconExt\Mvc\View\Model as ViewModel;

class View extends \Phalcon\Mvc\View
{
    protected $_layoutsDir = 'layouts/';

    /**
     * Checks whether view exists on registered extensions and render it.
     *
     * @param  array            $engines
     * @param  string|ViewModel $template
     * @param  boolean          $silence
     * @param  boolean          $mustClean
     * @param  \Phalcon\Cache\BackendInterface|false $cache
     * @return void
     */
    protected function _engineRender($engines, $template, $silence, $mustClean, $cache)
    {
        if ($template instanceof ViewModel) {
            if ($template->isDisabled()) {
                return;
            }
            $viewModel = $template;
            $template = $viewModel->getTemplate();
        }

        if ($cache) {
            $renderLevel = $this->_renderLevel;
            $cacheLevel = $this->_cacheLevel;

            if ($cacheLevel <= $renderLevel) {
                if (!$cache->isStarted()) {

                    $key = null;
                    $lifetime = null;
                    $viewOptions = $this->_options;

                    if (isset($viewOptions['cache'])) {
                        $cacheOptions = $viewOptions['cache'];
                        if (isset($cacheOptions['key'])) {
                            $key = $cacheOptions['key'];
                        }
                        if (isset($cacheOptions['lifetime'])) {
                            $lifetime = $cacheOptions['lifetime'];
                        }
                    }
                    if (null === $key) {
                        $key = md5($template);
                    }

                    $cachedView = $cache->start($key, $lifetime);
                    if ($cachedView) {
                        $this->setContent($cachedView);
                        return;
                    }

                }

                if (!$cache->isFresh()) {
                    return;
                }
            }
        }

        if (empty($engines)) {
            return;
        }

        // Variables are available for all templates
        $vars = $this->getParamsToView();

        if (isset($viewModel)) {
            // Render the nested templates, if there is
            if ($viewModel->hasChildren()) {

                /** @var $child ViewModel */
                foreach ($viewModel->getChildren() as $child) {
                    ob_start();
                    $this->_engineRender($engines, $child, false, false, false);
                    $viewModel->setVar($child->getCaptureTo(), ob_get_clean());
                }
            }

            // Gets variables for the template. If there is a common, merge them
            if (empty($vars)) {
                $vars = $viewModel->getVars();
            } else {
                $vars = array_merge($vars, $viewModel->getVars());
            }
        }

        $notExists = true;
        $templatePath = $this->_basePath . $this->_viewsDir . $template;

        /** @var $eventsManager \Phalcon\Events\ManagerInterface */
        $eventsManager = $this->_eventsManager;

        /** @var $engine \PhalconExt\Mvc\View\EngineInterface */
        foreach ($engines as $extension => $engine) {
            $templateFile = $templatePath . $extension;
            if (file_exists($templateFile)) {
                if ($eventsManager) {
                    $this->_activeRenderPath = $templateFile;
                    $status = $eventsManager->fire('view:beforeRenderView', $this, $templateFile);
                    if (!$status) {
                        continue;
                    }
                }
                $engine->render($templateFile, $vars, $mustClean);

                $notExists = false;
                if ($eventsManager) {
                    $eventsManager->fire('view:afterRenderView', $this);
                }
                break;
            }
        }

        if ($notExists) {
            if ($eventsManager) {
                $this->_activeRenderPath = $templateFile;
                $eventsManager->fire('view:notFoundView', $this);
            }
            if (!$silence) {
                $message = 'View "' . $templatePath . '" was not found in the views directory';
                throw new \Phalcon\Mvc\View\Exception($message);
            }
        }
    }

    public function render($controllerName, $actionName, $params = null)
    {
        if ($this->_disabled) {
            $this->setContent(ob_get_contents());
            return false;
        }

        $actionName = strtr(\Phalcon\Text::uncamelize($actionName), '_', '-');

        $this->_controllerName = $controllerName;
        $this->_actionName = $actionName;
        $this->_params = $params;

        /** @var $di \Phalcon\DiInterface */
        $di = $this->_dependencyInjector;

        $result = null;
        if (!empty($di)) {
            /** @var $dispatcher \Phalcon\Mvc\DispatcherInterface */
            $dispatcher = $di->getShared('dispatcher');
            if (!empty($dispatcher)) {
                $result = $dispatcher->getReturnedValue();
            }
        }

        /** @var $eventsManager \Phalcon\Events\ManagerInterface */
        $eventsManager = $this->_eventsManager;

        // Call beforeRender if there is an events manager
        if ($eventsManager) {
            if (!$eventsManager->fire('view:beforeRender', $this)) {
                return false;
            }
        }

        // Get the current content in the buffer maybe some output from the controller
        $this->setContent(ob_get_contents());

        // Render level will tell use when to stop
        $renderLevel = $this->_renderLevel;

        if ($renderLevel != self::LEVEL_NO_RENDER && $result !== false) {

            if ($result instanceof ViewModel) {
                $renderView = $result;
            } elseif (is_array($result)) {
                $renderView = new ViewModel($result);
            } else {
                $renderView = new ViewModel();
            }

            if (isset($this->_pickView[0])) {
                $renderView->setTemplate($this->_pickView[0]);
            } elseif (!$renderView->getTemplate()) {
                $renderView->setTemplate($controllerName . DIRECTORY_SEPARATOR . $actionName);
            }

            $engines = $this->_loadTemplateEngines();

            $cache = null;
            if ($this->_cacheLevel) {
                $cache = $this->getCache();
            }

            // Disabled levels allow to avoid an specific level of rendering
            $disabledLevels = $this->_disabledLevels;

            // Inserts view related to action
            $curLevel = self::LEVEL_ACTION_VIEW;
            if ($renderLevel >= $curLevel && empty($disabledLevels[$curLevel])) {
                $this->_engineRender($engines, $renderView, true, true, $cache);
            }

            // Inserts templates before layout
            $curLevel = self::LEVEL_BEFORE_TEMPLATE;
            if ($renderLevel >= $curLevel && empty($disabledLevels[$curLevel])) {
                if (is_array($this->_templatesBefore)) {
                    $this->renderTemplatesBeforeOrAfter($engines, $this->_templatesBefore, $cache);
                }
            }

            // Inserts controller layout
            $curLevel = self::LEVEL_LAYOUT;
            if ($renderLevel >= $curLevel && empty($disabledLevels[$curLevel])) {

                $renderView = $this->_layout;
                if (empty($renderView)) {
                    $renderView = $controllerName;
                }
                if (isset($this->_pickView[1])) {
                    if ($renderView instanceof ViewModel) {
                        $renderView->setTemplate($this->_layoutsDir . $this->_pickView[1]);
                    } else {
                        $renderView = $this->_pickView[1];
                    }
                }
                if (is_string($renderView)) {
                    $renderView = $this->_layoutsDir . $renderView;
                } elseif (!$renderView->getTemplate()) {
                    $renderView->setTemplate($this->_layoutsDir . $controllerName);
                }

                $this->_engineRender($engines, $renderView, true, true, $cache);
            }

            // Inserts templates after layout
            $curLevel = self::LEVEL_AFTER_TEMPLATE;
            if ($renderLevel >= $curLevel && empty($disabledLevels[$curLevel])) {
                if (is_array($this->_templatesAfter)) {
                    $this->renderTemplatesBeforeOrAfter($engines, $this->_templatesAfter, $cache);
                }
            }

            // Inserts main view
            $curLevel = self::LEVEL_MAIN_LAYOUT;
            if ($renderLevel >= $curLevel && empty($disabledLevels[$curLevel])) {
                $renderView = $this->_mainView;
                $this->_engineRender($engines, $renderView, true, true, $cache);
            }

            // Store the data in the cache
            if ($cache) {
                if ($cache->isStarted() && $cache->isFresh()) {
                    $cache->save();
                } else {
                    $cache->stop();
                }
            }
        }

        // Call afterRender event
        if ($eventsManager) {
            $eventsManager->fire('view:afterRender', $this);
        }

        return true;
    }

    protected function renderTemplatesBeforeOrAfter(array $engines, array $templates, $cache = null)
    {
        /** @var $template string|\PhalconExt\Mvc\View\Model */
        foreach ($templates as $template) {
            if (is_string($template)) {
                $template = $this->_layoutsDir . $template;
            }
            $this->_engineRender($engines, $template, false, true, $cache);
        }
    }

    /**
     * Renders a partial view
     *
     * <code>
     * 	 // Show a partial inside another view
     * 	 $this->partial('shared/footer');
     *   $this->partial('partials/menu', ['selected' => $itemSelected]);
     * </code>
     *
     * @param  string $template
     * @param  array  $vars
     * @return void
     */
    public function partial($template, array $vars = null)
    {
        $template = $this->_partialsDir . $template;

        if (null !== $vars) {
            $template = new ViewModel($vars, $template);
        }

        $engines = $this->_loadTemplateEngines();

        $this->_engineRender($engines, $template, false, false, false);
    }
}
