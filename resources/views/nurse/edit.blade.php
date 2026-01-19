@extends('layouts.nurse')

@section('title', 'Input SOAP - Nurse Station')
@section('page-title', 'Input SOAP')

@section('content')
    <livewire:nurse.soap-input :visit-id="$visitId" />
@endsection