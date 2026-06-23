@extends('layouts.app')

@section('title', 'Detail Pengguna: ' . $user->nama_lengkap)

@section('content')
<div class="page-header d-flex justify-content-between">
    <div>
        <h1><i class="bi bi-person"></i> {{ $user->nama_lengkap }}</h1>
        <p>Username: <code>{{ $user->username }}</code></p>
    </div>
    <a href="{{ route('users.edit', $user->user_id) }}" class="btn btn-warning">Edit</a>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><h5>Informasi Pengguna</h5></div>
            <div class="card-body">
                <p><strong>Username:</strong> {{ $user->username }}</p>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Nama Lengkap:</strong> {{ $user->nama_lengkap }}</p>
                <p><strong>Role:</strong> <span class="badge bg-info">{{ ucwords(str_replace('_', ' ', $user->role)) }}</span></p>
                <p><strong>Status:</strong> <span class="badge bg-{{ $user->status == 'aktif' ? 'success' : 'danger' }}">{{ ucfirst($user->status) }}</span></p>
                <p><strong>Terdaftar:</strong> {{ $user->created_at->format('d M Y, H:i') }}</p>
            </div>
        </div>
    </div>
</div>
<a href="{{ route('users.index') }}" class="btn btn-secondary mt-3">Kembali</a>
@endsection
