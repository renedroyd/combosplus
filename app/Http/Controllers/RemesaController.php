<?php

namespace App\Http\Controllers;

use App\Models\MetodoEnvio;
use App\Models\Pais;
use App\Models\Provincia;
use App\Models\Remesa;
use App\Services\RemittancePaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RemesaController extends Controller
{
    public function __construct(
        private readonly RemittancePaymentService $remittancePayments,
    ) {}

    public function index()
    {
        $paises = Pais::where('activo', true)->get();
        $metodosEnvio = MetodoEnvio::where('activo', true)->get();

        return view('remesas.index', compact('paises', 'metodosEnvio'));
    }

    public function calcular(Request $request)
    {
        $request->validate([
            'monto' => 'required|numeric|min:10|max:1000',
            'moneda_origen' => 'required|in:USD,EUR',
            'metodo_envio' => 'required|exists:metodos_envio,id',
        ]);

        $costoEnvio = $this->calcularCostoEnvio($request->monto, $request->metodo_envio);
        $tasaCambio = $this->obtenerTasaCambio();
        $montoFinal = $request->monto - $costoEnvio;
        $montoEnCUP = $montoFinal * $tasaCambio;

        return response()->json([
            'success' => true,
            'costo_envio' => $costoEnvio,
            'monto_final' => number_format($montoFinal, 2),
            'monto_en_cup' => number_format($montoEnCUP, 2),
            'tasa_cambio' => $tasaCambio,
        ]);
    }

    public function calcularCosto(Request $request)
    {
        return $this->calcular($request);
    }

    public function enviar(Request $request)
    {
        $request->validate([
            'remitente_nombre' => 'required|string|max:100',
            'remitente_email' => 'required|email',
            'remitente_telefono' => 'required|string|max:20',
            'destinatario_nombre' => 'required|string|max:100',
            'destinatario_ci' => 'required|string|max:11',
            'destinatario_telefono' => 'required|string|max:20',
            'destinatario_direccion' => 'required|string',
            'municipio_id' => 'required|exists:municipios,id',
            'monto' => 'required|numeric|min:10',
            'metodo_envio_id' => 'required|exists:metodos_envio,id',
            'moneda_origen' => 'required|in:USD,EUR',
        ]);

        $attributes = [
            'codigo' => $this->generarCodigoUnico(),
            'remitente_nombre' => $request->remitente_nombre,
            'remitente_email' => $request->remitente_email,
            'remitente_telefono' => $request->remitente_telefono,
            'destinatario_nombre' => $request->destinatario_nombre,
            'destinatario_ci' => $request->destinatario_ci,
            'destinatario_telefono' => $request->destinatario_telefono,
            'destinatario_direccion' => $request->destinatario_direccion,
            'municipio_id' => $request->municipio_id,
            'monto' => $request->monto,
            'monto_recibir' => $this->calcularMontoRecibir($request->monto, $request->metodo_envio_id),
            'metodo_envio_id' => $request->metodo_envio_id,
            'moneda_origen' => $request->moneda_origen,
            'estado' => 'pendiente',
            'platform_user_id' => auth()->id(),
        ];

        $remesa = Remesa::create($attributes);

        return redirect()->route('remesas.pago', $remesa->codigo);
    }

    public function pago(string $codigo)
    {
        $remesa = Remesa::where('codigo', $codigo)->with('metodoEnvio')->firstOrFail();

        return view('remesas.pago', compact('remesa'));
    }

    public function procesarPago(Request $request, string $codigo)
    {
        $remesa = Remesa::where('codigo', $codigo)->firstOrFail();

        if ($remesa->estado !== 'pendiente') {
            return redirect()->route('remesas.pago', $remesa->codigo)
                ->with('error', 'Esta remesa ya no está pendiente de pago.');
        }

        $request->validate([
            'payment_method_id' => ['nullable', 'string', 'max:255'],
        ]);

        $this->remittancePayments->submit($remesa);

        return redirect()->route('remesas.seguimiento', $remesa->codigo)
            ->with('success', 'Solicitud de pago recibida. El pago queda pendiente de verificación.');
    }

    public function buscarSeguimiento(Request $request)
    {
        $codigo = trim((string) $request->query('codigo'));

        if ($codigo === '') {
            return view('remesas.buscar');
        }

        $remesa = Remesa::where('codigo', $codigo)->first();

        return view('remesas.buscar', compact('remesa'));
    }

    public function seguimiento(string $codigo)
    {
        $remesa = Remesa::where('codigo', $codigo)->firstOrFail();

        return view('remesas.seguimiento', compact('remesa'));
    }

    private function generarCodigoUnico(): string
    {
        do {
            $codigo = 'CUB-' . strtoupper(Str::random(10)) . '-' . random_int(100, 999);
        } while (Remesa::where('codigo', $codigo)->exists());

        return $codigo;
    }

    private function calcularCostoEnvio($monto, $metodoEnvioId)
    {
        return $monto > 500 ? 5 : 8;
    }

    private function obtenerTasaCambio()
    {
        return 120;
    }

    private function calcularMontoRecibir($monto, $metodoEnvioId)
    {
        return ($monto - $this->calcularCostoEnvio($monto, $metodoEnvioId)) * $this->obtenerTasaCambio();
    }

    public function create()
    {
        $provincias = Provincia::where('activo', true)->orderBy('nombre')->get();
        $metodosEnvio = MetodoEnvio::where('activo', true)->orderBy('costo_base')->get();

        return view('remesas.nueva', compact('provincias', 'metodosEnvio'));
    }
}
