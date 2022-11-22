<?php

namespace App\Controller;

use Slim\Http\Request;
use Slim\Http\Response;

class UserController extends BaseController
{
  public function Index(Request $req, Response $res, array $args)
  {
    $users = $this->c->db->select('tbl_users', [
      'user_id', 'username', 'first_name', 'last_name', 'gender'
    ]);

    $data = [
      'title' => 'Home',
      'users' => $users,
    ];

    return $this->c->view->render($res, 'pages/home.twig', $data);
  }
  public function login(Request $req, Response $res, array $args)
  {
    $user = $this->c->db->get('tbl_users', ['username', 'password'], [
      'username' => $req->getParsedBody()['username']
    ]);
    if ($user) {
      $password = $user['password'];
      if ($password == md5($req->getParsedBody()['password'])) {
        $_SESSION['username'] = $user['username'];
      } else {
        $this->c->flash->addMessage('errors', 'Password Anda Salah');
        return $res->withRedirect('/auth/login');
      }
    } else {
      $this->c->flash->addMessage('errors', 'Username belum terdaftar');
      return $res->withRedirect('/auth/login');
    }
    return $res->withRedirect('/dashboard');
  }
  public function register(Request $req, Response $res, array $args)
  {
    $data = $req->getParsedBody();
    $user = $this->c->db->select('tbl_users', ['username'], [
      'username' => $data['username']
    ]);
    if (!$user) {
      $result = $this->c->db->insert('tbl_users', [
        'username' => $data['username'],
        'first_name' => $data['first_name'],
        'last_name' => $data['last_name'],
        'gender' => $data['gender'],
        'password' => md5($data['password'])
      ]);
      if (!$result) {
        $this->c->flash->addMessage('errors', 'gagal daftar ada sesuatu yang errors');
        return $res->withRedirect('/auth/register');
      } else {
        $_SESSION['username'] = $data['username'];
      }
      // dd($result);
    } else {
      $this->c->flash->addMessage('errors', 'username telah terdaftar');
      return $res->withRedirect('/auth/register');
    }
    return $res->withRedirect('/dashboard');
  }
}
