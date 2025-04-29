<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OneSignalCustomNotificationRequest;
use App\Http\Requests\Admin\OneSignalBroadcastNotificationRequest;
use App\Http\Requests\Admin\OneSignalSegmentNotificationRequest;
use App\Http\Requests\Admin\OneSignalTemplateRequest;
use App\Http\Resources\Admin\OneSignalResource;
use App\Http\Resources\Admin\NotificationTemplateResource;
use App\Interfaces\Services\Admin\OneSignalServiceInterface;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OneSignalController extends Controller
{
    use ApiResponseTrait;

    private OneSignalServiceInterface $oneSignalService;

    /**
     * Constructor
     *
     * @param OneSignalServiceInterface $oneSignalService
     */
    public function __construct(OneSignalServiceInterface $oneSignalService)
    {
        $this->oneSignalService = $oneSignalService;
    }

    /**
     * Bildirimleri listeler
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['type', 'date_from', 'date_to', 'search']);
        $perPage = $request->input('per_page', 15);

        $notifications = $this->oneSignalService->getAllNotifications($filters, $perPage);

        return $this->successResponse(
            OneSignalResource::collection($notifications),
            'notification.list_success'
        );
    }

    /**
     * Bildirim detayını gösterir
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $notification = $this->oneSignalService->getNotificationById($id);

        return $this->successResponse(
            new OneSignalResource($notification),
            'notification.detail_success'
        );
    }

    /**
     * Tek bir kullanıcıya bildirim gönderir
     *
     * @param OneSignalCustomNotificationRequest $request
     * @return JsonResponse
     */
    public function sendToUser(OneSignalCustomNotificationRequest $request): JsonResponse
    {
        $result = $this->oneSignalService->sendNotificationToUser($request->validated());

        if ($result) {
            return $this->successResponse(
                [],
                'notification.send_to_user_success'
            );
        }

        return $this->errorResponse('notification.send_to_user_error', 500);
    }

    /**
     * Belirli bir segmente bildirim gönderir
     *
     * @param OneSignalSegmentNotificationRequest $request
     * @return JsonResponse
     */
    public function sendToSegment(OneSignalSegmentNotificationRequest $request): JsonResponse
    {
        $result = $this->oneSignalService->sendNotificationToSegment($request->validated());

        if ($result) {
            return $this->successResponse(
                [],
                'notification.send_to_segment_success'
            );
        }

        return $this->errorResponse('notification.send_to_segment_error', 500);
    }

    /**
     * Tüm kullanıcılara bildirim gönderir
     *
     * @param OneSignalBroadcastNotificationRequest $request
     * @return JsonResponse
     */
    public function sendToAll(OneSignalBroadcastNotificationRequest $request): JsonResponse
    {
        $result = $this->oneSignalService->sendNotificationToAll($request->validated());

        if ($result) {
            return $this->successResponse(
                [],
                'notification.broadcast_success'
            );
        }

        return $this->errorResponse('notification.broadcast_error', 500);
    }

    /**
     * Bildirim iptal eder
     *
     * @param string $notificationId
     * @return JsonResponse
     */
    public function cancelNotification(string $notificationId): JsonResponse
    {
        $result = $this->oneSignalService->cancelNotification($notificationId);

        if ($result) {
            return $this->successResponse(
                [],
                'notification.cancel_success'
            );
        }

        return $this->errorResponse('notification.cancel_error', 500);
    }

    /**
     * Bildirim şablonu oluşturur
     *
     * @param OneSignalTemplateRequest $request
     * @return JsonResponse
     */
    public function createTemplate(OneSignalTemplateRequest $request): JsonResponse
    {
        $template = $this->oneSignalService->createNotificationTemplate($request->validated());

        if ($template) {
            return $this->successResponse(
                new NotificationTemplateResource($template),
                'notification.template_create_success'
            );
        }

        return $this->errorResponse('notification.template_create_error', 500);
    }

    /**
     * Bildirim şablonunu günceller
     *
     * @param int $id
     * @param OneSignalTemplateRequest $request
     * @return JsonResponse
     */
    public function updateTemplate(int $id, OneSignalTemplateRequest $request): JsonResponse
    {
        $template = $this->oneSignalService->updateNotificationTemplate($id, $request->validated());

        if ($template) {
            return $this->successResponse(
                new NotificationTemplateResource($template),
                'notification.template_update_success'
            );
        }

        return $this->errorResponse('notification.template_update_error', 500);
    }

    /**
     * Bildirim şablonunu siler
     *
     * @param int $id
     * @return JsonResponse
     */
    public function deleteTemplate(int $id): JsonResponse
    {
        $result = $this->oneSignalService->deleteNotificationTemplate($id);

        if ($result) {
            return $this->successResponse(
                [],
                'notification.template_delete_success'
            );
        }

        return $this->errorResponse('notification.template_delete_error', 500);
    }

    /**
     * Bildirim istatistiklerini getirir
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getStatistics(Request $request): JsonResponse
    {
        $filters = $request->only(['date_from', 'date_to', 'type']);
        $statistics = $this->oneSignalService->getNotificationStatistics($filters);

        return $this->successResponse(
            $statistics,
            'notification.statistics_success'
        );
    }
} 