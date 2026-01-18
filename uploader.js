// ------------------------------------------------------------
// Prevent browser from opening dropped files anywhere
// ------------------------------------------------------------
["dragover", "drop"].forEach(eventName => {
    window.addEventListener(eventName, e => e.preventDefault());
    document.addEventListener(eventName, e => e.preventDefault());
    document.body.addEventListener(eventName, e => e.preventDefault());
});

// ------------------------------------------------------------
// Chunk uploader settings
// ------------------------------------------------------------
const CHUNK_SIZE = 1024 * 1024 * 2; // 2MB
const MAX_PARALLEL_UPLOADS = 2;

let uploadQueue = [];
let activeUploads = 0;

// ------------------------------------------------------------
// DOM Ready
// ------------------------------------------------------------
document.addEventListener("DOMContentLoaded", () => {

    const dropzone = document.getElementById("dropzone");
    const fileInput = document.getElementById("fileInput");
    const previewContainer = document.getElementById("previewContainer");

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
        handleFiles(e.dataTransfer.files);
    });

    // Click to open file picker
    dropzone.addEventListener("click", () => fileInput.click());

    fileInput.addEventListener("change", () => {
        if (fileInput.files.length) {
            handleFiles(fileInput.files);
        }
    });

    // --------------------------------------------------------
    // Handle incoming files
    // --------------------------------------------------------
    function handleFiles(files) {
        for (let file of files) {
            const id = crypto.randomUUID();

            const preview = createPreviewElement(file);

            uploadQueue.push({
                id,
                file,
                preview,          // { wrapper, bar }
                status: "pending",
                uploadedBytes: 0
            });
        }

        processQueue();
    }

    // --------------------------------------------------------
    // Create preview card
    // --------------------------------------------------------
function createPreviewElement(file) {
    const wrapper = document.createElement("div");
    wrapper.className = "preview";

    let media;

    if (file.type.startsWith("image/")) {
        // IMAGE PREVIEW
        media = document.createElement("img");
        media.src = URL.createObjectURL(file);
    }
    else if (file.type.startsWith("video/")) {
        // VIDEO PREVIEW
        media = document.createElement("video");
        media.src = URL.createObjectURL(file);
        media.muted = true;
        media.autoplay = true;
        media.loop = true;
        media.playsInline = true;
    }
    else {
        // FALLBACK ICON
        media = document.createElement("div");
        media.textContent = "FILE";
        media.style.width = "60px";
        media.style.height = "60px";
        media.style.display = "flex";
        media.style.alignItems = "center";
        media.style.justifyContent = "center";
        media.style.background = "#ddd";
        media.style.borderRadius = "4px";
    }

    media.style.width = "60px";
    media.style.height = "60px";
    media.style.objectFit = "cover";
    media.style.borderRadius = "4px";

    const info = document.createElement("div");
    info.innerHTML = `<strong>${file.name}</strong>`;

    const progress = document.createElement("div");
    progress.className = "progress";

    const bar = document.createElement("div");
    bar.className = "progress-bar";

    progress.appendChild(bar);
    wrapper.appendChild(media);
    wrapper.appendChild(info);
    wrapper.appendChild(progress);

    previewContainer.appendChild(wrapper);

    return { wrapper, bar };
}

    // --------------------------------------------------------
    // Queue processor
    // --------------------------------------------------------
    function processQueue() {
        if (activeUploads >= MAX_PARALLEL_UPLOADS) return;

        const next = uploadQueue.find(f => f.status === "pending");
        if (!next) return;

        next.status = "uploading";
        activeUploads++;

        uploadFileInChunks(next).then(() => {
            activeUploads--;
            processQueue();
        }).catch(() => {
            activeUploads--;
            processQueue();
        });
    }

    // --------------------------------------------------------
    // Chunk upload engine
    // --------------------------------------------------------
    async function uploadFileInChunks(item) {
        const file = item.file;
        const totalChunks = Math.ceil(file.size / CHUNK_SIZE);

        for (let index = 0; index < totalChunks; index++) {
            const start = index * CHUNK_SIZE;
            const end = Math.min(start + CHUNK_SIZE, file.size);
            const chunk = file.slice(start, end);

            const formData = new FormData();
            formData.append("chunk", chunk);
            formData.append("fileId", item.id);
            formData.append("fileName", file.name);
            formData.append("chunkIndex", index);
            formData.append("totalChunks", totalChunks);

            await fetch("upload_chunk.php", {
                method: "POST",
                body: formData
            });

            const progress = Math.round(((index + 1) / totalChunks) * 100);
            item.preview.bar.style.width = progress + "%";
        }

        // Finalize
        const finalizeData = new FormData();
        finalizeData.append("fileId", item.id);
        finalizeData.append("fileName", file.name);

        await fetch("finalize_upload.php", {
            method: "POST",
            body: finalizeData
        });

        item.preview.bar.style.width = "100%";
    }
});