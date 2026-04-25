<?php
session_start();

// Protect the page
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

// Define book data with file associations
$books = [
    [
        'title' => 'Healing the Soul',
        'image' => 'the_soul.jpeg',
        'file' => 'Healing_the_soul.pdf',
        'tags' => 'healing, trauma, recovery'
    ],
    [
        'title' => 'Anxiety Guide',
        'image' => 'shopping.jpeg',
        'file' => 'anxiety-guide.pdf',
        'tags' => 'anxiety, stress, coping'
    ],
    [
        'title' => 'Self-Care Workbook',
        'image' => 'selfcare.jpeg',
        'file' => 'Self_care_Workbook.pdf',
        'tags' => 'selfcare, mindfulness, wellness'
    ],
    [
        'title' => 'Overcoming Depression',
        'image' => 'Depression.jpeg',
        'file' => 'Depression.pdf',
        'tags' => 'depression, mental health, recovery'
    ],
    [
        'title' => 'Suicidal Thoughts',
        'image' => 'Suicide.jpeg',
        'file' => 'Suicide.pdf',
        'tags' => 'suicide, mental health, prevention'
    ],
    [
        'title' => 'Overcoming Trauma',
        'image' => 'Overcoming_Trauma.jpeg',
        'file' => 'Overcoming_Trauma.pdf',
        'tags' => 'trauma, ptsd, recovery'
    ]
];

// Handle book download
if (isset($_GET['download'])) {
    $fileName = basename($_GET['download']);
    $filePath = __DIR__ . '/books/' . $fileName;
    
    if (file_exists($filePath)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="'.basename($filePath).'"');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    } else {
        die('File not found');
    }
}

// Filter books if search is performed
$filteredBooks = $books;
if (isset($_GET['search'])) {
    $searchTerm = strtolower($_GET['search']);
    $filteredBooks = array_filter($books, function($book) use ($searchTerm) {
        return strpos(strtolower($book['title']), $searchTerm) !== false || 
               strpos(strtolower($book['tags']), $searchTerm) !== false;
    });
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Library - CareLink</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f2f5;
            margin: 0;
            padding: 1rem;
        }
        .navbar {
            background-color: #003C7E;
            padding: 1rem;
            display: flex;
            justify-content: center;
            gap: 2rem;
        }
        .navbar a {
            color: #ffffff;
            text-decoration: none;
            font-weight: bold;
            font-size: 1.1rem;
        }
        .container {
            background: #ffffff;
            margin: 2rem auto;
            padding: 2rem;
            border-radius: 12px;
            max-width: 900px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
        h2 {
            text-align: center;
            color: #003C7E;
            margin-bottom: 1.5rem;
        }
        .search-bar {
            display: flex;
            margin-bottom: 2rem;
        }
        .search-bar input[type="text"] {
            flex: 1;
            padding: 0.8rem;
            border: 1px solid #ccc;
            border-radius: 8px 0 0 8px;
            font-size: 1rem;
        }
        .search-bar button {
            background-color: #003C7E;
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 0 8px 8px 0;
            font-size: 1rem;
            cursor: pointer;
        }
        .search-bar button:hover {
            background-color: #0056b3;
        }
        .books {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1.5rem;
        }
        .book {
            background-color: #e3f2fd;
            padding: 1rem;
            border-radius: 8px;
            text-align: center;
            transition: transform 0.2s;
        }
        .book:hover {
            transform: scale(1.03);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .book img {
            width: 100px;
            height: 150px;
            object-fit: cover;
            margin-bottom: 0.5rem;
            border: 1px solid #ddd;
        }
        .book-title {
            font-weight: bold;
            color: #003C7E;
            font-size: 1rem;
            margin-bottom: 8px;
        }
        .download-btn {
            display: inline-block;
            padding: 6px 12px;
            background-color: #003C7E;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 0.9rem;
        }
        .download-btn:hover {
            background-color: #0056b3;
        }
        .no-results {
            grid-column: 1 / -1;
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 2rem;
        }
    </style>
</head>
<body>

<!-- Navigation -->
<div class="navbar">
    <a href="dashboard.php">Home</a>
    <a href="library.php">Library</a>
    <a href="appointments.php">Appointments</a>
    <a href="scheduled.php">Scheduled</a>
</div>

<!-- Main Content -->
<div class="container">
    <h2>Counseling Books Library</h2>

    <form method="GET" class="search-bar">
        <input type="text" name="search" placeholder="Search books by title or topic" 
               value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
        <button type="submit">Search</button>
    </form>

    <div class="books">
        <?php if (empty($filteredBooks)): ?>
            <div class="no-results">No books found matching your search. Try different keywords.</div>
        <?php else: ?>
            <?php foreach ($filteredBooks as $book): ?>
                <div class="book">
                    <img src="images/<?php echo htmlspecialchars($book['image']); ?>" 
                         alt="<?php echo htmlspecialchars($book['title']); ?>">
                    <div class="book-title"><?php echo htmlspecialchars($book['title']); ?></div>
                    <?php if (file_exists(__DIR__ . '/books/' . $book['file'])): ?>
                        <a href="?download=<?php echo urlencode($book['file']); ?>" class="download-btn">
                            Download PDF
                        </a>
                    <?php else: ?>
                        <span class="download-btn" style="background-color:#999">File Missing</span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

</body>
</html>