<?php

namespace Src\Event\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\SlotResource;
use Illuminate\Http\JsonResponse;
use Src\Event\Application\Services\UserIdProviderInterface;
use Src\Event\Application\UseCases\BookSlotUseCase;
use Src\Event\Application\UseCases\HoldSlotUseCase;
use Src\Event\Application\UseCases\UnholdSlotUseCase;
use Src\Event\Domain\Exceptions\SlotNotAvailableException;
use Src\Event\Domain\Exceptions\SlotNotHeldException;
use Src\Event\Domain\Exceptions\SlotNotFoundException;
use Src\Event\Domain\Exceptions\UserMismatchException;

final class SlotController extends Controller
{
    public function __construct(
        private readonly HoldSlotUseCase $holdSlotUseCase,
        private readonly UnholdSlotUseCase $unholdSlotUseCase,
        private readonly BookSlotUseCase $bookSlotUseCase,
        private readonly UserIdProviderInterface $userIdProvider
    ) {}

    public function hold(string $eventId, string $slotId): JsonResponse
    {
        try {
            $userId = $this->userIdProvider->getUserId();
            $this->holdSlotUseCase->execute($eventId, $slotId, $userId);
            return response()->json([
                'status' => 'hold',
                'user_id' => $userId,
            ]);
        } catch (SlotNotFoundException $e) {
            return response()->json(['error' => 'Slot does not belong to this event'], 404);
        } catch (SlotNotAvailableException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }

    public function unhold(string $eventId, string $slotId): JsonResponse
    {
        try {
            $userId = $this->userIdProvider->getUserId();
            $slotData = $this->unholdSlotUseCase->execute($eventId, $slotId, $userId);
            return response()->json([
                'message' => 'Slot released successfully.',
                'data' => new SlotResource($slotData),
            ]);
        } catch (SlotNotHeldException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (UserMismatchException $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }

    public function book(string $eventId, string $slotId): JsonResponse
    {
        try {
            $userId = $this->userIdProvider->getUserId();
            $this->bookSlotUseCase->execute($eventId, $slotId, $userId);
            return response()->json([
                'status' => 'booked',
                'user_id' => $userId,
            ]);
        } catch (SlotNotFoundException $e) {
            return response()->json(['error' => 'Slot does not belong to this event'], 404);
        } catch (SlotNotHeldException $e) {
            return response()->json(['error' => 'Only held slots can be booked'], 422);
        } catch (UserMismatchException $e) {
            return response()->json(['message' => 'Forbidden action'], 403);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }
}