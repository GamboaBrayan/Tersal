<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
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
            'monto_reclamado' => 'required|numeric|min:0',
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

        \App\Models\Complaint::create($validated);

        try {
            $adminEmail = config('tersal.admin_email', 'Llantastersal@gmail.com');
            $clientEmail = $validated['email'];
            $clientName = $validated['nombres'] . ' ' . $validated['apellidos'];
            $subject = 'Nuevo Registro en Libro de Reclamaciones - ' . $generatedId;

            // Encolar correo al administrador y con copia al cliente para evitar bloqueos SMTP
            Mail::queue('emails.complaint', $validated, function ($message) use ($adminEmail, $clientEmail, $clientName, $subject) {
                $message->to($adminEmail, 'Administración Tersal')
                        ->cc($clientEmail, $clientName)
                        ->subject($subject);
            });

            return response()->json([
                'success' => true,
                'generated_id' => $generatedId
            ]);
        } catch (\Exception $e) {
            Log::error('Error al enviar correo por SMTP: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => true,
                'generated_id' => $generatedId,
                'warning' => 'Tu reclamo fue registrado correctamente, pero hubo un retraso al enviar la copia por correo.'
            ]);
        }
    }
}
