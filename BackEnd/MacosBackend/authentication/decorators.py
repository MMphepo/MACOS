# authentication/decorators.py
from functools import wraps
from django.http import JsonResponse
from authentication.utils import decode_jwt
from authentication.models import User
import jwt

def get_token_from_request(request):
    auth = request.META.get("HTTP_AUTHORIZATION", "")
    if not auth:
        return None
    parts = auth.split()
    if len(parts) == 2 and parts[0].lower() == "bearer":
        return parts[1]
    return None

def role_required(*required_roles):
    """
    Decorator to enforce that the JWT user has at least one of the required_roles.
    Usage: @role_required('admin') or @role_required('manager','complaint_officer')
    """
    def decorator(view_func):
        @wraps(view_func)
        def _wrapped(request, *args, **kwargs):
            token = get_token_from_request(request)
            if not token:
                return JsonResponse({"error": "Authorization token required"}, status=401)
            try:
                payload = decode_jwt(token)
            except jwt.ExpiredSignatureError:
                return JsonResponse({"error": "Token expired"}, status=401)
            except Exception:
                return JsonResponse({"error": "Invalid token"}, status=401)

            user_id = payload.get("user_id")
            roles = payload.get("roles", [])
            if not user_id:
                return JsonResponse({"error": "Invalid token payload"}, status=401)

            # Check roles
            if required_roles and not any(r in roles for r in required_roles):
                return JsonResponse({"error": "Forbidden - insufficient role"}, status=403)

            # Attach user (optional)
            try:
                user = User.objects.get(user_id=user_id)
                request.auth_user = user
            except User.DoesNotExist:
                return JsonResponse({"error": "User not found"}, status=401)

            return view_func(request, *args, **kwargs)
        return _wrapped
    return decorator
