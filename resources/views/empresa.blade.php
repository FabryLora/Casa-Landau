@extends('layouts.default')

@section('title', 'Empresa - Autopartes TB')

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
            <a href="{{ route('nosotros') }}">
                Nosotros
            </a>
        </div>

        <img class="absolute h-full w-full object-cover object-center" src="{{ $banner->image ?? '' }}"
            alt="Banner nosotros" />

        <h2 class="absolute z-10 mx-auto w-[1200px] pb-20 text-3xl font-bold text-white sm:text-4xl">
            Nosotros
        </h2>
    </div>
    <div
        class="mx-auto flex w-full max-w-[1200px] h-full flex-col gap-6 px-4 py-10 sm:gap-8 sm:py-16 lg:flex-row lg:gap-10 lg:px-0 lg:py-20">
        <div class="h-full w-full py-4 lg:py-10">
            <div class="flex flex-col gap-4 lg:gap-6">
                <h2 class="text-2xl font-bold sm:text-3xl">{{ $nosotros->title ?? null }}</h2>
                <div class="" {!! $nosotros->text ?? null !!}></div>
            </div>
        </div>
        <div class="h-[514px] w-full ">
            <img class="h-full w-full object-cover" src="{{ $nosotros->image ?? null }}" alt="Imagen nosotros">
        </div>

    </div>

    <div className="max-w-[1200px] mx-auto flex justify-center h-[688px]">
        <video className="w-full h-full object-cover rounded-lg" src="{{$nosotros->video ?? ""}}" controls></video>
    </div>




@endsection