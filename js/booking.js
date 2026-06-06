document.addEventListener("DOMContentLoaded", () => {
  const serviceCategory = document.getElementById("serviceCategory");
  const service = document.getElementById("service");
  const appointmentDate = document.getElementById("appointmentDate");
  const appointmentTime = document.getElementById("appointmentTime");
  const bookingForm = document.getElementById("bookingForm");
  const bookingMessage = document.getElementById("bookingMessage");

  const services = {
    gel: [
      "Укрепване с гел - къси нокти",
      "Укрепване с гел - средни нокти",
      "Укрепване с гел - дълги нокти",
      "Укрепване с гел - екстремно дълги"
    ],
    extensions: [
      "Ноктопластика - къси нокти",
      "Ноктопластика - средни нокти",
      "Ноктопластика - дълги нокти",
      "Ноктопластика - екстремно дълги",
      "Ноктопластика - изграждане на един нокът"
    ],
    pedicure: [
      "Педикюр - основен без покритие",
      "Педикюр - цялостен с покритие"
    ],
    japanese: [
      "Японски маникюр"
    ],
    decorations: [
      "Декорация - бърза",
      "Декорация - рисувана",
      "Декорация - релефна",
      "Декорация - вградена"
    ]
  };

  serviceCategory.addEventListener("change", () => {
    const selectedCategory = serviceCategory.value;

    service.innerHTML = "";

    if (!selectedCategory || !services[selectedCategory]) {
      service.innerHTML = "<option value=''>Първо избери категория</option>";
      return;
    }

    service.innerHTML = "<option value=''>Избери услуга</option>";

    services[selectedCategory].forEach((item) => {
      const option = document.createElement("option");
      option.value = item;
      option.textContent = item;
      service.appendChild(option);
    });
  });

  appointmentDate.addEventListener("change", async () => {
    const selectedDate = appointmentDate.value;

    if (!selectedDate) {
      appointmentTime.innerHTML = "<option value=''>Първо избери дата</option>";
      return;
    }

    appointmentTime.innerHTML = "<option value=''>Зареждане...</option>";

    try {
      const response = await fetch(`api/get_slots.php?date=${selectedDate}`);
      const result = await response.json();

      appointmentTime.innerHTML = "";

      if (!result.availableSlots || result.availableSlots.length === 0) {
        appointmentTime.innerHTML = "<option value=''>Няма свободни часове</option>";
        return;
      }

      result.availableSlots.forEach((slot) => {
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
      service: service.value,
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
        bookingMessage.textContent = result.error || "Възникна грешка.";
        return;
      }

      bookingMessage.textContent = result.message;
      bookingForm.reset();

      service.innerHTML = "<option value=''>Първо избери категория</option>";
      appointmentTime.innerHTML = "<option value=''>Първо избери дата</option>";
    } catch (error) {
      bookingMessage.textContent = "Възникна грешка. Моля, опитайте отново.";
    }
  });
});