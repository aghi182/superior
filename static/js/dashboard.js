{% extends "layout.html" %}
{% block content %}
<h2>{{ 'Edit Project' if project else 'Add Project' }}</h2>
<form method="post" enctype="multipart/form-data">
  <div class="mb-3">
    <label>Title</label>
    <input class="form-control" name="title" value="{{ project['title'] if project else '' }}">
  </div>
  <div class="mb-3">
    <label>Description</label>
    <textarea class="form-control" name="description">{{ project['description'] if project else '' }}</textarea>
  </div>
  <div class="mb-3">
    <label>Category</label>
    <input class="form-control" name="category" value="{{ project['category'] if project else '' }}">
  </div>
  <div class="mb-3">
    <label>Image (upload)</label>
    <input type="file" name="image" class="form-control">
    <small class="text-muted">Jika tidak diisi, akan tetap menggunakan yang lama.</small>
  </div>
  <div class="mb-3">
    <label>Status</label>
    <select class="form-select" name="status">
      <option value="1" {% if project and project['status']==1 %}selected{% endif %}>Active</option>
      <option value="0" {% if project and project['status']==0 %}selected{% endif %}>Inactive</option>
    </select>
  </div>
  <button class="btn btn-primary">Simpan Perubahan</button>
</form>
{% endblock %}
