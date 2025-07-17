<!-- Script para inicializar los datos -->
{{--
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('filtroSubcategorias', () => ({
            categoriaSeleccionada: '{{ request("id") }}',
            modeloSeleccionado: '{{ request("modelo_id") }}',
            subcategorias: @js($subcategorias),

            init() {
                // Resetear modelo si la categoría cambia
                this.$watch('categoriaSeleccionada', () => {
                    this.modeloSeleccionado = '';
                });
            },

            get subcategoriasFiltradas() {
                if (!this.categoriaSeleccionada) {
                    return this.subcategorias;
                }
                return this.subcategorias.filter(sub => sub.categoria_id == this.categoriaSeleccionada);
            }
        }));
    });
</script>
--}}
<div class="w-full bg-[#F5F5F5] flex justify-center items-center h-fit py-10" x-data="filtroSubcategorias">
    <form action="{{ route('productos') }}" method="GET"
        class="w-[1200px] mx-auto bg-white shadow-lg rounded-lg flex flex-col text-[#6E7173] font-semibold text-[16px] h-[231px] px-4 justify-evenly">
        <div class="flex flex-col gap-3">
            <label for="avanzada">Búsqueda avanzada</label>
            <input placeholder="Código, Nombre, Categoría, Sub categoría" type="text"
                class="h-[42px] pl-2 border rounded-lg border-[#DFDFDF]">
        </div>
        <div class="grid grid-cols-5 gap-4">
            <div class="flex flex-col gap-3">
                <label for="avanzada">Rubro</label>
                <select class="h-[42px] pl-2 border rounded-lg border-[#DFDFDF]" name="" id="">
                    <option value="">Elegir rubro</option>
                </select>
            </div>


            <div class="flex flex-col gap-3">
                <label for="avanzada">Categoria</label>
                <select class="h-[42px] pl-2 border rounded-lg border-[#DFDFDF]" name="" id="">
                    <option value="">Elegir categoria</option>
                </select>
            </div>


            <div class="flex flex-col gap-3">
                <label for="avanzada">Material</label>
                <select class="h-[42px] pl-2 border rounded-lg border-[#DFDFDF]" name="" id="">
                    <option value="">Elegir material</option>
                </select>
            </div>

            <div class="flex flex-col gap-3">
                <label for="avanzada">Medida</label>
                <input placeholder="mm" type="text" class="h-[42px] pl-2 border rounded-lg border-[#DFDFDF]">
            </div>

            <button
                class="py-1 px-2 font-bold text-white bg-primary-orange rounded-lg self-end h-[42px]">Buscar</button>

        </div>

    </form>
</div>