@extends('layouts.default')

@section('title', 'Autopartes TB')

@section('content')
    <div class="relative flex h-[250px] w-full items-end justify-center sm:h-[300px] md:h-[400px]">
        <div class="absolute inset-0 w-full h-full z-10"
            style="background: linear-gradient(90deg, rgba(0, 0, 0, 0.40) 0%, rgba(0, 0, 0, 0.00) 100%), url('{{ $banner->image ?? '' }}') lightgray 50% / cover no-repeat;">
        </div>
        <div
            class="absolute top-16 z-40 mx-auto w-full max-w-[1200px] px-4 text-[12px] text-white sm:top-10 md:top-10 lg:px-0">
            <a class="font-bold" href="{{ route('home') }}">
                Inicio
            </a>
            /
            <a href="{{ route('productos') }}">
                Productos
            </a>
        </div>

        <img class="absolute h-full w-full object-cover object-center" src="{{ $banner->image ?? '' }}"
            alt="Banner nosotros" />

        <h2 class="absolute z-10 mx-auto w-[1200px] pb-20 text-3xl font-bold text-white sm:text-4xl">
            Productos
        </h2>
    </div>

    <x-search-bar :terminaciones="$terminaciones" :materiales="$materiales" :categorias="$categorias"
        :subcategorias="$subcategorias" />

    <div class="w-[1200px] mx-auto grid grid-cols-4 gap-5 my-10">
        @foreach ($categorias as $categoria)
            <a href="{{route("productos.categorias", ["id" => $categoria->id])}}"
                class="h-[350px] max-w-[288px] border flex flex-col rounded-lg shadow-sm">
                <div class="w-full min-h-[250px] rounded-t-lg">
                    <img src="{{ $categoria->image ?? "" }}" alt="{{ $categoria->name ?? ""}}"
                        class="h-full w-full object-cover rounded-t-lg">
                </div>
                <div class="w-full h-full flex justify-center items-center border-t border-[1px] px-2">
                    <p class="text-primary-orange text-lg font-bold uppercase">{{ $categoria->name ?? "" }}</p>
                </div>
            </a>
        @endforeach

    </div>
@endsection