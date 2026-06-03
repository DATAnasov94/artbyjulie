const appointmentDate = document.getElementById("appointmentDate");
const appointmentTime = document.getElementById("appointmentTime");
const bookingForm = document.getElementById("bookingForm");
const bookingMessage = document.getElementById("bookingMessage");

appointmentDate.addEventListener("change", async () => {
  const selectedDate = appointmentDate.value;

  appointmentTime.innerHTML = "<option>Зареждане...</option>";

  try {
    const response = await fetch(`api/get_slots.php?date=${selectedDate}`);
    const result = await response.json();

    appointmentTime.innerHTML = "";

    if (!result.availableSlots || result.availableSlots.length === 0) {
      appointmentTime.innerHTML = "<option value=''>Няма свободни часове</option>";
      return;
    }

    result.availableSlots.forEach(slot => {
      const cleanSlot = slot.slice(0, 5);

      const option = document.createElement("option");
      option.value = cleanSlot;
      option.textContent = cleanSlot;

      appointmentTime.appendChild(option);
    });
  } catch (error) {
    appointmentTime.innerHTML = "<option value=''>Грешка при зареждане</option>";
  }
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
    appointmentTime.innerHTML = "<option value=''>Първо избери дата</option>";
  } catch (error) {
    bookingMessage.textContent = "Възникна грешка. Моля, опитайте отново.";
  }
});
