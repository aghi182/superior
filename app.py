# app.py
import os
import sqlite3
from flask import Flask, render_template, request, redirect, url_for, jsonify, send_from_directory, flash, session
from werkzeug.utils import secure_filename
from datetime import datetime
from functools import wraps

BASE_DIR = os.path.dirname(os.path.abspath(__file__))
DB_PATH = os.path.join(BASE_DIR, 'website.db')
UPLOAD_FOLDER = os.path.join(BASE_DIR, 'pictures')
ALLOWED_EXT = {'png','jpg','jpeg','gif','svg','webp','ico'}

os.makedirs(UPLOAD_FOLDER, exist_ok=True)

app = Flask(__name__)
app.config['UPLOAD_FOLDER'] = UPLOAD_FOLDER
app.config['SECRET_KEY'] = 'change_this_secret_key_for_prod'  # ganti di production

def get_db():
    conn = sqlite3.connect(DB_PATH)
    conn.row_factory = sqlite3.Row
    return conn

def allowed_file(filename):
    return '.' in filename and filename.rsplit('.',1)[1].lower() in ALLOWED_EXT

def login_required(f):
    @wraps(f)
    def decorated_function(*args, **kwargs):
        if 'logged_in' not in session:
            return redirect(url_for('login'))
        return f(*args, **kwargs)
    return decorated_function

# Login route
@app.route('/login', methods=['GET', 'POST'])
def login():
    if request.method == 'POST':
        username = request.form.get('username')
        password = request.form.get('password')
        
        # Simple hardcoded login - ganti dengan database authentication di production
        if username == 'admin' and password == 'admin123':
            session['logged_in'] = True
            flash('Login berhasil!', 'success')
            return redirect(url_for('dashboard'))
        else:
            flash('Username atau password salah!', 'error')
    
    return render_template('login.html')

@app.route('/logout')
def logout():
    session.pop('logged_in', None)
    flash('Anda telah logout', 'info')
    return redirect(url_for('index'))

# -------------------------
# Admin UI routes
# -------------------------
@app.route('/admin')
@login_required
def dashboard():
    # show overview with counts
    conn = get_db()
    cur = conn.cursor()
    cur.execute("SELECT COUNT(*) FROM projects"); total_projects = cur.fetchone()[0]
    cur.execute("SELECT COUNT(*) FROM services"); total_services = cur.fetchone()[0]
    cur.execute("SELECT COUNT(*) FROM contact_content"); total_contacts = cur.fetchone()[0]
    conn.close()
    return render_template('dashboard.html', total_projects=total_projects, total_services=total_services, total_contacts=total_contacts)

# Home Section
@app.route('/home', methods=['GET','POST'])
@login_required
def home_section():
    conn = get_db()
    cur = conn.cursor()
    if request.method == 'POST':
        hero_title1 = request.form.get('hero_title1')
        hero_title2 = request.form.get('hero_title2')
        hero_subtitle = request.form.get('hero_subtitle')
        hero_button_text = request.form.get('hero_button_text')
        hero_button_link = request.form.get('hero_button_link')
        cur.execute("UPDATE home_content SET hero_title1=?,hero_title2=?,hero_subtitle=?,hero_button_text=?,hero_button_link=?,updated_at=CURRENT_TIMESTAMP WHERE id=1",
                    (hero_title1,hero_title2,hero_subtitle,hero_button_text,hero_button_link))
        if cur.rowcount == 0:  # if no row with id=1 (first time)
            cur.execute("INSERT INTO home_content (hero_title1, hero_title2, hero_subtitle, hero_button_text, hero_button_link) VALUES (?,?,?,?,?)",
                        (hero_title1,hero_title2,hero_subtitle,hero_button_text,hero_button_link))
        conn.commit()
        conn.close()
        # notify front-end via API change (client polling will see update)
        flash('Home content updated', 'success')
        return redirect(url_for('home_section'))
    cur.execute("SELECT * FROM home_content ORDER BY id LIMIT 1")
    home = cur.fetchone()
    conn.close()
    return render_template('home_section.html', home=home)

