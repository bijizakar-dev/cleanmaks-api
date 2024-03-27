@extends('layouts.main')

@section('title', 'Cuti')

@section('stylesheet')
    <link rel="stylesheet" href="{{ asset('node_modules/jqvmap/dist/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('node_modules/weathericons/css/weather-icons.min.cs') }}">
    <link rel="stylesheet" href="{{ asset('node_modules/weathericons/css/weather-icons-wind.min.css') }}">
    <link rel="stylesheet" href="{{ asset('node_modules/summernote/dist/summernote-bs4.css') }}">
    <link rel="stylesheet" href="{{ asset('node_modules/izitoast/dist/css/iziToast.min.css') }}">
    <link rel="stylesheet" href="{{ asset('node_modules/chocolat/dist/css/chocolat.css') }}">
    <style>
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .fa-spinner {
            animation: spin 1s infinite linear;
        }

    </style>
@endsection

@section('content')

<section class="section">
    <div class="section-header">
        <h1>Pengajuan Cuti</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ url('/')}}">Dashboard</a></div>
            <div class="breadcrumb-item active">Cuti</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>
                            <a href="{{ url('/cuti/create') }}" class="btn btn-primary" ><i class="fas fa-plus"></i> Tambah</a>
                            <button class="btn btn-secondary"><i class="fas fa-history"></i> Reload</button>
                        </h4>
                        <div class="card-header-form">
                            <form action="{{ route('cuti.index') }}" method="GET">
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
                                <th style="width: 3%" class="text-center">No</th>
                                <th style="width: 23%">Pegawai</th>
                                <th>Waktu Pengajuan</th>
                                <th>Tipe</th>
                                <th style="width: 18%">Tanggal Cuti</th>
                                <th style="width: 7%" class="text-center">Total</th>
                                <th style="width: 10%" class="text-center">Status</th>
                                <th style="width: 15%" class="text-center">Action</th>
                            </tr>

                            {{-- Panggil data employee --}}
                            @foreach ($result as $val)
                            <tr>
                                <td class="p-0 text-center">{{ ($result->currentPage() - 1) * $result->perPage() + $loop->index + 1 }}</td>
                                <td>{{ $val->applicant->name }} <br/> <small>{{$val->applicant->divisi->name}}</small></td>
                                <td>{{ date('d/m/Y H:i', strtotime($val->date)) }}</td>
                                <td>{{ $val->cuti_type->name }}</td>
                                <td>{{ date('d/m/Y', strtotime($val->start_date)) }} s.d {{date('d/m/Y', strtotime($val->end_date))}} </td>
                                <td class="text-center">{{ $val->total }}</td>
                                <td class="text-center">
                                    @if($val->status == 'Submitted')
                                        <div type="button" class="badge badge-info" onclick="open_edit_status({{$val->id}}, '{{$val->status}}')"><i class="fa fa-paper-plane"></i> Submitted</div>
                                    @elseif($val->status == 'Pending')
                                        <div class="badge badge-warning" onclick="open_edit_status({{$val->id}}, '{{$val->status}}')"><i class="fa fa-spinner fa-spin"></i> Pending</div>
                                    @elseif($val->status == 'Approved')
                                        <div class="badge badge-success" onclick="open_edit_status({{$val->id}}, '{{$val->status}}')"><i class="fa fa-check-circle"></i> Approved</div>
                                    @elseif($val->status == 'Rejected')
                                        <div class="badge badge-danger" onclick="open_edit_status({{$val->id}}, '{{$val->status}}')"><i class="fas fa-times-circle"></i> Rejected</div>
                                    @elseif($val->status == 'Cancelled')
                                        <div class="badge badge-primary" onclick="open_edit_status({{$val->id}}, '{{$val->status}}')"><i class="fas fa-eject"></i> Cancelled</div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-info btn-sm" onclick="detail_cuti({{$val->id}})" data-toggle="modal" ><i class="fas fa-eye"></i></button>
                                    <a href={{ url('izin/edit/'.$val->id) }} class="btn btn-success btn-sm"><i class="fas fa-pencil-alt"></i></a>
                                    <button onclick="confirmDelete('{{ url('cuti/delete/'.$val->id) }}')" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            @endforeach
                            {{-- Panggil data employee --}}

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
<div class="modal fade" tabindex="-1" role="dialog" id="detail-cuti-modal">
    <div class="modal-dialog  modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Cuti</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modalBody">
                <div class="row">
                    <div class="col-lg-8">
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <td style="width: 20%">Nama</td>
                                    <td style="width: 1%">:</td>
                                    <td class="text-left"><span id="cuti_name"></span></td>
                                </tr>
                                <tr>
                                    <td style="width: 20%">Divisi</td>
                                    <td style="width: 1%">:</td>
                                    <td class="text-left"><span id="cuti_divisi"></span></td>
                                </tr>
                                <tr>
                                    <td style="width: 20%">Pengganti</td>
                                    <td style="width: 1%">:</td>
                                    <td class="text-left"><span id="cuti_replacement"></span></td>
                                </tr>
                                <tr>
                                    <td style="width: 20%">Pengajuan</td>
                                    <td style="width: 1%">:</td>
                                    <td class="text-left"><span id="cuti_pengajuan"></span></td>
                                </tr>
                                <tr>
                                    <td style="width: 20%">Tgl. Mulai</td>
                                    <td style="width: 1%">:</td>
                                    <td class="text-left"><span id="cuti_waktu_start"></span></td>
                                </tr>
                                <tr>
                                    <td style="width: 20%">Tgl. Selesai</td>
                                    <td style="width: 1%">:</td>
                                    <td class="text-left"><span id="cuti_waktu_end"></span></td>
                                </tr>
                                <tr>
                                    <td style="width: 20%">Lama Cuti</td>
                                    <td style="width: 1%">:</td>
                                    <td class="text-left"><span id="cuti_total"></span></td>
                                </tr>
                                <tr>
                                    <td style="width: 20%">Tipe</td>
                                    <td style="width: 1%">:</td>
                                    <td class="text-left"><span id="cuti_type"></span></td>
                                </tr>
                                <tr>
                                    <td style="width: 20%">Alasan</td>
                                    <td style="width: 1%">:</td>
                                    <td class="text-left"><span id="cuti_reason"></span></td>
                                </tr>
                                <tr>
                                    <td style="width: 20%">Status</td>
                                    <td style="width: 1%">:</td>
                                    <td class="text-left"><span id="cuti_status"></span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h4>Dokumen Pendukung</h4>
                            </div>
                            <div class="card-body">
                                <div class="chocolat-parent">
                                    <a href="" class="chocolat-image" title="File Pendukung">
                                      <div data-crop-image="300" style="overflow: hidden; position: relative; height: 285px;">
                                        <img alt="image" src="#" class="img-fluid izin_image">
                                      </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer bg-whitesmoke br">
                <button type="button" class="btn btn-primary">OK</button>
            </div>
        </div>
    </div>
