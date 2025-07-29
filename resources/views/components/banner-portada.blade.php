<div class="flex flex-row h-[700px] max-md:h-fit my-10 bg-primary-orange max-md:flex-col text-white">
    <div class="w-full ">
        <img class="w-full h-full object-cover" src="{{ $bannerPortada->image ?? '' }}" alt="" />
    </div>



    <div class="flex flex-col w-full max-md:items-center max-md:justify-center   ">
        <div class="flex flex-col h-full px-20 pt-20 max-md:py-0 max-md:px-2 gap-10 max-md:gap-4">
            <h2 class="text-white text-[32px] font-bold max-md:text-center">
                {{ $bannerPortada->title ?? '' }}
            </h2>
            <div
                class="custom-container w-full h-full prose prose-sm sm:prose lg:prose-lg xl:prose-xl text-white max-md:p-6">
                {!! $bannerPortada->text ?? '' !!}
            </div>
        </div>
        <a {{-- href="{{ route('nosotros') }}" --}}
            class="py-2 px-4 bg-white text-primary-orange rounded-lg font-bold w-fit text-primary-red mx-20 mb-10 flex justify-center items-center hover:bg-gray-100 transition-colors duration-300">
            Más info
        </a>
    </div>
</div>