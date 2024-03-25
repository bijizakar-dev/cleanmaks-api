@extends('layouts.main')

@section('title', 'Tambah Pegawai')

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
            <a href="{{ url('/cuti')}}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
        </div>
        <h1>Tambah Cuti</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ url('/')}}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ url('/cuti')}}">Cuti</a></div>
            <div class="breadcrumb-item active">Create Cuti</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Silahkan isi dan lengkapi form dibawah ini : </h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('izin.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Pegawai Pengaju</label>
                                <div class="col-sm-12 col-md-7">
                                    <select class="form-control select2 @error('employee_id_applicant') is-invalid @enderror" name="employee_id_applicant" id="employee_id_applicant">
                                        <option selected disabled>Pilih Pegawai..</option>
                                        @foreach($employee as $key => $val)
                                            <option value="{{ $val->id }}" data-email="{{ $val->email }}">{{ $val->name }} ( {{$val->divisi->name}})</option>
                                        @endforeach
                                    </select>
                                    @error('employee_id_applicant')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Pegawai Pengganti</label>
                                <div class="col-sm-12 col-md-7">
                                    <select class="form-control select2 @error('employee_id_replacement') is-invalid @enderror" name="employee_id_replacement" id="employee_id_replacement">
                                        <option selected disabled>Pilih Pegawai..</option>
                                        @foreach($employee as $key => $val)
                                            <option value="{{ $val->id }}" data-email="{{ $val->email }}">{{ $val->name }} ( {{$val->divisi->name}})</option>
                                        @endforeach
                                    </select>
                                    @error('employee_id_replacement')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Tanggal Cuti <br/><small>Jika 1 hari input tanggal yang sama*</small></label>
                                <div class="col-sm-6 col-md-2">
                                    <input type="date" class="form-control @error('start_date') is-invalid @enderror" name="start_date" id="start_date">
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <label class="col-form-label text-md-center col-12 col-md-3 col-lg-1">s.d</label>
                                <div class="col-sm-6 col-md-2">
                                    <input type="date" class="form-control @error('end_date') is-invalid @enderror" name="end_date" id="end_date">
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <label class="col-form-label text-md-center col-12 col-md-3 col-lg-1">Lama</label>
                                <div class="col-sm-6 col-md-1">
                                    <input type="number" class="form-control @error('total') is-invalid @enderror" name="total" id="total" value="0" readonly>
                                    @error('total')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Tipe</label>
                                <div class="col-sm-12 col-md-7">
                                    <select name="type" class="form-control selectric @error('type') is-invalid @enderror">
                                        <option selected disabled>Pilih Tipe..</option>
                                        @foreach($type as $key => $val)
                                            <option value="{{ $val->id }}">{{ $val->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row mb-1">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Alasan</label>
                                <div class="col-sm-12 col-md-7">
                                    <textarea type="text" class="form-control summernote-simple @error('reason') is-invalid @enderror" name="reason" id="reason" placeholder="Alasan pengajuan izin"></textarea>
                                    @error('reason')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">File Pendukung<br/><small>png, jpg, jpeg*</small></label>
                                <div class="col-sm-12 col-md-7">
                                    <div id="image-preview" class="image-preview">
                                        <label for="image-upload" id="image-label">Choose File</label>
                                        <input type="file" name="image" id="image-upload" style="width: 100px" class="@error('image') is-invalid @enderror"/>
                                        @error('image')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                                <div class="col-sm-12 col-md-7">
                                    <button class="btn btn-primary">Ajukan Perizinan</button>
                                </div>
                            </div>
                        </form>
                    </div>
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

            $("#start_date, #end_date").change(function() {
                var start_date = $("#start_date").val();
                var end_date = $("#end_date").val();
                if (start_date != '' && end_date != '') {
                    total_date = betweenWorkingDate(start_date, end_date);
                    $("#total").val(total_date);
                }
            })
        });
    </script>

@endsection
