<?php
require_once 'config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Fetch user data
$userStmt = $pdo->prepare("SELECT * FROM users LIMIT 1");
$userStmt->execute();
$user = $userStmt->fetch(PDO::FETCH_ASSOC);

// Check if user data was found before trying to access it
if ($user === false) {
    $user = ['id' => 0]; // Set a default user ID to avoid further errors
}

// Fetch social links
$socialStmt = $pdo->prepare("SELECT * FROM social_links WHERE user_id = ?");
$socialStmt->execute([$user['id']]);
$socialLinks = $socialStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch typed strings
$typedStmt = $pdo->prepare("SELECT string FROM typed_strings WHERE user_id = ?");
$typedStmt->execute([$user['id']]);
$typedStrings = $typedStmt->fetchAll(PDO::FETCH_COLUMN);

// Fetch tech stack
$techStmt = $pdo->prepare("SELECT * FROM tech_stack WHERE user_id = ?");
$techStmt->execute([$user['id']]);
$techStack = $techStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch education
$eduStmt = $pdo->prepare("SELECT * FROM education WHERE user_id = ? ORDER BY end_year DESC");
$eduStmt->execute([$user['id']]);
$education = $eduStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch experience
$expStmt = $pdo->prepare("SELECT * FROM experience WHERE user_id = ? ORDER BY 
    CASE type 
        WHEN 'full-time' THEN 1 
        WHEN 'part-time' THEN 2 
        WHEN 'internship' THEN 3 
    END, 
    end_date DESC");
$expStmt->execute([$user['id']]);
$experiences = $expStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch projects
$projStmt = $pdo->prepare("SELECT p.*, 
    GROUP_CONCAT(pt.technology SEPARATOR '|||') as technologies 
    FROM projects p 
    LEFT JOIN project_technologies pt ON p.id = pt.project_id 
    WHERE p.user_id = ? 
    GROUP BY p.id 
    ORDER BY p.id DESC");
$projStmt->execute([$user['id']]);
$projects = $projStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch skills
$skillStmt = $pdo->prepare("SELECT * FROM skills WHERE user_id = ? ORDER BY category, name");
$skillStmt->execute([$user['id']]);
$skills = $skillStmt->fetchAll(PDO::FETCH_ASSOC);

// echo '<pre>Experience Data: ';
// print_r($experiences);
// echo 'Filtered full-time: ';
// print_r(array_filter($experiences, function($exp) { return $exp['type'] === 'full-time'; }));
// echo '</pre>';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($user['full_name'] ?? 'Portfolio') ?> | <?= htmlspecialchars($user['profession'] ?? 'Full Stack Developer') ?></title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
<nav class="navbar">
    <div class="nav-links">
        <a href="#home" class="nav-link">Home</a>
        <a href="#about" class="nav-link">About</a>
        <a href="#education" class="nav-link">Education</a>
        <a href="#experience" class="nav-link">Experience</a>
        <a href="#projects" class="nav-link">Projects</a>
        <a href="#skills" class="nav-link">Skills</a>
        <a href="#contact" class="nav-link">Contact</a>
    </div>
    <div class="nav-right">
        <div class="nav-social">
            <?php foreach ($socialLinks as $link): ?>
                <a href="<?= htmlspecialchars($link['url']) ?>" class="social-link" target="_blank">
                    <i class="<?= htmlspecialchars($link['icon_class']) ?>"></i>
                </a>
            <?php endforeach; ?>
        </div>
        <button class="theme-toggle" aria-label="Toggle dark mode">
            <i class="fas fa-moon"></i>
        </button>
    </div>
</nav>

    <!-- Hero Section -->
    <section id="home" class="hero">
        <div class="animated-bg">
            <div class="floating-shape shape1"></div>
            <div class="floating-shape shape2"></div>
            <div class="floating-shape shape3"></div>
            <div class="floating-shape shape4"></div>
        </div>
        
        <div class="hero-content">
            <div class="hero-text">
                <div class="subtitle">Welcome to my portfolio</div>
                <h1>Hi, I'm <span class="name"><?= htmlspecialchars($user['full_name'] ?? 'John Doe') ?></span></h1>
                <h2><?= htmlspecialchars($user['profession'] ?? 'Full Stack Developer') ?></h2>
                <!-- <div class="typing-container">
                    <span class="typing-text">Passionate about creating </span>
                    <span id="typed-strings">
                        <?php foreach ($typedStrings as $string): ?>
                            <span><?= htmlspecialchars($string) ?></span>
                        <?php endforeach; ?>
                    </span>
                    <span class="typed"></span>
                </div> -->
                <div class="hero-buttons">
                    <a href="#contact" class="btn btn-primary">
                        <span>Hire Me</span>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </a>
                    <a href="#projects" class="btn btn-secondary">View Projects</a>
                </div>
                
                <!-- Tech stack icons -->
                <div class="tech-stack">
                    <div class="tech-title">Tech Stack</div>
                    <div class="tech-icons">
                        <?php foreach ($techStack as $tech): ?>
                            <div class="tech-icon" title="<?= htmlspecialchars($tech['title']) ?>">
                                <?= $tech['icon_svg'] ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <div class="hero-image">
                <div class="image-frame">
                    <img src="<?= htmlspecialchars($user['profile_image'] ?? 'https://tse2.mm.bing.net/th/id/OIP.KcpgwKTrcie2WIGQo4V8pAHaDt?rs=1&pid=ImgDetMain') ?>"
                        alt="Profile Photo" class="profile-photo">
                    <div class="frame-accent"></div>
                </div>
                
                <!-- Social indicators around image -->
                <div class="social-indicator">
                    <?php foreach ($socialLinks as $link): ?>
                        <div class="social-bubble <?= htmlspecialchars(strtolower($link['platform'])) ?>">
                            <i class="<?= htmlspecialchars($link['icon_class']) ?>"></i>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <!-- Scroll Indicator -->
        <div class="scroll-indicator">
            <div class="scroll-text">Scroll Down</div>
            <div class="scroll-arrow">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <polyline points="19 12 12 19 5 12"></polyline>
                </svg>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about">
        <h2 class="section-title">About Me</h2>
        <div class="about-content">
            <div class="about-text">
                <p><?= htmlspecialchars($user['about_text'] ?? 'I\'m a passionate Full Stack Developer...') ?></p>
                <div class="about-stats">
                    <div class="stat">
                        <span class="stat-number"><?= htmlspecialchars($user['years_experience'] ?? '3+') ?></span>
                        <span class="stat-label">Years Experience</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number"><?= htmlspecialchars($user['projects_completed'] ?? '20+') ?></span>
                        <span class="stat-label">Projects Completed</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number"><?= htmlspecialchars($user['technologies_used'] ?? '15+') ?></span>
                        <span class="stat-label">Technologies</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Education Section -->
    <section id="education" class="education">
        <div class="scroll-nav">
            <div class="scroll-arrow scroll-left">←</div>
            <div class="scroll-arrow scroll-right">→</div>
        </div>
        <h2 class="section-title">Education</h2>
        <div class="education-grid">
            <?php foreach ($education as $edu): ?>
                <div class="education-card">
                    <div class="education-icon">
                        <i class="<?= htmlspecialchars($edu['icon_class'] ?? 'fas fa-university') ?>"></i>
                    </div>
                    <h3><?= htmlspecialchars($edu['degree']) ?></h3>
                    <h4><?= htmlspecialchars($edu['field']) ?></h4>
                    <p class="institution"><?= htmlspecialchars($edu['institution']) ?></p>
                    <p class="duration"><?= htmlspecialchars($edu['start_year']) ?> - <?= htmlspecialchars($edu['end_year']) ?></p>
                    <div class="marks">
                        <?php if (!empty($edu['gpa'])): ?>
                            <span>CGPA: <?= htmlspecialchars($edu['gpa']) ?></span>
                        <?php endif; ?>
                        <?php if (!empty($edu['percentage'])): ?>
                            <span>Percentage: <?= htmlspecialchars($edu['percentage']) ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($edu['achievements'])): ?>
    <div class="achievement-box">
        <!-- <h4>Achievements</h4> -->
        <ul>
            <?php 
            $achievements = array_filter(array_map('trim', preg_split("/\r\n|\n|\r/", $edu['achievements'])));
            foreach ($achievements as $achievement): ?>
                <?= htmlspecialchars($achievement) ?>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Experience Section -->
<!-- Experience Section -->
<section id="experience" class="experience">
        <h2 class="section-title">Work Experience</h2>
        <div class="experience-container">
            <?php 
            $experienceTypes = ['full-time', 'part-time', 'internship'];
            foreach ($experienceTypes as $type): 
                $typeExperiences = array_filter($experiences, function($exp) use ($type) {
                    return $exp['type'] === $type;
                });
                
                if (!empty($typeExperiences)): ?>
                    <div class="experience-section">
                        <h3 class="subsection-title"><?= ucfirst($type) ?></h3>
                        <div class="timeline">
                            <?php foreach ($typeExperiences as $exp): ?>
                                <div class="timeline-item">
                                    <div class="timeline-date"><?= htmlspecialchars($exp['start_date']) ?> - <?= htmlspecialchars($exp['end_date']) ?></div>
                                    <div class="timeline-content">
                                        <h4><?= htmlspecialchars($exp['job_title']) ?></h4>
                                        <h5><?= htmlspecialchars($exp['company']) ?></h5>
                                        <p><?= htmlspecialchars($exp['description']) ?></p>
                                        <?php if (!empty($exp['achievements'])): ?>
                                            <ul class="achievements">
                                                <?php 
                                                $achievements = explode("\n", $exp['achievements']);
                                                foreach ($achievements as $achievement): 
                                                    if (!empty(trim($achievement))): ?>
                                                        <li><?= htmlspecialchars(trim($achievement)) ?></li>
                                                    <?php endif; 
                                                endforeach; ?>
                                            </ul>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; 
            endforeach; ?>
        </div>
    </section>
    <!-- Loading Animation -->
    <div class="loading-animation">
        <div class="loading-circle"></div>
    </div>

<!-- Projects Section -->
<section id="projects" class="projects-section">
    <div class="section-container">
        <div class="projects-heading">
            <div>
                <h2 class="projects-title">Featured Projects</h2>
                <p class="projects-subtitle">Explore my latest work spanning multiple domains from web applications to AI solutions and blockchain innovation.</p>
            </div>
            <div class="filter-tabs">
                <button class="filter-tab active" data-filter="all">All</button>
                <?php
                // Get unique project types for filter tabs
                $projectTypes = [];
                foreach ($projects as $project) {
                    if (!empty($project['type']) && !in_array($project['type'], $projectTypes)) {
                        $projectTypes[] = $project['type'];
                    }
                }
                
                // Generate filter tabs based on unique project types
                foreach ($projectTypes as $type): ?>
                    <button class="filter-tab" data-filter="<?= strtolower(htmlspecialchars($type)) ?>"><?= htmlspecialchars($type) ?></button>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="projects-grid">
            <?php foreach ($projects as $project):
                $techStack = isset($project['technologies']) ? explode('|||', $project['technologies']) : [];
                $projectType = strtolower($project['type'] ?? ''); ?>
                <div class="project-card" data-type="<?= htmlspecialchars($projectType) ?>">
                    <div class="project-thumbnail">
                        <img src="<?= htmlspecialchars($project['image_url'] ?? 'https://via.placeholder.com/600x400') ?>"
                              alt="<?= htmlspecialchars($project['title']) ?>" class="project-image">
                        <div class="project-overlay"></div>
                    </div>
                    <div class="project-content">
                        <span class="project-type"><?= htmlspecialchars($project['type']) ?></span>
                        <h3 class="project-title"><?= htmlspecialchars($project['title']) ?></h3>
                        <p class="project-description"><?= htmlspecialchars($project['description']) ?></p>
                        <div class="project-tech-stack">
                            <?php foreach ($techStack as $tech): ?>
                                <span class="tech-tag"><?= htmlspecialchars($tech) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="project-links">
                        <?php if (!empty($project['github_url'])): ?>
                            <a href="<?= htmlspecialchars($project['github_url']) ?>" class="project-link" target="_blank"><i class="fab fa-github"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($project['live_url'])): ?>
                            <a href="<?= htmlspecialchars($project['live_url']) ?>" class="project-link" target="_blank"><i class="fa fa-globe"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($project['video_id'])): ?>
                            <button class="project-link" data-video="<?= htmlspecialchars($project['video_id']) ?>"><i class="fa fa-play"></i></button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php if (count($projects) > 3): ?>
            <div class="view-more-container">
                <button id="view-more-projects" class="view-more-btn">View More Projects <i class="fas fa-chevron-down"></i></button>
            </div>
        <?php endif; ?>
    </div>
