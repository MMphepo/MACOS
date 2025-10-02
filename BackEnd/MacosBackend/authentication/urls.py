from django.urls import path
from . import views

app_name = 'accounts'

urlpatterns = [
    # Authentication API endpoints
    path('auth/register/', views.register_api, name='register'),
    path('auth/login/', views.login_api, name='login'),
    path('auth/logout/', views.logout_api, name='logout'),
    
    # User Profile API endpoints
    path('user/profile/', views.profile_api, name='profile'),
    path('user/dashboard/', views.dashboard_api, name='dashboard'),
    
    # Utility API endpoints
    path('auth/check-username/', views.check_username_api, name='check_username'),
    path('auth/check-email/', views.check_email_api, name='check_email'),
    path('auth/job-titles/', views.get_job_titles_api, name='job_titles'),

    # Consumer API endpoint
    path('consumers/', views.fetch_all_consumers, name='fetch_all_consumers'),
    # Investigator API endpoint
    path('investigators/', views.fetch_all_investigators, name='fetch_all_investigators'),

    # Workload and task assignment endpoints
    path('investigators/<int:investigator_id>/workload/', views.investigator_workload_api, name='investigator_workload'),
    path('assign-task/', views.assign_task_to_staff_api, name='assign_task_to_staff'),

    # Endpoint to check job title of a specific MacraStaff
    path('staff/<int:staff_id>/job-title/', views.check_job_title, name='check_job_title'),
]