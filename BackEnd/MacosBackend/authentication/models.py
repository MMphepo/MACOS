from django.db import models
from django.contrib.auth.models import AbstractUser

# -------------------------------------------------------
# 1. USER MODEL (Supertype)
# -------------------------------------------------------
class Users(AbstractUser):
    ROLE_CHOICES = [
        ('consumer', 'Consumer'),
        ('macra_staff', 'MACRA Staff'),
    ]
    role = models.CharField(max_length=20, choices=ROLE_CHOICES)

    def __str__(self):
        return f"{self.username} ({self.get_role_display()})"


# -------------------------------------------------------
# 2. CONSUMER MODEL (Subtype of Userss)
# -------------------------------------------------------
class Consumer(models.Model):
    user = models.OneToOneField(Users, on_delete=models.CASCADE, primary_key=True)
    phone_number = models.CharField(max_length=20, blank=True, null=True)
    address = models.TextField(blank=True, null=True)
    registration_date = models.DateTimeField(auto_now_add=True)

    def __str__(self):
        return f"{self.user.first_name} {self.user.last_name}"


# -------------------------------------------------------
# 3. MACRA STAFF MODEL (Subtype of Users)
# -------------------------------------------------------
class MacraStaff(models.Model):

    user = models.OneToOneField(Users, on_delete=models.CASCADE, primary_key=True)
    phone_number = models.CharField(max_length=20, blank=True, null=True)
    job_title = models.CharField(max_length=255, blank=True, null=True)
    department = models.CharField(max_length=255, blank=True, null=True)
    hire_date = models.DateField()
    skills = models.CharField(max_length=255, blank=True, null=True, help_text="Comma-separated list of skills (e.g. Telecommunications, Broadcasting)")

    def __str__(self):
        return f"{self.user.first_name} {self.user.last_name}"


# -------------------------------------------------------
# 4. SERVICE PROVIDER
# -------------------------------------------------------
class ServiceProvider(models.Model):
    provider_name = models.CharField(max_length=255)
    contact_person = models.CharField(max_length=255, blank=True, null=True)
    contact_email = models.EmailField(blank=True, null=True)
    contact_phone = models.CharField(max_length=20, blank=True, null=True)
    license_number = models.CharField(max_length=50, unique=True)
    registration_date = models.DateTimeField(auto_now_add=True)

    def __str__(self):
        return self.provider_name


# -------------------------------------------------------
# 5. COMPLAINT CATEGORY
# -------------------------------------------------------
class ComplaintCategory(models.Model):
    category_name = models.CharField(max_length=255)
    description = models.TextField(blank=True, null=True)

    def __str__(self):
        return self.category_name


# -------------------------------------------------------
# 6. COMPLAINT STATUS
# -------------------------------------------------------
class ComplaintStatus(models.Model):
    status_name = models.CharField(max_length=255)

    def __str__(self):
        return self.status_name


# -------------------------------------------------------
# 7. COMPLAINT
# -------------------------------------------------------
class Complaint(models.Model):
    consumer = models.ForeignKey(Consumer, on_delete=models.CASCADE, related_name='complaints')
    provider = models.ForeignKey(ServiceProvider, on_delete=models.CASCADE, related_name='complaints')
    category = models.ForeignKey(ComplaintCategory, on_delete=models.SET_NULL, null=True, related_name='complaints')
    status = models.ForeignKey(ComplaintStatus, on_delete=models.SET_NULL, null=True, related_name='complaints')
    complaint_date = models.DateTimeField(auto_now_add=True)
    complaint_details = models.TextField()
    resolution_details = models.TextField(blank=True, null=True)
    assigned_staff = models.ForeignKey(
        MacraStaff,
        on_delete=models.SET_NULL,
        null=True,
        blank=True,
        related_name='assigned_complaints'
    )

    def __str__(self):
        return f"Complaint #{self.id} by {self.consumer}"


# -------------------------------------------------------
# 8. COMPLAINT ATTACHMENTS
# -------------------------------------------------------
class ComplaintAttachment(models.Model):
    complaint = models.ForeignKey(Complaint, on_delete=models.CASCADE, related_name='attachments')
    file_name = models.CharField(max_length=255)
    file = models.FileField(upload_to='complaint_attachments/')
    upload_date = models.DateTimeField(auto_now_add=True)

    def __str__(self):
        return f"Attachment {self.file_name} for Complaint #{self.complaint.id}"
