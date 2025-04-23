// db.js - MySQL Database Connection Module

const mysql = require('mysql2/promise');

const pool = mysql.createPool({
    host: 'localhost',
    user: 'root',
    password: '',
    database: 'portfolio_db',
    waitForConnections: true,
    connectionLimit: 10,
    queueLimit: 0
});

module.exports = {
    // Get user profile data
    async getUserProfile() {
        const [rows] = await pool.query('SELECT * FROM user_profile LIMIT 1');
        return rows[0];
    },

    // Update user profile
    async updateUserProfile(data) {
        const result = await pool.query(`
            UPDATE user_profile SET 
                name = ?, title = ?, profile_image = ?, about_text = ?,
                contact_email = ?, contact_phone = ?, location = ?,
                linkedin_url = ?, github_url = ?, twitter_url = ?
            WHERE id = 1
        `, [
            data.name, data.title, data.profile_image, data.about_text,
            data.contact_email, data.contact_phone, data.location,
            data.linkedin_url, data.github_url, data.twitter_url
        ]);
        return result;
    },

    // Education CRUD operations
    async getEducation() {
        const [rows] = await pool.query('SELECT * FROM education ORDER BY start_date DESC');
        return rows;
    },

    async addEducation(data) {
        const result = await pool.query(`
            INSERT INTO education (institution, degree, field, start_date, end_date, grade, description)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        `, [data.institution, data.degree, data.field, data.start_date, data.end_date, data.grade, data.description]);
        return result;
    },

    async updateEducation(id, data) {
        const result = await pool.query(`
            UPDATE education SET 
                institution = ?, degree = ?, field = ?, start_date = ?, 
                end_date = ?, grade = ?, description = ?
            WHERE id = ?
        `, [data.institution, data.degree, data.field, data.start_date, data.end_date, data.grade, data.description, id]);
        return result;
    },

    async deleteEducation(id) {
        const result = await pool.query('DELETE FROM education WHERE id = ?', [id]);
        return result;
    },

    // Skills CRUD operations
    async getSkills() {
        const [rows] = await pool.query('SELECT * FROM skills ORDER BY id');
        return rows;
    },

    async addSkill(data) {
        const result = await pool.query(`
            INSERT INTO skills (name, level, icon, category)
            VALUES (?, ?, ?, ?)
        `, [data.name, data.level, data.icon, data.category]);
        return result;
    },

    async updateSkill(id, data) {
        const result = await pool.query(`
            UPDATE skills SET 
                name = ?, level = ?, icon = ?, category = ?
            WHERE id = ?
        `, [data.name, data.level, data.icon, data.category, id]);
        return result;
    },

    async deleteSkill(id) {
        const result = await pool.query('DELETE FROM skills WHERE id = ?', [id]);
        return result;
    },

    // Projects CRUD operations
    async getProjects() {
        const [rows] = await pool.query('SELECT * FROM projects ORDER BY created_at DESC');
        return rows;
    },

    async addProject(data) {
        const result = await pool.query(`
            INSERT INTO projects (title, description, image_url, video_url, github_url, live_url, technologies)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        `, [data.title, data.description, data.image_url, data.video_url, data.github_url, data.live_url, data.technologies]);
        return result;
    },

    async updateProject(id, data) {
        const result = await pool.query(`
            UPDATE projects SET 
                title = ?, description = ?, image_url = ?, video_url = ?, 
                github_url = ?, live_url = ?, technologies = ?
            WHERE id = ?
        `, [data.title, data.description, data.image_url, data.video_url, data.github_url, data.live_url, data.technologies, id]);
        return result;
    },

    async deleteProject(id) {
        const result = await pool.query('DELETE FROM projects WHERE id = ?', [id]);
        return result;
    },

    // Experience CRUD operations
    async getExperience(type = null) {
        let query = 'SELECT * FROM experience';
        const params = [];

        if (type) {
            query += ' WHERE type = ?';
            params.push(type);
        }

        query += ' ORDER BY start_date DESC';

        const [rows] = await pool.query(query, params);
        return rows;
    },

    async addExperience(data) {
        const result = await pool.query(`
            INSERT INTO experience (company, position, type, start_date, end_date, description)
            VALUES (?, ?, ?, ?, ?, ?)
        `, [data.company, data.position, data.type, data.start_date, data.end_date, data.description]);
        return result;
    },

    async updateExperience(id, data) {
        const result = await pool.query(`
            UPDATE experience SET 
                company = ?, position = ?, type = ?, start_date = ?, 
                end_date = ?, description = ?
            WHERE id = ?
        `, [data.company, data.position, data.type, data.start_date, data.end_date, data.description, id]);
        return result;
    },

    async deleteExperience(id) {
        const result = await pool.query('DELETE FROM experience WHERE id = ?', [id]);
        return result;
    },

    // Internships CRUD operations
    async getInternships() {
        const [rows] = await pool.query('SELECT * FROM internships ORDER BY start_date DESC');
        return rows;
    },

    async addInternship(data) {
        const result = await pool.query(`
            INSERT INTO internships (company, position, start_date, end_date, description)
            VALUES (?, ?, ?, ?, ?)
        `, [data.company, data.position, data.start_date, data.end_date, data.description]);
        return result;
    },

    async updateInternship(id, data) {
        const result = await pool.query(`
            UPDATE internships SET 
                company = ?, position = ?, start_date = ?, 
                end_date = ?, description = ?
            WHERE id = ?
        `, [data.company, data.position, data.start_date, data.end_date, data.description, id]);
        return result;
    },

    async deleteInternship(id) {
        const result = await pool.query('DELETE FROM internships WHERE id = ?', [id]);
        return result;
    },

    // Contact message operations
    async addContactMessage(data) {
        const result = await pool.query(`
            INSERT INTO contact_messages (name, email, message)
            VALUES (?, ?, ?)
        `, [data.name, data.email, data.message]);
        return result;
    },

    async getContactMessages(status = null) {
        let query = 'SELECT * FROM contact_messages';
        const params = [];

        if (status) {
            query += ' WHERE status = ?';
            params.push(status);
        }

        query += ' ORDER BY created_at DESC';

        const [rows] = await pool.query(query, params);
        return rows;
    },

    async updateMessageStatus(id, status) {
        const result = await pool.query(`
            UPDATE contact_messages SET status = ? WHERE id = ?
        `, [status, id]);
        return result;
    },

    // Admin authentication
    async authenticateAdmin(username, password) {
        const [rows] = await pool.query(
            'SELECT * FROM admin_users WHERE username = ?',
            [username]
        );

        if (rows.length === 0) {
            return null;
        }

        const user = rows[0];

        // Compare password with hash (using bcrypt in actual implementation)
        // This is a simplified example - in production, use proper password hashing
        if (user.password_hash === password) {
            await pool.query(
                'UPDATE admin_users SET last_login = CURRENT_TIMESTAMP WHERE id = ?',
                [user.id]
            );
            return user;
        }

        return null;
    },

    // Get all portfolio data (for main portfolio page)
    async getAllPortfolioData() {
        const [profile] = await pool.query('SELECT * FROM user_profile LIMIT 1');
        const [education] = await pool.query('SELECT * FROM education ORDER BY start_date DESC');
        const [skills] = await pool.query('SELECT * FROM skills ORDER BY id');
        const [projects] = await pool.query('SELECT * FROM projects ORDER BY created_at DESC');
        const [fulltime] = await pool.query('SELECT * FROM experience WHERE type = ? ORDER BY start_date DESC', ['fulltime']);
        const [parttime] = await pool.query('SELECT * FROM experience WHERE type = ? ORDER BY start_date DESC', ['parttime']);
        const [internships] = await pool.query('SELECT * FROM internships ORDER BY start_date DESC');

        // Format dates for frontend display
        education.forEach(edu => {
            edu.period = `${formatDate(edu.start_date)} - ${edu.end_date ? formatDate(edu.end_date) : 'Present'}`;
        });

        fulltime.forEach(exp => {
            exp.period = `${formatDate(exp.start_date)} - ${exp.end_date ? formatDate(exp.end_date) : 'Present'}`;
        });

        parttime.forEach(exp => {
            exp.period = `${formatDate(exp.start_date)} - ${exp.end_date ? formatDate(exp.end_date) : 'Present'}`;
        });

        internships.forEach(int => {
            int.period = `${formatDate(int.start_date)} - ${int.end_date ? formatDate(int.end_date) : 'Present'}`;
        });

        return {
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
            experience: {
                fulltime,
                parttime
            },
            internships
        };
    }
};

// Helper function to format dates
function formatDate(date) {
    if (!date) return '';
    const d = new Date(date);
    return d.toLocaleDateString('en-US', { year: 'numeric', month: 'short' });
}