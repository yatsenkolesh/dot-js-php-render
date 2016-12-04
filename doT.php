<?php

/**
  * @author https://github.com/webfay
  * @version 0.0.1
*/

class doT
{

  protected $templateContents = null;
  protected $assigns = [];

  public $settings = [
    'jsPropertyName' => 'it',
    'jsPropertyCall' => '.',
    'getdoTValueTemplate' => '{{=%s}}',
    'isCacheContents' => 1,
    'findIdRegex' => '/id\=("|\')%ID%("|\')(.*)\>(.*)\<\/script\>/Uims',
    'regexResultId' => 4,
    'templatesIsHandleByPHP' => 1
  ];


  public static $cacheContents = [];

  /**
    * @param string $template path or content of template
    * @param boolean $isFile is $template of path
    * @param array $settings
    * @return object of doT
  */

  public static

  function instance($template = null, $isFile = 0,array $settings = [])
  {
    return new self($template, $isFile, $settings);
  }

  /**
    * @param string $template path to template or contents
    * @param boolean $isFile  $template is path to template file
    * @param array $setinngs
  */

  public

  function __construct($template = null, $isFile = 0,array $settings = [])
  {
    $this->settings = $settings + $this->settings;

    if($template && !$isFile)
      return $this->templateContents = $template;

    if($isFile && file_exists($template))
      return $this->loadTemplate($template, $this->settings['templatesIsHandleByPHP']);

    if($isFile)
      throw new Exception('File '.$template.' is not exists');
  }

  public

  function __set($key, $value)
  {
    $this->assigns[$key] = $value;
  }

  /**
    * @param string $templatePath path of template
    * @param boolean $isHandlePHP if set to true code in template will be execute
    * @return object $this
  */

  public

  function loadTemplate($templatePath = null, $isHandlePHP = 0)
  {
    if($this->settings['isCacheContents'] && isset(self::$cacheContents[$templatePath]))
      return $this->templateContents = self::$cacheContents[$templatePath];

    if(!file_exists($templatePath) || !is_readable($templatePath))
      throw new Exception('Path to template '. $templatePath.' is not exist or not readable');

    if($isHandlePHP)
    {
      ob_start();
        require $templatePath;
      $this->templateContents = ob_get_clean();
      return $this;
    }

    $this->templateContents = file_get_contents($templatePath);

    return $this;
  }


  /**
    * @param array $assigns
    * @return object $this
  */

  public

  function assign(array $assigns = [])
  {
    $this->assigns = $assigns;
    return $this;
  }

  /**
    * @return array prepared assigns for replace in template contents
  */

  protected

  function getPrepareAssigns()
  {
    $assigns = [];
    foreach($this->assigns as $assignKey => $assignValue)
    {
      if(is_array($assignValue) || is_object($assignValue))
        throw new Exception('php doT render: value not can be array or object. Array key :' . $assignKey);

      $key = $this->settings['jsPropertyName'].$this->settings['jsPropertyCall']. $assignKey;
      $assigns [str_replace('%s', $key, $this->settings['getdoTValueTemplate'])] = $assignValue;
    }
    return $assigns;
  }

  /**
    * @param string $ID script template id to find in contents
    * @return string part with ID
  */

  protected

  function getTemplatePartByScriptId($ID = null)
  {
    // @TODO: make cache for next
    preg_match(str_replace('%ID%', $ID, $this->settings['findIdRegex']), $this->templateContents, $match);
    return isset($match[$this->settings['regexResultId']]) ? $match[$this->settings['regexResultId']] : FALSE;
  }
  /**
    * @param string $FindID script template id to find in contents
    * @param array $assigns assigns to set with $this->assign
    * @return string ready template
  */

  public

  function render($FindID = null, array $assigns = [])
  {
    if(sizeof($assigns))
      $this->assign($assigns);

    if($FindID && !$this->getTemplatePartByScriptId($FindID))
      throw new Exception('Failed to parse template: id '. $FindID.' is not find');

    return strtr(($FindID ? $this->getTemplatePartByScriptId($FindID) : $this->templateContents), $this->getPrepareAssigns());
  }

}
