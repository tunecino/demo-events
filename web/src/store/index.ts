import { configureStore } from "@reduxjs/toolkit";
import eventsReducer, {
  type EventsState,
  fetchEvents,
  holdSlot,
  bookSlot,
  releaseHold,
} from "./eventsSlice";
import { useDispatch } from "react-redux";

export const useAppDispatch = () => useDispatch<AppDispatch>();

export interface RootState {
  events: EventsState;
}

export const store = configureStore({
  reducer: {
    events: eventsReducer,
  },
});

export type AppDispatch = typeof store.dispatch;
export { fetchEvents, holdSlot, bookSlot, releaseHold };
export default store;
