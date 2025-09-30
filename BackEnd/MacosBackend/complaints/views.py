# Fetch complaints filed by a specific consumer (by username)
from django.views.decorators.http import require_GET
from authentication.models import Users

@require_GET
def fetch_filed_complaints(request, username):
    try:
        user = Users.objects.get(username=username)
        consumer = Consumer.objects.get(user=user)
        complaints = Complaint.objects.filter(consumer=consumer)
        data = [
            {
                'id': c.id,
                'provider': c.provider.provider_name,
                'category': c.category.category_name if c.category else None,
                'status': c.status.status_name if c.status else None,
                'complaint_details': c.complaint_details,
                'complaint_date': c.complaint_date,
                'assigned_staff': c.assigned_staff.user.username if c.assigned_staff else None,
            }
            for c in complaints
        ]
        return JsonResponse({'success': True, 'complaints': data})
    except Users.DoesNotExist:
        return JsonResponse({'success': False, 'error': 'User not found'}, status=404)
    except Consumer.DoesNotExist:
        return JsonResponse({'success': False, 'error': 'Consumer not found'}, status=404)

# Fetch all complaints with optional filters
@require_GET
def fetch_all_complaints(request):
    complaints = Complaint.objects.all()
    # Filters: status, provider, category, date range, consumer username
    status_id = request.GET.get('status_id')
    provider_id = request.GET.get('provider_id')
    category_id = request.GET.get('category_id')
    start_date = request.GET.get('start_date')
    end_date = request.GET.get('end_date')
    username = request.GET.get('username')

    if status_id:
        complaints = complaints.filter(status_id=status_id)
    if provider_id:
        complaints = complaints.filter(provider_id=provider_id)
    if category_id:
        complaints = complaints.filter(category_id=category_id)
    if start_date:
        complaints = complaints.filter(complaint_date__gte=start_date)
    if end_date:
        complaints = complaints.filter(complaint_date__lte=end_date)
    if username:
        try:
            user = Users.objects.get(username=username)
            consumer = Consumer.objects.get(user=user)
            complaints = complaints.filter(consumer=consumer)
        except (Users.DoesNotExist, Consumer.DoesNotExist):
            return JsonResponse({'success': False, 'error': 'Consumer not found'}, status=404)

    data = [
        {
            'id': c.id,
            'consumer': c.consumer.user.username,
            'provider': c.provider.provider_name,
            'category': c.category.category_name if c.category else None,
            'status': c.status.status_name if c.status else None,
            'complaint_details': c.complaint_details,
            'complaint_date': c.complaint_date,
            'assigned_staff': c.assigned_staff.user.username if c.assigned_staff else None,
        }
        for c in complaints
    ]
    return JsonResponse({'success': True, 'complaints': data})
# complaints/views.py
from django.http import JsonResponse
from django.views.decorators.csrf import csrf_exempt
import json
from authentication.models import Consumer, ServiceProvider, ComplaintCategory, ComplaintStatus, Complaint, MacraStaff
from django.utils.dateparse import parse_datetime

@csrf_exempt
def file_complaint(request):
    if request.method != "POST":
        return JsonResponse({"error": "Only POST requests allowed"}, status=405)

    # Use multipart/form-data for file uploads
    data = request.POST
    files = request.FILES.getlist('attachments')  # Expecting multiple files under 'attachments'

    # Required fields
    required_fields = ["consumer_id", "provider_id", "category_id", "status_id", "complaint_details"]
    missing_fields = [field for field in required_fields if not data.get(field)]

    if missing_fields:
        return JsonResponse({"error": f"Missing required fields: {', '.join(missing_fields)}"}, status=400)

    # Validate foreign keys
    try:
        consumer = Consumer.objects.get(pk=data["consumer_id"])
    except Consumer.DoesNotExist:
        return JsonResponse({"error": "Consumer not found"}, status=404)

    try:
        provider = ServiceProvider.objects.get(pk=data["provider_id"])
    except ServiceProvider.DoesNotExist:
        return JsonResponse({"error": "Service Provider not found"}, status=404)

    try:
        category = ComplaintCategory.objects.get(pk=data["category_id"])
    except ComplaintCategory.DoesNotExist:
        return JsonResponse({"error": "Complaint Category not found"}, status=404)

    try:
        status = ComplaintStatus.objects.get(pk=data["status_id"])
    except ComplaintStatus.DoesNotExist:
        return JsonResponse({"error": "Complaint Status not found"}, status=404)

    assigned_staff = None
    if data.get("assigned_staff_id"):
        try:
            assigned_staff = MacraStaff.objects.get(pk=data["assigned_staff_id"])
        except MacraStaff.DoesNotExist:
            return JsonResponse({"error": "Assigned staff not found"}, status=404)

    # Create complaint
    complaint = Complaint.objects.create(
        consumer=consumer,
        provider=provider,
        category=category,
        status=status,
        complaint_details=data["complaint_details"],
        assigned_staff=assigned_staff
    )

    # Handle file attachments (documents, images, media, etc.)
    from authentication.models import ComplaintAttachment
    for f in files:
        ComplaintAttachment.objects.create(
            complaint=complaint,
            file_name=f.name,
            file=f
        )

    return JsonResponse({"success": True, "complaint_id": complaint.id, "attachments": [f.name for f in files]})
