# complaints/urls.py
from django.urls import path
from . import views

urlpatterns = [
    path("file-complaint/", views.file_complaint, name="file_complaint"),
    path("fetch-filed-complaints/<str:username>/", views.fetch_filed_complaints, name="fetch_filed_complaints"),
    path("fetch-all-complaints/", views.fetch_all_complaints, name="fetch_all_complaints"),
]