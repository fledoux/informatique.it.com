<?php

namespace App\Http\Controllers;

use \App\Models\TicketAttachment;
use App\Http\Requests\TicketAttachmentStoreRequest;
use App\Http\Requests\TicketAttachmentUpdateRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

/**
 * @method \Illuminate\Routing\ControllerMiddlewareOptions middleware(string $middleware)
 */
class TicketAttachmentController extends Controller
{
    /**
     * Apply permission middleware to controller methods.
     */
    public function __construct()
    {
        $this->middleware('auth')->only('download'); // Authentification requise pour télécharger
        $this->middleware('permission:ticketattachment.index')->only('index');
        $this->middleware('permission:ticketattachment.create')->only(['create', 'store']);
        $this->middleware('permission:ticketattachment.show')->only('show');
        $this->middleware('permission:ticketattachment.edit')->only(['edit', 'update']);
        $this->middleware('permission:ticketattachment.delete')->only('destroy');
    }

    public function create($ticketId = null)
    {
        $ticket = \App\Models\Ticket::findOrFail($ticketId);
        
        // Vérifier les droits d'accès au ticket
        $user = Auth::user();
        if (!$user->hasRole('super-admin')) {
            if ($user->company_id !== $ticket->company_id) {
                abort(403, 'Accès refusé à ce ticket.');
            }
        }
        
        return view('ticketattachment.create', compact('ticket'));
    }

    public function store(TicketAttachmentStoreRequest $request)
    {
        try {
            $ticket = \App\Models\Ticket::findOrFail($request->ticket_id);
            
            // Vérifier les droits
            $user = Auth::user();
            if (!$user->hasRole('super-admin')) {
                if ($user->company_id !== $ticket->company_id) {
                    abort(403, 'Accès refusé.');
                }
            }

            // Utiliser le service centralisé
            $attachmentService = new \App\Services\AttachmentService();
            $uploadedAttachments = $attachmentService->uploadMultipleAttachments(
                $request->file('files'),
                $ticket->id,
                $ticket->company_id,
                $user->id,
                null // message_id = null pour les uploads manuels
            );

            $uploadedCount = count($uploadedAttachments);

            if ($uploadedCount > 0) {
                return redirect()->route('ticket.show', $ticket)->with('success', __('global.messages.created') . " ({$uploadedCount} fichier(s) uploadé(s))");
            } else {
                return redirect()->back()->with('error', 'Aucun fichier uploadé.');
            }

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    /**
     * Télécharger un fichier depuis S3
     */
    public function download($id)
    {
        try {
            $ticketAttachment = TicketAttachment::findOrFail($id);

            // Vérifier les droits d'accès
            $user = Auth::user();
            
            // Super-admin : accès total
            if ($user->hasRole('super-admin')) {
                // Accès autorisé
            }
            // Admin ou Manager de la même société : accès aux fichiers de leur société
            elseif ($user->hasAnyRole(['admin', 'manager'])) {
                if ($user->company_id !== $ticketAttachment->company_id) {
                    abort(403, 'Accès refusé à ce fichier.');
                }
            }
            // Utilisateur standard : uniquement ses propres tickets ou tickets assignés
            else {
                if ($user->company_id !== $ticketAttachment->company_id) {
                    abort(403, 'Accès refusé à ce fichier.');
                }
                
                $ticket = $ticketAttachment->ticket;
                if ($ticket && $ticket->author_id !== $user->id && $ticket->assigned_to !== $user->id) {
                    abort(403, 'Accès refusé à ce fichier.');
                }
            }

            // Vérifier que le s3_path existe
            if (empty($ticketAttachment->s3_path)) {
                \Illuminate\Support\Facades\Log::error("Empty s3_path for attachment #{$id}");
                return redirect()->back()->with('error', 'Chemin S3 invalide.');
            }

            // Créer le client S3
            $s3Client = new \Aws\S3\S3Client([
                'version' => 'latest',
                'region'  => env('AWS_DEFAULT_REGION', 'eu-west-3'),
                'credentials' => [
                    'key'    => env('AWS_ACCESS_KEY_ID'),
                    'secret' => env('AWS_SECRET_ACCESS_KEY'),
                ],
            ]);

            // Récupérer le fichier depuis S3
            $result = $s3Client->getObject([
                'Bucket' => env('AWS_BUCKET'),
                'Key'    => $ticketAttachment->s3_path,
            ]);

            // Retourner le fichier en téléchargement
            return response($result['Body'], 200)
                ->header('Content-Type', $ticketAttachment->mime_type)
                ->header('Content-Disposition', 'attachment; filename="' . $ticketAttachment->original_filename . '"')
                ->header('Content-Length', $ticketAttachment->size_bytes);

        } catch (\Aws\S3\Exception\S3Exception $e) {
            \Illuminate\Support\Facades\Log::error("S3 download error: " . $e->getMessage());
            return redirect()->back()->with('error', 'Impossible de télécharger le fichier.');
        }
    }

    public function destroy($id)
    {
        try {
            $ticketAttachment = TicketAttachment::findOrFail($id);
            
            // Vérifier les droits (seul super-admin peut supprimer)
            $user = Auth::user();
            if (!$user->hasRole('super-admin')) {
                abort(403, 'Seuls les super-admins peuvent supprimer des fichiers.');
            }
            
            // Récupérer le ticket pour la redirection
            $ticket = $ticketAttachment->ticket;
            
            // Supprimer le fichier de S3
            try {
                $s3Client = new \Aws\S3\S3Client([
                    'version' => 'latest',
                    'region'  => env('AWS_DEFAULT_REGION', 'eu-west-3'),
                    'credentials' => [
                        'key'    => env('AWS_ACCESS_KEY_ID'),
                        'secret' => env('AWS_SECRET_ACCESS_KEY'),
                    ],
                ]);

                $s3Client->deleteObject([
                    'Bucket' => env('AWS_BUCKET'),
                    'Key'    => $ticketAttachment->s3_path,
                ]);

                \Illuminate\Support\Facades\Log::info("File deleted from S3: {$ticketAttachment->s3_path}");
            } catch (\Aws\S3\Exception\S3Exception $e) {
                \Illuminate\Support\Facades\Log::error("S3 delete error: " . $e->getMessage());
                // On continue quand même la suppression en base
            }
            
            // Supprimer l'enregistrement en base
            $ticketAttachment->delete();
            
            return redirect()->route('ticket.show', $ticket)
                ->with('success', __('global.messages.deleted'));
                
        } catch (ModelNotFoundException $e) {
            return redirect()->back()
                ->with('error', __('global.messages.delete_not_found'));
        }
    }
}