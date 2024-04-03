@extends('layouts.main')

@section('title', 'Tambah Divisi')

@section('stylesheet')
    <link rel="stylesheet" href="{{ asset('node_modules/summernote/dist/summernote-bs4.css') }}">
    <link rel="stylesheet" href="{{ asset('node_modules/selectric/public/selectric.css') }}">
    <link rel="stylesheet" href="{{ asset('node_modules/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('node_modules/bootstrap-tagsinput/dist/bootstrap-tagsinput.css') }}">
@endsection

@section('content')
<section class="section">
    <div class="section-header">
        <div class="section-header-back">
            <a href="{{ url('/jadwal-shift')}}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
        </div>
        <h1>Tambah Jadwal Pegawai</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ url('/')}}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ url('/divisi')}}">Jadwal Pegawai</a></div>
            <div class="breadcrumb-item active">Tambah Jadwal</div>
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
                        <form method="POST" action="{{ route('jadwal-shift.store') }}">
                            @csrf
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Nama Pegawai<small>*</small></label>
                                <div class="col-sm-12 col-md-8">
                                    <select class="form-control select2 @error('employee_id') is-invalid @enderror" name="employee_id" id="employee_id">
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
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Hari<small>*</small></label>
                                <div class="col-sm-12 col-md-8">
                                    <select class="form-control selectric @error('day') is-invalid @enderror" name="day">
                                        <option selected disabled>Pilih Hari</option>
                                        <option value='Monday'>Senin</option>
                                        <option value='Tuesday'>Selasa</option>
                                        <option value='Wednesday'>Rabu</option>
                                        <option value='Thursday'>Kamis</option>
                                        <option value='Friday'>Jumat</option>
                                        <option value='Saturday'>Sabtu</option>
                                        <option value='Sunday'>Minggu</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Jam Kerja <br/><small>Jika Libur jam tidak perlu di isi*</small></label>
                                <div class="col-sm-6 col-md-2">
                                    <input type="time" class="form-control @error('time_start') is-invalid @enderror" name="time_start" id="time_start">
                                    @error('time_start')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <label class="col-form-label text-md-center col-12 col-md-3 col-lg-1">s.d</label>
                                <div class="col-sm-6 col-md-2">
                                    <input type="time" class="form-control @error('time_end') is-invalid @enderror" name="time_end" id="time_end">
                                    @error('time_end')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <label class="col-form-label text-md-center col-12 col-md-3 col-lg-1">Total</label>
                                <div class="col-sm-6 col-md-2">
                                    <input type="time" class="form-control @error('time_diff') is-invalid @enderror" name="time_diff" id="time_diff" readonly>
                                    @error('time_diff')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                                <div class="col-sm-12 col-md-7">
                                    <button class="btn btn-primary">Tambah Jadwal</button>
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
            $("#time_start, #time_end").change(function() {
                let time_start = $("#time_start").val();
                let time_end = $("#time_end").val();

                if (time_start != '' && time_end != '') {
                    time_diff = betweenTime(time_start, time_end);
                    console.log(time_diff);
                    $("#time_diff").val(time_diff);
                }
            })
        });
    </script>
@endsection
