<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login_page.php");
    exit;
}

$item_id = intval($_GET['id'] ?? 0);
?>
<!DOCTYPE html>
<html>
<head>
<title>Edit Item</title>

<!-- ⭐ FULL ORIGINAL CSS FROM YOUR FILE ⭐ -->
<style>
:root {
    --isu-orange: #F77F00;
    --isu-black: #1A1A1A;
    --isu-gray: #F7F7F7;
    --isu-border: #D9D9D9;
}

/* Container */
.edit-container {
    max-width: 900px;
    margin: 40px auto;
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.15);
}

/* Title */
.edit-container h2 {
    color: var(--isu-orange);
    border-left: 6px solid var(--isu-orange);
    padding-left: 12px;
    font-size: 26px;
}

/* Labels & Inputs */
label {
    display: block;
    margin-top: 15px;
    font-weight: bold;
    color: var(--isu-black);
}

input[type="text"], textarea, select {
    width: 100%;
    padding: 10px;
    border: 1px solid var(--isu-border);
    border-radius: 6px;
    font-size: 15px;
    margin-top: 5px;
    background: white;
}

textarea {
    height: 90px;
}

/* Button */
button {
    background: var(--isu-orange);
    padding: 12px 22px;
    border-radius: 6px;
    border: none;
    color: white;
    font-size: 16px;
    margin-top: 18px;
    cursor: pointer;
    font-weight: bold;
}
button:hover {
    background: #d56c00;
}

/* History */
.history-title {
    margin-top: 40px;
    font-size: 22px;
    font-weight: bold;
    color: var(--isu-orange);
}

.history-card {
    background: var(--isu-gray);
    border-left: 5px solid var(--isu-orange);
    padding: 18px;
    margin-top: 18px;
    border-radius: 8px;
}

.history-card h4 {
    margin-bottom: 6px;
    color: var(--isu-black);
}

.history-meta {
    font-size: 13px;
    color: #555;
    margin-bottom: 10px;
}

.old span, .new span {
    font-weight: bold;
    color: var(--isu-black);
}
</style>

<script>
// Load item details
document.addEventListener("DOMContentLoaded", () => {
    fetch("api/get_item.php?id=<?= $item_id ?>")
        .then(res => res.json())
        .then(data => {
            if (!data.item) {
                alert("Item not found");
                return;
            }
            document.querySelector("[name=title]").value = data.item.title;
            document.querySelector("[name=description]").value = data.item.description;
            document.querySelector("[name=location]").value = data.item.location;
            document.querySelector("[name=status]").value = data.item.status;
        });

    loadHistory();
});

// Load change history
function loadHistory() {
    fetch("api/item_history.php?id=<?= $item_id ?>")
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById("history");
            container.innerHTML = "";

            data.history.forEach(h => {
                container.innerHTML += `
                    <div class="history-card">
                        <h4>${h.change_type} changed</h4>
                        <div class="history-meta">
                            By <strong>${h.user_name}</strong> on ${h.changed_at}
                        </div>
                        <div><strong>Old:</strong> ${h.old_value}</div>
                        <div><strong>New:</strong> ${h.new_value}</div>
                    </div>
                `;
            });
        });
}

// Save changes using API
function saveChanges(event) {
    event.preventDefault();

    let form = new FormData(document.getElementById("editForm"));
    form.append("id", <?= $item_id ?>);

    fetch("api/edit_item.php", {
        method: "POST",
        body: form
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        loadHistory();
    });
}
</script>

</head>
<body>

<div class="edit-container">

    <h2>Edit Item</h2>

    <form id="editForm" onsubmit="saveChanges(event)">
        <label>Title</label>
        <input type="text" name="title">

        <label>Description</label>
        <textarea name="description"></textarea>

        <label>Location</label>
        <input type="text" name="location">

        <label>Status</label>
        <select name="status">
            <option value="LOST">LOST</option>
            <option value="FOUND">FOUND</option>
            <option value="CLAIMED">CLAIMED</option>
        </select>

        <button type="submit">Save Changes</button>
    </form>

    <div class="history-title">Item Change History</div>
    <div id="history"></div>

</div>

</body>
</html>
