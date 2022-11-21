<?php

namespace App\Controller;

use Slim\Http\Request;
use Slim\Http\Response;

class UserController extends BaseController
{
  public function Index(Request $req, Response $res, array $args)
  {
    $users = $this->c->db->select('user_details', [
      'user_id', 'username', 'first_name', 'last_name', 'gender'
    ]);

    $data = [
      'title' => 'Home',
      'users' => $users,
    ];
    // dd($users);

    return $this->c->view->render($res, 'home.twig', $data);
  }
  public function login(Request $req, Response $res, array $args)
  {
    $user = $this->c->db->get('user_details', ['username', 'password'], [
      'username' => $req->getParsedBody()['username']
    ]);
    if ($user) {
      $password = $user['password'];
      if ($password == md5($req->getParsedBody()['password'])) {
        $_SESSION['username'] = $user['username'];
      } else {
        $this->c->flash->addMessage('errors', 'Password Anda Salah');
      }
    } else {
      $this->c->flash->addMessage('errors', 'Username belum terdaftar');
    }
    return $res->withRedirect('/');
  }
  public function register(Request $req, Response $res, array $args)
  {
    $data = $req->getParsedBody();
    $user = $this->c->db->select('user_details', ['username'], [
      'username' => $data['username']
    ]);
    if (!$user) {
      $result = $this->c->db->insert('user_details', [
        'username' => $data['username'],
        'first_name' => $data['first_name'],
        'last_name' => $data['last_name'],
        'gender' => $data['gender'],
        'password' => md5($data['password'])
      ]);
      if (!$result) {
        $this->c->flash->addMessage('errors', 'gagal daftar ada sesuatu yang errors');
      } else {
        $_SESSION['username'] = $data['username'];
      }
      // dd($result);
    } else {
      $this->c->flash->addMessage('errors', 'username telah terdaftar');
    }
    return $res->withRedirect('/');
  }
}
