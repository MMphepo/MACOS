# complaints/urls.py
from django.urls import path
from . import views

urlpatterns = [
    path("file-complaint/", views.file_complaint, name="file_complaint"),
]