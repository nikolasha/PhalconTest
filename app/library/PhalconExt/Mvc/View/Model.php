<?php

namespace PhalconExt\Mvc\View;

class Model
{
    /**
     * What variable a parent model should capture this model to.
     *
     * @var string
     */
    protected $captureTo = 'content';

    /**
     * Nested models.
     *
     * @var array
     */
    protected $children = array();

    /**
     * Template to use when rendering this model.
     *
     * @var string
     */
    protected $template = null;

    /**
     * Variables passed to the template.
     *
     * @var array
     */
    protected $vars = array();

    /**
     * @var bool
     */
    protected $disabled = false;

    /**
     * Constructor.
     *
     * @param array|string $vars
     * @param string       $template
     */
    public function __construct($vars = null, $template = null)
    {
        if (null !== $vars) {
            $this->vars = $vars;
        }
        if (null !== $template) {
            $this->template = $template;
        }

        $this->initialize();
    }

    /**
     * View Model initialization.
     * Can be used in case of extension of the class.
     *
     * @return void
     */
    public function initialize()
    {
    }

    /**
     * @param  string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->getVar($name);
    }

    /**
     * @param  string $name
     * @param  mixed  $value
     * @return void
     */
    public function __set($name, $value)
    {
        $this->setVar($name, $value);
    }

    /**
     * @param  string $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->vars[$name]);
    }

    /**
     * @param  string $name
     * @return void
     */
    public function __unset($name)
    {
        unset($this->vars[$name]);
    }

    /**
     * @param  string $name
     * @param  mixed  $default
     * @return mixed
     */
    public function getVar($name, $default = null)
    {
        return isset($this->vars[$name]) ? $this->vars[$name] : $default;
    }

    /**
     * @param  string $name
     * @param  mixed  $value
     * @return self
     */
    public function setVar($name, $value)
    {
        $this->vars[$name] = $value;
        return $this;
    }

    /**
     * @return array
     */
    public function getVars()
    {
        return $this->vars;
    }

    /**
     * @param  array $vars
     * @param  bool  $overwrite
     * @return self
     */
    public function setVars(array $vars, $overwrite = false)
    {
        if ($overwrite) {
            $this->vars = $vars;
        } else {
            foreach ($vars as $name => $value) {
                $this->setVar($name, $value);
            }
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getCaptureTo()
    {
        return $this->captureTo;
    }

    /**
     * @param  string $captureTo
     * @return self
     */
    public function setCaptureTo($captureTo)
    {
        $this->captureTo = $captureTo;
        return $this;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Sets template name. Must be a file without extension in the views directory.
     *
     * @param  string $template
     * @return self
     */
    public function setTemplate($template)
    {
        $this->template = $template;
        return $this;
    }

    /**
     * @param  Model  $child
     * @param  string $captureTo
     * @return self
     */
    public function addChild(Model $child, $captureTo = null)
    {
        $this->children[] = $child;
        if (null !== $captureTo) {
            $child->setCaptureTo($captureTo);
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @return bool
     */
    public function hasChildren()
    {
        return !empty($this->children);
    }

    /**
     * @param  bool $flag
     * @return void
     */
    public function disable($flag = true)
    {
        $this->disabled = (bool) $flag;
    }

    /**
     * @return bool
     */
    public function isDisabled()
    {
        return $this->disabled;
    }
}
