<?php

namespace App\Controller;

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\UploadedFile;


class ProductController extends BaseController
{
  /**
   * This function will be return view to /dashboard/products
   * */
  public function toProducts(Request $req, Response $res, array $args)
  {
    return $this->c->view->render($res, 'pages/products.twig');
  }
  /**
   * This function will be return all products
   * */
  public function showAll(Request $req, Response $res, array $args)
  {
    $products = $this->c->db->select('tbl_products', [
      'nama',
      'total', 'harga'
    ]);

    if (count($products) < 1) {
      $data["msg"] = "data tidak tersedia";
      return $res->withJson($data, 404);
    }
    return $res->withJson($products);
  }
  public function addProduct(Request $req, Response $res, array $args)
  {
    $directory = $this->c->get('upload_directory');
    $uploadedFiles = $req->getUploadedFiles();

    $data = $req->getParsedBody();
    $uploadedFile = $uploadedFiles['file'];
    if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
      $filename = $this->moveUploadedFile($directory, $uploadedFile);
      // die(var_dump($filename));
      $result = $this->c->db->insert('tbl_products', [
        'nama' => $data['nama'],
        'deskripsi' => $data['deskripsi'],
        'total' => $data['total'],
        'harga' => $data['harga'],
        'gambar' => '/asstes/img/'. $filename
      ]);
    }else{

    }
  }
  function moveUploadedFile($directory, UploadedFile $uploadedFile)
  {
    $oriname = $uploadedFile->getClientFilename();
    $basename = bin2hex(random_bytes(8)); // see http://php.net/manual/en/function.random-bytes.php
    $filename = Date('YmdHis'). '-'  . $oriname;

    $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

    return $filename;
  }
}
