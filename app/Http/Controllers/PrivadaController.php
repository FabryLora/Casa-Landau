<?php

namespace App\Http\Controllers;

use App\Exports\PedidoExport;
use App\Mail\InformacionDePagoMail;
use App\Mail\PedidoMail;
use App\Models\Categoria;
use App\Models\Contacto;
use App\Models\InformacionImportante;
use App\Models\Pedido;
use App\Models\PedidoProducto;
use App\Models\Producto;
use App\Models\SubCategoria;
use App\Models\User;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class   PrivadaController extends Controller
{
    public function carrito()
    {
        $contacto = Contacto::first();
        $informacion = InformacionImportante::first();
        $carrito = Cart::content();

        // Extraer los IDs de los productos del carrito
        $productosIds = $carrito->pluck('id')->toArray();

        // Traer todos los productos con esos IDs
        $productos = Producto::whereIn('id', $productosIds)->with(['imagenes', 'categoria', 'subcategoria', 'precio', 'terminacion', 'material'])->get();

        $productosConRowId = $productos->map(function ($producto) use ($carrito) {
            // Buscar el item del carrito que corresponde a este producto
            $itemCarrito = $carrito->where('id', $producto->id)->first();



            // Agregar el rowId al producto
            $producto->rowId = $itemCarrito ? $itemCarrito->rowId : null;
            $producto->qty = $itemCarrito ? $itemCarrito->qty : null;
            $producto->subtotal = $producto->oferta == 1 ? $producto->precio->precio * (1 - $producto->descuento_oferta / 100) * ($itemCarrito->qty ?? 1) : $producto->precio->precio * ($itemCarrito->qty ?? 1);

            return $producto;
        });

        // Calcular el subtotal total del carrito
        $subtotalTotal = $productosConRowId->sum('subtotal');

        // Obtener los descuentos del usuario logueado
        $descuento_uno = auth()->user()->rol == "cliente" ? auth()->user()->descuento_uno : session('cliente_seleccionado')->descuento_uno ?? 0;
        $descuento_dos = auth()->user()->rol == "cliente" ? auth()->user()->descuento_dos : session('cliente_seleccionado')->descuento_dos ?? 0;
        $descuento_tres = auth()->user()->rol == "cliente" ? auth()->user()->descuento_tres : session('cliente_seleccionado')->descuento_tres ?? 0;

        // Calcular subtotal con descuentos aplicados en orden
        $subtotal_descuento = $subtotalTotal;

        // Aplicar descuento_uno si es mayor a 0
        if ($descuento_uno > 0) {
            $subtotal_descuento = $subtotal_descuento * (1 - ($descuento_uno / 100));
        }

        // Aplicar descuento_dos si es mayor a 0
        if ($descuento_dos > 0) {
            $subtotal_descuento = $subtotal_descuento * (1 - ($descuento_dos / 100));
        }

        // Aplicar descuento_tres si es mayor a 0
        if ($descuento_tres > 0) {
            $subtotal_descuento = $subtotal_descuento * (1 - ($descuento_tres / 100));
        }

        // Calcular el IVA (21% del subtotal con descuentos)
        $iva = $subtotal_descuento * 0.21;

        $total = $subtotal_descuento + $iva;

        $descuento = $subtotalTotal - $subtotal_descuento;

        $categorias = Categoria::orderBy('order', 'asc')->get();
        $subcategorias = SubCategoria::orderBy('order', 'asc')->get();

        return inertia('privada/carrito', [
            'informacion' => $informacion,
            'contacto' => $contacto,
            'carrito' => $carrito,
            'productos' => $productosConRowId,
            'subtotal' => $subtotalTotal,
            'descuento_uno' => $descuento_uno,
            'descuento_dos' => $descuento_dos,
            'descuento_tres' => $descuento_tres,
            'subtotal_descuento' => $subtotal_descuento,
            'iva' => $iva,
            'categorias' => $categorias,
            'subcategorias' => $subcategorias,
            'total' => $total,
            'descuento' => $descuento,
        ]);
    }

    public function borrarCliente()
    {
        session()->forget('cliente_seleccionado');
        return redirect('/privada/productos');
    }

    // Cuando el vendedor selecciona el cliente
    public function seleccionarCliente(Request $request)
    {

        $cliente = User::find($request->cliente_id);

        session([
            'cliente_seleccionado' => $cliente,
        ]);
    }

    public function hacerPedido(Request $request)
    {

        $pedido = Pedido::create(
            [
                'user_id' => session('cliente_seleccionado') ? session('cliente_seleccionado')->id : auth()->id(),
                'tipo_entrega' => $request->tipo_entrega,
                'descuento' => $request->descuento,
                'mensaje' => $request->mensaje,
                'forma_pago' => $request->forma_pago,
                'subtotal' => $request->subtotal,
                'iva' => $request->iva,
                'total' => $request->total,
            ]
        );

        foreach (Cart::content() as $item) {
            PedidoProducto::create([
                'pedido_id' => $pedido->id,
                'producto_id' => $item->id,
                'cantidad' => $item->qty,
                'precio_unitario' => $item->price,
            ]);
        }

        $this->actualizarExcelMaestro($pedido);



        // Enviar correo al administrador (o a la dirección que desees)
        Mail::to(Contacto::first()->mail_pedidos)->send(new PedidoMail($pedido, $request->file('archivo')));




        Cart::destroy();

        // Devolver mensaje de éxito al usuario
        session([
            'pedido_id' => $pedido->id,
        ]);
    }

    private function actualizarExcelMaestro(Pedido $pedido)
    {
        $nombreArchivo = 'pedidos_maestro.xlsx';
        $rutaCompleta = storage_path('app/public/pedidos/' . $nombreArchivo);

        // Crear directorio si no existe
        if (!file_exists(dirname($rutaCompleta))) {
            mkdir(dirname($rutaCompleta), 0755, true);
        }

        $spreadsheet = null;

        // Verificar si el archivo ya existe
        if (file_exists($rutaCompleta)) {
            // Cargar el Excel existente
            $spreadsheet = IOFactory::load($rutaCompleta);
            $sheet = $spreadsheet->getActiveSheet();

            // Encontrar la siguiente fila vacía
            $ultimaFila = $sheet->getHighestRow();
            $siguienteFila = $ultimaFila + 1;
        } else {
            // Crear nuevo Excel si no existe
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Registro de Pedidos');

            // Crear encabezados
            $this->crearEncabezados($sheet);
            $siguienteFila = 2; // Primera fila de datos después de encabezados
        }

        // Agregar los productos del nuevo pedido
        $productos = $pedido->productos()->with('producto')->get();

        foreach ($productos as $pedidoProducto) {
            $this->agregarFilaPedido($sheet, $siguienteFila, $pedido, $pedidoProducto);
            $siguienteFila++;
        }

        // Autoajustar columnas
        foreach (range('A', 'O') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Guardar el archivo actualizado
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($rutaCompleta);

        // Limpiar memoria
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        return $nombreArchivo;
    }

    private function crearEncabezados($sheet)
    {
        $encabezados = [
            'A1' => 'Pedido ID',
            'B1' => 'Cliente',
            'C1' => 'Email Cliente',
            'D1' => 'Fecha Pedido',
            'E1' => 'Producto',
            'F1' => 'Cantidad',
            'G1' => 'Precio Unitario',
            'H1' => 'Subtotal Producto',
            'I1' => 'Descuento',
            'J1' => 'Subtotal Pedido',
            'K1' => 'IVA',
            'L1' => 'Total Pedido',
            'M1' => 'Mensaje'
        ];

        // Establecer encabezados
        foreach ($encabezados as $celda => $valor) {
            $sheet->setCellValue($celda, $valor);
        }

        // Estilo para encabezados
        $sheet->getStyle('A1:M1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => '4472C4']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);
    }

    private function agregarFilaPedido($sheet, $fila, $pedido, $pedidoProducto)
    {
        $sheet->setCellValue('A' . $fila, $pedido->id);
        $sheet->setCellValue('B' . $fila, $pedido->user->name ?? 'Cliente no encontrado');
        $sheet->setCellValue('C' . $fila, $pedido->user->email ?? 'Email no disponible');
        $sheet->setCellValue('D' . $fila, $pedido->created_at->format('d/m/Y H:i'));
        $sheet->setCellValue('E' . $fila, $pedidoProducto->producto->code ?? 'Producto eliminado');
        $sheet->setCellValue('F' . $fila, $pedidoProducto->cantidad);
        $sheet->setCellValue('G' . $fila, '$' . number_format($pedidoProducto->precio_unitario, 2));
        $sheet->setCellValue('H' . $fila, '$' . number_format($pedidoProducto->cantidad * $pedidoProducto->precio_unitario, 2));

        $sheet->setCellValue('I' . $fila, '$' . number_format($pedido->descuento, 2));
        $sheet->setCellValue('J' . $fila, '$' . number_format($pedido->subtotal, 2));
        $sheet->setCellValue('K' . $fila, '$' . number_format($pedido->iva, 2));
        $sheet->setCellValue('L' . $fila, '$' . number_format($pedido->total, 2));
        $sheet->setCellValue('M' . $fila, $pedido->mensaje ?? 'Sin mensaje');

        // Aplicar bordes a la nueva fila
        $sheet->getStyle('A' . $fila . ':M' . $fila)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);
    }



    public function sendInformacion(Request $request)
    {



        $data = [
            'fecha' => $request->fecha,
            'importe' => $request->importe,
            'banco' => $request->banco,
            'sucursal' => $request->sucursal,
            'facturas' => $request->facturas,
            'observaciones' => $request->observaciones,
        ];

        // Enviar correo al administrador (o a la dirección que desees)
        Mail::to(Contacto::first()->mail_info)->send(new InformacionDePagoMail($data, $request->file('archivo')));

        // Devolver mensaje de éxito al usuario
        return redirect()->back()->with('success', 'Tu mensaje ha sido enviado con éxito.');
    }


    public function carritoAdmin()
    {

        $informacion = InformacionImportante::first();

        return inertia('admin/carritoAdmin', [
            'informacion' => $informacion,
        ]);
    }
}
