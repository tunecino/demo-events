import "./App.css";
import EventsList from "./components/EventsList";
import ThemeToggle from "./components/ThemeToggle";

function App() {
  return (
    <>
      <div className="flex justify-end p-4">
        <ThemeToggle />
      </div>
      <EventsList />
    </>
  );
}

export default App;
