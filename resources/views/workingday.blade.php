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
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header">
                            <h4>Jump To</h4>
                        </div>
                        <div class="card-body">
                            <ul class="nav nav-pills flex-column">
                                <li class="nav-item"><a href="{{route('setting.index')}}" class="nav-link">General</a></li>
                                <li class="nav-item"><a href="{{route('setting.working-day')}}" class="nav-link active">Jam Kerja</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="card" id="settings-card">
                        <div class="card-header">
                            <h4>Jam Kerja</h4>
                        </div>
                        <form method="POST" action="{{ route('setting.working-update') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                <p class="text-muted">Pengaturan Hari & Jam kerja Universal pegawai</p>
                                <div class="form-group row align-items-center">
                                    <label for="site-title" class="form-control-label col-sm-3 text-md-center">HARI</label>
                                    <label for="site-title" class="form-control-label col-sm-2 text-md-center">STATUS</label>
                                    <label for="site-title" class="form-control-label col-sm-2 text-md-center">JAM MASUK</label>
                                    <label for="site-title" class="form-control-label col-sm-2 text-md-center">JAM KELUAR</label>
                                    <label for="site-title" class="form-control-label col-sm-2 text-md-center">TOTAL JAM</label>
                                </div>
                                <div class="form-group row align-items-center">
                                    <label for="site-title" class="form-control-label col-sm-3 text-md-center">Senin</label>
                                    <div class="col-sm-6 col-md-2">
                                        <select class="form-control selectric @error('monday_type') is-invalid @enderror" name="monday_type"  required>
                                            <option value='' disabled {{ ($data->monday_type == null) ? 'selected' : '' }}>Pilih Tipe</option>
                                            <option value=1 {{ ($data->monday_type == '1') ? 'selected' : '' }}>Masuk</option>
                                            <option value=0 {{ ($data->monday_type == '0') ? 'selected' : '' }}>Libur</option>
                                        </select>
                                        @error('monday_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-sm-6 col-md-2">
                                        <input type="time" class="form-control @error('monday_in') is-invalid @enderror" name="monday_in" id="monday_in" value="{{ $data->monday_in }}"></input>
                                        @error('monday_in')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-sm-6 col-md-2">
                                        <input type="time" class="form-control @error('monday_out') is-invalid @enderror" name="monday_out" id="monday_out" value="{{ $data->monday_out }}"></input>
                                        @error('monday_out')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-sm-6 col-md-2">
                                        <input type="time" class="form-control @error('monday_total') is-invalid @enderror" name="monday_total" id="monday_total" value="{{ $data->monday_total }}" readonly></input>
                                        @error('monday_total')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row align-items-center">
                                    <label for="site-title" class="form-control-label col-sm-3 text-md-center">Selasa</label>
                                    <div class="col-sm-6 col-md-2">
                                        <select class="form-control selectric @error('tuesday_type') is-invalid @enderror" name="tuesday_type" required>
                                            <option value='' disabled {{ ($data->tuesday_type == null) ? 'selected' : '' }}>Pilih Tipe</option>
                                            <option value=1 {{ ($data->tuesday_type == '1') ? 'selected' : '' }}>Masuk</option>
                                            <option value=0 {{ ($data->tuesday_type == '0') ? 'selected' : '' }}>Libur</option>
                                        </select>
                                        @error('tuesday_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-sm-6 col-md-2">
                                        <input type="time" class="form-control @error('tuesday_in') is-invalid @enderror" name="tuesday_in" id="tuesday_in" value="{{ $data->tuesday_in }}" ></input>
                                        @error('tuesday_in')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-sm-6 col-md-2">
                                        <input type="time" class="form-control @error('tuesday_out') is-invalid @enderror" name="tuesday_out" id="tuesday_out" value="{{ $data->tuesday_out }}"></input>
                                        @error('tuesday_out')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-sm-6 col-md-2">
                                        <input type="time" class="form-control @error('tuesday_total') is-invalid @enderror" name="tuesday_total" id="tuesday_total" value="{{ $data->tuesday_total }}" readonly></input>
                                        @error('tuesday_total')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row align-items-center">
                                    <label for="site-title" class="form-control-label col-sm-3 text-md-center">Rabu</label>
                                    <div class="col-sm-6 col-md-2">
                                        <select class="form-control selectric @error('wednesday_type') is-invalid @enderror" name="wednesday_type" required>
                                            <option value='' disabled {{ ($data->wednesday_type == null) ? 'selected' : '' }}>Pilih Tipe</option>
                                            <option value=1 {{ ($data->wednesday_type == '1') ? 'selected' : '' }}>Masuk</option>
                                            <option value=0 {{ ($data->wednesday_type == '0') ? 'selected' : '' }}>Libur</option>
                                        </select>
                                        @error('wednesday_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-sm-6 col-md-2">
                                        <input type="time" class="form-control @error('wednesday_in') is-invalid @enderror" name="wednesday_in" id="wednesday_in" value="{{ $data->wednesday_in }}"></input>
                                        @error('wednesday_in')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-sm-6 col-md-2">
                                        <input type="time" class="form-control @error('wednesday_out') is-invalid @enderror" name="wednesday_out" id="wednesday_out" value="{{ $data->wednesday_out }}"></input>
                                        @error('wednesday_out')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-sm-6 col-md-2">
                                        <input type="time" class="form-control @error('wednesday_total') is-invalid @enderror" name="wednesday_total" id="wednesday_total" value="{{ $data->wednesday_total }}" readonly></input>
                                        @error('wednesday_total')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row align-items-center">
                                    <label for="site-title" class="form-control-label col-sm-3 text-md-center">Kamis</label>
                                    <div class="col-sm-6 col-md-2">
                                        <select class="form-control selectric @error('thursday_type') is-invalid @enderror" name="thursday_type" value="{{ ($data->thursday_type != null) ? $data->thursday_type : '' }}" required>
                                            <option value='' disabled {{ ($data->thursday_type == null) ? 'selected' : '' }}>Pilih Tipe</option>
                                            <option value=1 {{ ($data->thursday_type == '1') ? 'selected' : '' }}>Masuk</option>
                                            <option value=0 {{ ($data->thursday_type == '0') ? 'selected' : '' }}>Libur</option>
                                        </select>
                                        @error('thursday_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-sm-6 col-md-2">
                                        <input type="time" class="form-control @error('thursday_in') is-invalid @enderror" name="thursday_in" id="thursday_in" value="{{ $data->thursday_in }}"></input>
                                        @error('thursday_in')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-sm-6 col-md-2">
                                        <input type="time" class="form-control @error('thursday_out') is-invalid @enderror" name="thursday_out" id="thursday_out" value="{{ $data->thursday_out }}"></input>
                                        @error('thursday_out')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-sm-6 col-md-2">
                                        <input type="time" class="form-control @error('thursday_total') is-invalid @enderror" name="thursday_total" id="thursday_total" value="{{ $data->thursday_total }}" readonly></input>
                                        @error('thursday_total')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row align-items-center">
                                    <label for="site-title" class="form-control-label col-sm-3 text-md-center">Jum'at</label>
                                    <div class="col-sm-6 col-md-2">
                                        <select class="form-control selectric @error('friday_type') is-invalid @enderror" name="friday_type" required>
                                            <option value='' disabled {{ ($data->friday_type == null) ? 'selected' : '' }}>Pilih Tipe</option>
                                            <option value=1 {{ ($data->friday_type == '1') ? 'selected' : '' }}>Masuk</option>
                                            <option value=0 {{ ($data->friday_type == '0') ? 'selected' : '' }}>Libur</option>
                                        </select>
                                        @error('friday_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-sm-6 col-md-2">
                                        <input type="time" class="form-control @error('friday_in') is-invalid @enderror" name="friday_in" id="friday_in" value="{{ $data->friday_in }}"></input>
                                        @error('friday_in')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-sm-6 col-md-2">
                                        <input type="time" class="form-control @error('friday_out') is-invalid @enderror" name="friday_out" id="friday_out" value="{{ $data->friday_out }}"></input>
                                        @error('friday_out')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-sm-6 col-md-2">
                                        <input type="time" class="form-control @error('friday_total') is-invalid @enderror" name="friday_total" id="friday_total" value="{{ $data->friday_total }}" readonly></input>
                                        @error('friday_total')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row align-items-center">
                                    <label for="site-title" class="form-control-label col-sm-3 text-md-center">Sabtu</label>
                                    <div class="col-sm-6 col-md-2">
                                        <select class="form-control selectric @error('saturday_type') is-invalid @enderror" name="saturday_type" required>
                                            <option value='' disabled {{ ($data->saturday_type == null) ? 'selected' : '' }}>Pilih Tipe</option>
                                            <option value=1 {{ ($data->saturday_type == '1') ? 'selected' : '' }}>Masuk</option>
                                            <option value=0 {{ ($data->saturday_type == '0') ? 'selected' : '' }}>Libur</option>

                                        </select>
                                        @error('saturday_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-sm-6 col-md-2">
                                        <input type="time" class="form-control @error('saturday_in') is-invalid @enderror" name="saturday_in" id="saturday_in" >{{ $data->saturday_in }}</input>
                                        @error('saturday_in')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-sm-6 col-md-2">
                                        <input type="time" class="form-control @error('saturday_out') is-invalid @enderror" name="saturday_out" id="saturday_out" >{{ $data->saturday_out }}</input>
                                        @error('saturday_out')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-sm-6 col-md-2">
                                        <input type="time" class="form-control @error('saturday_total') is-invalid @enderror" name="saturday_total" id="saturday_total" readonly>{{ $data->saturday_total }}</input>
                                        @error('saturday_total')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row align-items-center">
                                    <label for="site-title" class="form-control-label col-sm-3 text-md-center">Minggu</label>
                                    <div class="col-sm-6 col-md-2">
                                        <select class="form-control selectric @error('sunday_type') is-invalid @enderror" name="sunday_type" required>
                                            <option value='' disabled {{ ($data->sunday_type == null) ? 'selected' : '' }}>Pilih Tipe</option>
                                            <option value=1 {{ ($data->sunday_type == '1') ? 'selected' : '' }}>Masuk</option>
                                            <option value=0 {{ ($data->sunday_type == '0') ? 'selected' : '' }}>Libur</option>
                                        </select>
                                        @error('sunday_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-sm-6 col-md-2">
                                        <input type="time" class="form-control @error('sunday_in') is-invalid @enderror" name="sunday_in" id="sunday_in" value="{{ $data->sunday_in }}"></input>
                                        @error('sunday_in')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-sm-6 col-md-2">
                                        <input type="time" class="form-control @error('sunday_out') is-invalid @enderror" name="sunday_out" id="sunday_out" value="{{ $data->sunday_out }}"></input>
                                        @error('sunday_out')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-sm-6 col-md-2">
                                        <input type="time" class="form-control @error('sunday_total') is-invalid @enderror" name="sunday_total" id="sunday_total" value="{{ $data->sunday_total }}" readonly></input>
                                        @error('sunday_total')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
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

            $("#sunday_in, #sunday_out").change(function() { checkChangeTimeBetween('sunday') });
            $("#monday_in, #monday_out").change(function() { checkChangeTimeBetween('monday') });
            $("#thuesday_in, #tuesday_out").change(function() { checkChangeTimeBetween('tuesday') });
            $("#wednesday_in, #wednesday_out").change(function() { checkChangeTimeBetween('wednesday') });
            $("#thursday_in, #thursday_out").change(function() { checkChangeTimeBetween('thursday') });
            $("#friday_in, #friday_out").change(function() { checkChangeTimeBetween('friday') });
            $("#saturday_in, #saturday_out").change(function() { checkChangeTimeBetween('saturday') });

        });

        function checkChangeTimeBetween(elm) {
            let start_work = $("#"+elm+"_in").val();
            let end_work = $("#"+elm+"_out").val();

            if (start_work != '' && end_work != '') {
                total_time = betweenTime(start_work, end_work);
                $("#"+elm+"_total").val(total_time);
            }
        }

    </script>

@endsection
