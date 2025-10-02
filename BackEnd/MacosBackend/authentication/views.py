from rest_framework.decorators import api_view, permission_classes
from rest_framework.permissions import AllowAny, IsAuthenticated
from rest_framework.response import Response
from rest_framework import status
from .models import MacraStaff, Users, Consumer, Complaint, MacraStaff
from rest_framework.decorators import api_view, permission_classes
from rest_framework.permissions import IsAuthenticated
from .models import Complaint
from django.http import JsonResponse
from authentication.models import MacraStaff
from django.views.decorators.http import require_GET

# Endpoint to check the job title of a specific MacraStaff
@require_GET
def check_job_title(request, staff_id):
    try:
        staff = MacraStaff.objects.get(pk=staff_id)
        return JsonResponse({
            'success': True,
            'staff_id': staff_id,
            'job_title': staff.job_title
        })
    except MacraStaff.DoesNotExist:
        return JsonResponse({'success': False, 'error': 'Staff not found'}, status=404)


# -------------------------------------------------------
# ASSIGN TASK TO STAFF ENDPOINT
# -------------------------------------------------------
@api_view(["POST"])
# @permission_classes([IsAuthenticated])
def assign_task_to_staff_api(request):
    
    """
    Assign a complaint (task) to a specific staff member.
    Expects JSON: {"complaint_id": int, "staff_id": int}
    """
    print("[DEBUG] assign_task_to_staff_api called")
    data = request.data
    print(f"[DEBUG] Incoming data: {data}")
    complaint_id = data.get("complaint_id")
    staff_id = data.get("staff_id")
    print(f"[DEBUG] complaint_id: {complaint_id}, staff_id: {staff_id}")
    if not complaint_id or not staff_id:
        print("[DEBUG] Missing complaint_id or staff_id")
        return Response({
            "success": False,
            "message": "Both complaint_id and staff_id are required."
        }, status=status.HTTP_400_BAD_REQUEST)
    try:
        complaint = Complaint.objects.get(pk=complaint_id)
        print(f"[DEBUG] Found complaint: {complaint}")
        staff = MacraStaff.objects.get(pk=staff_id)
        print(f"[DEBUG] Found staff: {staff}")
        complaint.assigned_staff = staff
        complaint.save()
        print(f"[DEBUG] Assigned complaint {complaint_id} to staff {staff_id}")
        return Response({
            "success": True,
            "message": f"Complaint {complaint_id} assigned to staff {staff_id}."
        }, status=status.HTTP_200_OK)
    except Complaint.DoesNotExist:
        print(f"[DEBUG] Complaint with id {complaint_id} not found")
        return Response({
            "success": False,
            "message": "Complaint not found."
        }, status=status.HTTP_404_NOT_FOUND)
    except MacraStaff.DoesNotExist:
        print(f"[DEBUG] Staff with id {staff_id} not found")
        return Response({
            "success": False,
            "message": "Staff not found."
        }, status=status.HTTP_404_NOT_FOUND)

# -------------------------------------------------------
# INVESTIGATOR WORKLOAD ENDPOINT
# -------------------------------------------------------
@api_view(["GET"])
# @permission_classes([IsAuthenticated])
def investigator_workload_api(request, investigator_id):
    """
    Returns the number of complaints assigned to a specific investigator (MacraStaff).
    """
    try:
        staff = MacraStaff.objects.get(pk=investigator_id)
        workload = staff.assigned_complaints.count()
        return Response({
            "success": True,
            "investigator_id": investigator_id,
            "workload": workload
        }, status=status.HTTP_200_OK)
    except MacraStaff.DoesNotExist:
        return Response({
            "success": False,
            "message": "Investigator not found."
        }, status=status.HTTP_404_NOT_FOUND)

@api_view(['GET'])
@permission_classes([AllowAny])
def fetch_all_investigators(request):
    investigators = MacraStaff.objects.select_related('user').all()
    data = [
        {
            'id': staff.user.id,
            'username': staff.user.username,
            'email': staff.user.email,
            'first_name': staff.user.first_name,
            'last_name': staff.user.last_name,
            'phone_number': staff.phone_number,
            'job_title': staff.job_title,
            'department': staff.department,
            'hire_date': staff.hire_date,
            'skills': staff.skills,
        }
        for staff in investigators
    ]
    return Response({'success': True, 'investigators': data})

# Fetch all consumers
from django.views.decorators.http import require_GET
from django.http import JsonResponse

