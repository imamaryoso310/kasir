<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Sistem Kasir Sederhana</title>
</head>

<body>
    <div class="container mt-5">
        <h2 class="mb-4">Data Transaksi</h2>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#formModal">+ Tambah
            Transaksi</button>

        <div class="relative overflow-x-auto">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Total Harga</th>
                        <th>Bayar</th>
                        <th>Kembalian</th>
                        <th>Kasir</th>
                        <th>Metode</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="table-transaksi">
                    @foreach ($transaksis as $transaksi)
                        <tr data-id="{{ $transaksi->id }}">
                            <td>{{$loop->iteration}}</td>
                            <td>{{ $transaksi->tanggal }}</td>
                            <td>{{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                            <td>{{ number_format($transaksi->bayar, 0, ',', '.') }}</td>
                            <td>{{ number_format($transaksi->kembalian, 0, ',', '.') }}</td>
                            <td>{{ $transaksi->kasir }}</td>
                            <td>{{ $transaksi->metode }}</td>
                            <td>
                                <button class="btn btn-sm btn-warning btn-edit">Edit</button>
                                <button class="btn btn-sm btn-danger btn-hapus">Hapus</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form -->
    <div class="modal fade" id="formModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="formTransaksi">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah/Edit Transaksi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="transaksi_id">
                        <div class="mb-2">
                            <label>Tanggal</label>
                            <input type="date" class="form-control" id="tanggal" required>
                        </div>
                        <div class="mb-2">
                            <label>Total Harga</label>
                            <input type="number" class="form-control" id="total_harga" required>
                        </div>
                        <div class="mb-2">
                            <label>Bayar</label>
                            <input type="number" class="form-control" id="bayar" required>
                        </div>
                        <div class="mb-2">
                            <label>Kembalian</label>
                            <input type="number" class="form-control" id="kembalian" readonly>
                        </div>
                        <div class="mb-2">
                            <label>Kasir</label>
                            <input type="text" class="form-control" id="kasir" required>
                        </div>
                        <div class="mb-2">
                            <label>Metode</label>
                            <select class="form-control" id="metode">
                                <option value="Tunai">Tunai</option>
                                <option value="Qris">Qris</option>
                                <option value="Transfer">Transfer</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Simpan</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function () {
            // Hitung kembalian otomatis
            $('#bayar, #total_harga').on('input', function () {
                let bayar = parseFloat($('#bayar').val()) || 0;
                let total = parseFloat($('#total_harga').val()) || 0;
                $('#kembalian').val(bayar - total);
            });

            // Tambah/Edit Transaksi
            $('#formTransaksi').on('submit', function (e) {
                e.preventDefault();
                let id = $('#transaksi_id').val();
                let data = {
                    tanggal: $('#tanggal').val(),
                    total_harga: $('#total_harga').val(),
                    bayar: $('#bayar').val(),
                    kembalian: $('#kembalian').val(),
                    kasir: $('#kasir').val(),
                    metode: $('#metode').val(),
                };

                let url = id ? `/transaksi/${id}` : '/transaksi';
                let method = id ? 'PUT' : 'POST';

                if (id) data._method = 'PUT'; // Laravel needs this for PUT via AJAX

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: data,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function () {
                        location.reload();
                    },
                    error: function (xhr) {
                        alert('Terjadi kesalahan: ' + xhr.responseText);
                    }
                });
            });

            // Edit Transaksi
            $('.btn-edit').on('click', function () {
                let row = $(this).closest('tr');
                $('#transaksi_id').val(row.data('id'));
                $('#tanggal').val(row.find('td:eq(1)').text()); // Tanggal
                $('#total_harga').val(row.find('td:eq(2)').text().replace(/\./g, '')); // Total Harga
                $('#bayar').val(row.find('td:eq(3)').text().replace(/\./g, '')); // Bayar
                $('#kembalian').val(row.find('td:eq(4)').text().replace(/\./g, '')); // Kembalian
                $('#kasir').val(row.find('td:eq(5)').text()); // Kasir
                $('#metode').val(row.find('td:eq(6)').text()); // Metode
                $('#formModal').modal('show');
            });

            // Hapus Transaksi
            $('.btn-hapus').on('click', function () {
                let id = $(this).closest('tr').data('id');
                if (confirm("Yakin ingin menghapus data ini?")) {
                    $.ajax({
                        url: `/transaksi/${id}`,
                        type: 'POST',
                        data: { _method: 'DELETE' },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function () {
                            location.reload();
                        },
                        error: function (xhr) {
                            alert('Gagal menghapus data: ' + xhr.responseText);
                        }
                    });
                }
            });
        });
    </script>
</body>

</html>