$(document).ready(function() {
    $("#addDataForm").submit(function(e) {
        e.preventDefault(); // Prevent default form submission
        var data = $(this).serialize(); // Serialize form data

        $.ajax({
            url: "/api/add_data.php",
            type: "POST",
            data: data,
            dataType: "json", // Expect JSON response
            success: function(response) {
                if (response.status === "success") {
                    $("#addDataModal").modal("hide"); // Hide modal
                    $("#addDataForm")[0].reset(); // Reset form
                    $("#usersTable").DataTable().ajax.reload(); // Reload DataTable
                    Toastify({
                        text: "Data added successfully",
                        duration: 3000,
                        gravity: "top", // Position: 'top' or 'bottom'
                        position: "right", // Position: 'left', 'center' or 'right'
                        backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
                        close: true
                    }).showToast();
                } else {
                    Toastify({
                        text: "Failed to add data: " + response.message,
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "linear-gradient(to right, #ff5f6d, #ffc371)",
                        close: true
                    }).showToast();
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error);
                Toastify({
                    text: "An error occurred while processing your request. Please try again.",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "linear-gradient(to right, #ff5f6d, #ffc371)",
                    close: true
                }).showToast();
            }
        });
    });
});
