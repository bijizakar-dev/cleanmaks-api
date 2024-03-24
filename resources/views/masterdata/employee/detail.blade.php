@extends('layouts.main')

@section('title', 'Detail Pegawai')

@section('stylesheet')
    <link rel="stylesheet" href="{{ asset('node_modules/summernote/dist/summernote-bs4.css') }}">
    <link rel="stylesheet" href="{{ asset('node_modules/selectric/public/selectric.css') }}">
    <link rel="stylesheet" href="{{ asset('node_modules/bootstrap-tagsinput/dist/bootstrap-tagsinput.css') }}">
    <link rel="stylesheet" href="{{ asset('node_modules/izitoast/dist/css/iziToast.min.css') }}">
    <link rel="stylesheet" href="{{ asset('../assets/css/components.css') }}">

    <style>
        #image-preview {
            width: 300px;
            height: 200px;
            border: 0px solid #ccc;
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
        <h1>Detail Pegawai</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ url('/')}}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ url('/employees')}}">Employee</a></div>
            <div class="breadcrumb-item active">Detail Pegawai</div>
        </div>
    </div>
    <div class="section-body">

      <div class="row mt-sm-2">
        <div class="col-12 col-md-12 col-lg-12">
          <div class="card profile-widget">
            <div class="profile-widget-header">
                @if ($result->photo)
                    <img alt="image" src="{{ asset($result->photo) }}" class="rounded-circle profile-widget-picture" width="100px" height="100px">
                @else
                    <img alt="image" src="{{ asset('/assets/img/avatar/avatar-5.png') }}" class="rounded-circle profile-widget-picture" width="100px" height="100px">
                @endif
              <div class="profile-widget-items">
                <div class="profile-widget-item">
                  <div class="profile-widget-item-label">Posts</div>
                  <div class="profile-widget-item-value">187</div>
                </div>
                <div class="profile-widget-item">
                  <div class="profile-widget-item-label">Followers</div>
                  <div class="profile-widget-item-value">6,8K</div>
                </div>
                <div class="profile-widget-item">
                  <div class="profile-widget-item-label">Following</div>
                  <div class="profile-widget-item-value">2,1K</div>
                </div>
              </div>
            </div>
            <div class="profile-widget-description">
              <div class="profile-widget-name">{{$result->name}} <div class="text-muted d-inline font-weight-normal"><div class="slash"></div> {{$result->jabatan->name}} {{$result->divisi->name}}</div></div>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-12 col-lg-12">
            <form method="POST" action="{{ route('employees.update', $result->id) }}" enctype="multipart/form-data">
                @csrf
                <div class="card">
                    <div class="card-header">
                        <h4>Biodata Pegawai</h4>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-7 col-12">
                                <label>Nama Lengkap</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" placeholder="Nama Lengkap" value="{{$result->name}}">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group col-md-5 col-12">
                                <label>Jenis Kelamin</label>
                                <select class="form-control selectric @error('gender') is-invalid @enderror" name="gender" value="{{$result->gender}}">
                                    <option disabled>Pilih Jenis Kelamin..</option>
                                    <option value="M" {{ $result->gender == 'M' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="F" {{ $result->gender == 'F' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-7 col-12">
                                <label>Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" placeholder="Alamat Email" value="{{$result->email}}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group col-md-5 col-12">
                                <label>No. Telfon</label>
                                <input type="text" class="form-control  @error('phone') is-invalid @enderror" name="phone" placeholder="Nomor Telfon" value="{{$result->phone}}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6 col-12">
                                <label>Divisi / Unit</label>
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
                            <div class="form-group col-md-6 col-12">
                                <label>Jabatan</label>
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
                        <div class="row">
                            <div class="form-group col-md-6 col-12">
                                <label>Alamat</label>
                                <textarea class="summernote-simple @error('address') is-invalid @enderror" name="address">{{$result->address}}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group col-md-6 col-12">
                                <label>Foto</label>
                                <div id="image-preview" class="image-preview">
                                        @if ($result->photo)
                                            <img alt="image" id="image-prev" src="{{ asset($result->photo) }}" class="rounded-circle profile-widget-picture" width="200px" height="200px">
                                        @else
                                            <label for="image-upload" id="image-label">
                                                Choose File
                                            </label>
                                        @endif
                                    <input type="file" name="photo" id="image-upload" class="@error('photo') is-invalid @enderror" value="{{$result->photo}}"/>
                                    @error('photo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SETTING PEGAWAI --}}
                    <div class="card-header">
                        <h4>Setting Pegawai</h4>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-5 col-12">
                                <label>Status Pegawai</label>
                                <select name="is_verified" class="form-control selectric @error('is_verified') is-invalid @enderror" value="{{$result->is_verified}}">
                                    <option value="1" {{ $result->is_verified == 1 ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ $result->is_verified == 0 ? 'selected' : '' }}>Non Aktif</option>
                                </select>
                                @error('is_verified')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group col-md-3 col-12">
                                <label>Kuota Cuti Tahunan</label>
                                <input type="number" class="form-control @error('quota_cuti') is-invalid @enderror" name="quota_cuti" placeholder="Jumlah Hari" value="{{ $result->employeeCuti ? $result->employeeCuti->quota : 0 }}">

                                @error('quota_cuti')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="card-footer text-right">
                        <button class="btn btn-primary">Save Changes</button>
                    </div>
                </div>
            </form>
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
    <script src="{{ asset('node_modules/izitoast/dist/js/iziToast.min.js') }}"></script>
    <script src="{{ asset('../assets/js/page/index-0.js') }}"></script>

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
                        $("#image-preview").attr("class", "rounded-circle profile-widget-picture");
                        $("#image-preview").css("width", "200px");
                        $("#image-preview").css("height", "200px");
                        $("#image-label").hide(); // Sembunyikan label "Choose File"
                    };

                    reader.readAsDataURL(input.files[0]);
                }
            }
        });
    </script>

@endsection