</section>


    <!-- Skills Section -->
    <section id="skills" class="skills">
        <h2 class="section-title">Technical Skills</h2>
        <div class="skills-container">
            <?php 
            $skillCategories = array_unique(array_column($skills, 'category'));
            foreach ($skillCategories as $category): ?>
                <div class="skill-category">
                    <h3><?= htmlspecialchars(ucfirst($category)) ?></h3>
                    <div class="skills-grid">
                        <?php foreach ($skills as $skill): 
                            if ($skill['category'] === $category): ?>
                                <div class="skill-item">
                                    <?php if (!empty($skill['icon_class'])): ?>
                                        <i class="<?= htmlspecialchars($skill['icon_class']) ?>"></i>
                                    <?php endif; ?>
                                    <span><?= htmlspecialchars($skill['name']) ?></span>
                                </div>
                            <?php endif; 
                        endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact">
        <h2 class="section-title">Get In Touch</h2>
        <div class="contact-content">
            <div class="contact-info">
                <h3>Let's Connect</h3>
                <p>I'm always open to discussing new projects and opportunities.</p>
                <div class="contact-details">
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <span><?= htmlspecialchars($user['email'] ?? 'john.doe@email.com') ?></span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <span><?= htmlspecialchars($user['phone'] ?? '+1 234 567 8900') ?></span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span><?= htmlspecialchars($user['location'] ?? 'San Francisco, CA') ?></span>
                    </div>
                </div>
                <div class="social-links">
                    <?php foreach ($socialLinks as $link): ?>
                        <a href="<?= htmlspecialchars($link['url']) ?>" class="social-link" target="_blank">
                            <i class="<?= htmlspecialchars($link['icon_class']) ?>"></i>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="contact-form">
                <form id="contactForm" action="send_email.php" method="POST">
                    <div class="form-group">
                        <input type="text" id="name" name="name" placeholder="Your Name" required>
                    </div>
                    <div class="form-group">
                        <input type="email" id="email" name="email" placeholder="Your Email" required>
                    </div>
                    <div class="form-group">
                        <input type="text" id="subject" name="subject" placeholder="Subject" required>
                    </div>
                    <div class="form-group">
                        <textarea id="message" name="message" placeholder="Your Message" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($user['full_name'] ?? 'John Doe') ?>. All rights reserved.</p>
        </div>
    </footer>

    <!-- Chat Widget -->
    <div class="chat-widget">
        <button class="chat-button">
            <i class="fas fa-comments"></i>
        </button>
        <div class="chat-box">
            <div class="chat-header">
                <h4>Chat with me</h4>
                <button class="close-chat">&times;</button>
            </div>
            <div class="chat-messages">
                <!-- Messages will be dynamically added here -->
            </div>
            <div class="chat-input">
                <input type="text" placeholder="Type your message...">
                <button class="send-message"><i class="fas fa-paper-plane"></i></button>
            </div>
        </div>
    </div>
    