# About Section
@app.route('/about', methods=['GET', 'POST'])
@login_required
def about_section():
    conn = get_db()
    cur = conn.cursor()

    if request.method == 'POST':
        title = request.form.get('title')
        description = request.form.get('description')

        # Ambil file gambar yang diupload
        image_file = request.files.get('image')
        image_name = None

        if image_file and image_file.filename != '':
            # Simpan ke folder pictures/
            image_name = image_file.filename
            image_file.save(os.path.join('pictures', image_name))

            # Update database dengan gambar baru
            cur.execute("""
                UPDATE about_content 
                SET title=?, description=?, images=?, updated_at=CURRENT_TIMESTAMP 
                WHERE id=1
            """, (title, description, image_name))
        else:
            # Kalau gambar tidak diubah
            cur.execute("""
                UPDATE about_content 
                SET title=?, description=?, updated_at=CURRENT_TIMESTAMP 
                WHERE id=1
            """, (title, description))

        # Kalau belum ada data → insert baru
        if cur.rowcount == 0:
            cur.execute("""
                INSERT INTO about_content (title, description, images) 
                VALUES (?, ?, ?)
            """, (title, description, image_name))

        conn.commit()
        conn.close()
        flash('About updated', 'success')
        return redirect(url_for('about_section'))

    cur.execute("SELECT * FROM about_content ORDER BY id LIMIT 1")
    about = cur.fetchone()
    conn.close()
    return render_template('about_section.html', about=about)


# Projects (list / add / edit / delete)
@app.route('/projects')
@login_required
def projects_page():
    conn = get_db()
    cur = conn.cursor()
    cur.execute("SELECT * FROM projects ORDER BY sort_order ASC, id DESC")
    projects = cur.fetchall()
    conn.close()
    return render_template('projects_section.html', projects=projects)

@app.route('/projects/add', methods=['GET','POST'])
@login_required
def add_project():
    if request.method == 'POST':
        title = request.form.get('title')
        description = request.form.get('description')
        category = request.form.get('category')
        client = request.form.get('client')
        status = int(request.form.get('status',1))
        sort_order = int(request.form.get('sort_order',0))
        image_url = ''
        file = request.files.get('image')
        if file and allowed_file(file.filename):
            filename = secure_filename(file.filename)
            filepath = os.path.join(app.config['UPLOAD_FOLDER'], filename)
            file.save(filepath)
            image_url = f"pictures/{filename}"
        conn = get_db()
        cur = conn.cursor()
        cur.execute("INSERT INTO projects (title,description,image_url,category,client,status,sort_order) VALUES (?,?,?,?,?,?,?)",
                    (title,description,image_url,category,client,status,sort_order))
        conn.commit(); conn.close()
        flash('Project added', 'success')
        return redirect(url_for('projects_page'))
    return render_template('project_form.html', project=None)

@app.route('/projects/edit/<int:id>', methods=['GET','POST'])
@login_required
def edit_project(id):
    conn = get_db()
    cur = conn.cursor()
    if request.method == 'POST':
        title = request.form.get('title')
        description = request.form.get('description')
        category = request.form.get('category')
        client = request.form.get('client')
        status = int(request.form.get('status',1))
        sort_order = int(request.form.get('sort_order',0))
        # Keep existing image_url if no new file uploaded
        image_url = request.form.get('image_url', '')
        file = request.files.get('image')
        if file and allowed_file(file.filename):
            filename = secure_filename(file.filename)
            filepath = os.path.join(app.config['UPLOAD_FOLDER'], filename)
            file.save(filepath)
            image_url = f"pictures/{filename}"
        cur.execute("UPDATE projects SET title=?,description=?,image_url=?,category=?,client=?,status=?,sort_order=?,updated_at=CURRENT_TIMESTAMP WHERE id=?",
                    (title,description,image_url,category,client,status,sort_order,id))
        conn.commit(); conn.close()
        flash('Project updated', 'success')
        return redirect(url_for('projects_page'))
    cur.execute("SELECT * FROM projects WHERE id=?", (id,))
    project = cur.fetchone()
    conn.close()
    return render_template('project_form.html', project=project)

@app.route('/projects/delete/<int:id>', methods=['POST'])
@login_required
def delete_project(id):
    conn = get_db()
    cur = conn.cursor()
    cur.execute("DELETE FROM projects WHERE id=?", (id,))
    conn.commit(); conn.close()
    return redirect(url_for('projects_page'))

