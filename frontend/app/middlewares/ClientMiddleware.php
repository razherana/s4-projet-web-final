<?php

namespace app\middlewares;

class ClientMiddleware
{
  public function before($params) {
    if (!isset($_SESSION['user']) || $_SESSION['user']['user_id'] === 1) {
      \Flight::redirect('/login');
      exit;
    }
  }
}