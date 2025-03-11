document.addEventListener("DOMContentLoaded", function () {
    fetchProfile();

    document.getElementById("editProfileBtn").addEventListener("click", openModal);
    document.getElementById("cancelEditBtn").addEventListener("click", closeModal);
    document.getElementById("editProfileForm").addEventListener("submit", saveProfile);
});

function fetchProfile() {
    fetch("get_profile.php")
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById("nameDisplay").textContent = data.user.name;
                document.getElementById("ageDisplay").textContent = data.user.age;
                document.getElementById("heightDisplay").textContent = data.user.height + " cm";
                document.getElementById("weightDisplay").textContent = data.user.weight + " kg";

                document.getElementById("editName").value = data.user.name;
                document.getElementById("editAge").value = data.user.age;
                document.getElementById("editHeight").value = data.user.height;
                document.getElementById("editWeight").value = data.user.weight;
            } else {
                console.error("Error fetching profile:", data.error);
            }
        })
        .catch(error => console.error("Fetch error:", error));
}

function openModal() {
    document.getElementById("editProfileModal").classList.remove("hidden");
}

function closeModal() {
    document.getElementById("editProfileModal").classList.add("hidden");
}

function saveProfile(event) {
    event.preventDefault();

    let updatedProfile = {
        name: document.getElementById("editName").value,
        age: document.getElementById("editAge").value,
        height: document.getElementById("editHeight").value,
        weight: document.getElementById("editWeight").value
    };

    fetch("update_profile.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(updatedProfile)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            fetchProfile();  // Refresh profile display
            closeModal();
        } else {
            console.error("Error updating profile:", data.error);
        }
    })
    .catch(error => console.error("Update error:", error));
}
