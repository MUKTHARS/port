const express = require('express');
const mysql = require('mysql2/promise');
const bodyParser = require('body-parser');
const cors = require('cors');
const path = require('path');

const app = express();
const PORT = process.env.PORT || 3000;

// Middleware
app.use(cors());
app.use(bodyParser.json());
app.use(express.static(path.join(__dirname, 'public')));

// Database connection
const pool = mysql.createPool({
    host: 'localhost',
    user: 'root',
    password: '',
    database: 'portfolio_db',
    waitForConnections: true,
    connectionLimit: 10,
    queueLimit: 0
});

// API Routes
// Profile routes
app.get('/api/portfolio', async (req, res) => {
    try {
        const [profile] = await pool.query('SELECT * FROM user_profile LIMIT 1');
        const [education] = await pool.query('SELECT * FROM education ORDER BY start_date DESC');
        const [skills] = await pool.query('SELECT * FROM skills ORDER BY id');
        const [projects] = await pool.query('SELECT * FROM projects ORDER BY created_at DESC');
        const [fulltime] = await pool.query('SELECT * FROM experience WHERE type = ? ORDER BY start_date DESC', ['fulltime']);
        const [parttime] = await pool.query('SELECT * FROM experience WHERE type = ? ORDER BY start_date DESC', ['parttime']);
        const [internships] = await pool.query('SELECT * FROM internships ORDER BY start_date DESC');

        res.json({
            name: profile[0]?.name || '',
            title: profile[0]?.title || '',
            profileImage: profile[0]?.profile_image || '',
            about: profile[0]?.about_text || '',
            contactEmail: profile[0]?.contact_email || '',
            contactPhone: profile[0]?.contact_phone || '',
            location: profile[0]?.location || '',
            linkedinUrl: profile[0]?.linkedin_url || '',
            githubUrl: profile[0]?.github_url || '',
            twitterUrl: profile[0]?.twitter_url || '',
            education,
            skills,
            projects,
            experience: { fulltime, parttime },
            internships
        });
    } catch (err) {
        console.error(err);
        res.status(500).json({ error: 'Database error' });
    }
});

// Admin profile routes
app.get('/api/admin/profile', async (req, res) => {
    try {
        const [rows] = await pool.query('SELECT * FROM user_profile LIMIT 1');
        res.json(rows[0] || {});
    } catch (err) {
        console.error(err);
        res.status(500).json({ error: 'Database error' });
    }
});

app.post('/api/admin/profile', async (req, res) => {
    try {
        const { name, title, profile_image, about_text, contact_email, contact_phone, location, linkedin_url, github_url, twitter_url } = req.body;

        // Check if profile exists
        const [existing] = await pool.query('SELECT * FROM user_profile LIMIT 1');

        if (existing.length > 0) {
            // Update existing profile
            await pool.query(`
                UPDATE user_profile SET 
                    name = ?, title = ?, profile_image = ?, about_text = ?,
                    contact_email = ?, contact_phone = ?, location = ?,
                    linkedin_url = ?, github_url = ?, twitter_url = ?
                WHERE id = 1
            `, [name, title, profile_image, about_text, contact_email, contact_phone, location, linkedin_url, github_url, twitter_url]);
        } else {
            // Insert new profile
            await pool.query(`
                INSERT INTO user_profile 
                (name, title, profile_image, about_text, contact_email, contact_phone, location, linkedin_url, github_url, twitter_url)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            `, [name, title, profile_image, about_text, contact_email, contact_phone, location, linkedin_url, github_url, twitter_url]);
        }

        res.json({ success: true });
    } catch (err) {
        console.error(err);
        res.status(500).json({ error: 'Database error' });
    }
});

// Education routes
app.get('/api/admin/education', async (req, res) => {
    try {
        const [rows] = await pool.query('SELECT * FROM education ORDER BY start_date DESC');
        res.json(rows);
    } catch (err) {
        console.error(err);
        res.status(500).json({ error: 'Database error' });
    }
});

app.post('/api/admin/education', async (req, res) => {
    try {
        const { institution, degree, field, start_date, end_date, grade, description } = req.body;
        const [result] = await pool.query(`
            INSERT INTO education 
            (institution, degree, field, start_date, end_date, grade, description)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        `, [institution, degree, field, start_date, end_date, grade, description]);

        res.json({ success: true, id: result.insertId });
    } catch (err) {
        console.error(err);
        res.status(500).json({ error: 'Database error' });
    }
});

app.put('/api/admin/education/:id', async (req, res) => {
    try {
        const { id } = req.params;
        const { institution, degree, field, start_date, end_date, grade, description } = req.body;

        await pool.query(`
            UPDATE education SET 
                institution = ?, degree = ?, field = ?, start_date = ?, 
                end_date = ?, grade = ?, description = ?
            WHERE id = ?
        `, [institution, degree, field, start_date, end_date, grade, description, id]);

        res.json({ success: true });
    } catch (err) {
        console.error(err);
        res.status(500).json({ error: 'Database error' });
    }
});

app.delete('/api/admin/education/:id', async (req, res) => {
    try {
        const { id } = req.params;
        await pool.query('DELETE FROM education WHERE id = ?', [id]);
        res.json({ success: true });
    } catch (err) {
        console.error(err);
        res.status(500).json({ error: 'Database error' });
    }
});

// Similar routes for skills, projects, experience, internships, and messages...

// Start server
app.listen(PORT, () => {
    console.log(`Server running on port ${PORT}`);
});