<?php

namespace App\Middleware;

class Auth
{

  public function __invoke($req, $res, $next)
  {
    if (!isset($_SESSION['username'])) {
      dd($_SESSION['username']);
      return $res->withRedirect('/');
    }

    return $next($req, $res);
  }
}