@require_GET
def fetch_all_consumers(request):
    consumers = Consumer.objects.select_related('user').all()
    data = [
        {
            'id': c.user.id,
            'username': c.user.username,
            'email': c.user.email,
            'first_name': c.user.first_name,
            'last_name': c.user.last_name,
            'phone_number': c.phone_number,
            'address': c.address,
            'registration_date': c.registration_date,
        }
        for c in consumers
    ]
    return JsonResponse({'success': True, 'consumers': data})
from django.shortcuts import get_object_or_404
from django.contrib.auth import authenticate, login, logout
from django.contrib.auth.decorators import login_required
from django.db import transaction
from django.views.decorators.csrf import csrf_exempt
from django.views.decorators.http import require_http_methods
from django.core.exceptions import ValidationError
from django.contrib.auth.password_validation import validate_password
from django.utils.decorators import method_decorator
from django.views import View
from rest_framework.authtoken.models import Token
import json
from datetime import datetime


# -------------------------------------------------------
# JOB TITLE CHOICES FOR MACRA STAFF
# -------------------------------------------------------
MACRA_JOB_TITLES = [
    ('director_general', 'Director General'),
    ('consumer_affairs_manager', 'Consumer Affairs Manager'),
    ('senior_consumer_affairs_officer', 'Senior Consumer Affairs Officer'),
    ('investigation_officer', 'Investigation Officer'),
    ('consumer_affairs_officer', 'Consumer Affairs Officer'),
    ('consumer_affairs_assistant', 'Consumer Affairs Assistant'),
    ('legal_officer', 'Legal Officer'),
    ('data_entry_clerk', 'Data Entry Clerk'),
    ('registry_clerk', 'Registry Clerk'),
    ('administrative_assistant', 'Administrative Assistant'),
    ('front_office_clerk', 'Front Office Clerk/Receptionist'),
]

MACRA_DEPARTMENTS = [
    ('consumer_affairs', 'Consumer Affairs'),
    ('legal', 'Legal Department'),
    ('administration', 'Administration'),
    ('registry', 'Registry'),
    ('executive', 'Executive Office'),
]


