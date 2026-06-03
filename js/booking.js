const appointmentDate = document.getElementById("appointmentDate");
const appointmentTime = document.getElementById("appointmentTime");
const bookingForm = document.getElementById("bookingForm");
const bookingMessage = document.getElementById("bookingMessage");

const allSlots = [
  "09:00",
  "10:00",
  "11:00",
  "12:00",
  "13:00",
  "14:00",
  "15:00",
  "16:00"
];

appointmentDate.addEventListener("change", () => {
  appointmentTime.innerHTML = "";

  allSlots.forEach(slot => {
    const option = document.createElement("option");
    option.value = slot;
    option.textContent = slot;
    appointmentTime.appendChild(option);
  });
});

bookingForm.addEventListener("submit", async (e) => {
  e.preventDefault();

  const data = {
    client_name: document.getElementById("clientName").value,
    phone: document.getElementById("phone").value,
    service: document.getElementById("service").value,
    appointment_date: appointmentDate.value,
    appointment_time: appointmentTime.value
  };

  try {
    const response = await fetch("api/book.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify(data)
    });

    const result = await response.json();

    if (!response.ok) {
      bookingMessage.textContent = result.error;
      return;
    }

    bookingMessage.textContent = result.message;
    bookingForm.reset();
  } catch (error) {
    bookingMessage.textContent = "Възникна грешка. Моля, опитайте отново.";
  }
});
