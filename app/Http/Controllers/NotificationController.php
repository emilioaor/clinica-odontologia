<?php

namespace App\Http\Controllers;

use App\Notifications\SendLabNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\JsonResponse;

class NotificationController extends Controller
{
    /**
     * Obtiene las notificaciones de aviso para fecha de entrega
     * a laboratorio
     *
     * @return JsonResponse
     */
    public function sendLabNotifications()
    {
        $now = new \DateTime();
        $response = [];
        $notifications = Auth::user()
            ->unreadNotifications()
            ->where('type', SendLabNotification::class)
            ->get();

        // Verifico si ya es la hora de mostrar la notificacion
        foreach ($notifications as $notification) {

            $deliveryDate = new \DateTime($notification->data['delivery_date']['date']);
            $notificationDate = new \DateTime($notification->data['delivery_date']['date']);
            $notificationDate->modify("-{$notification->data['hours']} hour");

            if ($now >= $deliveryDate) {

                // Ya paso la fecha de entrega
                $notification->markAsRead();

            } elseif ($now >= $notificationDate) {

                // Notificar
                $response[] = $notification;
            }
        }

        return new JsonResponse([
            'success' => true,
            'notifications' => $response
        ]);
    }

    /**
     * Marca las notificaciones como leidas
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function markAsRead(Request $request)
    {
        $notifications = Auth::user()
            ->unreadNotifications()
            ->whereIn('id', $request->notifications)
            ->get()
        ;

        foreach ($notifications as $notification) {
            $notification->markAsRead();
        }

        return new JsonResponse(['success' => true]);
    }
}
