<!-- Script para inicializar los datos -->
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('filtroSubcategorias', () => ({
            categoriaSeleccionada: '{{ request("id") ?? "" }}',
            modeloSeleccionado: '{{ request("modelo_id") ?? "" }}',
            rubroSeleccionado: '{{ request("rubro") ?? "" }}',
            terminacionSeleccionada: '{{ request("terminacion") ?? "" }}',
            materialSeleccionado: '{{ request("material") ?? "" }}',
            medidaSeleccionada: '{{ request("medida") ?? "" }}',
            busquedaAvanzada: '{{ request("busqueda") ?? "" }}',
            subcategorias: @js($subcategorias),
            medidaSeleccionada: '{{ request("medida") ?? "" }}',
            minMedida: 0,
            maxMedida: 100,
            mostrandoTooltip: false,

            init() {
                // Resetear campos dependientes cuando la categoría cambia
                this.$watch('categoriaSeleccionada', () => {
                    this.modeloSeleccionado = '';
                    this.rubroSeleccionado = '';
                });
            },

            get subcategoriasFiltradas() {
                if (!this.categoriaSeleccionada || this.categoriaSeleccionada === '') {
                    return this.subcategorias;
                }
                return this.subcategorias.filter(sub => sub.categoria_id == this.categoriaSeleccionada);
            }
        }));
    });
</script>

<style>
    .range-track {
        position: relative;
        height: 6px;
        background: #DFDFDF;
        border-radius: 3px;
    }

    .range-progress {
        height: 100%;
        background: #3a4a9f;
        /* o tu color primary-orange */
        border-radius: 3px;
        transition: width 0.2s ease;
    }
</style>

<div class="w-full bg-[#F5F5F5] flex justify-center items-center h-fit py-10" x-data="filtroSubcategorias">
    <form action="{{ route('productos.categorias') }}" method="GET"
        class="w-[1200px] mx-auto bg-white shadow-lg rounded-lg flex flex-col text-[#6E7173] font-semibold text-[16px] h-[231px] px-4 justify-evenly">

        <div class="flex flex-col gap-3">
            <label for="busqueda_avanzada">Búsqueda avanzada</label>
            <input id="busqueda_avanzada" name="busqueda" placeholder="Código, Nombre, Categoría, Sub categoría"
                type="text" x-model="busquedaAvanzada" class="h-[42px] pl-2 border rounded-lg border-[#DFDFDF]">
        </div>

        <div class="grid grid-cols-6 gap-4">
            <div class="flex flex-col gap-3">
                <label for="categoria">Categoria</label>
                <select name="id" id="categoria" x-model="categoriaSeleccionada"
                    class="h-[42px] pl-2 border rounded-lg border-[#DFDFDF]">
                    <option value="">Elegir categoria</option>
                    @foreach ($categorias as $categoria)
                        <option value="{{ $categoria->id }}" {{ (request('id') == $categoria->id) ? 'selected' : '' }}>
                            {{ $categoria->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col gap-3">
                <label for="rubro">Rubro</label>
                <select name="rubro" id="rubro" x-model="rubroSeleccionado"
                    class="h-[42px] pl-2 border rounded-lg border-[#DFDFDF]">
                    <option value="">Elegir rubro</option>
                    <template x-for="subcategoria in subcategoriasFiltradas" :key="subcategoria.id">
                        <option :value="subcategoria.id" x-text="subcategoria.name"
                            :selected="rubroSeleccionado == subcategoria.id"></option>
                    </template>
                </select>
            </div>

            <div class="flex flex-col gap-3">
                <label for="terminacion">Terminacion</label>
                <select name="terminacion" id="terminacion" x-model="terminacionSeleccionada"
                    class="h-[42px] pl-2 border rounded-lg border-[#DFDFDF]">
                    <option value="">Elegir terminacion</option>
                    @foreach ($terminaciones as $terminacion)
                        <option value="{{ $terminacion->id }}" {{ (request('terminacion') == $terminacion->id) ? 'selected' : '' }}>
                            {{ $terminacion->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col gap-3">
                <label for="material">Material</label>
                <select name="material" id="material" x-model="materialSeleccionado"
                    class="h-[42px] pl-2 border rounded-lg border-[#DFDFDF]">
                    <option value="">Elegir material</option>
                    @foreach ($materiales as $material)
                        <option value="{{ $material->id }}" {{ (request('material') == $material->id) ? 'selected' : '' }}>
                            {{ $material->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col gap-3">
                <label for="medida" class="text-[#6E7173] font-semibold text-[16px]">Medida</label>
                <div class="relative">
                    <!-- Track personalizado -->
                    <div class="range-track">
                        <div class="range-progress"
                            :style="`width: ${(medidaSeleccionada - minMedida) / (maxMedida - minMedida) * 100}%`">
                        </div>
                    </div>
                    <!-- Input range transparente encima -->
                    <input id="medida" name="medida" type="range" :min="minMedida" :max="maxMedida"
                        x-model="medidaSeleccionada" @mousedown="mostrandoTooltip = true"
                        @mouseup="mostrandoTooltip = false" @touchstart="mostrandoTooltip = true"
                        @touchend="mostrandoTooltip = false" value="{{ request('medida') ?? '' }}"
                        class="absolute top-0 w-full h-6 opacity-0 cursor-pointer">
                    <!-- Valor flotante - solo se muestra al interactuar -->
                    <div x-show="mostrandoTooltip" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 transform scale-95"
                        x-transition:enter-end="opacity-100 transform scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 transform scale-100"
                        x-transition:leave-end="opacity-0 transform scale-95"
                        class="absolute bg-gray-800 text-white px-2 py-1 rounded text-sm transform -translate-x-1/2 -translate-y-10 z-10"
                        :style="`left: ${(medidaSeleccionada - minMedida) / (maxMedida - minMedida) * 100}%`">
                        <span x-text="medidaSeleccionada + ' mm'"></span>
                        <div
                            class="w-2 h-2 bg-gray-800 transform rotate-45 absolute left-1/2 -translate-x-1/2 -bottom-1">
                        </div>
                    </div>
                </div>
                <div class="flex justify-between text-sm text-gray-500">
                    <span x-text="minMedida + ' mm'"></span>
                    <span x-text="maxMedida + ' mm'"></span>
                </div>
            </div>

            <button type="submit" class="py-1 px-2 font-bold text-white bg-primary-orange rounded-lg self-end h-[42px]">
                Buscar
            </button>
        </div>
    </form>
</div>