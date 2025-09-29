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

    try:
        data = json.loads(request.body)
    except json.JSONDecodeError:
        return JsonResponse({"error": "Invalid JSON"}, status=400)

    # Required fields
    required_fields = ["consumer_id", "provider_id", "category_id", "status_id", "complaint_details"]
    missing_fields = [field for field in required_fields if field not in data]

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

    return JsonResponse({"success": True, "complaint_id": complaint.id})
