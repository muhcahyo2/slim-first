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
    $page = $req->getParam('page') ?? 1;
    $perpage = $req->getParam('per_page') ?? 2;
    $offset = ($page - 1) * $perpage;

    $products = $this->c->db->select('tbl_products', [
      'id_product', 'nama',
      'total', 'harga'
    ], ['LIMIT' => [
      $offset, $perpage
    ], 'ORDER' => [
      'id_product' => 'DESC'
    ]]);

    $count = $this->c->db->count('tbl_products');

    if (count($products) < 1) {
      $data["msg"] = "data tidak tersedia";
      return $res->withJson($data, 404);
    }
    return $res->withJson(array(
      "data" => $products,
      "page" => $page,
      "per_page" => $perpage,
      "total_page" => ceil($count / $perpage),

      "jumlah" => $count
    ));
  }
  public function show(Request $req, Response $res, array $args)
  {
    $product = $this->c->db->get('tbl_products', '*', ['id_product' => $args['id']]);
    if (count($product) < 1) {
      $data["msg"] = "data tidak tersedia";
      return $res->withJson($data, 404);
    }
    return $res->withJson($product);
  }
  /**
   * This Function will recive data from user then input to db
   * */
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
        'gambar' => $filename
      ]);
    } else {
    }
  }

  /**
   * This Function will upload file to /uploads dir 
   * */
  function moveUploadedFile($directory, UploadedFile $uploadedFile)
  {
    $oriname = $uploadedFile->getClientFilename();
    $basename = bin2hex(random_bytes(8)); // see http://php.net/manual/en/function.random-bytes.php
    $filename = Date('YmdHis') . '-'  . $oriname;

    $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

    return $filename;
  }
  /**
   * this function will delete data when id_product = @param id
   * */ 
  public function destroy(Request $req, Response $res, array $args)
  {
    $id = $args['id'];
    $result = $this->c->db->delete('tbl_products', ['id_product' => $id]);
    if (!$result) {
      return $res->withJson([
        'msg' => 'gagal menghapus data'
      ]);
    }
    return $res->withJson([
      'msg' => 'berhasil menghapus products dengan id' .  $id
    ]);
  }
}
