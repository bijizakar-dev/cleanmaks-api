@extends('layouts.main')

@section('title', 'Pegawai')

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
        <h1>Pegawai</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ url('/')}}">Dashboard</a></div>
            <div class="breadcrumb-item active">Pegawai</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>
                            <a href="{{ url('/employees/create') }}" class="btn btn-primary" ><i class="fas fa-plus"></i> Tambah</a>
                            <button class="btn btn-secondary"><i class="fas fa-history"></i> Reload</button>
                        </h4>
                        <div class="card-header-form">
                            <form action="{{ route('employees.index') }}" method="GET">
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
                                <th style="width: 25%">Nama</th>
                                <th>Divisi</th>
                                <th>Jabatan</th>
                                <th>Alamat</th>
                                <th class="text-center">Status</th>
                                <th style="width: 10%" class="text-center">Action</th>
                            </tr>

                            {{-- Panggil data employee --}}
                            @foreach ($result as $val)
                            <tr>
                                <td class="p-0 text-center">{{ ($result->currentPage() - 1) * $result->perPage() + $loop->index + 1 }}</td>
                                <td>
                                    <img alt="image" src="{{$val->photo != null ? asset($val->photo) : asset('/assets/img/avatar/avatar-5.png')}}" class="rounded-circle" width="35" height="35" data-toggle="title" title="">
                                    <div class="d-inline-block ml-2">{{ $val->name }}</div>
                                </td>
                                <td>{{ optional($val->divisi)->name ?? '-' }}</td>
                                <td>{{ optional($val->jabatan)->name ?? '-' }}</td>
                                <td class="align-middle">{{ strip_tags($val->address) }} <br/> {{$val->phone}}</td>
                                <td class="text-center">
                                    @if($val->is_verified == 1)
                                        <div class="badge badge-info">Active</div>
                                    @else
                                        <div class="badge badge-warning">Non-Active</div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href={{ url('employees/edit/'.$val->id) }} class="btn btn-success btn-sm"><i class="fas fa-pencil-alt"></i></a>
                                    <button onclick="confirmDelete('{{ url('employees/delete/'.$val->id) }}')" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
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

@endsection

@section('libraries')
    <script src="{{ asset('node_modules/simpleweather/jquery.simpleWeather.min.js') }}"></script>
    <script src="{{ asset('node_modules/chart.js/dist/Chart.min.js') }}"></script>
    <script src="{{ asset('node_modules/jqvmap/dist/jquery.vmap.min.js') }}"></script>
    <script src="{{ asset('node_modules/jqvmap/dist/maps/jquery.vmap.world.js') }}"></script>
    <script src="{{ asset('node_modules/summernote/dist/summernote-bs4.js') }}"></script>
    <script src="{{ asset('node_modules/chocolat/dist/js/jquery.chocolat.min.js') }}"></script>
    <script src="{{ asset('../assets/js/page/index-0.js') }}"></script>
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

        $("#deletePegawai").click(function() {

        });

        function refreshPage() {
            // Reload the current page
            window.location.href = window.location.pathname;
        }

        function confirmDelete(url) {
            swal({
                title: 'Konfirmasi Hapus Pegawai',
                text: 'Anda akan menghapus pegawai tersebut ?',
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

    </script>
@endsection
