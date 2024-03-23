@extends('layouts.main')

@section('title', 'Edit Tipe Jenis')

@section('stylesheet')
    <link rel="stylesheet" href="{{ asset('node_modules/summernote/dist/summernote-bs4.css') }}">
    <link rel="stylesheet" href="{{ asset('node_modules/selectric/public/selectric.css') }}">
    <link rel="stylesheet" href="{{ asset('node_modules/bootstrap-tagsinput/dist/bootstrap-tagsinput.css') }}">
@endsection

@section('content')
<section class="section">
    <div class="section-header">
        <div class="section-header-back">
            <a href="{{ url('/jenis-type')}}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
        </div>
        <h1>Edit Tipe Jenis</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ url('/')}}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ url('/jenis-type')}}">Tipe Jenis</a></div>
            <div class="breadcrumb-item active">Edit Tipe Jenis</div>
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
                        <form method="POST" action="{{ route('jenis-type.update', $type->id) }}">
                            @csrf

                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Kategori<small>*</small></label>
                                <div class="col-sm-12 col-md-7">
                                    <select class="form-control selectric @error('category') is-invalid @enderror" name="category" value="{{$type->category}}">
                                        <option disabled>Pilih Kategori</option>
                                        <option value='Izin' {{ $type->category == 'Izin' ? 'selected' : '' }}>Izin</option>
                                        <option value='Cuti' {{ $type->category == 'Cuti' ? 'selected' : '' }}>Cuti</option>
                                        <option value='Lembur' {{ $type->category == 'Lembur' ? 'selected' : '' }}>Lembur</option>
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Nama<small>*</small></label>
                                <div class="col-sm-12 col-md-7">
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{$type->name}}">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Status<small>*</small></label>
                                <div class="col-sm-12 col-md-7">
                                    <select class="form-control selectric @error('status') is-invalid @enderror" name="status" value="{{$type->status}}">
                                        <option value='' disabled>Pilih Status</option>
                                        <option value='1' {{ $type->status == '1' ? 'selected' : '' }}>Aktif</option>
                                        <option value='0' {{ $type->status == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                                <div class="col-sm-12 col-md-7">
                                    <button class="btn btn-primary">Simpan</button>
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

@endsection
