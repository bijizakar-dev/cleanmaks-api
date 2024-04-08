@extends('layouts.main')

@section('title', 'Hari Libur')

@section('stylesheet')
    <link rel="stylesheet" href="{{ asset('node_modules/jqvmap/dist/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('node_modules/weathericons/css/weather-icons.min.cs') }}">
    <link rel="stylesheet" href="{{ asset('node_modules/weathericons/css/weather-icons-wind.min.css') }}">
    <link rel="stylesheet" href="{{ asset('node_modules/summernote/dist/summernote-bs4.css') }}">
    <link rel="stylesheet" href="{{ asset('node_modules/izitoast/dist/css/iziToast.min.css') }}">

@endsection

@section('content')

<section class="section">
    <div class="section-header">
        <h1>Hari Libur</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ url('/')}}">Dashboard</a></div>
            <div class="breadcrumb-item active">Hari Libur</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>
                            <a href="{{ route('hari-libur.create') }}" class="btn btn-primary" ><i class="fas fa-plus"></i> Tambah</a>
                            <button class="btn btn-secondary" onclick="refreshPage()"><i class="fas fa-history"></i> Reload</button>
                            <button class="btn btn-info" onclick="getHariLiburApi()"><i class="fas fa-check-circle"></i> Ambil Hari Libur</button>
                        </h4>
                        <div class="card-header-form">
                            <form action="{{ route('hari-libur.index') }}" method="GET">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="search" placeholder="Search" value="{{ request('search') }}">
                                    <div class="input-group-btn">
                                        <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tr>
                                <th style="width: 15%">Hari Libur</th>
                                <th style="width: 30%">Tanggal</th>
                                <th style="width: 10%" class="text-center">Status</th>
                                <th style="width: 10%" class="text-center">Action</th>
                            </tr>

                            {{-- Panggil data jabatan --}}
                            @foreach ($result as $i => $val)
                            <tr>
                                <td>{{ $val->name }}</td>
                                <td>{{ App\Helper\LibHelper::formatTanggalHari($val->date) }}</td>
                                <td class="text-center">
                                    @if($val->is_cuti == '1')
                                        <div class="badge badge-danger"><i class="fas fa-check-circle"></i> Masuk</div>
                                    @else
                                        <div class="badge badge-info"><i class="fas fa-check-circle"></i> Libur</div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button onclick='openEdit({{$val->id}}, "{{$val->is_cuti}}", "{{$val->date}}", "{{$val->name}}")' class="btn btn-success btn-sm"><i class="fas fa-pencil-alt"></i></button>
                                    <button onclick="confirmDelete('{{ url('hari-libur/delete/'.$val->id) }}')" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            @endforeach
                            {{-- Panggil data jabatan --}}

                        </table>
                    </div>
                </div>
                <div class="card-footer text-right">
                    @include('components.pagination', ['paginator' => $result])
                </div>
            </div>
        </div>
    </div>

</section>


