<!-- index.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Explorer</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        #filterInput {
            margin-bottom: 20px;
            padding: 5px;
            width: 300px;
        }
        #fileList {
            list-style-type: none;
            padding: 0;
        }
        .file-item {
            position: relative;
            padding: 10px;
            border-bottom: 1px solid #ddd;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .file-item img.preview {
            display: none;
            position: absolute;
            top: 40px;
            left: 0;
            width: 200px;
            height: auto;
            border: 1px solid #ccc;
            z-index: 10;
        }
        .file-item:hover img.preview {
            display: block;
        }
        .file-item a {
            color: #007bff;
            text-decoration: none;
        }
        .file-item a:hover {
            text-decoration: underline;
        }
        .tags {
            margin-left: 20px;
        }
        .tags select {
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <h1>File Explorer</h1>
    <input type="text" id="filterInput" placeholder="Filter files..." onkeyup="filterFiles()">

    <ul id="fileList">
        <?php
        $dir = "/Users/gabegiancarlo/Projects/SpotifyApp"; // Adjust path as needed
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;
            $filePath = $dir . '/' . $file;
            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            $fileSize = filesize($filePath);
            $lastModified = date("M d H:i:s", filemtime($filePath));
            $fileType = ($extension === 'jpg' || $extension === 'jpeg') ? 'image' : 
                       ($extension === 'html' || $extension === 'php') ? 'html' : 'other';

            echo "<li class='file-item' data-filename='$file' data-type='$fileType'>";
            echo "<span>$file ($fileSize bytes, modified $lastModified)</span>";

            if ($fileType === 'image') {
                echo "<a href='$filePath' target='_blank'>$file</a>";
                echo "<img class='preview' src='$filePath' alt='Preview'>";
            } elseif ($fileType === 'html') {
                echo "<a href='$filePath' target='_blank'>$file (Open)</a>";
                echo "<a href='#' onclick='viewSource(\"$filePath\"); return false;'> (View Source)</a>";
            } else {
                echo "<span>$file</span>";
            }

            echo "<div class='tags'>";
            echo "<select onchange='tagFile(\"$file\", this.value)'>";
            echo "<option value=''>Tag...</option>";
            echo "<option value='archive'>Archive</option>";
            echo "<option value='migrate'>Migrate</option>";
            echo "<option value='organize'>Organize</option>";
            echo "</select>";
            echo "<span class='tag-label'></span>";
            echo "</div>";
            echo "</li>";
        }
        ?>
    </ul>

    <script>
        // Filter files based on input
        function filterFiles() {
            const input = document.getElementById('filterInput').value.toLowerCase();
            const fileItems = document.querySelectorAll('.file-item');
            fileItems.forEach(item => {
                const fileName = item.getAttribute('data-filename').toLowerCase();
                item.style.display = fileName.includes(input) ? '' : 'none';
            });
        }

        // View HTML source in a new window
        function viewSource(filePath) {
            fetch(filePath)
                .then(response => response.text())
                .then(data => {
                    const newWindow = window.open('');
                    newWindow.document.write('<pre>' + data.replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</pre>');
                    newWindow.document.close();
                })
                .catch(err => alert('Error loading source: ' + err));
        }

        // Tag files and store in localStorage
        function tagFile(fileName, tag) {
            let tags = JSON.parse(localStorage.getItem('fileTags')) || {};
            tags[fileName] = tag;
            localStorage.setItem('fileTags', JSON.stringify(tags));
            updateTagLabels();
        }

        // Update tag labels on the UI
        function updateTagLabels() {
            let tags = JSON.parse(localStorage.getItem('fileTags')) || {};
            document.querySelectorAll('.file-item').forEach(item => {
                const fileName = item.getAttribute('data-filename');
                const tagLabel = item.querySelector('.tag-label');
                tagLabel.textContent = tags[fileName] ? `[${tags[fileName]}]` : '';
            });
        }

        // Load tags on page load
        window.onload = updateTagLabels;
    </script>
</body>
</html>
