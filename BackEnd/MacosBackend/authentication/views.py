# authentication/views.py
import json
from django.http import JsonResponse
from django.views.decorators.csrf import csrf_exempt
from django.views.decorators.http import require_http_methods

from .models import User, Role, UserRole
from .utils import hash_password, verify_password, create_jwt
from .decorators import role_required

@csrf_exempt
@require_http_methods(["POST"])
def register(request):
    """
    POST /api/auth/register/
    Expected JSON: first_name, last_name, email, phone_number, password
    Default role assigned: 'consumer' (created if missing)
    """
    try:
        data = json.loads(request.body)
        first_name = data.get("first_name")
        last_name = data.get("last_name", "")
        email = data.get("email")
        phone = data.get("phone_number")
        password = data.get("password")
        role = data.get("role")

        if not all([first_name, email, password]):
            return JsonResponse({"error": "first_name, email and password are required"}, status=400)

        if User.objects.filter(email=email).exists():
            return JsonResponse({"error": "Email already exists"}, status=400)

        if phone and User.objects.filter(phone_number=phone).exists():
            return JsonResponse({"error": "Phone number already exists"}, status=400)

        password_hash = hash_password(password)

        user = User.objects.create(
            first_name=first_name,
            last_name=last_name,
            email=email,
            phone_number=phone,
            password_hash=password_hash,
        )

        # ensure consumer role exists and assign
        consumer_role, _ = Role.objects.get_or_create(name="consumer", defaults={"description": "Public consumer/public user"})
        UserRole.objects.create(user=user, role=consumer_role)

        return JsonResponse({"message": "Registration successful", "user_id": str(user.user_id)}, status=201)
    except Exception as e:
        return JsonResponse({"error": str(e)}, status=500)


@csrf_exempt
@require_http_methods(["POST"])
def login(request):
    """
    POST /api/auth/login/
    Expected JSON: email, password
    Returns JWT and user info
    """
    try:
        data = json.loads(request.body)
        email = data.get("email")
        password = data.get("password")
        if not email or not password:
            return JsonResponse({"error": " 1 pass << email and password required"}, status=400)

        try:
            user = User.objects.get(email=email)
        except User.DoesNotExist:
            return JsonResponse({"error": "user not found Invalid email or password"}, status=401)

        if not verify_password(password, user.password_hash):
            return JsonResponse({"error": "Invalid password"}, status=401)

        # compute roles
        roles_qs = user.roles.all()
        roles = [r.name for r in roles_qs]

        payload = {
            "user_id": str(user.user_id),
            "email": user.email,
            "roles": roles
        }
        token = create_jwt(payload)

        return JsonResponse({
            "message": "Login successful",
            "token": token,
            "user_id": str(user.user_id),
            "roles": roles
        })
    except Exception as e:
        return JsonResponse({"error": str(e)}, status=500)


# ADMIN - assign role
@csrf_exempt
@require_http_methods(["POST"])
@role_required("admin")
def assign_role(request):
    """
    POST /api/auth/assign-role/
    JSON: { "user_id": "<uuid>", "role": "complaint_officer" }
    Admin only
    """
    try:
        data = json.loads(request.body)
        user_id = data.get("user_id")
        role_name = data.get("role")
        if not user_id or not role_name:
            return JsonResponse({"error": "user_id and role required"}, status=400)

        try:
            user = User.objects.get(user_id=user_id)
        except User.DoesNotExist:
            return JsonResponse({"error": "User not found"}, status=404)

        role, _ = Role.objects.get_or_create(name=role_name)
        # create linking row if not exists
        ur, created = UserRole.objects.get_or_create(user=user, role=role)
        if created:
            return JsonResponse({"message": f"Role '{role_name}' assigned to user {user_id}"})
        else:
            return JsonResponse({"message": f"User already has role '{role_name}'"})

    except Exception as e:
        return JsonResponse({"error": str(e)}, status=500)


# ADMIN - revoke role
@csrf_exempt
@require_http_methods(["POST"])
@role_required("admin")
def revoke_role(request):
    """
    POST /api/auth/revoke-role/
    JSON: { "user_id": "<uuid>", "role": "complaint_officer" }
    Admin only
    """
    try:
        data = json.loads(request.body)
        user_id = data.get("user_id")
        role_name = data.get("role")
        if not user_id or not role_name:
            return JsonResponse({"error": "user_id and role required"}, status=400)

        try:
            user = User.objects.get(user_id=user_id)
        except User.DoesNotExist:
            return JsonResponse({"error": "User not found"}, status=404)

        try:
            role = Role.objects.get(name=role_name)
        except Role.DoesNotExist:
            return JsonResponse({"error": "Role not found"}, status=404)

        deleted, _ = UserRole.objects.filter(user=user, role=role).delete()
        return JsonResponse({"message": f"Role '{role_name}' revoked from user {user_id}"})

    except Exception as e:
        return JsonResponse({"error": str(e)}, status=500)


# Example protected endpoint
@require_http_methods(["GET"])
@role_required("admin")
def admin_only_endpoint(request):
    """
    GET /api/auth/admin-only/
    Only accessible by admin role
    """
    return JsonResponse({"message": "Welcome, admin!", "user": str(request.auth_user.email)})
