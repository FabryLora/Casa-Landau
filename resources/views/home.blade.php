@extends('layouts.default')

@section('title', 'Casa Landau')

@section('description', $metadatos->description ?? "")
@section('keywords', $metadatos->keywords ?? "")

@section('content')
    <x-slider :sliders="$sliders" />
    <x-search-bar :terminaciones="$terminaciones" :materiales="$materiales" :categorias="$categorias"
        :subcategorias="$subcategorias" />
    <x-productos-destacados :productos="$productos" />
    <x-banner-portada :bannerPortada="$bannerPortada" />
    <x-novedades-inicio :novedades="$novedades" />
@endsection