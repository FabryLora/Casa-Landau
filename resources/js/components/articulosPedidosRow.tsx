import { useEffect, useRef, useState } from 'react';

export default function ArticulosPedidosRow({ producto }) {
    const [verDetalles, setVerDetalles] = useState(false);

    const detallesRef = useRef(null);

    /* cerrarr el modeal si clickeo afuera de el */
    useEffect(() => {
        const handleClickOutside = (event) => {
            if (detallesRef.current && !detallesRef.current.contains(event.target)) {
                setVerDetalles(false);
            }
        };

        document.addEventListener('mousedown', handleClickOutside);
        return () => {
            document.removeEventListener('mousedown', handleClickOutside);
        };
    }, []);

    return (
        <>
            {verDetalles && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
                    <div ref={detallesRef} className="flex max-w-6xl flex-col gap-6 rounded-lg bg-white p-5">
                        <div className="flex flex-row items-center gap-4">
                            <div className="h-[80px] w-[80px] rounded-lg">
                                <img
                                    className="h-full w-full rounded-lg object-cover"
                                    src={producto?.producto?.imagenes[0]?.image}
                                    alt={producto?.producto?.name}
                                />
                            </div>
                            <div className="text-[16px] font-bold">
                                <p>{producto?.producto?.name}</p>
                            </div>
                        </div>
                        <div>
                            <div className="grid h-[50px] grid-cols-6 items-center justify-items-center rounded-t-lg bg-gray-200 font-bold">
                                <p className="text-center">Nº de pedido</p>
                                <p className="text-center">Fecha de compra</p>
                                <p className="text-center">Fecha de entrega</p>
                                <p className="text-center">Cantidad Total</p>
                                <p className="text-center">Pendiente de entrega</p>
                                <p className="text-center">Cantidad entregada</p>
                            </div>

                            {producto?.producto?.pedidos?.map((pedido) => (
                                <div
                                    key={pedido?.id}
                                    className="grid h-[90px] grid-cols-6 items-center justify-items-center odd:bg-gray-100 even:bg-gray-50"
                                >
                                    <p>#{pedido?.id}</p>
                                    <p>{pedido?.created_at?.split('T')[0]}</p>
                                    <p>{pedido?.updated_at?.split('T')[0]}</p>
                                    <p>{pedido?.cantidad}</p>
                                    <p>{pedido?.cantidad - pedido?.cantidad_entregada}</p>
                                    <p>{pedido?.cantidad_entregada}</p>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            )}
            <div className="grid grid-cols-7 gap-2 border-b py-2 text-[#74716A]">
                <div className="flex items-center">
                    <div className="flex h-[80px] w-[80px] items-center justify-center rounded-lg bg-[#F5F5F5]">
                        <img className="" src={producto?.producto?.imagenes[0]?.image} alt="" />
                    </div>
                </div>

                <div className="flex items-center">{producto?.producto?.id}</div>

                <div className="flex items-center">{producto?.producto?.name}</div>

                <div className="flex items-center justify-center">{producto?.cantidad_total}</div>

                <div className="flex items-center justify-center">{producto?.cantidad_entregada_total}</div>

                <div className="flex items-center justify-center">{producto?.cantidad_pendiente}</div>

                <div className="flex items-center">
                    <button
                        onClick={() => setVerDetalles(true)}
                        className="bg-primary-orange h-10 w-full min-w-[138px] rounded-lg font-bold text-white"
                    >
                        Ver detalles
                    </button>
                </div>
            </div>
        </>
    );
}