</div>
{{-- Modal --}}

{{-- Modal --}}
<div class="modal fade" tabindex="-1" role="dialog" id="status-cuti-modal">
    <div class="modal-dialog  modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ubah Status Perizinan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modalBody">
                <div class="row">
                    <div class="col-lg-12">
                        <form id="editStatusForm" method="POST">
                            @csrf
                            <input type="hidden" name="id_edit_status" id="id_edit_status" />
                            <div class="form-group row mb-4">
                                <div class="col-lg-12">
                                    <select name="status" class="form-control selectric @error('status') is-invalid @enderror" id="status_cuti">
                                        <option selected disabled>Pilih Status..</option>
                                        <option value="Submitted">Submitted</option>
                                        <option value="Pending">Pending</option>
                                        <option value="Approved">Approved</option>
                                        <option value="Rejected">Rejected</option>
                                        <option value="Cancelled">Cancelled</option>
                                    </select>
                                    @error('status')
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
                <button type="button" class="btn btn-primary" onclick="save_edit_status()">Simpan</button>
            </div>
        </div>
    </div>
</div>
{{-- Modal --}}

@endsection

@section('libraries')
    <script src="{{ asset('node_modules/simpleweather/jquery.simpleWeather.min.js') }}"></script>
    <script src="{{ asset('node_modules/chart.js/dist/Chart.min.js') }}"></script>
    <script src="{{ asset('node_modules/jqvmap/dist/jquery.vmap.min.js') }}"></script>
    <script src="{{ asset('node_modules/jqvmap/dist/maps/jquery.vmap.world.js') }}"></script>
    <script src="{{ asset('node_modules/summernote/dist/summernote-bs4.js') }}"></script>
    <script src="{{ asset('node_modules/chocolat/dist/js/jquery.chocolat.min.js') }}"></script>
    <script src="{{ asset('node_modules/chocolat/dist/js/jquery.chocolat.js') }}"></script>
    <script src="{{ asset('../assets/js/page/index-0.js') }}"></script>
    <script src="{{ asset('../assets/js/lib_helper.js') }}"></script>
    <script src="{{ asset('node_modules/izitoast/dist/js/iziToast.min.js') }}"></script>
    <script src="{{ asset('node_modules/sweetalert/dist/sweetalert.min.js') }}"></script>

    <script>

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

        $(document).ready(function() {
            // Check if Chocolat exists and initialize it
            if(jQuery().Chocolat) {
                $('.chocolat-parent').Chocolat();
            }

            $(document).on('click', 'a.chocolat-image', function(e) {
                e.preventDefault();
                var url = $(this).attr('href');
                $(this).Chocolat();
            });
        });


        function refreshPage() {
            window.location.href = window.location.pathname;
        }

        function confirmDelete(url) {
            swal({
                title: 'Konfirmasi Hapus Pengajuan Cuti',
                text: 'Anda akan menghapus pengjuan tersebut ?',
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

        function open_edit_status(id, status) {
            $('#id_edit_status').val(id);
            $('#status_cuti').val(status);

            if(status == 'Rejected' | status == 'Cancelled') {
                swal({
                    title: 'Informasi Status Cuti',
                    text: 'Status Cuti sudah tidak dapat diubah silahkan ajukan ulang',
                    icon: 'warning'
                });
            } else {
                $('#status-cuti-modal').modal('show');
            }

        }

        function save_edit_status(){
            let token = $('meta[name="csrf-token"]').attr('content');

            $('#status-cuti-modal').modal('hide');

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
                "status" : $('#status_cuti').val()
            }

            $.ajax({
                url: 'cuti/edit_status/' + $('#id_edit_status').val(),
                type: 'POST',
                data: json,
                dataType: 'json',
                success: function(data){
                    refreshPage();
                    if(data.response.status == true) {
                        iziToast.success({
                            title: 'Berhasil',
                            message: data.response.msg,
                            position: 'topRight'
                        });
                    }
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

        function detail_cuti(cutiId) {
            $.ajax({
                url: 'cuti/detail/' + cutiId,
                type: 'GET',
                success: function(response){
                    console.log(response);
                    $('.modal-title').text('Detail Cuti');

                    $('#cuti_name').text(response.name_applicant);
                    $('#cuti_replacement').html(response.name_replacement+' <br/> <small>'+response.divisi_replacement+' / '+response.jabatan_replacement+'</small>');
                    $('#cuti_divisi').text(response.divisi_applicant+' / '+response.jabatan_applicant);
                    $('#cuti_pengajuan').text(response.date);
                    $('#cuti_waktu_start').text(formatTanggalIndo(response.start_date));
                    $('#cuti_waktu_end').text(formatTanggalIndo(response.end_date));
                    $('#cuti_total').text(response.total+' Hari');
                    $('#cuti_type').text(response.type_name);
                    $('#cuti_reason').html(response.reason);

                    var status = '-';
                    if (response.status == 'Submitted') {
                        status = '<div class="badge badge-info"><i class="fa fa-paper-plane"></i> Submitted</div>'
                    } else if (response.status == 'Pending') {
                        status = '<div class="badge badge-warning"><i class="fa fa-spinner fa-spin"></i> Pending</div>'
                    } else if (response.status == 'Approved') {
                        status = '<div class="badge badge-success"><i class="fa fa-check-circle"></i> Approved</div>'
                    } else if (response.status == 'Rejected') {
                        status = '<div class="badge badge-danger"><i class="fas fa-times-circle"></i> Rejected</div>'
                    } else if (response.status == 'Cancelled'){
                        status = '<div class="badge badge-primary"><i class="fas fa-eject"></i> Cancelled</div>'
                    }

                    $('#cuti_status').html(status);

                    var imagePreview = '';
                    if (response.file !== '') {
                        $('.chocolat-image').attr('href', response.file);
                        $('.izin_image').attr('src', response.file);
                    } else {
                        $('.chocolat-image').attr('href', '../assets/img/example-image.jpg');
                        $('.izin_image').attr('src', '../assets/img/example-image.jpg');
                    }

                    $('#detail-cuti-modal').modal('show');

                },
                error: function(xhr){
                    console.log(xhr.responseText);
                }
            });
        }

    </script>
@endsection
