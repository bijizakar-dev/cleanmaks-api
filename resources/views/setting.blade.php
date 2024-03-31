@extends('layouts.main')

@section('title', 'Tambah Cuti')

@section('stylesheet')
    <link rel="stylesheet" href="{{ asset('node_modules/summernote/dist/summernote-bs4.css') }}">
    <link rel="stylesheet" href="{{ asset('node_modules/selectric/public/selectric.css') }}">
    <link rel="stylesheet" href="{{ asset('node_modules/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('node_modules/bootstrap-tagsinput/dist/bootstrap-tagsinput.css') }}">
    <style>
        #image-preview {
            width: 300px;
            height: 200px;
            border: 1px solid #ccc;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #image-label {
            cursor: pointer;
        }
    </style>
@endsection

@section('content')
    <section class="section">
        <div class="section-header">
            <div class="section-header-back">
                <a href="{{ route('dashboard') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
            </div>
            <h1>Setting App</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                <div class="breadcrumb-item active"><a href="#">Profile</a></div>
                <div class="breadcrumb-item">Setting App</div>
            </div>
        </div>

        <div class="section-body">
            <div id="output-status"></div>
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h4>Jump To</h4>
                        </div>
                        <div class="card-body">
                            <ul class="nav nav-pills flex-column">
                                <li class="nav-item"><a href="#" class="nav-link active">General</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card" id="settings-card">
                        <div class="card-header">
                            <h4>General Settings</h4>
                        </div>
                        <form method="POST" action="{{ url('/setting-update') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                <p class="text-muted">Pengaturan Aplikasi HRIS Perusahaan</p>
                                <div class="form-group row align-items-center">
                                    <label for="site-title" class="form-control-label col-sm-3 text-md-right">Nama Perusahaan</label>
                                    <div class="col-sm-6 col-md-9">
                                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="name" value="{{$data->name ?? ''}}" placeholder="Nama Perusahaan">
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row align-items-center">
                                    <label for="site-description" class="form-control-label col-sm-3 text-md-right">Alamat</label>
                                    <div class="col-sm-6 col-md-9">
                                        <textarea class="form-control @error('address') is-invalid @enderror" name="address" id="address" placeholder="Alamat Perusahaan">{{ $data->address ?? ''}}</textarea>
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row align-items-center">
                                    <label for="site-title" class="form-control-label col-sm-3 text-md-right">No. Telp</label>
                                    <div class="col-sm-6 col-md-9">
                                        <input type="text" name="phone_number" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" value="{{$data->phone_number ?? ''}}" placeholder="No. Telp Perusahaan">
                                        @error('phone_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row align-items-center">
                                    <label for="site-title" class="form-control-label col-sm-3 text-md-right">Latitude</label>
                                    <div class="col-sm-6 col-md-9">
                                        <input type="text" name="latitude" class="form-control @error('latitude') is-invalid @enderror" id="latitude" value="{{$data->latitude ?? ''}}" placeholder="Latitude Lokasi Perusahaan">
                                        @error('latitude')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row align-items-center">
                                    <label for="site-title" class="form-control-label col-sm-3 text-md-right">Longitude</label>
                                    <div class="col-sm-6 col-md-9">
                                        <input type="text" name="longitude" class="form-control @error('longitude') is-invalid @enderror" id="longitude" value="{{$data->longitude ?? ''}}" placeholder="Longitude Lokasi Perusahaan">
                                        @error('longitude')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row align-items-center">
                                    <label for="site-title" class="form-control-label col-sm-3 text-md-right">Jam Kerja</label>
                                    <div class="col-sm-6 col-md-2">
                                        <input type="hidden" name="working_hour" id="working_hour" value="{{$data->working_hour ?? '00:00:00'}}">
                                        <input type="time" class="form-control @error('working_hour') is-invalid @enderror" id="working_hour_label" value="{{$data->working_hour ?? ''}}" placeholder="Jam Kerja" disabled>
                                        @error('working_hour')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <label for="site-title" class="form-control-label col-sm-2 text-md-right">Kode Absensi</label>
                                    <div class="col-sm-6 col-md-5">
                                        <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" id="code" value="{{$data->code ?? ''}}" placeholder="Kode QR Scan Absensi">
                                        @error('code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row align-items-center">
                                    <label for="site-title" class="form-control-label col-sm-3 text-md-right">Jam Masuk</label>
                                    <div class="col-sm-6 col-md-2">
                                        <input type="time" name="time_in" class="form-control @error('time_in') is-invalid @enderror" id="time_in" value="{{$data->time_in ?? ''}}" placeholder="Jam Masuk">
                                        @error('time_in')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <label for="site-title" class="form-control-label col-sm-2 text-md-right">Jam Pulang</label>
                                    <div class="col-sm-6 col-md-2">
                                        <input type="time" name="time_out" class="form-control @error('time_out') is-invalid @enderror" id="time_out" value="{{$data->time_out ?? ''}}" placeholder="Jam Pulang">
                                        @error('time_out')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row align-items-center">
                                    <label class="form-control-label col-sm-3 text-md-right">Logo</label>
                                    <div class="col-sm-6 col-md-9">
                                        <div class="custom-file">
                                            <input type="file" name="logo" class="custom-file-input @error('logo') is-invalid @enderror" id="site-logo" value="{{ $data->logo ?? '' }}">
                                            @error('logo')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <label class="custom-file-label">{{ $data->logo ?? 'Choose File' }}</label>
                                        </div>
                                        <div class="form-text text-muted">The image must have a maximum size of 1MB</div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-whitesmoke text-md-right">
                                <button class="btn btn-primary" id="save-btn">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            </div>
    </section>
@endsection

@section('libraries')
    <script src="{{ asset('node_modules/summernote/dist/summernote-bs4.js') }}"></script>
    <script src="{{ asset('node_modules/selectric/public/jquery.selectric.min.js') }}"></script>
    <script src="{{ asset('node_modules/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('node_modules/jquery_upload_preview/assets/js/jquery.uploadPreview.min.js') }}"></script>
    <script src="{{ asset('node_modules/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js') }}"></script>
    <script src="{{ asset('../assets/js/page/index-0.js') }}"></script>
    <script src="{{ asset('../assets/js/lib_helper.js') }}"></script>

    <script>
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
            // Fungsi untuk menangani perubahan pada input file
            $("#image-upload").change(function() {
                readURL(this);
            });

            // Fungsi untuk menampilkan preview gambar
            function readURL(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function(e) {
                        $("#image-preview").css("background-image", "url(" + e.target.result + ")");
                        $("#image-label").hide();
                    };

                    reader.readAsDataURL(input.files[0]);
                }
            }

            $("#time_in, #time_out").change(function() {
                let start_work = $("#time_in").val();
                let end_work = $("#time_out").val();

                if (start_work != '' && end_work != '') {
                    total_time = betweenTime(start_work, end_work);
                    $("#working_hour").val(total_time);
                    $("#working_hour_label").val(total_time);
                }
             });
        });
    </script>

@endsection
