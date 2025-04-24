<?php
// Start session to maintain user login state
session_start();

// Connect to database - update these with your actual credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "portfolio_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user ID - assuming you're managing a single user portfolio
$sql = "SELECT id FROM users LIMIT 1";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $user_id = $row['id'];
} else {
    die("No user found in database");
}

// Initialize variables
$experiences = $projects = $skills = $typedStrings = $techStack = [];
$edit_table = '';
$edit_id = '';
$edit_item = null;

// Handle form submissions for Experience section
if (isset($_POST['add_experience'])) {
    // Sanitize inputs
    $job_title = mysqli_real_escape_string($conn, $_POST['job_title']);
    $company = mysqli_real_escape_string($conn, $_POST['company']);
    $start_date = mysqli_real_escape_string($conn, $_POST['start_date']);
    $end_date = mysqli_real_escape_string($conn, $_POST['end_date']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $achievements = mysqli_real_escape_string($conn, $_POST['achievements']);
    $type = mysqli_real_escape_string($conn, $_POST['type']);
    
    // Insert new experience
    $sql = "INSERT INTO experience (user_id, job_title, company, start_date, end_date, description, achievements, type) 
            VALUES ('$user_id', '$job_title', '$company', '$start_date', '$end_date', '$description', '$achievements', '$type')";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: admin-dashboard.php#experiences");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

if (isset($_POST['edit_experience'])) {
    // Sanitize inputs
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $job_title = mysqli_real_escape_string($conn, $_POST['job_title']);
    $company = mysqli_real_escape_string($conn, $_POST['company']);
    $start_date = mysqli_real_escape_string($conn, $_POST['start_date']);
    $end_date = mysqli_real_escape_string($conn, $_POST['end_date']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $achievements = mysqli_real_escape_string($conn, $_POST['achievements']);
    $type = mysqli_real_escape_string($conn, $_POST['type']);
    
    // Update experience
    $sql = "UPDATE experience 
            SET job_title = '$job_title', company = '$company', start_date = '$start_date', 
                end_date = '$end_date', description = '$description', achievements = '$achievements', type = '$type' 
            WHERE id = '$id' AND user_id = '$user_id'";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: admin-dashboard.php#experiences");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

if (isset($_POST['delete_experience'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    
    $sql = "DELETE FROM experience WHERE id = '$id' AND user_id = '$user_id'";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: admin-dashboard.php#experiences");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Handle form submissions for Project section
if (isset($_POST['add_project'])) {
    // Sanitize inputs
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $type = mysqli_real_escape_string($conn, $_POST['type']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $image_url = mysqli_real_escape_string($conn, $_POST['image_url']);
    $github_url = mysqli_real_escape_string($conn, $_POST['github_url']);
    $live_url = mysqli_real_escape_string($conn, $_POST['live_url']);
    $video_id = mysqli_real_escape_string($conn, $_POST['video_id']);
    $technologies = mysqli_real_escape_string($conn, $_POST['technologies']);
    
    // Insert new project
    $sql = "INSERT INTO projects (user_id, title, type, description, image_url, github_url, live_url, video_id) 
            VALUES ('$user_id', '$title', '$type', '$description', '$image_url', '$github_url', '$live_url', '$video_id')";
    
    if ($conn->query($sql) === TRUE) {
        $project_id = $conn->insert_id;
        
        // Handle technologies
        $tech_array = array_map('trim', explode(',', $technologies));
        foreach ($tech_array as $tech) {
            if (!empty($tech)) {
                $tech = mysqli_real_escape_string($conn, $tech);
                $sql = "INSERT INTO project_technologies (project_id, technology) VALUES ('$project_id', '$tech')";
                $conn->query($sql);
            }
        }
        
        header("Location: admin-dashboard.php#projects");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

if (isset($_POST['edit_project'])) {
    // Sanitize inputs
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $type = mysqli_real_escape_string($conn, $_POST['type']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $image_url = mysqli_real_escape_string($conn, $_POST['image_url']);
    $github_url = mysqli_real_escape_string($conn, $_POST['github_url']);
    $live_url = mysqli_real_escape_string($conn, $_POST['live_url']);
    $video_id = mysqli_real_escape_string($conn, $_POST['video_id']);
    $technologies = mysqli_real_escape_string($conn, $_POST['technologies']);
    
    // Update project
    $sql = "UPDATE projects 
            SET title = '$title', type = '$type', description = '$description', 
                image_url = '$image_url', github_url = '$github_url', live_url = '$live_url', video_id = '$video_id' 
            WHERE id = '$id' AND user_id = '$user_id'";
    
    if ($conn->query($sql) === TRUE) {
        // Delete existing technologies
        $sql = "DELETE FROM project_technologies WHERE project_id = '$id'";
        $conn->query($sql);
        
        // Add updated technologies
        $tech_array = array_map('trim', explode(',', $technologies));
        foreach ($tech_array as $tech) {
            if (!empty($tech)) {
                $tech = mysqli_real_escape_string($conn, $tech);
                $sql = "INSERT INTO project_technologies (project_id, technology) VALUES ('$id', '$tech')";
                $conn->query($sql);
            }
        }
        
        header("Location: admin-dashboard.php#projects");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

if (isset($_POST['delete_project'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    
    // Delete related project technologies first (foreign key constraint)
    $sql = "DELETE FROM project_technologies WHERE project_id = '$id'";
    $conn->query($sql);
    
    // Then delete the project
    $sql = "DELETE FROM projects WHERE id = '$id' AND user_id = '$user_id'";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: admin-dashboard.php#projects");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Handle form submissions for Skills section
if (isset($_POST['add_skill'])) {
    // Sanitize inputs
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $icon_class = mysqli_real_escape_string($conn, $_POST['icon_class']);
    
    // Insert new skill
    $sql = "INSERT INTO skills (user_id, name, category, icon_class) 
            VALUES ('$user_id', '$name', '$category', '$icon_class')";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: admin-dashboard.php#skills");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

if (isset($_POST['edit_skill'])) {
    // Sanitize inputs
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $icon_class = mysqli_real_escape_string($conn, $_POST['icon_class']);
    
    // Update skill
    $sql = "UPDATE skills 
            SET name = '$name', category = '$category', icon_class = '$icon_class' 
            WHERE id = '$id' AND user_id = '$user_id'";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: admin-dashboard.php#skills");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

if (isset($_POST['delete_skill'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    
    $sql = "DELETE FROM skills WHERE id = '$id' AND user_id = '$user_id'";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: admin-dashboard.php#skills");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Handle form submissions for Typed Strings section
if (isset($_POST['add_typed_string'])) {
    // Sanitize input
    $string = mysqli_real_escape_string($conn, $_POST['string']);
    
    // Insert new typed string
    $sql = "INSERT INTO typed_strings (user_id, string) VALUES ('$user_id', '$string')";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: admin-dashboard.php#typed-strings");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

if (isset($_POST['edit_typed_string'])) {
    // Sanitize inputs
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $string = mysqli_real_escape_string($conn, $_POST['string']);
    
    // Update typed string
    $sql = "UPDATE typed_strings SET string = '$string' WHERE id = '$id' AND user_id = '$user_id'";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: admin-dashboard.php#typed-strings");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

if (isset($_POST['delete_typed_string'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    
    $sql = "DELETE FROM typed_strings WHERE id = '$id' AND user_id = '$user_id'";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: admin-dashboard.php#typed-strings");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Handle form submissions for Tech Stack section
if (isset($_POST['add_tech_stack'])) {
    // Sanitize inputs
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $icon_svg = mysqli_real_escape_string($conn, $_POST['icon_svg']);
    
    // Insert new tech stack item
    $sql = "INSERT INTO tech_stack (user_id, name, title, icon_svg) 
            VALUES ('$user_id', '$name', '$title', '$icon_svg')";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: admin-dashboard.php#tech-stack");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

if (isset($_POST['edit_tech_stack'])) {
    // Sanitize inputs
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $icon_svg = mysqli_real_escape_string($conn, $_POST['icon_svg']);
    
    // Update tech stack item
    $sql = "UPDATE tech_stack 
            SET name = '$name', title = '$title', icon_svg = '$icon_svg' 
            WHERE id = '$id' AND user_id = '$user_id'";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: admin-dashboard.php#tech-stack");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

if (isset($_POST['delete_tech_stack'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    
    $sql = "DELETE FROM tech_stack WHERE id = '$id' AND user_id = '$user_id'";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: admin-dashboard.php#tech-stack");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Handle edit requests
if (isset($_GET['edit']) && isset($_GET['id'])) {
    $edit_table = mysqli_real_escape_string($conn, $_GET['edit']);
    $edit_id = mysqli_real_escape_string($conn, $_GET['id']);
    
    switch ($edit_table) {
        case 'experiences':
            $sql = "SELECT * FROM experience WHERE id = '$edit_id' AND user_id = '$user_id'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $edit_item = $result->fetch_assoc();
            }
            break;
            
        case 'projects':
            $sql = "SELECT p.*, GROUP_CONCAT(pt.technology SEPARATOR ', ') AS technologies 
                    FROM projects p 
                    LEFT JOIN project_technologies pt ON p.id = pt.project_id 
                    WHERE p.id = '$edit_id' AND p.user_id = '$user_id' 
                    GROUP BY p.id";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $edit_item = $result->fetch_assoc();
            }
            break;
            
        case 'skills':
            $sql = "SELECT * FROM skills WHERE id = '$edit_id' AND user_id = '$user_id'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $edit_item = $result->fetch_assoc();
            }
            break;
            
        case 'typed_strings':
            $sql = "SELECT * FROM typed_strings WHERE id = '$edit_id' AND user_id = '$user_id'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $edit_item = $result->fetch_assoc();
            }
            break;
            
        case 'tech_stack':
            $sql = "SELECT * FROM tech_stack WHERE id = '$edit_id' AND user_id = '$user_id'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $edit_item = $result->fetch_assoc();
            }
            break;
    }
}

// Fetch existing items for each section
// Experiences
$sql = "SELECT * FROM experience WHERE user_id = '$user_id' ORDER BY id DESC";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $experiences[] = $row;
    }
}

// Projects with technologies
$sql = "SELECT p.*, GROUP_CONCAT(pt.technology SEPARATOR ', ') AS technologies 
        FROM projects p 
        LEFT JOIN project_technologies pt ON p.id = pt.project_id 
        WHERE p.user_id = '$user_id' 
        GROUP BY p.id 
        ORDER BY p.id DESC";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $projects[] = $row;
    }
}

// Skills
$sql = "SELECT * FROM skills WHERE user_id = '$user_id' ORDER BY id DESC";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $skills[] = $row;
    }
}

// Typed Strings
$sql = "SELECT * FROM typed_strings WHERE user_id = '$user_id' ORDER BY id DESC";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $typedStrings[] = $row;
    }
}

// Tech Stack
$sql = "SELECT * FROM tech_stack WHERE user_id = '$user_id' ORDER BY id DESC";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $techStack[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="logo">
                <h1>Portfolio Admin</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="#experiences">Experiences</a></li>
                    <li><a href="#projects">Projects</a></li>
                    <li><a href="#skills">Skills</a></li>
                    <li><a href="#typed-strings">Typed Strings</a></li>
                    <li><a href="#tech-stack">Tech Stack</a></li>
                    <li><a href="logout.php" class="logout">Logout</a></li>
                </ul>
            </nav>
        </aside>
        
        <main class="content">
            <section id="experiences" class="section">
                <h2>Experiences</h2>
                <div class="existing-items">
                    <?php foreach ($experiences as $exp): ?>
                        <div class="item-card">
                            <h3><?= htmlspecialchars($exp['job_title']) ?> at <?= htmlspecialchars($exp['company']) ?></h3>
                            <p><?= htmlspecialchars($exp['start_date']) ?> - <?= htmlspecialchars($exp['end_date']) ?></p>
                            <p><?= htmlspecialchars(substr($exp['description'], 0, 100)) ?>...</p>
                            <div class="item-actions">
                                <a href="?edit=experiences&id=<?= $exp['id'] ?>#experiences" class="btn-action edit">Edit</a>
                                <form method="POST" class="delete-form" onsubmit="return confirm('Are you sure you want to delete this experience?');">
                                    <input type="hidden" name="id" value="<?= $exp['id'] ?>">
                                    <button type="submit" name="delete_experience" class="btn-action delete">Delete</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($edit_table === 'experiences' && $edit_item): ?>
                    <form method="POST">
                        <h3>Edit Experience</h3>
                        <input type="hidden" name="id" value="<?= $edit_id ?>">
                        <div class="form-group">
                            <label>Job Title</label>
                            <input type="text" name="job_title" value="<?= htmlspecialchars($edit_item['job_title']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Company</label>
                            <input type="text" name="company" value="<?= htmlspecialchars($edit_item['company']) ?>" required>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Start Date</label>
                                <input type="text" name="start_date" value="<?= htmlspecialchars($edit_item['start_date']) ?>" placeholder="Month Year" required>
                            </div>
                            <div class="form-group">
                                <label>End Date</label>
                                <input type="text" name="end_date" value="<?= htmlspecialchars($edit_item['end_date']) ?>" placeholder="Month Year or Present" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Type</label>
                            <select name="type" required>
                                <option value="full-time" <?= $edit_item['type'] == 'full-time' ? 'selected' : '' ?>>Full-time</option>
                                <option value="part-time" <?= $edit_item['type'] == 'part-time' ? 'selected' : '' ?>>Part-time</option>
                                <option value="internship" <?= $edit_item['type'] == 'internship' ? 'selected' : '' ?>>Internship</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" required><?= htmlspecialchars($edit_item['description']) ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Achievements (one per line)</label>
                            <textarea name="achievements" rows="4"><?= htmlspecialchars($edit_item['achievements']) ?></textarea>
                        </div>
                        <div class="button-group">
                            <button type="submit" name="edit_experience" class="btn">Update Experience</button>
                            <a href="admin-dashboard.php#experiences" class="btn-cancel">Cancel</a>
                        </div>
                    </form>
                <?php else: ?>
                    <form method="POST">
                        <h3>Add New Experience</h3>
                        <div class="form-group">
                            <label>Job Title</label>
                            <input type="text" name="job_title" required>
                        </div>
                        <div class="form-group">
                            <label>Company</label>
                            <input type="text" name="company" required>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Start Date</label>
                                <input type="text" name="start_date" placeholder="Month Year" required>
                            </div>
                            <div class="form-group">
                                <label>End Date</label>
                                <input type="text" name="end_date" placeholder="Month Year or Present" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Type</label>
                            <select name="type" required>
                                <option value="full-time">Full-time</option>
                                <option value="part-time">Part-time</option>
                                <option value="internship">Internship</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Achievements (one per line)</label>
                            <textarea name="achievements" rows="4"></textarea>
                        </div>
                        <button type="submit" name="add_experience" class="btn">Add Experience</button>
                    </form>
                <?php endif; ?>
            </section>

            <!-- Projects Section -->
            <section id="projects" class="section">
                <h2>Projects</h2>
                <div class="existing-items">
                    <?php foreach ($projects as $project): ?>
                        <div class="item-card">
                            <h3><?= htmlspecialchars($project['title']) ?></h3>
                            <p><?= htmlspecialchars($project['type']) ?></p>
                            <p><?= htmlspecialchars(substr($project['description'], 0, 100)) ?>...</p>
                            <div class="item-actions">
                                <a href="?edit=projects&id=<?= $project['id'] ?>#projects" class="btn-action edit">Edit</a>
                                <form method="POST" class="delete-form" onsubmit="return confirm('Are you sure you want to delete this project?');">
                                    <input type="hidden" name="id" value="<?= $project['id'] ?>">
                                    <button type="submit" name="delete_project" class="btn-action delete">Delete</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($edit_table === 'projects' && $edit_item): ?>
                    <form method="POST">
                        <h3>Edit Project</h3>
                        <input type="hidden" name="id" value="<?= $edit_id ?>">
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" name="title" value="<?= htmlspecialchars($edit_item['title']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Type</label>
                            <input type="text" name="type" value="<?= htmlspecialchars($edit_item['type']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" required><?= htmlspecialchars($edit_item['description']) ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Image URL</label>
                            <input type="text" name="image_url" value="<?= htmlspecialchars($edit_item['image_url']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>GitHub URL</label>
                            <input type="url" name="github_url" value="<?= htmlspecialchars($edit_item['github_url']) ?>">
                        </div>
                        <div class="form-group">
                            <label>Live URL</label>
                            <input type="url" name="live_url" value="<?= htmlspecialchars($edit_item['live_url']) ?>">
                        </div>
                        <div class="form-group">
                            <label>Video ID (optional)</label>
                            <input type="text" name="video_id" value="<?= htmlspecialchars($edit_item['video_id']) ?>">
                        </div>
                        <div class="form-group">
                            <label>Technologies (comma separated)</label>
                            <input type="text" name="technologies" value="<?= htmlspecialchars($edit_item['technologies']) ?>" required>
                        </div>
                        <div class="button-group">
                            <button type="submit" name="edit_project" class="btn">Update Project</button>
                            <a href="admin-dashboard.php#projects" class="btn-cancel">Cancel</a>
                        </div>
                    </form>
                <?php else: ?>
                    <form method="POST">
                        <h3>Add New Project</h3>
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" name="title" required>
                        </div>
                        <div class="form-group">
                            <label>Type</label>
                            <input type="text" name="type" required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Image URL</label>
                            <input type="text" name="image_url" required>
                        </div>
                        <div class="form-group">
                            <label>GitHub URL</label>
                            <input type="url" name="github_url">
                        </div>
                        <div class="form-group">
                            <label>Live URL</label>
                            <input type="url" name="live_url">
                        </div>
                        <div class="form-group">
                            <label>Video ID (optional)</label>
                            <input type="text" name="video_id">
                        </div>
                        <div class="form-group">
                            <label>Technologies (comma separated)</label>
                            <input type="text" name="technologies" placeholder="Python, Pandas, Matplotlib" required>
                        </div>
                        <button type="submit" name="add_project" class="btn">Add Project</button>
                    </form>
                <?php endif; ?>
            </section>

            <!-- Skills Section -->
            <section id="skills" class="section">
                <h2>Skills</h2>
                <div class="existing-items">
                    <?php foreach ($skills as $skill): ?>
                        <div class="item-card">
                            <h3><?= htmlspecialchars($skill['name']) ?></h3>
                            <p><?= htmlspecialchars($skill['category']) ?></p>
                            <p><i class="<?= htmlspecialchars($skill['icon_class']) ?>"></i></p>
                            <div class="item-actions">
                                <a href="?edit=skills&id=<?= $skill['id'] ?>#skills" class="btn-action edit">Edit</a>
                                <form method="POST" class="delete-form" onsubmit="return confirm('Are you sure you want to delete this skill?');">
                                    <input type="hidden" name="id" value="<?= $skill['id'] ?>">
                                    <button type="submit" name="delete_skill" class="btn-action delete">Delete</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($edit_table === 'skills' && $edit_item): ?>
                    <form method="POST">
                        <h3>Edit Skill</h3>
                        <input type="hidden" name="id" value="<?= $edit_id ?>">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" value="<?= htmlspecialchars($edit_item['name']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Category</label>
                            <select name="category" required>
                                <option value="Frontend" <?= $edit_item['category'] == 'Frontend' ? 'selected' : '' ?>>Frontend</option>
                                <option value="Backend" <?= $edit_item['category'] == 'Backend' ? 'selected' : '' ?>>Backend</option>
                                <option value="Database" <?= $edit_item['category'] == 'Database' ? 'selected' : '' ?>>Database</option>
                                <option value="Mobile" <?= $edit_item['category'] == 'Mobile' ? 'selected' : '' ?>>Mobile</option>
                                <option value="DevOps" <?= $edit_item['category'] == 'DevOps' ? 'selected' : '' ?>>DevOps</option>
                                <option value="Business Intelligence" <?= $edit_item['category'] == 'Business Intelligence' ? 'selected' : '' ?>>Business Intelligence</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Icon Class (Font Awesome)</label>
                            <input type="text" name="icon_class" value="<?= htmlspecialchars($edit_item['icon_class']) ?>" required>
                        </div>
                        <div class="button-group">
                            <button type="submit" name="edit_skill" class="btn">Update Skill</button>
                            <a href="admin-dashboard.php#skills" class="btn-cancel">Cancel</a>
                        </div>
                    </form>
                <?php else: ?>
                    <form method="POST">
                        <h3>Add New Skill</h3>
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" required>
                        </div>
                        <div class="form-group">
                            <label>Category</label>
                            <select name="category" required>
                                <option value="Frontend">Frontend</option>
                                <option value="Backend">Backend</option>
                                <option value="Database">Database</option>
                                <option value="Mobile">Mobile</option>
                                <option value="DevOps">DevOps</option>
                                <option value="Business Intelligence">Business Intelligence</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Icon Class (Font Awesome)</label>
                            <input type="text" name="icon_class" placeholder="fab fa-js" required>
                        </div>
                        <button type="submit" name="add_skill" class="btn">Add Skill</button>
                    </form>
                <?php endif; ?>
            </section>

            <!-- Typed Strings Section -->
            <section id="typed-strings" class="section">
                <h2>Typed Strings</h2>
                <div class="existing-items">
                    <?php foreach ($typedStrings as $typed): ?>
                        <div class="item-card">
                            <p><?= htmlspecialchars($typed['string']) ?></p>
                            <div class="item-actions">
                                <a href="?edit=typed_strings&id=<?= $typed['id'] ?>#typed-strings" class="btn-action edit">Edit</a>
                                <form method="POST" class="delete-form" onsubmit="return confirm('Are you sure you want to delete this string?');">
                                    <input type="hidden" name="id" value="<?= $typed['id'] ?>">
                                    <button type="submit" name="delete_typed_string" class="btn-action delete">Delete</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($edit_table === 'typed_strings' && $edit_item): ?>
                    <form method="POST">
                        <h3>Edit Typed String</h3>
                        <input type="hidden" name="id" value="<?= $edit_id ?>">
                        <div class="form-group">
                            <label>String</label>
                            <input type="text" name="string" value="<?= htmlspecialchars($edit_item['string']) ?>" required>
                        </div>
                        <div class="button-group">
                            <button type="submit" name="edit_typed_string" class="btn">Update String</button>
                            <a href="admin-dashboard.php#typed-strings" class="btn-cancel">Cancel</a>
                        </div>
                    </form>
                <?php else: ?>
                    <form method="POST">
                        <h3>Add New Typed String</h3>
                        <div class="form-group">
                            <label>String</label>
                            <input type="text" name="string" placeholder="e.g., Full Stack Developer" required>
                        </div>
                        <button type="submit" name="add_typed_string" class="btn">Add String</button>
                    </form>
                <?php endif; ?>
            </section>

            <!-- Tech Stack Section -->
            <section id="tech-stack" class="section">
                <h2>Tech Stack</h2>
                <div class="existing-items">
                    <?php foreach ($techStack as $tech): ?>
                        <div class="item-card">
                            <h3><?= htmlspecialchars($tech['title']) ?></h3>
                            <p><?= htmlspecialchars($tech['name']) ?></p>
                            <div class="tech-icon-preview">
                                <?= $tech['icon_svg'] ?>
                            </div>
                            <div class="item-actions">
                                <a href="?edit=tech_stack&id=<?= $tech['id'] ?>#tech-stack" class="btn-action edit">Edit</a>
                                <form method="POST" class="delete-form" onsubmit="return confirm('Are you sure you want to delete this tech stack item?');">
                                    <input type="hidden" name="id" value="<?= $tech['id'] ?>">
                                    <button type="submit" name="delete_tech_stack" class="btn-action delete">Delete</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($edit_table === 'tech_stack' && $edit_item): ?>
                    <form method="POST">
                        <h3>Edit Tech Stack Item</h3>
                        <input type="hidden" name="id" value="<?= $edit_id ?>">
                        <div class="form-group">
                            <label>Name (lowercase, no spaces)</label>
                            <input type="text" name="name" value="<?= htmlspecialchars($edit_item['name']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" name="title" value="<?= htmlspecialchars($edit_item['title']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>SVG Icon Code</label>
                            <textarea name="icon_svg" rows="6" required><?= htmlspecialchars($edit_item['icon_svg']) ?></textarea>
                        </div>
                        <div class="button-group">
                            <button type="submit" name="edit_tech_stack" class="btn">Update Tech Stack Item</button>
                            <a href="admin-dashboard.php#tech-stack" class="btn-cancel">Cancel</a>
                        </div>
                    </form>
                <?php else: ?>
                    <form method="POST">
                        <h3>Add New Tech Stack Item</h3>
                        <div class="form-group">
                            <label>Name (lowercase, no spaces)</label>
                            <input type="text" name="name" placeholder="e.g., javascript" required>
                        </div>
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" name="title" placeholder="e.g., JavaScript" required>
                        </div>
                        <div class="form-group">
                            <label>SVG Icon Code</label>
                            <textarea name="icon_svg" rows="6" required></textarea>
                        </div>
                        <button type="submit" name="add_tech_stack" class="btn">Add Tech Stack Item</button>
                    </form>
                <?php endif; ?>
            </section>
        </main>
    </div>

    <script>
        // Add any JavaScript functionality you need
        document.addEventListener('DOMContentLoaded', function() {
            // Smooth scrolling for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    document.querySelector(this.getAttribute('href')).scrollIntoView({
                        behavior: 'smooth'
                    });
                });
            });
            
            // Optional: Add preview functionality for icons
            const iconInputs = document.querySelectorAll('input[name="icon_class"]');
            iconInputs.forEach(input => {
                input.addEventListener('input', function() {
                    const previewElement = document.createElement('div');
                    previewElement.innerHTML = `<i class="${this.value}"></i>`;
                    // Add preview logic here if needed
                });
            });
            
            // Optional: Add preview for SVG
            const svgInputs = document.querySelectorAll('textarea[name="icon_svg"]');
            svgInputs.forEach(input => {
                input.addEventListener('input', function() {
                    const previewElement = document.createElement('div');
                    previewElement.innerHTML = this.value;
                    // Add preview logic here if needed
                });
            });
        });
    </script>
</body>
</html>