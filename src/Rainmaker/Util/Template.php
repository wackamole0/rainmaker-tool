<?php

namespace Rainmaker\Util;

/**
 * A wrapper class for Twig template rendering
 */
class Template {

  /**
   * @var \Twig_Environment $twig
   */
  protected static $twig = null;

  /**
   * Renders a Twig template
   *
   * @param $name
   * @param array $context
   * @return string
   */
  public static function render($name, $context = array()) {
    if (is_null(static::$twig)) {
      $loader = new \Twig_Loader_Filesystem(dirname(__FILE__) . '/../Resources/views');
      static::$twig = new \Twig_Environment($loader, array());
    }

    return static::$twig->render($name, $context);
  }

}