# Services (single row edits)
@app.route('/services', methods=['GET','POST'])
@login_required
def services_page():
    conn = get_db()
    cur = conn.cursor()
    if request.method == 'POST':
        mechanical_title = request.form.get('mechanical_title')
        mechanical_description = request.form.get('mechanical_description')
        electrical_title = request.form.get('electrical_title')
        electrical_description = request.form.get('electrical_description')
        mechanical_image_url = request.form.get('mechanical_image_url', '')
        electrical_image_url = request.form.get('electrical_image_url', '')
        # handle uploads
        mfile = request.files.get('mechanical_image')
        efile = request.files.get('electrical_image')
        if mfile and allowed_file(mfile.filename):
            mfn = secure_filename(mfile.filename)
            mfile.save(os.path.join(app.config['UPLOAD_FOLDER'], mfn))
            mechanical_image_url = f"pictures/{mfn}"
        if efile and allowed_file(efile.filename):
            efn = secure_filename(efile.filename)
            efile.save(os.path.join(app.config['UPLOAD_FOLDER'], efn))
            electrical_image_url = f"pictures/{efn}"
        cur.execute("UPDATE services SET mechanical_title=?,mechanical_description=?,mechanical_image_url=?,electrical_title=?,electrical_description=?,electrical_image_url=?,updated_at=CURRENT_TIMESTAMP WHERE id=1",
                    (mechanical_title,mechanical_description,mechanical_image_url,electrical_title,electrical_description,electrical_image_url))
        if cur.rowcount == 0:
            cur.execute("INSERT INTO services (mechanical_title,mechanical_description,mechanical_image_url,electrical_title,electrical_description,electrical_image_url) VALUES (?,?,?,?,?,?)",
                        (mechanical_title,mechanical_description,mechanical_image_url,electrical_title,electrical_description,electrical_image_url))
        conn.commit(); conn.close()
        flash('Services updated', 'success')
        return redirect(url_for('services_page'))
    cur.execute("SELECT * FROM services ORDER BY id LIMIT 1")
    services = cur.fetchone()
    conn.close()
    return render_template('services_section.html', services=services)

# Vision & Mission
@app.route('/vision', methods=['GET','POST'])
@login_required
def vision_page():
    conn = get_db()
    cur = conn.cursor()
    if request.method == 'POST':
        title = request.form.get('title')
        description = request.form.get('description')
        image_url = request.form.get('image_url','')
        f = request.files.get('image')
        if f and allowed_file(f.filename):
            fn = secure_filename(f.filename)
            f.save(os.path.join(app.config['UPLOAD_FOLDER'], fn))
            image_url = f"pictures/{fn}"
        cur.execute("UPDATE vision_content SET title=?,description=?,image_url=?,updated_at=CURRENT_TIMESTAMP WHERE id=1",
                    (title,description,image_url))
        if cur.rowcount==0:
            cur.execute("INSERT INTO vision_content (title,description,image_url) VALUES (?,?,?)",(title,description,image_url))
        conn.commit(); conn.close()
        flash('Vision updated','success')
        return redirect(url_for('vision_page'))
    cur.execute("SELECT * FROM vision_content ORDER BY id LIMIT 1")
    vision = cur.fetchone()
    conn.close()
    return render_template('vision_section.html', vision=vision)

@app.route('/mission', methods=['GET','POST'])
@login_required
def mission_page():
    conn = get_db()
    cur = conn.cursor()
    if request.method == 'POST':
        title = request.form.get('title')
        description = request.form.get('description')
        cur.execute("UPDATE mission_content SET title=?,description=?,updated_at=CURRENT_TIMESTAMP WHERE id=1",(title,description))
        if cur.rowcount==0:
            cur.execute("INSERT INTO mission_content (title,description) VALUES (?,?)",(title,description))
        conn.commit(); conn.close()
        flash('Mission updated','success')
        return redirect(url_for('mission_page'))
    cur.execute("SELECT * FROM mission_content ORDER BY id LIMIT 1")
    mission = cur.fetchone()
    conn.close()
    return render_template('mission_section.html', mission=mission)

# Contact
@app.route('/contact', methods=['GET','POST'])
@login_required
def contact_page():
    conn = get_db()
    cur = conn.cursor()
    if request.method == 'POST':
        title = request.form.get('title')
        person1_name = request.form.get('person1_name')
        person1_phone = request.form.get('person1_phone')
        person1_status = 1 if request.form.get('person1_status')=='on' else 0
        person2_name = request.form.get('person2_name')
        person2_phone = request.form.get('person2_phone')
        person2_status = 1 if request.form.get('person2_status')=='on' else 0
        email = request.form.get('email')
        text_wa = request.form.get('text_wa')
        cur.execute("UPDATE contact_content SET title=?,person1_name=?,person1_phone=?,person1_status=?,person2_name=?,person2_phone=?,person2_status=?,email=?,text_wa=?,updated_at=CURRENT_TIMESTAMP WHERE id=1",
                    (title,person1_name,person1_phone,person1_status,person2_name,person2_phone,person2_status,email,text_wa))
        if cur.rowcount==0:
            cur.execute("INSERT INTO contact_content (title,person1_name,person1_phone,person1_status,person2_name,person2_phone,person2_status,email,text_wa) VALUES (?,?,?,?,?,?,?,?,?)",
                        (title,person1_name,person1_phone,person1_status,person2_name,person2_phone,person2_status,email,text_wa))
        conn.commit(); conn.close()
        flash('Contact updated','success')
        return redirect(url_for('contact_page'))
    cur.execute("SELECT * FROM contact_content ORDER BY id LIMIT 1")
    contact = cur.fetchone()
    conn.close()
    return render_template('contact_section.html', contact=contact)

