const params = new Proxy(new URLSearchParams(window.location.search), {
  get: (searchParams, prop) => searchParams.get(prop),
});
const per_page = params.per_page ?? 5
const page = params.page ?? 1
let q = params.q

$(document).ready((e) => {
  // load data pertama kali
  loadData(page)
})

const loadData = (page) => {
  if (q != null) {
    url = `/api/orders?page=${page}&per_page=${per_page}&q=${q}`
  } else {
    url = `/api/orders?page=${page}&per_page=${per_page}`
  }
  $('.data').remove()
  $('#loader').show()
  $.ajax({
    url: url,
    accept: 'application/json',
    error: (res) => {
      const { msg } = JSON.parse(res.responseText)
      $('#loader').hide()
      $('table').append(`<tr class="data"><td colspan="5" class="text-center p-2" id="loader"><h3>${msg}<h3></td><tr>`)
    }
  }).done((res) => {
    drawTable(res)
  })
}

const drawTable = (res) => {
  currentPage = parseInt(res.page ?? 1)
  nextPage = (currentPage < res.total_page) ? '' : 'disabled'
  prevPage = (currentPage == 1) ? 'disabled' : ''
  prevLink = (q != null) ? `?page=${currentPage - 1}&per_page=${per_page}&q=${q}` : `?page=${currentPage - 1}&per_page=${per_page}`
  nextLink = (q != null) ? `?page=${currentPage + 1}&per_page=${per_page}&q=${q}` : `?page=${currentPage + 1}&per_page=${per_page}`
  let tr = "";
  start = (currentPage - 1) * per_page
  $.each(res.data, (i, data) => {
    const orderStatus = (data.order_updated == null) ? `<span class="btn btn-outline-warning"> process</span>` : `<span class="btn btn-success">selesai</span>`
    // console.log(orderStatus);
    tr += `<tr class="data" data-target-id=${data.id_order}>
          <td class="text-center">${++start}</td>`
    tr += `<td>${data.nama_pengorder}</td>`
    tr += `<td class="text-end">${accounting.formatMoney(data.harga_product * data.jumlah_order, 'Rp. ', 2, ".", ",")}</td>`
    tr += `<td class="text-end">${data.jumlah_order}</td>`
    tr += `<td class="text-end">${data.nama_product}</td>`
    tr += `<td class="text-end">${data.alamat}</td>`
    tr += `<td class="text-end">${formatDate(data.tgl_order)}</td>`
    tr += `<td class="text-end">${orderStatus}</td>`
    tr += `<td class="text-center"><button class="btn btn-primary" onclick="doneOrder(${data.id_order})">Selesai</button> | <button class="btn btn-danger" onclick="deleteOrder(event,${data.id_order})">Hapus</button></tr>`
  })
  $('table').append(tr)
  $('table').append(`<tr class="data">
            <td colspan="8" class="text-start p-2"><span>Page ke - ${res.page} dari ${res.total_page} page dengan Jumlah Order yang tersedia ${res.jumlah}<span></td>
            <td class="text-center">
              <nav aria-label="Page navigation example">
                <ul class="pagination">
                  <li class="page-item"><a href="${prevLink}" class="page-link ${prevPage}" onclick="loadData(event,${currentPage - 1})"><span aria-hidden="true">&laquo;</span> Prev</a></li>
                  <li class="page-item"><a href="${nextLink}" class="page-link ${nextPage}" onclick="loadData(event,${currentPage + 1})">Next <span aria-hidden="true">&raquo;</span></a></li>
                </ul>
              </nav>
            </td>
          </tr>`)
  $('#loader').hide()
}

// load product ready when modal open
$('#modalAddOrder').on('show.bs.modal', () => {
  $('#modalAddOrder #dataLoader').removeClass('d-none')
  $.ajax({
    url: '/api/products/ready',
    method: 'GET',
    success: (res) => {
      $.each(res, (i, data) => {
        $('#product_name').append(`<option value="${data.id_product}" data-total=${data.total} data-harga=${data.harga}>${data.nama} - ${accounting.formatMoney(data.harga, 'Rp. ', 2, ".", ",")}</option>`)
        $('#modalAddOrder #dataLoader').addClass('d-none')
      })
    }
  })
  $('#product_name').change((e) => {
    const total = e.target.options[e.target.selectedIndex].getAttribute('data-total')
    const harga = e.target.options[e.target.selectedIndex].getAttribute('data-harga')
    $('#total').attr('max', total).attr('placeholder', `stock tersedia ada ${total}`)
    $('#total').delay(500).keyup((e) => {
      $('#harga').val(accounting.formatMoney(harga * e.target.value, 'Rp. ', 2, ".", ","))
    })
  })
  $('#modalAddOrder').submit((e) => {
    e.preventDefault()
    console.log(e)
    $('#modalAddOrder #dataLoader').removeClass('d-none')
    const nama = $('#modalAddOrder #name').val()
    const product = $('#modalAddOrder #product_name').val()
    const total = $('#modalAddOrder #total').val()
    const alamat = $('#modalAddOrder #alamat').val()

    $.ajax({
      url: '/api/orders',
      method: 'POST',
      type: 'aplication/json',
      data: {
        nama, product, total, alamat
      },
      success: (res) => {
        $('.data').remove()
        loadData(currentPage)
        $('#modalAddProduct .form-control').val('')
        $('#dataLoader').addClass('d-none')
        $('.modal').modal('hide')
        $('#success').modal('show')
        setInterval(() => {
          $('#success').modal('hide')
        }, 800)
      }
    })
  })
})

const formatDate = (date) => {
  const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(date).toLocaleDateString('id', options)
}

const doneOrder = (id) => {
  $('#ModalHapus').modal('show')
  $('#ModalHapus h4').text('Order Selesai')
  $('#ModalHapus p').text('Apakah Anda Akan Menyelsaikan Order Ini?')
  $('#btn_hapus').text('selesai')
  $('#btn_hapus').click(() => {
    $.ajax({
      url: '/api/orders/' + id + '/done',
      method: 'POST',
      contentType: 'application/json',
      processData: false,
      success: function (response) {
        $('tr[data-target-id="7"] td:nth-child(8) > span').removeClass('btn-outline-warning').addClass('btn-success')
        // $('#modalEditProduct .form-control').addAttr('disabled')

        $('#dataLoader').addClass('d-none')
        $('.modal').modal('hide')
        $('#success').modal('show')
        setInterval(() => {
          $('#success').modal('hide')
        }, 800)
      },
      fail: (e) => {
        console.log(e);
      }
    }).done(res => {
      loadData(currentPage)
    })
  })
}

const deleteOrder = (e, id) => {
  e.preventDefault()
  $('#ModalHapus').modal('show')
  $('#btn_hapus').click(() => {
    $('#ModalHapus #dataLoader').removeClass('d-none')
    $.ajax({
      url: `/api/orders/${id}`,
      method: 'DELETE',
      success: (res) => {
        $('#ModalHapus #dataLoader').addClass('d-none')
        $(`tr[data-target-id="${id}"]`).remove()
        $('.modal').modal('hide')
        $('#success').modal('show')
        loadData(currentPage)
        setInterval(() => {
          $('#success').modal('hide')
        }, 1000)
      }
    })
  })
}
