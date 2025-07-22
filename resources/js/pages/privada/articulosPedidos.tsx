import { Head, Link, usePage } from '@inertiajs/react';
import DefaultLayout from '../defaultLayout';

export default function ArticulosPedidos() {
    const { pedidos } = usePage().props;

    return (
        <DefaultLayout>
            <Head>
                <title>Articulos pedidos</title>
            </Head>

            <div className="mx-auto flex min-h-[40vh] w-[1200px] flex-col gap-20 py-20">
                <div className="flex flex-row gap-5">
                    <Link href={'/privada/pedidos'} className="fotn-bold border-b-3 border-gray-500 text-[24px]">
                        Mis pedidos
                    </Link>
                    <Link href={'/privada/articulos-pedidos'} className="fotn-bold text-primary-orange border-primary-orange border-b-3 text-[24px]">
                        Mis articulos pedidos
                    </Link>
                </div>
                <div className="col-span-2 grid w-full items-start">
                    <div className="w-full">
                        <div className="grid h-[52px] grid-cols-8 items-center bg-[#F5F5F5]">
                            <p></p>
                            <p>Codigo</p>
                            <p>Nombre</p>
                            <p>Cantidad consolidada</p>
                            <p>Entregados</p>
                            <p>Pendientes</p>
                            <p></p>
                            <p></p>
                        </div>
                        {/* {pedidos?.map((pedido) => <MispedidosRow key={pedido?.id} pedido={pedido} />)} */}
                    </div>
                </div>
            </div>
        </DefaultLayout>
    );
}
