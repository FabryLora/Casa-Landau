import ProductosPrivadaRow from '@/components/productosPrivadaRow';
import { Head, router, usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import toast from 'react-hot-toast';
import DefaultLayout from '../defaultLayout';

export default function ProductosPrivada({ categorias, subcategorias }) {
    const { productos, auth, clienteSeleccionado } = usePage().props;
    const user = auth.user;

    const [margenSwitch, setMargenSwitch] = useState(false);
    const [vendedorScreen, setVendedorScreen] = useState(user?.rol == 'vendedor' && clienteSeleccionado == null);
    const [selectedUserId, setSelectedUserId] = useState(null);

    const [marcaSelected, setMarcaSelected] = useState();

    useEffect(() => {
        if (user?.rol === 'vendedor' && !clienteSeleccionado) {
            setVendedorScreen(true);
        } else {
            setVendedorScreen(false);
        }
    }, [user, clienteSeleccionado]);

    useEffect(() => {
        localStorage.setItem('margenSwitch', JSON.stringify(margenSwitch));
    }, [margenSwitch]);

    const handlePageChange = (page) => {
        router.get(
            route('index.privada.productos'),
            {
                page: page,
            },
            {
                preserveState: true,
                preserveScroll: true,
            },
        );
    };

    const handleFastBuy = (e) => {
        e.preventDefault();
        router.post(
            route('compraRapida'),
            {
                code: e.target.code.value,
                qty: Number(e.target.qty.value),
            },
            {
                preserveScroll: true,
                onSuccess: () => {
                    toast.success('Producto añadido al carrito');
                },
                onError: (errors) => {
                    toast.error(errors.message || 'Error al añadir el producto');
                },
            },
        );
    };

    const seleccionarCliente = (e) => {
        e.preventDefault();

        router.post(
            route('seleccionarCliente'),
            { cliente_id: selectedUserId },
            {
                onSuccess: () => {
                    setVendedorScreen(false);
                    toast.success('Cliente seleccionado correctamente');
                },
            },
        );
    };

    return (
        <DefaultLayout>
            <Head>
                <title>Productos</title>
            </Head>
            {vendedorScreen && (
                <div className="fixed z-[100] flex h-screen w-screen items-center justify-center bg-black/50">
                    <div className="flex h-[218px] w-[476px] items-center justify-center bg-white max-sm:h-auto max-sm:w-[90%] max-sm:p-4">
                        <form onSubmit={seleccionarCliente} className="flex w-[350px] flex-col items-center gap-6 max-sm:w-full">
                            <h2 className="text-[16px] font-semibold max-sm:text-[14px]">Seleccionar cliente</h2>
                            <select
                                onChange={(e) => setSelectedUserId(e.target.value)}
                                className="h-[48px] w-full border px-2 max-sm:h-[40px]"
                                name="cliente_id"
                                id=""
                            >
                                <option value="">Seleccione un cliente</option>
                                {user?.clientes?.map((cliente) => (
                                    <option key={cliente.id} value={cliente.id}>
                                        {cliente.name}
                                    </option>
                                ))}
                            </select>
                            <div className="flex w-full justify-between gap-5 text-[16px] max-sm:gap-3 max-sm:text-[14px]">
                                <button
                                    type="button"
                                    onClick={() => setVendedorScreen(false)}
                                    className="text-primary-orange border-primary-orange hover:bg-primary-orange h-[41px] w-full border transition duration-300 hover:text-white max-sm:h-[36px]"
                                >
                                    Cancelar
                                </button>
                                <button
                                    onClick={seleccionarCliente}
                                    className="bg-primary-orange hover:bg-opacity-80 hover:text-primary-orange hover:border-primary-orange h-[41px] w-full text-white transition duration-300 hover:border hover:bg-transparent max-sm:h-[36px]"
                                >
                                    Confirmar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            <div className="mb-10 flex flex-col max-sm:gap-6">
                <div className="flex h-fit w-full items-center justify-center bg-[#F5F5F5] py-10" x-data="filtroSubcategorias">
                    <form
                        action="{{ route('productos') }}"
                        method="GET"
                        className="mx-auto flex h-[231px] w-[1200px] flex-col justify-evenly rounded-lg bg-white px-4 text-[16px] font-semibold text-[#6E7173] shadow-lg"
                    >
                        <div className="flex flex-col gap-3">
                            <label htmlFor="avanzada">Búsqueda avanzada</label>
                            <input
                                placeholder="Código, Nombre, Categoría, Sub categoría"
                                type="text"
                                className="h-[42px] rounded-lg border border-[#DFDFDF] pl-2"
                            />
                        </div>
                        <div className="grid grid-cols-5 gap-4">
                            <div className="flex flex-col gap-3">
                                <label htmlFor="avanzada">Rubro</label>
                                <select className="h-[42px] rounded-lg border border-[#DFDFDF] pl-2" name="" id="">
                                    <option value="">Elegir rubro</option>
                                </select>
                            </div>

                            <div className="flex flex-col gap-3">
                                <label htmlFor="avanzada">Categoria</label>
                                <select className="h-[42px] rounded-lg border border-[#DFDFDF] pl-2" name="" id="">
                                    <option value="">Elegir categoria</option>
                                </select>
                            </div>

                            <div className="flex flex-col gap-3">
                                <label htmlFor="avanzada">Material</label>
                                <select className="h-[42px] rounded-lg border border-[#DFDFDF] pl-2" name="" id="">
                                    <option value="">Elegir material</option>
                                </select>
                            </div>

                            <div className="flex flex-col gap-3">
                                <label htmlFor="avanzada">Medida</label>
                                <input placeholder="mm" type="text" className="h-[42px] rounded-lg border border-[#DFDFDF] pl-2" />
                            </div>

                            <button className="bg-primary-orange h-[42px] self-end rounded-lg px-2 py-1 font-bold text-white">Buscar</button>
                        </div>
                    </form>
                </div>
                <div className="mx-auto my-10 flex w-[1200px] flex-col gap-2 max-sm:w-full max-sm:px-4">
                    <div className="w-full">
                        <div className="grid h-[52px] grid-cols-10 items-center bg-[#F5F5F5] text-[16px] font-bold max-sm:hidden max-sm:h-[40px] max-sm:grid-cols-4 max-sm:text-[12px]">
                            <p className="max-sm:hidden"></p>
                            <p>Código</p>
                            <p className="max-sm:hidden">Nombre</p>
                            <p>Terminacion</p>
                            <p className="text-center">Rubro</p>
                            <p className="text-right max-sm:hidden">Precio</p>
                            <p className="text-center max-sm:hidden">Descuento</p>
                            <p className="text-right max-sm:hidden">Subtotal</p>
                            <p className="text-center max-sm:hidden">Cantidad</p>
                            <p className="max-sm:hidden"></p>
                        </div>
                        {productos?.data?.map((producto, index) => (
                            <ProductosPrivadaRow
                                key={producto?.id}
                                producto={producto}
                                margenSwitch={margenSwitch}
                                margen={localStorage.getItem('margen') || 0}
                            />
                        ))}
                    </div>
                    <div className="mt-4 flex justify-center">
                        {productos.links && (
                            <div className="flex items-center max-sm:flex-wrap max-sm:gap-1">
                                {productos.links.map((link, index) => (
                                    <button
                                        key={index}
                                        onClick={() => link.url && handlePageChange(link.url.split('page=')[1])}
                                        disabled={!link.url}
                                        className={`px-4 py-2 max-sm:px-2 max-sm:py-1 max-sm:text-[12px] ${
                                            link.active
                                                ? 'bg-primary-orange text-white'
                                                : link.url
                                                  ? 'bg-gray-300 text-black'
                                                  : 'bg-gray-200 text-gray-500 opacity-50'
                                        } ${index === 0 ? 'rounded-l-md' : ''} ${index === productos.links.length - 1 ? 'rounded-r-md' : ''}`}
                                        dangerouslySetInnerHTML={{ __html: link.label }}
                                    />
                                ))}
                            </div>
                        )}
                    </div>

                    {/* Información de paginación */}
                    <div className="mt-2 text-center text-sm text-gray-600 max-sm:text-[12px]">
                        Mostrando {productos.from || 0} a {productos.to || 0} de {productos.total} resultados
                    </div>
                </div>
            </div>
        </DefaultLayout>
    );
}
