<?php

namespace App\Controller;

use Psr\Container\ContainerInterface;

class BaseController
{
  protected $c;

  public function __construct(ContainerInterface $container)
  {
    $this->c = $container;
  }
};
