<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ComplaintController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'es_menor' => 'required|boolean',
            'nombre_apoderado' => 'nullable|string|max:255',
            'tipo_documento' => 'required|string',
            'numero_documento' => 'required|string|max:20',
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'telefono' => 'required|string|max:50',
            'email' => 'required|email|max:255',
            'direccion' => 'required|string|max:255',
            'distrito' => 'required|string|max:255',
            'tipo_bien' => 'required|string',
            'monto_reclamado' => 'required|numeric',
            'descripcion_bien' => 'required|string',
            'tipo_reclamo' => 'required|string',
            'detalle' => 'required|string',
            'pedido' => 'nullable|string',
            'acepta_privacidad' => 'required|accepted',
        ]);

        // Sanitize string inputs to prevent XSS
        foreach ($validated as $key => $value) {
            if (is_string($value) && $key !== 'email') {
                $validated[$key] = strip_tags($value);
            }
        }

        // Generate unique ID
        $prefix = $validated['tipo_reclamo'] === 'queja' ? 'QUE' : 'REC';
        $generatedId = $prefix . '-' . now()->format('Ymd-Hisv');

        $validated['generated_id'] = $generatedId;

        // Render HTML from blade view
        $htmlContent = view('emails.complaint', $validated)->render();

        $apiKey = env('BREVO_API_KEY');

        if (empty($apiKey)) {
            Log::error('BREVO_API_KEY no está configurada.');
            return response()->json(['error' => 'Error de configuración del servidor.'], 500);
        }

        try {
            $response = Http::withHeaders([
                'api-key' => $apiKey,
                'Content-Type' => 'application/json',
                'accept' => 'application/json',
            ])->post('https://api.brevo.com/v3/smtp/email', [
                'sender' => [
                    'name' => 'Libro de Reclamaciones Tersal',
                    'email' => 'no-reply@tersal.pe'
                ],
                'to' => [
                    [
                        'email' => env('ADMIN_EMAIL', 'Llantastersal@gmail.com'),
                        'name' => 'Tersal'
                    ],
                    [
                        'email' => $validated['email'], // Send a copy to the user
                        'name' => $validated['nombres'] . ' ' . $validated['apellidos']
                    ]
                ],
                'subject' => 'Nuevo Registro en Libro de Reclamaciones - ' . $generatedId,
                'htmlContent' => $htmlContent
            ]);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'generated_id' => $generatedId
                ]);
            } else {
                Log::error('Error al enviar correo con Brevo API', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return response()->json(['error' => 'No se pudo enviar el reclamo.'], 500);
            }
        } catch (\Exception $e) {
            Log::error('Excepción al enviar correo con Brevo API: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor.'], 500);
        }
    }
}
