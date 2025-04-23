@extends('layouts.app')

@section('content')
@php
  $config = [
    'files'  => [ asset('storage/'.$file->path) ],
    'server' => [
      'url'     => route('files.photopea.save', $file),
      'formats' => ['psd','jpg'],
    ],
  ];
  $hash = rawurlencode(json_encode($config));
@endphp

<iframe
  src="https://www.photopea.com/#{{ $hash }}"
  style="width:100%; height:80vh; border:none;"
></iframe>

@endsection
