<?php

namespace App\Http\Controllers\Inscripcion;

use App\Http\Controllers\Controller;
use App\Models\Admision;
use App\Models\ConfigSistema;
use Illuminate\Http\Request;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Stripe;

class Paso4Controller extends Controller
{
    public function create()
    {
        $admisionId = session('inscripcion_admision_id');
        $admision   = Admision::with(['carrera1', 'carrera2', 'estudiante.persona', 'pago'])
            ->findOrFail($admisionId);

        // Si ya tiene pago completado, ir al paso 5
        if ($admision->pago && $admision->pago->estado_pago === 'completado') {
            return redirect()->route('inscripcion.paso5');
        }

        $config = ConfigSistema::find('monto_inscripcion');
        $monto  = $config ? (float) $config->valor : 100.00;

        return view('inscripcion.paso4', [
            'admision' => $admision,
            'monto'    => $monto,
            'paso'     => 4,
        ]);
    }

    public function crearSesionStripe(Request $request)
    {
        $admisionId = session('inscripcion_admision_id');
        $admision   = Admision::with(['carrera1', 'carrera2'])->findOrFail($admisionId);

        $config = ConfigSistema::find('monto_inscripcion');
        $monto  = $config ? (float) $config->valor : 100.00;

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items'           => [
                [
                    'price_data' => [
                        'currency'     => 'usd',
                        'product_data' => [
                            'name' => 'Inscripción CUP - ' . ($admision->carrera1->nombre ?? 'Carrera'),
                        ],
                        'unit_amount' => (int) ($monto * 100),
                    ],
                    'quantity' => 1,
                ],
            ],
            'mode'         => 'payment',
            'success_url'  => route('inscripcion.pago.exito') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'   => route('inscripcion.pago.cancelado'),
            'metadata'     => [
                'admision_id' => $admision->id,
            ],
            'customer_email' => Auth()->user()->correo,
        ]);

        return redirect($session->url);
    }

    public function exito(Request $request)
    {
        $admisionId = session('inscripcion_admision_id');
        $admision   = $admisionId
            ? Admision::with(['carrera1', 'carrera2', 'pago'])->find($admisionId)
            : null;

        return view('inscripcion.pago-exitoso', [
            'admision' => $admision,
            'paso'     => 4,
        ]);
    }

    public function cancelado()
    {
        return view('inscripcion.pago-cancelado', ['paso' => 4]);
    }
}