{{-- Modal --}}
<div class="modal fade" tabindex="-1" role="dialog" id="edit-modal">
    <div class="modal-dialog  modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ubah Status Hari Libur</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modalBody">
                <div class="row mt-2">
                    <div class="col-lg-12">
                        <form id="editStatusForm" method="POST">
                            <input type="hidden" name="id_edit_status" id="id_edit_status" />
                            @csrf
                            <div class="form-group row mb-2">
                                <label class="col-form-label col-12 col-md-3 col-lg-2">Hari Libur</label>
                                <div class="col-sm-12 col-md-10">
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" id="name_ed">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row mb-2">
                                <label class="col-form-label col-12 col-md-3 col-lg-2">Tanggal Libur</label>
                                <div class="col-sm-12 col-md-5">
                                    <input type="date" class="form-control @error('date') is-invalid @enderror" name="date" id="date_ed">
                                    @error('date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row mb-2">
                                <label class="col-form-label col-12 col-md-3 col-lg-2">Status</label>
                                <div class="col-sm-12 col-md-5">
                                    <select name="is_cuti" class="form-control selectric @error('is_cuti') is-invalid @enderror" id="is_cuti_ed">
                                        <option selected disabled>Pilih Status..</option>
                                        <option value="1">Masuk</option>
                                        <option value="0">Libur</option>
                                    </select>
                                    @error('is_cuti')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-whitesmoke br">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="saveEdit()">Simpan</button>
            </div>
        </div>
    </div>
</div>
{{-- Modal --}}

@endsection

@section('libraries')
    {{-- <script src="{{ asset('node_modules/simpleweather/jquery.simpleWeather.min.js') }}"></script> --}}
    <script src="{{ asset('node_modules/chart.js/dist/Chart.min.js') }}"></script>
    <script src="{{ asset('node_modules/jqvmap/dist/jquery.vmap.min.js') }}"></script>
    <script src="{{ asset('node_modules/jqvmap/dist/maps/jquery.vmap.world.js') }}"></script>
    <script src="{{ asset('node_modules/summernote/dist/summernote-bs4.js') }}"></script>
    <script src="{{ asset('node_modules/chocolat/dist/js/jquery.chocolat.min.js') }}"></script>
    <script src="{{ asset('assets/js/page/index-0.js') }}"></script>
    <script src="{{ asset('node_modules/izitoast/dist/js/iziToast.min.js') }}"></script>
    <script src="{{ asset('node_modules/sweetalert/dist/sweetalert.min.js') }}"></script>

    <script>
        function refreshPage() {
            // Reload the current page
            window.location.href = window.location.pathname;
        }

        //message with toastr
        @if(session()->has('success'))
            iziToast.success({
                title: 'Berhasil',
                message: '{{ session('success') }}',
                position: 'topRight'
            });
        @elseif(session()->has('error'))
            iziToast.error({
                title: 'Gagal',
                message: '{{ session('error') }}',
                position: 'topRight'
            });
        @endif

        function refreshPage() {
            // Reload the current page
            window.location.href = window.location.pathname;
        }

        function getHariLiburApi() {
            swal({
                title: 'Konfirmasi Ambil Hari Libur API',
                text: 'Data lama akan terhapus anda yakin ingin update hari libur ?',
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        url: 'hari-libur/hit-api-holiday/',
                        type: 'GET',
                        dataType: 'json',
                        success: function(data){
                            refreshPage();
                        },
                        error: function(xhr){
                            iziToast.error({
                                title: 'Gagal',
                                message: xhr.responseJSON.response.msg,
                                position: 'topRight'
                            });
                        }
                    });
                } else {
                    swal('Batal update data data masih tersedia!');
                }
            });
        }

        function confirmDelete(url) {
            swal({
                title: 'Konfirmasi Hapus Data',
                text: 'Anda akan menghapus jenis tipe tersebut ?',
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    window.location.href = url;
                } else {
                    swal('Batal menghapus data masih tersedia!');
                }
            });
        }

        function openEdit(id, isCuti, date, name) {
            $('#id_edit_status').val(id);
            $('#name_ed').val(name);
            $('#date_ed').val(date);
            $('#is_cuti_ed').val(isCuti);

            $('#edit-modal').modal('show');
        }

        function saveEdit(){
            let token = $('meta[name="csrf-token"]').attr('content');

            $('#edit-modal').modal('hide');

            if($('#id_edit_status').val() == '' || $('#id_edit_status').val() == null) {
                iziToast.error({
                    title: 'Gagal',
                    message: 'ID Tidak Ada',
                    position: 'topRight'
                });
                return false;
            }

            let json = {
                "_token": token,
                "name" : $('#name_ed').val(),
                "date" : $('#date_ed').val(),
                "is_cuti" : $('#is_cuti_ed').val()
            }

            $.ajax({
                url: 'hari-libur/edit/' + $('#id_edit_status').val(),
                type: 'POST',
                data: json,
                dataType: 'json',
                success: function(data){
                    var currentUrl = new URL(window.location.href);
                    if(data.response.status == true) {
                        iziToast.success({
                            title: 'Berhasil',
                            message: data.response.msg,
                            position: 'topRight'
                        });
                    }

                    window.location.href = currentUrl
                },
                error: function(xhr){
                    iziToast.error({
                        title: 'Gagal',
                        message: xhr.responseJSON.response.msg,
                        position: 'topRight'
                    });
                }
            });

        }

    </script>
@endsection
