<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Remesa;
use App\Models\Pais;
use App\Models\MetodoEnvio;
use App\Models\Provincia;

class RemesaController extends Controller
{
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

        // Aquí iría la lógica de cálculo con tu proveedor
        $costoEnvio = $this->calcularCostoEnvio($request->monto, $request->metodo_envio);
        $tasaCambio = $this->obtenerTasaCambio();
        $montoFinal = $request->monto - $costoEnvio;
        $montoEnCUP = $montoFinal * $tasaCambio;

        return response()->json([
            'success' => true,
            'costo_envio' => $costoEnvio,
            'monto_final' => number_format($montoFinal, 2),
            'monto_en_cup' => number_format($montoEnCUP, 2),
            'tasa_cambio' => $tasaCambio
        ]);
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

        // Crear la remesa
        $remesa = Remesa::create([
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
            'user_id' => auth()->id(),
        ]);

        // Redirigir a pago
        return redirect()->route('remesas.pago', $remesa->codigo);
    }

    public function seguimiento($codigo)
    {
        $remesa = Remesa::where('codigo', $codigo)->firstOrFail();
        return view('remesas.seguimiento', compact('remesa'));
    }

    private function generarCodigoUnico()
    {
        return 'CUB-' . strtoupper(uniqid()) . '-' . rand(100, 999);
    }

    private function calcularCostoEnvio($monto, $metodoEnvioId)
    {
        // Lógica según tu proveedor
        if ($monto > 500) {
            return 5; // Descuento para montos grandes
        }
        return 8; // Costo base
    }

    private function obtenerTasaCambio()
    {
        // Aquí conectarías con una API de tasa de cambio
        // Por ahora, valor aproximado
        return 120; // 1 USD ≈ 120 CUP
    }

    private function calcularMontoRecibir($monto, $metodoEnvioId)
    {
        $costo = $this->calcularCostoEnvio($monto, $metodoEnvioId);
        $tasa = $this->obtenerTasaCambio();
        return ($monto - $costo) * $tasa;
    }

    public function create()
    {
        // Obtener todas las provincias activas (para el selector de provincia/municipio)
        $provincias = Provincia::where('activo', true)
                                ->orderBy('nombre')
                                ->get();

        // Obtener todos los métodos de envío activos, ordenados por costo (opcional)
        $metodosEnvio = MetodoEnvio::where('activo', true)
                                    ->orderBy('costo_base')
                                    ->get();

        // Retornar la vista con los datos necesarios
        return view('remesas.nueva', compact('provincias', 'metodosEnvio'));
    }
}