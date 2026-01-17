@extends('layouts.admin')

@section('title', 'Invoice Management')

@section('content')
<div class="container-fluid px-4 py-4">
    @livewire('admin.invoice.index')
</div>
@endsection
