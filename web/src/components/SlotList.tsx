import React, { useEffect, useState } from "react";
import { type Slot } from "../store/eventsSlice";
import { browserUserId } from "../utils";

interface SlotListProps {
  slots: Slot[];
  onSlotClick: (slotId: string) => void;
  isLoading: boolean;
}

const SlotList: React.FC<SlotListProps> = ({
  slots,
  onSlotClick,
  isLoading,
}) => {
  const [userId, setUserId] = useState<string | null>(null);

  useEffect(() => {
    browserUserId().then(setUserId);
  }, []);

  if (isLoading) {
    return (
      <div className="flex justify-center">
        <span className="loading loading-spinner"></span>
      </div>
    );
  }

  const getTextStatus = (slot: Slot): "hold" | "sold" | "booked" | null => {
    if (slot.status === "hold") return "hold";
    if (slot.status === "booked") {
      if (!userId) return "booked"; // before userId is loaded
      return slot.user_id === userId ? "booked" : "sold";
    }
    return null;
  };

  const renderStatusLabel = (status: ReturnType<typeof getTextStatus>) => {
    if (status === "booked") {
      return <span className="text-xs text-success font-medium">Booked</span>;
    }
    if (status === "sold") {
      return (
        <span className="text-xs text-warning/50 font-medium">Sold Out</span>
      );
    }
    if (status === "hold") {
      return <span className="text-xs text-gray-500 font-medium">On Hold</span>;
    }
    return null;
  };

  return (
    <ul className="menu bg-base-100 rounded-box p-4 w-full">
      {slots.map((slot) => (
        <li key={slot.id} className="block">
          <button
            className="btn btn-ghost w-full justify-start"
            onClick={() => onSlotClick(slot.id)}
            disabled={slot.status !== "available"}
          >
            <div className="flex justify-between w-full">
              <div>
                {slot.start_at.slice(11, 16)} - {slot.end_at.slice(11, 16)}
              </div>
              <div>{renderStatusLabel(getTextStatus(slot))}</div>
            </div>
          </button>
        </li>
      ))}
      {slots.length === 0 && (
        <li className="text-center text-gray-500">No slots available</li>
      )}
    </ul>
  );
};

export default SlotList;