# -------------------------------------------------------
# REGISTRATION API ENDPOINT
# -------------------------------------------------------
@api_view(['POST'])
@permission_classes([AllowAny])
def register_api(request):
    print('ndafika mu register')
    """
    API endpoint for user registration (Consumer and MACRA Staff)
    
    Expected JSON payload:
    {
        "username": "string",
        "email": "string", 
        "password": "string",
        "confirm_password": "string",
        "first_name": "string",
        "last_name": "string",
        "role": "consumer|macra_staff",
        "phone_number": "string" (optional),
        "address": "string" (optional for consumers),
        "job_title": "string" (required for macra_staff),
        "department": "string" (required for macra_staff),
        "hire_date": "YYYY-MM-DD" (optional for macra_staff)
    }
    """
    try:
        data = request.data
        
        # Validate required fields
        required_fields = ['username', 'email', 'password', 'confirm_password', 'first_name', 'last_name', 'role']
        missing_fields = [field for field in required_fields if not data.get(field)]
        
        if missing_fields:
            return Response({
                'success': False,
                'message': f'Missing required fields: {", ".join(missing_fields)}',
                'errors': {field: 'This field is required.' for field in missing_fields}
            }, status=status.HTTP_400_BAD_REQUEST)
        
        role = data.get('role')
        if role not in ['consumer', 'macra_staff']:
            return Response({
                'success': False,
                'message': 'Invalid role. Must be "consumer" or "macra_staff".',
                'errors': {'role': 'Invalid role selected.'}
            }, status=status.HTTP_400_BAD_REQUEST)
        
        # Check if passwords match
        if data.get('password') != data.get('confirm_password'):
            return Response({
                'success': False,
                'message': 'Passwords do not match.',
                'errors': {'password': 'Passwords do not match.'}
            }, status=status.HTTP_400_BAD_REQUEST)
        
        # Validate password strength
        try:
            validate_password(data.get('password'))
        except ValidationError as e:
            return Response({
                'success': False,
                'message': 'Password validation failed.',
                'errors': {'password': e.messages}
            }, status=status.HTTP_400_BAD_REQUEST)
        
        # Check if username already exists
        if Users.objects.filter(username=data.get('username')).exists():
            return Response({
                'success': False,
                'message': 'Username already exists.',
                'errors': {'username': 'This username is already taken.'}
            }, status=status.HTTP_400_BAD_REQUEST)
        
        # Check if email already exists
        if Users.objects.filter(email=data.get('email')).exists():
            return Response({
                'success': False,
                'message': 'Email already exists.',
                'errors': {'email': 'This email is already registered.'}
            }, status=status.HTTP_400_BAD_REQUEST)
        
        # Additional validation for MACRA staff
        if role == 'macra_staff':
            job_title = data.get('job_title')
            department = data.get('department')
            
            if not job_title:
                return Response({
                    'success': False,
                    'message': 'Job title is required for MACRA staff.',
                    'errors': {'job_title': 'This field is required for MACRA staff.'}
                }, status=status.HTTP_400_BAD_REQUEST)
            
            if not department:
                return Response({
                    'success': False,
                    'message': 'Department is required for MACRA staff.',
                    'errors': {'department': 'This field is required for MACRA staff.'}
                }, status=status.HTTP_400_BAD_REQUEST)
            
            # Validate job title
            valid_job_titles = [code for code, _ in MACRA_JOB_TITLES]
            if job_title not in valid_job_titles:
                return Response({
                    'success': False,
                    'message': 'Invalid job title.',
                    'errors': {'job_title': 'Please select a valid job title.'}
                }, status=status.HTTP_400_BAD_REQUEST)
            
            # Validate department
            valid_departments = [code for code, _ in MACRA_DEPARTMENTS]
            if department not in valid_departments:
                return Response({
                    'success': False,
                    'message': 'Invalid department.',
                    'errors': {'department': 'Please select a valid department.'}
                }, status=status.HTTP_400_BAD_REQUEST)
        
        # Create user with transaction
        with transaction.atomic():
            # Create base user
            user = Users.objects.create_user(
                username=data.get('username'),
                email=data.get('email'),
                password=data.get('password'),
                first_name=data.get('first_name'),
                last_name=data.get('last_name'),
                role=role
            )
            
            # Create authentication token
            token, created = Token.objects.get_or_create(user=user)
            
            # Create role-specific profile
            if role == 'consumer':
                consumer = Consumer.objects.create(
                    user=user,
                    phone_number=data.get('phone_number', ''),
                    address=data.get('address', '')
                )
                profile_data = {
                    'phone_number': consumer.phone_number,
                    'address': consumer.address,
                    'registration_date': consumer.registration_date.isoformat()
                }
                
            elif role == 'macra_staff':
                # Parse hire date
                hire_date = data.get('hire_date')
                if hire_date:
                    try:
                        hire_date = datetime.strptime(hire_date, '%Y-%m-%d').date()
                    except ValueError:
                        hire_date = datetime.now().date()
                else:
                    hire_date = datetime.now().date()
                
                # Get job title display name
                job_title_display = dict(MACRA_JOB_TITLES).get(data.get('job_title'))
                department_display = dict(MACRA_DEPARTMENTS).get(data.get('department'))
                
                staff = MacraStaff.objects.create(
                    user=user,
                    phone_number=data.get('phone_number', ''),
                    job_title=job_title_display,
                    department=department_display,
                    hire_date=hire_date
                )
                profile_data = {
                    'phone_number': staff.phone_number,
                    'job_title': staff.job_title,
                    'department': staff.department,
                    'hire_date': staff.hire_date.isoformat()
                }
            
            return Response({
                'success': True,
                'message': f'{role.replace("_", " ").title()} registration successful!',
                'data': {
                    'user': {
                        'id': user.id,
                        'username': user.username,
                        'email': user.email,
                        'first_name': user.first_name,
                        'last_name': user.last_name,
                        'role': user.role,
                        'role_display': user.get_role_display(),
                        'date_joined': user.date_joined.isoformat()
                    },
                    'profile': profile_data,
                    'token': token.key
                }
            }, status=status.HTTP_201_CREATED)
            
    except Exception as e:
        return Response({
            'success': False,
            'message': f'Registration failed: {str(e)}',
            'errors': {'general': str(e)}
        }, status=status.HTTP_500_INTERNAL_SERVER_ERROR)


