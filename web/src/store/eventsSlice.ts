import { createSlice, createAsyncThunk } from "@reduxjs/toolkit";
import axios, { AxiosError } from "axios";

export interface Slot {
  id: string;
  start_at: string;
  end_at: string;
  status: "available" | "hold" | "booked";
  user_id: string | null;
}

export interface Event {
  id: string;
  name: string;
  description: string;
  image: string;
  start_at: string;
  end_at: string;
  amount: number;
  currency: string;
  slots: Slot[];
}

export interface EventsState {
  events: Event[];
  loading: boolean;
  error: string | null;
}

interface ApiError {
  message: string;
}

const initialState: EventsState = {
  events: [],
  loading: false,
  error: null,
};

const API_BASE_URL = "http://localhost:8000/api";

export const fetchEvents = createAsyncThunk<
  Event[],
  void,
  { rejectValue: string }
>("events/fetchEvents", async (_, { rejectWithValue }) => {
  try {
    const response = await axios.get<{ data: Event[] }>(
      `${API_BASE_URL}/events`,
    );
    return response.data.data;
  } catch (error) {
    const axiosError = error as AxiosError<ApiError>;
    return rejectWithValue(
      axiosError.response?.data?.message || "Failed to fetch events",
    );
  }
});

export const holdSlot = createAsyncThunk<
  { eventId: string; slotId: string; data: Slot },
  { eventId: string; slotId: string },
  { rejectValue: string }
>("events/holdSlot", async ({ eventId, slotId }, { rejectWithValue }) => {
  try {
    const response = await axios.put<Slot>(
      `${API_BASE_URL}/events/${eventId}/slots/${slotId}/hold`,
    );
    return { eventId, slotId, data: response.data };
  } catch (error) {
    const axiosError = error as AxiosError<ApiError>;
    return rejectWithValue(
      axiosError.response?.data?.message || "Failed to hold slot",
    );
  }
});

export const bookSlot = createAsyncThunk<
  { eventId: string; slotId: string; data: Slot },
  { eventId: string; slotId: string },
  { rejectValue: string }
>("events/bookSlot", async ({ eventId, slotId }, { rejectWithValue }) => {
  try {
    const response = await axios.put<Slot>(
      `${API_BASE_URL}/events/${eventId}/slots/${slotId}/book`,
    );
    return { eventId, slotId, data: response.data };
  } catch (error) {
    const axiosError = error as AxiosError<ApiError>;
    return rejectWithValue(
      axiosError.response?.data?.message || "Failed to book slot",
    );
  }
});

export const releaseHold = createAsyncThunk<
  { eventId: string; slotId: string; data: Slot },
  { eventId: string; slotId: string },
  { rejectValue: string }
>("events/releaseHold", async ({ eventId, slotId }, { rejectWithValue }) => {
  try {
    const response = await axios.delete<Slot>(
      `${API_BASE_URL}/events/${eventId}/slots/${slotId}/hold`,
    );
    return { eventId, slotId, data: response.data };
  } catch (error) {
    const axiosError = error as AxiosError<ApiError>;
    return rejectWithValue(
      axiosError.response?.data?.message || "Failed to release hold",
    );
  }
});

const eventsSlice = createSlice({
  name: "events",
  initialState,
  reducers: {
    updateSlot(
      state,
      action: {
        payload: { eventId: string; slotId: string; data: Slot };
        type: string;
      },
    ) {
      const { eventId, slotId, data } = action.payload;
      const event = state.events.find((e) => e.id === eventId);
      if (event) {
        const slot = event.slots.find((s) => s.id === slotId);
        if (slot) {
          slot.status = data.status;
          slot.user_id = data.user_id;
        }
      }
    },
  },
  extraReducers: (builder) => {
    builder
      .addCase(fetchEvents.pending, (state) => {
        state.loading = true;
        state.error = null;
      })
      .addCase(fetchEvents.fulfilled, (state, action) => {
        state.loading = false;
        state.events = action.payload;
      })
      .addCase(fetchEvents.rejected, (state, action) => {
        state.loading = false;
        state.error = action.payload || "Unknown error";
      })
      .addCase(holdSlot.fulfilled, (state, action) => {
        const { eventId, slotId, data } = action.payload;
        const event = state.events.find((e) => e.id === eventId);
        if (event) {
          const slot = event.slots.find((s) => s.id === slotId);
          if (slot) {
            slot.status = data.status;
            slot.user_id = data.user_id;
          }
        }
      })
      .addCase(bookSlot.fulfilled, (state, action) => {
        const { eventId, slotId, data } = action.payload;
        const event = state.events.find((e) => e.id === eventId);
        if (event) {
          const slot = event.slots.find((s) => s.id === slotId);
          if (slot) {
            slot.status = data.status;
            slot.user_id = data.user_id;
          }
        }
      })
      .addCase(releaseHold.fulfilled, (state, action) => {
        const { eventId, slotId } = action.payload;
        const event = state.events.find((e) => e.id === eventId);
        if (event) {
          const slot = event.slots.find((s) => s.id === slotId);
          if (slot) {
            slot.status = "available";
            slot.user_id = null;
          }
        }
      });
  },
});

export const { updateSlot } = eventsSlice.actions;
export default eventsSlice.reducer;
