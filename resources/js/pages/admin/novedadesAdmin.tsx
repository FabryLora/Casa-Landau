import CustomReactQuill from '@/components/CustomReactQuill';
import NovedadAdminRow from '@/components/novedadAdminRow';
import { router, useForm, usePage } from '@inertiajs/react';
import { AnimatePresence, motion } from 'framer-motion';
import { useEffect, useState } from 'react';
import toast from 'react-hot-toast';
import Dashboard from './dashboard';

export default function NovedadesAdmin() {
    const { novedades } = usePage().props;

    const [text, setText] = useState('');

    const { data, setData, post, reset } = useForm({
        title: '',
        type: '',
        text: '',
    });

    useEffect(() => {
        setData('text', text);
    }, [text]);

    const [searchTerm, setSearchTerm] = useState('');
    const [createView, setCreateView] = useState(false);

    const handleSubmit = (e) => {
        e.preventDefault();

        post(route('admin.novedades.store'), {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Novedad creada correctamente');
                reset();
                setCreateView(false);
                setText('');
            },
            onError: (errors) => {
                toast.error('Error al crear novedad');
                console.log(errors);
            },
        });
    };

    // Manejadores para la paginación del backend
    const handlePageChange = (page) => {
        router.get(
            route('admin.novedades'),
            {
                page: page,
                search: searchTerm,
            },
            {
                preserveState: true,
                preserveScroll: true,
            },
        );
    };

    // Función para realizar la búsqueda
    const handleSearch = () => {
        router.get(
            route('admin.novedades'),
            {
                search: searchTerm,
                page: 1, // Resetear a la primera página al buscar
            },
            {
                preserveState: true,
                preserveScroll: true,
            },
        );
    };

    return (
        <Dashboard>
            <div className="flex w-full flex-col p-6">
                <AnimatePresence>
                    {createView && (
                        <motion.div
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            exit={{ opacity: 0 }}
                            className="fixed top-0 left-0 z-50 flex h-full w-full items-center justify-center bg-black/50 text-left"
                        >
                            <form onSubmit={handleSubmit} method="POST" className="text-black">
                                <div className="w-[500px] rounded-md bg-white p-4">
                                    <h2 className="mb-4 text-2xl font-semibold">Crear novedad</h2>
                                    <div className="flex flex-col gap-4">
                                        <label htmlFor="ordennn">Orden</label>
                                        <input
                                            className="focus:outline-primary-orange rounded-md p-2 outline outline-gray-300 focus:outline"
                                            type="text"
                                            name="ordennn"
                                            id="ordennn"
                                            onChange={(e) => setData('order', e.target.value)}
                                        />
                                        <label htmlFor="type">
                                            Tipo <span className="text-red-500">*</span>
                                        </label>
                                        <input
                                            className="focus:outline-primary-orange rounded-md p-2 outline outline-gray-300 focus:outline"
                                            type="text"
                                            name="type"
                                            id="type"
                                            onChange={(e) => setData('type', e.target.value)}
                                        />
                                        <label htmlFor="nombree">
                                            Titulo <span className="text-red-500">*</span>
                                        </label>
                                        <input
                                            className="focus:outline-primary-orange rounded-md p-2 outline outline-gray-300 focus:outline"
                                            type="text"
                                            name="nombree"
                                            id="nombree"
                                            onChange={(e) => setData('title', e.target.value)}
                                        />
                                        <label htmlFor="text">
                                            Texto <span className="text-red-500">*</span>
                                        </label>
                                        <CustomReactQuill value={text} onChange={setText} />
                                        <label htmlFor="imagenn">Imagen</label>

                                        <span className="text-base font-normal">Resolucion recomendada: 392x300px</span>
                                        <div className="flex flex-row">
                                            <input
                                                type="file"
                                                name="imagen"
                                                id="imagenn"
                                                onChange={(e) => setData('image', e.target.files[0])}
                                                className="hidden"
                                            />
                                            <label
                                                className="border-primary-orange text-primary-orange hover:bg-primary-orange cursor-pointer rounded-md border px-2 py-1 transition duration-300 hover:text-white"
                                                htmlFor="imagenn"
                                            >
                                                Elegir imagen
                                            </label>
                                            <p className="self-center px-2">{data?.image?.name}</p>
                                        </div>

                                        <div className="flex justify-end gap-4">
                                            <button
                                                type="button"
                                                onClick={() => setCreateView(false)}
                                                className="border-primary-orange text-primary-orange hover:bg-primary-orange rounded-md border px-2 py-1 transition duration-300 hover:text-white"
                                            >
                                                Cancelar
                                            </button>
                                            <button
                                                type="submit"
                                                className="border-primary-orange text-primary-orange hover:bg-primary-orange rounded-md border px-2 py-1 transition duration-300 hover:text-white"
                                            >
                                                Guardar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </motion.div>
                    )}
                </AnimatePresence>
                <div className="mx-auto flex w-full flex-col gap-3">
                    <h2 className="border-primary-orange text-primary-orange text-bold w-full border-b-2 text-2xl">Novedades</h2>
                    <div className="flex h-fit w-full flex-row gap-5">
                        <input
                            type="text"
                            placeholder="Buscar marca..."
                            value={searchTerm}
                            onChange={(e) => setSearchTerm(e.target.value)}
                            className="w-full rounded-md border border-gray-300 px-3"
                        />
                        <button
                            onClick={handleSearch}
                            className="bg-primary-orange w-[200px] rounded px-4 py-1 font-bold text-white hover:bg-orange-400"
                        >
                            Buscar
                        </button>
                        <button
                            onClick={() => setCreateView(true)}
                            className="bg-primary-orange w-[300px] rounded px-4 py-1 font-bold text-white hover:bg-orange-400"
                        >
                            Crear Novedad
                        </button>
                    </div>

                    <div className="flex w-full justify-center">
                        <table className="w-full border text-left text-sm text-gray-500 rtl:text-right dark:text-gray-400">
                            <thead className="bg-gray-300 text-sm font-medium text-black uppercase">
                                <tr>
                                    <td className="text-center">ORDEN</td>
                                    <td className="text-center">TIPO</td>
                                    <td className="text-center">NOMBRE</td>
                                    <td className="text-center">TEXTO</td>
                                    <td className="w-[400px] px-3 py-2 text-center">IMAGEN</td>
                                    <td className="text-center">DESTACADA</td>
                                    <td className="text-center">EDITAR</td>
                                </tr>
                            </thead>
                            <tbody className="text-center">
                                {novedades.data?.map((novedad) => <NovedadAdminRow key={novedad.id} novedad={novedad} />)}
                            </tbody>
                        </table>
                    </div>

                    {/* Paginación con datos del backend */}
                    <div className="mt-4 flex justify-center">
                        {novedades.links && (
                            <div className="flex items-center">
                                {novedades.links.map((link, index) => (
                                    <button
                                        key={index}
                                        onClick={() => link.url && handlePageChange(link.url.split('page=')[1])}
                                        disabled={!link.url}
                                        className={`px-4 py-2 ${
                                            link.active
                                                ? 'bg-primary-orange text-white'
                                                : link.url
                                                  ? 'bg-gray-300 text-black'
                                                  : 'bg-gray-200 text-gray-500 opacity-50'
                                        } ${index === 0 ? 'rounded-l-md' : ''} ${index === novedades.links.length - 1 ? 'rounded-r-md' : ''}`}
                                        dangerouslySetInnerHTML={{ __html: link.label }}
                                    />
                                ))}
                            </div>
                        )}
                    </div>

                    {/* Información de paginación */}
                    <div className="mt-2 text-center text-sm text-gray-600">
                        Mostrando {novedades.from || 0} a {novedades.to || 0} de {novedades.total} resultados
                    </div>
                </div>
            </div>
        </Dashboard>
    );
}
