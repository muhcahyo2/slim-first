<?php

namespace App\Controller;

use Medoo\Medoo;
use Slim\Http\Request;
use Slim\Http\Response;

class OrderController extends BaseController
{
  /**
   * This function will be return view to /dashboard/orders
   * */
  public function toOrders(Request $req, Response $res, $args)
  {
    $this->c->view->render($res, 'pages/order.twig', ['title' => 'Daftar Orders']);
  }

  // Show All data

  public function showAll(Request $req, Response $res, array $args)
  {
    $page = $req->getParam('page') ?? 1;
    $perpage = $req->getParam('per_page') ?? 10;
    $query = $req->getParam('q') ?? '';
    $offset = ($page - 1) * $perpage;
    $conditions =  [
      // 'order.nama[~]' => $query,
      'LIMIT' => [
        $offset, $perpage
      ],
      'ORDER' => [
        'id_order' => 'DESC'
      ]
    ];
    $select = [
      'order.id_order',
      'order.nama (nama_pengorder)',
      'product.nama (nama_product)',
      'product.harga (harga_product)',
      'order.total (jumlah_order)',
      'order.created_at (tgl_order)',
      'order.updated_at (order_updated)',
      'order.alamat'
    ];
    $orders = $this->c->db->select('tbl_orders (order)', [
      '[><]tbl_products (product)' => ['order.id_products' => 'id_product']
    ], $select, $conditions);
    $count = $this->c->db->count('tbl_orders');

    if (count($orders) < 1) {
      $data["msg"] = "data tidak tersedia";
      return $res->withJson($data, 404);
    }
    return $res->withJson(array(
      "data" => $orders,
      "page" => $page,
      "per_page" => $perpage,
      "total_page" => ceil($count / $perpage),

      "jumlah" => $count
    ));
  }

  public function addOrder(Request $req, Response $res, array $args)
  {
    $data = $req->getParsedBody();
    $result = $this->c->db->insert('tbl_orders', [
      'nama' => $data['nama'],
      'total' => $data['total'],
      'id_products' => $data['product'],
      'alamat' => $data['alamat']
    ]);
    if (!$result) {
      return $res->withJson(array('msg' => 'gagal menambah data'), 501);
    }
    $this->c->db->update('tbl_products', ['total'=>Medoo::raw("SUM(<total> - ". $data['total'] .")")], ['id_product' => $data['product']]);
    return $res->withJson(array('msg' => 'berhasil tambah data'), 200);
  }
  public function doneOrder(Request $req, Response $res, array $args)
  {
    $id = $args['id'];
    $result = $this->c->db->update('tbl_orders', ['updated_at'=> Medoo::raw('now()')], ['id_order'=>$id]);
    return $res->withJson($result, 200);
  }

  public function destroy(Request $req, Response $res, array $args)
  {
    $id = $args['id'];
    $result = $this->c->db->delete('tbl_orders', ['id_order' => $id]);
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
