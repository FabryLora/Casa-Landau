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
                <label for="medida">Medida</label>
                <input id="medida" name="medida" placeholder="mm" type="text" x-model="medidaSeleccionada"
                    value="{{ request('medida') ?? '' }}" class="h-[42px] pl-2 border rounded-lg border-[#DFDFDF]">
            </div>

            <button type="submit" class="py-1 px-2 font-bold text-white bg-primary-orange rounded-lg self-end h-[42px]">
                Buscar
            </button>
        </div>
    </form>
</div>