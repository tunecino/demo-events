import React, { useEffect, useState } from "react";
import Dinero, { type Currency } from "dinero.js";

import {
  bookSlot,
  holdSlot,
  releaseHold,
  type Event,
  type Slot,
} from "../store/eventsSlice";
import { browserUserId, formatEventDate } from "../utils";
import SlotList from "./SlotList";
import PaymentModal from "./PaymentModal";
import { PiArmchair, PiX } from "react-icons/pi";
import { useAppDispatch } from "../store";

interface EventCardProps {
  event: Event;
}

const EventCard: React.FC<EventCardProps> = ({ event }) => {
  const [selectedSlotId, setSelectedSlotId] = useState<string | null>(null);
  const [showModal, setShowModal] = useState(false);
  const [isLoading, setIsLoading] = useState(false);
  const [isBooked, setIsBooked] = useState(false);

  useEffect(() => {
    /**
     * check if user has booked any of the slots
     * so the card could be marked as booked.
     */
    browserUserId().then((userId) => {
      const booked = event.slots.some((slot) => slot.user_id === userId);
      setIsBooked(booked);
    });
  }, [event?.slots]);

  const availableSlots = event.slots.filter(
    (slot: Slot) => slot.status === "available",
  );
  const availableSeats = availableSlots.length;
  const price = Dinero({
    amount: event.amount,
    currency: event.currency as Currency,
  }).toFormat();

  const dispatch = useAppDispatch();

  const handleSlotClick = async (slotId: string) => {
    setIsLoading(true);
    setSelectedSlotId(slotId);
    await dispatch(holdSlot({ eventId: event.id, slotId }));
    setIsLoading(false);
    setShowModal(true);
  };

  const handlePay = async () => {
    setShowModal(false);
    setIsLoading(true);
    await dispatch(bookSlot({ eventId: event.id, slotId: selectedSlotId! }));
    setIsLoading(false);
    setIsBooked(true);
  };

  const handleCancel = async () => {
    setIsLoading(true);
    await dispatch(releaseHold({ eventId: event.id, slotId: selectedSlotId! }));
    setIsLoading(false);
    setShowModal(false);
  };

  const [isFlipped, setIsFlipped] = useState(false);

  const handleFlip = () => {
    setIsFlipped(!isFlipped);
  };

  return (
    <>
      <div className="flex flex-col items-center justify-center ">
        <div className="group h-120 w-88 [perspective:1000px] ">
          <div
            className={`relative h-full w-full rounded-xl shadow-xl transition-all duration-400 [transform-style:preserve-3d] ${isFlipped ? "[transform:rotateY(-180deg)]" : ""}`}
          >
            {/* Front side of the card */}
            <div className="absolute inset-0 backface-hidden">
              <div className="flex h-full flex-col items-center justify-center card shadow-xl bg-base-200 border border-base-300">
                {/* Front */}
                <div className="card-body">
                  <figure className="mb-4">
                    <img
                      src={event.image}
                      alt={event.name}
                      className="rounded-xl w-full h-48 object-cover"
                    />
                  </figure>
                  <h2 className="card-title text-xl font-bold">{event.name}</h2>
                  <div className="flex items-center justify-center space-x-2">
                    {/* seats */}
                    <div
                      className="badge badge-soft tooltip tooltip-primary tooltip-right"
                      data-tip="Available Seats"
                    >
                      <PiArmchair />
                      {availableSeats}
                    </div>
                    {/* date */}
                    <p className="text-sm text-base-content/70 uppercase font-bold">
                      {formatEventDate(event.start_at)}
                    </p>
                  </div>

                  <p className="text-gray-600 mb-2">{event.description}</p>

                  <div className="flex justify-between items-center mt-auto">
                    <div className="flex flex-col">
                      {!isBooked && availableSeats === 0 && (
                        <button
                          className="btn btn-warning btn-soft"
                          onClick={handleFlip}
                        >
                          Sold Out
                        </button>
                      )}
                      {!isBooked && availableSeats > 0 && (
                        <button
                          className="btn btn-primary"
                          onClick={handleFlip}
                        >
                          Book it
                        </button>
                      )}
                      {isBooked && (
                        <button
                          className="btn btn-success btn-soft"
                          onClick={handleFlip}
                        >
                          Booked
                        </button>
                      )}
                    </div>

                    <div className="text-right">
                      <span className="text-sm text-gray-500 block">Price</span>
                      <span className="text-xl font-bold">{price}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            {/* Back side of the card */}
            <div className="absolute inset-0 [transform:rotateY(-180deg)] backface-hidden">
              <div className="flex h-full flex-col items-center justify-center card shadow-xl bg-base-200 border border-base-300">
                <div className="w-full pt-2 px-2 flex justify-end">
                  <button
                    onClick={handleFlip}
                    className="btn btn-circle btn-ghost text-xl"
                  >
                    <PiX />
                  </button>
                </div>
                {/* Back */}
                <div className="card-body w-full px-6 py-2">
                  <h2 className="text-sm text-base-content/70 uppercase font-bold text-center pb-2">
                    Available Slots
                  </h2>
                  <SlotList
                    slots={event.slots}
                    onSlotClick={handleSlotClick}
                    isLoading={isLoading}
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <PaymentModal
        show={showModal}
        onPay={handlePay}
        onCancel={handleCancel}
        isLoading={isLoading}
        price={price}
      />
    </>
  );
};

export default EventCard;
