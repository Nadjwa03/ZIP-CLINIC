@extends('layouts.admin')

@section('title', 'Invoice Detail')

@section('content')
    @livewire('admin.invoice.show', ['invoiceId' => $invoiceId])
@endsection
