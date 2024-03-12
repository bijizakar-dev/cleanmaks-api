@extends('layouts.main')

@section('stylesheet')
    <link rel="stylesheet" href="{{ asset('node_modules/summernote/dist/summernote-bs4.css') }}">
    <link rel="stylesheet" href="{{ asset('node_modules/selectric/public/selectric.css') }}">
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
            <a href="{{ url('/employees')}}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
        </div>
        <h1>Edit Pegawai</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ url('/')}}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ url('/employees')}}">Employee</a></div>
            <div class="breadcrumb-item active">Edit Pegawai</div>
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
                        <form method="POST" action="{{ route('employees.update', $result->id) }}" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Nama</label>
                                <div class="col-sm-12 col-md-7">
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" placeholder="Nama Lengkap" value="{{$result->name}}">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Jenis Kelamin</label>
                                <div class="col-sm-12 col-md-7">
                                    <select class="form-control selectric @error('gender') is-invalid @enderror" name="gender" value="{{$result->gender}}">
                                        <option disabled>Pilih Divisi..</option>
                                        <option value="M" {{ $result->gender == 'M' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="F" {{ $result->gender == 'F' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                    @error('gender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">E-mail</label>
                                <div class="col-sm-12 col-md-7">
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" placeholder="Alamat Email" value="{{$result->email}}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Divisi</label>
                                <div class="col-sm-12 col-md-7">
                                    <select class="form-control selectric @error('unit_id') is-invalid @enderror" name="unit_id"">
                                        <option disabled>Pilih Divisi..</option>
                                        @foreach($divisi as $id => $name)
                                            <option value="{{ $id }}" {{ $id == $result->unit_id ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @error('unit_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Jabatan</label>
                                <div class="col-sm-12 col-md-7">
                                    <select class="form-control selectric @error('jabatan_id') is-invalid @enderror" name="jabatan_id">
                                        <option disabled>Pilih Jabatan..</option>
                                        @foreach($jabatan as $id => $name)
                                            <option value="{{ $id }}" {{ $id == $result->jabatan_id ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @error('jabatan_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">No. Telp</label>
                                <div class="col-sm-12 col-md-7">
                                    <input type="text" class="form-control  @error('phone') is-invalid @enderror" name="phone" placeholder="Nomor Telfon" value="{{$result->phone}}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Alamat</label>
                                <div class="col-sm-12 col-md-7">
                                    <textarea class="summernote-simple @error('address') is-invalid @enderror" name="address">{{$result->address}}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Photo</label>
                                <div class="col-sm-12 col-md-7">
                                    <div id="image-preview" class="image-preview">
                                        <label for="image-upload" id="image-label">
                                            @if ($result->photo)
                                                <img src="{{ asset($result->photo) }}" alt="Preview" width="100%">
                                            @else
                                                Choose File
                                            @endif
                                        </label>
                                        <input type="file" name="photo" id="image-upload" class="@error('photo') is-invalid @enderror" value="{{$result->photo}}"/>
                                        @error('photo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Status</label>
                                <div class="col-sm-12 col-md-7">
                                    <select name="is_verified" class="form-control selectric @error('is_verified') is-invalid @enderror" value="{{$result->is_verified}}">
                                        <option value="1" {{ $result->is_verified == 1 ? 'selected' : '' }}>Aktif</option>
                                        <option value="0" {{ $result->is_verified == 0 ? 'selected' : '' }}>Non Aktif</option>
                                    </select>
                                    @error('is_verified')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                                <div class="col-sm-12 col-md-7">
                                    <button class="btn btn-primary" >Edit Pegawai</button>
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
    <script src="{{ asset('node_modules/jquery_upload_preview/assets/js/jquery.uploadPreview.min.js') }}"></script>
    <script src="{{ asset('node_modules/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js') }}"></script>
    <script src="{{ asset('../assets/js/page/index-0.js') }}"></script>

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
                        // Menetapkan sumber gambar pada elemen dengan ID image-preview
                        $("#image-preview").css("background-image", "url(" + e.target.result + ")");
                        $("#image-label").hide(); // Sembunyikan label "Choose File"
                    };

                    reader.readAsDataURL(input.files[0]);
                }
            }
        });
    </script>

@endsection
