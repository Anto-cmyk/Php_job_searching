<?php    
require_once $_SERVER['DOCUMENT_ROOT'] . '/db.php';
if (!session_id()) {
    session_start();
}

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

$query = "
    SELECT id, title, company, location, description 
    FROM jobs 
    WHERE status = 'open' 
      AND (title LIKE '%$search%' OR company LIKE '%$search%' OR location LIKE '%$search%')
    ORDER BY created_at DESC
";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>JobFinder - Latest Jobs</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">



<!-- SEARCH BAR -->
<section class="bg-white py-6 shadow-sm mt-4">
    <div class="container mx-auto flex flex-col md:flex-row justify-center items-center gap-3 px-4">
        <form method="GET" class="flex w-full md:w-1/2">
            <input 
                type="text" 
                name="search" 
                value="<?= htmlspecialchars($search) ?>" 
                placeholder="Search for jobs, titles or companies..."
                class="flex-grow border border-gray-300 p-2 rounded-l-lg focus:outline-none"
            >
            <button type="submit" class="bg-blue-600 text-white px-4 rounded-r-lg hover:bg-blue-700">
                Search
            </button>
        </form>
    </div>
</section>

<!-- JOB LISTINGS -->
<section class="container mx-auto px-4 py-10">
    <h2 class="text-2xl font-semibold mb-6 text-center text-blue-600">Available Job Openings</h2>

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if ($result && mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="bg-white p-5 rounded-lg shadow hover:shadow-lg transition duration-300">
                    <h3 class="text-xl font-bold text-blue-700 mb-2">
                        <?= htmlspecialchars($row['title']) ?>
                    </h3>
                    <p class="text-gray-600 mb-1">
                        <strong>Company:</strong> <?= htmlspecialchars($row['company']) ?>
                    </p>
                    <p class="text-gray-600 mb-3">
                        <strong>Location:</strong> <?= htmlspecialchars($row['location']) ?>
                    </p>
                    <p class="text-gray-700 mb-4">
                        <?= substr(htmlspecialchars($row['description']), 0, 100) ?>...
                    </p>
                    <a href="/home/components/jobs/apply_job.php?id=<?= $row['id'] ?>" 
                       class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700">
                        View Details
                    </a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-center col-span-3 text-gray-600">No jobs found.</p>
        <?php endif; ?>
    </div>
</section>

</body>
</html>
