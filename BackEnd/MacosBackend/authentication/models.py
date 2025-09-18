
# authentication/models.py
import uuid
from django.db import models

class Role(models.Model):
    """
    Roles such as: consumer, complaint_officer, analyst, manager, admin
    """
    name = models.CharField(max_length=50, unique=True)
    description = models.TextField(blank=True)

    def __str__(self):
        return self.name


class User(models.Model):
    """
    Custom user model (no django.contrib.auth used)
    """
    user_id = models.UUIDField(primary_key=True, default=uuid.uuid4, editable=False)
    first_name = models.CharField(max_length=150)
    last_name = models.CharField(max_length=150, blank=True)
    email = models.EmailField(unique=True)
    phone_number = models.CharField(max_length=20, unique=True, null=True, blank=True)
    password_hash = models.CharField(max_length=512)  # pbkdf2 result with salt encoded
    created_at = models.DateTimeField(auto_now_add=True)
    # optional profile fields:
    is_active = models.BooleanField(default=True)

    roles = models.ManyToManyField(Role, through="UserRole", related_name="users")

    def __str__(self):
        return f"{self.first_name} {self.last_name or ''} <{self.email}>"


class UserRole(models.Model):
    """
    Explicit through table (allows role-specific metadata in future)
    """
    id = models.AutoField(primary_key=True)
    user = models.ForeignKey(User, on_delete=models.CASCADE)
    role = models.ForeignKey(Role, on_delete=models.CASCADE)
    assigned_at = models.DateTimeField(auto_now_add=True)

    class Meta:
        unique_together = ("user", "role")


class UserIdentification(models.Model):
    """
    Stores identification records for users (IDs, staff numbers, national ID, etc.)
    All user-identification related tables live in this file as requested.
    """
    IDENT_TYPE_CHOICES = [
        ("national_id", "National ID"),
        ("passport", "Passport"),
        ("staff_id", "Staff ID"),
        ("other", "Other"),
    ]

    id = models.BigAutoField(primary_key=True)
    user = models.ForeignKey(User, on_delete=models.CASCADE, related_name="identifications")
    id_type = models.CharField(max_length=50, choices=IDENT_TYPE_CHOICES)
    id_value = models.CharField(max_length=255)
    is_verified = models.BooleanField(default=False)
    issued_date = models.DateField(null=True, blank=True)
    verified_at = models.DateTimeField(null=True, blank=True)

    class Meta:
        unique_together = ("id_type", "id_value")
