import { format, parseISO } from "date-fns";

export const formatEventDate = (dateString: string): string => {
  const date = parseISO(dateString);
  return format(date, "MMMM d"); // e.g., "October 4"
};
