document.getElementById("userForm").addEventListener("submit", async (e) => {
  e.preventDefault();

  const formData = new FormData(e.target);
  const userId = formData.get("user");
  if (!userId) return alert("Please select a user.");

  try {
      const response = await fetch(`data.php?user=${userId}`);
      if (!response.ok) throw new Error("Failed to fetch data");

      const transactions = await response.json();

      document.querySelector("#userName span").textContent = e.target.user.options[e.target.user.selectedIndex].text;
      const dataBody = document.getElementById("dataBody");
      dataBody.innerHTML = transactions.map(({ month, balance }) => `
          <tr>
              <td>${month}</td>
              <td>${balance}</td>
          </tr>`).join("");

      document.getElementById("data").style.display = "block";
  } catch (error) {
      console.error(error);
      alert("An error occurred while fetching data.");
  }
});