<script>// Update script.js or add this to your existing JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.querySelector('.theme-toggle');
    
    // Check for saved theme preference
    const currentTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', currentTheme);
    
    if (currentTheme === 'dark') {
        themeToggle.innerHTML = '<i class="fas fa-sun"></i>';
    }
    
    themeToggle.addEventListener('click', () => {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'light' ? 'dark' : 'light';
        
        document.documentElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        
        // Change icon
        themeToggle.innerHTML = newTheme === 'dark' 
            ? '<i class="fas fa-sun"></i>' 
            : '<i class="fas fa-moon"></i>';
    });
});</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const timelineItems = document.querySelectorAll(".timeline-item");

        function revealItems() {
            timelineItems.forEach(item => {
                const rect = item.getBoundingClientRect();
                if (rect.top < window.innerHeight - 100) {
                    item.style.opacity = "1";
                    item.style.transform = "translateX(0)";
                }
            });
        }

        // Initial check
        revealItems();

        // Check on scroll
        window.addEventListener("scroll", revealItems);
    });
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterTabs = document.querySelectorAll('.filter-tab');
    const projectCards = document.querySelectorAll('.project-card');
    
    filterTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            // Remove active class from all tabs
            filterTabs.forEach(t => t.classList.remove('active'));
            
            // Add active class to clicked tab
            this.classList.add('active');
            
            // Get filter value
            const filterValue = this.getAttribute('data-filter');
            
            // Filter projects
            projectCards.forEach(card => {
                if (filterValue === 'all') {
                    card.style.display = 'block';
                } else {
                    const cardType = card.getAttribute('data-type');
                    if (cardType === filterValue) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                }
            });
        });
    });
});
</script>

   <script src="script.js"></script>
</body>
</html>