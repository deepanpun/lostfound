<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Report Lost / Found Item</title>

<script>
function submitReport(event) {
    event.preventDefault();

    let form = new FormData(document.getElementById("reportForm"));

    fetch("api/report_item.php", {
        method: "POST",
        body: form
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);

        if (data.status === "success") {
            document.getElementById("reportForm").reset();
        }
    })
    .catch(err => console.error("Error:", err));
}
</script>

</head>
<body>

<h2>Report Lost / Found Item</h2>

<form id="reportForm" onsubmit="submitReport(event)" enctype="multipart/form-data">

    <label>Status</label>
    <select name="status" required>
        <option value="">Select</option>
        <option value="LOST">LOST</option>
        <option value="FOUND">FOUND</option>
    </select>

    <label>Item Title</label>
    <input type="text" name="title" required>

    <label>Category</label>
    <select name="category_id" required>
        <option value="">Select category</option>
        <!-- Fill with PHP later if needed -->
    </select>

    <label>Location</label>
    <input type="text" name="location">

    <label>Description</label>
    <textarea name="description"></textarea>

    <label>Image</label>
    <input type="file" name="image">

    <button type="submit">Submit Item</button>
</form>

</body>
</html>