# -------------------------------------------------------
# LOGIN API ENDPOINT
# -------------------------------------------------------
@api_view(['POST'])
@permission_classes([AllowAny])
def login_api(request):
    """
    API endpoint for user login
    
    Expected JSON payload:
    {
        "username": "string",
        "password": "string"
    }
    """
    try:
        data = request.data
        username = data.get('username')
        password = data.get('password')
        
        if not username or not password:
            return Response({
                'success': False,
                'message': 'Username and password are required.',
                'errors': {
                    'username': 'This field is required.' if not username else None,
                    'password': 'This field is required.' if not password else None
                }
            }, status=status.HTTP_400_BAD_REQUEST)
        
        # Authenticate user
        user = authenticate(username=username, password=password)
        
        if user is not None:
            if user.is_active:
                # Get or create token
                token, created = Token.objects.get_or_create(user=user)
                
                # Get profile information
                profile_info = get_user_profile_info(user)
                
                # Update last login
                user.last_login = datetime.now()
                user.save(update_fields=['last_login'])
                
                return Response({
                    'success': True,
                    'message': f'Login successful. Welcome back, {user.first_name}!',
                    'data': {
                        'user': {
                            'id': user.id,
                            'username': user.username,
                            'email': user.email,
                            'first_name': user.first_name,
                            'last_name': user.last_name,
                            'role': user.role,
                            'role_display': user.get_role_display(),
                            'last_login': user.last_login.isoformat() if user.last_login else None
                        },
                        'profile': profile_info,
                        'token': token.key,
                        'permissions': get_user_permissions(user)
                    }
                }, status=status.HTTP_200_OK)
            else:
                return Response({
                    'success': False,
                    'message': 'Your account is inactive. Please contact support.',
                    'errors': {'account': 'Account is inactive.'}
                }, status=status.HTTP_401_UNAUTHORIZED)
        else:
            return Response({
                'success': False,
                'message': 'Invalid username or password.',
                'errors': {'credentials': 'Invalid login credentials.'}
            }, status=status.HTTP_401_UNAUTHORIZED)
            
    except Exception as e:
        return Response({
            'success': False,
            'message': f'Login failed: {str(e)}',
            'errors': {'general': str(e)}
        }, status=status.HTTP_500_INTERNAL_SERVER_ERROR)


# -------------------------------------------------------
# LOGOUT API ENDPOINT
# -------------------------------------------------------
@api_view(['POST'])
@permission_classes([IsAuthenticated])
def logout_api(request):
    """
    API endpoint for user logout
    """
    try:
        # Delete the user's token
        try:
            request.user.auth_token.delete()
        except:
            pass
        
        return Response({
            'success': True,
            'message': 'Logout successful. Goodbye!'
        }, status=status.HTTP_200_OK)
        
    except Exception as e:
        return Response({
            'success': False,
            'message': f'Logout failed: {str(e)}',
            'errors': {'general': str(e)}
        }, status=status.HTTP_500_INTERNAL_SERVER_ERROR)


# -------------------------------------------------------
# USER PROFILE API ENDPOINT
# -------------------------------------------------------
@api_view(['GET', 'PUT'])
@permission_classes([IsAuthenticated])
def profile_api(request):
    """
    API endpoint to get or update user profile
    """
    try:
        if request.method == 'GET':
            profile_info = get_user_profile_info(request.user)
            
            return Response({
                'success': True,
                'message': 'Profile retrieved successfully.',
                'data': {
                    'user': {
                        'id': request.user.id,
                        'username': request.user.username,
                        'email': request.user.email,
                        'first_name': request.user.first_name,
                        'last_name': request.user.last_name,
                        'role': request.user.role,
                        'role_display': request.user.get_role_display(),
                        'date_joined': request.user.date_joined.isoformat(),
                        'last_login': request.user.last_login.isoformat() if request.user.last_login else None
                    },
                    'profile': profile_info
                }
            }, status=status.HTTP_200_OK)
        
        elif request.method == 'PUT':
            data = request.data
            user = request.user
            
            # Update basic user information
            user.first_name = data.get('first_name', user.first_name)
            user.last_name = data.get('last_name', user.last_name)
            user.email = data.get('email', user.email)
            
            # Check if email is already taken by another user
            if data.get('email') and Users.objects.filter(email=data.get('email')).exclude(id=user.id).exists():
                return Response({
                    'success': False,
                    'message': 'Email already exists.',
                    'errors': {'email': 'This email is already registered to another user.'}
                }, status=status.HTTP_400_BAD_REQUEST)
            
            user.save()
            
            # Update role-specific profile
            if user.role == 'consumer':
                try:
                    consumer = Consumer.objects.get(user=user)
                    consumer.phone_number = data.get('phone_number', consumer.phone_number)
                    consumer.address = data.get('address', consumer.address)
                    consumer.save()
                except Consumer.DoesNotExist:
                    pass
            elif user.role == 'macra_staff':
                try:
                    staff = MacraStaff.objects.get(user=user)
                    staff.phone_number = data.get('phone_number', staff.phone_number)
                    staff.save()
                except MacraStaff.DoesNotExist:
                    pass
            
            # Get updated profile info
            profile_info = get_user_profile_info(user)
            
            return Response({
                'success': True,
                'message': 'Profile updated successfully.',
                'data': {
                    'user': {
                        'id': user.id,
                        'username': user.username,
                        'email': user.email,
                        'first_name': user.first_name,
                        'last_name': user.last_name,
                        'role': user.role,
                        'role_display': user.get_role_display()
                    },
                    'profile': profile_info
                }
            }, status=status.HTTP_200_OK)
            
    except Exception as e:
        return Response({
            'success': False,
            'message': f'Profile operation failed: {str(e)}',
            'errors': {'general': str(e)}
        }, status=status.HTTP_500_INTERNAL_SERVER_ERROR)