# Serve uploaded pictures
@app.route('/pictures/<path:filename>')
def pictures(filename):
    return send_from_directory(app.config['UPLOAD_FOLDER'], filename)

# -------------------------
# API endpoints (JSON)
# -------------------------
@app.route('/api/get_all', methods=['GET'])
def api_get_all():
    conn = get_db()
    cur = conn.cursor()
    data = {}
    cur.execute("SELECT * FROM home_content ORDER BY id LIMIT 1"); data['home'] = dict(cur.fetchone()) if cur.fetchone() is None else None
    # caution: above line uses fetchone twice; fix by trying properly
    # better to re-query:
    cur.execute("SELECT * FROM home_content ORDER BY id LIMIT 1")
    row = cur.fetchone()
    data['home'] = dict(row) if row else None

    cur.execute("SELECT * FROM about_content ORDER BY id LIMIT 1")
    row = cur.fetchone()
    data['about'] = dict(row) if row else None

    cur.execute("SELECT * FROM contact_content ORDER BY id LIMIT 1")
    row = cur.fetchone()
    data['contact'] = dict(row) if row else None

    cur.execute("SELECT * FROM projects ORDER BY sort_order ASC, id DESC LIMIT 100")
    rows = cur.fetchall()
    data['projects'] = [dict(r) for r in rows]

    cur.execute("SELECT * FROM services ORDER BY id LIMIT 1")
    row = cur.fetchone()
    data['services'] = dict(row) if row else None

    cur.execute("SELECT * FROM vision_content ORDER BY id LIMIT 1")
    row = cur.fetchone()
    data['vision'] = dict(row) if row else None

    cur.execute("SELECT * FROM mission_content ORDER BY id LIMIT 1")
    row = cur.fetchone()
    data['mission'] = dict(row) if row else None

    conn.close()
    return jsonify(data)

# small health check
@app.route('/api/health')
def api_health():
    return jsonify({"status":"ok","db":os.path.exists(DB_PATH)})

@app.route('/')
def index():
    conn = get_db()
    cur = conn.cursor()
    data = {}

    # Home
    cur.execute("SELECT * FROM home_content ORDER BY id LIMIT 1")
    row = cur.fetchone()
    data['home'] = dict(row) if row else None

    # About
    cur.execute("SELECT * FROM about_content ORDER BY id LIMIT 1")
    row = cur.fetchone()
    data['about'] = dict(row) if row else None

    # Contact
    cur.execute("SELECT * FROM contact_content ORDER BY id LIMIT 1")
    row = cur.fetchone()
    data['contact'] = dict(row) if row else None

    # Projects
    cur.execute("SELECT * FROM projects ORDER BY sort_order ASC, id DESC LIMIT 100")
    rows = cur.fetchall()
    data['projects'] = [dict(r) for r in rows]

    # Services
    cur.execute("SELECT * FROM services ORDER BY id LIMIT 1")
    row = cur.fetchone()
    data['services'] = dict(row) if row else None

    # Vision
    cur.execute("SELECT * FROM vision_content ORDER BY id LIMIT 1")
    row = cur.fetchone()
    data['vision'] = dict(row) if row else None

    # Mission
    cur.execute("SELECT * FROM mission_content ORDER BY id LIMIT 1")
    row = cur.fetchone()
    data['mission'] = dict(row) if row else None

    conn.close()
    return render_template('index.html', data=data, current_year=datetime.now().year)



@app.route('/api/content')
def api_content():
    profile = CompanyProfile.query.first()
    if not profile:
        return jsonify({'error': 'data not found'}), 404
    return jsonify({
        'hero_title1': profile.hero_title1,
        'hero_title2': profile.hero_title2,
        'hero_subtitle': profile.hero_subtitle,
        'about_title': profile.about_title,
        'about_description': profile.about_description,
        'stat_projects': profile.stat_projects,
        'stat_years': profile.stat_years,
        'stat_satisfaction': profile.stat_satisfaction,
        'contact_person1': profile.contact_person1,
        'contact_phone1': profile.contact_phone1,
        'contact_person2': profile.contact_person2,
        'contact_phone2': profile.contact_phone2,
        'contact_email': profile.contact_email
    })



if __name__ == "__main__":
    app.run(debug=True, host='127.0.0.1', port=5000)
