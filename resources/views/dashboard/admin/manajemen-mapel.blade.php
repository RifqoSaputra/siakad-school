@extends('layouts.template')

@section('title', 'Data Mata Pelajaran')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Data Mata Pelajaran</h1>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Daftar Mata Pelajaran</h6>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Kode Mapel</th>
                            <th>Nama Mapel</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($mapel as $index => $m)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                {{-- SESUAIKAN nama kolom di tabel mapel --}}
                                <td>{{ $m->kode_mapel ?? '-' }}</td>
                                <td>{{ $m->nama_mapel ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">Belum ada data mata pelajaran</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
