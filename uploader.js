// ------------------------------------------------------------
// 1. Prevent browser from opening dropped files anywhere
// ------------------------------------------------------------
["dragover", "drop"].forEach(eventName => {
    window.addEventListener(eventName, e => e.preventDefault());
    document.addEventListener(eventName, e => e.preventDefault());
    document.body.addEventListener(eventName, e => e.preventDefault());
});

// ------------------------------------------------------------
// 2. AJAX upload logic
// ------------------------------------------------------------
document.addEventListener("DOMContentLoaded", () => {

    const dropzone = document.getElementById("dropzone");
    const fileInput = document.getElementById("fileInput");
    const output = document.getElementById("output");

    // Highlight drop zone
    dropzone.addEventListener("dragover", (e) => {
        e.preventDefault();
        e.stopPropagation();
        dropzone.classList.add("dragover");
    });

    dropzone.addEventListener("dragleave", (e) => {
        e.preventDefault();
        e.stopPropagation();
        dropzone.classList.remove("dragover");
    });

    // Handle drop
    dropzone.addEventListener("drop", (e) => {
        e.preventDefault();
        e.stopPropagation();
        dropzone.classList.remove("dragover");

        const files = e.dataTransfer.files;
        if (!files.length) return;

        uploadFiles(files);
    });

    // Optional: clicking dropzone opens file picker
    dropzone.addEventListener("click", () => fileInput.click());

    fileInput.addEventListener("change", () => {
        if (fileInput.files.length) {
            uploadFiles(fileInput.files);
        }
    });

    // --------------------------------------------------------
    // Core AJAX uploader
    // --------------------------------------------------------
    function uploadFiles(files) {
        const formData = new FormData();

        for (let i = 0; i < files.length; i++) {
            formData.append("media[]", files[i]);
        }

        output.innerHTML = "Uploading...";

        fetch("upload.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            output.innerHTML = "";

            data.forEach(item => {
                if (item.success) {
                    output.innerHTML += `Uploaded: ${item.path}<br>`;
                } else {
                    output.innerHTML += `Error: ${item.error}<br>`;
                }
            });
        })
        .catch(err => {
            output.innerHTML = "Upload failed.";
            console.error(err);
        });
    }
});