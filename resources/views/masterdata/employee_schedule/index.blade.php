@extends('layouts.main')

@section('title', 'Jadwal Kerja Pegawai')

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
        <h1>Jadwal Kerja Pegawai</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ url('/')}}">Dashboard</a></div>
            <div class="breadcrumb-item active">Jadwal Pegawai</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>
                            <a href="{{ route('jadwal-shift.create') }}" class="btn btn-primary" ><i class="fas fa-plus"></i> Tambah</a>
                            <button class="btn btn-secondary" onclick="refreshPage()"><i class="fas fa-history"></i> Reload</button>
                        </h4>
                        <div class="card-header-form">
                            <form action="{{ route('jadwal-shift.index') }}" method="GET">
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
                                <th >Nama</th>
                                <th >Tanggal</th>
                                <th class="text-center">Jam Masuk</th>
                                <th class="text-center">Jam Keluar</th>
                                <th class="text-center">Total</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Action</th>
                            </tr>

                            {{-- Panggil data divisi --}}
                            @foreach ($result as $i => $val)
                            <tr>
                                <td>{{ $val->employee->name }}</td>
                                <td>{{ App\Helper\LibHelper::formatTanggalHari($val->date) }}</td>

                                <td class="text-center">{{ $val->time_start }}</td>
                                <td class="text-center">{{ $val->time_end }}</td>
                                <td class="text-center">{{ $val->time_diff }}</td>
                                <td class="text-center">
                                    @if($val->status == 'Terjadwal')
                                        <div class="badge badge-info">Terjadwal</div>
                                    @else
                                        <div class="badge badge-success">Absen</div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href={{ url('jadwal-shift/edit/'.$val->id) }} class="btn btn-success btn-sm"><i class="fas fa-pencil-alt"></i></a>
                                    <a href={{ url('jadwal-shift/delete/'.$val->id) }} class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                            @endforeach
                            {{-- Panggil data divisi --}}

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
    {{-- <script src="{{ asset('node_modules/simpleweather/jquery.simpleWeather.min.js') }}"></script> --}}
    <script src="{{ asset('node_modules/chart.js/dist/Chart.min.js') }}"></script>
    <script src="{{ asset('node_modules/jqvmap/dist/jquery.vmap.min.js') }}"></script>
    <script src="{{ asset('node_modules/jqvmap/dist/maps/jquery.vmap.world.js') }}"></script>
    <script src="{{ asset('node_modules/summernote/dist/summernote-bs4.js') }}"></script>
    <script src="{{ asset('node_modules/chocolat/dist/js/jquery.chocolat.min.js') }}"></script>
    <script src="{{ asset('assets/js/page/index-0.js') }}"></script>
    <script src="{{ asset('node_modules/izitoast/dist/js/iziToast.min.js') }}"></script>

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
    </script>
@endsection
