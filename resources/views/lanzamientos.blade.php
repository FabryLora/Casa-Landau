@extends('layouts.default')

@section('title', 'Novedades - Autopartes TB')

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
            <a href="{{ route('novedades') }}">
                Novedades
            </a>
        </div>

        <img class="absolute h-full w-full object-cover object-center" src="{{ $banner->image ?? '' }}"
            alt="Banner nosotros" />

        <h2 class="absolute z-10 mx-auto w-[1200px] pb-20 text-3xl font-bold text-white sm:text-4xl">
            Novedades
        </h2>
    </div>
    <div class="w-[1200px] mx-auto my-20">
        <div class="grid grid-cols-3  gap-6">
            @foreach($novedades as $novedad)
                <a href="{{ url('/novedades/' . $novedad->id) }}"
                    class="flex flex-col gap-2 max-w-[392px] max-sm:max-w-full h-fit max-sm:h-auto border shadow-md rounded-lg transition transform hover:-translate-y-1 hover:shadow-lg duration-300">
                    <div class="max-w-[391px] max-sm:max-w-full min-h-[321px] h-[260px] rounded-t-lg">
                        <img src="{{ $novedad->image }}" alt="{{ $novedad->title }}"
                            class="h-full w-full object-cover rounded-t-lg object-center">
                    </div>
                    <div class="flex h-full flex-col justify-between max-sm:p-3 p-4 ">
                        <div class="flex flex-col gap-2">
                            <p class="text-primary-orange text-sm max-sm:text-xs font-bold uppercase">{{ $novedad->type }}
                            </p>
                            <div>
                                <p class="text-xl font-bold sm:text-2xl max-sm:text-lg">{{ $novedad->title }}</p>
                                <div class="line-clamp-3 overflow-hidden text=[#252525] break-words max-sm:text-sm">
                                    {!! $novedad->text !!}
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 max-sm:mt-3 flex flex-row items-center justify-between text-[#252525]">
                            <p class="font-bold text-[16px] max-sm:text-sm">Leer más</p>

                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>

@endsection