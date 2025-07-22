import { faPen, faTrash } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { useForm, usePage } from '@inertiajs/react';
import { AnimatePresence, motion } from 'framer-motion';
import { useEffect, useState } from 'react';
import toast from 'react-hot-toast';
import Select from 'react-select';
import Switch from './Switch';

export default function ProductosAdminRow({ producto }) {
    const [edit, setEdit] = useState(false);

    const { categorias, subcategorias, terminaciones, materiales } = usePage().props;

    const [categoriaSelected, setCategoriaSelected] = useState(producto.categoria_id);

    const { data, setData, post, reset, errors } = useForm({
        order: producto?.order,
        name: producto?.name,
        code: producto?.code,
        medida: producto?.medida,
        categoria_id: producto?.categoria_id,
        sub_categoria_id: producto?.sub_categoria_id,
        terminacion_id: producto?.terminacion_id,
        material_id: producto?.material_id,
        unidad_minima: producto?.unidad_minima,
        descuento: producto?.descuento,
        id: producto?.id,
    });

    useEffect(() => {
        setCategoriaSelected(data.categoria_id);
    }, [data.categoria_id]);

    const handleUpdate = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        post(route('admin.productos.update'), {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Producto actualizada correctamente');
                setEdit(false);
            },
            onError: (errors) => {
                toast.error('Error al actualizar producto');
                console.log(errors);
            },
        });
    };

    const deleteMarca = () => {
        if (confirm('¿Estas seguro de eliminar este producto?')) {
            post(route('admin.productos.destroy'), {
                preserveScroll: true,
                onSuccess: () => {
                    toast.success('Producto eliminada correctamente');
                },
                onError: (errors) => {
                    toast.error('Error al eliminar producto');
                    console.log(errors);
                },
            });
        }
    };

    const [existingImages, setExistingImages] = useState(producto.imagenes || []);
    const [newImagePreviews, setNewImagePreviews] = useState([]);
    const [imagesToDelete, setImagesToDelete] = useState([]);

    const handleFileChange = (e) => {
        const files = Array.from(e.target.files);

        // Actualizar el form data con los archivos nuevos
        setData('new_images', files);

        // Crear previews de las nuevas imágenes
        const previews = files.map((file) => {
            return new Promise((resolve) => {
                const reader = new FileReader();
                reader.onload = (e) =>
                    resolve({
                        file: file,
                        url: e.target.result,
                        name: file.name,
                        size: file.size,
                        isNew: true,
                    });
                reader.readAsDataURL(file);
            });
        });

        Promise.all(previews).then(setNewImagePreviews);
    };

    const removeExistingImage = (indexToRemove) => {
        const imageToDelete = existingImages[indexToRemove];

        // Agregar al array de imágenes a eliminar
        setImagesToDelete((prev) => [...prev, imageToDelete.id]);

        // Remover de imágenes existentes
        const newExistingImages = existingImages.filter((_, index) => index !== indexToRemove);
        setExistingImages(newExistingImages);

        // Actualizar form data
        setData('images_to_delete', [...imagesToDelete, imageToDelete.id]);
    };

    const removeNewImage = (indexToRemove) => {
        const newImages = data.new_images?.filter((_, index) => index !== indexToRemove) || [];
        const newPreviews = newImagePreviews.filter((_, index) => index !== indexToRemove);

        setData('new_images', newImages);
        setNewImagePreviews(newPreviews);
    };

    return (
        <tr className={`border text-black odd:bg-gray-100 even:bg-white`}>
            <td className="align-middle">{producto?.order}</td>
            <td className="align-middle">{producto?.name}</td>
            <td className="align-middle">{producto?.code}</td>
            <td className="align-middle">{producto?.categoria?.name}</td>
            <td className="align-middle">{producto?.subcategoria?.name}</td>
            <td className="align-middle">
                {producto?.descuento > 0 ? <p className="text-green-500">{producto?.descuento}%</p> : <p>Sin descuento</p>}
            </td>

            <td className="flex h-[90px] flex-row items-center justify-center">
                <Switch routeName="cambiarDestacado" id={producto?.id} status={producto?.destacado == 1} />
            </td>

            <td className="w-[140px] text-center">
                <div className="flex flex-row justify-center gap-3">
                    <button onClick={() => setEdit(true)} className="h-10 w-10 rounded-md border border-blue-500 px-2 py-1 text-white">
                        <FontAwesomeIcon icon={faPen} size="lg" color="#3b82f6" />
                    </button>
                    <button onClick={deleteMarca} className="h-10 w-10 rounded-md border border-red-500 px-2 py-1 text-white">
                        <FontAwesomeIcon icon={faTrash} size="lg" color="#fb2c36" />
                    </button>
                </div>
            </td>

            <AnimatePresence>
                {edit && (
                    <motion.div
                        initial={{ opacity: 0 }}
                        animate={{ opacity: 1 }}
                        exit={{ opacity: 0 }}
                        className="fixed top-0 left-0 z-50 flex h-full w-full items-center justify-center bg-black/50 text-left"
                    >
                        <form onSubmit={handleUpdate} method="POST" className="relative rounded-lg bg-white text-black">
                            <div className="bg-primary-orange sticky top-0 flex flex-row items-center gap-2 rounded-t-lg p-4">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="28"
                                    height="28"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="#ffffff"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    className="lucide lucide-pen-icon lucide-pen"
                                >
                                    <path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z" />
                                </svg>
                                <h2 className="text-2xl font-semibold text-white">Actualizar Producto</h2>
                            </div>

                            <div className="max-h-[60vh] w-[500px] overflow-y-auto rounded-md bg-white p-4">
                                <div className="flex flex-col gap-4">
                                    <label htmlFor="ordennn">Orden</label>
                                    <input
                                        className="focus:outline-primary-orange rounded-md p-2 outline outline-gray-300 focus:outline"
                                        type="text"
                                        name="ordennn"
                                        id="ordennn"
                                        value={data.order}
                                        onChange={(e) => setData('order', e.target.value)}
                                    />
                                    <label htmlFor="nombree">
                                        Nombre <span className="text-red-500">*</span>
                                    </label>
                                    <input
                                        className="focus:outline-primary-orange rounded-md p-2 outline outline-gray-300 focus:outline"
                                        type="text"
                                        name="nombree"
                                        id="nombree"
                                        value={data.name}
                                        onChange={(e) => setData('name', e.target.value)}
                                    />

                                    <label htmlFor="code">
                                        Codigo <span className="text-red-500">*</span>
                                    </label>
                                    <input
                                        className="focus:outline-primary-orange rounded-md p-2 outline outline-gray-300 focus:outline"
                                        type="text"
                                        name="code"
                                        id="code"
                                        value={data.code}
                                        onChange={(e) => setData('code', e.target.value)}
                                    />

                                    <label htmlFor="medida">
                                        Medida <span className="text-red-500">*</span>
                                    </label>
                                    <input
                                        className="focus:outline-primary-orange rounded-md p-2 outline outline-gray-300 focus:outline"
                                        type="text"
                                        name="medida"
                                        id="medida"
                                        value={data.medida}
                                        onChange={(e) => setData('medida', e.target.value)}
                                    />

                                    <label htmlFor="unidad">
                                        Unidad minima <span className="text-red-500">*</span>
                                    </label>
                                    <input
                                        className="focus:outline-primary-orange rounded-md p-2 outline outline-gray-300 focus:outline"
                                        type="number"
                                        name="unidad"
                                        id="unidad"
                                        value={data.unidad_minima}
                                        onChange={(e) => setData('unidad_minima', e.target.value)}
                                    />

                                    <label htmlFor="descuento">Descuento</label>
                                    <input
                                        className="focus:outline-primary-orange rounded-md p-2 outline outline-gray-300 focus:outline"
                                        type="number"
                                        name="descuento"
                                        id="descuento"
                                        value={data.descuento}
                                        onChange={(e) => setData('descuento', e.target.value)}
                                    />

                                    <label htmlFor="categoria_id">Categoria</label>
                                    <Select
                                        value={data.categoria_id}
                                        name="categoria_id"
                                        id="categoria_id"
                                        options={categorias.map((categoria) => ({
                                            value: categoria.id,
                                            label: categoria.name,
                                        }))}
                                        onChange={(option) => setData('categoria_id', option.value)}
                                        className="basic-single"
                                        classNamePrefix="select"
                                    />

                                    {/* mostrar subcategorias dependiendo la categoria seleccionada */}

                                    <label htmlFor="sub_categoria_id">Rubro</label>
                                    <Select
                                        value={data.sub_categoria_id}
                                        name="sub_categoria_id"
                                        id="sub_categoria_id"
                                        options={subcategorias
                                            .filter((sub) => sub.categoria_id == categoriaSelected)
                                            .map((subcategoria) => ({
                                                value: subcategoria.id,
                                                label: subcategoria.name,
                                            }))}
                                        onChange={(option) => setData('sub_categoria_id', option.value)}
                                        className="basic-single"
                                        classNamePrefix="select"
                                    />

                                    <label htmlFor="terminacion_id">Terminación</label>
                                    <Select
                                        value={data.terminacion_id}
                                        name="terminacion_id"
                                        id="terminacion_id"
                                        options={terminaciones.map((terminacion) => ({
                                            value: terminacion.id,
                                            label: terminacion.name,
                                        }))}
                                        onChange={(option) => setData('terminacion_id', option.value)}
                                        className="basic-single"
                                        classNamePrefix="select"
                                    />

                                    <label htmlFor="material_id">Material</label>
                                    <Select
                                        value={data.material_id}
                                        name="material_id"
                                        id="material_id"
                                        options={materiales.map((material) => ({
                                            value: material.id,
                                            label: material.name,
                                        }))}
                                        onChange={(option) => setData('material_id', option.value)}
                                        className="basic-single"
                                        classNamePrefix="select"
                                    />

                                    <label>Imágenes del Producto</label>
                                    <input
                                        type="file"
                                        multiple
                                        accept="image/*"
                                        onChange={handleFileChange}
                                        className="file:bg-primary-orange w-full rounded border p-2 file:cursor-pointer file:rounded-full file:px-4 file:py-2 file:text-white"
                                    />
                                    {errors.new_images && <span className="text-red-500">{errors.new_images}</span>}
                                    {errors['new_images.*'] && <span className="text-red-500">{errors['new_images.*']}</span>}

                                    {/* Mostrar imágenes existentes */}
                                    {existingImages.length > 0 && (
                                        <div className="mt-4 space-y-2">
                                            <h4>Imágenes actuales ({existingImages.length})</h4>
                                            <div className="grid grid-cols-2 gap-4 md:grid-cols-4">
                                                {existingImages.map((image, index) => (
                                                    <div key={image.id} className="relative">
                                                        <img
                                                            src={image.image || image.path} // Ajusta según tu estructura
                                                            alt={image.name || `Imagen ${index + 1}`}
                                                            className="h-32 w-full rounded border object-cover"
                                                        />
                                                        <button
                                                            type="button"
                                                            onClick={() => removeExistingImage(index)}
                                                            className="absolute -top-2 -right-2 flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-sm text-white hover:bg-red-600"
                                                        >
                                                            ×
                                                        </button>
                                                        <p className="mt-1 truncate text-xs text-gray-600">{image.name || `Imagen ${index + 1}`}</p>
                                                        <span className="inline-block rounded bg-blue-100 px-2 py-1 text-xs text-blue-800">
                                                            Existente
                                                        </span>
                                                    </div>
                                                ))}
                                            </div>
                                        </div>
                                    )}

                                    {/* Mostrar nuevas imágenes */}
                                    {newImagePreviews.length > 0 && (
                                        <div className="mt-4 space-y-2">
                                            <h4>Nuevas imágenes ({newImagePreviews.length})</h4>
                                            <div className="grid grid-cols-2 gap-4 md:grid-cols-4">
                                                {newImagePreviews.map((preview, index) => (
                                                    <div key={index} className="relative">
                                                        <img
                                                            src={preview.url}
                                                            alt={preview.name}
                                                            className="h-32 w-full rounded border object-cover"
                                                        />
                                                        <button
                                                            type="button"
                                                            onClick={() => removeNewImage(index)}
                                                            className="absolute -top-2 -right-2 flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-sm text-white hover:bg-red-600"
                                                        >
                                                            ×
                                                        </button>
                                                        <p className="mt-1 truncate text-xs text-gray-600">{preview.name}</p>
                                                        <p className="text-xs text-gray-500">{(preview.size / 1024 / 1024).toFixed(2)} MB</p>
                                                        <span className="inline-block rounded bg-green-100 px-2 py-1 text-xs text-green-800">
                                                            Nueva
                                                        </span>
                                                    </div>
                                                ))}
                                            </div>
                                        </div>
                                    )}

                                    {/* Mostrar total de imágenes */}
                                    <div className="mt-2 text-sm text-gray-600">
                                        Total de imágenes: {existingImages.length + newImagePreviews.length}
                                    </div>
                                </div>
                            </div>

                            <div className="bg-primary-orange sticky bottom-0 flex justify-end gap-4 rounded-b-md p-4">
                                <button
                                    type="button"
                                    onClick={() => setEdit(false)}
                                    className="rounded-md border border-red-500 bg-red-500 px-2 py-1 text-white transition duration-300"
                                >
                                    Cancelar
                                </button>
                                <button
                                    type="submit"
                                    className="hover:text-primary-orange rounded-md px-2 py-1 text-white outline outline-white transition duration-300 hover:bg-white"
                                >
                                    Actualizar
                                </button>
                            </div>
                        </form>
                    </motion.div>
                )}
            </AnimatePresence>
        </tr>
    );
}
