import React, { useEffect } from "react";
import { useDispatch, useSelector } from "react-redux";
import { fetchEvents, type AppDispatch, type RootState } from "../store";
import EventCard from "./EventCard";

const EventsList: React.FC = () => {
  const dispatch = useDispatch<AppDispatch>();
  const { events, loading, error } = useSelector(
    (state: RootState) => state.events,
  );

  useEffect(() => {
    dispatch(fetchEvents());
  }, [dispatch]);

  if (loading)
    return (
      <div className="flex justify-center items-center h-64">
        <span className="loading loading-spinner loading-lg"></span>
      </div>
    );
  if (error)
    return (
      <div className="alert alert-error">
        <span>{error}</span>
      </div>
    );

  return (
    <div className="w-full md:w-2/3 xl:w-1/2 mx-auto pb-20 px-8">
      <h1 className="uppercase my-8 text-center text-base-content/30">
        Upcoming Events
      </h1>
      <div className="grid grid-cols-1 xl:grid-cols-2 gap-6">
        {events.map((event) => (
          <EventCard key={event.id} event={event} />
        ))}
      </div>
    </div>
  );
};

export default EventsList;
