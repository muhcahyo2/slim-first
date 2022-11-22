$(document).ready((e) => {
  // load data pertama kali
  
  firstData()
  
})

function firstData(){
  $('#loader').show()
  $.ajax({
    url: '/api/products',
    accept: 'application/json',
    success: (res) => {
      let tr = "";
      $.each(res, (i, data)=>{
        tr+= `<tr class="data">
				<td class="text-center">${++i}</td>`
        tr+=`<td>${data.nama}</td>`
        tr+=`<td>${data.harga}</td>`
        tr+=`<td>${data.total}</td></tr>`
      })
      $('table').append(tr)
      $('#loader').hide()
    },
    error: (res) => {
      const { msg } = JSON.parse(res.responseText)
      $('#loader').hide()
      $('table').append(`<td colspan="4" class="text-center p-2" id="loader"><h3>${msg}<h3></td>`)
    }
  })
}

// post untuk tambah data
$('#modalProduct').submit(e => {
  e.preventDefault();
  var fd = new FormData();
  var files = $('#gambar')[0].files;
  const nama = $('input#name').val()
  const deskripsi = $('#deskripsi').val()
  const total = $('input#total').val()
  const harga = $('input#harga').val()
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
      firstData()
      $('.modal').modal('hide')
    }
  })
})