# -------------------------------------------------------
# DASHBOARD DATA API ENDPOINT
# -------------------------------------------------------
@api_view(['GET'])
@permission_classes([IsAuthenticated])
def dashboard_api(request):
    """
    API endpoint to get dashboard data based on user role
    """
    try:
        user = request.user
        dashboard_data = {
            'user': {
                'id': user.id,
                'username': user.username,
                'first_name': user.first_name,
                'last_name': user.last_name,
                'role': user.role,
                'role_display': user.get_role_display()
            }
        }
        
        if user.role == 'consumer':
            try:
                consumer = Consumer.objects.get(user=user)
                recent_complaints = consumer.complaints.all().order_by('-complaint_date')[:5]
                
                dashboard_data.update({
                    'consumer_data': {
                        'total_complaints': consumer.complaints.count(),
                        'recent_complaints': [
                            {
                                'id': complaint.id,
                                'provider': complaint.provider.provider_name if complaint.provider else None,
                                'category': complaint.category.category_name if complaint.category else None,
                                'status': complaint.status.status_name if complaint.status else None,
                                'complaint_date': complaint.complaint_date.isoformat(),
                                'complaint_details': complaint.complaint_details[:100] + '...' if len(complaint.complaint_details) > 100 else complaint.complaint_details
                            }
                            for complaint in recent_complaints
                        ]
                    }
                })
            except Consumer.DoesNotExist:
                dashboard_data['consumer_data'] = {'total_complaints': 0, 'recent_complaints': []}
                
        elif user.role == 'macra_staff':
            try:
                staff = MacraStaff.objects.get(user=user)
                assigned_complaints = staff.assigned_complaints.all().order_by('-complaint_date')[:10]
                
                dashboard_data.update({
                    'staff_data': {
                        'job_title': staff.job_title,
                        'department': staff.department,
                        'total_assigned': staff.assigned_complaints.count(),
                        'assigned_complaints': [
                            {
                                'id': complaint.id,
                                'consumer': f"{complaint.consumer.user.first_name} {complaint.consumer.user.last_name}",
                                'provider': complaint.provider.provider_name if complaint.provider else None,
                                'category': complaint.category.category_name if complaint.category else None,
                                'status': complaint.status.status_name if complaint.status else None,
                                'complaint_date': complaint.complaint_date.isoformat(),
                                'complaint_details': complaint.complaint_details[:100] + '...' if len(complaint.complaint_details) > 100 else complaint.complaint_details
                            }
                            for complaint in assigned_complaints
                        ]
                    }
                })
            except MacraStaff.DoesNotExist:
                dashboard_data['staff_data'] = {
                    'job_title': None,
                    'department': None,
                    'total_assigned': 0,
                    'assigned_complaints': []
                }
        
        return Response({
            'success': True,
            'message': 'Dashboard data retrieved successfully.',
            'data': dashboard_data
        }, status=status.HTTP_200_OK)
        
    except Exception as e:
        return Response({
            'success': False,
            'message': f'Failed to retrieve dashboard data: {str(e)}',
            'errors': {'general': str(e)}
        }, status=status.HTTP_500_INTERNAL_SERVER_ERROR)


