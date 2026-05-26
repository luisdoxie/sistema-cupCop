<?php

namespace App\Http\Controllers\Inscripcion;

use App\Http\Controllers\Controller;
use App\Models\Admision;
use App\Models\Pago;
use Illuminate\Http\Request;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload   = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret    = config('services.stripe.webhook_secret');

        Stripe::setApiKey(config('services.stripe.secret'));

        // Verificar firma si hay webhook secret configurado
        if ($secret) {
            try {
                $event = Webhook::constructEvent($payload, $sigHeader, $secret);
            } catch (SignatureVerificationException $e) {
                return response()->json(['error' => 'Firma de webhook inválida.'], 400);
            } catch (\UnexpectedValueException $e) {
                return response()->json(['error' => 'Payload inválido.'], 400);
            }
        } else {
            // Sin secret configurado, parsear el payload directamente (solo para desarrollo)
            $event = \Stripe\Event::constructFrom(json_decode($payload, true));
        }

        // Procesar evento checkout.session.completed
        if ($event->type === 'checkout.session.completed') {
            $session    = $event->data->object;
            $admisionId = $session->metadata->admision_id ?? null;

            if (! $admisionId) {
                return response()->json(['error' => 'admision_id no encontrado en metadata.'], 400);
            }

            $admision = Admision::find($admisionId);

            if (! $admision) {
                return response()->json(['error' => 'Admisión no encontrada.'], 404);
            }

            // Crear o actualizar el pago
            Pago::updateOrCreate(
                ['id_admision' => $admision->id],
                [
                    'monto'                  => $session->amount_total / 100,
                    'tipo_pasarela'          => 'stripe',
                    'referencia_transaccion' => $session->payment_intent ?? $session->id,
                    'estado_pago'            => 'completado',
                    'fecha_pago'             => now(),
                ]
            );

            // Actualizar estado de admisión
            $admision->update(['estado' => 'pago_pendiente']);
        }

        return response()->json(['status' => 'ok']);
    }
}
