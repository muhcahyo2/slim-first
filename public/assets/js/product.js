const params = new Proxy(new URLSearchParams(window.location.search), {
  get: (searchParams, prop) => searchParams.get(prop),
});
const per_page = params.per_page ?? 10
const page = params.page ?? 1
let q = params.q

$(document).ready((e) => {
  // load data pertama kali
  loadData(page)
})

const loadData = (page)  => {
  if (q != null) {
    url = `/api/products?page=${page}&per_page=${per_page}&q=${q}`
  } else {
    url = `/api/products?page=${page}&per_page=${per_page}`
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
    tr += `<tr class="data" data-target-id=${data.id_product}>
          <td class="text-center">${++start}</td>`
    tr += `<td>${data.nama}</td>`
    tr += `<td class="text-end">${accounting.formatMoney(data.harga, 'Rp. ', 2, ".", ",")}</td>`
    tr += `<td class="text-end">${data.total}</td>`
    tr += `<td class="text-center"><button class="btn btn-primary" onclick="lihatProduct(${data.id_product})">Lihat</button> | <button class="btn btn-danger" onclick="deleteData(event,${data.id_product})">Hapus</button></tr>`
  })
  $('table').append(tr)
  $('table').append(`<tr class="data">
            <td colspan="4" class="text-start p-2"><span>Page ke - ${res.page} dari ${res.total_page} page dengan Jumlah Products yang tersedia ${res.jumlah}<span></td>
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

// post untuk tambah data
$('#modalAddProduct').submit(e => {
  e.preventDefault();
  $('#modalAddProduct #dataLoader').removeClass('d-none')
  var fd = new FormData();
  var files = $('#modalAddProduct #gambar')[0].files;
  const nama = $('#modalAddProduct input#name').val()
  const deskripsi = $('#modalAddProduct #deskripsi').val()
  const total = $('#modalAddProduct input#total').val()
  const harga = $('#modalAddProduct input#harga').val()
  fd.append('file', files[0])
  fd.append('nama', nama)
  fd.append('deskripsi', deskripsi)
  fd.append('total', total)
  fd.append('harga', harga)
  $.ajax({
    url: '/api/products',
    method: 'POST',
    data: fd,
    contentType: false,
    processData: false,
    success: function (response) {
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

// function untuk hapus data

const deleteData = (e, id) => {
  e.preventDefault()
  $('#ModalHapus').modal('show')
  $('#btn_hapus').click(() => {
    $('#ModalHapus #dataLoader').removeClass('d-none')
    $.ajax({
      url: `/api/products/${id}`,
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

const lihatProduct = (id) => {
  $('#modalEditProduct #dataLoader').removeClass('d-none')
  let nama = $('#modalEditProduct input#name')
  let deskripsi = $('#modalEditProduct #deskripsi')
  let total = $('#modalEditProduct input#total')
  let harga = $('#modalEditProduct input#harga')
  $('#modalEditProduct').modal('show')
  $.ajax({
    url: `/api/products/${id}`,
    method: 'GET',
    success: (res) => {
      $('#modalEditProduct #dataLoader').addClass('d-none')
      nama.val(res.nama)
      deskripsi.val(res.deskripsi)
      total.val(res.total)
      harga.val(res.harga)
      $('#modalEditProduct img').attr('src', `/uploads/img/${res.gambar}`)
    }
  })
  $('#edit_btn').click(function (e) {
    e.preventDefault()
    $(this).fadeOut()
    $('#submit_btn').removeClass('d-none').fadeIn()
    $('#modalEditProduct .form-control').removeAttr('disabled')
    nama.focus()
  })
  $('#submit_btn').click(e => {
    e.preventDefault()
    editProduct(id)
  })
  $('#myModal').on('dismiss.bs.modal', function () {
    $('#modalEditProduct .form-control').addAttr('disabled')
  })
}

const editProduct = (id) => {
  $('#modalEditProduct #dataLoader').removeClass('d-none')
  var fd = new FormData();
  const nama = $('#modalEditProduct input#name').val()
  const deskripsi = $('#modalEditProduct #deskripsi').val()
  const total = $('#modalEditProduct input#total').val()
  const harga = $('#modalEditProduct input#harga').val()
  fd.append('nama', nama)
  fd.append('deskripsi', deskripsi)
  fd.append('total', total)
  fd.append('harga', harga)
  $.ajax({
    url: '/api/products/' + id,
    method: 'PUT',
    data: JSON.stringify({
      nama,
      deskripsi,
      total,
      harga
    }),
    contentType: 'application/json',
    processData: false,
    success: function (response) {
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
}
$('[type="search"]').keyup((e) => {
  q = e.target.value
  console.log(q);
  $('#searchForm').delay(800).submit()
})
$("#searchForm").submit(function (event) {
  event.preventDefault();
  loadData(page)
})