# -------------------------------------------------------
# CHECK USERNAME AVAILABILITY API
# -------------------------------------------------------
@api_view(['POST'])
@permission_classes([AllowAny])
def check_username_api(request):
    """
    API endpoint to check username availability
    """
    username = request.data.get('username')
    if not username:
        return Response({
            'success': False,
            'message': 'Username is required.',
            'available': False
        }, status=status.HTTP_400_BAD_REQUEST)
    
    is_available = not Users.objects.filter(username=username).exists()
    
    return Response({
        'success': True,
        'available': is_available,
        'message': 'Username is available.' if is_available else 'Username is already taken.'
    }, status=status.HTTP_200_OK)


# -------------------------------------------------------
# CHECK EMAIL AVAILABILITY API
# -------------------------------------------------------
@api_view(['POST'])
@permission_classes([AllowAny])
def check_email_api(request):
    """
    API endpoint to check email availability
    """
    email = request.data.get('email')
    if not email:
        return Response({
            'success': False,
            'message': 'Email is required.',
            'available': False
        }, status=status.HTTP_400_BAD_REQUEST)
    
    is_available = not Users.objects.filter(email=email).exists()
    
    return Response({
        'success': True,
        'available': is_available,
        'message': 'Email is available.' if is_available else 'Email is already registered.'
    }, status=status.HTTP_200_OK)


# -------------------------------------------------------
# GET JOB TITLES AND DEPARTMENTS API
# -------------------------------------------------------
@api_view(['GET'])
@permission_classes([AllowAny])
def get_job_titles_api(request):
    """
    API endpoint to get available job titles and departments for MACRA staff
    """
    return Response({
        'success': True,
        'data': {
            'job_titles': [{'code': code, 'display': display} for code, display in MACRA_JOB_TITLES],
            'departments': [{'code': code, 'display': display} for code, display in MACRA_DEPARTMENTS]
        }
    }, status=status.HTTP_200_OK)


# -------------------------------------------------------
# UTILITY FUNCTIONS
# -------------------------------------------------------
def get_user_profile_info(user):
    """
    Get comprehensive user profile information
    """
    profile_info = {
        'role_display': user.get_role_display(),
        'registration_date': user.date_joined.isoformat(),
        'last_login': user.last_login.isoformat() if user.last_login else None,
    }
    
    if user.role == 'consumer':
        try:
            consumer = Consumer.objects.get(user=user)
            profile_info.update({
                'phone_number': consumer.phone_number,
                'address': consumer.address,
                'profile_registration_date': consumer.registration_date.isoformat(),
                'total_complaints': consumer.complaints.count(),
            })
        except Consumer.DoesNotExist:
            profile_info.update({
                'phone_number': '',
                'address': '',
                'total_complaints': 0,
            })
    elif user.role == 'macra_staff':
        try:
            staff = MacraStaff.objects.get(user=user)
            profile_info.update({
                'phone_number': staff.phone_number,
                'job_title': staff.job_title,
                'department': staff.department,
                'hire_date': staff.hire_date.isoformat(),
                'assigned_complaints': staff.assigned_complaints.count(),
            })
        except MacraStaff.DoesNotExist:
            profile_info.update({
                'phone_number': '',
                'job_title': '',
                'department': '',
                'assigned_complaints': 0,
            })
    
    return profile_info


def get_user_permissions(user):
    """
    Get user permissions based on role
    """
    permissions = {
        'can_file_complaint': False,
        'can_view_all_complaints': False,
        'can_assign_complaints': False,
        'can_resolve_complaints': False,
        'can_manage_users': False,
        'can_generate_reports': False
    }
    
    if user.role == 'consumer':
        permissions.update({
            'can_file_complaint': True,
        })
    elif user.role == 'macra_staff':
        staff = getattr(user, 'macrastaff', None)
        if staff:
            job_title_lower = staff.job_title.lower()
            
            # Basic staff permissions
            permissions.update({
                'can_view_all_complaints': True,
                'can_resolve_complaints': True,
            })
            
            # Role-specific permissions
            if 'director' in job_title_lower or 'manager' in job_title_lower:
                permissions.update({
                    'can_assign_complaints': True,
                    'can_manage_users': True,
                    'can_generate_reports': True,
                })
            elif 'senior' in job_title_lower or 'investigation' in job_title_lower:
                permissions.update({
                    'can_assign_complaints': True,
                    'can_generate_reports': True,
                })
    
    return permissions