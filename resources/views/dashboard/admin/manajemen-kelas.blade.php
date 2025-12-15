@extends('layouts.template')

@section('title', 'Data Kelas')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Data Kelas</h1>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Daftar Kelas</h6>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tingkat</th>
                            <th>Nama Kelas</th>
                            <th>Tahun Ajaran</th>
                            <th>Semester</th>
                            <th>Wali Kelas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($kelas as $i => $k)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $k->tingkat_kelas }}</td>
                                <td>{{ $k->nama_kelas }}</td>
                                <td>{{ $k->tahun_ajaran }}</td>
                                <td>{{ $k->semester }}</td>

                                {{-- walikelas berisi ID guru, jadi tampilkan langsung dulu --}}
                                <td>{{ $k->walikelas ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Belum ada data kelas</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
