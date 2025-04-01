import { useEffect, useState } from "react";
import "./App.css";

function App() {
  const [count, setCount] = useState(0);

  useEffect(() => {
    const fetchAPI = async () => {
      const res = await fetch("http://localhost:8080/messages", {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
        },
      });
      const json = await res.json();

      console.log(json)
    };
    fetchAPI()
  }, []);

  return (
    <>
      <h1>React config</h1>
    </>
  );
}

export default App;
