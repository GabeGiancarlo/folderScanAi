<!-- index.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dark File Explorer</title>
    <style>
        :root {
            --bg-primary: #1e1e2e;
            --bg-secondary: #262636;
            --text-primary: #e2e2e2;
            --text-secondary: #a6a6b0;
            --accent: #7289da;
            --border: #3a3a4a;
            --hover: #3a3a50;
            --item-height: 50px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-primary);
            color: var(--text-primary);
            line-height: 1.6;
            padding: 0;
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            flex-grow: 1;
        }

        header {
            background-color: var(--bg-secondary);
            padding: 15px 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        h1 {
            font-size: 1.8rem;
            font-weight: 500;
            color: var(--accent);
            margin: 0;
        }

        .current-path {
            background-color: var(--bg-secondary);
            border-radius: 6px;
            padding: 10px 15px;
            margin-bottom: 10px;
            border: 1px solid var(--border);
            font-family: monospace;
            overflow-x: auto;
            white-space: nowrap;
        }

        .breadcrumb {
            list-style: none;
            display: flex;
            background-color: var(--bg-secondary);
            border-radius: 6px;
            padding: 10px 15px;
            margin-bottom: 20px;
            overflow-x: auto;
            white-space: nowrap;
        }

        .breadcrumb li {
            display: flex;
            align-items: center;
        }

        .breadcrumb li:not(:last-child)::after {
            content: '/';
            margin: 0 10px;
            color: var(--text-secondary);
        }

        .breadcrumb a {
            color: var(--accent);
            text-decoration: none;
            transition: color 0.2s;
        }

        .breadcrumb a:hover {
            color: var(--text-primary);
        }

        #filterInput {
            background-color: var(--bg-secondary);
            border: 1px solid var(--border);
            color: var(--text-primary);
            padding: 10px 15px;
            border-radius: 4px;
            font-size: 1rem;
            width: 100%;
            margin-bottom: 20px;
            transition: border-color 0.2s;
        }

        #filterInput:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 2px rgba(114, 137, 218, 0.3);
        }

        #fileList {
            list-style-type: none;
            background-color: var(--bg-secondary);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .file-item {
            position: relative;
            padding: 12px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            transition: background-color 0.2s;
            min-height: var(--item-height);
        }

        .file-item:hover {
            background-color: var(--hover);
        }

        .file-item:last-child {
            border-bottom: none;
        }

        .file-icon {
            margin-right: 15px;
            font-size: 1.2rem;
            width: 24px;
            text-align: center;
            color: var(--text-secondary);
        }

        .file-icon.folder {
            color: #ffc107;
        }

        .file-icon.image {
            color: #4caf50;
        }

        .file-icon.html {
            color: #f44336;
        }

        .file-icon.code {
            color: #2196f3;
        }

        .file-details {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .file-name {
            font-weight: 500;
            margin-bottom: 3px;
        }

        .file-meta {
            font-size: 0.8rem;
            color: var(--text-secondary);
        }

        .file-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .file-actions a, .action-btn {
            color: var(--accent);
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.9rem;
            transition: background-color 0.2s;
            cursor: pointer;
        }

        .file-actions a:hover, .action-btn:hover {
            background-color: rgba(114, 137, 218, 0.1);
        }

        .tags {
            margin-left: 15px;
            position: relative;
        }

        .tags select {
            background-color: var(--bg-secondary);
            color: var(--text-primary);
            border: 1px solid var(--border);
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.9rem;
            cursor: pointer;
            transition: border-color 0.2s;
            appearance: none;
            padding-right: 25px;
        }

        .tags select:focus {
            outline: none;
            border-color: var(--accent);
        }

        .tags::after {
            content: "▼";
            font-size: 0.8rem;
            color: var(--text-secondary);
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
        }

        .tag-label {
            margin-left: 10px;
            font-size: 0.8rem;
            background-color: var(--border);
            color: var(--text-primary);
            padding: 3px 8px;
            border-radius: 4px;
            display: inline-block;
        }

        .file-item img.preview {
            display: none;
            position: absolute;
            top: calc(var(--item-height) + 5px);
            left: 50px;
            max-width: 300px;
            max-height: 200px;
            border-radius: 4px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            z-index: 10;
        }

        .file-item:hover img.preview {
            display: block;
        }

        .empty-dir {
            text-align: center;
            padding: 30px;
            color: var(--text-secondary);
            font-style: italic;
        }
        
        .path-form {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .path-form input {
            flex-grow: 1;
            background-color: var(--bg-secondary);
            border: 1px solid var(--border);
            color: var(--text-primary);
            padding: 10px 15px;
            border-radius: 4px;
            font-size: 1rem;
        }
        
        .path-form button {
            background-color: var(--accent);
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .path-form button:hover {
            background-color: #5a6cbd;
        }

        footer {
            background-color: var(--bg-secondary);
            text-align: center;
            padding: 15px;
            font-size: 0.9rem;
            color: var(--text-secondary);
            margin-top: 30px;
        }

        @media (max-width: 768px) {
            .file-actions {
                flex-direction: column;
                gap: 5px;
                align-items: flex-end;
            }
            
            .file-meta {
                max-width: 200px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <h1>Dark File Explorer</h1>
        </div>
    </header>

    <div class="container">
        <?php
        // Get the current directory where this script is located
        $scriptDir = dirname(__FILE__);
        
        // Get navigation path from GET parameter or use the script directory
        $currentPath = isset($_GET['path']) ? $_GET['path'] : '';
        
        // Security check - prevent directory traversal attacks
        $currentPath = str_replace('..', '', $currentPath);
        $currentPath = trim($currentPath, '/\\');
        
        // Determine full path - either current directory or a subdirectory
        $baseDir = $scriptDir;
        $fullPath = $baseDir;
        
        if ($currentPath) {
            $fullPath .= '/' . $currentPath;
        }
        
        // Validate path exists and is a directory
        if (!file_exists($fullPath) || !is_dir($fullPath)) {
            echo "<div class='empty-dir'>Directory not found or inaccessible.</div>";
            exit;
        }
        
        // Display the current directory path
        echo "<div class='current-path'>";
        echo "Current Directory: " . $fullPath;
        echo "</div>";
        
        // Manual path navigation form
        echo "<form class='path-form' method='GET'>";
        echo "<input type='text' name='path' placeholder='Enter directory path relative to script location' value='$currentPath'>";
        echo "<button type='submit'>Navigate</button>";
        echo "</form>";
        
        // Build breadcrumb navigation
        echo "<ul class='breadcrumb'>";
        echo "<li><a href='?path='>Home</a></li>";
        
        if ($currentPath) {
            $paths = explode('/', $currentPath);
            $buildPath = '';
            
            foreach ($paths as $index => $folder) {
                $buildPath .= ($buildPath ? '/' : '') . $folder;
                $isLast = ($index === count($paths) - 1);
                
                if ($isLast) {
                    echo "<li>$folder</li>";
                } else {
                    echo "<li><a href='?path=$buildPath'>$folder</a></li>";
                }
            }
        }
        echo "</ul>";
        ?>

        <input type="text" id="filterInput" placeholder="Filter files and folders..." onkeyup="filterFiles()">

        <ul id="fileList">
            <?php
            $files = scandir($fullPath);
            $hasFiles = false;
            
            // First list parent directory link if we're in a subdirectory
            if ($currentPath) {
                echo "<li class='file-item' data-filename='..' data-type='folder'>";
                echo "<div class='file-icon folder'>📁</div>";
                echo "<div class='file-details'>";
                echo "<div class='file-name'>..</div>";
                echo "<div class='file-meta'>Parent Directory</div>";
                echo "</div>";
                echo "<div class='file-actions'>";
                
                // Get parent directory path
                $parentPath = dirname($currentPath);
                $parentPath = ($parentPath === '.') ? '' : $parentPath;
                
                echo "<a href='?path=$parentPath'>Go Up</a>";
                echo "</div>";
                echo "</li>";
            }
            
            // Then list directories
            foreach ($files as $file) {
                if ($file === '.' || $file === '..') continue;
                
                $filePath = $fullPath . '/' . $file;
                if (is_dir($filePath)) {
                    $hasFiles = true;
                    $subPath = $currentPath ? $currentPath . '/' . $file : $file;
                    
                    echo "<li class='file-item' data-filename='$file' data-type='folder'>";
                    echo "<div class='file-icon folder'>📁</div>";
                    echo "<div class='file-details'>";
                    echo "<div class='file-name'>$file</div>";
                    echo "<div class='file-meta'>Directory</div>";
                    echo "</div>";
                    echo "<div class='file-actions'>";
                    echo "<a href='?path=$subPath'>Open</a>";
                    echo "</div>";
                    echo "</li>";
                }
            }
            
            // Then list files
            foreach ($files as $file) {
                if ($file === '.' || $file === '..') continue;
                
                $filePath = $fullPath . '/' . $file;
                if (!is_dir($filePath)) {
                    $hasFiles = true;
                    $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    $fileSize = filesize($filePath);
                    
                    // Format file size
                    if ($fileSize < 1024) {
                        $formattedSize = $fileSize . " B";
                    } elseif ($fileSize < 1024 * 1024) {
                        $formattedSize = round($fileSize / 1024, 1) . " KB";
                    } else {
                        $formattedSize = round($fileSize / (1024 * 1024), 1) . " MB";
                    }
                    
                    $lastModified = date("M d Y, H:i", filemtime($filePath));
                    
                    // Determine file type and icon
                    if ($extension === 'jpg' || $extension === 'jpeg' || $extension === 'png' || $extension === 'gif') {
                        $fileType = 'image';
                        $icon = '🖼️';
                    } elseif ($extension === 'html' || $extension === 'php') {
                        $fileType = 'html';
                        $icon = '📄';
                    } elseif ($extension === 'js' || $extension === 'css' || $extension === 'py' || $extension === 'java' || $extension === 'cpp') {
                        $fileType = 'code';
                        $icon = '📝';
                    } else {
                        $fileType = 'other';
                        $icon = '📄';
                    }
                    
                    // Construct a relative URL for the file
                    $fileURL = '';
                    if ($currentPath) {
                        $fileURL = $currentPath . '/' . $file;
                    } else {
                        $fileURL = $file;
                    }
                    
                    echo "<li class='file-item' data-filename='$file' data-type='$fileType'>";
                    echo "<div class='file-icon $fileType'>$icon</div>";
                    echo "<div class='file-details'>";
                    echo "<div class='file-name'>$file</div>";
                    echo "<div class='file-meta'>$formattedSize, modified $lastModified</div>";
                    echo "</div>";
                    
                    echo "<div class='file-actions'>";
                    if ($fileType === 'image') {
                        echo "<a href='$fileURL' target='_blank'>View</a>";
                        echo "<img class='preview' src='$fileURL' alt='Preview'>";
                    } elseif ($fileType === 'html') {
                        echo "<a href='$fileURL' target='_blank'>Open</a>";
                        echo "<span class='action-btn' onclick='viewSource(\"$fileURL\")'>Source</span>";
                    } else {
                        echo "<span class='action-btn' onclick='viewSource(\"$fileURL\")'>View</span>";
                    }
                    
                    echo "<div class='tags'>";
                    echo "<select onchange='tagFile(\"$file\", this.value)'>";
                    echo "<option value=''>Tag</option>";
                    echo "<option value='archive'>Archive</option>";
                    echo "<option value='important'>Important</option>";
                    echo "<option value='review'>Review</option>";
                    echo "<option value='migrate'>Migrate</option>";
                    echo "</select>";
                    echo "<span class='tag-label' id='tag-$file'></span>";
                    echo "</div>";
                    
                    echo "</div>"; // End file-actions
                    echo "</li>";
                }
            }
            
            if (count($files) <= 2) { // Only '.' and '..' entries
                echo "<li class='empty-dir'>This directory is empty</li>";
            }
            ?>
        </ul>
    </div>

    <footer>
        &copy; <?php echo date('Y'); ?> Dark File Explorer
    </footer>

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

        // View file source in a new window
        function viewSource(filePath) {
            fetch(filePath)
                .then(response => response.text())
                .then(data => {
                    const newWindow = window.open('');
                    newWindow.document.write('<html><head><title>Source: ' + filePath + '</title>');
                    newWindow.document.write('<style>body{background:#1e1e2e;color:#e2e2e2;font-family:monospace;padding:20px;line-height:1.5;} pre{background:#262636;padding:15px;border-radius:8px;overflow:auto;}</style>');
                    newWindow.document.write('</head><body>');
                    newWindow.document.write('<h3 style="color:#7289da;margin-bottom:20px;">Source: ' + filePath + '</h3>');
                    newWindow.document.write('<pre>' + data.replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</pre>');
                    newWindow.document.write('</body></html>');
                    newWindow.document.close();
                })
                .catch(err => alert('Error loading source: ' + err));
        }

        // Tag files and store in localStorage
        function tagFile(fileName, tag) {
            let tags = JSON.parse(localStorage.getItem('fileTags')) || {};
            
            if (tag === '') {
                delete tags[fileName];
            } else {
                tags[fileName] = tag;
            }
            
            localStorage.setItem('fileTags', JSON.stringify(tags));
            updateTagLabels();
        }

        // Update tag labels on the UI
        function updateTagLabels() {
            let tags = JSON.parse(localStorage.getItem('fileTags')) || {};
            
            Object.keys(tags).forEach(fileName => {
                const tagElement = document.getElementById('tag-' + fileName);
                if (tagElement) {
                    tagElement.textContent = tags[fileName];
                    tagElement.style.display = 'inline-block';
                    
                    // Color-code tags
                    switch(tags[fileName]) {
                        case 'important':
                            tagElement.style.backgroundColor = '#e74c3c';
                            break;
                        case 'archive':
                            tagElement.style.backgroundColor = '#3498db';
                            break;
                        case 'review':
                            tagElement.style.backgroundColor = '#f39c12';
                            break;
                        case 'migrate':
                            tagElement.style.backgroundColor = '#9b59b6';
                            break;
                        default:
                            tagElement.style.backgroundColor = '#95a5a6';
                    }
                }
            });
        }

        // Load tags on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateTagLabels();
        });
    </script>
</body>
</html>
